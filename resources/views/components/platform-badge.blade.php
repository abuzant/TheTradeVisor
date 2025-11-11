@props(['account'])

@if($account->platform_type)
    <div class="inline-flex items-center space-x-1">
        {{-- Platform Type Badge --}}
        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $account->platform_type === 'MT5' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
            {{ $account->platform_type }}
        </span>
        
        {{-- Account Mode Badge (only for MT5) --}}
        @if($account->platform_type === 'MT5' && $account->account_mode)
            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold {{ $account->account_mode === 'netting' ? 'bg-purple-200 text-purple-900' : 'bg-blue-200 text-blue-900' }}" title="{{ ucfirst($account->account_mode) }} Mode">
                {{ strtoupper(substr($account->account_mode, 0, 1)) }}
            </span>
        @endif
    </div>
@else
    {{-- Unknown platform --}}
    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-600" title="Platform not detected yet">
        ?
    </span>
@endif
