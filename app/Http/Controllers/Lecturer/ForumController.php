<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\Flag;
use App\Models\User;
use App\Models\Unit;
use App\Models\Resource;
use App\Models\ForumResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Notifications\NewForumReply;
use App\Notifications\PostFlagged;
use App\Notifications\NewForumPost;
use App\Notifications\PostPinned;

class ForumController extends Controller
{
    /**
     * Display forum posts for lecturer's assigned units.
     */
    public function index(Request $request)
    {
        $lecturer = Auth::user();
        
        // Get assigned unit codes
        $unitCodes = $lecturer->units()->pluck('code')->toArray();
        
        $query = ForumPost::with(['user', 'unit', 'replies.user'])
            ->withCount('replies')
            ->whereIn('unit_code', $unitCodes);
        
        // Apply filters
        if ($request->filled('unit')) {
            $query->where('unit_code', $request->unit);
        }
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by status (flagged, pinned, etc.)
        if ($request->filled('status')) {
            if ($request->status == 'flagged') {
                $query->whereHas('flags', function($q) {
                    $q->where('status', 'pending');
                });
            } elseif ($request->status == 'pinned') {
                $query->where('is_pinned', true);
            }
        }
        
        $posts = $query->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Get assigned units for filter
        $units = $lecturer->units()->orderBy('code')->get();
        
        return view('lecturer.forum.index', [
            'posts' => $posts,
            'units' => $units,
            'filters' => $request->only(['unit', 'search', 'status'])
        ]);
    }

    /**
     * Show form to create a new post.
     */
    public function create()
    {
        $lecturer = Auth::user();
        $units = $lecturer->units()->orderBy('code')->get();
        
        return view('lecturer.forum.create', [
            'units' => $units
        ]);
    }

