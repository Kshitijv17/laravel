@extends('layouts.web')

@section('title', 'Customer Support Chat')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2"></i>
                        Customer Support Chat
                    </h5>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-2" id="connectionStatus">Connected</span>
                        <button class="btn btn-sm btn-outline-light" onclick="minimizeChat()">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0" style="height: 500px; display: flex; flex-direction: column;">
                    <!-- Chat Messages Area -->
                    <div class="flex-grow-1 p-3" id="chatMessages" style="overflow-y: auto; background-color: #f8f9fa;">
                        <div class="text-center text-muted mb-3">
                            <small>Chat session started at {{ now()->format('M d, Y H:i') }}</small>
                        </div>
                        
                        <!-- Welcome Message -->
                        <div class="message-item mb-3">
                            <div class="d-flex">
                                <div class="avatar me-2">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="fas fa-headset text-white small"></i>
                                    </div>
                                </div>
                                <div class="message-content">
                                    <div class="bg-white rounded p-2 shadow-sm">
                                        <strong>Support Agent</strong>
                                        <p class="mb-0">Hello! How can I help you today?</p>
                                    </div>
                                    <small class="text-muted">{{ now()->format('H:i') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Typing Indicator -->
                    <div class="px-3 py-1" id="typingIndicator" style="display: none;">
                        <div class="d-flex align-items-center text-muted">
                            <div class="spinner-border spinner-border-sm me-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <small>Support agent is typing...</small>
                        </div>
                    </div>

                    <!-- Message Input Area -->
                    <div class="border-top p-3">
                        <form id="chatForm" class="d-flex align-items-end">
                            <div class="flex-grow-1 me-2">
                                <textarea 
                                    class="form-control" 
                                    id="messageInput" 
                                    placeholder="Type your message..." 
                                    rows="2"
                                    style="resize: none;"
                                ></textarea>
                            </div>
                            <div class="d-flex flex-column">
                                <button type="button" class="btn btn-outline-secondary btn-sm mb-1" onclick="attachFile()">
                                    <i class="fas fa-paperclip"></i>
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                        
                        <!-- Quick Actions -->
                        <div class="mt-2">
                            <small class="text-muted">Quick actions:</small>
                            <div class="btn-group btn-group-sm mt-1" role="group">
                                <button type="button" class="btn btn-outline-secondary" onclick="sendQuickMessage('I need help with my order')">
                                    Order Help
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="sendQuickMessage('I want to return a product')">
                                    Returns
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="sendQuickMessage('I have a technical issue')">
                                    Technical
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- File Upload Modal -->
<div class="modal fade" id="fileUploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attach File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="file" class="form-control" id="fileInput" accept="image/*,.pdf,.doc,.docx,.txt">
                <small class="text-muted">Max file size: 10MB. Supported formats: Images, PDF, DOC, TXT</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="uploadFile()">Upload</button>
            </div>
        </div>
    </div>
</div>

<script>
let chatSocket = null;
let currentTicketId = null;
let isTyping = false;
let typingTimeout = null;

// Initialize chat
document.addEventListener('DOMContentLoaded', function() {
    initializeChat();
    setupEventListeners();
});

function initializeChat() {
    // Create or join chat session
    fetch('{{ route("chat.start") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            type: 'support',
            subject: 'General Support'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentTicketId = data.ticket_id;
            connectWebSocket();
            loadChatHistory();
        }
    })
    .catch(error => {
        console.error('Error initializing chat:', error);
        showError('Failed to initialize chat. Please refresh the page.');
    });
}

function connectWebSocket() {
    // In a real implementation, you would connect to a WebSocket server
    // For now, we'll simulate real-time updates with polling
    setInterval(checkForNewMessages, 3000);
}

function setupEventListeners() {
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });
    
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
        
        // Typing indicator
        if (!isTyping) {
            isTyping = true;
            sendTypingIndicator(true);
        }
        
        clearTimeout(typingTimeout);
        typingTimeout = setTimeout(() => {
            isTyping = false;
            sendTypingIndicator(false);
        }, 1000);
    });
}

function sendMessage() {
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    
    if (!message) return;
    
    // Add message to chat immediately
    addMessageToChat(message, 'user');
    messageInput.value = '';
    
    // Send to server
    fetch('{{ route("chat.send") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            ticket_id: currentTicketId,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            showError('Failed to send message. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        showError('Failed to send message. Please try again.');
    });
}

function sendQuickMessage(message) {
    document.getElementById('messageInput').value = message;
    sendMessage();
}

function addMessageToChat(message, sender, timestamp = null) {
    const chatMessages = document.getElementById('chatMessages');
    const messageTime = timestamp ? new Date(timestamp).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    
    const messageElement = document.createElement('div');
    messageElement.className = 'message-item mb-3';
    
    if (sender === 'user') {
        messageElement.innerHTML = `
            <div class="d-flex justify-content-end">
                <div class="message-content me-2">
                    <div class="bg-primary text-white rounded p-2 shadow-sm">
                        <p class="mb-0">${escapeHtml(message)}</p>
                    </div>
                    <small class="text-muted d-block text-end">${messageTime}</small>
                </div>
                <div class="avatar">
                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="fas fa-user text-white small"></i>
                    </div>
                </div>
            </div>
        `;
    } else {
        messageElement.innerHTML = `
            <div class="d-flex">
                <div class="avatar me-2">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="fas fa-headset text-white small"></i>
                    </div>
                </div>
                <div class="message-content">
                    <div class="bg-white rounded p-2 shadow-sm">
                        <strong>Support Agent</strong>
                        <p class="mb-0">${escapeHtml(message)}</p>
                    </div>
                    <small class="text-muted">${messageTime}</small>
                </div>
            </div>
        `;
    }
    
    chatMessages.appendChild(messageElement);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function loadChatHistory() {
    if (!currentTicketId) return;
    
    fetch(`{{ url('chat/history') }}/${currentTicketId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.messages) {
                data.messages.forEach(msg => {
                    addMessageToChat(msg.message, msg.sender, msg.created_at);
                });
            }
        })
        .catch(error => {
            console.error('Error loading chat history:', error);
        });
}

function checkForNewMessages() {
    if (!currentTicketId) return;
    
    fetch(`{{ url('chat/check') }}/${currentTicketId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.new_messages) {
                data.new_messages.forEach(msg => {
                    if (msg.sender !== 'user') {
                        addMessageToChat(msg.message, msg.sender, msg.created_at);
                    }
                });
            }
            
            // Update typing indicator
            if (data.agent_typing) {
                showTypingIndicator();
            } else {
                hideTypingIndicator();
            }
        })
        .catch(error => {
            console.error('Error checking for new messages:', error);
        });
}

function sendTypingIndicator(typing) {
    if (!currentTicketId) return;
    
    fetch('/chat/typing', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            ticket_id: currentTicketId,
            typing: typing
        })
    });
}

function showTypingIndicator() {
    document.getElementById('typingIndicator').style.display = 'block';
}

function hideTypingIndicator() {
    document.getElementById('typingIndicator').style.display = 'none';
}

function attachFile() {
    new bootstrap.Modal(document.getElementById('fileUploadModal')).show();
}

function uploadFile() {
    const fileInput = document.getElementById('fileInput');
    const file = fileInput.files[0];
    
    if (!file) {
        alert('Please select a file to upload.');
        return;
    }
    
    if (file.size > 10 * 1024 * 1024) {
        alert('File size must be less than 10MB.');
        return;
    }
    
    const formData = new FormData();
    formData.append('file', file);
    formData.append('ticket_id', currentTicketId);
    
    fetch('{{ route("chat.upload") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addMessageToChat(`📎 File uploaded: ${file.name}`, 'user');
            bootstrap.Modal.getInstance(document.getElementById('fileUploadModal')).hide();
            fileInput.value = '';
        } else {
            alert('Failed to upload file. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error uploading file:', error);
        alert('Failed to upload file. Please try again.');
    });
}

function minimizeChat() {
    // Minimize chat functionality
    document.querySelector('.card').style.height = '60px';
    document.querySelector('.card-body').style.display = 'none';
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed';
    errorDiv.style.top = '20px';
    errorDiv.style.right = '20px';
    errorDiv.style.zIndex = '9999';
    errorDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(errorDiv);
    
    setTimeout(() => {
        errorDiv.remove();
    }, 5000);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<style>
.message-item {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.avatar {
    flex-shrink: 0;
}

#chatMessages::-webkit-scrollbar {
    width: 6px;
}

#chatMessages::-webkit-scrollbar-track {
    background: #f1f1f1;
}

#chatMessages::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

#chatMessages::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection
