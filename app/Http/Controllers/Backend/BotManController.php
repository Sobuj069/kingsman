<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

use BotMan\BotMan\Cache\LaravelCache;

class BotManController extends Controller
{
    public function handle()
    {
        // Load the web driver
        DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);

        $config = [];
        $botman = BotManFactory::create($config, new LaravelCache());

        // Basic Greeting
        $botman->hears('hi|hello|হ্যালো|hey', function (BotMan $bot) {
            $bot->reply('হ্যালো! আমি Fast-IT চ্যাটবট। আমাকে "menu" লিখে মেসেজ দিন।');
        });

        // Interactive Menu
        $botman->hears('menu|মেনু|help', function (BotMan $bot) {
            $question = Question::create('আপনি কী দেখতে বা জানতে চান?')
                ->fallback('দুঃখিত, বুঝতে পারিনি।')
                ->callbackId('main_menu')
                ->addButtons([
                    Button::create('আজকের সেলস')->value('sales'),
                    Button::create('মোট কাস্টমার')->value('customer'),
                    Button::create('মালামালের স্টক')->value('stock'),
                ]);

            $bot->ask($question, function ($answer, $conversation) {
                if ($answer->isInteractiveMessageReply()) {
                    if ($answer->getValue() === 'sales') {
                        $sales = \App\Models\Invoice::whereDate('created_at', today())->sum('total_amount');
                        $conversation->say('আজকের মোট সেলস: ' . number_format($sales, 2) . ' টাকা।');
                    } elseif ($answer->getValue() === 'customer') {
                        $customers = \App\Models\Customer::count();
                        $conversation->say('সিস্টেমে মোট ' . $customers . ' জন কাস্টমার আছেন।');
                    } elseif ($answer->getValue() === 'stock') {
                        $conversation->say('কোন প্রোডাক্টের স্টক জানতে চান? নাম লিখুন (যেমন: ল্যাপটপ বা laptop):');
                    }
                } else {
                    // Fallback for manual text input instead of button click
                    if ($answer->getText() === 'stock') {
                         $conversation->say('কোন প্রোডাক্টের স্টক জানতে চান? নাম লিখুন:');
                    } else {
                         $conversation->say('দুঃখিত, দয়া করে বাটনগুলোতে ক্লিক করুন।');
                    }
                }
            });
        });

        // Catch-all Product Search
        $botman->fallback(function (BotMan $bot) {
            $message = strtolower($bot->getMessage()->getText());
            $product = \App\Models\Product::where('name', 'LIKE', '%' . $message . '%')->first();
            
            if ($product) {
                $bot->reply("হ্যাঁ, '{$product->name}' এর স্টক আছে: {$product->main_qty} পিস।");
            } else {
                $bot->reply("আমি '{$message}' নামে কোনো প্রোডাক্ট খুঁজে পাইনি। দয়া করে সঠিক নাম লিখুন অথবা 'menu' লিখুন।");
            }
        });

        $botman->listen();
    }
}