    /**
     * Store a new forum post with notifications and resource attachments.
     * Resources are saved to unit-specific folder: resources/{unit_code}/
     */
    public function store(Request $request)
    {
        $lecturer = Auth::user();
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'unit_code' => 'required|exists:units,code',
            'is_pinned' => 'nullable|boolean',
            'is_announcement' => 'nullable|boolean',
            'attachment' => 'nullable|file|max:51200',
            'link_url' => 'nullable|url',
            'link_title' => 'nullable|string|max:255',
        ]);
        
        $unit = Unit::where('code', $request->unit_code)->first();
        
        // Check if lecturer is assigned to this unit
        if (!$lecturer->units()->where('code', $request->unit_code)->exists()) {
            return back()->with('error', 'You are not assigned to this unit.');
        }
        
        $post = ForumPost::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => $lecturer->id,
            'unit_id' => $unit->id,
            'unit_code' => $request->unit_code,
            'is_pinned' => $request->has('is_pinned'),
            'is_announcement' => $request->has('is_announcement'),
        ]);
        
        // Handle attachment if uploaded - Save to UNIT resources folder
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Store in unit-specific folder: resources/{unit_code}/
            $filePath = $file->storeAs("resources/{$request->unit_code}", $fileName, 'public');
            
            // Create main resource record in resources table (for unit resources page)
            $resource = Resource::create([
                'title' => $file->getClientOriginalName(),
                'description' => "Uploaded in forum post: " . Str::limit($request->title, 100),
                'unit_id' => $unit->id,
                'unit_code' => $request->unit_code,
                'topic_id' => null,
                'file_type' => $file->getClientOriginalExtension(),
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'uploaded_by' => $lecturer->id,
                'download_count' => 0,
                'views_count' => 0,
                'is_official' => true,
                'source_id' => $post->id,
                'source_type' => ForumPost::class,
            ]);
            
            // Link resource to forum post
            ForumResource::create([
                'title' => $file->getClientOriginalName(),
                'type' => 'file',
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'user_id' => $lecturer->id,
                'unit_code' => $request->unit_code,
                'forum_post_id' => $post->id,
                'resource_id' => $resource->id,
            ]);
        }
        
        // Handle link if added - Also save to resources
        if ($request->filled('link_url')) {
            // Create main resource record in resources table
            $resource = Resource::create([
                'title' => $request->link_title ?? $request->link_url,
                'description' => "External link from forum post: " . Str::limit($request->title, 100),
                'unit_id' => $unit->id,
                'unit_code' => $request->unit_code,
                'file_type' => 'link',
                'file_size' => 0,
                'file_path' => null,
                'file_name' => $request->link_title ?? $request->link_url,
                'url' => $request->link_url,
                'uploaded_by' => $lecturer->id,
                'download_count' => 0,
                'views_count' => 0,
                'is_official' => true,
                'source_id' => $post->id,
                'source_type' => ForumPost::class,
            ]);
            
            // Link resource to forum post
            ForumResource::create([
                'title' => $request->link_title ?? $request->link_url,
                'type' => 'link',
                'url' => $request->link_url,
                'user_id' => $lecturer->id,
                'unit_code' => $request->unit_code,
                'forum_post_id' => $post->id,
                'resource_id' => $resource->id,
            ]);
        }
        
        // Handle legacy resources array (if any)
        if ($request->has('resources') && is_array($request->resources)) {
            foreach ($request->resources as $resourceItem) {
                if (!empty($resourceItem['title'])) {
                    $resourceData = [
                        'title' => $resourceItem['title'],
                        'type' => $resourceItem['type'],
                        'forum_post_id' => $post->id,
                        'user_id' => $lecturer->id,
                        'unit_code' => $request->unit_code,
                        'description' => $resourceItem['description'] ?? null,
                    ];
                    
                    // Handle file uploads
                    if (in_array($resourceItem['type'], ['file', 'document']) && isset($resourceItem['file']) && $resourceItem['file'] instanceof \Illuminate\Http\UploadedFile) {
                        $file = $resourceItem['file'];
                        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                        $filePath = $file->storeAs("resources/{$request->unit_code}", $fileName, 'public');
                        
                        // Create main resource record
                        $resource = Resource::create([
                            'title' => $resourceItem['title'],
                            'description' => $resourceItem['description'] ?? "Uploaded in forum post",
                            'unit_id' => $unit->id,
                            'unit_code' => $request->unit_code,
                            'file_type' => $file->getClientOriginalExtension(),
                            'file_path' => $filePath,
                            'file_name' => $file->getClientOriginalName(),
                            'file_size' => $file->getSize(),
                            'uploaded_by' => $lecturer->id,
                            'is_official' => true,
                            'source_id' => $post->id,
                            'source_type' => ForumPost::class,
                        ]);
                        
                        $resourceData['file_path'] = $filePath;
                        $resourceData['file_name'] = $file->getClientOriginalName();
                        $resourceData['file_size'] = $file->getSize();
                        $resourceData['mime_type'] = $file->getMimeType();
                        $resourceData['resource_id'] = $resource->id;
                    }
                    
                    // Handle URL links
                    elseif ($resourceItem['type'] === 'link' && isset($resourceItem['url'])) {
                        $resourceData['url'] = $resourceItem['url'];
                        
                        // Create main resource record
                        $resource = Resource::create([
                            'title' => $resourceItem['title'],
                            'description' => $resourceItem['description'] ?? "External link from forum",
                            'unit_id' => $unit->id,
                            'unit_code' => $request->unit_code,
                            'file_type' => 'link',
                            'file_size' => 0,
                            'file_path' => null,
                            'file_name' => $resourceItem['title'],
                            'url' => $resourceItem['url'],
                            'uploaded_by' => $lecturer->id,
                            'is_official' => true,
                            'source_id' => $post->id,
                            'source_type' => ForumPost::class,
                        ]);
                        
                        $resourceData['resource_id'] = $resource->id;
                    }
                    
                    ForumResource::create($resourceData);
                }
            }
        }
        
        // Notify all students enrolled in this unit about the new post
        $students = $unit->students()->get();
        foreach ($students as $student) {
            $student->notify(new NewForumPost($post, $lecturer));
        }
        
        return redirect()->route('lecturer.forum.index')
            ->with('success', 'Post created successfully!');
    }

    /**
     * Display a single post.
     * REDIRECTED to main forum index for continuous feed experience.
     */
    public function show(ForumPost $post)
    {
        // Redirect to main forum index instead of separate view
        return redirect()->route('lecturer.forum.index');
    }

    /**
     * Show form to edit a post.
     */
    public function edit(ForumPost $post)
    {
        $lecturer = Auth::user();
        
        // Check if lecturer is assigned to this unit and is the author
        if (!$lecturer->units()->where('code', $post->unit_code)->exists()) {
            abort(403, 'You are not assigned to this unit.');
        }
        
        if ($post->user_id !== $lecturer->id && !$lecturer->hasRole('admin')) {
            abort(403, 'You can only edit your own posts.');
        }
        
        $units = $lecturer->units()->orderBy('code')->get();
        
        return view('lecturer.forum.edit', [
            'post' => $post,
            'units' => $units
        ]);
    }

    /**
     * Update a post.
     */
    public function update(Request $request, ForumPost $post)
    {
        $lecturer = Auth::user();
        
        // Check if lecturer is assigned to this unit and is the author
        if (!$lecturer->units()->where('code', $post->unit_code)->exists()) {
            return back()->with('error', 'You are not assigned to this unit.');
        }
        
        if ($post->user_id !== $lecturer->id && !$lecturer->hasRole('admin')) {
            return back()->with('error', 'You can only edit your own posts.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        
        $post->update([
            'title' => $request->title,
            'content' => $request->content,
            'is_pinned' => $request->has('is_pinned'),
            'is_announcement' => $request->has('is_announcement'),
        ]);
        
        return redirect()->route('lecturer.forum.index')
            ->with('success', 'Post updated successfully!');
    }

    /**
     * Toggle pin status with notification.
     */
    public function togglePin(ForumPost $post)
    {
        $lecturer = Auth::user();
        
        // Check if lecturer is assigned to this unit
        if (!$lecturer->units()->where('code', $post->unit_code)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $wasPinned = $post->is_pinned;
        $post->update([
            'is_pinned' => !$post->is_pinned
        ]);
        
        // Notify post author if pinned (and not the same person)
        if (!$wasPinned && $post->is_pinned && $post->user_id !== $lecturer->id) {
            $post->user->notify(new PostPinned($post, $lecturer));
        }
        
        return response()->json([
            'success' => true,
            'is_pinned' => $post->is_pinned
        ]);
    }

    /**
     * Toggle announcement status.
     */
    public function toggleAnnouncement(ForumPost $post)
    {
        $lecturer = Auth::user();
        
        // Check if lecturer is assigned to this unit
        if (!$lecturer->units()->where('code', $post->unit_code)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $post->update([
            'is_announcement' => !$post->is_announcement
        ]);
        
        return response()->json([
            'success' => true,
            'is_announcement' => $post->is_announcement
        ]);
    }

    /**
     * Add a reply to a post with notifications.
     * Supports AJAX for inline replies, file attachments, and nested replies.
     */
    public function reply(Request $request, ForumPost $post)
    {
        $lecturer = Auth::user();
        
        // Check if lecturer is assigned to this unit
        if (!$lecturer->units()->where('code', $post->unit_code)->exists()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'You are not assigned to this unit.'], 403);
            }
            return back()->with('error', 'You are not assigned to this unit.');
        }
        
        // Validate request
        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:forum_replies,id',
            'attachment' => 'nullable|file|max:51200',
            'resource_id' => 'nullable|exists:resources,id', // For selecting existing resources
        ]);
        
        $unit = Unit::where('code', $post->unit_code)->first();
        
        // Create the reply
        $reply = ForumReply::create([
            'content' => $request->content,
            'user_id' => $lecturer->id,
            'forum_post_id' => $post->id,
            'parent_id' => $request->parent_id,
        ]);
        
        // Handle attachment from file explorer
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $filePath = $file->storeAs("resources/{$post->unit_code}", $fileName, 'public');
            
            $resource = Resource::create([
                'title' => $file->getClientOriginalName(),
                'description' => "Attached in forum reply",
                'unit_id' => $unit->id,
                'unit_code' => $post->unit_code,
                'file_type' => $file->getClientOriginalExtension(),
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'uploaded_by' => $lecturer->id,
                'download_count' => 0,
                'views_count' => 0,
                'is_official' => false,
                'source_id' => $reply->id,
                'source_type' => ForumReply::class,
            ]);
        }
        
        // Handle resource selected from My Resources - JUST LINK, DON'T DUPLICATE
        if ($request->filled('resource_id')) {
            $existingResource = Resource::find($request->resource_id);
            if ($existingResource && $existingResource->unit_code == $post->unit_code) {
                // Just update the existing resource to link to this reply (NO duplication)
                $existingResource->update([
                    'source_id' => $reply->id,
                    'source_type' => ForumReply::class,
                ]);
            }
        }
        
        $reply->load('user');
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Reply added successfully!',
                'reply' => [
                    'id' => $reply->id,
                    'content' => $reply->content,
                    'user_name' => $lecturer->name,
                    'user_initial' => substr($lecturer->name ?? 'U', 0, 1),
                    'user_role' => $lecturer->hasRole('lecturer') ? 'lecturer' : 'student',
                    'created_at' => 'Just now',
                    'parent_id' => $reply->parent_id,
                ]
            ]);
        }
        
        return back()->with('success', 'Reply added successfully!');
    }

    /**
     * Delete a reply (lecturer can delete any reply in their units).
     */
    public function deleteReply(ForumReply $reply)
    {
        $lecturer = Auth::user();
        $post = $reply->post;
        
        // Check if lecturer is assigned to this unit
        if (!$lecturer->units()->where('code', $post->unit_code)->exists()) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            return back()->with('error', 'Unauthorized');
        }
        
        $reply->delete();
        
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Reply deleted successfully!']);
        }
        
        return back()->with('success', 'Reply deleted successfully!');
    }

    /**
     * Edit a reply (within 5-minute window for own replies).
     * Lecturers can only edit their own replies, not others.
     */
    public function editReply(Request $request, ForumReply $reply)
    {
        $lecturer = Auth::user();
        
        // Validate request
        $request->validate([
            'content' => 'required|string',
        ]);
        
        // Check if user owns this reply
        if ($reply->user_id !== $lecturer->id) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'You can only edit your own replies'], 403);
            }
            return back()->with('error', 'You can only edit your own replies.');
        }
        
        // Check if within edit window (5 minutes)
        if (!$reply->canEdit()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Edit window has expired (5 minutes)'], 403);
            }
            return back()->with('error', 'Edit window has expired (5 minutes).');
        }
        
        // Store original content if first edit
        if ($reply->edit_count == 0) {
            $reply->original_content = $reply->content;
        }
        
        // Update the reply
        $reply->content = $request->content;
        $reply->edited_at = now();
        $reply->edit_count = $reply->edit_count + 1;
        $reply->save();
        
        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Reply updated successfully!',
                'content' => nl2br(e($reply->content)),
                'edited_time' => $reply->edited_time,
                'edit_count' => $reply->edit_count,
                'edit_display' => $reply->edit_count_display,
            ]);
        }
        
        return back()->with('success', 'Reply updated successfully!');
    }

    /**
     * Flag a post as inappropriate with admin notification.
     */
    public function flag(ForumPost $post)
    {
        $lecturer = Auth::user();
        
        // Check if already flagged by this user
        $existingFlag = Flag::where('forum_post_id', $post->id)
            ->where('reporter_id', $lecturer->id)
            ->first();
        
        if ($existingFlag) {
            return back()->with('error', 'You have already flagged this post.');
        }
        
        $flag = Flag::create([
            'forum_post_id' => $post->id,
            'reporter_id' => $lecturer->id,
            'reported_user_id' => $post->user_id,
            'reason' => 'inappropriate',
            'status' => 'pending'
        ]);
        
        // Notify all admins about the flag
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new PostFlagged($flag, $post, $lecturer));
        }
        
        return back()->with('success', 'Post has been flagged for moderator review.');
    }

    /**
     * Download a forum attachment.
     */
    public function downloadAttachment($postId, $resourceId)
    {
        $lecturer = Auth::user();
        $post = ForumPost::findOrFail($postId);
        
        // Check if lecturer is assigned to this unit
        if (!$lecturer->units()->where('code', $post->unit_code)->exists()) {
            abort(403, 'You are not authorized to access this attachment.');
        }
        
        $resource = ForumResource::where('id', $resourceId)
            ->where('forum_post_id', $postId)
            ->firstOrFail();
        
        if ($resource->file_path && Storage::disk('public')->exists($resource->file_path)) {
            return Storage::disk('public')->download(
                $resource->file_path,
                $resource->file_name
            );
        }
        
        return back()->with('error', 'File not found.');
    }
}