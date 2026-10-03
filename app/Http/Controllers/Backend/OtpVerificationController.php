<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpVerificationController extends Controller
{
    /**
     * Show the OTP verification page.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        if (!session()->has('superadmin_temp_user_id') || !session()->has('superadmin_otp')) {
            return redirect()->route('login')->withErrors([
                'email' => __('Session expired. Please log in again.'),
            ]);
        }

        return view('auth.verify-otp');
    }

    /**
     * Verify the submitted OTP code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $userId = session('superadmin_temp_user_id');
        $otpCode = session('superadmin_otp');
        $expiresAt = session('otp_expires_at');

        if (!$userId || !$otpCode || !$expiresAt) {
            return redirect()->route('login')->withErrors([
                'email' => __('Session expired or invalid. Please try logging in again.'),
            ]);
        }

        // Check if OTP is expired
        if (now()->greaterThan($expiresAt)) {
            session()->forget(['superadmin_otp', 'superadmin_temp_user_id', 'otp_expires_at']);
            return redirect()->route('login')->withErrors([
                'email' => __('The OTP code has expired. Please log in again.'),
            ]);
        }

        // Check if OTP is correct
        if ($request->otp == $otpCode) {
            // Retrieve user
            $user = User::find($userId);

            if ($user && $user->status == 1) {
                // Perform login
                Auth::login($user, $request->has('remember'));

                // Log login activity
                logActivity('Login', "Super Admin '{$user->name}' logged in successfully via Telegram OTP", $user);

                // Clear temporary session data
                session()->forget(['superadmin_otp', 'superadmin_temp_user_id', 'otp_expires_at']);

                // Redirect to dashboard
                return redirect()->intended(config('fortify.home'));
            }
        }

        return back()->withErrors([
            'otp' => __('The provided OTP code is incorrect.'),
        ]);
    }
}
