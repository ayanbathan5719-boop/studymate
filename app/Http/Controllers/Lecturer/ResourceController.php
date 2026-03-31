<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\Unit;
use App\Models\Topic;
use App\Models\ResourceDownload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResourceController extends Controller
{
    /**
     * Display a listing of the lecturer's resources.
     */
    public function index(Request $request)
    {
        $lecturer = Auth::user();
        
        // Get units assigned to this lecturer
        $assignedUnits = $lecturer->assignedUnits()->get();
        $assignedUnitIds = $assignedUnits->pluck('id');
        
        // Base query - only show resources from assigned units
        $query = Resource::whereIn('unit_id', $assignedUnitIds)
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
        
        // Apply type filter - FIXED: changed 'type' to 'file_type'
        if ($request->filled('type')) {
            $query->where('file_type', $request->type);
        }
        
        // Get resources with pagination
        $resources = $query->latest()->paginate(15)->withQueryString();
        
        // ========== FIXED: Changed 'type' to 'file_type' in stats ==========
        $stats = [
            'total' => Resource::whereIn('unit_id', $assignedUnitIds)->count(),
            'pdf' => Resource::whereIn('unit_id', $assignedUnitIds)->where('file_type', 'pdf')->count(),
            'video' => Resource::whereIn('unit_id', $assignedUnitIds)->where('file_type', 'video')->count(),
            'link' => Resource::whereIn('unit_id', $assignedUnitIds)->where('file_type', 'link')->count(),
            'document' => Resource::whereIn('unit_id', $assignedUnitIds)->where('file_type', 'document')->count(),
        ];
        // ========== END FIX ==========
        
        return view('lecturer.resources.index', compact('resources', 'assignedUnits', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $lecturer = Auth::user();
        
        // Get assigned units for dropdown
        $assignedUnits = $lecturer->assignedUnits()->with('topics')->get();
        
        // If unit is pre-selected in URL
        $selectedUnit = null;
        $topics = collect(); // Empty collection by default
        
        if ($request->has('unit')) {
            $selectedUnit = $assignedUnits->where('code', $request->unit)->first();
            if ($selectedUnit) {
                $topics = Topic::where('unit_code', $selectedUnit->code)->get();
            }
        }
        
        return view('lecturer.resources.create', compact('assignedUnits', 'selectedUnit', 'topics'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $lecturer = Auth::user();
        
        // Validate based on resource type
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_code' => 'required|exists:units,code',
            'topic_id' => 'nullable|exists:topics,id',
            'type' => 'required|in:pdf,video,link,document',
        ];
        
        // Add type-specific validation
        if ($request->type === 'link') {
            $rules['url'] = 'required|url|max:500';
        } else {
            $rules['file'] = 'required|file|max:51200'; // 50MB max
            if ($request->type === 'pdf') {
                $rules['file'] = 'required|file|mimes:pdf|max:51200';
            } elseif ($request->type === 'video') {
                $rules['file'] = 'required|file|mimes:mp4,mov,avi|max:204800'; // 200MB for video
            } elseif ($request->type === 'document') {
                $rules['file'] = 'required|file|mimes:doc,docx,ppt,pptx,txt|max:51200';
            }
        }
        
        $request->validate($rules);
        
        // Check if unit is assigned to this lecturer
        $unit = Unit::where('code', $request->unit_code)->first();
        if (!$lecturer->assignedUnits()->where('unit_id', $unit->id)->exists()) {
            return back()->with('error', 'You are not authorized to add resources to this unit.');
        }
        
        // Prepare resource data
        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'unit_id' => $unit->id,
            'unit_code' => $request->unit_code,
            'topic_id' => $request->topic_id,
            'file_type' => $request->type,
            'uploaded_by' => $lecturer->id,
        ];
        
        // Handle file upload or URL based on type
        if ($request->type === 'link') {
            // Store the URL in file_path for links
            $data['file_path'] = $request->url;
            $data['file_name'] = $request->url;
            $data['file_size'] = 0; // Add default file_size for links
        } else {
            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('resources/' . $request->unit_code, $fileName, 'public');
                
                $data['file_path'] = $filePath;
                $data['file_name'] = $file->getClientOriginalName();
                $data['file_size'] = $file->getSize();
                $data['mime_type'] = $file->getMimeType();
            }
        }
        
        // Create resource
        $resource = Resource::create($data);
        
        return redirect()->route('lecturer.resources.index')
            ->with('success', 'Resource uploaded successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $lecturer = Auth::user();
        
        $resource = Resource::with(['user', 'unit', 'unit.course', 'topic'])
            ->withCount('downloads')
            ->findOrFail($id);
        
        // Check if resource belongs to lecturer's unit
        $unit = Unit::find($resource->unit_id);
        if (!$lecturer->assignedUnits()->where('unit_id', $unit->id)->exists()) {
            abort(403, 'You are not authorized to view this resource.');
        }
        
        // Get download statistics
        $downloads = ResourceDownload::where('resource_id', $resource->id)
            ->with('user')
            ->latest()
            ->paginate(20);
        
        $totalDownloads = $resource->downloads_count;
        $uniqueUsers = ResourceDownload::where('resource_id', $resource->id)
            ->distinct('user_id')
            ->count('user_id');
        
        return view('lecturer.resources.show', compact('resource', 'downloads', 'totalDownloads', 'uniqueUsers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $lecturer = Auth::user();
        
        $resource = Resource::findOrFail($id);
        
        // Check if resource belongs to lecturer's unit
        $unit = Unit::find($resource->unit_id);
        if (!$lecturer->assignedUnits()->where('unit_id', $unit->id)->exists()) {
            abort(403, 'You are not authorized to edit this resource.');
        }
        
        // Get assigned units for dropdown
        $assignedUnits = $lecturer->assignedUnits()->with('topics')->get();
        
        // Get topics for the current unit - FIXED: use unit_code instead of unit_id
        $topics = Topic::where('unit_code', $unit->code)->get();
        
        return view('lecturer.resources.edit', compact('resource', 'assignedUnits', 'topics'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $lecturer = Auth::user();
        
        $resource = Resource::findOrFail($id);
        
        // Check if resource belongs to lecturer's unit
        $unit = Unit::find($resource->unit_id);
        if (!$lecturer->assignedUnits()->where('unit_id', $unit->id)->exists()) {
            return back()->with('error', 'You are not authorized to update this resource.');
        }
        
        // Validate
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'topic_id' => 'nullable|exists:topics,id',
        ];
        
        // Only validate unit if it's being changed
        if ($request->has('unit_code') && $request->unit_code !== $resource->unit_code) {
            $rules['unit_code'] = 'required|exists:units,code';
        }
        
        // File validation if uploading new file
        if ($request->hasFile('file')) {
            if ($resource->file_type === 'pdf') {
                $rules['file'] = 'required|file|mimes:pdf|max:51200';
            } elseif ($resource->file_type === 'video') {
                $rules['file'] = 'required|file|mimes:mp4,mov,avi|max:204800';
            } elseif ($resource->file_type === 'document') {
                $rules['file'] = 'required|file|mimes:doc,docx,ppt,pptx,txt|max:51200';
            }
        }
        
        // URL validation for link type - Note: URLs are stored in file_path, not url column
        if ($resource->file_type === 'link' && $request->filled('url')) {
            $rules['url'] = 'required|url|max:500';
        }
        
        $request->validate($rules);
        
        // Prepare update data
        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'topic_id' => $request->topic_id,
        ];
        
        // Update unit if changed
        if ($request->has('unit_code') && $request->unit_code !== $resource->unit_code) {
            $newUnit = Unit::where('code', $request->unit_code)->first();
            
            // Check if new unit is assigned to lecturer
            if (!$lecturer->assignedUnits()->where('unit_id', $newUnit->id)->exists()) {
                return back()->with('error', 'You are not authorized to move resources to that unit.');
            }
            
            $data['unit_id'] = $newUnit->id;
            $data['unit_code'] = $request->unit_code;
        }
        
        // Handle file update
        if ($request->hasFile('file')) {
            // Delete old file
            if ($resource->file_path) {
                Storage::disk('public')->delete($resource->file_path);
            }
            
            $file = $request->file('file');
            $fileName = time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('resources/' . ($data['unit_code'] ?? $resource->unit_code), $fileName, 'public');
            
            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['mime_type'] = $file->getMimeType();
        }
        
        // Update URL for link type - Store in file_path column with file_size
        if ($resource->file_type === 'link' && $request->filled('url')) {
            $data['file_path'] = $request->url;
            $data['file_name'] = $request->url;
            $data['file_size'] = 0; // Add default file_size for links
        }
        
        $resource->update($data);
        
        return redirect()->route('lecturer.resources.show', $resource->id)
            ->with('success', 'Resource updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $lecturer = Auth::user();
        
        $resource = Resource::findOrFail($id);
        
        // Check if resource belongs to lecturer's unit
        $unit = Unit::find($resource->unit_id);
        if (!$lecturer->assignedUnits()->where('unit_id', $unit->id)->exists()) {
            return back()->with('error', 'You are not authorized to delete this resource.');
        }
        
        // Delete file if exists
        if ($resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }
        
        // Delete download records
        ResourceDownload::where('resource_id', $resource->id)->delete();
        
        $resource->delete();
        
        return redirect()->route('lecturer.resources.index')
            ->with('success', 'Resource deleted successfully');
    }

    /**
     * Download or view a resource (for lecturer).
     * For PDFs: Opens inline in browser (not downloaded to file explorer)
     * For images: Opens inline
     * For videos: Opens inline with browser player
     * For other files: Downloads to computer
     */
    public function download($id)
    {
        $lecturer = Auth::user();
        
        $resource = Resource::findOrFail($id);
        
        // Check if resource belongs to lecturer's unit
        $unit = Unit::find($resource->unit_id);
        if (!$lecturer->assignedUnits()->where('unit_id', $unit->id)->exists()) {
            abort(403, 'You are not authorized to access this resource.');
        }
        
        // Track download
        ResourceDownload::create([
            'resource_id' => $resource->id,
            'user_id' => $lecturer->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        
        $resource->increment('download_count');
        
        // If it's a link type, redirect to the URL stored in file_path
        if ($resource->file_type === 'link' && $resource->file_path) {
            return redirect()->away($resource->file_path);
        }
        
        // Check if file exists
        if (!$resource->file_path || !Storage::disk('public')->exists($resource->file_path)) {
            return back()->with('error', 'File not found.');
        }
        
        $filePath = storage_path('app/public/' . $resource->file_path);
        $fileName = $resource->file_name ?? basename($resource->file_path);
        
        // For PDF files, open inline in browser (not download)
        if ($resource->file_type === 'pdf') {
            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                'Cache-Control' => 'public, max-age=3600',
            ]);
        }
        
        // For images, open inline
        if (in_array($resource->file_type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return response()->file($filePath, [
                'Content-Type' => mime_content_type($filePath),
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        }
        
        // For videos, open inline (browser will use built-in player)
        if (in_array($resource->file_type, ['mp4', 'webm', 'ogg', 'mov'])) {
            return response()->file($filePath, [
                'Content-Type' => 'video/' . $resource->file_type,
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        }
        
        // For all other files (Word, Excel, PowerPoint, zip, etc.) - download
        return Storage::disk('public')->download(
            $resource->file_path,
            $fileName
        );
    }

    /**
     * Get topics for a unit (AJAX).
     */
    public function getTopics($unitCode)
    {
        try {
            // Get topics for this unit - use 'title' as the name field
            $topics = Topic::where('unit_code', $unitCode)
                ->select('id', 'title as name')
                ->where('status', 'published')
                ->orderBy('order')
                ->get();
            
            return response()->json($topics);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get all resources for a unit (for forum attachment)
     */
    public function getUnitResources($unitCode)
    {
        $lecturer = Auth::user();
        
        $unit = Unit::where('code', $unitCode)->first();
        
        if (!$unit) {
            return response()->json([]);
        }
        
        // Check if unit is assigned to lecturer
        if (!$lecturer->assignedUnits()->where('unit_id', $unit->id)->exists()) {
            return response()->json([]);
        }
        
        $resources = Resource::where('unit_code', $unitCode)
            ->select('id', 'title', 'file_type')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($resources);
    }
}