<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display profile page
     */
    public function index()
    {
        $user = Auth::user();
        return view('admin.profile.index', compact('user'));
    }

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.profile')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ]);

        // Cek password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Password lama tidak sesuai!'])
                ->withInput();
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.profile')
            ->with('success', 'Password berhasil diubah!');
    }
}