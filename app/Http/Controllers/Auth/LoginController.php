<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Akun tidak ditemukan.',
            ])->withInput($request->only('email', 'remember'));
        }

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors([
                'password' => 'Password yang Anda masukkan salah.',
            ])->withInput($request->only('email', 'remember'));
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            return back()->withErrors([
                'email' => 'Akun Anda dinonaktifkan. Hubungi administrator.',
            ]);
        }

        $request->session()->regenerate();

        AuditLogService::log('login', 'auth', "User {$user->name} logged in");

        if ($user->role === 'staff') {
            return redirect()->route('my-warehouse');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            AuditLogService::log('logout', 'auth', "User {$user->name} logged out");
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
