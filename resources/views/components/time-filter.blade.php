@props(['periods', 'currentPeriod', 'baseRoute', 'routeParams' => []])

<div class="inline-flex rounded-lg border border-gray-200 bg-white p-1 shadow-sm">
    @foreach($periods as $key => $period)
        @php
            $isActive = $currentPeriod === $key;
            $isLocked = $period['locked'];
            $params = array_merge($routeParams, ['days' => $period['days'] ?: 1]);
            $route = $isLocked ? '#' : route($baseRoute, $params);
        @endphp
        
        @if($isLocked)
            <!-- Locked Period -->
            <button 
                type="button"
                onclick="showUpgradeModal('{{ $period['label'] }}')"
                class="relative px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 text-gray-400 hover:text-gray-600 hover:bg-gray-50 group"
                title="Upgrade required for {{ $period['label'] }} view"
            >
                <span class="flex items-center">
                    {{ $period['label'] }}
                    <svg class="w-3 h-3 ml-1 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                </span>
            </button>
        @else
            <!-- Unlocked Period -->
            <a 
                href="{{ $route }}"
                class="relative px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ $isActive ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100' }}"
            >
                {{ $period['label'] }}
            </a>
        @endif
    @endforeach
</div>

<!-- Upgrade Modal (Hidden by default) -->
<div id="upgradeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 transform transition-all">
        <div class="text-center">
            <!-- Lock Icon -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-amber-100 mb-4">
                <svg class="h-8 w-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            
            <h3 class="text-lg font-semibold text-gray-900 mb-2" id="modalTitle">
                Unlock Extended History
            </h3>
            
            <p class="text-gray-600 mb-6">
                Ask your broker about enterprise access to unlock <strong id="modalPeriod"></strong> of historical data.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <button 
                    onclick="closeUpgradeModal()"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </button>
                <a 
                    href="mailto:hello@thetradevisor.com?subject=Enterprise%20Access%20Inquiry"
                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Learn More
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function showUpgradeModal(period) {
    document.getElementById('modalPeriod').textContent = period;
    document.getElementById('upgradeModal').classList.remove('hidden');
}

function closeUpgradeModal() {
    document.getElementById('upgradeModal').classList.add('hidden');
}

// Close modal on outside click
document.getElementById('upgradeModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeUpgradeModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeUpgradeModal();
    }
});
</script>
