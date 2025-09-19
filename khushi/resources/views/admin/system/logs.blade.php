@extends('layouts.admin')

@section('title', 'System Logs')
@section('subtitle', 'Monitor application logs and system events')

@section('content')
<div class="d-flex justify-content-end align-items-center mb-3">
    <div class="btn-group">
        <button class="btn btn-danger" onclick="clearLogs()" title="Clear All Logs">
            <i class="fas fa-trash me-2"></i>Clear Logs
        </button>
        <button class="btn btn-warning" onclick="downloadLogs()" title="Download Logs">
            <i class="fas fa-download me-2"></i>Download
        </button>
        <button class="btn btn-info" onclick="refreshLogs()" title="Refresh">
            <i class="fas fa-sync-alt me-2"></i>Refresh
        </button>
        <button class="btn btn-success" onclick="toggleAutoRefresh()" id="autoRefreshBtn" title="Auto Refresh">
            <i class="fas fa-play me-2"></i>Auto Refresh
        </button>
    </div>
</div>
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.system.index') }}">System</a></li>
        <li class="breadcrumb-item active">Logs</li>
    </ol>
</nav>

<!-- Log Statistics -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Errors</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="errorCount">{{ $stats['errors'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Warnings</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="warningCount">{{ $stats['warnings'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Info</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="infoCount">{{ $stats['info'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-info-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Debug</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="debugCount">{{ $stats['debug'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-bug fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">System Logs</h5>
            <div class="d-flex align-items-center">
                <!-- Log Level Filter -->
                <select class="form-select form-select-sm me-2" id="logLevelFilter" onchange="filterLogs()">
                    <option value="">All Levels</option>
                    <option value="emergency">Emergency</option>
                    <option value="alert">Alert</option>
                    <option value="critical">Critical</option>
                    <option value="error">Error</option>
                    <option value="warning">Warning</option>
                    <option value="notice">Notice</option>
                    <option value="info">Info</option>
                    <option value="debug">Debug</option>
                </select>
                
                <!-- Date Filter -->
                <input type="date" class="form-control form-control-sm me-2" id="dateFilter" onchange="filterLogs()">
                
                <!-- Search -->
                <input type="text" class="form-control form-control-sm me-2" id="logSearch" placeholder="Search logs..." style="width: 200px;">
                
                <!-- Live Mode Toggle -->
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="liveMode" onchange="toggleLiveMode()">
                    <label class="form-check-label" for="liveMode">Live</label>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <!-- Log Container -->
        <div id="logContainer" class="log-container">
            <!-- Logs will be loaded here -->
        </div>
        
        <!-- Loading State -->
        <div id="loadingState" class="text-center py-4 d-none">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading logs...</p>
        </div>
        
        <!-- Empty State -->
        <div id="emptyState" class="text-center py-5 d-none">
            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
            <p class="text-muted">No logs found</p>
        </div>
    </div>
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Last updated: <span id="lastUpdated">{{ now()->format('Y-m-d H:i:s') }}</span>
            </small>
            <div>
                <button class="btn btn-sm btn-outline-secondary" onclick="loadMoreLogs()">
                    <i class="fas fa-chevron-down me-1"></i>Load More
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Log Details Modal -->
<div class="modal fade" id="logDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="logDetailsContent">
                    <!-- Content loaded via JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="copyLogDetails()">
                    <i class="fas fa-copy me-2"></i>Copy to Clipboard
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.log-container {
    max-height: 600px;
    overflow-y: auto;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
    background-color: #f8f9fa;
}

.log-entry {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e9ecef;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.log-entry:hover {
    background-color: #e9ecef;
}

.log-entry.emergency,
.log-entry.alert,
.log-entry.critical {
    border-left: 4px solid #dc3545;
    background-color: rgba(220, 53, 69, 0.05);
}

.log-entry.error {
    border-left: 4px solid #fd7e14;
    background-color: rgba(253, 126, 20, 0.05);
}

.log-entry.warning {
    border-left: 4px solid #ffc107;
    background-color: rgba(255, 193, 7, 0.05);
}

.log-entry.notice,
.log-entry.info {
    border-left: 4px solid #17a2b8;
    background-color: rgba(23, 162, 184, 0.05);
}

.log-entry.debug {
    border-left: 4px solid #6c757d;
    background-color: rgba(108, 117, 125, 0.05);
}

.log-level {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    min-width: 70px;
    text-align: center;
}

.log-level.emergency,
.log-level.alert,
.log-level.critical {
    background-color: #dc3545;
    color: white;
}

.log-level.error {
    background-color: #fd7e14;
    color: white;
}

.log-level.warning {
    background-color: #ffc107;
    color: #212529;
}

.log-level.notice,
.log-level.info {
    background-color: #17a2b8;
    color: white;
}

.log-level.debug {
    background-color: #6c757d;
    color: white;
}

.log-timestamp {
    color: #6c757d;
    font-size: 0.8rem;
}

.log-message {
    margin-top: 0.25rem;
    word-break: break-word;
}

.log-context {
    margin-top: 0.5rem;
    padding: 0.5rem;
    background-color: rgba(0, 0, 0, 0.05);
    border-radius: 0.25rem;
    font-size: 0.8rem;
}

.log-stack-trace {
    margin-top: 0.5rem;
    padding: 0.5rem;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    white-space: pre-wrap;
    max-height: 200px;
    overflow-y: auto;
}

.auto-refresh-indicator {
    position: fixed;
    top: 20px;
    right: 20px;
    background-color: #28a745;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    z-index: 1000;
    display: none;
}

.auto-refresh-indicator.active {
    display: block;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.pulse {
    animation: pulse 1s infinite;
}
</style>
@endpush

@push('scripts')
<script>
let currentPage = 1;
let isAutoRefresh = false;
let autoRefreshInterval = null;
let isLiveMode = false;
let logData = [];

// Initialize logs viewer
document.addEventListener('DOMContentLoaded', function() {
    loadLogs();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    // Search functionality
    document.getElementById('logSearch').addEventListener('input', debounce(filterLogs, 300));
    
    // Auto-scroll to bottom in live mode
    const logContainer = document.getElementById('logContainer');
    logContainer.addEventListener('scroll', function() {
        if (isLiveMode && this.scrollTop + this.clientHeight >= this.scrollHeight - 10) {
            // User is at bottom, keep auto-scrolling
        }
    });
}

// Load logs from server
function loadLogs(page = 1, append = false) {
    if (!append) {
        showLoading(true);
        currentPage = 1;
    }
    
    const params = new URLSearchParams({
        page: page,
        level: document.getElementById('logLevelFilter').value,
        date: document.getElementById('dateFilter').value,
        search: document.getElementById('logSearch').value
    });
    
    fetch(`/admin/system/logs/data?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (append) {
                    logData = [...logData, ...data.logs];
                } else {
                    logData = data.logs;
                }
                renderLogs(logData, append);
                updateStats(data.stats);
                updateLastUpdated();
            } else {
                showError('Failed to load logs');
            }
        })
        .catch(error => {
            console.error('Error loading logs:', error);
            showError('Error loading logs');
        })
        .finally(() => {
            showLoading(false);
        });
}

// Render logs in container
function renderLogs(logs, append = false) {
    const container = document.getElementById('logContainer');
    const emptyState = document.getElementById('emptyState');
    
    if (logs.length === 0 && !append) {
        container.innerHTML = '';
        emptyState.classList.remove('d-none');
        return;
    }
    
    emptyState.classList.add('d-none');
    
    const html = logs.map(log => createLogEntry(log)).join('');
    
    if (append) {
        container.innerHTML += html;
    } else {
        container.innerHTML = html;
    }
    
    // Add click event listeners
    container.querySelectorAll('.log-entry').forEach(entry => {
        entry.addEventListener('click', () => {
            showLogDetails(entry.dataset.logId);
        });
    });
    
    // Auto-scroll to bottom in live mode
    if (isLiveMode && !append) {
        container.scrollTop = container.scrollHeight;
    }
}

// Create log entry HTML
function createLogEntry(log) {
    const contextPreview = log.context ? Object.keys(log.context).slice(0, 3).join(', ') : '';
    
    return `
        <div class="log-entry ${log.level}" data-log-id="${log.id}">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-1">
                        <span class="log-level ${log.level}">${log.level}</span>
                        <span class="log-timestamp ms-2">${log.timestamp}</span>
                        ${log.channel ? `<span class="badge bg-secondary ms-2">${log.channel}</span>` : ''}
                    </div>
                    <div class="log-message">${log.message}</div>
                    ${contextPreview ? `<small class="text-muted">Context: ${contextPreview}</small>` : ''}
                </div>
                <div class="ms-2">
                    ${log.count > 1 ? `<span class="badge bg-warning">${log.count}x</span>` : ''}
                </div>
            </div>
        </div>
    `;
}

// Show log details in modal
function showLogDetails(logId) {
    const log = logData.find(l => l.id == logId);
    if (!log) return;
    
    const content = document.getElementById('logDetailsContent');
    content.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>Log Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Level:</strong></td><td><span class="log-level ${log.level}">${log.level}</span></td></tr>
                    <tr><td><strong>Timestamp:</strong></td><td>${log.timestamp}</td></tr>
                    <tr><td><strong>Channel:</strong></td><td>${log.channel || 'N/A'}</td></tr>
                    <tr><td><strong>File:</strong></td><td>${log.file || 'N/A'}</td></tr>
                    <tr><td><strong>Line:</strong></td><td>${log.line || 'N/A'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Request Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>URL:</strong></td><td>${log.url || 'N/A'}</td></tr>
                    <tr><td><strong>Method:</strong></td><td>${log.method || 'N/A'}</td></tr>
                    <tr><td><strong>IP:</strong></td><td>${log.ip || 'N/A'}</td></tr>
                    <tr><td><strong>User Agent:</strong></td><td>${log.user_agent ? log.user_agent.substring(0, 50) + '...' : 'N/A'}</td></tr>
                </table>
            </div>
        </div>
        
        <div class="mt-3">
            <h6>Message</h6>
            <div class="log-message p-3 bg-light border rounded">${log.message}</div>
        </div>
        
        ${log.context ? `
            <div class="mt-3">
                <h6>Context</h6>
                <pre class="log-context"><code>${JSON.stringify(log.context, null, 2)}</code></pre>
            </div>
        ` : ''}
        
        ${log.stack_trace ? `
            <div class="mt-3">
                <h6>Stack Trace</h6>
                <pre class="log-stack-trace"><code>${log.stack_trace}</code></pre>
            </div>
        ` : ''}
    `;
    
    new bootstrap.Modal(document.getElementById('logDetailsModal')).show();
}

// Filter logs
function filterLogs() {
    loadLogs(1, false);
}

// Load more logs (pagination)
function loadMoreLogs() {
    currentPage++;
    loadLogs(currentPage, true);
}

// Toggle auto refresh
function toggleAutoRefresh() {
    isAutoRefresh = !isAutoRefresh;
    const btn = document.getElementById('autoRefreshBtn');
    
    if (isAutoRefresh) {
        btn.innerHTML = '<i class="fas fa-pause me-2"></i>Stop Auto Refresh';
        btn.classList.remove('btn-success');
        btn.classList.add('btn-warning');
        autoRefreshInterval = setInterval(() => {
            loadLogs(1, false);
        }, 5000);
        showAutoRefreshIndicator(true);
    } else {
        btn.innerHTML = '<i class="fas fa-play me-2"></i>Auto Refresh';
        btn.classList.remove('btn-warning');
        btn.classList.add('btn-success');
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
        showAutoRefreshIndicator(false);
    }
}

// Toggle live mode
function toggleLiveMode() {
    isLiveMode = document.getElementById('liveMode').checked;
    
    if (isLiveMode) {
        // Start live updates
        if (!isAutoRefresh) {
            toggleAutoRefresh();
        }
    }
}

// Refresh logs manually
function refreshLogs() {
    const refreshBtn = document.querySelector('[onclick="refreshLogs()"] i');
    refreshBtn.classList.add('fa-spin');
    
    loadLogs(1, false);
    
    setTimeout(() => {
        refreshBtn.classList.remove('fa-spin');
    }, 1000);
}

// Clear all logs
function clearLogs() {
    if (confirm('Are you sure you want to clear all logs? This action cannot be undone.')) {
        fetch('/admin/system/logs/clear', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadLogs(1, false);
                showSuccess('Logs cleared successfully');
            } else {
                showError('Failed to clear logs');
            }
        })
        .catch(error => {
            console.error('Error clearing logs:', error);
            showError('Error clearing logs');
        });
    }
}

// Download logs
function downloadLogs() {
    const params = new URLSearchParams({
        level: document.getElementById('logLevelFilter').value,
        date: document.getElementById('dateFilter').value,
        search: document.getElementById('logSearch').value
    });
    
    window.open(`/admin/system/logs/download?${params}`, '_blank');
}

// Copy log details to clipboard
function copyLogDetails() {
    const content = document.getElementById('logDetailsContent').innerText;
    navigator.clipboard.writeText(content).then(() => {
        showSuccess('Log details copied to clipboard');
    });
}

// Utility functions
function showLoading(show) {
    document.getElementById('loadingState').classList.toggle('d-none', !show);
}

function showError(message) {
    // Replace with toast notification
    console.error(message);
}

function showSuccess(message) {
    // Replace with toast notification
    console.log(message);
}

function updateStats(stats) {
    if (stats) {
        document.getElementById('errorCount').textContent = stats.errors || 0;
        document.getElementById('warningCount').textContent = stats.warnings || 0;
        document.getElementById('infoCount').textContent = stats.info || 0;
        document.getElementById('debugCount').textContent = stats.debug || 0;
    }
}

function updateLastUpdated() {
    document.getElementById('lastUpdated').textContent = new Date().toLocaleString();
}

function showAutoRefreshIndicator(show) {
    let indicator = document.querySelector('.auto-refresh-indicator');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.className = 'auto-refresh-indicator';
        indicator.innerHTML = '<i class="fas fa-sync-alt pulse me-2"></i>Auto Refreshing...';
        document.body.appendChild(indicator);
    }
    indicator.classList.toggle('active', show);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
});
</script>
@endpush
