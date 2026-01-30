<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|min:6',
        ], [
            'username.required' => 'İstifadəçi adı tələb olunur',
            'password.required' => 'Şifrə tələb olunur',
            'password.min' => 'Şifrə minimum 6 simvol olmalıdır',
        ]);

        // Convert username to lowercase for case-insensitive login
        $credentials['username'] = strtolower($credentials['username']);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect based on user role
            if ($user->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->isDoctor()) {
                return redirect()->intended('/doctor/dashboard');
            } elseif ($user->isRegistrar()) {
                return redirect()->intended('/registrar/dashboard');
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'username' => 'İstifadəçi adı və ya şifrə yanlışdır.',
        ])->withInput($request->only('username'));
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Çıxış edildi');
    }
}
