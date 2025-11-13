@props(['broker', 'class' => ''])

@php
    // Trim to first 2 words only to save vertical space
    $words = explode(' ', $broker);
    $displayName = count($words) > 2 
        ? implode(' ', array_slice($words, 0, 2)) . '...' 
        : $broker;
@endphp

<a href="{{ route('broker-details', ['broker' => urlencode($broker)]) }}"
   class="hover:underline {{ $class }}"
   title="{{ $broker }}">
    {{ $displayName }}
</a>
