@extends('layouts.app')

@section('styles')
<style>
.backup-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.backup-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.backup-stats {
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
    border-left: 4px solid #667eea;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #667eea;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #64748b;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.backup-jobs {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    overflow: hidden;
    margin-bottom: 2rem;
}

.jobs-header {
    background: #f8fafc;
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.jobs-list {
    padding: 1rem;
}

.job-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    background: #f8fafc;
    transition: background-color 0.2s ease;
}

.job-item:hover {
    background: #f1f5f9;
}

.job-status {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 1rem;
}

.job-status.success {
    background: #10b981;
}

.job-status.failed {
    background: #ef4444;
}

.job-details {
    flex: 1;
}

.job-type {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.job-time {
    font-size: 0.85rem;
    color: #64748b;
}

.backup-files {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
}

.file-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    overflow: hidden;
}

.section-header {
    background: #f8fafc;
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
}

.file-list {
    max-height: 400px;
    overflow-y: auto;
}

.file-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
    transition: background-color 0.2s ease;
}

.file-item:hover {
    background: #f8fafc;
}

.file-item:last-child {
    border-bottom: none;
}

.file-info {
    flex: 1;
}

.file-name {
    font-weight: 500;
    color: #1e293b;
    margin-bottom: 0.25rem;
    word-break: break-all;
}

.file-meta {
    font-size: 0.85rem;
    color: #64748b;
}

.file-actions {
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

.btn-primary {
    background: #667eea;
    color: white;
}

.btn-primary:hover {
    background: #5a67d8;
    transform: translateY(-1px);
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
}

.btn-secondary {
    background: #64748b;
    color: white;
}

.btn-secondary:hover {
    background: #475569;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.85rem;
}

.backup-actions {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.loading {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #64748b;
}

.empty-state-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}
</style>
@endsection

@section('content')
<div class="backup-container">
    <!-- Header -->
    <div class="backup-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-2">🛡️ Backup Manager</h1>
                <p class="mb-0 opacity-90">Monitor and manage your automated backup system</p>
            </div>
            <div>
                <a href="{{ route('admin.backup.logs') }}" class="btn btn-secondary">
                    <i class="fas fa-file-alt me-2"></i>View Logs
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <!-- Storage Statistics -->
    <div class="backup-stats">
        <div class="stat-card">
            <div class="stat-number">{{ $storageStats['database']['file_count'] }}</div>
            <div class="stat-label">Database Backups</div>
            <div class="text-muted small mt-2">{{ $storageStats['database']['total_size'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $storageStats['application']['file_count'] }}</div>
            <div class="stat-label">Application Backups</div>
            <div class="text-muted small mt-2">{{ $storageStats['application']['total_size'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $storageStats['total']['file_count'] }}</div>
            <div class="stat-label">Total Files</div>
            <div class="text-muted small mt-2">{{ $storageStats['total']['total_size'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">7</div>
            <div class="stat-label">Day Retention</div>
            <div class="text-muted small mt-2">Automatic cleanup</div>
        </div>
    </div>

    <!-- Manual Backup Actions -->
    <div class="backup-actions">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Backup Information:</strong> Backups are automatically performed daily via cron jobs.
            <br>Last backup times and status are shown in the "Recent Backup Jobs" section below.
        </div>
        <button type="button" class="btn btn-secondary" onclick="location.reload()">
            <i class="fas fa-sync me-2"></i>Refresh Status
        </button>
    </div>

    <!-- Recent Backup Jobs -->
    <div class="backup-jobs">
        <div class="jobs-header">
            <h3 class="h5 mb-0">📊 Recent Backup Jobs (Last 7)</h3>
            <span class="badge bg-secondary">{{ count($recentJobs) }} jobs</span>
        </div>
        <div class="jobs-list">
            @if(count($recentJobs) > 0)
                @foreach($recentJobs as $job)
                    <div class="job-item">
                        <div class="job-status {{ $job['status'] }}"></div>
                        <div class="job-details">
                            <div class="job-type">{{ $job['type'] }} Backup</div>
                            <div class="job-time">{{ $job['timestamp'] ? date('M j, Y H:i:s', $job['timestamp']) : 'Unknown time' }}</div>
                        </div>
                        <div class="job-message">
                            @if($job['status'] === 'success')
                                <span class="text-success">✅ {{ $job['message'] }}</span>
                            @else
                                <span class="text-danger">❌ {{ $job['message'] }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">📋</div>
                    <h5>No Recent Backup Jobs</h5>
                    <p>Backup jobs will appear here once automated backups run.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Backup Files -->
    <div class="backup-files">
        <!-- Database Backups -->
        <div class="file-section">
            <div class="section-header">
                <h4 class="section-title">🗄️ Database Backups</h4>
                <span class="badge bg-info">{{ count($backupData['database']) }} files</span>
            </div>
            <div class="file-list">
                @if(count($backupData['database']) > 0)
                    @foreach($backupData['database'] as $file)
                        <div class="file-item">
                            <div class="file-info">
                                <div class="file-name">{{ $file['filename'] }}</div>
                                <div class="file-meta">{{ $file['size'] }} • {{ $file['created_at'] }}</div>
                            </div>
                            <div class="file-actions">
                                <a href="{{ route('admin.backup.download', ['type' => 'database', 'file' => $file['filename']]) }}" 
                                   class="btn btn-sm btn-secondary">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">🗄️</div>
                        <h6>No Database Backups</h6>
                        <p class="small">Database backups will appear here.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Application Backups -->
        <div class="file-section">
            <div class="section-header">
                <h4 class="section-title">📁 Application Backups</h4>
                <span class="badge bg-info">{{ count($backupData['application']) }} files</span>
            </div>
            <div class="file-list">
                @if(count($backupData['application']) > 0)
                    @foreach($backupData['application'] as $file)
                        <div class="file-item">
                            <div class="file-info">
                                <div class="file-name">{{ $file['filename'] }}</div>
                                <div class="file-meta">{{ $file['size'] }} • {{ $file['created_at'] }}</div>
                            </div>
                            <div class="file-actions">
                                <a href="{{ route('admin.backup.download', ['type' => 'application', 'file' => $file['filename']]) }}" 
                                   class="btn btn-sm btn-secondary">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">📁</div>
                        <h6>No Application Backups</h6>
                        <p class="small">Application backups will appear here.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Auto-refresh every 5 minutes
setInterval(() => {
    location.reload();
}, 300000);

// Manual refresh function
function refreshPage() {
    location.reload();
}
</script>
@endsection
