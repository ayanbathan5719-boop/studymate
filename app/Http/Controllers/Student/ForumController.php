<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\Flag;
use App\Models\User;
use App\Models\Unit;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Notifications\NewForumReply;
use App\Notifications\PostFlagged;
use App\Notifications\NewForumPost;

class ForumController extends Controller
{
    /**
     * Display forum posts for student's enrolled units.
     */
    public function index(Request $request)
    {
        $student = Auth::user();
        
        // Get enrolled unit codes
        $unitCodes = $student->enrolledUnits()->pluck('code')->toArray();
        
        // Build query with ALL necessary relationships loaded for avatars
        $query = ForumPost::with(['user', 'replies.user', 'replies.resources', 'resources'])
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
        
        $posts = $query->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Get enrolled units for filter
        $units = $student->enrolledUnits()->orderBy('code')->get();
        
        return view('student.forum.index', [
            'posts' => $posts,
            'units' => $units,
            'filters' => $request->only(['unit', 'search'])
        ]);
    }

    /**
     * Show form to create a new post.
     * This method is kept for backward compatibility but may not be used with AJAX approach.
     */
    public function create()
    {
        $student = Auth::user();
        $units = $student->enrolledUnits()->orderBy('code')->get();
        
        return view('student.forum.create', [
            'units' => $units
        ]);
    }

    /**
     * Show form to edit a post.
     */
    public function edit(ForumPost $post)
    {
        $student = Auth::user();
        
        // Check if student is enrolled in this unit and is the author
        if (!$student->enrolledUnits()->where('code', $post->unit_code)->exists()) {
            abort(403, 'You are not enrolled in this unit.');
        }
        
        if ($post->user_id !== $student->id && !$student->hasRole('admin')) {
            abort(403, 'You can only edit your own posts.');
        }
        
        $units = $student->enrolledUnits()->orderBy('code')->get();
        
        return view('student.forum.edit', [
            'post' => $post,
            'units' => $units
        ]);
    }

