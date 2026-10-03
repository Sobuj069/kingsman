<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    /**
     * Send a message to the configured Telegram chat.
     *
     * @param string $message
     * @return bool
     */
    public static function sendMessage(string $message): bool
    {
        $token = config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN');
        $chatId = config('services.telegram.chat_id') ?: env('TELEGRAM_CHAT_ID');

        if (!$token || !$chatId) {
            Log::warning('Telegram bot token or chat ID is not configured in services config or .env');
            return false;
        }

        try {
            $url = "https://api.telegram.org/bot{$token}/sendMessage";
            $response = Http::post($url, [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('Telegram API error: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('Telegram API exception: ' . $e->getMessage());
            return false;
        }
    }
}
