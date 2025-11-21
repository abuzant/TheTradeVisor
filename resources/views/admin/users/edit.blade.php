<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit User: {{ $user->name }}
            </h2>
            <a href="{{ route('admin.users.show', $user) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                ← Back to User
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Edit Form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">User Information</h3>

                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">

                            {{-- Name --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       value="{{ old('name', $user->name) }}"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email"
                                       name="email"
                                       id="email"
                                       value="{{ old('email', $user->email) }}"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Account Status <span class="text-red-500">*</span>
                                </label>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input type="radio"
                                               name="is_active"
                                               id="is_active_true"
                                               value="1"
                                               {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                        <label for="is_active_true" class="ml-3 block text-sm font-medium text-gray-700">
                                            Active
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="radio"
                                               name="is_active"
                                               id="is_active_false"
                                               value="0"
                                               {{ old('is_active', $user->is_active) ? '' : 'checked' }}
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                        <label for="is_active_false" class="ml-3 block text-sm font-medium text-gray-700">
                                            Suspended
                                        </label>
                                    </div>
                                </div>
                                @error('is_active')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Admin Status --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Admin Privileges <span class="text-red-500">*</span>
                                </label>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input type="radio"
                                               name="is_admin"
                                               id="is_admin_true"
                                               value="1"
                                               {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                        <label for="is_admin_true" class="ml-3 block text-sm font-medium text-gray-700">
                                            Yes 👨‍💼 (Full admin access)
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="radio"
                                               name="is_admin"
                                               id="is_admin_false"
                                               value="0"
                                               {{ old('is_admin', $user->is_admin) ? '' : 'checked' }}
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                        <label for="is_admin_false" class="ml-3 block text-sm font-medium text-gray-700">
                                            No (Regular user)
                                        </label>
                                    </div>
                                </div>
                                @error('is_admin')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Submit Buttons --}}
                            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium">
                                    Cancel
                                </a>
                                <button type="submit"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">
                                    Save Changes
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="bg-red-50 border border-red-200 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-red-900 mb-2">Danger Zone</h3>
                    <p class="text-sm text-red-700 mb-4">These actions are irreversible. Please be careful.</p>

                    <div class="space-y-3">
                        {{-- Delete Account --}}
                        @if($user->id !== auth()->id())
                        <div class="flex items-center justify-between p-4 bg-white rounded-md">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900">Delete User Account</h4>
                                <p class="text-xs text-gray-500">Permanently delete this user and all associated data</p>
                            </div>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Are you absolutely sure? This will permanently delete the user and ALL their trading data. This action CANNOT be undone!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium">
                                    Delete User
                                </button>
                            </form>
                        </div>
                        @else
                        <div class="flex items-center justify-between p-4 bg-white rounded-md opacity-50">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900">Delete User Account</h4>
                                <p class="text-xs text-gray-500">You cannot delete your own account</p>
                            </div>
                            <button class="px-3 py-1 bg-gray-400 text-white rounded-md text-sm font-medium cursor-not-allowed" disabled>
                                Delete User
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
