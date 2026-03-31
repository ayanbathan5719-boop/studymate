<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Http\Request;

class LecturerUnitController extends Controller
{
    /**
     * Show form to assign units to a lecturer.
     */
    public function edit(User $lecturer)
    {
        // Ensure the user is a lecturer
        if (!$lecturer->hasRole('lecturer')) {
            abort(404, 'Lecturer not found.');
        }

        // Get all units
        $allUnits = Unit::with('course')->orderBy('code')->get();
        
        // Get units already assigned to this lecturer
        $assignedUnits = $lecturer->units()->get();
        $assignedUnitIds = $assignedUnits->pluck('id')->toArray();

        return view('admin.lecturers.units', [
            'lecturer' => $lecturer,
            'allUnits' => $allUnits,
            'assignedUnits' => $assignedUnits,
            'assignedUnitIds' => $assignedUnitIds,
        ]);
    }

    /**
     * Update unit assignments for a lecturer.
     */
    public function update(Request $request, User $lecturer)
    {
        // Ensure the user is a lecturer
        if (!$lecturer->hasRole('lecturer')) {
            abort(404, 'Lecturer not found.');
        }

        $unitIds = $request->input('units', []);
        
        // Sync the selected units
        $lecturer->units()->sync($unitIds);

        return redirect()->route('admin.lecturers.index')
            ->with('success', 'Units assigned successfully!');
    }
}