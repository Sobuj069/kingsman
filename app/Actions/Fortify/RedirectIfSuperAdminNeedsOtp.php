<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RedirectIfSuperAdminNeedsOtp
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return mixed
     */
    public function handle(Request $request, $next)
    {
        $username = $request->input('email'); // Fortify uses 'email' by default
        $password = $request->input('password');

        $user = User::where('email', $username)->first();

        // Check if the user exists, password is correct, and role is Super Admin (role_id == 2)
        if ($user && Hash::check($password, $user->password) && $user->role_id == 2) {
            
            // Check if user is active (status == 1 or status == 3)
            if ($user->status == 3) {
                return $next($request);
            }

            if ($user->status != 1) {
                return redirect()->back()->withErrors([
                    'email' => __('Your account is inactive.'),
                ]);
            }

            // Generate a random 6-digit OTP
            $otp = rand(100000, 999999);
            
            // Save in session
            session([
                'superadmin_otp' => $otp,
                'superadmin_temp_user_id' => $user->id,
                'otp_expires_at' => now()->addMinutes(5),
            ]);

            // Format message
            $time = now()->format('Y-m-d H:i:s');
            $ip = $request->ip();
            $message = "⚠️ *Malta POS - Super Admin Login Attempt*\n\n"
                     . "🔑 Your Login OTP: *{$otp}*\n"
                     . "⏱️ Valid for: *5 minutes*\n"
                     . "📅 Time: `{$time}`\n"
                     . "🌐 IP: `{$ip}`\n\n"
                     . "If this wasn't you, please secure your credentials immediately.";

            // Send via Telegram
            TelegramService::sendMessage($message);

            // Redirect to OTP verification page
            return redirect()->route('otp.verify');
        }

        // For non-superadmin users or incorrect credentials, pass to next pipeline step (AttemptToAuthenticate)
        return $next($request);
    }
}
