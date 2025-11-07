<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        return view('settings.api-key', compact('user'));
    }
    
    public function regenerate(Request $request)
    {
        $user = $request->user();
        $newKey = $user->regenerateApiKey();
        
        return redirect()
            ->route('settings.api-key')
            ->with('success', 'API key regenerated successfully!')
            ->with('new_key', $newKey);
    }
}
