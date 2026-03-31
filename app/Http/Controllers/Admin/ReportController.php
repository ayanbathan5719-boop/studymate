<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Unit;
use App\Models\User;
use App\Models\ForumPost;
use App\Models\Flag;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('admin.reports.courses');
    }

    public function courses(Request $request)
    {
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');
        $search = $request->get('search');
        
        $data = Course::withCount('units')
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%")
                   ->orWhere('code', 'like', "%{$search}%");
            }))
            ->latest()
            ->get();
        
        return view('admin.reports.pages.courses', [
            'data' => $data,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'total' => $data->count()
        ]);
    }

    public function units(Request $request)
    {
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');
        $search = $request->get('search');
        $courseId = $request->get('course');
        
        $data = Unit::with(['course', 'lecturer'])
            ->withCount('resources', 'forumPosts')
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%")
                   ->orWhere('code', 'like', "%{$search}%");
            }))
            ->when($courseId, fn($q) => $q->where('course_id', $courseId))
            ->latest()
            ->get();
        
        return view('admin.reports.pages.units', [
            'data' => $data,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'total' => $data->count()
        ]);
    }

    public function lecturers(Request $request)
    {
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');
        $search = $request->get('search');
        
        $data = User::role('lecturer')
            ->withCount('units')
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%")
                   ->orWhere('email', 'like', "%{$search}%");
            }))
            ->latest()
            ->get();
        
        return view('admin.reports.pages.lecturers', [
            'data' => $data,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'total' => $data->count()
        ]);
    }

    public function students(Request $request)
    {
        $search = $request->get('search');
        $unitCode = $request->get('unit');
        $minPosts = $request->get('min_posts');
        
        $data = User::role('student')
            ->withCount('enrolledUnits', 'forumPosts')
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%")
                   ->orWhere('email', 'like', "%{$search}%");
            }))
            ->when($unitCode, fn($q) => $q->whereHas('enrolledUnits', function($q2) use ($unitCode) {
                $q2->where('unit_code', $unitCode);
            }))
            ->when($minPosts, fn($q) => $q->having('forum_posts_count', '>=', $minPosts))
            ->latest()
            ->get();
        
        return view('admin.reports.pages.students', [
            'data' => $data,
            'dateFrom' => null,
            'dateTo' => null,
            'total' => $data->count()
        ]);
    }

    public function forum(Request $request)
    {
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');
        $search = $request->get('search');
        $unitCode = $request->get('unit');
        $authorId = $request->get('author');
        $minReplies = $request->get('min_replies');
        
        $data = ForumPost::with(['user', 'unit'])
            ->withCount('replies')
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('title', 'like', "%{$search}%")
                   ->orWhere('content', 'like', "%{$search}%");
            }))
            ->when($unitCode, fn($q) => $q->where('unit_code', $unitCode))
            ->when($authorId, fn($q) => $q->where('user_id', $authorId))
            ->when($minReplies, fn($q) => $q->having('replies_count', '>=', $minReplies))
            ->latest()
            ->get();
        
        return view('admin.reports.pages.forum', [
            'data' => $data,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'total' => $data->count()
        ]);
    }

    public function flags(Request $request)
    {
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');
        $status = $request->get('status');
        $reason = $request->get('reason');
        $reportedUserId = $request->get('reported_user');
        
        $data = Flag::with(['reporter', 'reportedUser'])
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($reason, fn($q) => $q->where('reason', $reason))
            ->when($reportedUserId, fn($q) => $q->where('reported_user_id', $reportedUserId))
            ->latest()
            ->get();
        
        return view('admin.reports.pages.flags', [
            'data' => $data,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'total' => $data->count()
        ]);
    }

    public function exportCsv(Request $request)
    {
        $reportType = $request->get('type', 'courses');
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');
        
        $filename = $reportType . '-report-' . now()->format('Y-m-d') . '.csv';
        
        $handle = fopen('php://memory', 'w');
        
        switch ($reportType) {
            case 'courses':
                fputcsv($handle, ['ID', 'Course Name', 'Code', 'Units Count', 'Created At']);
                $data = Course::withCount('units')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->when($request->get('search'), fn($q, $search) => $q->where(function($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%");
                    }))
                    ->latest()
                    ->get();
                foreach ($data as $item) {
                    fputcsv($handle, [
                        $item->id,
                        $item->name,
                        $item->code,
                        $item->units_count,
                        $item->created_at->format('Y-m-d')
                    ]);
                }
                break;
                
            case 'units':
                fputcsv($handle, ['ID', 'Unit Name', 'Code', 'Course', 'Lecturer', 'Resources', 'Forum Posts', 'Created At']);
                $data = Unit::with(['course', 'lecturer'])
                    ->withCount('resources', 'forumPosts')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->when($request->get('search'), fn($q, $search) => $q->where(function($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%");
                    }))
                    ->when($request->get('course'), fn($q, $courseId) => $q->where('course_id', $courseId))
                    ->latest()
                    ->get();
                foreach ($data as $item) {
                    fputcsv($handle, [
                        $item->id,
                        $item->name,
                        $item->code,
                        $item->course->name ?? 'N/A',
                        $item->lecturer->name ?? 'N/A',
                        $item->resources_count,
                        $item->forum_posts_count,
                        $item->created_at->format('Y-m-d')
                    ]);
                }
                break;
                
            case 'lecturers':
                fputcsv($handle, ['ID', 'Name', 'Email', 'Units Taught', 'Joined']);
                $data = User::role('lecturer')
                    ->withCount('units')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->when($request->get('search'), fn($q, $search) => $q->where(function($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                    }))
                    ->latest()
                    ->get();
                foreach ($data as $item) {
                    fputcsv($handle, [
                        $item->id,
                        $item->name,
                        $item->email,
                        $item->units_count,
                        $item->created_at->format('Y-m-d')
                    ]);
                }
                break;
                
            case 'students':
                fputcsv($handle, ['ID', 'Name', 'Email', 'Enrolled Units', 'Forum Posts', 'Joined']);
                $data = User::role('student')
                    ->withCount('enrolledUnits', 'forumPosts')
                    ->when($request->get('search'), fn($q, $search) => $q->where(function($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                    }))
                    ->when($request->get('unit'), fn($q, $unitCode) => $q->whereHas('enrolledUnits', function($q2) use ($unitCode) {
                        $q2->where('unit_code', $unitCode);
                    }))
                    ->when($request->get('min_posts'), fn($q, $min) => $q->having('forum_posts_count', '>=', $min))
                    ->latest()
                    ->get();
                foreach ($data as $item) {
                    fputcsv($handle, [
                        $item->id,
                        $item->name,
                        $item->email,
                        $item->enrolled_units_count,
                        $item->forum_posts_count,
                        $item->created_at->format('Y-m-d')
                    ]);
                }
                break;
                
            case 'forum':
                fputcsv($handle, ['ID', 'Title', 'Author', 'Unit', 'Replies', 'Views', 'Created At']);
                $data = ForumPost::with(['user', 'unit'])
                    ->withCount('replies')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->when($request->get('search'), fn($q, $search) => $q->where(function($q2) use ($search) {
                        $q2->where('title', 'like', "%{$search}%")->orWhere('content', 'like', "%{$search}%");
                    }))
                    ->when($request->get('unit'), fn($q, $unitCode) => $q->where('unit_code', $unitCode))
                    ->when($request->get('author'), fn($q, $authorId) => $q->where('user_id', $authorId))
                    ->when($request->get('min_replies'), fn($q, $min) => $q->having('replies_count', '>=', $min))
                    ->latest()
                    ->get();
                foreach ($data as $item) {
                    fputcsv($handle, [
                        $item->id,
                        $item->title,
                        $item->user->name ?? 'N/A',
                        $item->unit->name ?? 'N/A',
                        $item->replies_count,
                        $item->views,
                        $item->created_at->format('Y-m-d')
                    ]);
                }
                break;
                
            case 'flags':
                fputcsv($handle, ['ID', 'Reporter', 'Reported User', 'Reason', 'Status', 'Created At']);
                $data = Flag::with(['reporter', 'reportedUser'])
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->when($request->get('status'), fn($q, $status) => $q->where('status', $status))
                    ->when($request->get('reason'), fn($q, $reason) => $q->where('reason', $reason))
                    ->when($request->get('reported_user'), fn($q, $userId) => $q->where('reported_user_id', $userId))
                    ->latest()
                    ->get();
                foreach ($data as $item) {
                    fputcsv($handle, [
                        $item->id,
                        $item->reporter->name ?? 'N/A',
                        $item->reportedUser->name ?? 'N/A',
                        $item->reason,
                        $item->status,
                        $item->created_at->format('Y-m-d')
                    ]);
                }
                break;
        }
        
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);
        
        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function exportPdf(Request $request)
    {
        $reportType = $request->get('type', 'courses');
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');
        
        $data = Course::withCount('units')
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->get();
        
        $pdf = Pdf::loadView('admin.reports.pdf', [
            'title' => 'Courses Report',
            'reportType' => $reportType,
            'data' => $data,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'generatedAt' => now()->format('d M Y, H:i:s')
        ]);
        
        return $pdf->download($reportType . '-report-' . now()->format('Y-m-d') . '.pdf');
    }
}