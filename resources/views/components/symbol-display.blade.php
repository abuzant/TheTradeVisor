@props(['symbol', 'normalized', 'link' => null])

@php
    $showRaw = $symbol !== $normalized;
@endphp

@if($link)
    <a href="{{ $link }}" class="text-indigo-600 hover:text-indigo-900" title="Raw: {{ $symbol }}">
        <span class="font-medium">{{ $normalized }}</span>
        @if($showRaw)
            <span class="text-xs text-gray-400 ml-1">({{ $symbol }})</span>
        @endif
    </a>
@else
    <span title="Raw: {{ $symbol }}">
        <span class="font-medium">{{ $normalized }}</span>
        @if($showRaw)
            <span class="text-xs text-gray-400 ml-1">({{ $symbol }})</span>
        @endif
    </span>
@endif
