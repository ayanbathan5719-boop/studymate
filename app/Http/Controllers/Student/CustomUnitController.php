<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CustomUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomUnitController extends Controller
{
    /**
     * Display a listing of custom units.
     */
    public function index()
    {
        $student = Auth::user();
        
        $customUnits = CustomUnit::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('student.custom.index', [
            'customUnits' => $customUnits
        ]);
    }

    /**
     * Show form to create a new custom unit.
     */
    public function create()
    {
        return view('student.custom.create');
    }

    /**
     * Store a newly created custom unit.
     */
    public function store(Request $request)
    {
        $student = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'goal_minutes' => 'nullable|integer|min:0'
        ]);
        
        CustomUnit::create([
            'student_id' => $student->id,
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color ?? '#f59e0b',
            'icon' => $request->icon ?? 'fa-book',
            'goal_minutes' => $request->goal_minutes
        ]);
        
        return redirect()->route('student.custom.index')
            ->with('success', 'Custom unit created successfully!');
    }

    /**
     * Show the form for editing a custom unit.
     */
    public function edit(CustomUnit $customUnit)
    {
        $student = Auth::user();
        
        if ($customUnit->student_id !== $student->id) {
            abort(403);
        }
        
        return view('student.custom.edit', [
            'customUnit' => $customUnit
        ]);
    }

    /**
     * Update the specified custom unit.
     */
    public function update(Request $request, CustomUnit $customUnit)
    {
        $student = Auth::user();
        
        if ($customUnit->student_id !== $student->id) {
            abort(403);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'goal_minutes' => 'nullable|integer|min:0'
        ]);
        
        $customUnit->update($request->all());
        
        return redirect()->route('student.custom.index')
            ->with('success', 'Custom unit updated successfully!');
    }

    /**
     * Remove the specified custom unit.
     */
    public function destroy(CustomUnit $customUnit)
    {
        $student = Auth::user();
        
        if ($customUnit->student_id !== $student->id) {
            abort(403);
        }
        
        $customUnit->delete();
        
        return redirect()->route('student.custom.index')
            ->with('success', 'Custom unit deleted successfully!');
    }

    /**
     * Update progress for a custom unit.
     */
    public function updateProgress(Request $request, CustomUnit $customUnit)
    {
        $student = Auth::user();
        
        if ($customUnit->student_id !== $student->id) {
            abort(403);
        }
        
        $request->validate([
            'progress' => 'required|integer|min:0'
        ]);
        
        $customUnit->update([
            'progress' => $request->progress
        ]);
        
        return response()->json(['success' => true]);
    }
}