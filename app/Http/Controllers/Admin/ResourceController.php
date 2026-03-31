<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    /**
     * Display a listing of all resources.
     */
    public function index(Request $request)
    {
        $query = Resource::with('user', 'unit');
        
        // Search filter
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('unit_code', 'like', '%' . $request->search . '%');
            });
        }
        
        // File type filter
        if ($request->has('file_type') && $request->file_type) {
            $query->where('file_type', $request->file_type);
        }
        
        $resources = $query->orderBy('created_at', 'desc')->paginate(12);
        
        return view('admin.resources.index', compact('resources'));
    }
    
    /**
     * Display the specified resource.
     */
    public function show(Resource $resource)
    {
        // Increment view count
        $resource->increment('views_count');
        
        return view('admin.resources.show', compact('resource'));
    }
    
    /**
     * Download a resource file.
     */
    public function download($id)
    {
        $resource = Resource::findOrFail($id);
        
        // If it's a link, redirect to the URL
        if ($resource->file_type == 'link') {
            return redirect($resource->url);
        }
        
        // Check if file exists
        if (!$resource->file_path || !Storage::disk('public')->exists($resource->file_path)) {
            abort(404, 'File not found');
        }
        
        // Increment download count
        $resource->increment('download_count');
        
        // Return file download
        return Storage::disk('public')->download($resource->file_path, $resource->file_name ?? 'download');
    }
    
    /**
     * Remove the specified resource.
     */
    public function destroy(Resource $resource)
    {
        // Delete file if exists
        if ($resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }
        
        $resource->delete();
        
        return redirect()->route('admin.resources.index')
            ->with('success', 'Resource deleted successfully.');
    }
}