<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AIChatbotController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function chat(Request $request)
    {
        $message = $request->input('message');
        $history = Session::get('chat_history', []);

        // Dynamic Data Fetching for Specific Product
        $specificProductInfo = "";
        if (strlen($message) > 3) {
            $product = \App\Models\Product::where('name', 'LIKE', '%' . $message . '%')->first();
            if ($product) {
                $specificProductInfo = "\nDetailed Info for '{$product->name}': Stock: {$product->main_qty}, Price: {$product->price}, Purchase Price: {$product->purchase_price}.\n";
            }
        }

        // System Context with Real-time Data Summary
        $dataSummary = $this->gemini->getGlobalSummary();
        $context = "You are 'Fast-IT AI Assistant'. You have direct access to the POS database summary. 
        Current Business Data Snapshot:\n" . $dataSummary . $specificProductInfo . "\n
        Answer questions based on this data. If asked about stock, sales, expenses, bank, or recent activity, look at the snapshot. 
        Respond in Bengali. Be direct and helpful. Use previous conversation context if relevant.";

        // Build Messages array including history
        $messages = [['role' => 'system', 'content' => $context]];
        foreach ($history as $chat) {
            $messages[] = ['role' => 'user', 'content' => $chat['user']];
            $messages[] = ['role' => 'assistant', 'content' => $chat['ai']];
        }
        $messages[] = ['role' => 'user', 'content' => $message];

        try {
            // 1. Try Rule-Based Logic First for very common commands (Instant)
            // Note: Rule-based doesn't use history yet
            $reply = $this->gemini->generateResponse($message);
            if (!str_contains($reply, 'বুঝতে পারিনি')) {
                return response()->json(['success' => true, 'reply' => $reply]);
            }

            // 2. Use Groq AI (Ultra Fast)
            $groqKey = env('GROQ_API_KEY');
            if (!empty($groqKey)) {
                $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $groqKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->timeout(20) 
                    ->post("https://api.groq.com/openai/v1/chat/completions", [
                        'model' => 'llama-3.3-70b-versatile',
                        'messages' => $messages,
                        'temperature' => 0.7,
                    ]);

                if ($response->successful()) {
                    $reply = $response->json('choices.0.message.content');
                    if (!empty($reply)) {
                        // Update History
                        $history[] = ['user' => $message, 'ai' => $reply];
                        Session::put('chat_history', array_slice($history, -10));
                        return response()->json(['success' => true, 'reply' => $reply]);
                    }
                }
            }

            // 3. Fallback to Pollinations AI if Groq fails or no key
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->timeout(30) 
                ->post("https://text.pollinations.ai/", [
                    'messages' => $messages,
                    'model' => 'openai',
                    'jsonMode' => false
                ]);

            if ($response->successful()) {
                $reply = $response->body();
                
                if (empty($reply)) {
                    throw new \Exception('AI Response was empty.');
                }

                // Update History
                $history[] = ['user' => $message, 'ai' => $reply];
                Session::put('chat_history', array_slice($history, -10));

                return response()->json([
                    'success' => true,
                    'reply' => $reply
                ]);
            } else {
                throw new \Exception('AI Timeout or Connection Error.');
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'reply' => 'বট এই মুহূর্তে বিজি আছে। দয়া করে স্টক বা সেলস সম্পর্কে সরাসরি প্রশ্ন করুন।'
            ]);
        }
    }

    public function clearHistory()
    {
        Session::forget('chat_history');
        return response()->json(['success' => true]);
    }
}
