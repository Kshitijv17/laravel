<!-- Chat Widget -->
<div id="chat-widget" class="fixed bottom-4 right-4 z-50">
    <!-- Chat Button -->
    <div id="chat-button" class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg cursor-pointer transition-all duration-300 transform hover:scale-105">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a9.863 9.863 0 01-4.906-1.289L3 21l1.289-5.094A9.863 9.863 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
        </svg>
        <span id="unread-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
    </div>

    <!-- Chat Window -->
    <div id="chat-window" class="hidden bg-white rounded-lg shadow-2xl w-80 h-96 flex flex-col overflow-hidden">
        <!-- Header -->
        <div class="bg-blue-600 text-white p-4 flex justify-between items-center">
            <div>
                <h3 class="font-semibold">Customer Support</h3>
                <p class="text-xs opacity-90" id="agent-status">We're here to help!</p>
            </div>
            <div class="flex space-x-2">
                <button id="minimize-chat" class="hover:bg-blue-700 p-1 rounded">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 10h12v2H4z"/>
                    </svg>
                </button>
                <button id="close-chat" class="hover:bg-blue-700 p-1 rounded">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Messages Area -->
        <div id="messages-container" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">
            <!-- Welcome Message -->
            <div class="flex items-start space-x-2">
                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                    CS
                </div>
                <div class="bg-white p-3 rounded-lg shadow-sm max-w-xs">
                    <p class="text-sm">Hello! How can we help you today?</p>
                    <span class="text-xs text-gray-500">Just now</span>
                </div>
            </div>
        </div>

        <!-- Typing Indicator -->
        <div id="typing-indicator" class="hidden px-4 py-2">
            <div class="flex items-center space-x-2 text-gray-500 text-sm">
                <div class="flex space-x-1">
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
                <span>Agent is typing...</span>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-4 border-t bg-white">
            <form id="quick-message-form" class="space-y-3">
                @guest
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" name="name" placeholder="Your Name" class="text-sm border rounded px-2 py-1" required>
                    <input type="email" name="email" placeholder="Your Email" class="text-sm border rounded px-2 py-1" required>
                </div>
                @endguest
                
                <div class="flex space-x-2">
                    <input type="text" name="message" placeholder="Type your message..." 
                           class="flex-1 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           maxlength="500" required>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.429a1 1 0 001.17-1.409l-7-14z"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Full Chat Window (for authenticated users) -->
    <div id="full-chat-window" class="hidden fixed inset-4 bg-white rounded-lg shadow-2xl flex flex-col z-50">
        <!-- Header -->
        <div class="bg-blue-600 text-white p-4 flex justify-between items-center rounded-t-lg">
            <div>
                <h3 class="font-semibold text-lg">Customer Support Chat</h3>
                <p class="text-sm opacity-90" id="full-agent-status">Connected</p>
            </div>
            <button id="close-full-chat" class="hover:bg-blue-700 p-2 rounded">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

        <!-- Messages Area -->
        <div id="full-messages-container" class="flex-1 overflow-y-auto p-6 bg-gray-50">
            <!-- Messages will be loaded here -->
        </div>

        <!-- Input Area -->
        <div class="p-4 border-t bg-white rounded-b-lg">
            <form id="full-message-form" class="flex space-x-3">
                <input type="file" id="attachment-input" class="hidden" accept="image/*,.pdf,.doc,.docx">
                <button type="button" id="attachment-btn" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                </button>
                <input type="text" name="message" placeholder="Type your message..." 
                       class="flex-1 border rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors">
                    Send
                </button>
            </form>
        </div>
    </div>
</div>

<style>
@keyframes bounce {
    0%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
}
.animate-bounce {
    animation: bounce 1s infinite;
}
</style>

<script>
class ChatWidget {
    constructor() {
        this.isOpen = false;
        this.isFullScreen = false;
        this.currentChatId = null;
        this.typingTimer = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.checkExistingChats();
    }

    bindEvents() {
        // Toggle chat window
        document.getElementById('chat-button').addEventListener('click', () => {
            this.toggleChat();
        });

        // Minimize/Close buttons
        document.getElementById('minimize-chat').addEventListener('click', () => {
            this.toggleChat();
        });

        document.getElementById('close-chat').addEventListener('click', () => {
            this.closeChat();
        });

        document.getElementById('close-full-chat').addEventListener('click', () => {
            this.closeFullChat();
        });

        // Quick message form
        document.getElementById('quick-message-form').addEventListener('submit', (e) => {
            this.handleQuickMessage(e);
        });

        // Full message form
        document.getElementById('full-message-form').addEventListener('submit', (e) => {
            this.handleFullMessage(e);
        });

        // Attachment button
        document.getElementById('attachment-btn').addEventListener('click', () => {
            document.getElementById('attachment-input').click();
        });

        // Typing indicator
        const messageInput = document.querySelector('#full-message-form input[name="message"]');
        if (messageInput) {
            messageInput.addEventListener('input', () => {
                this.handleTyping();
            });
        }
    }

