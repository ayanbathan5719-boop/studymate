<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Deadline;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeadlineController extends Controller
{
    /**
     * Display deadlines for the student.
     */
    public function index(Request $request)
    {
        $student = Auth::user();
        
        // Get enrolled unit IDs
        $enrolledUnitIds = $student->enrolledUnits()->pluck('unit_id')->toArray();
        
        $query = Deadline::whereIn('unit_id', $enrolledUnitIds)
            ->with('unit');
        
        // Filter by status
        if ($request->status == 'upcoming') {
            $query->where('due_date', '>', now());
        } elseif ($request->status == 'urgent') {
            $query->where('due_date', '>', now())
                  ->where('due_date', '<', now()->addDays(3));
        } elseif ($request->status == 'overdue') {
            $query->where('due_date', '<', now());
        } elseif ($request->status == 'accepted') {
            $query->whereHas('students', function($q) use ($student) {
                $q->where('student_id', $student->id);
            });
        }
        
        // Filter by unit
        if ($request->has('unit')) {
            $query->where('unit_id', $request->unit);
        }
        
        $deadlines = $query->orderBy('due_date')->paginate(15);
        
        // Get enrolled units for filter
        $units = $student->enrolledUnits()->orderBy('code')->get();
        
        // Get accepted deadline IDs
        $acceptedDeadlineIds = $student->acceptedDeadlines()->pluck('deadline_id')->toArray();
        
        return view('student.deadlines.index', [
            'deadlines' => $deadlines,
            'units' => $units,
            'acceptedDeadlineIds' => $acceptedDeadlineIds,
            'filters' => $request->only(['status', 'unit'])
        ]);
    }

    /**
     * Accept a deadline.
     */
    public function accept(Deadline $deadline)
    {
        $student = Auth::user();
        
        // Check if student is enrolled in the unit
        if (!$student->enrolledUnits()->where('unit_id', $deadline->unit_id)->exists()) {
            return back()->with('error', 'You are not enrolled in this unit.');
        }
        
        // Check if already accepted
        if ($student->acceptedDeadlines()->where('deadline_id', $deadline->id)->exists()) {
            return back()->with('error', 'You have already accepted this deadline.');
        }
        
        $student->acceptedDeadlines()->attach($deadline->id, [
            'accepted_at' => now(),
            'completed_at' => null
        ]);
        
        return back()->with('success', 'Deadline accepted and added to your calendar!');
    }

    /**
     * Decline a deadline.
     */
    public function decline(Deadline $deadline)
    {
        $student = Auth::user();
        
        $student->acceptedDeadlines()->detach($deadline->id);
        
        return back()->with('success', 'Deadline removed from your calendar.');
    }

    /**
     * Mark deadline as completed.
     */
    public function complete(Deadline $deadline)
    {
        $student = Auth::user();
        
        $student->acceptedDeadlines()->updateExistingPivot($deadline->id, [
            'completed_at' => now()
        ]);
        
        return back()->with('success', 'Deadline marked as completed! Great job!');
    }

    /**
     * Create personal deadline.
     */
    public function createPersonal()
    {
        $student = Auth::user();
        $units = $student->enrolledUnits()->orderBy('code')->get();
        
        return view('student.deadlines.personal', [
            'units' => $units
        ]);
    }

    /**
     * Store personal deadline.
     */
    public function storePersonal(Request $request)
    {
        $student = Auth::user();
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_id' => 'required|exists:units,id',
            'due_date' => 'required|date|after:now'
        ]);
        
        // Check if student is enrolled in the unit
        if (!$student->enrolledUnits()->where('unit_id', $request->unit_id)->exists()) {
            return back()->with('error', 'You are not enrolled in this unit.');
        }
        
        // Create personal deadline (not linked to any lecturer)
        $deadline = Deadline::create([
            'title' => $request->title . ' (Personal)',
            'description' => $request->description,
            'unit_id' => $request->unit_id,
            'due_date' => $request->due_date,
            'created_by' => $student->id,
            'is_personal' => true
        ]);
        
        // Auto-accept personal deadline
        $student->acceptedDeadlines()->attach($deadline->id, [
            'accepted_at' => now()
        ]);
        
        return redirect()->route('student.deadlines.index')
            ->with('success', 'Personal deadline created successfully!');
    }
}