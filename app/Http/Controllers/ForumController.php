<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\StudentQuestionNotification;

class ForumController extends Controller
{
    /**
     * Display the forum index page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $unitCode = $request->get('unit');
        $tag = $request->get('tag');
        $search = $request->get('search');
        
        // Base query
        $query = ForumPost::with(['user', 'unit'])
            ->withCount('replies');
        
        // Filter by unit based on user role
        if ($user->hasRole('student')) {
            // Students see posts from units they're enrolled in
            $unitCodes = $user->enrolledUnits()->pluck('code')->toArray();
            $query->whereIn('unit_code', $unitCodes);
        } elseif ($user->hasRole('lecturer')) {
            // Lecturers see posts from units they teach
            $unitCodes = $user->units()->pluck('code')->toArray();
            $query->whereIn('unit_code', $unitCodes);
        }
        // Admin sees all (no filter)
        
        // Apply additional filters
        if ($unitCode) {
            $query->where('unit_code', $unitCode);
        }
        
        if ($tag) {
            $query->whereJsonContains('tags', $tag);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        // Get pinned posts first, then by latest
        $posts = $query->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Get available units for filtering
        if ($user->hasRole('admin')) {
            $units = Unit::select('code', 'name')->orderBy('code')->get();
        } elseif ($user->hasRole('lecturer')) {
            $units = $user->units()->select('code', 'name')->orderBy('code')->get();
        } else {
            $units = $user->enrolledUnits()->select('code', 'name')->orderBy('code')->get();
        }
        
        return view('forum.index', [
            'posts' => $posts,
            'units' => $units,
            'currentUnit' => $unitCode,
            'currentTag' => $tag,
            'search' => $search,
        ]);
    }
    
    /**
     * Show form to create a new post.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get available units for posting
        if ($user->hasRole('admin')) {
            $units = Unit::select('code', 'name')->orderBy('code')->get();
        } elseif ($user->hasRole('lecturer')) {
            $units = $user->units()->select('code', 'name')->orderBy('code')->get();
        } else {
            $units = $user->enrolledUnits()->select('code', 'name')->orderBy('code')->get();
        }
        
        return view('forum.create', [
            'units' => $units
        ]);
    }
    
    /**
     * Store a new forum post.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'unit_code' => 'required|exists:units,code',
            'tags' => 'nullable|string',
        ]);
        
        $unit = Unit::where('code', $request->unit_code)->first();
        
        $post = ForumPost::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
            'unit_id' => $unit->id,
            'unit_code' => $request->unit_code,
            'tags' => $request->tags ? array_map('trim', explode(',', $request->tags)) : [],
            'views' => 0,
            'is_pinned' => false,
            'is_announcement' => false,
        ]);
        
        // If the post is created by a student, notify the unit's lecturer
        if (Auth::user()->hasRole('student')) {
            $lecturer = User::find($unit->lecturer_id);
            if ($lecturer) {
                $lecturer->notify(new StudentQuestionNotification($post));
            }
        }
        
        return redirect()->route('forum.show', $post->id)
            ->with('success', 'Post created successfully!');
    }
    
    /**
     * Display a single forum post.
     */
    public function show($id)
    {
        $post = ForumPost::with(['user', 'unit', 'replies.user'])
            ->withCount('replies')
            ->findOrFail($id);
        
        // Check if user has access to this unit
        $user = Auth::user();
        $hasAccess = false;
        
        if ($user->hasRole('admin')) {
            $hasAccess = true;
        } elseif ($user->hasRole('lecturer')) {
            $hasAccess = $user->units()->where('code', $post->unit_code)->exists();
        } else {
            $hasAccess = $user->enrolledUnits()->where('code', $post->unit_code)->exists();
        }
        
        if (!$hasAccess) {
            abort(403, 'You do not have access to this forum post.');
        }
        
        // Increment views
        $post->incrementViews();
        
        return view('forum.show', [
            'post' => $post
        ]);
    }
    
    /**
     * Add a reply to a post.
     */
    public function reply(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);
        
        $post = ForumPost::findOrFail($postId);
        
        // Check access
        $user = Auth::user();
        $hasAccess = false;
        
        if ($user->hasRole('admin')) {
            $hasAccess = true;
        } elseif ($user->hasRole('lecturer')) {
            $hasAccess = $user->units()->where('code', $post->unit_code)->exists();
        } else {
            $hasAccess = $user->enrolledUnits()->where('code', $post->unit_code)->exists();
        }
        
        if (!$hasAccess) {
            abort(403, 'You do not have access to this forum post.');
        }
        
        $reply = ForumReply::create([
            'content' => $request->content,
            'user_id' => Auth::id(),
            'forum_post_id' => $postId,
        ]);
        
        return back()->with('success', 'Reply added successfully!');
    }
}