<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\Flag;
use App\Models\Unit;
use App\Models\User;
use App\Models\Resource;
use App\Models\ForumResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\PostPinned;
use App\Notifications\PostFlagged;
use App\Notifications\UserRestricted;

class ForumController extends Controller
{
    /**
     * Display all forum posts with admin controls.
     */
    public function index(Request $request)
    {
        $query = ForumPost::with(['user', 'unit', 'replies.user'])
            ->withCount(['replies', 'flags' => function($q) {
                $q->where('status', 'pending');
            }]);
        
        // Apply filters
        if ($request->filled('unit')) {
            $query->where('unit_code', $request->unit);
        }
        
        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }
        
        if ($request->filled('status')) {
            if ($request->status == 'flagged') {
                $query->whereHas('flags', function($q) {
                    $q->where('status', 'pending');
                });
            } elseif ($request->status == 'pinned') {
                $query->where('is_pinned', true);
            } elseif ($request->status == 'announcement') {
                $query->where('is_announcement', true);
            }
        }
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }
        
        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $posts = $query->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Get units for filter dropdown
        $units = Unit::orderBy('code')->get();
        
        // Get users who have posted for filter dropdown
        $users = User::whereHas('forumPosts')->orderBy('name')->get();
        
        return view('admin.forum.index', [
            'posts' => $posts,
            'units' => $units,
            'users' => $users,
            'filters' => $request->only(['unit', 'user', 'status', 'search', 'date_from', 'date_to'])
        ]);
    }

    /**
     * Display moderation dashboard.
     */
    public function moderation()
    {
        // Get flagged posts count
        $flaggedCount = Flag::where('status', 'pending')->count();
        
        // Get most flagged posts
        $mostFlagged = ForumPost::withCount(['flags' => function($q) {
                $q->where('status', 'pending');
            }])
            ->having('flags_count', '>', 0)
            ->orderBy('flags_count', 'desc')
            ->limit(10)
            ->get();
        
        // Get recent flags
        $recentFlags = Flag::with(['post', 'reporter', 'reportedUser'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        
        // Get statistics
        $stats = [
            'total_posts' => ForumPost::count(),
            'total_replies' => ForumReply::count(),
            'total_flags' => Flag::count(),
            'pending_flags' => Flag::where('status', 'pending')->count(),
            'resolved_flags' => Flag::where('status', 'resolved')->count(),
            'dismissed_flags' => Flag::where('status', 'dismissed')->count(),
            'pinned_posts' => ForumPost::where('is_pinned', true)->count(),
            'announcements' => ForumPost::where('is_announcement', true)->count(),
        ];
        
        return view('admin.forum.moderation', [
            'flaggedCount' => $flaggedCount,
            'mostFlagged' => $mostFlagged,
            'recentFlags' => $recentFlags,
            'stats' => $stats
        ]);
    }

    /**
     * Display a single post with admin controls.
     * REDIRECTED to main forum index for continuous feed experience.
     */
    public function show(ForumPost $post)
    {
        // Redirect to main forum index instead of separate view
        return redirect()->route('admin.forum.index');
    }

    /**
     * Toggle pin status with notification.
     */
    public function togglePin(ForumPost $post)
    {
        $admin = Auth::user();
        
        $wasPinned = $post->is_pinned;
        $post->update([
            'is_pinned' => !$post->is_pinned
        ]);
        
        // Notify post author if pinned by admin (and not the same person)
        if (!$wasPinned && $post->is_pinned && $post->user_id !== $admin->id) {
            $post->user->notify(new PostPinned($post, $admin));
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
        $post->update([
            'is_announcement' => !$post->is_announcement
        ]);
        
        return response()->json([
            'success' => true,
            'is_announcement' => $post->is_announcement
        ]);
    }

    /**
     * Delete a post.
     */
    public function destroy(ForumPost $post)
    {
        // Delete associated flags first
        Flag::where('forum_post_id', $post->id)->delete();
        
        // Delete replies
        $post->replies()->delete();
        
        // Delete the post
        $post->delete();
        
        return redirect()->route('admin.forum.index')
            ->with('success', 'Post deleted successfully!');
    }

    /**
     * Add a reply to a post with notifications.
     * Supports AJAX for inline replies, file attachments, and nested replies.
     */
    public function reply(Request $request, ForumPost $post)
    {
        $admin = Auth::user();
        
        // Validate request with attachment support and parent_id for nested replies
        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:forum_replies,id',
            'attachment' => 'nullable|file|max:51200', // 50MB max
            'link_url' => 'nullable|url',
            'link_title' => 'nullable|string|max:255',
        ]);
        
        // Get unit model for resource storage
        $unit = Unit::where('code', $post->unit_code)->first();
        
        // Create the reply with parent_id if provided
        $reply = ForumReply::create([
            'content' => $request->content,
            'user_id' => $admin->id,
            'forum_post_id' => $post->id,
            'parent_id' => $request->parent_id, // For nested replies
        ]);
        
        // Load the reply with user relationship
        $reply->load('user');
        
        // ===== Handle File Attachment =====
        $attachments = [];
        
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Store in unit-specific folder: resources/{unit_code}/
            $filePath = $file->storeAs("resources/{$post->unit_code}", $fileName, 'public');
            
            // Create main resource record in resources table
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
                'uploaded_by' => $admin->id,
                'user_id' => $admin->id,
                'download_count' => 0,
                'views_count' => 0,
                'is_official' => true,
                'source' => 'forum_reply',
                'source_id' => $reply->id,
            ]);
            
            // Link resource to reply (using forum_resources table)
            $replyResource = ForumResource::create([
                'title' => $file->getClientOriginalName(),
                'type' => 'file',
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'user_id' => $admin->id,
                'unit_code' => $post->unit_code,
                'forum_post_id' => $post->id,
                'resource_id' => $resource->id,
            ]);
            
            $attachments[] = [
                'id' => $replyResource->id,
                'title' => $replyResource->title,
                'type' => 'file',
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
            ];
        }
        
        // ===== Handle Link Attachment =====
        if ($request->filled('link_url')) {
            // Create main resource record in resources table
            $resource = Resource::create([
                'title' => $request->link_title ?? $request->link_url,
                'description' => "External link from forum reply",
                'unit_id' => $unit->id,
                'unit_code' => $post->unit_code,
                'file_type' => 'link',
                'url' => $request->link_url,
                'uploaded_by' => $admin->id,
                'user_id' => $admin->id,
                'download_count' => 0,
                'views_count' => 0,
                'is_official' => true,
                'source' => 'forum_reply',
                'source_id' => $reply->id,
            ]);
            
            // Link resource to reply
            $replyResource = ForumResource::create([
                'title' => $request->link_title ?? $request->link_url,
                'type' => 'link',
                'url' => $request->link_url,
                'user_id' => $admin->id,
                'unit_code' => $post->unit_code,
                'forum_post_id' => $post->id,
                'resource_id' => $resource->id,
            ]);
            
            $attachments[] = [
                'id' => $replyResource->id,
                'title' => $replyResource->title,
                'type' => 'link',
                'url' => $request->link_url,
            ];
        }
        
        // Notify the post author (if not the same person)
        if ($post->user_id !== $admin->id) {
            $post->user->notify(new \App\Notifications\NewForumReply($reply, $post, $admin));
        }
        
        // Notify all other repliers (except the current user and post author)
        $repliers = ForumReply::where('forum_post_id', $post->id)
            ->where('user_id', '!=', $admin->id)
            ->where('user_id', '!=', $post->user_id)
            ->with('user')
            ->get()
            ->pluck('user')
            ->unique('id');
        
        foreach ($repliers as $replier) {
            $replier->notify(new \App\Notifications\NewForumReply($reply, $post, $admin));
        }
        
        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Reply added successfully!',
                'reply' => [
                    'id' => $reply->id,
                    'content' => $reply->content,
                    'user_name' => $admin->name,
                    'user_initial' => substr($admin->name ?? 'U', 0, 1),
                    'user_role' => $admin->hasRole('admin') ? 'admin' : ($admin->hasRole('lecturer') ? 'lecturer' : 'student'),
                    'created_at' => 'Just now',
                    'parent_id' => $reply->parent_id,
                    'attachments' => $attachments,
                ]
            ]);
        }
        
        return back()->with('success', 'Reply added successfully!');
    }

    /**
     * Delete a reply (admin can delete any reply).
     */
    public function deleteReply(ForumReply $reply)
    {
        $reply->delete();
        
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Reply deleted successfully!']);
        }
        
        return back()->with('success', 'Reply deleted successfully!');
    }

    /**
     * Edit a reply (admin can edit any reply, no time limit).
     */
    public function editReply(Request $request, ForumReply $reply)
    {
        $admin = Auth::user();
        
        // Validate request
        $request->validate([
            'content' => 'required|string',
        ]);
        
        // Admin can edit any reply (no ownership check)
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
     * Display flagged posts.
     */
    public function flaggedPosts()
    {
        $posts = ForumPost::whereHas('flags', function($q) {
                $q->where('status', 'pending');
            })
            ->with(['user', 'unit'])
            ->withCount(['flags' => function($q) {
                $q->where('status', 'pending');
            }])
            ->orderBy('flags_count', 'desc')
            ->paginate(20);
        
        return view('admin.forum.flagged', [
            'posts' => $posts
        ]);
    }

    /**
     * Display flags management page.
     */
    public function flags()
    {
        $flags = Flag::with(['post', 'reporter', 'reportedUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.forum.flags', [
            'flags' => $flags
        ]);
    }

    /**
     * Update flag status with auto-restriction and notification.
     */
    public function updateFlag(Request $request, Flag $flag)
    {
        $request->validate([
            'status' => 'required|in:pending,resolved,dismissed',
            'moderation_notes' => 'nullable|string|max:500'
        ]);
        
        $oldStatus = $flag->status;
        $flag->update([
            'status' => $request->status,
            'moderated_by' => Auth::id(),
            'moderated_at' => now(),
            'moderation_notes' => $request->moderation_notes
        ]);
        
        // If flag is resolved and it's the last pending flag, auto-restrict the user
        if ($request->status == 'resolved') {
            $pendingFlags = Flag::where('reported_user_id', $flag->reported_user_id)
                ->where('status', 'pending')
                ->where('id', '!=', $flag->id)
                ->count();
            
            if ($pendingFlags >= 3) {
                // Auto-restrict user after 3 flags
                User::where('id', $flag->reported_user_id)->update([
                    'forum_restricted_until' => now()->addDays(7)
                ]);
                
                // Notify the restricted user
                $reportedUser = User::find($flag->reported_user_id);
                if ($reportedUser) {
                    $reportedUser->notify(new UserRestricted(7));
                }
            }
        }
        
        return back()->with('success', 'Flag status updated successfully!');
    }

    /**
     * Bulk update flags.
     */
    public function bulkUpdateFlags(Request $request)
    {
        $request->validate([
            'flag_ids' => 'required|array',
            'flag_ids.*' => 'exists:flags,id',
            'action' => 'required|in:resolve,dismiss'
        ]);
        
        $status = $request->action == 'resolve' ? 'resolved' : 'dismissed';
        
        Flag::whereIn('id', $request->flag_ids)
            ->where('status', 'pending')
            ->update([
                'status' => $status,
                'moderated_by' => Auth::id(),
                'moderated_at' => now()
            ]);
        
        return back()->with('success', count($request->flag_ids) . ' flags updated successfully!');
    }

    /**
     * Restrict user from forum with notification.
     */
    public function restrictUser(Request $request, User $user)
    {
        $request->validate([
            'restriction_days' => 'required|integer|min:1|max:365',
            'reason' => 'nullable|string|max:500'
        ]);
        
        $user->update([
            'forum_restricted_until' => now()->addDays($request->restriction_days)
        ]);
        
        // Notify the restricted user
        $user->notify(new UserRestricted($request->restriction_days));
        
        // Log the restriction
        activity()
            ->performedOn($user)
            ->causedBy(Auth::user())
            ->withProperties([
                'restriction_days' => $request->restriction_days,
                'reason' => $request->reason
            ])
            ->log('User restricted from forum');
        
        return back()->with('success', "User restricted for {$request->restriction_days} days.");
    }

    /**
     * Remove user restriction.
     */
    public function unrestrictUser(User $user)
    {
        $user->update([
            'forum_restricted_until' => null
        ]);
        
        return back()->with('success', 'User restriction removed.');
    }

    /**
     * Export forum data.
     */
    public function export(Request $request)
    {
        $query = ForumPost::with(['user', 'unit', 'replies']);
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $posts = $query->get();
        
        // Generate CSV
        $filename = 'forum_export_' . now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'w+');
        
        // Headers
        fputcsv($handle, [
            'Post ID', 'Title', 'Content', 'Author', 'Author Role',
            'Unit', 'Created At', 'Views', 'Replies', 'Is Pinned',
            'Is Announcement', 'Flag Count'
        ]);
        
        // Data
        foreach ($posts as $post) {
            fputcsv($handle, [
                $post->id,
                $post->title,
                strip_tags($post->content),
                $post->user->name ?? 'Unknown',
                $post->user->hasRole('lecturer') ? 'Lecturer' : ($post->user->hasRole('student') ? 'Student' : 'Other'),
                $post->unit_code,
                $post->created_at->format('Y-m-d H:i:s'),
                $post->views,
                $post->replies->count(),
                $post->is_pinned ? 'Yes' : 'No',
                $post->is_announcement ? 'Yes' : 'No',
                $post->flags()->count()
            ]);
        }
        
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        
        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Get forum statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_posts' => ForumPost::count(),
            'total_replies' => ForumReply::count(),
            'total_users_posted' => User::whereHas('forumPosts')->count(),
            'most_active_unit' => Unit::withCount('forumPosts')
                ->orderBy('forum_posts_count', 'desc')
                ->first(),
            'most_active_user' => User::withCount('forumPosts')
                ->orderBy('forum_posts_count', 'desc')
                ->first(),
            'posts_by_role' => [
                'lecturer' => ForumPost::whereHas('user', function($q) {
                    $q->whereHas('roles', function($r) {
                        $r->where('name', 'lecturer');
                    });
                })->count(),
                'student' => ForumPost::whereHas('user', function($q) {
                    $q->whereHas('roles', function($r) {
                        $r->where('name', 'student');
                    });
                })->count(),
            ],
            'posts_by_month' => ForumPost::selectRaw('YEAR(created_at) year, MONTH(created_at) month, COUNT(*) count')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get(),
        ];
        
        return response()->json($stats);
    }
}