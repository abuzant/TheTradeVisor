@props(['column', 'label', 'sortBy' => null, 'sortDirection' => 'asc', 'sortByParam' => 'sort_by', 'sortDirectionParam' => 'sort_direction'])

@php
    $currentlySorting = $sortBy === $column;
    $newDirection = $currentlySorting && $sortDirection === 'asc' ? 'desc' : 'asc';

    // Get current URL and add/update sort parameters
    $queryParams = request()->except([$sortByParam, $sortDirectionParam]);
    $queryParams[$sortByParam] = $column;
    $queryParams[$sortDirectionParam] = $newDirection;

    $url = request()->url() . '?' . http_build_query($queryParams);
@endphp

<th {{ $attributes->merge(['class' => 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider']) }}>
    <a href="{{ $url }}"
       class="group inline-flex items-center space-x-1 hover:text-gray-900">
        <span>{{ $label }}</span>

        @if($currentlySorting)
            @if($sortDirection === 'asc')
                <svg class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
            @else
                <svg class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            @endif
        @else
            <svg class="w-4 h-4 text-gray-400 opacity-0 group-hover:opacity-100 transition" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        @endif
    </a>
</th>
