<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900">
                    @include('profile.partials.update-profile-information-form')
                </div>
                <hr />

                <div class="p-6 text-gray-900">
                    @include('profile.partials.update-password-form')
                </div>
                <hr />

                <div class="p-6 text-gray-900">
                    @include('profile.partials.update-public-profile-form')
                </div>
                <hr />

                <div class="p-6 text-gray-900">
                    TEST 4
                </div>
                <hr />

                <div class="p-6 text-gray-900">
                    TEST 5
                </div>
                <hr />

                <div class="p-6 text-gray-900">
                    TEST 6
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
