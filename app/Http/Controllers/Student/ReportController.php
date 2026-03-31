<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Resource;
use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\StudySession;
use App\Models\Deadline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display student reports.
     */
    public function index(Request $request)
    {
        $student = Auth::user();
        $unitId = $request->get('unit_id');
        
        // Get enrolled units
        $units = $student->enrolledUnits()->orderBy('code')->get();
        
        $reportData = null;
        $selectedUnit = null;
        
        if ($unitId) {
            $selectedUnit = Unit::findOrFail($unitId);
            
            // Check if student is enrolled
            if (!$student->enrolledUnits()->where('unit_id', $unitId)->exists()) {
                abort(403, 'You are not enrolled in this unit.');
            }
            
            $reportData = $this->getUnitReportData($student, $selectedUnit);
        } else {
            $reportData = $this->getOverallReportData($student);
        }
        
        return view('student.reports.index', [
            'student' => $student,
            'units' => $units,
            'selectedUnit' => $selectedUnit,
            'reportData' => $reportData,
            'filters' => $request->only(['unit_id'])
        ]);
    }

    /**
     * Get overall report data for all units.
     */
    private function getOverallReportData($student)
    {
        $enrolledUnits = $student->enrolledUnits()->get();
        $unitIds = $enrolledUnits->pluck('id');
        
        // Study time stats
        $totalStudySeconds = StudySession::where('student_id', $student->id)
            ->sum('duration_seconds');
        
        $studyTimeByUnit = StudySession::where('student_id', $student->id)
            ->select('unit_id', DB::raw('SUM(duration_seconds) as total_seconds'))
            ->whereIn('unit_id', $unitIds)
            ->groupBy('unit_id')
            ->get()
            ->keyBy('unit_id');
        
        // Resource stats
        $totalResources = Resource::whereIn('unit_id', $unitIds)->count();
        $resourcesAccessed = 0; // Track actual downloads
        
        // Forum stats
        $forumPosts = ForumPost::where('user_id', $student->id)
            ->whereIn('unit_id', $unitIds)
            ->count();
        
        $forumReplies = ForumReply::where('user_id', $student->id)
            ->whereIn('forum_post_id', ForumPost::whereIn('unit_id', $unitIds)->pluck('id'))
            ->count();
        
        // Deadline stats
        $acceptedDeadlines = $student->acceptedDeadlines()->count();
        $completedDeadlines = $student->acceptedDeadlines()
            ->wherePivotNotNull('completed_at')
            ->count();
        
        $deadlineCompletionRate = $acceptedDeadlines > 0 
            ? round(($completedDeadlines / $acceptedDeadlines) * 100, 1)
            : 0;
        
        // Unit breakdown
        $unitBreakdown = [];
        foreach ($enrolledUnits as $unit) {
            $unitStudySeconds = $studyTimeByUnit[$unit->id]->total_seconds ?? 0;
            $unitResources = Resource::where('unit_id', $unit->id)->count();
            $unitPosts = ForumPost::where('unit_id', $unit->id)
                ->where('user_id', $student->id)
                ->count();
            
            $unitBreakdown[] = [
                'code' => $unit->code,
                'name' => $unit->name,
                'study_time' => $this->formatTime($unitStudySeconds),
                'study_minutes' => round($unitStudySeconds / 60, 1),
                'resources' => $unitResources,
                'forum_posts' => $unitPosts
            ];
        }
        
        return [
            'study' => [
                'total_seconds' => $totalStudySeconds,
                'total_formatted' => $this->formatTime($totalStudySeconds),
                'total_hours' => round($totalStudySeconds / 3600, 1),
                'by_unit' => $studyTimeByUnit
            ],
            'resources' => [
                'total' => $totalResources,
                'accessed' => $resourcesAccessed
            ],
            'forum' => [
                'posts' => $forumPosts,
                'replies' => $forumReplies,
                'total' => $forumPosts + $forumReplies
            ],
            'deadlines' => [
                'accepted' => $acceptedDeadlines,
                'completed' => $completedDeadlines,
                'completion_rate' => $deadlineCompletionRate
            ],
            'unit_breakdown' => $unitBreakdown
        ];
    }

    /**
     * Get report data for a specific unit.
     */
    private function getUnitReportData($student, $unit)
    {
        // Study time
        $studySeconds = StudySession::where('student_id', $student->id)
            ->where('unit_id', $unit->id)
            ->sum('duration_seconds');
        
        // Weekly study pattern
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $seconds = StudySession::where('student_id', $student->id)
                ->where('unit_id', $unit->id)
                ->whereDate('started_at', $date->toDateString())
                ->sum('duration_seconds');
            
            $weeklyData[] = [
                'date' => $date->format('D'),
                'minutes' => round($seconds / 60, 1)
            ];
        }
        
        // Resources
        $resources = Resource::where('unit_id', $unit->id)
            ->orderBy('download_count', 'desc')
            ->limit(5)
            ->get(['title', 'download_count']);
        
        // Forum activity
        $forumPosts = ForumPost::where('unit_id', $unit->id)
            ->where('user_id', $student->id)
            ->count();
        
        $forumReplies = ForumReply::whereIn('forum_post_id', 
            ForumPost::where('unit_id', $unit->id)->pluck('id'))
            ->where('user_id', $student->id)
            ->count();
        
        // Deadlines
        $unitDeadlines = Deadline::where('unit_id', $unit->id)->get();
        $acceptedDeadlines = $student->acceptedDeadlines()
            ->where('unit_id', $unit->id)
            ->count();
        
        $completedDeadlines = $student->acceptedDeadlines()
            ->where('unit_id', $unit->id)
            ->wherePivotNotNull('completed_at')
            ->count();
        
        return [
            'unit' => $unit,
            'study' => [
                'total_formatted' => $this->formatTime($studySeconds),
                'total_minutes' => round($studySeconds / 60, 1),
                'weekly' => $weeklyData
            ],
            'resources' => [
                'total' => Resource::where('unit_id', $unit->id)->count(),
                'top' => $resources
            ],
            'forum' => [
                'posts' => $forumPosts,
                'replies' => $forumReplies,
                'total' => $forumPosts + $forumReplies
            ],
            'deadlines' => [
                'total' => $unitDeadlines->count(),
                'accepted' => $acceptedDeadlines,
                'completed' => $completedDeadlines,
                'completion_rate' => $acceptedDeadlines > 0 
                    ? round(($completedDeadlines / $acceptedDeadlines) * 100, 1)
                    : 0
            ]
        ];
    }

    /**
     * Format seconds to readable time.
     */
    private function formatTime($seconds)
    {
        if ($seconds < 60) {
            return $seconds . ' sec';
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            return $minutes . ' min';
        } else {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            return $hours . 'h ' . $minutes . 'm';
        }
    }
}