@extends('layouts.admin')

@section('title', 'File Manager')
@section('subtitle', 'Manage your media files and uploads')

@section('content')
<div class="d-flex justify-content-end align-items-center mb-3">
    <div class="btn-group">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="fas fa-upload me-2"></i>Upload Files
        </button>
        <button class="btn btn-success" onclick="createFolder()">
            <i class="fas fa-folder-plus me-2"></i>New Folder
        </button>
        <button class="btn btn-warning" onclick="bulkDelete()">
            <i class="fas fa-trash me-2"></i>Delete Selected
        </button>
        <button class="btn btn-info" onclick="refreshFiles()">
            <i class="fas fa-sync-alt me-2"></i>Refresh
        </button>
    </div>
</div>
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">File Manager</li>
    </ol>
</nav>

<!-- Storage Stats -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Files</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalFiles">{{ $stats['total_files'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Storage Used</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="storageUsed">{{ $stats['storage_used'] ?? '0 MB' }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hdd fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Images</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalImages">{{ $stats['total_images'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-image fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Documents</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalDocs">{{ $stats['total_documents'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">File Browser</h5>
            <div class="d-flex align-items-center">
                <!-- Search -->
                <input type="text" class="form-control form-control-sm me-2" id="fileSearch" placeholder="Search files..." style="width: 200px;">
                
                <!-- View Toggle -->
                <div class="btn-group btn-group-sm me-2">
                    <button class="btn btn-outline-secondary active" onclick="toggleView('grid')" id="gridView">
                        <i class="fas fa-th"></i>
                    </button>
                    <button class="btn btn-outline-secondary" onclick="toggleView('list')" id="listView">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
                
                <!-- Sort Options -->
                <select class="form-select form-select-sm" id="sortBy" onchange="sortFiles()">
                    <option value="name">Name</option>
                    <option value="date">Date</option>
                    <option value="size">Size</option>
                    <option value="type">Type</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Breadcrumb Navigation -->
        <nav aria-label="File path" class="mb-3">
            <ol class="breadcrumb mb-0" id="fileBreadcrumb">
                <li class="breadcrumb-item">
                    <a href="#" onclick="navigateToPath('')">
                        <i class="fas fa-home"></i> Root
                    </a>
                </li>
            </ol>
        </nav>
        
        <!-- File Grid/List Container -->
        <div id="fileContainer" class="file-grid">
            <!-- Files will be loaded here via JavaScript -->
        </div>
        
        <!-- Loading State -->
        <div id="loadingState" class="text-center py-5 d-none">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading files...</p>
        </div>
        
        <!-- Empty State -->
        <div id="emptyState" class="text-center py-5 d-none">
            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
            <p class="text-muted">No files found in this directory</p>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="fas fa-upload me-2"></i>Upload Files
            </button>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Files</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="upload-area" id="uploadArea">
                    <div class="upload-content">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                        <h5>Drag & Drop files here</h5>
                        <p class="text-muted">or click to browse</p>
                        <input type="file" id="fileInput" multiple accept="*/*" style="display: none;">
                        <button type="button" class="btn btn-primary" onclick="document.getElementById('fileInput').click()">
                            Choose Files
                        </button>
                    </div>
                </div>
                
                <!-- Upload Progress -->
                <div id="uploadProgress" class="mt-3 d-none">
                    <div class="progress mb-2">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Uploading files...</small>
                        <small class="text-muted"><span id="uploadPercent">0</span>%</small>
                    </div>
                </div>
                
                <!-- File Queue -->
                <div id="fileQueue" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="startUpload" onclick="startUpload()" disabled>
                    <i class="fas fa-upload me-2"></i>Upload Files
                </button>
            </div>
        </div>
    </div>
</div>

<!-- File Details Modal -->
<div class="modal fade" id="fileDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">File Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="fileDetailsContent">
                <!-- Content loaded via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="downloadFile()">
                    <i class="fas fa-download me-2"></i>Download
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Rename Modal -->
<div class="modal fade" id="renameModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rename File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">New Name</label>
                    <input type="text" class="form-control" id="newFileName" placeholder="Enter new name">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmRename()">
                    <i class="fas fa-save me-2"></i>Rename
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.file-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
}

.file-list {
    display: block;
}

.file-item {
    border: 1px solid #e3e6f0;
    border-radius: 0.5rem;
    padding: 1rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.file-item:hover {
    border-color: #4e73df;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.file-item.selected {
    border-color: #4e73df;
    background-color: rgba(78, 115, 223, 0.1);
}

.file-icon {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

.file-name {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    word-break: break-word;
}

.file-size {
    font-size: 0.75rem;
    color: #6c757d;
}

.file-checkbox {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
}

.file-actions {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.file-item:hover .file-actions {
    opacity: 1;
}

.upload-area {
    border: 2px dashed #e3e6f0;
    border-radius: 0.5rem;
    padding: 3rem;
    text-align: center;
    transition: border-color 0.3s ease;
}

.upload-area.dragover {
    border-color: #4e73df;
    background-color: rgba(78, 115, 223, 0.05);
}

.file-list .file-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    text-align: left;
}

.file-list .file-icon {
    font-size: 1.5rem;
    margin-right: 1rem;
    margin-bottom: 0;
}

.file-list .file-info {
    flex: 1;
}

.file-list .file-name {
    margin-bottom: 0.125rem;
}

.file-list .file-actions {
    position: static;
    opacity: 1;
}

.breadcrumb-item a {
    text-decoration: none;
    color: #4e73df;
}

.breadcrumb-item a:hover {
    text-decoration: underline;
}

.image-preview {
    max-width: 100%;
    max-height: 200px;
    object-fit: cover;
    border-radius: 0.25rem;
}
</style>
@endpush

@push('scripts')
<script>
let currentPath = '';
let selectedFiles = [];
let currentView = 'grid';
let fileData = [];

// Initialize file manager
document.addEventListener('DOMContentLoaded', function() {
    loadFiles();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    // File input change
    document.getElementById('fileInput').addEventListener('change', handleFileSelect);
    
    // Search functionality
    document.getElementById('fileSearch').addEventListener('input', filterFiles);
    
    // Upload area drag and drop
    const uploadArea = document.getElementById('uploadArea');
    uploadArea.addEventListener('dragover', handleDragOver);
    uploadArea.addEventListener('dragleave', handleDragLeave);
    uploadArea.addEventListener('drop', handleDrop);
}

// Load files from server
function loadFiles(path = '') {
    currentPath = path;
    showLoading(true);
    
    fetch(`/admin/media/files?path=${encodeURIComponent(path)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fileData = data.files;
                renderFiles(fileData);
                updateBreadcrumb(path);
                updateStats(data.stats);
            } else {
                showError('Failed to load files');
            }
        })
        .catch(error => {
            console.error('Error loading files:', error);
            showError('Error loading files');
        })
        .finally(() => {
            showLoading(false);
        });
}

// Render files in current view
function renderFiles(files) {
    const container = document.getElementById('fileContainer');
    const emptyState = document.getElementById('emptyState');
    
    if (files.length === 0) {
        container.innerHTML = '';
        emptyState.classList.remove('d-none');
        return;
    }
    
    emptyState.classList.add('d-none');
    
    if (currentView === 'grid') {
        container.className = 'file-grid';
        container.innerHTML = files.map(file => createFileGridItem(file)).join('');
    } else {
        container.className = 'file-list';
        container.innerHTML = files.map(file => createFileListItem(file)).join('');
    }
    
    // Add event listeners to file items
    container.querySelectorAll('.file-item').forEach(item => {
        item.addEventListener('click', (e) => {
            if (!e.target.closest('.file-checkbox') && !e.target.closest('.file-actions')) {
                handleFileClick(item.dataset.path, item.dataset.type);
            }
        });
    });
}

// Create file grid item HTML
function createFileGridItem(file) {
    const icon = getFileIcon(file.type, file.extension);
    const isImage = file.type === 'image';
    
    return `
        <div class="file-item" data-path="${file.path}" data-type="${file.type}">
            <input type="checkbox" class="form-check-input file-checkbox" onchange="toggleFileSelection('${file.path}')">
            <div class="file-actions">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="showFileDetails('${file.path}')" title="Details">
                        <i class="fas fa-info"></i>
                    </button>
                    <button class="btn btn-outline-warning" onclick="renameFile('${file.path}')" title="Rename">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-danger" onclick="deleteFile('${file.path}')" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            ${isImage ? 
                `<img src="${file.url}" alt="${file.name}" class="image-preview mb-2">` :
                `<div class="file-icon ${icon.color}">${icon.icon}</div>`
            }
            <div class="file-name">${file.name}</div>
            <div class="file-size">${file.size}</div>
        </div>
    `;
}

// Create file list item HTML
function createFileListItem(file) {
    const icon = getFileIcon(file.type, file.extension);
    
    return `
        <div class="file-item" data-path="${file.path}" data-type="${file.type}">
            <input type="checkbox" class="form-check-input file-checkbox me-3" onchange="toggleFileSelection('${file.path}')">
            <div class="file-icon ${icon.color}">${icon.icon}</div>
            <div class="file-info">
                <div class="file-name">${file.name}</div>
                <div class="file-size">${file.size} • ${file.modified}</div>
            </div>
            <div class="file-actions">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="showFileDetails('${file.path}')" title="Details">
                        <i class="fas fa-info"></i>
                    </button>
                    <button class="btn btn-outline-warning" onclick="renameFile('${file.path}')" title="Rename">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-danger" onclick="deleteFile('${file.path}')" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Get file icon based on type and extension
function getFileIcon(type, extension) {
    const icons = {
        folder: { icon: '<i class="fas fa-folder"></i>', color: 'text-warning' },
        image: { icon: '<i class="fas fa-image"></i>', color: 'text-success' },
        video: { icon: '<i class="fas fa-video"></i>', color: 'text-info' },
        audio: { icon: '<i class="fas fa-music"></i>', color: 'text-purple' },
        pdf: { icon: '<i class="fas fa-file-pdf"></i>', color: 'text-danger' },
        doc: { icon: '<i class="fas fa-file-word"></i>', color: 'text-primary' },
        xls: { icon: '<i class="fas fa-file-excel"></i>', color: 'text-success' },
        zip: { icon: '<i class="fas fa-file-archive"></i>', color: 'text-warning' },
        default: { icon: '<i class="fas fa-file"></i>', color: 'text-muted' }
    };
    
    if (type === 'folder') return icons.folder;
    if (type === 'image') return icons.image;
    if (type === 'video') return icons.video;
    if (type === 'audio') return icons.audio;
    if (extension === 'pdf') return icons.pdf;
    if (['doc', 'docx'].includes(extension)) return icons.doc;
    if (['xls', 'xlsx'].includes(extension)) return icons.xls;
    if (['zip', 'rar', '7z'].includes(extension)) return icons.zip;
    
    return icons.default;
}

// Handle file click (open folder or show preview)
function handleFileClick(path, type) {
    if (type === 'folder') {
        loadFiles(path);
    } else {
        showFileDetails(path);
    }
}

// Toggle file selection
function toggleFileSelection(path) {
    const index = selectedFiles.indexOf(path);
    if (index > -1) {
        selectedFiles.splice(index, 1);
    } else {
        selectedFiles.push(path);
    }
}

// Toggle between grid and list view
function toggleView(view) {
    currentView = view;
    document.getElementById('gridView').classList.toggle('active', view === 'grid');
    document.getElementById('listView').classList.toggle('active', view === 'list');
    renderFiles(fileData);
}

// Sort files
function sortFiles() {
    const sortBy = document.getElementById('sortBy').value;
    
    fileData.sort((a, b) => {
        switch (sortBy) {
            case 'name':
                return a.name.localeCompare(b.name);
            case 'date':
                return new Date(b.modified) - new Date(a.modified);
            case 'size':
                return b.sizeBytes - a.sizeBytes;
            case 'type':
                return a.type.localeCompare(b.type);
            default:
                return 0;
        }
    });
    
    renderFiles(fileData);
}

// Filter files based on search
function filterFiles() {
    const search = document.getElementById('fileSearch').value.toLowerCase();
    const filtered = fileData.filter(file => 
        file.name.toLowerCase().includes(search)
    );
    renderFiles(filtered);
}

// File upload functions
function handleFileSelect(e) {
    const files = Array.from(e.target.files);
    displayFileQueue(files);
}

function handleDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('dragover');
}

function handleDragLeave(e) {
    e.currentTarget.classList.remove('dragover');
}

function handleDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('dragover');
    const files = Array.from(e.dataTransfer.files);
    displayFileQueue(files);
}

function displayFileQueue(files) {
    const queue = document.getElementById('fileQueue');
    queue.innerHTML = files.map(file => `
        <div class="d-flex justify-content-between align-items-center p-2 border rounded mb-2">
            <div>
                <strong>${file.name}</strong>
                <small class="text-muted d-block">${formatFileSize(file.size)}</small>
            </div>
            <span class="badge bg-secondary">Pending</span>
        </div>
    `).join('');
    
    document.getElementById('startUpload').disabled = files.length === 0;
}

// Utility functions
function showLoading(show) {
    document.getElementById('loadingState').classList.toggle('d-none', !show);
}

function showError(message) {
    alert(message); // Replace with toast notification
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function updateBreadcrumb(path) {
    const breadcrumb = document.getElementById('fileBreadcrumb');
    const parts = path.split('/').filter(part => part);
    
    let html = `
        <li class="breadcrumb-item">
            <a href="#" onclick="navigateToPath('')">
                <i class="fas fa-home"></i> Root
            </a>
        </li>
    `;
    
    let currentPath = '';
    parts.forEach((part, index) => {
        currentPath += part + '/';
        if (index === parts.length - 1) {
            html += `<li class="breadcrumb-item active">${part}</li>`;
        } else {
            html += `
                <li class="breadcrumb-item">
                    <a href="#" onclick="navigateToPath('${currentPath}')">${part}</a>
                </li>
            `;
        }
    });
    
    breadcrumb.innerHTML = html;
}

function navigateToPath(path) {
    loadFiles(path);
}

function updateStats(stats) {
    if (stats) {
        document.getElementById('totalFiles').textContent = stats.total_files || 0;
        document.getElementById('storageUsed').textContent = stats.storage_used || '0 MB';
        document.getElementById('totalImages').textContent = stats.total_images || 0;
        document.getElementById('totalDocs').textContent = stats.total_documents || 0;
    }
}

// File operations
function createFolder() {
    const name = prompt('Enter folder name:');
    if (name) {
        fetch('/admin/media/folder', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                name: name,
                path: currentPath
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadFiles(currentPath);
            } else {
                showError(data.message);
            }
        });
    }
}

function deleteFile(path) {
    if (confirm('Are you sure you want to delete this file?')) {
        fetch(`/admin/media/delete`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ path: path })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadFiles(currentPath);
            } else {
                showError(data.message);
            }
        });
    }
}

function bulkDelete() {
    if (selectedFiles.length === 0) {
        alert('Please select files to delete');
        return;
    }
    
    if (confirm(`Delete ${selectedFiles.length} selected files?`)) {
        fetch('/admin/media/bulk-delete', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ files: selectedFiles })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                selectedFiles = [];
                loadFiles(currentPath);
            } else {
                showError(data.message);
            }
        });
    }
}

function refreshFiles() {
    loadFiles(currentPath);
}

function showFileDetails(path) {
    // Implementation for file details modal
    alert('File details modal would be implemented here');
}

function renameFile(path) {
    // Implementation for rename modal
    alert('Rename functionality would be implemented here');
}

function startUpload() {
    // Implementation for file upload
    alert('Upload functionality would be implemented here');
}
</script>
@endpush
