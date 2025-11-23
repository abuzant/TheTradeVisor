<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 sm:rounded">
            <div class="bg-white overflow-hidden sm:rounded-lg">
                <div class="p-6 text-gray-900 sm:p-8">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>
    </div>


    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 sm:rounded">
            <div class="bg-white overflow-hidden sm:rounded-lg">
                <div class="p-6 text-gray-900 sm:p-8">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>


    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 sm:rounded">
            <div class="bg-white overflow-hidden sm:rounded-lg">
                <div class="p-6 text-gray-900 sm:p-8">
                    @include('profile.partials.update-public-profile-form')
                </div>
            </div>
        </div>
    </div>


    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 sm:rounded">
            <div class="bg-white overflow-hidden sm:rounded-lg">
                <div class="p-6 text-gray-900 sm:p-8">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>



    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 sm:rounded">
            <div class="bg-white overflow-hidden sm:rounded-lg">
                <div class="p-6 text-gray-900 sm:p-8">
                    @include('profile.partials.update-digest-preferences-form')
                </div>
            </div>
        </div>
    </div>


</x-app-layout>
