<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Chat;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    public function broadcastOn()
    {
        // Mỗi user sẽ có 1 kênh riêng, ví dụ: chat.2
        return new PrivateChannel('App.Models.User.' . $this->chat->reciver_id);
    }

    public function broadcastWith()
    {
        // Dữ liệu gửi về client
        return [
            'id' => $this->chat->id,
            'message' => $this->chat->message,
            'sender_id' => $this->chat->sender_id,
            'reciver_id' => $this->chat->reciver_id,
            'created_at' => $this->chat->created_at->toDateTimeString(),
        ];
    }
}