    toggleChat() {
        const chatWindow = document.getElementById('chat-window');
        const chatButton = document.getElementById('chat-button');
        
        if (this.isOpen) {
            chatWindow.classList.add('hidden');
            chatButton.classList.remove('hidden');
            this.isOpen = false;
        } else {
            chatWindow.classList.remove('hidden');
            this.isOpen = true;
        }
    }

    closeChat() {
        document.getElementById('chat-window').classList.add('hidden');
        this.isOpen = false;
    }

    closeFullChat() {
        document.getElementById('full-chat-window').classList.add('hidden');
        this.isFullScreen = false;
    }

    async handleQuickMessage(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        try {
            const response = await fetch('/chat/quick-start', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            
            if (data.success) {
                if (data.redirect_url) {
                    // Authenticated user - redirect to full chat
                    window.location.href = data.redirect_url;
                } else {
                    // Guest user - show success message
                    this.showMessage('Thank you! An agent will contact you shortly.', 'system');
                    e.target.reset();
                }
            }
        } catch (error) {
            console.error('Error sending message:', error);
            this.showMessage('Sorry, there was an error. Please try again.', 'error');
        }
    }

    async handleFullMessage(e) {
        e.preventDefault();
        if (!this.currentChatId) return;

        const formData = new FormData(e.target);
        
        try {
            const response = await fetch(`/chat/${this.currentChatId}/message`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.addMessage(data.message);
                e.target.reset();
            }
        } catch (error) {
            console.error('Error sending message:', error);
        }
    }

    showMessage(text, type = 'system') {
        const container = document.getElementById('messages-container');
        const messageDiv = document.createElement('div');
        
        const bgColor = type === 'error' ? 'bg-red-100' : 'bg-blue-100';
        
        messageDiv.innerHTML = `
            <div class="flex items-start space-x-2">
                <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center text-white text-sm">
                    !
                </div>
                <div class="${bgColor} p-3 rounded-lg max-w-xs">
                    <p class="text-sm">${text}</p>
                    <span class="text-xs text-gray-500">Just now</span>
                </div>
            </div>
        `;
        
        container.appendChild(messageDiv);
        container.scrollTop = container.scrollHeight;
    }

    addMessage(message) {
        const container = document.getElementById('full-messages-container');
        const messageDiv = document.createElement('div');
        
        const isUser = message.sender_type === 'user';
        const alignment = isUser ? 'justify-end' : 'justify-start';
        const bgColor = isUser ? 'bg-blue-600 text-white' : 'bg-white';
        
        messageDiv.innerHTML = `
            <div class="flex ${alignment} mb-4">
                <div class="${bgColor} p-3 rounded-lg max-w-xs shadow">
                    <p class="text-sm">${message.message}</p>
                    <span class="text-xs opacity-70">${message.formatted_time}</span>
                </div>
            </div>
        `;
        
        container.appendChild(messageDiv);
        container.scrollTop = container.scrollHeight;
    }

    handleTyping() {
        if (!this.currentChatId) return;

        // Clear existing timer
        if (this.typingTimer) {
            clearTimeout(this.typingTimer);
        }

        // Send typing start
        fetch(`/chat/${this.currentChatId}/typing`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ is_typing: true })
        });

        // Set timer to send typing stop
        this.typingTimer = setTimeout(() => {
            fetch(`/chat/${this.currentChatId}/typing`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ is_typing: false })
            });
        }, 2000);
    }

    checkExistingChats() {
        // Check if user has existing active chats
        @auth
        fetch('/chat/active')
            .then(response => response.json())
            .then(data => {
                if (data.active_chat) {
                    this.currentChatId = data.active_chat.id;
                    this.updateUnreadCount(data.unread_count);
                }
            })
            .catch(error => console.error('Error checking chats:', error));
        @endauth
    }

    updateUnreadCount(count) {
        const badge = document.getElementById('unread-count');
        if (count > 0) {
            badge.textContent = count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
}

// Initialize chat widget when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ChatWidget();
});
</script>
