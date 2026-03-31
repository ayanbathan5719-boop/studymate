<?php

namespace App\Http\Controllers\Student;

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
        return view('student.profile.edit');
    }

    /**
     * Update the user's profile information (name only).
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        
        $user->update([
            'name' => $request->name,
        ]);
        
        return redirect()->route('student.profile.edit')->with('success', 'Profile updated successfully!');
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
        
        return redirect()->route('student.dashboard')->with('success', 'Password updated successfully!');
    }
}