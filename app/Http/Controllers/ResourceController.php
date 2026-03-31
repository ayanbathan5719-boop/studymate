<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\ResourceDownload;
use App\Models\Unit;
use App\Models\Topic;
use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResourceController extends Controller
{
    /**
     * Display a listing of resources for a unit.
     */
    public function index(Request $request, $unitCode = null)
    {
        $user = Auth::user();
        $query = Resource::with(['user', 'unit', 'topic'])->approved();

        // Filter by unit if provided
        if ($unitCode) {
            $query->forUnit($unitCode);
        } elseif ($user->isStudent()) {
            // Students see resources from their enrolled units
            $unitCodes = $user->enrolledUnits()->pluck('code')->toArray();
            $query->whereIn('unit_code', $unitCodes);
        } elseif ($user->isLecturer()) {
            // Lecturers see resources from their assigned units
            $unitCodes = $user->units()->pluck('code')->toArray();
            $query->whereIn('unit_code', $unitCodes);
        }

        // Apply filters
        if ($request->filled('topic')) {
            $query->forTopic($request->topic);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'popular':
                $query->orderBy('download_count', 'desc');
                break;
            case 'views':
                $query->orderBy('view_count', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $resources = $query->paginate(15);

        // Get units for filter dropdown
        $units = $user->isAdmin() 
            ? Unit::orderBy('code')->get()
            : ($user->isLecturer() 
                ? $user->units()->orderBy('code')->get()
                : $user->enrolledUnits()->orderBy('code')->get());

        // Get topics for filter dropdown
        $topics = $unitCode 
            ? Topic::where('unit_code', $unitCode)->orderBy('order')->get()
            : collect();

        return view('resources.index', [
            'resources' => $resources,
            'units' => $units,
            'topics' => $topics,
            'currentUnit' => $unitCode,
            'filters' => $request->only(['topic', 'type', 'search', 'sort'])
        ]);
    }

    /**
     * Show form to create a new resource.
     */
    public function create(Request $request, $unitCode = null)
    {
        $user = Auth::user();

        // Get units user can upload to
        if ($user->isAdmin()) {
            $units = Unit::orderBy('code')->get();
        } elseif ($user->isLecturer()) {
            $units = $user->units()->orderBy('code')->get();
        } else {
            $units = $user->enrolledUnits()->orderBy('code')->get();
        }

        $selectedUnit = $unitCode ? Unit::where('code', $unitCode)->first() : null;
        
        // Get topics for selected unit
        $topics = $selectedUnit 
            ? Topic::where('unit_code', $selectedUnit->code)->orderBy('order')->get()
            : collect();

        return view('resources.create', [
            'units' => $units,
            'topics' => $topics,
            'selectedUnit' => $selectedUnit,
            'forumPosts' => $selectedUnit ? $selectedUnit->forumPosts()->orderBy('created_at', 'desc')->get() : collect()
        ]);
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_code' => 'required|exists:units,code',
            'topic_id' => 'nullable|exists:topics,id',
            'type' => 'required|in:file,link,youtube',
            'forum_post_id' => 'nullable|exists:forum_posts,id',
            'file' => 'required_if:type,file|file|max:10240', // 10MB max
            'url' => 'required_if:type,link,youtube|url|nullable'
        ]);

        // Check if user has access to this unit
        if (!$user->isAdmin()) {
            $hasAccess = $user->isLecturer() 
                ? $user->units()->where('code', $request->unit_code)->exists()
                : $user->enrolledUnits()->where('code', $request->unit_code)->exists();
            
            if (!$hasAccess) {
                return back()->with('error', 'You do not have access to this unit.');
            }
        }

        $resourceData = [
            'title' => $request->title,
            'description' => $request->description,
            'unit_code' => $request->unit_code,
            'topic_id' => $request->topic_id,
            'user_id' => $user->id,
            'type' => $request->type,
            'forum_post_id' => $request->forum_post_id
        ];

        // Handle file upload
        if ($request->type === 'file' && $request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('resources/' . $request->unit_code . '/' . date('Y/m'), 'public');
            
            $resourceData['file_path'] = $path;
            $resourceData['file_name'] = $file->getClientOriginalName();
            $resourceData['file_size'] = $file->getSize();
            $resourceData['mime_type'] = $file->getMimeType();
        }

        // Handle link
        if (in_array($request->type, ['link', 'youtube'])) {
            $resourceData['url'] = $request->url;
            
            // Auto-detect YouTube
            if (str_contains($request->url, 'youtube.com') || str_contains($request->url, 'youtu.be')) {
                $resourceData['type'] = 'youtube';
                
                // Try to fetch thumbnail
                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $request->url, $matches);
                if (isset($matches[1])) {
                    $resourceData['thumbnail'] = "https://img.youtube.com/vi/{$matches[1]}/maxresdefault.jpg";
                }
            }
        }

        $resource = Resource::create($resourceData);

        // Create forum post for this resource (optional)
        if ($request->has('create_forum_post') && $request->create_forum_post) {
            $post = ForumPost::create([
                'title' => 'Resource: ' . $request->title,
                'content' => "A new resource has been uploaded:\n\n**{$request->title}**\n\n{$request->description}\n\n[View Resource](#)",
                'user_id' => $user->id,
                'unit_id' => Unit::where('code', $request->unit_code)->first()->id,
                'unit_code' => $request->unit_code
            ]);
            
            $resource->update(['forum_post_id' => $post->id]);
        }

        return redirect()->route('resources.show', $resource)
            ->with('success', 'Resource uploaded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Resource $resource)
    {
        $user = Auth::user();

        // Check access
        if (!$user->isAdmin()) {
            $hasAccess = $user->isLecturer() 
                ? $user->units()->where('code', $resource->unit_code)->exists()
                : $user->enrolledUnits()->where('code', $resource->unit_code)->exists();
            
            if (!$hasAccess) {
                abort(403, 'You do not have access to this resource.');
            }
        }

        // Increment view count
        $resource->incrementViews();

        // Get related resources
        $related = Resource::where('unit_code', $resource->unit_code)
            ->where('id', '!=', $resource->id)
            ->approved()
            ->orderBy('download_count', 'desc')
            ->limit(5)
            ->get();

        return view('resources.show', [
            'resource' => $resource,
            'related' => $related
        ]);
    }

    /**
     * Download the resource.
     */
    public function download(Resource $resource)
    {
        $user = Auth::user();

        // Check access
        if (!$user->isAdmin()) {
            $hasAccess = $user->isLecturer() 
                ? $user->units()->where('code', $resource->unit_code)->exists()
                : $user->enrolledUnits()->where('code', $resource->unit_code)->exists();
            
            if (!$hasAccess) {
                abort(403, 'You do not have access to this resource.');
            }
        }

        // Check if it's a downloadable file
        if (!$resource->is_file) {
            return redirect($resource->url);
        }

        // Record download
        ResourceDownload::create([
            'resource_id' => $resource->id,
            'user_id' => $user->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        // Increment download count
        $resource->incrementDownloads();

        // Return file download
        return Storage::disk('public')->download($resource->file_path, $resource->file_name);
    }

    /**
     * Show form to edit resource.
     */
    public function edit(Resource $resource)
    {
        $user = Auth::user();

        // Check ownership
        if (!$user->isAdmin() && $resource->user_id !== $user->id) {
            abort(403, 'You can only edit your own resources.');
        }

        $units = $user->isAdmin() 
            ? Unit::orderBy('code')->get()
            : ($user->isLecturer() 
                ? $user->units()->orderBy('code')->get()
                : $user->enrolledUnits()->orderBy('code')->get());

        $topics = Topic::where('unit_code', $resource->unit_code)->orderBy('order')->get();

        return view('resources.edit', [
            'resource' => $resource,
            'units' => $units,
            'topics' => $topics
        ]);
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, Resource $resource)
    {
        $user = Auth::user();

        // Check ownership
        if (!$user->isAdmin() && $resource->user_id !== $user->id) {
            abort(403, 'You can only edit your own resources.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'topic_id' => 'nullable|exists:topics,id'
        ]);

        $resource->update([
            'title' => $request->title,
            'description' => $request->description,
            'topic_id' => $request->topic_id
        ]);

        return redirect()->route('resources.show', $resource)
            ->with('success', 'Resource updated successfully!');
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(Resource $resource)
    {
        $user = Auth::user();

        // Check ownership
        if (!$user->isAdmin() && $resource->user_id !== $user->id) {
            abort(403, 'You can only delete your own resources.');
        }

        // Delete file if exists
        if ($resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }

        // Delete download records
        $resource->downloads()->delete();

        // Delete resource
        $resource->delete();

        return redirect()->route('resources.index', ['unit' => $resource->unit_code])
            ->with('success', 'Resource deleted successfully!');
    }

    /**
     * Toggle pin status.
     */
    public function togglePin(Resource $resource)
    {
        $user = Auth::user();

        // Only admin and lecturers can pin
        if (!$user->isAdmin() && !$user->isLecturer()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check lecturer access
        if ($user->isLecturer() && !$user->units()->where('code', $resource->unit_code)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $resource->update([
            'is_pinned' => !$resource->is_pinned
        ]);

        return response()->json([
            'success' => true,
            'is_pinned' => $resource->is_pinned
        ]);
    }

    /**
     * Toggle approval status (admin only).
     */
    public function toggleApproval(Resource $resource)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $resource->update([
            'is_approved' => !$resource->is_approved
        ]);

        return response()->json([
            'success' => true,
            'is_approved' => $resource->is_approved
        ]);
    }
}