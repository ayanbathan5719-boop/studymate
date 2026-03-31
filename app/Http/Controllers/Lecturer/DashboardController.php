<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Deadline;
use App\Models\Resource;
use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the lecturer dashboard.
     */
    public function index()
    {
        $lecturer = Auth::user();
        $lecturerId = $lecturer->id;
        
        // Get units taught by this lecturer (using the relationship)
        $myUnits = $lecturer->units()
            ->withCount(['resources', 'forumPosts', 'students'])
            ->get();
        
        $myUnitIds = $myUnits->pluck('id'); // Get IDs for queries
        
        // Get pending deadlines in lecturer's units
        $pendingDeadlines = Deadline::whereIn('unit_id', $myUnitIds) // FIXED: using unit_id
            ->with('unit')
            ->where('due_date', '>', now())
            ->orderBy('due_date')
            ->limit(5)
            ->get();
        
        // Get recent resource uploads - FIXED: using unit_id and user relationship
        $recentResources = Resource::whereIn('unit_id', $myUnitIds) // FIXED: using unit_id not unit_code
            ->with(['unit', 'user']) // FIXED: using 'user' not 'uploader'
            ->latest()
            ->limit(5)
            ->get();
        
        // Get recent forum posts in lecturer's units (for monitoring)
        $recentForumPosts = ForumPost::whereIn('unit_id', $myUnitIds)
            ->with(['user', 'unit'])
            ->latest()
            ->limit(5)
            ->get();
        
        // Count pending student questions (posts without lecturer reply)
        $pendingQuestions = ForumPost::whereIn('unit_id', $myUnitIds)
            ->whereDoesntHave('replies', function($query) use ($lecturerId) {
                $query->where('user_id', $lecturerId);
            })
            ->count();
        
        // Stats for dashboard cards - FIXED: using unit_id not unit_code
        $stats = [
            'units_count' => $myUnits->count(),
            'active_deadlines' => $pendingDeadlines->count(),
            'resources_count' => Resource::whereIn('unit_id', $myUnitIds)->count(), // FIXED
            'forum_posts_count' => ForumPost::where('user_id', $lecturerId)->count(),
            'pending_questions' => $pendingQuestions,
            'total_students' => $myUnits->sum('students_count'),
        ];
        
        // Get greeting based on time of day (NO EMOJIS)
        $greeting = $this->getGreeting();
        
        // Get motivational message based on activity
        $motivationalMessage = $this->getMotivationalMessage($stats);
        
        return view('lecturer.dashboard', [
            'lecturer' => $lecturer,
            'myUnits' => $myUnits,
            'pendingDeadlines' => $pendingDeadlines,
            'recentResources' => $recentResources,
            'recentForumPosts' => $recentForumPosts,
            'stats' => $stats,
            'greeting' => $greeting,
            'motivationalMessage' => $motivationalMessage,
        ]);
    }
    
    /**
     * Get greeting based on time of day.
     */
    private function getGreeting()
    {
        $hour = now()->hour;
        
        if ($hour < 12) {
            return 'Good morning';
        } elseif ($hour < 17) {
            return 'Good afternoon';
        } else {
            return 'Good evening';
        }
    }
    
    /**
     * Get motivational message based on activity.
     * NO EMOJIS - using Font Awesome icons in the view instead.
     */
    private function getMotivationalMessage($stats)
    {
        if ($stats['pending_questions'] > 0) {
            return [
                'icon' => 'fa-comment-question', // Font Awesome icon class
                'message' => "You have {$stats['pending_questions']} student question" . ($stats['pending_questions'] > 1 ? 's' : '') . " waiting for your response.",
                'type' => 'info',
                'action' => 'View Questions',
                'action_url' => '/lecturer/forum?filter=unanswered'
            ];
        } elseif ($stats['active_deadlines'] == 0) {
            return [
                'icon' => 'fa-calendar', // Font Awesome icon class
                'message' => 'No upcoming deadlines. Consider setting new assignments for your units.',
                'type' => 'reminder',
                'action' => 'Set Deadline',
                'action_url' => '/lecturer/deadlines/create'
            ];
        } elseif ($stats['resources_count'] < $stats['units_count']) {
            return [
                'icon' => 'fa-folder-open', // Font Awesome icon class
                'message' => 'Some units have no resources yet. Upload lecture notes to help your students.',
                'type' => 'gentle',
                'action' => 'Upload Resource',
                'action_url' => '/lecturer/resources/create'
            ];
        } else {
            return [
                'icon' => 'fa-star', // Font Awesome icon class
                'message' => 'Great job keeping your units active! Students appreciate your engagement.',
                'type' => 'excellent',
                'action' => null,
                'action_url' => null
            ];
        }
    }
}