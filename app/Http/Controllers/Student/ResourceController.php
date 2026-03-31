<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\ResourceDownload;
use App\Models\Unit;
use App\Models\Topic;
use App\Models\StudyProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResourceController extends Controller
{
    /**
     * Display a listing of resources for the student.
     */
    public function index(Request $request)
    {
        $student = Auth::user();
        
        // Get units the student is enrolled in
        $enrolledUnits = $student->enrolledUnits()->get();
        $enrolledUnitIds = $enrolledUnits->pluck('id');
        
        // Base query - only show resources from enrolled units
        $query = Resource::whereIn('unit_id', $enrolledUnitIds)
            ->with(['user', 'unit', 'topic']);
        
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('unit', function($unitQuery) use ($search) {
                      $unitQuery->where('code', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Apply unit filter
        if ($request->filled('unit')) {
            $unit = Unit::where('code', $request->unit)->first();
            if ($unit) {
                $query->where('unit_id', $unit->id);
            }
        }
        
        // Apply type filter
        if ($request->filled('type')) {
            $query->where('file_type', $request->type);
        }
        
        // Get resources with pagination
        $resources = $query->latest()->paginate(12)->withQueryString();
        
        // Get counts for stats
        $totalResources = Resource::whereIn('unit_id', $enrolledUnitIds)->count();
        $recentCount = Resource::whereIn('unit_id', $enrolledUnitIds)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        
        // Get counts by type for categories
        $typeCounts = [
            'pdf' => Resource::whereIn('unit_id', $enrolledUnitIds)->where('file_type', 'pdf')->count(),
            'video' => Resource::whereIn('unit_id', $enrolledUnitIds)->where('file_type', 'video')->count(),
            'link' => Resource::whereIn('unit_id', $enrolledUnitIds)->where('file_type', 'link')->count(),
            'document' => Resource::whereIn('unit_id', $enrolledUnitIds)->where('file_type', 'document')->count(),
        ];
        
        return view('student.resources.index', compact(
            'resources',
            'enrolledUnits',
            'totalResources',
            'recentCount',
            'typeCounts'
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $student = Auth::user();
        
        $resource = Resource::with(['user', 'unit', 'unit.course', 'topic'])
            ->findOrFail($id);
        
        $isEnrolled = $student->enrolledUnits()
            ->where('unit_id', $resource->unit_id)
            ->exists();
        
        if (!$isEnrolled) {
            abort(403, 'You are not enrolled in the unit for this resource.');
        }
        
        $userDownloadCount = ResourceDownload::where('resource_id', $resource->id)
            ->where('user_id', $student->id)
            ->count();
        
        $lastDownloaded = ResourceDownload::where('resource_id', $resource->id)
            ->where('user_id', $student->id)
            ->latest()
            ->first();
        
        $markedStudied = StudyProgress::where('student_id', $student->id)
            ->where('resource_id', $resource->id)
            ->where('completed', true)
            ->exists();
        
        $userNotes = StudyProgress::where('student_id', $student->id)
            ->where('resource_id', $resource->id)
            ->value('notes');
        
        $topicProgress = 0;
        if ($resource->topic_id) {
            $totalResourcesInTopic = Resource::where('topic_id', $resource->topic_id)->count();
            $studiedResourcesInTopic = StudyProgress::where('student_id', $student->id)
                ->whereIn('resource_id', Resource::where('topic_id', $resource->topic_id)->pluck('id'))
                ->where('completed', true)
                ->count();
            
            if ($totalResourcesInTopic > 0) {
                $topicProgress = round(($studiedResourcesInTopic / $totalResourcesInTopic) * 100);
            }
        }
        
        $relatedResources = Resource::where(function($query) use ($resource) {
                $query->where('unit_id', $resource->unit_id)
                    ->orWhere('topic_id', $resource->topic_id);
            })
            ->where('id', '!=', $resource->id)
            ->with(['unit', 'user'])
            ->latest()
            ->limit(5)
            ->get();
        
        $resource->increment('views_count');
        
        return view('student.resources.show', compact(
            'resource',
            'userDownloadCount',
            'lastDownloaded',
            'markedStudied',
            'userNotes',
            'topicProgress',
            'relatedResources'
        ));
    }

    /**
     * View a resource within the system (opens in embedded viewer)
     * Passes cumulative time data to view for resume prompt
     */
    public function viewer($id)
    {
        $resource = Resource::findOrFail($id);
        
        // Check if student is enrolled in this unit
        $student = Auth::user();
        $isEnrolled = $student->enrolledUnits()
            ->where('unit_id', $resource->unit_id)
            ->exists();
        
        if (!$isEnrolled && !$student->hasRole('admin')) {
            abort(403, 'You are not enrolled in the unit for this resource.');
        }
        
        // For links, redirect directly to the URL instead of showing viewer
        if ($resource->file_type === 'link' && $resource->url) {
            return redirect()->away($resource->url);
        }
        
        // Increment view count
        $resource->increment('views_count');
        
        // Get study progress for student
        $totalStudied = 0;
        $hasProgress = false;
        
        if ($student->hasRole('student')) {
            $progress = StudyProgress::where('resource_id', $id)
                ->where('student_id', $student->id)
                ->first();
            
            if ($progress) {
                // Convert minutes to seconds for display
                $totalStudied = ($progress->time_spent_minutes ?? 0) * 60;
                $hasProgress = $totalStudied > 0;
            }
        }
        
        return view('student.resources.viewer', compact('resource', 'totalStudied', 'hasProgress'));
    }

    /**
     * Resume study session - marks session as resumed
     */
    public function resumeStudy($id)
    {
        session(['resumed' => true]);
        return response()->json(['success' => true]);
    }

    /**
     * Restart study session - deletes all study progress for this resource
     */
    public function restartStudy($id)
    {
        StudyProgress::where('resource_id', $id)
            ->where('student_id', auth()->id())
            ->delete();
        
        session()->forget('resumed');
        return response()->json(['success' => true]);
    }

    /**
     * Track view time and accumulate total study time
     * Uses time_spent_minutes column (stores in minutes)
     */
    public function trackView(Request $request, $id)
    {
        $resource = Resource::findOrFail($id);
        
        if (auth()->user()->hasRole('student')) {
            $progress = StudyProgress::firstOrCreate(
                [
                    'student_id' => auth()->id(),
                    'resource_id' => $resource->id,
                ],
                [
                    'user_id' => auth()->id(),
                    'unit_code' => $resource->unit_code,
                    'last_studied_at' => now(),
                    'time_spent_minutes' => 0
                ]
            );
            
            if ($request->has('time_spent')) {
                $seconds = (int)$request->time_spent;
                $minutes = round($seconds / 60, 2);
                $progress->increment('time_spent_minutes', $minutes);
                $progress->last_studied_at = now();
                $progress->save();
            }
            
            return response()->json([
                'success' => true,
                'total_time_spent' => ($progress->time_spent_minutes ?? 0) * 60
            ]);
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Save resource to unit resources folder (download from forum to unit)
     */
    public function saveToUnit($id)
    {
        $student = Auth::user();
        
        $resource = Resource::with(['unit'])->findOrFail($id);
        
        // Check if student is enrolled
        $isEnrolled = $student->enrolledUnits()
            ->where('unit_id', $resource->unit_id)
            ->exists();
        
        if (!$isEnrolled && !$student->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'You are not enrolled in this unit.'], 403);
        }
        
        // Check if resource already exists in this unit's folder
        $existingResource = Resource::where('unit_id', $resource->unit_id)
            ->where('file_name', $resource->file_name)
            ->where('id', '!=', $resource->id)
            ->first();
        
        if ($existingResource) {
            return response()->json([
                'success' => false, 
                'message' => 'This file already exists in the unit resources. You can access it from the Resources section.',
                'existing' => true
            ]);
        }
        
        // If it's a link, just save it
        if ($resource->file_type === 'link') {
            $newResource = Resource::create([
                'title' => $resource->title,
                'description' => $resource->description,
                'unit_id' => $resource->unit_id,
                'unit_code' => $resource->unit_code,
                'file_type' => 'link',
                'url' => $resource->url,
                'uploaded_by' => $student->id,
                'is_official' => false,
            ]);
            
            return response()->json(['success' => true, 'message' => 'Link saved to unit resources!']);
        }
        
        // For files, copy to unit resources folder
        if ($resource->file_path && Storage::disk('public')->exists($resource->file_path)) {
            $fileName = $resource->file_name;
            $newPath = "resources/{$resource->unit_code}/" . time() . '_' . $fileName;
            
            // Copy the file
            Storage::disk('public')->copy($resource->file_path, $newPath);
            
            // Create new resource record
            $newResource = Resource::create([
                'title' => $resource->title,
                'description' => $resource->description,
                'unit_id' => $resource->unit_id,
                'unit_code' => $resource->unit_code,
                'file_type' => $resource->file_type,
                'file_path' => $newPath,
                'file_name' => $resource->file_name,
                'file_size' => $resource->file_size,
                'uploaded_by' => $student->id,
                'download_count' => 0,
                'views_count' => 0,
                'is_official' => false,
            ]);
            
            return response()->json(['success' => true, 'message' => 'File saved to unit resources!']);
        }
        
        return response()->json(['success' => false, 'message' => 'File not found.'], 404);
    }

    /**
     * Download or view a resource.
     * For PDFs: Opens inline in browser (not downloaded to file explorer)
     * For Word/Excel/PowerPoint: Forces download
     * For links: Redirects to URL
     * For other files: Downloads to computer
     */
    public function download($id)
    {
        $student = Auth::user();
        
        $resource = Resource::findOrFail($id);
        
        $isEnrolled = $student->enrolledUnits()
            ->where('unit_id', $resource->unit_id)
            ->exists();
        
        if (!$isEnrolled) {
            abort(403, 'You are not enrolled in the unit for this resource.');
        }
        
        // Track download
        ResourceDownload::create([
            'resource_id' => $resource->id,
            'user_id' => $student->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        
        $resource->increment('download_count');
        
        // If it's a link type, redirect to the URL
        if ($resource->file_type === 'link' && $resource->url) {
            return redirect()->away($resource->url);
        }
        
        // Check if file exists
        if (!$resource->file_path || !Storage::disk('public')->exists($resource->file_path)) {
            return back()->with('error', 'File not found.');
        }
        
        $filePath = storage_path('app/public/' . $resource->file_path);
        $fileName = $resource->file_name ?? basename($resource->file_path);
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Office file types - force download
        $officeExtensions = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pps'];
        
        // For PDF files, open inline in browser
        if ($resource->file_type === 'pdf' || $extension === 'pdf') {
            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                'Cache-Control' => 'public, max-age=3600',
            ]);
        }
        
        // For images, open inline
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'])) {
            return response()->file($filePath, [
                'Content-Type' => mime_content_type($filePath),
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        }
        
        // For videos, open inline
        if (in_array($extension, ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv'])) {
            return response()->file($filePath, [
                'Content-Type' => 'video/' . $extension,
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        }
        
        // For Office files - force download
        if (in_array($extension, $officeExtensions)) {
            return Storage::disk('public')->download($resource->file_path, $fileName, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        }
        
        // For all other files - download
        return Storage::disk('public')->download($resource->file_path, $fileName);
    }

    /**
     * Track download via AJAX.
     */
    public function trackDownload(Request $request)
    {
        $request->validate([
            'resource_id' => 'required|exists:resources,id'
        ]);
        
        $student = Auth::user();
        $resource = Resource::find($request->resource_id);
        
        $isEnrolled = $student->enrolledUnits()
            ->where('unit_id', $resource->unit_id)
            ->exists();
        
        if ($isEnrolled) {
            ResourceDownload::create([
                'resource_id' => $resource->id,
                'user_id' => $student->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            $resource->increment('download_count');
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Mark resource as studied.
     */
    public function markStudied(Request $request, $id)
    {
        $student = Auth::user();
        $resource = Resource::with('unit')->findOrFail($id);
        
        // Check enrollment
        $isEnrolled = $student->enrolledUnits()
            ->where('unit_id', $resource->unit_id)
            ->exists();
        
        if (!$isEnrolled) {
            return response()->json(['success' => false, 'message' => 'You are not enrolled in this unit.'], 403);
        }
        
        // Create or update study progress with all required fields
        StudyProgress::updateOrCreate(
            [
                'student_id' => $student->id,
                'resource_id' => $resource->id,
            ],
            [
                'user_id' => $student->id,
                'unit_code' => $resource->unit->code,
                'topic_id' => $resource->topic_id,
                'completed' => true,
                'completed_at' => now(),
                'duration_minutes' => $request->duration ?? 15,
                'last_studied_at' => now(),
            ]
        );
        
        return response()->json(['success' => true, 'message' => 'Resource marked as studied.']);
    }

    /**
     * Save study notes for a resource.
     */
    public function saveNotes(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string'
        ]);
        
        $student = Auth::user();
        $resource = Resource::with('unit')->findOrFail($id);
        
        // Check enrollment
        $isEnrolled = $student->enrolledUnits()
            ->where('unit_id', $resource->unit_id)
            ->exists();
        
        if (!$isEnrolled) {
            return back()->with('error', 'You are not enrolled in the unit for this resource.');
        }
        
        // Update or create study progress with notes
        StudyProgress::updateOrCreate(
            [
                'student_id' => $student->id,
                'resource_id' => $resource->id,
            ],
            [
                'user_id' => $student->id,
                'unit_code' => $resource->unit->code,
                'topic_id' => $resource->topic_id,
                'notes' => $request->notes,
                'last_studied_at' => now(),
            ]
        );
        
        return back()->with('success', 'Notes saved successfully');
    }

    /**
     * Get resources by type.
     */
    public function byType($type)
    {
        $student = Auth::user();
        $enrolledUnitIds = $student->enrolledUnits()->pluck('id');
        
        $resources = Resource::whereIn('unit_id', $enrolledUnitIds)
            ->where('file_type', $type)
            ->with(['unit', 'user'])
            ->latest()
            ->paginate(12);
        
        return view('student.resources.index', compact('resources'));
    }

    /**
     * Get resources by unit.
     */
    public function byUnit($unitCode)
    {
        $student = Auth::user();
        $unit = Unit::where('code', $unitCode)->firstOrFail();
        
        $isEnrolled = $student->enrolledUnits()
            ->where('unit_id', $unit->id)
            ->exists();
        
        if (!$isEnrolled) {
            abort(403);
        }
        
        $resources = Resource::where('unit_id', $unit->id)
            ->with(['user', 'topic'])
            ->latest()
            ->paginate(12);
        
        return view('student.resources.by_unit', compact('resources', 'unit'));
    }

    /**
     * Get resources by topic.
     */
    public function byTopic($topicId)
    {
        $student = Auth::user();
        $topic = Topic::with('unit')->findOrFail($topicId);
        
        $isEnrolled = $student->enrolledUnits()
            ->where('unit_id', $topic->unit->id)
            ->exists();
        
        if (!$isEnrolled) {
            abort(403);
        }
        
        $resources = Resource::where('topic_id', $topicId)
            ->with(['user', 'unit'])
            ->latest()
            ->paginate(12);
        
        return view('student.resources.by_topic', compact('resources', 'topic'));
    }

    /**
     * Get recommended resources based on student's study patterns.
     */
    public function recommendations()
    {
        $student = Auth::user();
        $enrolledUnitIds = $student->enrolledUnits()->pluck('id');
        
        $strugglingTopics = StudyProgress::where('student_id', $student->id)
            ->where('completed', false)
            ->orderBy('duration_minutes', 'desc')
            ->limit(3)
            ->pluck('topic_id');
        
        $recentlyAccessed = ResourceDownload::where('user_id', $student->id)
            ->with('resource')
            ->latest()
            ->limit(5)
            ->get()
            ->pluck('resource.id');
        
        $query = Resource::whereIn('unit_id', $enrolledUnitIds)
            ->with(['unit', 'topic', 'user']);
        
        if ($strugglingTopics->isNotEmpty()) {
            $query->whereIn('topic_id', $strugglingTopics);
        } else {
            if ($recentlyAccessed->isNotEmpty()) {
                $query->whereNotIn('id', $recentlyAccessed);
            }
        }
        
        $recommendations = $query->latest()->limit(6)->get();
        
        return view('student.resources.recommendations', compact('recommendations'));
    }
}