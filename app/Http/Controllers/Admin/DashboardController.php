<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Unit;
use App\Models\Resource; // ADDED THIS
use App\Models\ForumPost;
use App\Models\AuditLog;
use App\Models\Flag;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get counts for dashboard stats
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::role('student')->count(),
            'total_lecturers' => User::role('lecturer')->count(),
            'total_courses' => Course::count(),
            'total_units' => Unit::count(),
            'total_forum_posts' => ForumPost::count(),
            'total_resources' => Resource::count(), // ADDED THIS LINE
            'recent_users' => User::latest()->take(5)->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->getRoleNames()->first() ?? 'No role',
                    'created_at' => $user->created_at->format('d M Y, H:i'),
                ];
            }),
            'recent_logs' => AuditLog::with('user')
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'user' => $log->user ? $log->user->name : 'System',
                        'action' => $log->action,
                        'module' => $log->module,
                        'description' => $log->description,
                        'time' => $log->created_at->format('d M Y, H:i'),
                    ];
                }),
        ];

        // Get data for charts
        $userRoles = [
            'Students' => User::role('student')->count(),
            'Lecturers' => User::role('lecturer')->count(),
            'Admins' => 1,
        ];
        
        // Get weekly activity data (last 7 days)
        $weeklyLabels = [];
        $weeklyPosts = [];
        $weeklyFlags = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $weeklyLabels[] = $date->format('D');
            
            $weeklyPosts[] = ForumPost::whereDate('created_at', $date)->count();
            $weeklyFlags[] = Flag::whereDate('created_at', $date)->count();
        }
        
        // Get top units by activity
        $topUnits = Unit::withCount('forumPosts')
            ->orderBy('forum_posts_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function($unit) {
                return [
                    'name' => $unit->name,
                    'posts' => $unit->forum_posts_count,
                ];
            });

        return view('admin.dashboard.index', [
            'stats' => $stats,
            'chartData' => [
                'userRoles' => $userRoles,
                'weeklyLabels' => $weeklyLabels,
                'weeklyPosts' => $weeklyPosts,
                'weeklyFlags' => $weeklyFlags,
                'topUnits' => $topUnits,
            ]
        ]);
    }
}