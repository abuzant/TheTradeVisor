<?php

namespace App\Http\Controllers;

use App\Models\DigestSubscription;
use App\Services\DigestService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class MyDigestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function show(Request $request, DigestService $digestService): View
    {
        $user = $request->user();

        // Try to load the latest HTML digest for today
        $today = now()->format('Y-m-d');
        $relativePath = "digests/{$user->id}/{$today}.html";
        $htmlContent = null;

        \Log::info("MyDigest: Looking for digest at {$relativePath}");

        if (Storage::disk('local')->exists($relativePath)) {
            $htmlContent = Storage::disk('local')->get($relativePath);
            \Log::info("MyDigest: Found digest, size: " . strlen($htmlContent));
        } else {
            \Log::info("MyDigest: Digest not found");
        }

        return view('digest.show', [
            'user' => $user,
            'htmlContent' => $htmlContent,
            'today' => $today,
        ]);
    }
}
