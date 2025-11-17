<x-app-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
            {{ __('My Digest') }}
        </h1>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/90 backdrop-blur-sm overflow-hidden shadow-card rounded-xl">
                <div class="p-6">
                    <p class="text-sm text-gray-500 mb-6">Digest for {{ $today }}</p>

                    @if($htmlContent)
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            {!! $htmlContent !!}
                        </div>
                    @else
                        <div class="py-10 text-center text-gray-500">
                            <p class="text-sm">No digest data for today yet.</p>
                        </div>
                    @endif

                    <p class="mt-6 text-xs text-gray-400">This page shows today’s generated digest. Once the digest sending routine is active, you’ll receive this via email based on your profile preferences.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