    /**
     * Store a new forum post with notifications.
     * Supports both AJAX and traditional form submissions.
     * Resources are saved to unit-specific folder: resources/{unit_code}/
     */
    public function store(Request $request)
    {
        $student = Auth::user();
        
        // Validate the request
        $request->validate([
            'content' => 'required|string',
            'unit_code' => 'nullable|exists:units,code',
            'is_announcement' => 'nullable|boolean',
            'is_pinned' => 'nullable|boolean',
            'attachment' => 'nullable|file|max:51200', // 50MB max
            'link_url' => 'nullable|url',
            'link_title' => 'nullable|string|max:255',
        ]);
        
        // Determine unit code
        $unitCode = $request->unit_code;
        if (!$unitCode) {
            $firstUnit = $student->enrolledUnits()->first();
            if (!$firstUnit) {
                return $this->jsonOrBack($request, 'You are not enrolled in any units.', 'error');
            }
            $unitCode = $firstUnit->code;
        }
        
        // Check if student is enrolled in this unit
        if (!$student->enrolledUnits()->where('code', $unitCode)->exists()) {
            return $this->jsonOrBack($request, 'You are not enrolled in this unit.', 'error');
        }
        
        // Get unit model
        $unit = Unit::where('code', $unitCode)->first();
        
        // Auto-generate title from first line of content (max 100 chars)
        $title = strtok($request->content, "\n");
        $title = substr($title, 0, 100);
        
        // Create the post
        $post = ForumPost::create([
            'title' => $title,
            'content' => $request->content,
            'user_id' => $student->id,
            'unit_id' => $unit->id,
            'unit_code' => $unitCode,
            'is_announcement' => $request->has('is_announcement') && $request->is_announcement,
            'is_pinned' => $request->has('is_pinned') && $request->is_pinned,
        ]);
        
        // Handle attachment if uploaded - Save to UNIT resources folder
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Check for duplicate file in this unit
            $existingDuplicate = Resource::where('unit_code', $unitCode)
                ->where('file_name', $file->getClientOriginalName())
                ->where('uploaded_by', $student->id)
                ->first();
            
            if ($existingDuplicate) {
                // Link existing resource instead of creating new one
                $post->resources()->attach($existingDuplicate->id);
                // Return appropriate response
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Post created successfully! (Using existing file)',
                        'post' => $post->load(['user', 'resources'])
                    ]);
                }
                return redirect()->route('forum.index')->with('success', 'Post created successfully! (Using existing file)');
            }
            
            // Store in unit-specific folder: resources/{unit_code}/
            $filePath = $file->storeAs("resources/{$unitCode}", $fileName, 'public');
            
            // Create main resource record in resources table
            $resource = Resource::create([
                'title' => $file->getClientOriginalName(),
                'description' => "Uploaded in forum post: " . Str::limit($title, 100),
                'unit_id' => $unit->id,
                'unit_code' => $unitCode,
                'topic_id' => null,
                'file_type' => $file->getClientOriginalExtension(),
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'uploaded_by' => $student->id,
                'user_id' => $student->id,
                'download_count' => 0,
                'views_count' => 0,
                'is_official' => false,
                'source_id' => $post->id,
                'source_type' => ForumPost::class,
            ]);
            
            // Link resource to forum post using polymorphic relationship
            $post->resources()->save($resource);
        }
        
        // Handle link if added - Also save to resources (FIXED: added file_size => 0)
        if ($request->filled('link_url')) {
            // Create main resource record in resources table
            $resource = Resource::create([
                'title' => $request->link_title ?? $request->link_url,
                'description' => "External link from forum post: " . Str::limit($title, 100),
                'unit_id' => $unit->id,
                'unit_code' => $unitCode,
                'topic_id' => null,
                'file_type' => 'link',
                'file_size' => 0,
                'file_path' => null,
                'file_name' => $request->link_title ?? $request->link_url,
                'url' => $request->link_url,
                'uploaded_by' => $student->id,
                'user_id' => $student->id,
                'download_count' => 0,
                'views_count' => 0,
                'is_official' => false,
                'source_id' => $post->id,
                'source_type' => ForumPost::class,
            ]);
            
            // Link resource to forum post using polymorphic relationship
            $post->resources()->save($resource);
        }
        
        // Send notifications
        $this->sendPostNotifications($post, $student, $unit);
        
        // Return appropriate response
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Post created successfully!',
                'post' => $post->load(['user', 'resources'])
            ]);
        }
        
        return redirect()->route('forum.index')
            ->with('success', 'Post created successfully!');
    }
    
    /**
     * Send notifications for a new post.
     */
    protected function sendPostNotifications($post, $student, $unit)
    {
        // Notify lecturers of this unit about the new post
        $lecturers = $unit->lecturers()->get();
        foreach ($lecturers as $lecturer) {
            $lecturer->notify(new NewForumPost($post, $student));
        }
        
        // Notify other students enrolled in this unit
        $otherStudents = $unit->students()
            ->where('student_id', '!=', $student->id)
            ->get();
        
        foreach ($otherStudents as $otherStudent) {
            $otherStudent->notify(new NewForumPost($post, $student));
        }
    }
    
    /**
     * Helper method to handle JSON or redirect responses.
     */
    protected function jsonOrBack($request, $message, $type = 'error')
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message
            ], 422);
        }
        
        return back()->with($type, $message);
    }

    /**
     * Display a single post with resources.
     * REDIRECTED to main forum index for continuous feed experience.
     */
    public function show($id)
    {
        // Redirect to main forum index instead of separate view
        return redirect()->route('forum.index');
    }

    /**
     * Add a reply to a post with file/attachment support
     */
    public function reply(Request $request, ForumPost $post)
    {
        $student = Auth::user();
        
        // Check if student is enrolled in this unit
        if (!$student->enrolledUnits()->where('code', $post->unit_code)->exists()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'You are not enrolled in this unit.'], 403);
            }
            return back()->with('error', 'You are not enrolled in this unit.');
        }
        
        // Validate request
        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:forum_replies,id',
            'attachment' => 'nullable|file|max:51200',
            'link_url' => 'nullable|url',
            'link_title' => 'nullable|string|max:255',
            'resource_id' => 'nullable|exists:resources,id', // For selecting from existing resources
            'upload_source' => 'nullable|in:file_explorer,my_resources', // Where to upload from
        ]);
        
        // Get unit
        $unit = Unit::where('code', $post->unit_code)->first();
        
        // Create the reply
        $reply = ForumReply::create([
            'content' => $request->content,
            'user_id' => $student->id,
            'forum_post_id' => $post->id,
            'parent_id' => $request->parent_id,
        ]);
        
        // Handle attachment - either from file upload or existing resource
        $attachmentData = [];
        
        // Case 1: Upload from File Explorer (new file) with duplicate detection
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            
            // Check for duplicate file in this unit (same file name, same user)
            $existingDuplicate = Resource::where('unit_code', $post->unit_code)
                ->where('file_name', $file->getClientOriginalName())
                ->where('uploaded_by', $student->id)
                ->first();
            
            if ($existingDuplicate) {
                // Alert user about duplicate
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'You have already uploaded this file to this unit. Would you like to use the existing one?',
                        'existing_resource_id' => $existingDuplicate->id,
                        'duplicate' => true
                    ], 409);
                }
            }
            
            $filePath = $file->storeAs("resources/{$post->unit_code}/replies", $fileName, 'public');
            
            // Check if file already exists in this unit (by content)
            $existingResource = Resource::where('unit_code', $post->unit_code)
                ->where('file_name', $file->getClientOriginalName())
                ->first();
            
            if ($existingResource && !$existingDuplicate) {
                // Alert user and ask if they want to reuse
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'This file already exists in this unit. Do you want to use the existing one?',
                        'existing_resource_id' => $existingResource->id
                    ], 409);
                }
            }
            
            $resource = Resource::create([
                'title' => $file->getClientOriginalName(),
                'description' => "Attached in reply to forum post",
                'unit_id' => $unit->id,
                'unit_code' => $post->unit_code,
                'topic_id' => null,
                'file_type' => $file->getClientOriginalExtension(),
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'uploaded_by' => $student->id,
                'download_count' => 0,
                'views_count' => 0,
                'is_official' => false,
                'source_id' => $reply->id,
                'source_type' => ForumReply::class,
            ]);
            
            $attachmentData = [
                'id' => $resource->id,
                'title' => $resource->title,
                'type' => 'file',
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
            ];
        }
        
        // Case 2: Select from My Resources (existing resource)
        elseif ($request->filled('resource_id')) {
            $existingResource = Resource::find($request->resource_id);
            
            if ($existingResource && $existingResource->unit_code == $post->unit_code) {
                // Link existing resource to this reply
                $existingResource->update([
                    'source_id' => $reply->id,
                    'source_type' => ForumReply::class,
                ]);
                
                $attachmentData = [
                    'id' => $existingResource->id,
                    'title' => $existingResource->title,
                    'type' => 'file',
                    'file_path' => $existingResource->file_path,
                    'file_name' => $existingResource->file_name,
                ];
            }
        }
        
        // Handle link attachment (FIXED: added file_size => 0)
        if ($request->filled('link_url')) {
            $resource = Resource::create([
                'title' => $request->link_title ?? $request->link_url,
                'description' => "External link from forum reply",
                'unit_id' => $unit->id,
                'unit_code' => $post->unit_code,
                'topic_id' => null,
                'file_type' => 'link',
                'file_size' => 0,
                'file_path' => null,
                'file_name' => $request->link_title ?? $request->link_url,
                'url' => $request->link_url,
                'uploaded_by' => $student->id,
                'download_count' => 0,
                'views_count' => 0,
                'is_official' => false,
                'source_id' => $reply->id,
                'source_type' => ForumReply::class,
            ]);
            
            $attachmentData = [
                'id' => $resource->id,
                'title' => $resource->title,
                'type' => 'link',
                'url' => $request->link_url,
            ];
        }
        
        // Load the reply with relationships
        $reply->load('user', 'resources');
        
        // Return JSON response
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Reply added successfully!',
                'reply' => [
                    'id' => $reply->id,
                    'content' => $reply->content,
                    'user_name' => $student->name,
                    'user_initial' => substr($student->name ?? 'U', 0, 1),
                    'user_role' => $student->hasRole('lecturer') ? 'lecturer' : 'student',
                    'created_at' => 'Just now',
                    'parent_id' => $reply->parent_id,
                    'attachments' => $attachmentData ? [$attachmentData] : [],
                    'resources' => $reply->resources,
                ]
            ]);
        }
        
        return back()->with('success', 'Reply added successfully!');
    }

    /**
     * Get resources for a specific unit (for "My Resources" selection)
     */
    public function getUnitResources($unitCode)
    {
        $resources = Resource::where('unit_code', $unitCode)
            ->where('uploaded_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title', 'file_type', 'file_name', 'file_path', 'url']);
        
        return response()->json(['success' => true, 'resources' => $resources]);
    }

    /**
     * Delete a reply (only if user owns it or is admin)
     */
    public function deleteReply(ForumReply $reply)
    {
        $user = Auth::user();
        
        // Check if user can delete this reply (owner or admin)
        if ($reply->user_id !== $user->id && !$user->hasRole('admin')) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'You cannot delete this reply.'], 403);
            }
            return back()->with('error', 'You cannot delete this reply.');
        }
        
        $reply->delete();
        
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Reply deleted successfully!']);
        }
        
        return back()->with('success', 'Reply deleted successfully!');
    }

    /**
     * Edit a reply (within 5-minute window).
     */
    public function editReply(Request $request, ForumReply $reply)
    {
        $student = Auth::user();
        
        // Validate request
        $request->validate([
            'content' => 'required|string',
        ]);
        
        // Check if user owns this reply
        if ($reply->user_id !== $student->id) {
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
     * UPDATED: Added restrictions to prevent flagging lecturers, admins, and own posts
     */
    public function flag(ForumPost $post)
    {
        $student = Auth::user();
        
        // Check if student is enrolled in this unit
        if (!$student->enrolledUnits()->where('code', $post->unit_code)->exists()) {
            return back()->with('error', 'You are not enrolled in this unit.');
        }
        
        // ===== RESTRICTION 1: Prevent flagging lecturers or admins =====
        $postAuthor = $post->user;
        if ($postAuthor && ($postAuthor->hasRole('lecturer') || $postAuthor->hasRole('admin'))) {
            return back()->with('error', 'You cannot flag posts by lecturers or administrators.');
        }
        
        // ===== RESTRICTION 2: Prevent flagging your own posts =====
        if ($post->user_id === $student->id) {
            return back()->with('error', 'You cannot flag your own posts.');
        }
        
        // Check if already flagged by this user
        $existingFlag = Flag::where('forum_post_id', $post->id)
            ->where('reporter_id', $student->id)
            ->first();
        
        if ($existingFlag) {
            return back()->with('error', 'You have already flagged this post.');
        }
        
        $flag = Flag::create([
            'forum_post_id' => $post->id,
            'reporter_id' => $student->id,
            'reported_user_id' => $post->user_id,
            'reason' => 'inappropriate',
            'status' => 'pending'
        ]);
        
        // Notify all admins about the flag
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new PostFlagged($flag, $post, $student));
        }
        
        return back()->with('success', 'Post has been flagged for moderator review.');
    }
}