<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        return view('lecturer.profile.edit');
    }

    /**
     * Update the user's profile information (name only, email not allowed).
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Email validation removed - lecturers cannot change email
        ]);
        
        $user->update([
            'name' => $request->name,
            // Email NOT updated - kept as original
        ]);
        
        return redirect()->route('lecturer.profile.edit')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update user avatar.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = auth()->user();
        
        // Delete old avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        
        // Update user with new avatar path
        $user->avatar = $path;
        $user->save();
        
        return response()->json(['success' => true, 'path' => Storage::url($path)]);
    }

    /**
     * Show the password change form.
     */
    public function password()
    {
        return view('lecturer.profile.password');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);
        
        auth()->user()->update([
            'password' => Hash::make($request->new_password),
        ]);
        
        return redirect()->route('lecturer.dashboard')->with('success', 'Password updated successfully!');
    }
}