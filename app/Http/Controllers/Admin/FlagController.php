<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Flag;
use App\Models\User;
use Illuminate\Http\Request;

class FlagController extends Controller
{
    /**
     * Display a listing of flags.
     */
    public function index(Request $request)
    {
        $query = Flag::with(['reporter', 'reportedUser', 'forumPost', 'forumReply'])
            ->latest();
        
        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by reason
        if ($request->has('reason') && $request->reason !== 'all') {
            $query->where('reason', $request->reason);
        }
        
        $flags = $query->paginate(15);
        
        // Get flagged users with counts - FIXED QUERY
        $flaggedUsers = User::whereHas('flagsReceived', function($q) {
            $q->where('status', 'pending');
        })->get()->map(function($user) {
            $user->flags_received_count = $user->flagsReceived()->where('status', 'pending')->count();
            return $user;
        });
        
        return view('admin.flags.index', [
            'flags' => $flags,
            'flaggedUsers' => $flaggedUsers,
            'currentStatus' => $request->get('status', 'all'),
            'currentReason' => $request->get('reason', 'all')
        ]);
    }

    /**
     * Display flags for a specific user.
     */
    public function userFlags(User $user)
    {
        $flags = Flag::with(['reporter', 'forumPost', 'forumReply'])
            ->where('reported_user_id', $user->id)
            ->latest()
            ->paginate(15);
        
        return view('admin.flags.user', [
            'user' => $user,
            'flags' => $flags
        ]);
    }

    /**
     * Show the form for editing a flag.
     */
    public function edit(Flag $flag)
    {
        $flag->load(['reporter', 'reportedUser', 'forumPost', 'forumReply']);
        
        return view('admin.flags.edit', [
            'flag' => $flag
        ]);
    }

    /**
     * Update the flag status.
     */
    public function update(Request $request, Flag $flag)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,resolved,dismissed',
            'admin_notes' => 'nullable|string',
            'action' => 'nullable|in:warn,suspend,ban,remove_forum_access'
        ]);

        $flag->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);

        // Handle actions on the reported user
        if ($request->filled('action')) {
            $user = $flag->reportedUser;
            
            switch ($request->action) {
                case 'warn':
                    // Log warning (can be expanded)
                    break;
                    
                case 'suspend':
                    $user->forum_access = false;
                    $user->forum_restricted_until = now()->addDays(7);
                    $user->save();
                    break;
                    
                case 'ban':
                    $user->forum_access = false;
                    $user->forum_restricted_until = null;
                    $user->save();
                    break;
                    
                case 'remove_forum_access':
                    $user->forum_access = false;
                    $user->forum_restricted_until = null;
                    $user->save();
                    break;
            }
        }

        return redirect()->route('admin.flags.index')
            ->with('success', 'Flag updated successfully.');
    }

    /**
     * Remove the specified flag.
     */
    public function destroy(Flag $flag)
    {
        $flag->delete();
        
        return redirect()->route('admin.flags.index')
            ->with('success', 'Flag deleted successfully.');
    }

    /**
     * Bulk update flags.
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'flag_ids' => 'required|array',
            'flag_ids.*' => 'exists:flags,id',
            'status' => 'required|in:pending,reviewed,resolved,dismissed'
        ]);

        Flag::whereIn('id', $request->flag_ids)
            ->update([
                'status' => $request->status,
                'resolved_by' => auth()->id(),
                'resolved_at' => now(),
            ]);

        return redirect()->route('admin.flags.index')
            ->with('success', count($request->flag_ids) . ' flags updated successfully.');
    }
}