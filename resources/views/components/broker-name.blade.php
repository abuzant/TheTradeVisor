@props(['broker', 'class' => ''])

@php
    $displayName = strlen($broker) > 24 ? substr($broker, 0, 24) . '...' : $broker;
@endphp

<a href="{{ route('broker-details', ['broker' => urlencode($broker)]) }}"
   class="hover:underline {{ $class }}"
   title="{{ $broker }}">
    {{ $displayName }}
</a>
