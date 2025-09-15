<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    private $botToken;
    private $webhookToken;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->webhookToken = config('services.telegram.webhook_token');
    }

    public function webhook(Request $request)
    {
        // Verify webhook token
        if ($request->header('X-Telegram-Bot-Api-Secret-Token') !== $this->webhookToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $update = $request->all();
        
        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        } elseif (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);
        }

        return response()->json(['ok' => true]);
    }

    private function handleMessage($message)
    {
        try {
            $chatId = $message['chat']['id'];
            $userId = $message['from']['id'];
            $text = $message['text'] ?? '';
            $messageType = $this->getMessageType($message);

            // Handle commands
            if (str_starts_with($text, '/')) {
                return $this->handleCommand($text, $chatId, $userId, $message['from']);
            }

            // Find or create user
            $user = $this->findOrCreateUser($userId, $message['from']);
            
            // Find or create chat room
            $chatRoom = $this->findOrCreateChatRoom($user, $chatId);

            // Create message record
            $chatMessage = ChatMessage::create([
                'chat_room_id' => $chatRoom->id,
                'sender_id' => $user->id,
                'sender_type' => ChatMessage::SENDER_USER,
                'message' => $text,
                'message_type' => $messageType,
                'metadata' => [
                    'telegram_message_id' => $message['message_id'],
                    'telegram_chat_id' => $chatId,
                    'telegram_user_id' => $userId,
                    'platform' => 'telegram'
                ]
            ]);

            // Update chat room
            $chatRoom->update(['last_message_at' => now()]);

            // Send auto-reply for new conversations
            if ($chatRoom->messages()->count() === 1) {
                $this->sendAutoReply($chatId);
            }

            // Notify agents
            broadcast(new \App\Events\MessageSent($chatMessage))->toOthers();

        } catch (\Exception $e) {
            Log::error('Telegram message handling error: ' . $e->getMessage(), [
                'message' => $message,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function handleCommand($command, $chatId, $userId, $from)
    {
        $commandParts = explode(' ', $command, 2);
        $cmd = $commandParts[0];
        $args = $commandParts[1] ?? '';

        switch ($cmd) {
            case '/start':
                $this->sendWelcomeMessage($chatId, $from['first_name'] ?? 'User');
                break;
                
            case '/help':
                $this->sendHelpMessage($chatId);
                break;
                
            case '/support':
                $this->startSupportChat($chatId, $userId, $from, $args);
                break;
                
            case '/status':
                $this->sendChatStatus($chatId, $userId);
                break;
                
            case '/end':
                $this->endChat($chatId, $userId);
                break;
                
            default:
                $this->sendMessage($chatId, "Unknown command. Type /help for available commands.");
        }
    }

    private function sendWelcomeMessage($chatId, $firstName)
    {
        $message = "Hello {$firstName}! 👋\n\n";
        $message .= "Welcome to our customer support bot. Here's how I can help you:\n\n";
        $message .= "🔹 /support - Start a support conversation\n";
        $message .= "🔹 /status - Check your current chat status\n";
        $message .= "🔹 /help - Show this help message\n";
        $message .= "🔹 /end - End current support chat\n\n";
        $message .= "Just type /support followed by your question to get started!";

        $this->sendMessage($chatId, $message);
    }

    private function sendHelpMessage($chatId)
    {
        $message = "📋 Available Commands:\n\n";
        $message .= "/start - Welcome message\n";
        $message .= "/support [message] - Start support chat\n";
        $message .= "/status - Check chat status\n";
        $message .= "/end - End current chat\n";
        $message .= "/help - Show this message\n\n";
        $message .= "💡 Tip: You can also just send a message directly to start a conversation!";

        $this->sendMessage($chatId, $message);
    }

    private function startSupportChat($chatId, $userId, $from, $message)
    {
        $user = $this->findOrCreateUser($userId, $from);
        $chatRoom = $this->findOrCreateChatRoom($user, $chatId);

        if ($message) {
            ChatMessage::create([
                'chat_room_id' => $chatRoom->id,
                'sender_id' => $user->id,
                'sender_type' => ChatMessage::SENDER_USER,
                'message' => $message,
                'message_type' => ChatMessage::TYPE_TEXT,
                'metadata' => [
                    'telegram_chat_id' => $chatId,
                    'platform' => 'telegram'
                ]
            ]);

            $chatRoom->update(['last_message_at' => now()]);
        }

        $reply = "✅ Support chat started!\n\n";
        $reply .= "Your message has been forwarded to our support team. ";
        $reply .= "An agent will respond to you shortly.\n\n";
        $reply .= "Chat ID: #{$chatRoom->id}\n";
        $reply .= "Status: " . ucfirst($chatRoom->status);

        $this->sendMessage($chatId, $reply);
    }

    private function sendChatStatus($chatId, $userId)
    {
        $user = User::whereJsonContains('metadata->telegram_user_id', (string)$userId)->first();
        
        if (!$user) {
            $this->sendMessage($chatId, "❌ No active chats found. Use /support to start a new chat.");
            return;
        }

        $activeChat = $user->chatRooms()
            ->whereIn('status', [ChatRoom::STATUS_WAITING, ChatRoom::STATUS_ACTIVE])
            ->whereJsonContains('metadata->telegram_chat_id', (string)$chatId)
            ->first();

        if (!$activeChat) {
            $this->sendMessage($chatId, "❌ No active chats found. Use /support to start a new chat.");
            return;
        }

        $status = "📊 Chat Status:\n\n";
        $status .= "🆔 Chat ID: #{$activeChat->id}\n";
        $status .= "📋 Subject: " . ($activeChat->subject ?? 'General Support') . "\n";
        $status .= "🔄 Status: " . ucfirst($activeChat->status) . "\n";
        $status .= "⏰ Created: " . $activeChat->created_at->format('M j, Y H:i') . "\n";
        
        if ($activeChat->agent) {
            $status .= "👤 Agent: " . $activeChat->agent->name . "\n";
        }
        
        $status .= "💬 Messages: " . $activeChat->messages()->count();

        $this->sendMessage($chatId, $status);
    }

    private function endChat($chatId, $userId)
    {
        $user = User::whereJsonContains('metadata->telegram_user_id', (string)$userId)->first();
        
        if (!$user) {
            $this->sendMessage($chatId, "❌ No active chats to end.");
            return;
        }

        $activeChat = $user->chatRooms()
            ->whereIn('status', [ChatRoom::STATUS_WAITING, ChatRoom::STATUS_ACTIVE])
            ->whereJsonContains('metadata->telegram_chat_id', (string)$chatId)
            ->first();

        if (!$activeChat) {
            $this->sendMessage($chatId, "❌ No active chats to end.");
            return;
        }

        $activeChat->closeChat();

        // Send system message
        ChatMessage::create([
            'chat_room_id' => $activeChat->id,
            'sender_id' => 0,
            'sender_type' => ChatMessage::SENDER_SYSTEM,
            'message' => 'Chat ended by user via Telegram',
            'message_type' => ChatMessage::TYPE_SYSTEM
        ]);

        $this->sendMessage($chatId, "✅ Chat ended successfully. Thank you for contacting us!\n\nUse /support to start a new chat anytime.");
    }

    private function findOrCreateUser($telegramUserId, $telegramUser)
    {
        $user = User::whereJsonContains('metadata->telegram_user_id', (string)$telegramUserId)->first();

        if (!$user) {
            $name = trim(($telegramUser['first_name'] ?? '') . ' ' . ($telegramUser['last_name'] ?? ''));
            $username = $telegramUser['username'] ?? null;

            $user = User::create([
                'name' => $name ?: 'Telegram User',
                'email' => $telegramUserId . '@telegram.temp',
                'status' => 'active',
                'metadata' => [
                    'telegram_user_id' => (string)$telegramUserId,
                    'telegram_username' => $username,
                    'telegram_first_name' => $telegramUser['first_name'] ?? null,
                    'telegram_last_name' => $telegramUser['last_name'] ?? null,
                    'source' => 'telegram',
                    'created_via_telegram' => true
                ]
            ]);
        }

        return $user;
    }

    private function findOrCreateChatRoom($user, $telegramChatId)
    {
        $chatRoom = ChatRoom::where('user_id', $user->id)
            ->whereIn('status', [ChatRoom::STATUS_WAITING, ChatRoom::STATUS_ACTIVE])
            ->whereJsonContains('metadata->telegram_chat_id', (string)$telegramChatId)
            ->first();

        if (!$chatRoom) {
            $chatRoom = ChatRoom::create([
                'user_id' => $user->id,
                'subject' => 'Telegram Support',
                'department' => 'telegram',
                'priority' => ChatRoom::PRIORITY_MEDIUM,
                'status' => ChatRoom::STATUS_WAITING,
                'last_message_at' => now(),
                'metadata' => [
                    'telegram_chat_id' => (string)$telegramChatId,
                    'platform' => 'telegram'
                ]
            ]);
        }

        return $chatRoom;
    }

    private function getMessageType($message)
    {
        if (isset($message['photo'])) return ChatMessage::TYPE_IMAGE;
        if (isset($message['document'])) return ChatMessage::TYPE_FILE;
        if (isset($message['voice']) || isset($message['audio'])) return ChatMessage::TYPE_FILE;
        if (isset($message['video'])) return ChatMessage::TYPE_FILE;
        return ChatMessage::TYPE_TEXT;
    }

    private function sendAutoReply($chatId)
    {
        $message = "🤖 Thank you for contacting us!\n\n";
        $message .= "Your message has been received and forwarded to our support team. ";
        $message .= "An agent will respond to you as soon as possible.\n\n";
        $message .= "💡 You can use /status to check your chat status anytime.";

        $this->sendMessage($chatId, $message);
    }

    public function sendMessage($chatId, $text, $replyMarkup = null)
    {
        try {
            $data = [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML'
            ];

            if ($replyMarkup) {
                $data['reply_markup'] = json_encode($replyMarkup);
            }

            $response = Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", $data);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram send message error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendMessageToChat(Request $request, $chatRoomId)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $chatRoom = ChatRoom::findOrFail($chatRoomId);
        
        if (!isset($chatRoom->metadata['telegram_chat_id'])) {
            return response()->json(['error' => 'Not a Telegram chat'], 400);
        }

        $telegramChatId = $chatRoom->metadata['telegram_chat_id'];
        
        $success = $this->sendMessage($telegramChatId, $request->message);

        if ($success) {
            $message = ChatMessage::create([
                'chat_room_id' => $chatRoom->id,
                'sender_id' => auth('admin')->id(),
                'sender_type' => ChatMessage::SENDER_ADMIN,
                'message' => $request->message,
                'message_type' => ChatMessage::TYPE_TEXT,
                'metadata' => [
                    'platform' => 'telegram',
                    'telegram_chat_id' => $telegramChatId
                ]
            ]);

            $chatRoom->update(['last_message_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => $message->load('sender')
            ]);
        }

        return response()->json(['error' => 'Failed to send Telegram message'], 500);
    }

    public function setWebhook(Request $request)
    {
        $webhookUrl = route('telegram.webhook');
        
        $response = Http::post("https://api.telegram.org/bot{$this->botToken}/setWebhook", [
            'url' => $webhookUrl,
            'secret_token' => $this->webhookToken
        ]);

        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'message' => 'Webhook set successfully',
                'webhook_url' => $webhookUrl
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => 'Failed to set webhook',
            'response' => $response->json()
        ], 500);
    }
}
