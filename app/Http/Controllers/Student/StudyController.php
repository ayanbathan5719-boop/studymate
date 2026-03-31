<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudySession;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudyController extends Controller
{
    /**
     * Display study statistics.
     */
    public function index()
    {
        $student = Auth::user();
        
        // Get today's study time
        $todaySeconds = StudySession::where('student_id', $student->id)
            ->whereDate('started_at', now()->toDateString())
            ->sum('duration_seconds');
        
        // Get this week's study time
        $weekSeconds = StudySession::where('student_id', $student->id)
            ->whereBetween('started_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('duration_seconds');
        
        // Get this month's study time
        $monthSeconds = StudySession::where('student_id', $student->id)
            ->whereMonth('started_at', now()->month)
            ->sum('duration_seconds');
        
        // Get study time per unit
        $unitStats = Unit::whereIn('id', $student->enrolledUnits()->pluck('unit_id'))
            ->withCount(['studySessions' => function($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            ->get()
            ->map(function($unit) use ($student) {
                $seconds = StudySession::where('student_id', $student->id)
                    ->where('unit_id', $unit->id)
                    ->sum('duration_seconds');
                
                $unit->total_seconds = $seconds;
                $unit->formatted_time = $this->formatTime($seconds);
                return $unit;
            });
        
        // Get daily activity for the last 7 days
        $dailyActivity = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $seconds = StudySession::where('student_id', $student->id)
                ->whereDate('started_at', $date->toDateString())
                ->sum('duration_seconds');
            
            $dailyActivity[] = [
                'date' => $date->format('D'),
                'minutes' => round($seconds / 60, 1),
                'full_date' => $date->format('M d')
            ];
        }
        
        return view('student.study.index', [
            'today' => $this->formatTime($todaySeconds),
            'week' => $this->formatTime($weekSeconds),
            'month' => $this->formatTime($monthSeconds),
            'todayMinutes' => round($todaySeconds / 60, 1),
            'unitStats' => $unitStats,
            'dailyActivity' => $dailyActivity
        ]);
    }

    /**
     * Start a study session.
     */
    public function start(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id'
        ]);
        
        $student = Auth::user();
        
        // Check if already have an active session
        $activeSession = StudySession::where('student_id', $student->id)
            ->whereNull('ended_at')
            ->first();
        
        if ($activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active study session'
            ]);
        }
        
        $session = StudySession::create([
            'student_id' => $student->id,
            'unit_id' => $request->unit_id,
            'started_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'session_id' => $session->id
        ]);
    }

    /**
     * End a study session.
     */
    public function end(Request $request)
    {
        $student = Auth::user();
        
        $session = StudySession::where('student_id', $student->id)
            ->whereNull('ended_at')
            ->first();
        
        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'No active study session found'
            ]);
        }
        
        $session->ended_at = now();
        $session->duration_seconds = $session->started_at->diffInSeconds($session->ended_at);
        $session->save();
        
        return response()->json([
            'success' => true,
            'duration' => $this->formatTime($session->duration_seconds)
        ]);
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