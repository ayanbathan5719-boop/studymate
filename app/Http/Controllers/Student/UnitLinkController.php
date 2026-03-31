<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\UnitLink;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitLinkController extends Controller
{
    /**
     * Display links for a specific unit.
     */
    public function index($unitCode)
    {
        $unit = Unit::where('code', $unitCode)->firstOrFail();
        $links = UnitLink::where('student_id', Auth::id())
                        ->where('unit_id', $unit->id)
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('student.links.index', compact('unit', 'links'));
    }

    /**
     * Show form to create a new link.
     */
    public function create($unitCode)
    {
        $unit = Unit::where('code', $unitCode)->firstOrFail();
        return view('student.links.create', compact('unit'));
    }

    /**
     * Store a newly created link.
     */
    public function store(Request $request, $unitCode)
    {
        // First, find the unit by its code to get the ID
        $unit = Unit::where('code', $unitCode)->firstOrFail();
        
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'description' => 'nullable|string',
            'type' => 'nullable|string|in:link,youtube,article,document,github'
        ]);

        // Auto-detect YouTube URLs
        $type = $request->type ?? 'link';
        if (str_contains($request->url, 'youtube.com') || str_contains($request->url, 'youtu.be')) {
            $type = 'youtube';
        }

        UnitLink::create([
            'student_id' => Auth::id(),
            'user_id' => Auth::id(),
            'unit_id' => $unit->id,  // Use the ID from the found unit
            'title' => $request->title,
            'url' => $request->url,
            'description' => $request->description,
            'type' => $type,
            'clicks' => 0,
        ]);

        return redirect()->route('links.index', $unitCode)
                        ->with('success', 'Link added successfully!');
    }

    /**
     * Show form to edit a link.
     */
    public function edit($unitCode, $id)
    {
        $unit = Unit::where('code', $unitCode)->firstOrFail();
        $link = UnitLink::where('student_id', Auth::id())
                        ->where('unit_id', $unit->id)
                        ->where('id', $id)
                        ->firstOrFail();

        return view('student.links.edit', compact('unit', 'link'));
    }

    /**
     * Update the specified link.
     */
    public function update(Request $request, $unitCode, $id)
    {
        $unit = Unit::where('code', $unitCode)->firstOrFail();
        $link = UnitLink::where('student_id', Auth::id())
                        ->where('unit_id', $unit->id)
                        ->where('id', $id)
                        ->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'description' => 'nullable|string',
            'type' => 'nullable|string|in:link,youtube,article,document,github'
        ]);

        // Auto-detect YouTube URLs
        $type = $request->type ?? $link->type;
        if (str_contains($request->url, 'youtube.com') || str_contains($request->url, 'youtu.be')) {
            $type = 'youtube';
        }

        $link->update([
            'title' => $request->title,
            'url' => $request->url,
            'description' => $request->description,
            'type' => $type,
        ]);

        return redirect()->route('links.index', $unitCode)
                        ->with('success', 'Link updated successfully!');
    }

    /**
     * Delete the specified link.
     */
    public function destroy($unitCode, $id)
    {
        $unit = Unit::where('code', $unitCode)->firstOrFail();
        $link = UnitLink::where('student_id', Auth::id())
                        ->where('unit_id', $unit->id)
                        ->where('id', $id)
                        ->firstOrFail();
        
        $link->delete();

        return redirect()->route('links.index', $unitCode)
                        ->with('success', 'Link deleted successfully!');
    }

    /**
     * Increment click count for a link.
     */
    public function trackClick($unitCode, $id)
    {
        $unit = Unit::where('code', $unitCode)->firstOrFail();
        $link = UnitLink::where('student_id', Auth::id())
                        ->where('unit_id', $unit->id)
                        ->where('id', $id)
                        ->firstOrFail();
        
        $link->increment('clicks');

        return response()->json(['success' => true, 'clicks' => $link->clicks]);
    }
}