<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Display user notifications.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = $user->notifications();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', 'like', '%' . $request->type . '%');
        }

        // Filter by read/unread
        if ($request->filled('status')) {
            if ($request->status === 'read') {
                $query->whereNotNull('read_at');
            } elseif ($request->status === 'unread') {
                $query->whereNull('read_at');
            }
        }

        $notifications = $query->paginate(20);

        // Group notifications by date
        $grouped = $notifications->groupBy(function ($notification) {
            return $notification->created_at->format('Y-m-d');
        });

        return view('notifications.index', [
            'notifications' => $notifications,
            'grouped' => $grouped,
            'filters' => $request->only(['type', 'status'])
        ]);
    }

    /**
     * Get unread count for AJAX requests.
     */
    public function unreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();

        return response()->json([
            'count' => $count
        ]);
    }

    /**
     * Get recent notifications for dropdown.
     */
    public function recent()
    {
        $user = Auth::user();

        $recent = $user->notifications()
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->data['type'] ?? 'general',
                    'title' => $this->getNotificationTitle($notification),
                    'message' => $this->getNotificationMessage($notification),
                    'icon' => $this->getNotificationIcon($notification),
                    'color' => $this->getNotificationColor($notification),
                    'link' => $this->getNotificationLink($notification),
                    'time' => $notification->created_at->diffForHumans(),
                    'read_at' => $notification->read_at,
                    'is_read' => !is_null($notification->read_at)
                ];
            });

        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'notifications' => $recent,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Show notification preferences page.
     */
    public function getPreferences()
    {
        $user = Auth::user();
        
        $defaults = [
            'email_replies' => true,
            'email_new_posts' => true,
            'email_flags' => true,
            'push_replies' => true,
            'push_new_posts' => true,
            'push_flags' => true
        ];
        
        $preferences = array_merge($defaults, $user->notification_preferences ?? []);
        
        // Use the main preferences view with layout
        return view('notifications.preferences', compact('preferences'));
    }

    /**
     * Update notification preferences.
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'email_replies' => 'sometimes|boolean',
            'email_new_posts' => 'sometimes|boolean',
            'email_flags' => 'sometimes|boolean',
            'push_replies' => 'sometimes|boolean',
            'push_new_posts' => 'sometimes|boolean',
            'push_flags' => 'sometimes|boolean',
        ]);

        $preferences = [
            'email_replies' => $request->has('email_replies'),
            'email_new_posts' => $request->has('email_new_posts'),
            'email_flags' => $request->has('email_flags'),
            'push_replies' => $request->has('push_replies'),
            'push_new_posts' => $request->has('push_new_posts'),
            'push_flags' => $request->has('push_flags'),
        ];

        $user->notification_preferences = $preferences;
        $user->save();

        // Redirect back with success message
        return redirect()->route('notifications.preferences')
            ->with('success', 'Notification preferences saved successfully!');
    }

    /**
     * Helper: Get notification title.
     */
    private function getNotificationTitle($notification)
    {
        $data = $notification->data;

        switch ($data['type'] ?? '') {
            case 'forum_reply':
                return 'New Reply';
            case 'post_flagged':
                return 'Post Flagged';
            case 'post_pinned':
                return 'Post Pinned';
            case 'new_forum_post':
                return 'New Post';
            default:
                return 'Notification';
        }
    }

    /**
     * Helper: Get notification message.
     */
    private function getNotificationMessage($notification)
    {
        $data = $notification->data;

        switch ($data['type'] ?? '') {
            case 'forum_reply':
                return $data['replier_name'] . ' replied to "' . $data['post_title'] . '"';
            case 'post_flagged':
                return $data['reporter_name'] . ' flagged "' . $data['post_title'] . '"';
            case 'post_pinned':
                return 'Your post "' . $data['post_title'] . '" was pinned';
            case 'new_forum_post':
                return $data['author_name'] . ' posted in ' . $data['unit_code'];
            default:
                return 'You have a new notification';
        }
    }

    /**
     * Helper: Get notification icon.
     */
    private function getNotificationIcon($notification)
    {
        $data = $notification->data;

        switch ($data['type'] ?? '') {
            case 'forum_reply':
                return 'fa-reply';
            case 'post_flagged':
                return 'fa-flag';
            case 'post_pinned':
                return 'fa-thumbtack';
            case 'new_forum_post':
                return 'fa-comment';
            default:
                return 'fa-bell';
        }
    }

    /**
     * Helper: Get notification color.
     */
    private function getNotificationColor($notification)
    {
        $data = $notification->data;

        switch ($data['type'] ?? '') {
            case 'forum_reply':
                return '#3b82f6';
            case 'post_flagged':
                return '#ef4444';
            case 'post_pinned':
                return '#f59e0b';
            case 'new_forum_post':
                return '#10b981';
            default:
                return '#64748b';
        }
    }

    /**
     * Helper: Get notification link.
     */
    private function getNotificationLink($notification)
    {
        $data = $notification->data;

        switch ($data['type'] ?? '') {
            case 'forum_reply':
            case 'new_forum_post':
                return '/forum/' . ($data['post_id'] ?? '');
            case 'post_flagged':
                return '/admin/forum/flags';
            case 'post_pinned':
                return '/forum/' . ($data['post_id'] ?? '');
            default:
                return '/notifications';
        }
    }
}