<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    private $whatsappToken;
    private $whatsappPhoneId;
    private $verifyToken;

    public function __construct()
    {
        $this->whatsappToken = config('services.whatsapp.token');
        $this->whatsappPhoneId = config('services.whatsapp.phone_number_id');
        $this->verifyToken = config('services.whatsapp.verify_token');
    }

    public function webhook(Request $request)
    {
        // Verify webhook
        if ($request->hub_mode === 'subscribe' && 
            $request->hub_verify_token === $this->verifyToken) {
            return response($request->hub_challenge);
        }

        // Handle incoming messages
        $data = $request->all();
        
        if (isset($data['entry'][0]['changes'][0]['value']['messages'])) {
            foreach ($data['entry'][0]['changes'][0]['value']['messages'] as $message) {
                $this->handleIncomingMessage($message, $data['entry'][0]['changes'][0]['value']);
            }
        }

        return response()->json(['status' => 'success']);
    }

    private function handleIncomingMessage($message, $value)
    {
        try {
            $from = $message['from'];
            $messageText = $message['text']['body'] ?? '';
            $messageType = $message['type'];

            // Find or create user based on WhatsApp number
            $user = $this->findOrCreateUser($from, $value);
            
            // Find or create active chat room
            $chatRoom = $this->findOrCreateChatRoom($user, $from);

            // Create message record
            $chatMessage = ChatMessage::create([
                'chat_room_id' => $chatRoom->id,
                'sender_id' => $user->id,
                'sender_type' => ChatMessage::SENDER_USER,
                'message' => $messageText,
                'message_type' => $this->mapWhatsAppMessageType($messageType),
                'metadata' => [
                    'whatsapp_message_id' => $message['id'],
                    'whatsapp_from' => $from,
                    'platform' => 'whatsapp'
                ]
            ]);

            // Update chat room
            $chatRoom->update(['last_message_at' => now()]);

            // Send auto-reply if it's a new conversation
            if ($chatRoom->messages()->count() === 1) {
                $this->sendAutoReply($from);
            }

            // Notify agents
            $this->notifyAgents($chatRoom, $chatMessage);

        } catch (\Exception $e) {
            Log::error('WhatsApp message handling error: ' . $e->getMessage(), [
                'message' => $message,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function findOrCreateUser($whatsappNumber, $value)
    {
        // Try to find user by WhatsApp number in metadata
        $user = User::whereJsonContains('metadata->whatsapp_number', $whatsappNumber)->first();

        if (!$user) {
            // Get contact info from WhatsApp
            $contactName = $value['contacts'][0]['profile']['name'] ?? 'WhatsApp User';
            
            // Create new user
            $user = User::create([
                'name' => $contactName,
                'email' => $whatsappNumber . '@whatsapp.temp', // Temporary email
                'phone' => $whatsappNumber,
                'status' => 'active',
                'metadata' => [
                    'whatsapp_number' => $whatsappNumber,
                    'source' => 'whatsapp',
                    'created_via_whatsapp' => true
                ]
            ]);
        }

        return $user;
    }

    private function findOrCreateChatRoom($user, $whatsappNumber)
    {
        // Find active chat room for this WhatsApp number
        $chatRoom = ChatRoom::where('user_id', $user->id)
            ->whereIn('status', [ChatRoom::STATUS_WAITING, ChatRoom::STATUS_ACTIVE])
            ->whereJsonContains('metadata->whatsapp_number', $whatsappNumber)
            ->first();

        if (!$chatRoom) {
            $chatRoom = ChatRoom::create([
                'user_id' => $user->id,
                'subject' => 'WhatsApp Support',
                'department' => 'whatsapp',
                'priority' => ChatRoom::PRIORITY_MEDIUM,
                'status' => ChatRoom::STATUS_WAITING,
                'last_message_at' => now(),
                'metadata' => [
                    'whatsapp_number' => $whatsappNumber,
                    'platform' => 'whatsapp'
                ]
            ]);
        }

        return $chatRoom;
    }

    private function mapWhatsAppMessageType($whatsappType)
    {
        $mapping = [
            'text' => ChatMessage::TYPE_TEXT,
            'image' => ChatMessage::TYPE_IMAGE,
            'document' => ChatMessage::TYPE_FILE,
            'audio' => ChatMessage::TYPE_FILE,
            'video' => ChatMessage::TYPE_FILE
        ];

        return $mapping[$whatsappType] ?? ChatMessage::TYPE_TEXT;
    }

    private function sendAutoReply($to)
    {
        $message = "Hello! 👋 Thank you for contacting us via WhatsApp. An agent will be with you shortly. How can we help you today?";
        
        $this->sendWhatsAppMessage($to, $message);
    }

    public function sendMessage(Request $request, $chatRoomId)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $chatRoom = ChatRoom::findOrFail($chatRoomId);
        
        // Ensure this is a WhatsApp chat
        if (!isset($chatRoom->metadata['whatsapp_number'])) {
            return response()->json(['error' => 'Not a WhatsApp chat'], 400);
        }

        $whatsappNumber = $chatRoom->metadata['whatsapp_number'];
        
        // Send message via WhatsApp API
        $success = $this->sendWhatsAppMessage($whatsappNumber, $request->message);

        if ($success) {
            // Create message record
            $message = ChatMessage::create([
                'chat_room_id' => $chatRoom->id,
                'sender_id' => auth('admin')->id(),
                'sender_type' => ChatMessage::SENDER_ADMIN,
                'message' => $request->message,
                'message_type' => ChatMessage::TYPE_TEXT,
                'metadata' => [
                    'platform' => 'whatsapp',
                    'whatsapp_to' => $whatsappNumber
                ]
            ]);

            $chatRoom->update(['last_message_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => $message->load('sender')
            ]);
        }

        return response()->json(['error' => 'Failed to send WhatsApp message'], 500);
    }

    private function sendWhatsAppMessage($to, $message)
    {
        try {
            $response = Http::withToken($this->whatsappToken)
                ->post("https://graph.facebook.com/v17.0/{$this->whatsappPhoneId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'text',
                    'text' => [
                        'body' => $message
                    ]
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WhatsApp send message error: ' . $e->getMessage());
            return false;
        }
    }

    private function notifyAgents($chatRoom, $message)
    {
        // Broadcast to admin dashboard
        broadcast(new \App\Events\MessageSent($message))->toOthers();
    }

    public function sendTemplate(Request $request, $chatRoomId)
    {
        $request->validate([
            'template_name' => 'required|string',
            'language' => 'required|string|default:en',
            'parameters' => 'array'
        ]);

        $chatRoom = ChatRoom::findOrFail($chatRoomId);
        $whatsappNumber = $chatRoom->metadata['whatsapp_number'];

        try {
            $response = Http::withToken($this->whatsappToken)
                ->post("https://graph.facebook.com/v17.0/{$this->whatsappPhoneId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $whatsappNumber,
                    'type' => 'template',
                    'template' => [
                        'name' => $request->template_name,
                        'language' => [
                            'code' => $request->language
                        ],
                        'components' => $request->parameters ? [
                            [
                                'type' => 'body',
                                'parameters' => $request->parameters
                            ]
                        ] : []
                    ]
                ]);

            if ($response->successful()) {
                // Create message record
                ChatMessage::create([
                    'chat_room_id' => $chatRoom->id,
                    'sender_id' => auth('admin')->id(),
                    'sender_type' => ChatMessage::SENDER_ADMIN,
                    'message' => "Sent template: {$request->template_name}",
                    'message_type' => ChatMessage::TYPE_SYSTEM,
                    'metadata' => [
                        'platform' => 'whatsapp',
                        'template_name' => $request->template_name,
                        'template_language' => $request->language
                    ]
                ]);

                return response()->json(['success' => true]);
            }

            return response()->json(['error' => 'Failed to send template'], 500);
        } catch (\Exception $e) {
            Log::error('WhatsApp template send error: ' . $e->getMessage());
            return response()->json(['error' => 'Template send failed'], 500);
        }
    }
}
