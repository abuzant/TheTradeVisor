@props(['route' => null, 'startDate' => null, 'endDate' => null])

<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" action="{{ $route ?? request()->url() }}" class="flex flex-wrap items-end gap-4">
        <!-- Preserve existing query parameters -->
        @foreach(request()->except(['start_date', 'end_date', 'page']) as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach

        <div class="flex-1 min-w-[200px]">
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                Start Date
            </label>
            <input
                type="date"
                id="start_date"
                name="start_date"
                value="{{ $startDate ?? request('start_date') }}"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
        </div>

        <div class="flex-1 min-w-[200px]">
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                End Date
            </label>
            <input
                type="date"
                id="end_date"
                name="end_date"
                value="{{ $endDate ?? request('end_date') }}"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
        </div>

        <div class="flex gap-2">
            <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter
            </button>

            @if(request('start_date') || request('end_date'))
                <a
                    href="{{ request()->url() }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear
                </a>
            @endif
        </div>

        {{ $slot }}
    </form>
</div>
