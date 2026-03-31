<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of units.
     */
    public function index()
    {
        $units = Unit::with(['course', 'lecturer'])
            ->latest()
            ->paginate(10);
        
        return view('admin.units.index', [
            'units' => $units
        ]);
    }

    /**
     * Show the form for creating a new unit.
     */
    public function create()
    {
        $courses = Course::all();
        $lecturers = User::role('lecturer')->get();
        
        return view('admin.units.create', [
            'courses' => $courses,
            'lecturers' => $lecturers
        ]);
    }

    /**
     * Store a newly created unit in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:10',
                'unique:units',
                'regex:/^[A-Z]{2,4}\d{4}$/',
            ],
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'lecturer_id' => 'nullable|exists:users,id',
        ], [
            'code.regex' => 'The unit code must be in the format: 2-4 letters followed by 4 digits (e.g., BIT2204, ICT2101, CSIT2201)',
            'code.unique' => 'This unit code is already in use.',
        ]);

        Unit::create([
            'name' => $request->name,
            'code' => strtoupper($request->code), // Convert to uppercase
            'description' => $request->description,
            'course_id' => $request->course_id,
            'lecturer_id' => $request->lecturer_id,
        ]);

        return redirect('/admin/units')->with('success', 'Unit created successfully!');
    }

    /**
     * Show the form for editing a unit.
     */
    public function edit(Unit $unit)
    {
        $courses = Course::all();
        $lecturers = User::role('lecturer')->get();
        
        return view('admin.units.edit', [
            'unit' => $unit,
            'courses' => $courses,
            'lecturers' => $lecturers
        ]);
    }

    /**
     * Update the specified unit.
     */
    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:10',
                'unique:units,code,' . $unit->id,
                'regex:/^[A-Z]{2,4}\d{4}$/',
            ],
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'lecturer_id' => 'nullable|exists:users,id',
        ], [
            'code.regex' => 'The unit code must be in the format: 2-4 letters followed by 4 digits (e.g., BIT2204, ICT2101, CSIT2201)',
            'code.unique' => 'This unit code is already in use.',
        ]);

        $unit->update([
            'name' => $request->name,
            'code' => strtoupper($request->code), // Convert to uppercase
            'description' => $request->description,
            'course_id' => $request->course_id,
            'lecturer_id' => $request->lecturer_id,
        ]);

        return redirect('/admin/units')->with('success', 'Unit updated successfully!');
    }

    /**
     * Remove the specified unit.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();
        
        return redirect('/admin/units')->with('success', 'Unit deleted successfully!');
    }

    /**
     * Show unit assignments page.
     */
    public function assignments(Request $request)
    {
        $courseId = $request->get('course_id');
        $status = $request->get('status', 'all');
        
        $query = Unit::with(['course', 'lecturer']);
        
        if ($courseId) {
            $query->where('course_id', $courseId);
        }
        
        if ($status == 'assigned') {
            $query->whereNotNull('lecturer_id');
        } elseif ($status == 'unassigned') {
            $query->whereNull('lecturer_id');
        }
        
        $units = $query->orderBy('code')->get();
        
        $courses = Course::orderBy('name')->get();
        
        // Updated to join with lecturers table to get department information
        $lecturers = User::role('lecturer')
            ->leftJoin('lecturers', 'users.email', '=', 'lecturers.email')
            ->select('users.*', 'lecturers.department')
            ->orderBy('users.name')
            ->get();
        
        $totalUnits = Unit::count();
        $assignedUnits = Unit::whereNotNull('lecturer_id')->count();
        $unassignedUnits = Unit::whereNull('lecturer_id')->count();
        $totalLecturers = $lecturers->count();
        
        return view('admin.units.assignments', compact(
            'units', 'courses', 'lecturers',
            'totalUnits', 'assignedUnits', 'unassignedUnits', 'totalLecturers'
        ));
    }

    /**
     * Assign lecturer to unit.
     */
    public function assign(Request $request, Unit $unit)
    {
        $request->validate([
            'lecturer_id' => 'required|exists:users,id'
        ]);
        
        $lecturer = User::findOrFail($request->lecturer_id);
        
        // Verify user is a lecturer
        if (!$lecturer->hasRole('lecturer')) {
            return back()->with('error', 'Selected user is not a lecturer.');
        }
        
        // Update the unit's lecturer_id (for direct relationship)
        $unit->update(['lecturer_id' => $lecturer->id]);
        
        // Save to pivot table (for many-to-many relationship)
        $lecturer->units()->syncWithoutDetaching([$unit->id]);
        
        return back()->with('success', "{$lecturer->name} assigned to {$unit->code} successfully!");
    }

    /**
     * Unassign lecturer from unit.
     */
    public function unassign(Unit $unit)
    {
        $lecturerName = $unit->lecturer->name ?? 'Lecturer';
        
        // Remove from pivot table first
        if ($unit->lecturer) {
            $unit->lecturer->units()->detach($unit->id);
        }
        
        // Then remove from unit
        $unit->update(['lecturer_id' => null]);
        
        return back()->with('success', "{$lecturerName} unassigned from {$unit->code} successfully!");
    }
}