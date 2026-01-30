<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show profile page.
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Update profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Only admin can change name and surname
        if ($user->isAdmin()) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'hospital' => 'nullable|string|max:255',
                'position' => 'nullable|string|max:255',
            ]);

            $user->update($validated);
        } else {
            $validated = $request->validate([
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'hospital' => 'nullable|string|max:255',
                'position' => 'nullable|string|max:255',
            ]);

            $user->update([
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'hospital' => $validated['hospital'] ?? null,
                'position' => $validated['position'] ?? null,
            ]);
        }

        return back()->with('success', 'Profil uğurla yeniləndi');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Cari şifrə tələb olunur',
            'password.required' => 'Yeni şifrə tələb olunur',
            'password.min' => 'Şifrə minimum 6 simvol olmalıdır',
            'password.confirmed' => 'Şifrə təsdiqi uyğun gəlmir',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Cari şifrə yanlışdır']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Şifrə uğurla dəyişdirildi');
    }
}
