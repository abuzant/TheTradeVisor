<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AffiliateAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('affiliate.auth.login');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        
        if (Auth::guard('affiliate')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Update last login
            Auth::guard('affiliate')->user()->update(['last_login_at' => now()]);
            
            return redirect()->intended(route('affiliate.dashboard'));
        }
        
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
    
    public function showRegistrationForm()
    {
        return view('affiliate.auth.register');
    }
    
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:affiliates'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:affiliates'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        
        $affiliate = Affiliate::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        
        Auth::guard('affiliate')->login($affiliate);
        
        return redirect()->route('affiliate.dashboard');
    }
    
    public function logout(Request $request)
    {
        Auth::guard('affiliate')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('affiliate.login');
    }
}
