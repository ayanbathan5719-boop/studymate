<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Deadline;
use App\Models\Unit;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeadlineController extends Controller
{
    /**
     * Display a listing of deadlines.
     */
    public function index(Request $request)
    {
        // FIX 1: Changed created_by to lecturer_id
        $query = Deadline::where('lecturer_id', Auth::id())
                        ->with(['unit', 'topic']);

        // Filter by unit
        if ($request->has('unit')) {
            $query->where('unit_id', $request->unit);
        }

        // Filter by type
        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status == 'upcoming') {
                $query->where('due_date', '>', now());
            } elseif ($request->status == 'overdue') {
                $query->where('due_date', '<', now());
            }
        }

        $deadlines = $query->orderBy('due_date')
                          ->paginate(15);

        // FIX 2: Changed user_id to lecturer_id in the relationship query
        $units = Unit::whereHas('lecturers', function($query) {
                    $query->where('lecturer_id', Auth::id());
                })->get();

        return view('lecturer.deadlines.index', compact('deadlines', 'units'));
    }

    /**
     * Show form to create a new deadline.
     */
    public function create(Request $request)
    {
        // FIX 2: Changed user_id to lecturer_id in the relationship query
        $units = Unit::whereHas('lecturers', function($query) {
                    $query->where('lecturer_id', Auth::id());
                })->with('topics')->get();

        $selectedUnit = null;
        $topics = collect();

        if ($request->has('unit')) {
            $selectedUnit = Unit::find($request->unit);
            if ($selectedUnit) {
                $topics = Topic::where('unit_code', $selectedUnit->code)
                               ->where('status', 'published')
                               ->orderBy('order')
                               ->get();
            }
        }

        return view('lecturer.deadlines.create', compact('units', 'selectedUnit', 'topics'));
    }

    /**
     * Store a newly created deadline.
     */
    public function store(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'topic_id' => 'nullable|exists:topics,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date|after:now',
            'type' => 'required|in:general,topic,assignment'
        ]);

        // If topic is selected, ensure it belongs to the unit
        if ($request->topic_id) {
            $topic = Topic::find($request->topic_id);
            $unit = Unit::find($request->unit_id);
            
            if ($topic->unit_code !== $unit->code) {
                return back()->withErrors(['topic_id' => 'Selected topic does not belong to this unit.']);
            }
        }

        // Changed created_by to lecturer_id
        Deadline::create([
            'unit_id' => $request->unit_id,
            'topic_id' => $request->topic_id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'type' => $request->type,
            'lecturer_id' => Auth::id()
        ]);

        return redirect()->route('lecturer.deadlines.index')
                        ->with('success', 'Deadline created successfully!');
    }

    /**
     * Show form to edit a deadline.
     */
    public function edit($id)
    {
        // Changed created_by to lecturer_id
        $deadline = Deadline::where('lecturer_id', Auth::id())
                            ->with(['unit', 'topic'])
                            ->findOrFail($id);

        // FIX 2: Changed user_id to lecturer_id in the relationship query
        $units = Unit::whereHas('lecturers', function($query) {
                    $query->where('lecturer_id', Auth::id());
                })->with('topics')->get();

        $topics = Topic::where('unit_code', $deadline->unit->code)
                       ->where('status', 'published')
                       ->orderBy('order')
                       ->get();

        return view('lecturer.deadlines.edit', compact('deadline', 'units', 'topics'));
    }

    /**
     * Update the specified deadline.
     */
    public function update(Request $request, $id)
    {
        // Changed created_by to lecturer_id
        $deadline = Deadline::where('lecturer_id', Auth::id())
                            ->findOrFail($id);

        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'topic_id' => 'nullable|exists:topics,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'type' => 'required|in:general,topic,assignment'
        ]);

        // If topic is selected, ensure it belongs to the unit
        if ($request->topic_id) {
            $topic = Topic::find($request->topic_id);
            $unit = Unit::find($request->unit_id);
            
            if ($topic->unit_code !== $unit->code) {
                return back()->withErrors(['topic_id' => 'Selected topic does not belong to this unit.']);
            }
        }

        $deadline->update($request->all());

        return redirect()->route('lecturer.deadlines.index')
                        ->with('success', 'Deadline updated successfully!');
    }

    /**
     * Delete the specified deadline.
     */
    public function destroy($id)
    {
        // Changed created_by to lecturer_id
        $deadline = Deadline::where('lecturer_id', Auth::id())
                            ->findOrFail($id);
        
        $deadline->delete();

        return redirect()->route('lecturer.deadlines.index')
                        ->with('success', 'Deadline deleted successfully!');
    }

    /**
     * Get topics for a specific unit (AJAX).
     */
    public function getTopics($unitId)
    {
        $unit = Unit::findOrFail($unitId);
        
        $topics = Topic::where('unit_code', $unit->code)
                       ->where('status', 'published')
                       ->orderBy('order')
                       ->get(['id', 'title', 'order']);

        return response()->json($topics);
    }

    /**
     * Show calendar view.
     */
    public function calendar()
    {
        // Changed created_by to lecturer_id
        $deadlines = Deadline::where('lecturer_id', Auth::id())
                            ->with(['unit', 'topic'])
                            ->get();

        return view('lecturer.deadlines.calendar', compact('deadlines'));
    }

    /**
     * Export deadlines.
     */
    public function export(Request $request)
    {
        // Changed created_by to lecturer_id
        $query = Deadline::where('lecturer_id', Auth::id())
                        ->with(['unit', 'topic']);

        if ($request->has('unit')) {
            $query->where('unit_id', $request->unit);
        }

        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }

        $deadlines = $query->orderBy('due_date')->get();

        // Generate CSV
        $filename = 'deadlines_' . now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'w+');
        
        // Add headers
        fputcsv($handle, ['Unit', 'Topic', 'Title', 'Type', 'Due Date', 'Status']);
        
        // Add data
        foreach ($deadlines as $deadline) {
            fputcsv($handle, [
                $deadline->unit->code,
                $deadline->topic ? $deadline->topic->title : 'N/A',
                $deadline->title,
                ucfirst($deadline->type),
                $deadline->due_date->format('Y-m-d H:i'),
                $deadline->due_date->isPast() ? 'Overdue' : 'Upcoming'
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}