<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EnterpriseLoginController extends Controller
{
    /**
     * Show the enterprise login form
     */
    public function showLoginForm()
    {
        // If already authenticated as enterprise admin, redirect to dashboard
        if (Auth::check() && Auth::user()->is_enterprise_admin) {
            return redirect()->route('enterprise.dashboard');
        }
        
        return view('auth.enterprise-login');
    }

    /**
     * Handle enterprise login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check if user is enterprise admin
            if (!$user->is_enterprise_admin) {
                Auth::logout();
                
                throw ValidationException::withMessages([
                    'email' => ['This portal is for enterprise broker admins only. Please contact TheTradeVisor for access.'],
                ]);
            }
            
            // Check if user has active enterprise broker
            $broker = $user->enterpriseBroker;
            if (!$broker || !$broker->isCurrentlyActive()) {
                Auth::logout();
                
                throw ValidationException::withMessages([
                    'email' => ['Your enterprise subscription is inactive. Please contact TheTradeVisor support.'],
                ]);
            }
            
            $request->session()->regenerate();
            
            // Redirect to enterprise dashboard
            return redirect()->intended(route('enterprise.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    /**
     * Handle enterprise logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('enterprise.login');
    }
}
