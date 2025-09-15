@extends('layouts.admin')

@section('title', 'Chat Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Chat Management Dashboard</h3>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" onclick="refreshChats()">
                            <i class="fas fa-refresh"></i> Refresh
                        </button>
                        <button type="button" class="btn btn-success" onclick="exportChats()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $stats['active_chats'] ?? 0 }}</h4>
                                            <p class="mb-0">Active Chats</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-comments fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $stats['waiting_chats'] ?? 0 }}</h4>
                                            <p class="mb-0">Waiting</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $stats['resolved_today'] ?? 0 }}</h4>
                                            <p class="mb-0">Resolved Today</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ number_format($stats['avg_response_time'] ?? 0, 1) }}m</h4>
                                            <p class="mb-0">Avg Response</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-stopwatch fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="waiting">Waiting</option>
                                <option value="active">Active</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="priorityFilter">
                                <option value="">All Priorities</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="agentFilter">
                                <option value="">All Agents</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchChat" placeholder="Search chats...">
                        </div>
                    </div>

                    <!-- Chat List -->
                    <div class="table-responsive">
                        <table class="table table-striped" id="chatTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Agent</th>
                                    <th>Created</th>
                                    <th>Last Message</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($chats as $chat)
                                <tr class="chat-row" data-status="{{ $chat->status }}">
                                    <td>#{{ $chat->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-2">
                                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="fas fa-user text-white small"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <strong>{{ $chat->user->name ?? 'Guest' }}</strong>
                                                <br><small class="text-muted">{{ $chat->user->email ?? 'No email' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $chat->subject }}</strong>
                                        <br><small class="text-muted">{{ $chat->department }}</small>
                                    </td>
                                    <td>
                                        @switch($chat->status)
                                            @case('waiting')
                                                <span class="badge bg-warning">Waiting</span>
                                                @break
                                            @case('active')
                                                <span class="badge bg-primary">Active</span>
                                                @break
                                            @case('closed')
                                                <span class="badge bg-success">Closed</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($chat->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @switch($chat->priority)
                                            @case('urgent')
                                                <span class="badge bg-danger">Urgent</span>
                                                @break
                                            @case('high')
                                                <span class="badge bg-warning">High</span>
                                                @break
                                            @case('medium')
                                                <span class="badge bg-info">Medium</span>
                                                @break
                                            @case('low')
                                                <span class="badge bg-secondary">Low</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($chat->agent)
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success rounded-circle me-1" style="width: 8px; height: 8px;"></div>
                                                {{ $chat->agent->name }}
                                            </div>
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $chat->created_at->format('M d, H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($chat->last_message_at)
                                            <small>{{ $chat->last_message_at->diffForHumans() }}</small>
                                        @else
                                            <small class="text-muted">No messages</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" onclick="viewChat({{ $chat->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($chat->status !== 'closed')
                                                <button type="button" class="btn btn-outline-success" onclick="assignChat({{ $chat->id }})">
                                                    <i class="fas fa-user-plus"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" onclick="closeChat({{ $chat->id }})">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $chats->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Agent Modal -->
<div class="modal fade" id="assignAgentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Agent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignAgentForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Agent</label>
                        <select class="form-select" name="agent_id" required>
                            <option value="">Choose an agent...</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }} ({{ $agent->active_chats_count ?? 0 }} active)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Priority</label>
                        <select class="form-select" name="priority">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (optional)</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Any notes for the agent..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Agent</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Chat View Modal -->
<div class="modal fade" id="chatViewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chat Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="chatViewContent">
                    <!-- Content loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentChatId = null;

// View chat details
function viewChat(chatId) {
    fetch(`{{ url('admin/chat') }}/${chatId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('chatViewContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('chatViewModal')).show();
        });
}

// Assign agent to chat
function assignChat(chatId) {
    currentChatId = chatId;
    new bootstrap.Modal(document.getElementById('assignAgentModal')).show();
}

// Close chat
function closeChat(chatId) {
    if (confirm('Are you sure you want to close this chat?')) {
        fetch(`{{ url('admin/chat') }}/${chatId}/close`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error closing chat: ' + data.message);
            }
        });
    }
}

// Assign agent form submission
document.getElementById('assignAgentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`{{ url('admin/chat') }}/${currentChatId}/assign`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('assignAgentModal')).hide();
            location.reload();
        } else {
            alert('Error assigning agent: ' + data.message);
        }
    });
});

// Filter functionality
function filterChats() {
    const status = document.getElementById('statusFilter').value;
    const priority = document.getElementById('priorityFilter').value;
    const agent = document.getElementById('agentFilter').value;
    const search = document.getElementById('searchChat').value;
    
    const params = new URLSearchParams();
    if (status) params.append('status', status);
    if (priority) params.append('priority', priority);
    if (agent) params.append('agent', agent);
    if (search) params.append('search', search);
    
    window.location.href = '{{ route("admin.chat.index") }}?' + params.toString();
}

// Refresh chats
function refreshChats() {
    location.reload();
}

// Export chats
function exportChats() {
    window.open('{{ route("admin.chat.export") }}', '_blank');
}

// Add event listeners
document.getElementById('statusFilter').addEventListener('change', filterChats);
document.getElementById('priorityFilter').addEventListener('change', filterChats);
document.getElementById('agentFilter').addEventListener('change', filterChats);
document.getElementById('searchChat').addEventListener('keyup', function(e) {
    if (e.key === 'Enter') {
        filterChats();
    }
});

// Auto-refresh every 30 seconds
setInterval(function() {
    // Only refresh if no modals are open
    if (!document.querySelector('.modal.show')) {
        location.reload();
    }
}, 30000);
</script>
@endsection
