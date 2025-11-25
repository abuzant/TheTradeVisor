@extends('layouts.app')

@section('title', 'System Monitoring')

@section('styles')
<style>
.monitoring-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.monitoring-header {
    margin-bottom: 2rem;
}

.monitoring-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.metric-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.metric-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.metric-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.metric-icon {
    width: 20px;
    height: 20px;
    opacity: 0.7;
}

.metric-body {
    padding: 1.5rem;
}

.metric-value {
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0.5rem;
}

.metric-subtitle {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 1rem;
}

.progress-bar-container {
    background: #f3f4f6;
    height: 8px;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.progress-bar-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s ease;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-healthy { background: #d1fae5; color: #065f46; }
.status-warning { background: #fed7aa; color: #92400e; }
.status-critical { background: #fee2e2; color: #991b1b; }

.alert-card {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border: 1px solid #fecaca;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.alert-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    color: #991b1b;
    font-weight: 600;
}

.alert-item {
    background: white;
    border: 1px solid #fecaca;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.alert-icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    margin-top: 0.125rem;
}

.alert-content {
    flex: 1;
}

.alert-title {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.25rem;
}

.alert-message {
    font-size: 0.875rem;
    color: #6b7280;
}

.alert-time {
    font-size: 0.75rem;
    color: #9ca3af;
}

.settings-card {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 2rem;
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-input {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-help {
    font-size: 0.75rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

.btn-primary {
    background: #3b82f6;
    color: white;
    padding: 0.625rem 1.25rem;
    border: none;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
}

.btn-primary:hover {
    background: #2563eb;
}

.metric-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.metric-list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f3f4f6;
    font-size: 0.875rem;
}

.metric-list-item:last-child {
    border-bottom: none;
}

.metric-label {
    color: #6b7280;
}

.metric-value-text {
    font-weight: 500;
    color: #111827;
}

.error-list {
    max-height: 200px;
    overflow-y: auto;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 0.75rem;
}

.error-item {
    font-size: 0.75rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e5e7eb;
}

.error-item:last-child {
    border-bottom: none;
}

.error-time {
    color: #6b7280;
    display: block;
    margin-bottom: 0.25rem;
}

.error-message {
    color: #dc2626;
    font-family: monospace;
}

.refresh-indicator {
    position: fixed;
    top: 1rem;
    right: 1rem;
    background: #3b82f6;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.75rem;
    opacity: 0;
    transition: opacity 0.3s;
    z-index: 1000;
}

.refresh-indicator.show {
    opacity: 1;
}

@media (max-width: 768px) {
    .monitoring-container {
        padding: 1rem;
    }
    
    .monitoring-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .metric-value {
        font-size: 1.5rem;
    }
}
</style>
@endsection

@section('content')
<div class="monitoring-container">
    <!-- Header Section -->
    <div class="monitoring-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">System Monitoring</h1>
                <p class="text-muted mb-0">Real-time system performance and health metrics</p>
            </div>
            <div>
                <span class="status-badge status-healthy">
                    <i class="fas fa-circle me-1"></i> All Systems Operational
                </span>
            </div>
        </div>
    </div>

    <!-- Active Alerts Section -->
    @if(!empty($alerts))
    <div class="alert-card">
        <div class="alert-header">
            <i class="fas fa-exclamation-triangle alert-icon"></i>
            Active System Alerts
        </div>
        @foreach($alerts as $alert)
        <div class="alert-item">
            <i class="fas fa-{{ $alert['severity'] == 'critical' ? 'times-circle' : 'exclamation-circle' }} alert-icon text-{{ $alert['severity'] == 'critical' ? 'danger' : 'warning' }}"></i>
            <div class="alert-content">
                <div class="alert-title">{{ ucfirst(str_replace('_', ' ', $alert['type'])) }}</div>
                <div class="alert-message">{{ $alert['message'] }}</div>
                <div class="alert-time">{{ $alert['timestamp']->format('H:i:s') }}</div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Main Metrics Grid -->
    <div class="monitoring-grid">
        <!-- System Overview -->
        <div class="metric-card">
            <div class="metric-header">
                <div class="metric-title">
                    <i class="fas fa-server metric-icon"></i>
                    System Overview
                </div>
                <span class="status-badge status-healthy">Live</span>
            </div>
            <div class="metric-body">
                <div class="metric-value">{{ $metrics['system']['memory']['percentage'] }}%</div>
                <div class="metric-subtitle">Memory Usage</div>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill {{ $metrics['system']['memory']['percentage'] > 80 ? 'bg-danger' : 'bg-success' }}" 
                         style="width: {{ $metrics['system']['memory']['percentage'] }}%"></div>
                </div>
                <ul class="metric-list">
                    <li class="metric-list-item">
                        <span class="metric-label">Load Average</span>
                        <span class="metric-value-text">{{ $metrics['system']['load_average']['1_min'] }}</span>
                    </li>
                    <li class="metric-list-item">
                        <span class="metric-label">Disk Usage</span>
                        <span class="metric-value-text">{{ $metrics['system']['disk_usage']['percentage'] }}%</span>
                    </li>
                    <li class="metric-list-item">
                        <span class="metric-label">Uptime</span>
                        <span class="metric-value-text">{{ $metrics['application']['uptime'] }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Database Performance -->
        <div class="metric-card">
            <div class="metric-header">
                <div class="metric-title">
                    <i class="fas fa-database metric-icon"></i>
                    Database
                </div>
                <span class="status-badge status-healthy">PostgreSQL</span>
            </div>
            <div class="metric-body">
                <div class="metric-value">{{ $metrics['database']['active_connections'] }}</div>
                <div class="metric-subtitle">Active Connections</div>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill {{ $metrics['database']['connection_percentage'] > 80 ? 'bg-danger' : 'bg-primary' }}" 
                         style="width: {{ $metrics['database']['connection_percentage'] }}%"></div>
                </div>
                <ul class="metric-list">
                    <li class="metric-list-item">
                        <span class="metric-label">Max Connections</span>
                        <span class="metric-value-text">{{ $metrics['database']['max_connections'] }}</span>
                    </li>
                    <li class="metric-list-item">
                        <span class="metric-label">Cache Hit Ratio</span>
                        <span class="metric-value-text">{{ $metrics['database']['cache_hit_ratio'] }}%</span>
                    </li>
                    <li class="metric-list-item">
                        <span class="metric-label">Database Size</span>
                        <span class="metric-value-text">{{ $metrics['database']['database_size'] }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Redis Cache -->
        <div class="metric-card">
            <div class="metric-header">
                <div class="metric-title">
                    <i class="fas fa-memory metric-icon"></i>
                    Redis Cache
                </div>
                <span class="status-badge {{ $metrics['cache']['memory_percentage'] > 80 ? 'status-warning' : 'status-healthy' }}">
                    {{ $metrics['cache']['memory_percentage'] > 80 ? 'Warning' : 'Optimal' }}
                </span>
            </div>
            <div class="metric-body">
                <div class="metric-value">{{ $metrics['cache']['memory_percentage'] }}%</div>
                <div class="metric-subtitle">Memory Usage</div>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill {{ $metrics['cache']['memory_percentage'] > 80 ? 'bg-danger' : 'bg-success' }}" 
                         style="width: {{ $metrics['cache']['memory_percentage'] }}%"></div>
                </div>
                <ul class="metric-list">
                    <li class="metric-list-item">
                        <span class="metric-label">Hit Ratio</span>
                        <span class="metric-value-text">{{ $metrics['cache']['hit_ratio'] }}%</span>
                    </li>
                    <li class="metric-list-item">
                        <span class="metric-label">Memory Used</span>
                        <span class="metric-value-text">{{ $metrics['cache']['memory_used'] }}</span>
                    </li>
                    <li class="metric-list-item">
                        <span class="metric-label">Keys</span>
                        <span class="metric-value-text">{{ number_format($metrics['cache']['keyspace_hits'] + $metrics['cache']['keyspace_misses']) }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Queue Processing -->
        <div class="metric-card">
            <div class="metric-header">
                <div class="metric-title">
                    <i class="fas fa-tasks metric-icon"></i>
                    Queue Processing
                </div>
                <span class="status-badge {{ $metrics['queue']['status'] == 'running' ? 'status-healthy' : 'status-critical' }}">
                    {{ $metrics['queue']['status'] == 'running' ? 'Running' : 'Stopped' }}
                </span>
            </div>
            <div class="metric-body">
                <div class="metric-value">{{ $metrics['queue']['jobs_processed_1h'] }}</div>
                <div class="metric-subtitle">Jobs (Last Hour)</div>
                <ul class="metric-list">
                    <li class="metric-list-item">
                        <span class="metric-label">Failed Jobs</span>
                        <span class="metric-value-text text-danger">{{ $metrics['queue']['failed_jobs_1h'] }}</span>
                    </li>
                    <li class="metric-list-item">
                        <span class="metric-label">Wait Time</span>
                        <span class="metric-value-text">{{ $metrics['queue']['queue_wait_time'] }}s</span>
                    </li>
                    <li class="metric-list-item">
                        <span class="metric-label">Jobs (24h)</span>
                        <span class="metric-value-text">{{ $metrics['queue']['jobs_processed_24h'] }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Error Metrics -->
        <div class="metric-card">
            <div class="metric-header">
                <div class="metric-title">
                    <i class="fas fa-exclamation-triangle metric-icon"></i>
                    Error Metrics
                </div>
                <span class="status-badge {{ $metrics['errors']['error_rate'] > $settings['error_rate_threshold'] ? 'status-warning' : 'status-healthy' }}">
                    {{ $metrics['errors']['error_rate'] > $settings['error_rate_threshold'] ? 'Alert' : 'Normal' }}
                </span>
            </div>
            <div class="metric-body">
                <div class="metric-value">{{ $metrics['errors']['error_rate'] }}</div>
                <div class="metric-subtitle">Error Rate (per 1000)</div>
                <ul class="metric-list">
                    <li class="metric-list-item">
                        <span class="metric-label">Errors (1h)</span>
                        <span class="metric-value-text {{ $metrics['errors']['errors_last_hour'] > 0 ? 'text-danger' : '' }}">{{ $metrics['errors']['errors_last_hour'] }}</span>
                    </li>
                    <li class="metric-list-item">
                        <span class="metric-label">Errors (24h)</span>
                        <span class="metric-value-text {{ $metrics['errors']['errors_last_24h'] > 0 ? 'text-danger' : '' }}">{{ $metrics['errors']['errors_last_24h'] }}</span>
                    </li>
                    <li class="metric-list-item">
                        <span class="metric-label">Threshold</span>
                        <span class="metric-value-text">{{ $settings['error_rate_threshold'] }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Application Info -->
        <div class="metric-card">
            <div class="metric-header">
                <div class="metric-title">
                    <i class="fas fa-info-circle metric-icon"></i>
                    Application
                </div>
                <span class="status-badge status-healthy">{{ $metrics['application']['environment'] }}</span>
            </div>
            <div class="metric-body">
                <div class="metric-value">Laravel</div>
                <div class="metric-subtitle">{{ $metrics['application']['laravel_version'] }}</div>
                <ul class="metric-list">
                    <li class="metric-list-item">
                        <span class="metric-label">Memory Usage</span>
                        <span class="metric-value-text">{{ $metrics['application']['memory_usage']['current'] }}</span>
                    </li>
                    <li class="metric-list-item">
                        <span class="metric-label">Peak Memory</span>
                        <span class="metric-value-text">{{ $metrics['application']['memory_usage']['peak'] }}</span>
                    </li>
                    <li class="metric-list-item">
                        <span class="metric-label">New Relic</span>
                        <span class="metric-value-text text-success">Active</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Recent Errors Section -->
    @if(!empty($metrics['errors']['recent_errors']))
    <div class="metric-card mb-4">
        <div class="metric-header">
            <div class="metric-title">
                <i class="fas fa-bug metric-icon"></i>
                Recent Errors
            </div>
            <span class="status-badge status-warning">Last Hour</span>
        </div>
        <div class="metric-body">
            <div class="error-list">
                @foreach($metrics['errors']['recent_errors'] as $error)
                <div class="error-item">
                    <span class="error-time">{{ $error['timestamp'] }}</span>
                    <span class="error-message">{{ $error['message'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Alert Settings Section -->
    <div class="settings-card">
        <h3 class="h5 mb-4">
            <i class="fas fa-cog me-2"></i>Alert Configuration
        </h3>
        <form action="{{ route('admin.monitoring.update-settings') }}" method="POST">
            @csrf
            <div class="settings-grid">
                <div class="form-group">
                    <label for="error_rate_threshold" class="form-label">
                        Error Rate Threshold (%)
                    </label>
                    <input type="number" class="form-input" id="error_rate_threshold" name="error_rate_threshold" 
                           value="{{ $settings['error_rate_threshold'] }}" step="0.1" min="0" max="100">
                    <div class="form-help">Alert when error rate exceeds this percentage</div>
                </div>
                
                <div class="form-group">
                    <label for="memory_threshold" class="form-label">
                        Memory Threshold (%)
                    </label>
                    <input type="number" class="form-input" id="memory_threshold" name="memory_threshold" 
                           value="{{ $settings['memory_threshold'] }}" step="1" min="0" max="100">
                    <div class="form-help">Alert when Redis memory usage exceeds this</div>
                </div>
                
                <div class="form-group">
                    <label for="queue_wait_threshold" class="form-label">
                        Queue Wait Threshold (seconds)
                    </label>
                    <input type="number" class="form-input" id="queue_wait_threshold" name="queue_wait_threshold" 
                           value="{{ $settings['queue_wait_threshold'] }}" step="1" min="0" max="300">
                    <div class="form-help">Alert when queue wait time exceeds this</div>
                </div>
                
                <div class="form-group">
                    <label for="alert_channel" class="form-label">
                        Alert Channel
                    </label>
                    <select class="form-input" id="alert_channel" name="alert_channel" onchange="toggleNotificationOptions()">
                        <option value="slack" {{ ($settings['alert_channel'] ?? 'email') == 'slack' ? 'selected' : '' }}>
                            📱 Slack Webhook
                        </option>
                        <option value="email" {{ ($settings['alert_channel'] ?? 'email') == 'email' ? 'selected' : '' }}>
                            📧 Email Alert
                        </option>
                        <option value="none" {{ ($settings['alert_channel'] ?? 'email') == 'none' ? 'selected' : '' }}>
                            🔄 Support Email (Fallback)
                        </option>
                    </select>
                    <div class="form-help">
                        Choose where alerts should be sent. Slack requires webhook URL in .env
                    </div>
                </div>
                
                <div class="form-group" id="email_settings">
                    <label class="form-label">
                        Email Notifications
                    </label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" 
                               value="1" {{ $settings['email_notifications'] ? 'checked' : '' }}>
                        <label class="form-check-label" for="email_notifications">
                            Enable email alerts
                        </label>
                    </div>
                    <div class="form-help">
                        @if(config('monitoring.slack_webhook_url'))
                            Slack webhook configured - will use Slack when selected
                        @elseif(config('monitoring.alert_email'))
                            Alerts will go to: {{ config('monitoring.alert_email') }}
                        @else
                            Fallback to: {{ config('monitoring.support_email') }}
                        @endif
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn-primary">
                <i class="fas fa-save me-2"></i>Update Settings
            </button>
        </form>
    </div>
</div>

<!-- Refresh Indicator -->
<div class="refresh-indicator" id="refreshIndicator">
    <i class="fas fa-sync-alt me-2"></i>Refreshing...
</div>

<script>
// Auto-refresh every 30 seconds
let refreshInterval;
let refreshIndicator = document.getElementById('refreshIndicator');

function showRefreshIndicator() {
    refreshIndicator.classList.add('show');
    setTimeout(() => {
        refreshIndicator.classList.remove('show');
    }, 2000);
}

function refreshPage() {
    showRefreshIndicator();
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

// Toggle notification options based on alert channel
function toggleNotificationOptions() {
    const alertChannel = document.getElementById('alert_channel').value;
    const emailSettings = document.getElementById('email_settings');
    
    if (alertChannel === 'slack') {
        emailSettings.style.opacity = '0.5';
        emailSettings.style.pointerEvents = 'none';
        document.getElementById('email_notifications').checked = false;
    } else {
        emailSettings.style.opacity = '1';
        emailSettings.style.pointerEvents = 'auto';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleNotificationOptions();
});

// Start auto-refresh
refreshInterval = setInterval(refreshPage, 30000);

// Clear interval when page is hidden (user switched tabs)
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        clearInterval(refreshInterval);
    } else {
        refreshInterval = setInterval(refreshPage, 30000);
    }
});
</script>
@endsection
