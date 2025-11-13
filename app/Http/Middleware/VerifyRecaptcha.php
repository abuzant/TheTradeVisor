<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use ReCaptcha\ReCaptcha;
use Symfony\Component\HttpFoundation\Response;

class VerifyRecaptcha
{
    public function handle(Request $request, Closure $next): Response
    {
        // Skip if reCAPTCHA is disabled
        if (!config('services.recaptcha.enabled')) {
            return $next($request);
        }

        $token = $request->input('g-recaptcha-response');
        
        if (!$token) {
            return redirect()->back()
                ->withErrors(['recaptcha' => 'Please complete the reCAPTCHA verification.'])
                ->withInput();
        }

        $recaptcha = new ReCaptcha(config('services.recaptcha.secret_key'));
        $response = $recaptcha->verify($token, $request->ip());

        if (!$response->isSuccess()) {
            return redirect()->back()
                ->withErrors(['recaptcha' => 'reCAPTCHA verification failed. Please try again.'])
                ->withInput();
        }

        // Note: Score check is only for reCAPTCHA v3
        // For v2 (checkbox), we only check isSuccess()

        return $next($request);
    }
}
