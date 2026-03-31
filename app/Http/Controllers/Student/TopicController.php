<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Unit;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopicController extends Controller
{
    /**
     * Display topics for a specific unit.
     */
    public function index($unitId)
    {
        // Find unit by ID
        $unit = Unit::findOrFail($unitId);
        
        // Check if student is enrolled in this unit
        $enrolled = Enrollment::where('student_id', Auth::id())
                              ->where('unit_id', $unit->id)
                              ->exists();

        if (!$enrolled) {
            return redirect()->route('student.units.available')
                            ->with('error', 'You must enroll in this unit first.');
        }

        $topics = Topic::where('unit_code', $unit->code)
                       ->where('status', 'published')
                       ->orderBy('order')
                       ->get();

        // Get study progress for each topic
        $progress = [];
        foreach ($topics as $topic) {
            $studyProgress = \App\Models\StudyProgress::where('student_id', Auth::id())
                                ->where('topic_id', $topic->id)
                                ->first();
            $progress[$topic->id] = $studyProgress ? 
                round(($studyProgress->duration_minutes / max($topic->estimated_minutes, 1)) * 100) : 0;
            $progress[$topic->id] = min($progress[$topic->id], 100);
        }

        return view('student.topics.index', compact('unit', 'topics', 'progress'));
    }

    /**
     * Display resources for a specific topic.
     */
    public function show($unitId, $topicId)
    {
        // Find unit by ID
        $unit = Unit::findOrFail($unitId);
        
        // Check if student is enrolled
        $enrolled = Enrollment::where('student_id', Auth::id())
                              ->where('unit_id', $unit->id)
                              ->exists();

        if (!$enrolled) {
            return redirect()->route('student.units.available')
                            ->with('error', 'You must enroll in this unit first.');
        }

        $topic = Topic::where('unit_code', $unit->code)
                      ->where('id', $topicId)
                      ->firstOrFail();

        // Get all resources for this topic
        $resources = \App\Models\Resource::where('unit_id', $unit->id)
                                         ->where('topic_id', $topicId)
                                         ->get();

        return view('student.topics.show', compact('unit', 'topic', 'resources'));
    }

    /**
     * Mark topic as completed.
     */
    public function markComplete($unitId, $topicId)
    {
        $unit = Unit::findOrFail($unitId);
        
        \App\Models\StudyProgress::updateOrCreate(
            [
                'student_id' => Auth::id(),
                'topic_id' => $topicId,
            ],
            [
                'completed' => true,
                'completed_at' => now(),
                'duration_minutes' => 100, // Mark as fully completed
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Get topic progress.
     */
    public function progress($unitId)
    {
        $unit = Unit::findOrFail($unitId);
        
        $topics = Topic::where('unit_code', $unit->code)
                       ->where('status', 'published')
                       ->orderBy('order')
                       ->get();

        $totalTopics = $topics->count();
        $completedTopics = 0;
        
        foreach ($topics as $topic) {
            $studyProgress = \App\Models\StudyProgress::where('student_id', Auth::id())
                                ->where('topic_id', $topic->id)
                                ->where('completed', true)
                                ->exists();
            if ($studyProgress) {
                $completedTopics++;
            }
        }

        $progress = $totalTopics > 0 
            ? round(($completedTopics / $totalTopics) * 100) 
            : 0;

        return response()->json([
            'success' => true,
            'progress' => $progress,
            'completed' => $completedTopics,
            'total' => $totalTopics
        ]);
    }
}