<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopicController extends Controller
{
    /**
     * Display topics for a specific unit.
     */
    public function index($unitCode)
    {
        $unit = Unit::where('code', $unitCode)
                    ->whereHas('lecturers', function($query) {
                        $query->where('lecturer_id', Auth::id());
                    })
                    ->firstOrFail();

        $topics = Topic::where('unit_code', $unitCode)
                       ->orderBy('order')
                       ->get();

        return view('lecturer.topics.index', compact('unit', 'topics'));
    }

    /**
     * Show form to create a new topic.
     */
    public function create($unitCode)
    {
        $unit = Unit::where('code', $unitCode)
                    ->whereHas('lecturers', function($query) {
                        $query->where('lecturer_id', Auth::id());
                    })
                    ->firstOrFail();

        return view('lecturer.topics.create', compact('unit'));
    }

    /**
     * Store a newly created topic.
     */
    public function store(Request $request, $unitCode)
    {
        $unit = Unit::where('code', $unitCode)
                    ->whereHas('lecturers', function($query) {
                        $query->where('lecturer_id', Auth::id());
                    })
                    ->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url|max:500',
            'content' => 'nullable|string',
            'estimated_minutes' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,published'
        ]);

        // Get the highest order number
        $maxOrder = Topic::where('unit_code', $unitCode)->max('order') ?? 0;

        Topic::create([
            'unit_code' => $unitCode,
            'title' => $request->title,
            'description' => $request->description,
            'video_url' => $request->video_url,
            'content' => $request->content,
            'estimated_minutes' => $request->estimated_minutes,
            'status' => $request->status,
            'order' => $maxOrder + 1
        ]);

        return redirect()->route('lecturer.topics.index', $unitCode)
                        ->with('success', 'Topic created successfully!');
    }

    /**
     * Show form to edit a topic.
     */
    public function edit($unitCode, $id)
    {
        $unit = Unit::where('code', $unitCode)
                    ->whereHas('lecturers', function($query) {
                        $query->where('lecturer_id', Auth::id());
                    })
                    ->firstOrFail();

        $topic = Topic::where('unit_code', $unitCode)
                      ->findOrFail($id);

        return view('lecturer.topics.edit', compact('unit', 'topic'));
    }

    /**
     * Update the specified topic.
     */
    public function update(Request $request, $unitCode, $id)
    {
        $unit = Unit::where('code', $unitCode)
                    ->whereHas('lecturers', function($query) {
                        $query->where('lecturer_id', Auth::id());
                    })
                    ->firstOrFail();

        $topic = Topic::where('unit_code', $unitCode)
                      ->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url|max:500',
            'content' => 'nullable|string',
            'estimated_minutes' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,published'
        ]);

        $topic->update($request->all());

        return redirect()->route('lecturer.topics.index', $unitCode)
                        ->with('success', 'Topic updated successfully!');
    }

    /**
     * Delete the specified topic.
     */
    public function destroy($unitCode, $id)
    {
        $unit = Unit::where('code', $unitCode)
                    ->whereHas('lecturers', function($query) {
                        $query->where('lecturer_id', Auth::id());
                    })
                    ->firstOrFail();

        $topic = Topic::where('unit_code', $unitCode)
                      ->findOrFail($id);
        
        // Reorder remaining topics
        Topic::where('unit_code', $unitCode)
             ->where('order', '>', $topic->order)
             ->decrement('order');

        $topic->delete();

        return redirect()->route('lecturer.topics.index', $unitCode)
                        ->with('success', 'Topic deleted successfully!');
    }

    /**
     * Reorder topics via drag and drop.
     */
    public function reorder(Request $request, $unitCode)
    {
        $request->validate([
            'topics' => 'required|array',
            'topics.*.id' => 'required|exists:topics,id',
            'topics.*.order' => 'required|integer'
        ]);

        foreach ($request->topics as $topicData) {
            Topic::where('unit_code', $unitCode)
                 ->where('id', $topicData['id'])
                 ->update(['order' => $topicData['order']]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Toggle topic status (draft/published).
     */
    public function toggleStatus($unitCode, $id)
    {
        $topic = Topic::where('unit_code', $unitCode)
                      ->findOrFail($id);

        $topic->update([
            'status' => $topic->status === 'published' ? 'draft' : 'published'
        ]);

        return response()->json([
            'success' => true,
            'status' => $topic->status
        ]);
    }
}