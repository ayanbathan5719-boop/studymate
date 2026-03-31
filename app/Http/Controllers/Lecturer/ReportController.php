<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Resource;
use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\StudySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display reports dashboard.
     */
    public function index(Request $request)
    {
        $lecturer = Auth::user();
        $unitId = $request->get('unit_id');
        
        // Get units taught by this lecturer
        $units = $lecturer->units()->orderBy('code')->get();
        
        // If no unit selected, use first unit
        if (!$unitId && $units->isNotEmpty()) {
            $unitId = $units->first()->id;
        }
        
        $reportData = null;
        $selectedUnit = null;
        
        if ($unitId) {
            $selectedUnit = Unit::findOrFail($unitId);
            
            // Verify lecturer owns this unit
            if (!$lecturer->units()->where('unit_id', $unitId)->exists()) {
                abort(403, 'You do not have access to this unit.');
            }
            
            // Get date range filters
            $dateFrom = $request->get('from');
            $dateTo = $request->get('to');
            
            $reportData = $this->getUnitReportData($selectedUnit, $dateFrom, $dateTo);
        }
        
        return view('lecturer.reports.index', [
            'units' => $units,
            'selectedUnit' => $selectedUnit,
            'reportData' => $reportData,
            'filters' => $request->only(['unit_id', 'from', 'to'])
        ]);
    }

    /**
     * Get comprehensive report data for a unit.
     */
    private function getUnitReportData($unit, $dateFrom = null, $dateTo = null)
    {
        // Base queries with date filters
        $resourcesQuery = Resource::where('unit_id', $unit->id);
        $forumPostsQuery = ForumPost::where('unit_id', $unit->id);
        $forumRepliesQuery = ForumReply::whereIn('forum_post_id', ForumPost::where('unit_id', $unit->id)->pluck('id'));
        $studySessionsQuery = StudySession::whereHas('unit', function($q) use ($unit) {
            $q->where('unit_id', $unit->id);
        });
        
        if ($dateFrom) {
            $resourcesQuery->whereDate('created_at', '>=', $dateFrom);
            $forumPostsQuery->whereDate('created_at', '>=', $dateFrom);
            $forumRepliesQuery->whereDate('created_at', '>=', $dateFrom);
            $studySessionsQuery->whereDate('started_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $resourcesQuery->whereDate('created_at', '<=', $dateTo);
            $forumPostsQuery->whereDate('created_at', '<=', $dateTo);
            $forumRepliesQuery->whereDate('created_at', '<=', $dateTo);
            $studySessionsQuery->whereDate('started_at', '<=', $dateTo);
        }
        
        // Resource statistics
        $resources = $resourcesQuery->get();
        $totalResources = $resources->count();
        $totalDownloads = $resources->sum('download_count');
        $resourcesByType = $resources->groupBy('file_type')
            ->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'downloads' => $items->sum('download_count')
                ];
            });
        
        // Forum statistics
        $forumPosts = $forumPostsQuery->get();
        $totalPosts = $forumPosts->count();
        $totalReplies = $forumRepliesQuery->count();
        $postsByLecturer = $forumPosts->where('user_id', Auth::id())->count();
        $postsByStudents = $totalPosts - $postsByLecturer;
        
        // Student engagement
        $students = $unit->students()->get();
        $totalStudents = $students->count();
        
        $activeStudents = $forumPostsQuery->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');
        
        // Study time statistics
        $studySessions = $studySessionsQuery->get();
        $totalStudyTime = $studySessions->sum('duration_seconds');
        $averageStudyTimePerStudent = $totalStudents > 0 
            ? round($totalStudyTime / $totalStudents / 60, 1) 
            : 0;
        
        // Weekly activity
        $weeklyActivity = $this->getWeeklyActivity($unit, $dateFrom, $dateTo);
        
        // Top resources
        $topResources = Resource::where('unit_id', $unit->id)
            ->orderBy('download_count', 'desc')
            ->limit(5)
            ->get(['title', 'download_count', 'created_at']);
        
        return [
            'resources' => [
                'total' => $totalResources,
                'total_downloads' => $totalDownloads,
                'by_type' => $resourcesByType,
                'top_resources' => $topResources,
            ],
            'forum' => [
                'total_posts' => $totalPosts,
                'total_replies' => $totalReplies,
                'posts_by_lecturer' => $postsByLecturer,
                'posts_by_students' => $postsByStudents,
                'active_students' => $activeStudents,
            ],
            'students' => [
                'total' => $totalStudents,
                'active' => $activeStudents,
                'engagement_rate' => $totalStudents > 0 
                    ? round(($activeStudents / $totalStudents) * 100, 1)
                    : 0,
            ],
            'study_time' => [
                'total_seconds' => $totalStudyTime,
                'total_hours' => round($totalStudyTime / 3600, 1),
                'average_per_student' => $averageStudyTimePerStudent,
            ],
            'weekly_activity' => $weeklyActivity,
        ];
    }

    /**
     * Get weekly activity data for charts.
     */
    private function getWeeklyActivity($unit, $dateFrom, $dateTo)
    {
        $startDate = $dateFrom ? \Carbon\Carbon::parse($dateFrom) : now()->subDays(30);
        $endDate = $dateTo ? \Carbon\Carbon::parse($dateTo) : now();
        
        $dates = [];
        $posts = [];
        $replies = [];
        $studyTime = [];
        
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $dates[] = $currentDate->format('M d');
            
            // Posts on this date
            $posts[] = ForumPost::where('unit_id', $unit->id)
                ->whereDate('created_at', $dateStr)
                ->count();
            
            // Replies on this date
            $replies[] = ForumReply::whereIn('forum_post_id', 
                ForumPost::where('unit_id', $unit->id)->pluck('id'))
                ->whereDate('created_at', $dateStr)
                ->count();
            
            // Study time on this date
            $seconds = StudySession::whereHas('unit', function($q) use ($unit) {
                    $q->where('unit_id', $unit->id);
                })
                ->whereDate('started_at', $dateStr)
                ->sum('duration_seconds');
            
            $studyTime[] = round($seconds / 60, 1); // Convert to minutes
            
            $currentDate->addDay();
        }
        
        return [
            'dates' => $dates,
            'posts' => $posts,
            'replies' => $replies,
            'study_time' => $studyTime,
        ];
    }

    /**
     * Export report as PDF.
     */
    public function exportPdf(Request $request)
    {
        $lecturer = Auth::user();
        $unitId = $request->get('unit_id');
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');
        
        $unit = Unit::findOrFail($unitId);
        
        // Verify lecturer owns this unit
        if (!$lecturer->units()->where('unit_id', $unitId)->exists()) {
            abort(403, 'You do not have access to this unit.');
        }
        
        $reportData = $this->getUnitReportData($unit, $dateFrom, $dateTo);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('lecturer.reports.pdf', [
            'unit' => $unit,
            'data' => $reportData,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'generatedAt' => now()->format('d M Y, H:i:s')
        ]);
        
        return $pdf->download('unit-report-' . $unit->code . '-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export report as CSV.
     */
    public function exportCsv(Request $request)
    {
        $lecturer = Auth::user();
        $unitId = $request->get('unit_id');
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');
        
        $unit = Unit::findOrFail($unitId);
        
        // Verify lecturer owns this unit
        if (!$lecturer->units()->where('unit_id', $unitId)->exists()) {
            abort(403, 'You do not have access to this unit.');
        }
        
        $filename = $unit->code . '-report-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($unit, $dateFrom, $dateTo) {
            $handle = fopen('php://output', 'w');
            
            // Title
            fputcsv($handle, ['Unit Report: ' . $unit->code . ' - ' . $unit->name]);
            fputcsv($handle, ['Generated: ' . now()->format('d M Y, H:i:s')]);
            if ($dateFrom || $dateTo) {
                fputcsv($handle, ['Date Range: ' . ($dateFrom ?: 'All') . ' to ' . ($dateTo ?: 'All')]);
            }
            fputcsv($handle, []);
            
            // Resource Summary
            fputcsv($handle, ['RESOURCE SUMMARY']);
            fputcsv($handle, ['Total Resources', Resource::where('unit_id', $unit->id)
                ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                ->count()]);
            
            fputcsv($handle, []);
            
            // Top Resources
            fputcsv($handle, ['TOP RESOURCES']);
            fputcsv($handle, ['Title', 'Downloads', 'Uploaded']);
            $topResources = Resource::where('unit_id', $unit->id)
                ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                ->orderBy('download_count', 'desc')
                ->limit(10)
                ->get(['title', 'download_count', 'created_at']);
            
            foreach ($topResources as $resource) {
                fputcsv($handle, [
                    $resource->title,
                    $resource->download_count,
                    $resource->created_at->format('d M Y')
                ]);
            }
            
            fputcsv($handle, []);
            
            // Forum Summary
            fputcsv($handle, ['FORUM SUMMARY']);
            fputcsv($handle, ['Total Posts', ForumPost::where('unit_id', $unit->id)
                ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                ->count()]);
            
            fputcsv($handle, ['Total Replies', ForumReply::whereIn('forum_post_id', 
                ForumPost::where('unit_id', $unit->id)->pluck('id'))
                ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                ->count()]);
            
            fclose($handle);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}