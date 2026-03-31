<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Deadline;
use App\Models\StudySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    /**
     * Display the calendar view.
     */
    public function index()
    {
        $student = Auth::user();
        
        return view('student.calendar.index');
    }

    /**
     * Get events for calendar (AJAX endpoint).
     */
    public function events(Request $request)
    {
        $student = Auth::user();
        
        $start = $request->get('start');
        $end = $request->get('end');
        
        // Get deadlines
        $deadlines = $student->acceptedDeadlines()
            ->with('unit')
            ->whereBetween('due_date', [$start, $end])
            ->get()
            ->map(function($deadline) {
                return [
                    'id' => 'deadline_' . $deadline->id,
                    'title' => $deadline->title,
                    'start' => $deadline->due_date->format('Y-m-d H:i:s'),
                    'end' => $deadline->due_date->format('Y-m-d H:i:s'),
                    'backgroundColor' => $deadline->due_date->isPast() ? '#ef4444' : '#f59e0b',
                    'borderColor' => $deadline->due_date->isPast() ? '#dc2626' : '#d97706',
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'type' => 'deadline',
                        'unit' => $deadline->unit->code,
                        'description' => $deadline->description,
                        'url' => route('student.deadlines.index')
                    ]
                ];
            });
        
        // Get study sessions (optional)
        $sessions = StudySession::where('student_id', $student->id)
            ->whereNotNull('ended_at')
            ->whereBetween('started_at', [$start, $end])
            ->get()
            ->map(function($session) {
                return [
                    'id' => 'session_' . $session->id,
                    'title' => 'Study: ' . ($session->unit->code ?? 'Unknown'),
                    'start' => $session->started_at->format('Y-m-d H:i:s'),
                    'end' => $session->ended_at->format('Y-m-d H:i:s'),
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#059669',
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'type' => 'study',
                        'duration' => $session->short_duration,
                        'url' => route('student.study.index')
                    ]
                ];
            });
        
        return response()->json(array_merge($deadlines->toArray(), $sessions->toArray()));
    }
}