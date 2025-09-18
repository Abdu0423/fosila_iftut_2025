<?php

namespace App\Broadcasting;

use App\Models\Chat;
use App\Models\User;

class ChatChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user, int $chatId): array|bool
    {
        // Проверяем, что пользователь участвует в чате
        $chat = Chat::find($chatId);
        
        if (!$chat) {
            return false;
        }

        // Проверяем, что пользователь является участником чата
        return $chat->users()->where('user_id', $user->id)->exists();
    }
}