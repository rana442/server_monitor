<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\UserSetting;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        return view('profile.edit');
    }
    
    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'dark_mode' => ['sometimes', 'boolean'],
        ]);
        
        // Update user name
        $request->user()->update([
            'name' => $request->name,
        ]);
        
        // Update or create user settings
        UserSetting::updateOrCreate(
            ['user_id' => $request->user()->id],
            ['dark_mode' => $request->dark_mode]
        );
        
        return back()->with('success', 'Profile updated successfully.');
    }
    
    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        
        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);
        
        return back()->with('success', 'Password updated successfully.');
    }
    
    /**
     * Toggle theme (for AJAX requests).
     */
    public function toggleTheme(Request $request)
    {
        $request->validate([
            'dark_mode' => ['required', 'boolean'],
        ]);
        
        UserSetting::updateOrCreate(
            ['user_id' => $request->user()->id],
            ['dark_mode' => $request->boolean('dark_mode')]
        );
        
        return response()->json(['success' => true]);
    }
}