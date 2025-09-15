@extends('layouts.admin')

@section('title', 'Chat Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['waiting_chats'] }}</h4>
                            <p class="mb-0">Waiting Chats</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['active_chats'] }}</h4>
                            <p class="mb-0">Active Chats</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-comments fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['my_active_chats'] }}</h4>
                            <p class="mb-0">My Active Chats</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-comments fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['closed_today'] }}</h4>
                            <p class="mb-0">Closed Today</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Performance Metrics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-info">{{ number_format($stats['avg_response_time'], 1) }}m</h4>
                                <p class="text-muted">Avg Response Time</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-success">{{ number_format($stats['satisfaction_rating'], 1) }}/5</h4>
                                <p class="text-muted">Satisfaction Rating</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.chat.index', ['status' => 'waiting']) }}" class="btn btn-warning">
                            <i class="fas fa-clock me-2"></i>View Waiting Chats
                        </a>
                        <a href="{{ route('admin.chat.index', ['agent_id' => auth('admin')->id()]) }}" class="btn btn-info">
                            <i class="fas fa-user me-2"></i>My Chats
                        </a>
                        <a href="{{ route('admin.chat.index') }}" class="btn btn-primary">
                            <i class="fas fa-list me-2"></i>All Chats
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Waiting Chats -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Waiting Chats</h5>
                    <span class="badge bg-warning">{{ $waitingChats->count() }}</span>
                </div>
                <div class="card-body p-0">
                    @if($waitingChats->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($waitingChats as $chat)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $chat->subject ?? 'General Support' }}</h6>
                                            <p class="mb-1 text-muted">{{ $chat->user->name ?? 'Guest User' }}</p>
                                            <small class="text-muted">
                                                {{ $chat->created_at->diffForHumans() }} • 
                                                <span class="badge bg-{{ $chat->priority === 'urgent' ? 'danger' : ($chat->priority === 'high' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($chat->priority) }}
                                                </span>
                                            </small>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.chat.show', $chat->id) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button class="btn btn-outline-success" onclick="takeChat({{ $chat->id }})">
                                                <i class="fas fa-hand-paper"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No waiting chats</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- My Active Chats -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Active Chats</h5>
                    <span class="badge bg-success">{{ $myActiveChats->count() }}</span>
                </div>
                <div class="card-body p-0">
                    @if($myActiveChats->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($myActiveChats as $chat)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $chat->subject ?? 'General Support' }}</h6>
                                            <p class="mb-1 text-muted">{{ $chat->user->name ?? 'Guest User' }}</p>
                                            <small class="text-muted">
                                                Last message: {{ $chat->last_message_at ? $chat->last_message_at->diffForHumans() : 'No messages' }}
                                            </small>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.chat.show', $chat->id) }}" class="btn btn-primary">
                                                <i class="fas fa-comments"></i> Chat
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No active chats</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Auto-refresh stats -->
<script>
function refreshStats() {
    fetch('{{ route("admin.chat.stats") }}')
        .then(response => response.json())
        .then(data => {
            // Update stat cards
            document.querySelector('.bg-warning .card-body h4').textContent = data.waiting;
            document.querySelector('.bg-success .card-body h4').textContent = data.active;
            document.querySelector('.bg-info .card-body h4').textContent = data.my_active;
            document.querySelector('.bg-primary .card-body h4').textContent = data.closed_today;
        })
        .catch(error => console.error('Error refreshing stats:', error));
}

function takeChat(chatId) {
    fetch(`/admin/chat/${chatId}/take`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = `/admin/chat/${chatId}`;
        } else {
            alert('Failed to take chat: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error taking chat:', error);
        alert('Error taking chat');
    });
}

// Refresh stats every 30 seconds
setInterval(refreshStats, 30000);

// Real-time notifications
@if(config('broadcasting.default') !== 'null')
Echo.private('admin-chat-notifications')
    .listen('.message.sent', (e) => {
        // Show notification for new messages
        if (e.sender_type === 'user') {
            showNotification(`New message from ${e.sender_name}`, 'info');
        }
    });

function showNotification(message, type = 'info') {
    // Create toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 5000);
}
@endif
</script>
@endsection
