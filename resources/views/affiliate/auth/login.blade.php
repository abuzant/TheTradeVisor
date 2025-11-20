@extends('layouts.affiliate')

@section('title', 'Affiliate Login')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div>
        <a href="/">
            <span class="text-3xl font-bold text-indigo-600">TheTradeVisor</span>
        </a>
        <p class="text-center text-sm text-gray-600 mt-2">Affiliate Program</p>
    </div>

    <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Affiliate Login</h2>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('affiliate.login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-6">
                <a class="text-sm text-indigo-600 hover:text-indigo-900" href="{{ route('affiliate.register') }}">
                    Don't have an account?
                </a>

                <x-primary-button>
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </form>

        <div class="mt-6 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-600 text-center">
                Already a trader? <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-900">Use your trader account</a>
            </p>
        </div>
    </div>
</div>
@endsection
