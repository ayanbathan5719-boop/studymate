<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Resource;
use App\Models\Deadline;
use App\Models\ForumPost;
use App\Models\StudyProgress;
use App\Models\Topic;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the student dashboard.
     */
    public function index()
    {
        $student = Auth::user();
        
        // Get units the student is enrolled in (APPROVED ONLY)
        $approvedEnrollments = Enrollment::where('student_id', $student->id)
                                         ->where('status', 'approved')
                                         ->with('unit')
                                         ->get();
        
        $enrolledUnits = $approvedEnrollments->pluck('unit');
        
        $enrolledUnitIds = $enrolledUnits->pluck('id');
        $enrolledUnitCodes = $enrolledUnits->pluck('code');
        
        // Get upcoming deadlines
        $upcomingDeadlines = Deadline::whereIn('unit_id', $enrolledUnitIds)
            ->with('unit')
            ->where('due_date', '>', now())
            ->orderBy('due_date')
            ->limit(5)
            ->get();
        
        // Get recent resources from enrolled units
        $recentResources = Resource::whereIn('unit_id', $enrolledUnitIds)
            ->with(['unit', 'user'])
            ->latest()
            ->limit(5)
            ->get();
        
        // Get recent forum posts from enrolled units
        $recentForumPosts = ForumPost::whereIn('unit_id', $enrolledUnitIds)
            ->with(['user', 'unit'])
            ->latest()
            ->limit(5)
            ->get();
        
        // Get study progress stats
        $totalStudyTime = StudyProgress::where('student_id', $student->id)
            ->sum('duration_minutes');
        
        $completedTopics = StudyProgress::where('student_id', $student->id)
            ->where('completed', true)
            ->count();
        
        $currentStreak = $this->calculateStreak($student->id);
        
        // Stats for dashboard cards
        $stats = [
            'enrolled_units' => $enrolledUnits->count(),
            'total_resources' => Resource::whereIn('unit_id', $enrolledUnitIds)->count(),
            'upcoming_deadlines' => $upcomingDeadlines->count(),
            'forum_posts' => ForumPost::whereIn('unit_id', $enrolledUnitIds)->count(),
            'study_time' => $totalStudyTime,
            'completed_topics' => $completedTopics,
            'current_streak' => $currentStreak,
        ];
        
        // Get recommended resources based on study patterns
        $recommendedResources = $this->getRecommendedResources($student->id, $enrolledUnitIds);
        
        // Get greeting based on time of day
        $greeting = $this->getGreeting();
        
        // Get last studied topic
        $lastStudiedTopic = $this->getLastStudiedTopic($student->id);
        
        // Get pending request count
        $pendingCount = Enrollment::where('student_id', $student->id)
                                  ->where('status', 'pending')
                                  ->count();

        return view('student.dashboard', [
            'student' => $student,
            'enrolledUnits' => $enrolledUnits,
            'upcomingDeadlines' => $upcomingDeadlines,
            'recentResources' => $recentResources,
            'recentForumPosts' => $recentForumPosts,
            'recommendedResources' => $recommendedResources,
            'stats' => $stats,
            'greeting' => $greeting,
            'lastStudiedTopic' => $lastStudiedTopic,
            'pendingCount' => $pendingCount,
        ]);
    }
    
    /**
     * Calculate current study streak (consecutive days with activity).
     */
    private function calculateStreak($studentId)
    {
        $progress = StudyProgress::where('student_id', $studentId)
            ->selectRaw('DATE(created_at) as date')
            ->distinct()
            ->orderBy('date', 'desc')
            ->get()
            ->pluck('date');
        
        if ($progress->isEmpty()) {
            return 0;
        }
        
        $streak = 1;
        $currentDate = now()->startOfDay();
        
        // Check if studied today
        if (!$progress->contains($currentDate->toDateString())) {
            return 0;
        }
        
        // Count consecutive days
        while ($progress->contains($currentDate->subDay()->toDateString())) {
            $streak++;
        }
        
        return $streak;
    }
    
    /**
     * Get recommended resources based on student's study patterns.
     */
    private function getRecommendedResources($studentId, $enrolledUnitIds)
    {
        // Get topics the student has struggled with (spent most time)
        $difficultTopics = StudyProgress::where('student_id', $studentId)
            ->where('completed', false)
            ->orderBy('duration_minutes', 'desc')
            ->limit(3)
            ->pluck('topic_id');
        
        // Get recently accessed resources
        $recentlyAccessed = Resource::whereHas('downloads', function($query) use ($studentId) {
                $query->where('user_id', $studentId);
            })
            ->latest()
            ->limit(5)
            ->pluck('id');
        
        // Build recommendations query
        $query = Resource::whereIn('unit_id', $enrolledUnitIds)
            ->with(['unit', 'topic', 'user']);
        
        // Prioritize resources from struggling topics
        if ($difficultTopics->isNotEmpty()) {
            $query->whereIn('topic_id', $difficultTopics);
        } else {
            // Otherwise exclude recently accessed
            if ($recentlyAccessed->isNotEmpty()) {
                $query->whereNotIn('id', $recentlyAccessed);
            }
        }
        
        return $query->latest()->limit(6)->get();
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
     * Get the last studied topic for the student.
     */
    private function getLastStudiedTopic($studentId)
    {
        // Get the most recent study progress entry
        $lastProgress = StudyProgress::where('student_id', $studentId)
                                     ->where('last_studied_at', '!=', null)
                                     ->orderBy('last_studied_at', 'desc')
                                     ->first();
        
        if (!$lastProgress) {
            return null;
        }
        
        // Get the topic if it exists
        if ($lastProgress->topic_id) {
            $topic = Topic::with('unit')->find($lastProgress->topic_id);
            if ($topic) {
                return $topic;
            }
        }
        
        return null;
    }
}