<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudyProgress;
use App\Models\Unit;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudyProgressController extends Controller
{
    /**
     * Display study progress dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all enrolled units using the relationship from User model
        $enrolledUnits = $user->enrolledUnits()->get();
        
        // Get progress for all units
        $progress = StudyProgress::where('user_id', $user->id)
                                 ->with(['unit', 'topic'])
                                 ->get()
                                 ->keyBy(function ($item) {
                                     return $item->unit_code . '_' . $item->topic_id;
                                 });
        
        // Calculate overall statistics
        $stats = $this->calculateStats($enrolledUnits, $progress);
        
        // Get recent activity
        $recentActivity = StudyProgress::where('user_id', $user->id)
                                       ->where('last_accessed_at', '!=', null)
                                       ->with(['unit', 'topic'])
                                       ->orderBy('last_accessed_at', 'desc')
                                       ->take(10)
                                       ->get();
        
        // Get study streak
        $streak = $this->calculateStreak($user->id);
        
        return view('student.study.index', compact('enrolledUnits', 'progress', 'stats', 'recentActivity', 'streak'));
    }

    /**
     * Show progress for a specific unit.
     */
    public function unitProgress($unitCode)
    {
        $unit = Unit::where('code', $unitCode)->firstOrFail();
        $user = Auth::user();
        
        // Check if student is enrolled using the relationship
        $enrolled = $user->enrolledUnits()
                         ->where('unit_id', $unit->id)
                         ->exists();
        
        if (!$enrolled) {
            return redirect()->route('student.units.available')
                            ->with('error', 'You must enroll in this unit first.');
        }
        
        // Get all topics for the unit
        $topics = Topic::where('unit_code', $unitCode)
                       ->where('status', 'published')
                       ->orderBy('order')
                       ->get();
        
        // Get progress for each topic
        $progress = StudyProgress::where('user_id', Auth::id())
                                 ->where('unit_code', $unitCode)
                                 ->get()
                                 ->keyBy('topic_id');
        
        // Calculate unit statistics
        $totalTopics = $topics->count();
        $completedTopics = $progress->where('status', 'completed')->count();
        $inProgressTopics = $progress->where('status', 'in_progress')->count();
        $totalTimeSpent = $progress->sum('time_spent_minutes');
        
        // Get mastered concepts
        $masteredConcepts = [];
        $conceptsInProgress = [];
        
        foreach ($progress as $p) {
            if ($p->concepts_mastered) {
                $masteredConcepts = array_merge($masteredConcepts, $p->concepts_mastered);
            }
            if ($p->concepts_in_progress) {
                $conceptsInProgress = array_merge($conceptsInProgress, $p->concepts_in_progress);
            }
        }
        
        return view('student.study.unit', compact(
            'unit', 
            'topics', 
            'progress', 
            'totalTopics',
            'completedTopics',
            'inProgressTopics',
            'totalTimeSpent',
            'masteredConcepts',
            'conceptsInProgress'
        ));
    }

    /**
     * Start or continue studying a topic.
     */
    public function startTopic($unitCode, $topicId)
    {
        $topic = Topic::where('unit_code', $unitCode)
                      ->findOrFail($topicId);
        
        // Get or create progress record
        $progress = StudyProgress::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'unit_code' => $unitCode,
                'topic_id' => $topicId
            ],
            [
                'status' => 'not_started',
                'progress_percentage' => 0,
                'time_spent_minutes' => 0
            ]
        );
        
        // Mark as started if not already
        if ($progress->status === 'not_started') {
            $progress->markAsStarted();
        }
        
        return redirect()->route('student.topics.show', [$unitCode, $topicId])
                        ->with('success', 'Resuming your study session...');
    }

    /**
     * Update progress for a topic.
     */
    public function updateProgress(Request $request, $unitCode, $topicId)
    {
        $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'time_spent' => 'nullable|integer|min:0',
            'concept' => 'nullable|string'
        ]);
        
        $progress = StudyProgress::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'unit_code' => $unitCode,
                'topic_id' => $topicId
            ],
            [
                'status' => 'not_started',
                'progress_percentage' => 0,
                'time_spent_minutes' => 0
            ]
        );
        
        // Update progress percentage
        if ($request->has('progress')) {
            $progress->updateProgress($request->progress);
        }
        
        // Add time spent
        if ($request->has('time_spent') && $request->time_spent > 0) {
            $progress->addTimeSpent($request->time_spent);
        }
        
        // Add concept if provided
        if ($request->has('concept')) {
            if ($request->progress >= 80) {
                $progress->addMasteredConcept($request->concept);
            } else {
                $progress->addConceptInProgress($request->concept);
            }
        }
        
        return response()->json([
            'success' => true,
            'progress' => $progress
        ]);
    }

    /**
     * Mark topic as completed.
     */
    public function completeTopic($unitCode, $topicId)
    {
        $progress = StudyProgress::where('user_id', Auth::id())
                                 ->where('unit_code', $unitCode)
                                 ->where('topic_id', $topicId)
                                 ->firstOrFail();
        
        $progress->markAsCompleted();
        
        // Check if unit is completed
        $this->checkUnitCompletion($unitCode);
        
        return response()->json([
            'success' => true,
            'message' => 'Topic marked as completed!'
        ]);
    }

    /**
     * Get study recommendations.
     */
    public function recommendations()
    {
        $user = Auth::user();
        
        // Get topics that need attention (low progress, not completed)
        $inProgress = StudyProgress::where('user_id', $user->id)
                                   ->where('status', 'in_progress')
                                   ->with(['unit', 'topic'])
                                   ->orderBy('last_accessed_at', 'asc')
                                   ->take(5)
                                   ->get();
        
        // Get topics not started using the relationship
        $enrolledUnits = $user->enrolledUnits()->get();
        
        $notStarted = [];
        foreach ($enrolledUnits as $unit) {
            $topics = Topic::where('unit_code', $unit->code)
                           ->where('status', 'published')
                           ->orderBy('order')
                           ->get();
            
            foreach ($topics as $topic) {
                $hasProgress = StudyProgress::where('user_id', $user->id)
                                            ->where('topic_id', $topic->id)
                                            ->exists();
                
                if (!$hasProgress) {
                    $notStarted[] = [
                        'unit' => $unit,
                        'topic' => $topic
                    ];
                }
            }
        }
        
        return view('student.study.recommendations', compact('inProgress', 'notStarted'));
    }

    /**
     * Get study statistics for charts.
     */
    public function statistics()
    {
        $user = Auth::id();
        
        // Get progress over time (last 30 days)
        $dailyProgress = StudyProgress::where('user_id', $user)
                                      ->whereNotNull('last_accessed_at')
                                      ->where('last_accessed_at', '>=', now()->subDays(30))
                                      ->get()
                                      ->groupBy(function($item) {
                                          return $item->last_accessed_at->format('Y-m-d');
                                      })
                                      ->map(function($day) {
                                          return $day->sum('time_spent_minutes');
                                      });
        
        // Get progress by unit
        $unitProgress = StudyProgress::where('user_id', $user)
                                     ->with('unit')
                                     ->get()
                                     ->groupBy('unit_code')
                                     ->map(function($items) {
                                         $totalTopics = Topic::where('unit_code', $items->first()->unit_code)
                                                             ->where('status', 'published')
                                                             ->count();
                                         
                                         $completed = $items->where('status', 'completed')->count();
                                         
                                         return [
                                             'unit' => $items->first()->unit,
                                             'completed' => $completed,
                                             'total' => $totalTopics,
                                             'percentage' => $totalTopics > 0 ? round(($completed / $totalTopics) * 100) : 0
                                         ];
                                     })
                                     ->values();
        
        return response()->json([
            'success' => true,
            'daily' => $dailyProgress,
            'units' => $unitProgress
        ]);
    }

    /**
     * Calculate overall statistics.
     */
    private function calculateStats($enrolledUnits, $progress)
    {
        $stats = [
            'total_units' => $enrolledUnits->count(),
            'total_topics' => 0,
            'completed_topics' => 0,
            'in_progress_topics' => 0,
            'total_time_spent' => 0,
            'mastered_concepts' => 0,
            'average_progress' => 0
        ];
        
        foreach ($enrolledUnits as $unit) {
            $topics = Topic::where('unit_code', $unit->code)
                           ->where('status', 'published')
                           ->count();
            
            $stats['total_topics'] += $topics;
        }
        
        foreach ($progress as $p) {
            if ($p->status === 'completed') {
                $stats['completed_topics']++;
            } elseif ($p->status === 'in_progress') {
                $stats['in_progress_topics']++;
            }
            
            $stats['total_time_spent'] += $p->time_spent_minutes;
            $stats['mastered_concepts'] += count($p->concepts_mastered ?? []);
        }
        
        if ($stats['total_topics'] > 0) {
            $stats['average_progress'] = round(($stats['completed_topics'] / $stats['total_topics']) * 100);
        }
        
        return $stats;
    }

    /**
     * Calculate study streak.
     */
    private function calculateStreak($userId)
    {
        $progress = StudyProgress::where('user_id', $userId)
                                 ->whereNotNull('last_accessed_at')
                                 ->orderBy('last_accessed_at', 'desc')
                                 ->get()
                                 ->pluck('last_accessed_at')
                                 ->map(function($date) {
                                     return $date->format('Y-m-d');
                                 })
                                 ->unique()
                                 ->values();
        
        if ($progress->isEmpty()) {
            return [
                'current' => 0,
                'longest' => 0
            ];
        }
        
        $streak = 1;
        $longest = 1;
        $currentDate = now()->format('Y-m-d');
        
        // Check if studied today
        if ($progress->first() !== $currentDate) {
            $streak = 0;
        }
        
        // Calculate streak
        for ($i = 1; $i < $progress->count(); $i++) {
            $prev = date('Y-m-d', strtotime($progress[$i - 1] . ' -1 day'));
            
            if ($prev === $progress[$i]) {
                $streak++;
                $longest = max($longest, $streak);
            } else {
                $streak = 1;
            }
        }
        
        return [
            'current' => $streak,
            'longest' => $longest
        ];
    }

    /**
     * Check if unit is completed.
     */
    private function checkUnitCompletion($unitCode)
    {
        $totalTopics = Topic::where('unit_code', $unitCode)
                            ->where('status', 'published')
                            ->count();
        
        $completedTopics = StudyProgress::where('user_id', Auth::id())
                                        ->where('unit_code', $unitCode)
                                        ->where('status', 'completed')
                                        ->count();
        
        if ($totalTopics > 0 && $completedTopics === $totalTopics) {
            // Unit completed! Could trigger achievement or notification
            session()->flash('achievement', 'Congratulations! You\'ve completed all topics in this unit!');
        }
    }
}