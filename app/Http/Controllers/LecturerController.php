<?php

namespace App\Http\Controllers;

use App\Models\Lecturer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class LecturerController extends Controller
{
    /**
     * Display a listing of lecturers.
     */
    public function index()
    {
        $lecturers = Lecturer::orderBy('name')
            ->paginate(10);

        return view('admin.lecturers.index', [
            'lecturers' => $lecturers
        ]);
    }

    /**
     * Show form to create a new lecturer.
     */
    public function create()
    {
        return view('admin.lecturers.create');
    }

    /**
     * Store a newly created lecturer.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:lecturers,email|unique:users,email',
            'department' => 'required|string|max:255',
            'password'   => ['required', 'confirmed', Password::defaults()],
        ]);

        // Create user account (for authentication)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        
        // Assign lecturer role
        $user->assignRole('lecturer');
        
        // Create lecturer profile (links via email)
        Lecturer::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'department' => $request->department,
            'user_id'    => $user->id,
        ]);

        return redirect()->route('admin.lecturers.index')
            ->with('success', 'Lecturer created successfully! They can now log in with their email and password.');
    }

    /**
     * Show the form for editing a lecturer.
     */
    public function edit(Lecturer $lecturer)
    {
        return view('admin.lecturers.edit', [
            'lecturer' => $lecturer
        ]);
    }

    /**
     * Update the specified lecturer.
     */
    public function update(Request $request, Lecturer $lecturer)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:lecturers,email,' . $lecturer->id,
            'department' => 'required|string|max:255',
        ]);

        // Update lecturer profile
        $lecturer->update($validated);
        
        // Update associated user account if exists
        $user = User::where('email', $lecturer->email)->first();
        if ($user) {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
        }

        return redirect()->route('admin.lecturers.index')
            ->with('success', 'Lecturer updated successfully!');
    }

    /**
     * Remove the specified lecturer.
     */
    public function destroy(Lecturer $lecturer)
    {
        // Delete associated user account
        $user = User::where('email', $lecturer->email)->first();
        if ($user) {
            $user->delete();
        }
        
        $lecturer->delete();

        return redirect()->route('admin.lecturers.index')
            ->with('success', 'Lecturer deleted successfully!');
    }
}