<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Helpers\RedirectHelper;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $user = Auth::user();

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect sesuai role
            $role = strtolower($user->role->name);
            $dept = strtolower(optional($user->department)->name);

            switch ($role) {
                case 'admin':
                    return redirect()->route('dashboard.admin');
                case 'manager':
                    return redirect()->route('manager.dashboard');
                case 'staff':
                    if ($dept === 'production') {
                        return redirect()->route('production.index');
                    } elseif ($dept === 'qc') {
                        return redirect()->route('qc.index');
                    } elseif ($dept === 'warehouse') {
                        return redirect()->route('warehouse.stock.index');
                    } else {
                        return abort(403);
                    }
                default:
                    return redirect('/login');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah!',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
