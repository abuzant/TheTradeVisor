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
        if (Auth::guard('enterprise')->check()) {
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

        if (Auth::guard('enterprise')->attempt($credentials, $remember)) {
            $admin = Auth::guard('enterprise')->user();
            
            // Check if admin is active
            if (!$admin->is_active) {
                Auth::guard('enterprise')->logout();
                
                throw ValidationException::withMessages([
                    'email' => ['Your account has been deactivated. Please contact TheTradeVisor support.'],
                ]);
            }
            
            // Check if broker is active
            $broker = $admin->enterpriseBroker;
            if (!$broker || !$broker->isCurrentlyActive()) {
                Auth::guard('enterprise')->logout();
                
                throw ValidationException::withMessages([
                    'email' => ['Your enterprise subscription is inactive. Please contact TheTradeVisor support.'],
                ]);
            }
            
            // Update last login
            $admin->last_login_at = now();
            $admin->save();
            
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
        Auth::guard('enterprise')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('enterprise.login');
    }
}
