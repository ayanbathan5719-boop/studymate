<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    /**
     * Display a listing of units assigned to the lecturer.
     */
    public function index()
    {
        $lecturer = Auth::user();
        
        $units = $lecturer->units()
            ->with('course')
            ->withCount(['resources', 'forumPosts', 'students'])
            ->get();
        
        return view('lecturer.units.index', [
            'units' => $units
        ]);
    }

    /**
     * Display all resources for a specific unit.
     * This includes files and links uploaded from forum posts.
     */
    public function resources($unitId)
    {
        $lecturer = Auth::user();
        
        // Find the unit
        $unit = Unit::findOrFail($unitId);
        
        // Check if lecturer is assigned to this unit
        $isAssigned = $lecturer->units()
            ->where('unit_id', $unitId)
            ->exists();
        
        if (!$isAssigned) {
            abort(403, 'You are not assigned to this unit.');
        }
        
        // Get all resources for this unit, ordered by latest first
        $resources = Resource::where('unit_id', $unitId)
                            ->with(['user', 'topic'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(20);
        
        return view('lecturer.units.resources', compact('unit', 'resources'));
    }
}