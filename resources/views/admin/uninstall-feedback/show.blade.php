<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Feedback Details #{{ $feedback->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('admin.uninstall-feedback.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                    ← Back to Feedback List
                </a>
            </div>

            <!-- Feedback Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        <!-- Left Column - Basic Info -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Feedback Information</h3>
                                
                                <div class="space-y-3">
                                    <div class="flex justify-between py-2 border-b">
                                        <span class="text-sm font-medium text-gray-500">Feedback ID</span>
                                        <span class="text-sm text-gray-900">#{{ $feedback->id }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between py-2 border-b">
                                        <span class="text-sm font-medium text-gray-500">Submitted At</span>
                                        <span class="text-sm text-gray-900">{{ $feedback->submitted_at->format('M j, Y H:i:s') }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between py-2 border-b">
                                        <span class="text-sm font-medium text-gray-500">Reason</span>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $feedback->reason }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex justify-between py-2 border-b">
                                        <span class="text-sm font-medium text-gray-500">Experience Rating</span>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($feedback->experience_rating == 'excellent') bg-green-100 text-green-800
                                            @elseif($feedback->experience_rating == 'good') bg-blue-100 text-blue-800
                                            @elseif($feedback->experience_rating == 'average') bg-yellow-100 text-yellow-800
                                            @elseif($feedback->experience_rating == 'poor') bg-orange-100 text-orange-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ $feedback->experience_rating }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex justify-between py-2 border-b">
                                        <span class="text-sm font-medium text-gray-500">Would Return</span>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($feedback->would_return == 'yes') bg-green-100 text-green-800
                                            @elseif($feedback->would_return == 'maybe') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ $feedback->would_return }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Contact Info -->
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
                                
                                <div class="space-y-3">
                                    <div class="py-2 border-b">
                                        <span class="text-sm font-medium text-gray-500 block mb-1">Email</span>
                                        <span class="text-sm text-gray-900">
                                            {{ $feedback->email ?: '<span class="text-gray-400 italic">Not provided</span>' }}
                                        </span>
                                    </div>
                                    
                                    <div class="py-2 border-b">
                                        <span class="text-sm font-medium text-gray-500 block mb-1">Comments</span>
                                        <p class="text-sm text-gray-900 whitespace-pre-wrap">
                                            {{ $feedback->comments ?: '<span class="text-gray-400 italic">No comments provided</span>' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Technical Details -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Technical Details</h3>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <div class="py-2 border-b">
                                    <span class="text-sm font-medium text-gray-500 block mb-1">IP Address</span>
                                    <span class="text-sm text-gray-900 font-mono">{{ $feedback->ip_address }}</span>
                                </div>
                                
                                <div class="py-2 border-b">
                                    <span class="text-sm font-medium text-gray-500 block mb-1">Referer</span>
                                    <span class="text-sm text-gray-900">
                                        {{ $feedback->referer ?: '<span class="text-gray-400 italic">Direct visit</span>' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <div class="py-2 border-b">
                                    <span class="text-sm font-medium text-gray-500 block mb-1">User Agent</span>
                                    <p class="text-sm text-gray-900 font-mono break-all">{{ $feedback->user_agent }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex space-x-4">
                            @if($feedback->email)
                                <a href="mailto:{{ $feedback->email }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                                    Send Email
                                </a>
                            @endif
                            
                            <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-700">
                                Print Details
                            </button>
                            
                            <form method="POST" action="{{ route('admin.uninstall-feedback.export') }}" class="inline">
                                @csrf
                                <input type="hidden" name="single_id" value="{{ $feedback->id }}">
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700">
                                    Export Single Entry
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Similar Feedback -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Similar Feedback</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Experience</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Would Return</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $similarFeedback = \App\Models\UninstallFeedback::where('reason', $feedback->reason)
                                        ->where('id', '!=', $feedback->id)
                                        ->orderBy('submitted_at', 'desc')
                                        ->limit(5)
                                        ->get();
                                @endphp
                                
                                @if($similarFeedback->count() > 0)
                                    @foreach($similarFeedback as $similar)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $similar->submitted_at->format('M j, Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ $similar->reason }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($similar->experience_rating == 'excellent') bg-green-100 text-green-800
                                                    @elseif($similar->experience_rating == 'good') bg-blue-100 text-blue-800
                                                    @elseif($similar->experience_rating == 'average') bg-yellow-100 text-yellow-800
                                                    @elseif($similar->experience_rating == 'poor') bg-orange-100 text-orange-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    {{ $similar->experience_rating }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($similar->would_return == 'yes') bg-green-100 text-green-800
                                                    @elseif($similar->would_return == 'maybe') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    {{ $similar->would_return }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('admin.uninstall-feedback.show', $similar->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    View Details
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No similar feedback found for this reason.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
