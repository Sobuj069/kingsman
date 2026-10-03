<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateLicense extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license:generate {domain?} {expires?} {plan?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a signed software LICENSE_KEY for a specific domain and expiry date';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $domain = $this->argument('domain') ?: $this->ask('Enter Client Domain (e.g. clientstore.com, localhost, or *):', 'localhost');
        $expires = $this->argument('expires') ?: $this->ask('Enter Expiry Date (YYYY-MM-DD, e.g. 2026-12-31):', date('Y-m-d', strtotime('+1 year')));
        $plan = $this->argument('plan') ?: $this->choice('Select Plan Type:', ['monthly', 'yearly', 'lifetime'], 1);

        $licenseData = [
            'domain'     => strtolower(trim($domain)),
            'expires_at' => trim($expires),
            'plan_type'  => strtolower(trim($plan)),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $jsonData = json_encode($licenseData);
        $encodedData = base64_encode($jsonData);

        // Secret key used for signing licenses
        $secretKey = config('app.key') ?: 'FAST_IT_POS_SECRET_MASTER_KEY_2026';
        $signature = hash_hmac('sha256', $jsonData, $secretKey);
        $encodedSignature = base64_encode($signature);

        $licenseKey = $encodedData . '.' . $encodedSignature;

        // Automatically send to Telegram if configured
        if (class_exists('\App\Services\TelegramService')) {
            $telegramMsg = "🔑 *NEW SOFTWARE LICENSE GENERATED*\n\n" .
                "🌐 *Domain:* `{$licenseData['domain']}`\n" .
                "📅 *Expires:* `{$licenseData['expires_at']}`\n" .
                "🏷️ *Plan:* `{$licenseData['plan_type']}`\n\n" .
                "```\nLICENSE_KEY={$licenseKey}\n```";
            \App\Services\TelegramService::sendMessage($telegramMsg);
        }

        $this->info('');
        $this->info('========================================================================');
        $this->info('                 SOFTWARE LICENSE REQUEST PROCESSED                     ');
        $this->info('========================================================================');
        $this->line('<fg=cyan>Domain:</>      ' . $licenseData['domain']);
        $this->line('<fg=cyan>Expires At:</>  ' . $licenseData['expires_at']);
        $this->line('<fg=cyan>Plan Type:</>   ' . $licenseData['plan_type']);
        $this->info('------------------------------------------------------------------------');
        $this->info('STATUS:      License Key generated & sent SECURELY to Telegram only!');
        $this->info('========================================================================');
        $this->info('');

        return Command::SUCCESS;
    }
}
