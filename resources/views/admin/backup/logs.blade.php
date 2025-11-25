@extends('layouts.app')

@section('styles')
<style>
.logs-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.logs-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.logs-controls {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.log-type-selector {
    display: flex;
    gap: 0.5rem;
}

.type-btn {
    padding: 0.5rem 1rem;
    border: 2px solid #e2e8f0;
    background: white;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-weight: 500;
}

.type-btn.active {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.type-btn:hover:not(.active) {
    border-color: #667eea;
    color: #667eea;
}

.search-box {
    flex: 1;
    min-width: 200px;
    position: relative;
}

.search-input {
    width: 100%;
    padding: 0.5rem 1rem 0.5rem 2.5rem;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.9rem;
    transition: border-color 0.2s ease;
}

.search-input:focus {
    outline: none;
    border-color: #667eea;
}

.search-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
}

.logs-viewer {
    background: #1e293b;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    overflow: hidden;
}

.logs-header-bar {
    background: #334155;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #475569;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logs-title {
    color: #f1f5f9;
    font-weight: 600;
}

.logs-stats {
    color: #94a3b8;
    font-size: 0.9rem;
}

.logs-content {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 0.85rem;
    line-height: 1.5;
    max-height: 600px;
    overflow-y: auto;
    padding: 1rem;
}

.log-entry {
    margin-bottom: 0.5rem;
    padding: 0.5rem;
    border-radius: 4px;
    transition: background-color 0.2s ease;
}

.log-entry:hover {
    background: rgba(255,255,255,0.05);
}

.log-timestamp {
    color: #64748b;
    margin-right: 1rem;
}

.log-message {
    color: #f1f5f9;
}

.log-level-success {
    color: #10b981;
}

.log-level-error {
    color: #ef4444;
}

.log-level-warning {
    color: #f59e0b;
}

.log-level-info {
    color: #3b82f6;
}

.empty-logs {
    text-align: center;
    padding: 3rem;
    color: #64748b;
}

.empty-logs-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-secondary {
    background: #64748b;
    color: white;
}

.btn-secondary:hover {
    background: #475569;
}

.highlight {
    background: rgba(251, 191, 36, 0.3);
    padding: 0.1rem 0.2rem;
    border-radius: 2px;
}

/* Scrollbar styling */
.logs-content::-webkit-scrollbar {
    width: 8px;
}

.logs-content::-webkit-scrollbar-track {
    background: #334155;
}

.logs-content::-webkit-scrollbar-thumb {
    background: #64748b;
    border-radius: 4px;
}

.logs-content::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endsection

@section('content')
<div class="logs-container">
    <!-- Header -->
    <div class="logs-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-2">📋 Backup Logs</h1>
                <p class="mb-0 opacity-90">View backup system logs for the past 7 days</p>
            </div>
            <div>
                <a href="{{ route('admin.backup.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Manager
                </a>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <div class="logs-controls">
        <div class="log-type-selector">
            <button type="button" class="type-btn {{ $logType === 'database' ? 'active' : '' }}" 
                    onclick="switchLogType('database')">
                🗄️ Database
            </button>
            <button type="button" class="type-btn {{ $logType === 'application' ? 'active' : '' }}" 
                    onclick="switchLogType('application')">
                📁 Application
            </button>
        </div>
        
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search logs..." id="searchInput" onkeyup="searchLogs()">
        </div>
        
        <button type="button" class="btn btn-secondary" onclick="refreshLogs()">
            <i class="fas fa-sync me-2"></i>Refresh
        </button>
    </div>

    <!-- Logs Viewer -->
    <div class="logs-viewer">
        <div class="logs-header-bar">
            <div class="logs-title">
                @if($logType === 'database')
                    🗄️ Database Backup Logs
                @else
                    📁 Application Backup Logs
                @endif
            </div>
            <div class="logs-stats">
                {{ count($logs) }} entries • Past 7 days
            </div>
        </div>
        
        <div class="logs-content" id="logsContent">
            @if(count($logs) > 0)
                @foreach($logs as $log)
                    <div class="log-entry" data-level="{{ $log['level'] }}">
                        <span class="log-timestamp">{{ $log['datetime'] }}</span>
                        <span class="log-message log-level-{{ $log['level'] }}">
                            {{ $log['message'] }}
                        </span>
                    </div>
                @endforeach
            @else
                <div class="empty-logs">
                    <div class="empty-logs-icon">📋</div>
                    <h5>No Logs Found</h5>
                    <p>No backup logs available for the selected period.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
let currentLogType = '{{ $logType }}';

function switchLogType(type) {
    window.location.href = `{{ route('admin.backup.logs') }}?type=${type}`;
}

function refreshLogs() {
    window.location.reload();
}

function searchLogs() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const logEntries = document.querySelectorAll('.log-entry');
    
    logEntries.forEach(entry => {
        const message = entry.querySelector('.log-message').textContent.toLowerCase();
        
        if (searchTerm === '' || message.includes(searchTerm)) {
            entry.style.display = 'block';
            
            // Highlight search terms
            if (searchTerm !== '') {
                const messageElement = entry.querySelector('.log-message');
                const originalText = messageElement.textContent;
                const regex = new RegExp(`(${searchTerm})`, 'gi');
                messageElement.innerHTML = originalText.replace(regex, '<span class="highlight">$1</span>');
            } else {
                // Remove highlights
                const messageElement = entry.querySelector('.log-message');
                messageElement.innerHTML = messageElement.textContent;
            }
        } else {
            entry.style.display = 'none';
        }
    });
    
    // Update stats
    const visibleEntries = document.querySelectorAll('.log-entry[style="display: block"], .log-entry:not([style*="display: none"])').length;
    document.querySelector('.logs-stats').textContent = `${visibleEntries} entries shown • Past 7 days`;
}

// Auto-refresh every 30 seconds
setInterval(() => {
    if (document.getElementById('searchInput').value === '') {
        refreshLogs();
    }
}, 30000);

// Focus search input with Ctrl+F
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        document.getElementById('searchInput').focus();
    }
});
</script>

@php
// Helper function to highlight search terms (will be used in JavaScript)
function highlightSearchTerms($text) {
    return $text;
}
@endphp
@endsection
