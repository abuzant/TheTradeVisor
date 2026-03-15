@props(['maxDrawdown', 'equity', 'currency'])

@php
    $severity = 'green';
    $severityText = 'Excellent';
    $severityIcon = '✅';
    
    if ($maxDrawdown > 30) {
        $severity = 'red';
        $severityText = 'Critical';
        $severityIcon = '🔴';
    } elseif ($maxDrawdown > 20) {
        $severity = 'orange';
        $severityText = 'High Risk';
        $severityIcon = '🟠';
    } elseif ($maxDrawdown > 10) {
        $severity = 'yellow';
        $severityText = 'Moderate';
        $severityIcon = '🟡';
    }
    
    $gaugeRotation = min($maxDrawdown * 1.8, 180); // 0-180 degrees
@endphp

<div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-card p-6">
    <div class="mb-4">
        <h3 class="text-lg font-bold text-gray-900">📉 Maximum Drawdown</h3>
        <p class="text-sm text-gray-600">Worst equity drop from peak</p>
    </div>

    <div class="flex flex-col items-center justify-center py-6">
        {{-- Gauge Visualization --}}
        <div class="relative w-48 h-24 mb-4">
            {{-- Background Arc --}}
            <svg class="w-full h-full" viewBox="0 0 200 100">
                {{-- Green Zone (0-10%) --}}
                <path d="M 20 80 A 80 80 0 0 1 56 20" 
                      fill="none" 
                      stroke="#10b981" 
                      stroke-width="12" 
                      stroke-linecap="round"/>
                
                {{-- Yellow Zone (10-20%) --}}
                <path d="M 56 20 A 80 80 0 0 1 100 10" 
                      fill="none" 
                      stroke="#fbbf24" 
                      stroke-width="12" 
                      stroke-linecap="round"/>
                
                {{-- Orange Zone (20-30%) --}}
                <path d="M 100 10 A 80 80 0 0 1 144 20" 
                      fill="none" 
                      stroke="#f97316" 
                      stroke-width="12" 
                      stroke-linecap="round"/>
                
                {{-- Red Zone (30%+) --}}
                <path d="M 144 20 A 80 80 0 0 1 180 80" 
                      fill="none" 
                      stroke="#ef4444" 
                      stroke-width="12" 
                      stroke-linecap="round"/>
                
                {{-- Needle --}}
                <g transform="translate(100, 80) rotate({{ $gaugeRotation - 90 }})">
                    <line x1="0" y1="0" x2="0" y2="-60" 
                          stroke="#1f2937" 
                          stroke-width="3" 
                          stroke-linecap="round"/>
                    <circle cx="0" cy="0" r="5" fill="#1f2937"/>
                </g>
            </svg>
        </div>

        {{-- Percentage Display --}}
        <div class="text-center mb-4">
            <div class="text-5xl font-bold text-{{ $severity }}-600 mb-2">
                {{ number_format($maxDrawdown, 2) }}%
            </div>
            <div class="text-sm font-semibold text-{{ $severity }}-600">
                {{ $severityIcon }} {{ $severityText }}
            </div>
        </div>

        {{-- Equity Stats --}}
        <div class="w-full grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
            <div class="text-center">
                <div class="text-xs text-gray-500 mb-1">Peak Equity</div>
                <div class="text-sm font-bold text-gray-900">
                    {{ number_format($equity['max'], 2) }}
                </div>
                <div class="text-xs text-gray-500">{{ $currency }}</div>
            </div>
            <div class="text-center">
                <div class="text-xs text-gray-500 mb-1">Lowest Equity</div>
                <div class="text-sm font-bold text-gray-900">
                    {{ number_format($equity['min'], 2) }}
                </div>
                <div class="text-xs text-gray-500">{{ $currency }}</div>
            </div>
        </div>
    </div>

    {{-- Info --}}
    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
        <p class="text-xs text-gray-600">
            <strong>Max Drawdown</strong> measures the largest peak-to-trough decline in equity. 
            Lower is better. Professional traders typically keep it under 20%.
        </p>
    </div>
</div>
