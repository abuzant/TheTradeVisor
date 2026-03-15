@extends('layouts.app')

@section('styles')
<style>
.security-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.security-header {
    background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.security-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border-left: 4px solid #dc2626;
}

.stat-card.critical {
    border-left-color: #dc2626;
}

.stat-card.warning {
    border-left-color: #f59e0b;
}

.stat-card.info {
    border-left-color: #3b82f6;
}

.stat-card.success {
    border-left-color: #10b981;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
    color: #1e293b;
}

.stat-label {
    color: #475569;
    font-size: 0.9rem;
}

.security-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
    overflow: hidden;
}

.section-header {
    background: #f8fafc;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-direction: row-reverse;
}

.section-title {
    font-weight: 600;
    color: #1e293b;
}

.issue-list {
    padding: 1rem;
}

.issue-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    background: #f8fafc;
    border-left: 4px solid #dc2626;
}

.issue-item.warning {
    border-left-color: #f59e0b;
}

.issue-item.info {
    border-left-color: #3b82f6;
}

.issue-icon {
    margin-right: 1rem;
    font-size: 1.2rem;
}

.issue-details {
    flex: 1;
}

.issue-file {
    font-weight: 600;
    color: #1e293b;
}

.issue-description {
    color: #64748b;
    font-size: 0.9rem;
    margin-top: 0.25rem;
}

.issue-actions {
    display: flex;
    gap: 0.5rem;
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

.btn-danger {
    background: #dc2626;
    color: white;
}

.btn-danger:hover {
    background: #b91c1c;
}

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background: #d97706;
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
}

.log-entry {
    padding: 0.75rem;
    border-radius: 6px;
    margin-bottom: 0.5rem;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 0.85rem;
    background: #f8fafc;
    border-left: 3px solid #e2e8f0;
}

.log-entry.error {
    border-left-color: #dc2626;
    background: #fef2f2;
}

.log-entry.warning {
    border-left-color: #f59e0b;
    background: #fffbeb;
}

.log-timestamp {
    color: #64748b;
    margin-right: 1rem;
}

.log-message {
    color: #1e293b;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.alert-critical {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

.alert-warning {
    background: #fffbeb;
    border: 1px solid #fed7aa;
    color: #92400e;
}

.alert-success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
}

