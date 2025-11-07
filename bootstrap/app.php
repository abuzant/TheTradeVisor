<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Event;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
	api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        //
		    $middleware->alias([
		        'api.key' => \App\Http\Middleware\ValidateApiKey::class,
		        'admin' => \App\Http\Middleware\IsAdmin::class,
			'recaptcha' => \App\Http\Middleware\VerifyRecaptcha::class,
			
		    ]);

        // Add to web middleware group
		        $middleware->web(append: [
		            \App\Http\Middleware\ExtendedRememberMe::class,
		        ]);

    })
    ->withEvents(discover: [
      	  __DIR__.'/../app/Listeners',
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
