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
    /**
     * Display the reports page.
     */
    public function index(Request $request)
    {
        $reportType = $request->get('type', 'courses');
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');
        
        $data = [];
        $title = '';
        
        switch ($reportType) {
            case 'courses':
                $title = 'Courses Report';
                $data = Course::withCount('units')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->latest()
                    ->get();
                break;
                
            case 'units':
                $title = 'Units Report';
                $data = Unit::with(['course', 'lecturer'])
                    ->withCount('resources', 'forumPosts')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->latest()
                    ->get();
                break;
                
            case 'lecturers':
                $title = 'Lecturers Report';
                $data = User::role('lecturer')
                    ->withCount('units')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->latest()
                    ->get();
                break;
                
            case 'students':
                $title = 'Students Report';
                $data = User::role('student')
                    ->withCount('enrolledUnits', 'forumPosts')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->latest()
                    ->get();
                break;
                
            case 'forum':
                $title = 'Forum Activity Report';
                $data = ForumPost::with(['user', 'unit'])
                    ->withCount('replies')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->latest()
                    ->get();
                break;
                
            case 'flags':
                $title = 'Flags Report';
                $data = Flag::with(['reporter', 'reportedUser', 'forumPost'])
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->latest()
                    ->get();
                break;
                
            default:
                $title = 'Courses Report';
                $data = Course::withCount('units')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->latest()
                    ->get();
                break;
        }
        
        return view('admin.reports.index', [
            'reportType' => $reportType,
            'title' => $title,
            'data' => $data,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'total' => $data->count()
        ]);
    }

    /**
     * Display Courses Report Page.
     */
    public function courses(Request $request)
    {
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');
        
        $data = Course::withCount('units')
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->get();
        
        return view('admin.reports.pages.courses', [
            'data' => $data,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'total' => $data->count()
        ]);
    }

    /**
     * Display Units Report Page.
     */
    public function units(Request $request)
    {
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');
        
        $data = Unit::with(['course', 'lecturer'])
            ->withCount('resources', 'forumPosts')
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->get();
        
        return view('admin.reports.pages.units', [
            'data' => $data,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'total' => $data->count()
        ]);
    }

    /**
     * Display Lecturers Report Page.
     */
    public function lecturers(Request $request)
    {
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');
        
        $data = User::role('lecturer')
            ->withCount('units')
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->get();
        
        return view('admin.reports.pages.lecturers', [
            'data' => $data,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'total' => $data->count()
        ]);
    }

    /**
     * Display Students Report Page.
     */
    public function students(Request $request)
    {
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');
        
        $data = User::role('student')
            ->withCount('enrolledUnits', 'forumPosts')
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->get();
        
        return view('admin.reports.pages.students', [
            'data' => $data,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'total' => $data->count()
        ]);
    }

    /**
     * Display Forum Report Page.
     */
    public function forum(Request $request)
    {
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');
        
        $data = ForumPost::with(['user', 'unit'])
            ->withCount('replies')
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->get();
        
        return view('admin.reports.pages.forum', [
            'data' => $data,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'total' => $data->count()
        ]);
    }

    /**
     * Display Flags Report Page.
     */
    public function flags(Request $request)
    {
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');
        
        $data = Flag::with(['reporter', 'reportedUser', 'forumPost'])
            ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->get();
        
        return view('admin.reports.pages.flags', [
            'data' => $data,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'total' => $data->count()
        ]);
    }

    /**
     * Export report as CSV.
     */
    public function exportCsv(Request $request)
    {
        $reportType = $request->get('type', 'courses');
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');
        
        $filename = $reportType . '-report-' . now()->format('Y-m-d') . '.csv';
        
        // Create a temporary string to store CSV content
        $csvContent = '';
        $handle = fopen('php://memory', 'w');
        
        // Add headers based on report type
        switch ($reportType) {
            case 'courses':
                fputcsv($handle, ['ID', 'Course Name', 'Code', 'Units Count', 'Created At']);
                $data = Course::withCount('units')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
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
                fputcsv($handle, ['ID', 'Name', 'Email', 'Department', 'Units Taught', 'Joined']);
                $data = User::role('lecturer')
                    ->withCount('units')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->latest()
                    ->get();
                foreach ($data as $item) {
                    fputcsv($handle, [
                        $item->id,
                        $item->name,
                        $item->email,
                        $item->department ?? 'N/A',
                        $item->units_count,
                        $item->created_at->format('Y-m-d')
                    ]);
                }
                break;
                
            case 'students':
                fputcsv($handle, ['ID', 'Name', 'Email', 'Enrolled Units', 'Forum Posts', 'Joined']);
                $data = User::role('student')
                    ->withCount('enrolledUnits', 'forumPosts')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
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
                fputcsv($handle, ['ID', 'Title', 'Author', 'Unit', 'Replies', 'Created At']);
                $data = ForumPost::with(['user', 'unit'])
                    ->withCount('replies')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->latest()
                    ->get();
                foreach ($data as $item) {
                    fputcsv($handle, [
                        $item->id,
                        $item->title,
                        $item->user->name ?? 'N/A',
                        $item->unit->name ?? 'N/A',
                        $item->replies_count,
                        $item->created_at->format('Y-m-d')
                    ]);
                }
                break;
                
            case 'flags':
                fputcsv($handle, ['ID', 'Reporter', 'Reported User', 'Reason', 'Status', 'Created At']);
                $data = Flag::with(['reporter', 'reportedUser'])
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
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
        
        // Move pointer to beginning and get contents
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);
        
        // Return as response
        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Export report as PDF.
     */
    public function exportPdf(Request $request)
    {
        $reportType = $request->get('type', 'courses');
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');
        
        $title = '';
        $data = [];
        
        switch ($reportType) {
            case 'courses':
                $title = 'Courses Report';
                $data = Course::withCount('units')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->latest()
                    ->get();
                break;
                
            case 'units':
                $title = 'Units Report';
                $data = Unit::with(['course', 'lecturer'])
                    ->withCount('resources', 'forumPosts')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->latest()
                    ->get();
                break;
                
            case 'lecturers':
                $title = 'Lecturers Report';
                $data = User::role('lecturer')
                    ->withCount('units')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->latest()
                    ->get();
                break;
                
            case 'students':
                $title = 'Students Report';
                $data = User::role('student')
                    ->withCount('enrolledUnits', 'forumPosts')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->latest()
                    ->get();
                break;
                
            case 'forum':
                $title = 'Forum Activity Report';
                $data = ForumPost::with(['user', 'unit'])
                    ->withCount('replies')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->latest()
                    ->get();
                break;
                
            case 'flags':
                $title = 'Flags Report';
                $data = Flag::with(['reporter', 'reportedUser'])
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->latest()
                    ->get();
                break;
                
            default:
                $title = 'Courses Report';
                $data = Course::withCount('units')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->latest()
                    ->get();
                break;
        }
        
        $pdf = Pdf::loadView('admin.reports.pdf', [
            'title' => $title,
            'reportType' => $reportType,
            'data' => $data,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'generatedAt' => now()->format('d M Y, H:i:s')
        ]);
        
        return $pdf->download($reportType . '-report-' . now()->format('Y-m-d') . '.pdf');
    }
}