.loading {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #dc2626;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endsection

@section('content')
<div class="security-container">
    <!-- Header -->
    <div class="security-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-2">🔒 Security Audit</h1>
                <p class="mb-0 opacity-90">Monitor and secure your application configuration and access controls</p>
            </div>
            <div>
                <button type="button" class="btn btn-success" onclick="secureConfigs()">
                    <i class="fas fa-shield-alt me-2"></i>Secure Configurations
                </button>
                <button type="button" class="btn btn-warning" onclick="generateReport()">
                    <i class="fas fa-file-alt me-2"></i>Generate Report
                </button>
            </div>
        </div>
    </div>

    @if(isset($configAudit['critical_count']) && $configAudit['critical_count'] > 0)
        <div class="alert alert-critical">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Critical Security Issues Found:</strong> {{ $configAudit['critical_count'] }} critical issues require immediate attention.
        </div>
    @endif

    @if(isset($exposedFiles['total_count']) && $exposedFiles['total_count'] > 0)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Exposed Sensitive Files:</strong> {{ $exposedFiles['total_count'] }} sensitive files found in web-accessible directories.
        </div>
    @endif

    <!-- Security Overview -->
    <div class="security-overview">
        <div class="stat-card critical">
            <div class="stat-number">{{ $configAudit['critical_count'] ?? 0 }}</div>
            <div class="stat-label">Critical Issues</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-number">{{ $configAudit['warning_count'] ?? 0 }}</div>
            <div class="stat-label">Warnings</div>
        </div>
        <div class="stat-card info">
            <div class="stat-number">{{ $exposedFiles['total_count'] ?? 0 }}</div>
            <div class="stat-label">Exposed Files</div>
        </div>
        <div class="stat-card @if(($configAudit['total_issues'] ?? 0) === 0) success @else critical @endif">
            <div class="stat-number">{{ $configAudit['total_issues'] ?? 0 }}</div>
            <div class="stat-label">Total Issues</div>
        </div>
    </div>

    <!-- Configuration Issues -->
    <div class="security-section">
        <div class="section-header">
            <h3 class="section-title">🔧 Configuration Issues</h3>
            <span class="badge bg-danger">{{ $configAudit['total_issues'] ?? 0 }} issues</span>
        </div>
        <div class="issue-list">
            @if(isset($configAudit['issues']) && count($configAudit['issues']) > 0)
                @foreach($configAudit['issues'] as $issue)
                    <div class="issue-item {{ $issue['type'] ?? 'warning' }}">
                        <div class="issue-icon">
                            @if(($issue['type'] ?? 'warning') === 'critical')
                                <i class="fas fa-exclamation-circle text-danger"></i>
                            @else
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                            @endif
                        </div>
                        <div class="issue-details">
                            <div class="issue-file">{{ $issue['file'] ?? 'Unknown' }}</div>
                            <div class="issue-description">
                                <strong>{{ $issue['issue'] ?? 'Unknown issue' }}</strong><br>
                                Current: {{ $issue['current'] ?? 'Unknown' }} | Recommended: {{ $issue['recommended'] ?? 'Unknown' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                    <h5>No Configuration Issues Found</h5>
                    <p>All configuration files are properly secured.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Exposed Files -->
    <div class="security-section">
        <div class="section-header">
            <h3 class="section-title">📁 Exposed Sensitive Files</h3>
            <span class="badge bg-warning">{{ $exposedFiles['total_count'] ?? 0 }} files</span>
        </div>
        <div class="issue-list">
            @if(isset($exposedFiles['exposed_files']) && count($exposedFiles['exposed_files']) > 0)
                @foreach($exposedFiles['exposed_files'] as $file)
                    <div class="issue-item warning">
                        <div class="issue-icon">
                            <i class="fas fa-file-exclamation text-warning"></i>
                        </div>
                        <div class="issue-details">
                            <div class="issue-file">{{ $file['file'] ?? 'Unknown file' }}</div>
                            <div class="issue-description">
                                <strong>Location:</strong> {{ $file['type'] ?? 'Unknown' }} directory<br>
                                <strong>Size:</strong> {{ number_format($file['size'] ?? 0) }} bytes | 
                                <strong>Modified:</strong> {{ date('Y-m-d H:i:s', $file['modified'] ?? time()) }}
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-shield-alt fa-3x mb-3 text-success"></i>
                    <h5>No Exposed Files Found</h5>
                    <p>No sensitive files found in web-accessible directories.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Security Logs -->
    <div class="security-section">
        <div class="section-header">
            <h3 class="section-title">📋 Recent Security Logs</h3>
            <button type="button" class="btn btn-sm btn-secondary" onclick="refreshLogs()">
                <i class="fas fa-sync me-1"></i>Refresh
            </button>
        </div>
        <div class="issue-list" id="securityLogs">
            @if(isset($recentLogs) && count($recentLogs) > 0)
                @foreach($recentLogs as $log)
                    <div class="log-entry {{ $log['level'] ?? 'info' }}">
                        <span class="log-timestamp">{{ $log['timestamp'] ?? now()->toDateTimeString() }}</span>
                        <span class="log-message">{{ $log['message'] ?? 'No message' }}</span>
                    </div>
                @endforeach
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <h5>No Recent Security Logs</h5>
                    <p>No security-related log entries found.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function secureConfigs() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<div class="loading me-2"></div>Securing...';
    button.disabled = true;
    
    fetch('/admin/security/secure-configs', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✅ Configurations secured successfully!', 'success');
            setTimeout(() => location.reload(), 2000);
        } else {
            showNotification('❌ ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('❌ Failed to secure configurations', 'error');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function generateReport() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<div class="loading me-2"></div>Generating...';
    button.disabled = true;
    
    fetch('/admin/security/generate-report', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.summary) {
            // Create downloadable report
            const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'security-audit-' + new Date().toISOString().split('T')[0] + '.json';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
            showNotification('✅ Security report generated!', 'success');
        } else {
            showNotification('❌ Failed to generate report', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('❌ Failed to generate report', 'error');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function refreshLogs() {
    fetch('/admin/security/logs', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const logsContainer = document.getElementById('securityLogs');
            if (data.logs.length > 0) {
                logsContainer.innerHTML = data.logs.map(log => 
                    `<div class="log-entry ${log.level}">
                        <span class="log-timestamp">${log.timestamp}</span>
                        <span class="log-message">${log.message}</span>
                    </div>`
                ).join('');
            } else {
                logsContainer.innerHTML = `
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-info-circle fa-3x mb-3"></i>
                        <h5>No Recent Security Logs</h5>
                        <p>No security-related log entries found.</p>
                    </div>
                `;
            }
            showNotification('✅ Security logs refreshed!', 'success');
        } else {
            showNotification('❌ Failed to refresh logs', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('❌ Failed to refresh logs', 'error');
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'alert-danger' : type === 'success' ? 'alert-success' : 'alert-warning'}`;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    notification.innerHTML = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}

// Auto-refresh logs every 5 minutes
setInterval(refreshLogs, 300000);
</script>
@endsection
