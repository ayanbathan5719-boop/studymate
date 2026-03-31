<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Unit;
use App\Models\Topic;
use App\Models\Enrollment;
use App\Models\Resource;
use App\Models\StudyProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    public function available()
    {
        $student = Auth::user();
        
        $courses = Course::with(['units' => function($query) {
            $query->orderBy('code');
        }])->get();
        
        $approvedUnitIds = Enrollment::where('student_id', $student->id)
                                     ->where('status', 'approved')
                                     ->pluck('unit_id')
                                     ->toArray();
        
        $pendingUnitIds = Enrollment::where('student_id', $student->id)
                                    ->where('status', 'pending')
                                    ->pluck('unit_id')
                                    ->toArray();

        // DEBUG - Write to file instead of log
        $debug = "=== Available Units Debug ===\n";
        $debug .= "Timestamp: " . now() . "\n";
        $debug .= "Student ID: " . $student->id . "\n";
        $debug .= "Student Name: " . $student->name . "\n";
        $debug .= "Student Email: " . $student->email . "\n";
        $debug .= "----------------------------------------\n";
        $debug .= "Courses count: " . $courses->count() . "\n";
        $debug .= "Approved Unit IDs: " . (empty($approvedUnitIds) ? 'none' : implode(', ', $approvedUnitIds)) . "\n";
        $debug .= "Pending Unit IDs: " . (empty($pendingUnitIds) ? 'none' : implode(', ', $pendingUnitIds)) . "\n";
        $debug .= "----------------------------------------\n";
        $debug .= "COURSES AND UNITS:\n";
        
        foreach($courses as $course) {
            $debug .= "\nCourse: " . $course->name . " (ID: " . $course->id . ")\n";
            $debug .= "  Units count: " . $course->units->count() . "\n";
            foreach($course->units as $unit) {
                $isApproved = in_array($unit->id, $approvedUnitIds) ? '[APPROVED]' : '';
                $isPending = in_array($unit->id, $pendingUnitIds) ? '[PENDING]' : '';
                $debug .= "  - Unit: " . $unit->name . " | Code: " . $unit->code . " | ID: " . $unit->id . " $isApproved $isPending\n";
            }
        }
        $debug .= "\n=== End Debug ===\n";
        
        file_put_contents(storage_path('debug.txt'), $debug);
        
        return view('student.units.available', compact('courses', 'approvedUnitIds', 'pendingUnitIds'));
    }

    public function enroll(Request $request)
    {
        $request->validate([
            'unit_ids' => 'required|array',
            'unit_ids.*' => 'exists:units,id'
        ]);

        $student = Auth::user();
        $enrolled = 0;
        $pending = 0;

        foreach ($request->unit_ids as $unitId) {
            $existing = Enrollment::where('student_id', $student->id)
                                  ->where('unit_id', $unitId)
                                  ->first();

            if (!$existing) {
                Enrollment::create([
                    'student_id' => $student->id,
                    'unit_id' => $unitId,
                    'status' => 'pending',
                    'enrolled_at' => now(),
                ]);
                $pending++;
            } elseif ($existing->isRejected()) {
                $existing->update([
                    'status' => 'pending',
                    'approved_by' => null,
                    'approved_at' => null,
                    'rejected_reason' => null,
                ]);
                $pending++;
            } elseif ($existing->isApproved()) {
                $enrolled++;
            }
        }

        $message = [];
        if ($pending > 0) {
            $message[] = "$pending enrollment request" . ($pending > 1 ? 's' : '') . " submitted for approval.";
        }
        if ($enrolled > 0) {
            $message[] = "$enrolled unit" . ($enrolled > 1 ? 's' : '') . " already enrolled.";
        }

        if (empty($message)) {
            $message = ['No units selected.'];
        }

        return redirect()->route('student.units.available')
                         ->with('success', implode(' ', $message));
    }

    public function unenroll(Unit $unit)
    {
        $student = Auth::user();
        
        $enrollment = Enrollment::where('student_id', $student->id)
                                ->where('unit_id', $unit->id)
                                ->first();
        
        if (!$enrollment || !$enrollment->isApproved()) {
            return back()->with('error', 'You are not enrolled in this unit.');
        }
        
        $enrollment->delete();
        
        return back()->with('success', 'Successfully unenrolled from ' . $unit->code);
    }

    public function myRequests()
    {
        $student = Auth::user();
        
        $pendingRequests = Enrollment::where('student_id', $student->id)
                                     ->where('status', 'pending')
                                     ->with('unit')
                                     ->get();
        
        $approvedUnits = Enrollment::where('student_id', $student->id)
                                   ->where('status', 'approved')
                                   ->with('unit')
                                   ->get();
        
        $rejectedRequests = Enrollment::where('student_id', $student->id)
                                      ->where('status', 'rejected')
                                      ->with('unit')
                                      ->get();

        return view('student.units.requests', compact('pendingRequests', 'approvedUnits', 'rejectedRequests'));
    }

    public function resources($unitId)
    {
        $student = Auth::user();
        
        $unit = Unit::findOrFail($unitId);
        
        $isEnrolled = Enrollment::where('student_id', $student->id)
                                ->where('unit_id', $unitId)
                                ->where('status', 'approved')
                                ->exists();
        
        if (!$isEnrolled) {
            abort(403, 'You are not enrolled in this unit.');
        }
        
        $resources = Resource::where('unit_id', $unitId)
                            ->with(['user', 'topic'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(20);
        
        return view('student.units.resources', compact('unit', 'resources'));
    }

    public function show($id)
    {
        $student = Auth::user();
        
        $unit = Unit::with(['topics' => function($query) {
            $query->orderBy('order');
        }])->findOrFail($id);
        
        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('unit_id', $unit->id)
            ->where('status', 'approved')
            ->first();
        
        if (!$enrollment && !$student->hasRole('admin')) {
            abort(403, 'You are not enrolled in this unit.');
        }
        
        $topics = $unit->topics;
        $totalTopics = $topics->count();
        $completedTopics = 0;
        
        foreach ($topics as $topic) {
            $topic->resources_count = Resource::where('topic_id', $topic->id)->count();
            
            $topic->completed_resources = StudyProgress::where('student_id', $student->id)
                ->where('topic_id', $topic->id)
                ->where('completed', true)
                ->count();
            
            if ($topic->resources_count > 0) {
                $topic->progress_percentage = round(($topic->completed_resources / $topic->resources_count) * 100);
                if ($topic->progress_percentage >= 100) {
                    $completedTopics++;
                }
            } else {
                $topic->progress_percentage = 0;
            }
        }
        
        $resources = Resource::where('unit_id', $unit->id)
            ->with('topic')
            ->latest()
            ->get();
        
        $overallProgress = 0;
        if ($totalTopics > 0) {
            $overallProgress = round(($completedTopics / $totalTopics) * 100);
        }
        
        $lastProgress = StudyProgress::where('student_id', $student->id)
            ->where('unit_code', $unit->code)
            ->orderBy('last_studied_at', 'desc')
            ->first();
        
        $lastStudiedTopic = null;
        if ($lastProgress && $lastProgress->topic_id) {
            $lastStudiedTopic = Topic::find($lastProgress->topic_id);
        }
        
        $recentActivity = StudyProgress::where('student_id', $student->id)
            ->where('unit_code', $unit->code)
            ->with('resource')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('student.units.show', compact(
            'unit',
            'topics',
            'resources',
            'totalTopics',
            'completedTopics',
            'overallProgress',
            'lastStudiedTopic',
            'recentActivity'
        ));
    }
}