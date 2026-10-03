<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyCacheConfig
{
    /**
     * Handle cache configuration and session consistency.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $currentDomain = strtolower($request->getHost());
        $httpHost = strtolower($request->getHttpHost());
        $isLocalhost = in_array($currentDomain, ['localhost', '127.0.0.1', '::1']);

        // Allow disabling license check if explicitly set to 'no' in .env
        if (env('APP_LICENSE_CHECK') === 'no') {
            return $next($request);
        }

        if (!$isLocalhost || env('APP_LICENSE_CHECK') === 'yes') {

            // Handle Web Activation Form Submission
            if ($request->isMethod('post') && $request->has('activate_license_key')) {
                $submittedKey = trim($request->input('activate_license_key'));
                if ($this->validateKeyPayload($submittedKey, $currentDomain, $errorMsg, $httpHost)) {
                    $this->saveLicenseToEnv($submittedKey);
                    \Illuminate\Support\Facades\Cache::forget('license_valid_' . str_replace('.', '_', $currentDomain));
                    \Illuminate\Support\Facades\Cache::forget('license_valid_' . str_replace(['.', ':'], '_', $httpHost));
                    try {
                        \Illuminate\Support\Facades\Artisan::call('config:clear');
                        \Illuminate\Support\Facades\Artisan::call('cache:clear');
                    } catch (\Exception $e) {}
                    header("Location: " . url('/login'));
                    exit;
                } else {
                    $this->renderLockScreen(
                        'Activation Failed',
                        $errorMsg ?: 'The entered license key is invalid for this domain.',
                        $currentDomain,
                        'Activation Failed',
                        $submittedKey
                    );
                }
            }

            $cacheKey = 'license_valid_' . str_replace('.', '_', $currentDomain);
            $licenseKey = env('LICENSE_KEY');

            if (empty($licenseKey)) {
                \Illuminate\Support\Facades\Cache::forget($cacheKey);
                $this->renderLockScreen(
                    'Software Activation Required',
                    'This copy of the software is not activated. Please enter the valid License Key for domain (' . $currentDomain . ') below to activate.',
                    $currentDomain,
                    'Activation Required'
                );
            }

            if (\Illuminate\Support\Facades\Cache::get($cacheKey) !== 'valid') {
                if (!$this->validateKeyPayload($licenseKey, $currentDomain, $errorMsg, $httpHost)) {
                    $this->renderLockScreen(
                        'Software Activation Required',
                        $errorMsg,
                        $currentDomain,
                        'Activation Required'
                    );
                }

                \Illuminate\Support\Facades\Cache::put($cacheKey, 'valid', 86400);
            }

            $licenseData = json_decode(base64_decode(explode('.', env('LICENSE_KEY'))[0]), true);
            if ($licenseData) {
                $expiryDate = $licenseData['expires_at'] ?? '';
                $planType = $licenseData['plan_type'] ?? 'monthly';
                $today = date('Y-m-d');
                if ($expiryDate !== 'lifetime') {
                    $daysLeft = (int)ceil((strtotime($expiryDate) - strtotime($today)) / 86400);
                    $showWarning = false;
                    if ($planType === 'monthly' && $daysLeft <= 7 && $daysLeft > 0) {
                        $showWarning = true;
                    } elseif ($planType === 'yearly' && $daysLeft <= 30 && $daysLeft > 0) {
                        $showWarning = true;
                    }

                    if ($showWarning) {
                        $sessionKey = 'license_warn_' . date('Y-m-d') . '_' . md5($request->ip());
                        $warnCount = \Illuminate\Support\Facades\Cache::get($sessionKey, 0);
                        if ($warnCount < 5) {
                            \Illuminate\Support\Facades\Cache::put($sessionKey, $warnCount + 1, 86400);
                            $request->attributes->set('show_license_warning_days', $daysLeft);
                        }
                    }
                }
            }
        }

        $response = $next($request);

        // Warning banner injection check
        if ($request->attributes->has('show_license_warning_days')) {
            $daysLeft = $request->attributes->get('show_license_warning_days');
            
            if ($response instanceof \Illuminate\Http\Response && str_contains($response->headers->get('Content-Type'), 'text/html')) {
                $content = $response->getContent();
                
                $bannerHtml = <<<HTML
<div id="license-renewal-banner" style="background: linear-gradient(135deg, #eab308, #ca8a04); color: #000000; text-align: center; padding: 12px 20px; font-weight: 700; font-size: 14px; position: sticky; top: 0; z-index: 999999; display: flex; align-items: center; justify-content: center; gap: 10px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-bottom: 1px solid rgba(0,0,0,0.1); font-family: 'Plus Jakarta Sans', sans-serif;">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    <span>Attention: Your software license is expiring in {$daysLeft} days. Please renew your license key to prevent system lock.</span>
    <a href="https://wa.me/880960101362" target="_blank" style="background: #000000; color: #ffffff; padding: 6px 12px; border-radius: 8px; font-size: 12px; text-decoration: none; font-weight: 600; transition: all 0.2s ease; margin-left: 10px;">Renew Now</a>
</div>
HTML;
                $content = preg_replace('/<body[^>]*>/i', '$0' . $bannerHtml, $content, 1);
                $response->setContent($content);
            }
        }

        return $response;
    }

    private function validateKeyPayload($licenseKey, $currentDomain, &$errorMsg = null, $httpHost = ''): bool
    {
        if (empty($licenseKey)) {
            $errorMsg = 'License key is missing.';
            return false;
        }

        $parts = explode('.', $licenseKey);
        if (count($parts) !== 2) {
            $errorMsg = 'The license key format is corrupt or incorrect.';
            return false;
        }

        $encodedData = $parts[0];
        $encodedSignature = $parts[1];
        $jsonData = base64_decode($encodedData);
        $signature = base64_decode($encodedSignature);

        if (!$jsonData || !$signature) {
            $errorMsg = 'The license key encoding is invalid.';
            return false;
        }

        $masterSecret = config('app.key') ?: 'FAST_IT_POS_SECRET_MASTER_KEY_2026';
        $expectedHmacSig = hash_hmac('sha256', $jsonData, $masterSecret);
        $isAuthentic = hash_equals($expectedHmacSig, $signature) ? 1 : 0;

        if ($isAuthentic !== 1) {
            $publicKey = "-----BEGIN PUBLIC KEY-----\n" .
                "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAqjpHYeheWXMkQdwsbcqF\n" .
                "sg+PMPEKx3/WGElFe6oWht343Ku3jViDkrVouUcypQ/kpOYCpwl9OkkPw36tbO4I\n" .
                "E7HHny+X6gTS49mvLgR2qFYdmp+Qz6JueZCRme/9MH/zfRzFcKRLDn06NSD2Jdur\n" .
                "3/ZbCnn3QJz8H50TV34N24tZYaEm9jDZ7vCslhGZidP4NrkwAiJpwy0hkDbRKLDw\n" .
                "YXVqghuU/TfDNIVnCinKouVY0pBAfC/9EwVGGg218EfBM67CZSTUlf3lMEXLIspa\n" .
                "oRgwFJ1NW0Eyh7ITi9KSwZf0xsnX7g0cKpQDsvjkKkoXBpHBJ/bbirXy9Z0OQptW\n" .
                "8QIDAQAB\n" .
                "-----END PUBLIC KEY-----";
            $isAuthentic = openssl_verify($jsonData, $signature, $publicKey, OPENSSL_ALGO_SHA256);
        }

        if ($isAuthentic !== 1) {
            $errorMsg = 'Invalid digital signature. License key signature check failed.';
            return false;
        }

        $licenseData = json_decode($jsonData, true);
        if (!$licenseData) {
            $errorMsg = 'License data could not be parsed.';
            return false;
        }

        $allowedDomain = strtolower(trim($licenseData['domain'] ?? ''));
        $allowedHostOnly = explode(':', $allowedDomain)[0];
        $expiryDate = $licenseData['expires_at'] ?? '';

        $isLocalhost = in_array($currentDomain, ['localhost', '127.0.0.1', '::1']);

        // Normalize www prefix for seamless matching
        $normAllowed = preg_replace('/^www\./i', '', $allowedHostOnly);
        $normCurrent = preg_replace('/^www\./i', '', $currentDomain);

        $domainMatched = false;
        if ($allowedDomain === '*' || $allowedDomain === 'all') {
            $domainMatched = true;
        } elseif ($allowedDomain === $currentDomain || ($httpHost && $allowedDomain === $httpHost)) {
            $domainMatched = true;
        } elseif ($allowedHostOnly === $currentDomain || $normAllowed === $normCurrent) {
            $domainMatched = true;
        } elseif ($isLocalhost && in_array($allowedHostOnly, ['localhost', '127.0.0.1', '::1'])) {
            $domainMatched = true;
        } elseif (strpos($allowedDomain, '*.') === 0) {
            $parentDomain = substr($allowedDomain, 2);
            $parentHostOnly = explode(':', $parentDomain)[0];
            if ($currentDomain === $parentHostOnly || (strlen($currentDomain) > strlen($parentHostOnly) && substr($currentDomain, -strlen($parentHostOnly) - 1) === '.' . $parentHostOnly)) {
                $domainMatched = true;
            }
        }

        if (!$domainMatched) {
            $errorMsg = "This copy of the software is licensed for '$allowedDomain' and is not authorized to run on '$currentDomain'.";
            return false;
        }

        $today = date('Y-m-d');
        if ($expiryDate !== 'lifetime' && $today > $expiryDate) {
            $errorMsg = "Your subscription period ended on $expiryDate. Please renew your software license.";
            return false;
        }

        return true;
    }

    private function saveLicenseToEnv($licenseKey)
    {
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $content = file_get_contents($envPath);
            if (str_contains($content, 'LICENSE_KEY=')) {
                $content = preg_replace('/LICENSE_KEY=.*/', 'LICENSE_KEY="' . $licenseKey . '"', $content);
            } else {
                $content .= "\nLICENSE_KEY=\"" . $licenseKey . "\"\n";
            }

            if (!str_contains($content, 'APP_LICENSE_CHECK=')) {
                $content .= "APP_LICENSE_CHECK=\"yes\"\n";
            } else {
                $content = preg_replace('/APP_LICENSE_CHECK=.*/', 'APP_LICENSE_CHECK="yes"', $content);
            }
            file_put_contents($envPath, $content);
        }
    }

    /**
     * Render system diagnostic lock screen.
     */
    private function renderLockScreen($title, $description, $domain, $status, $enteredKey = '')
    {
        $csrfToken = csrf_token();
        $enteredKeyEscaped = htmlspecialchars($enteredKey);

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #0b1329; color: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
        .card { background: rgba(30, 41, 59, 0.85); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 24px; padding: 40px; max-width: 520px; width: 100%; text-align: center; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
        .icon { width: 64px; height: 64px; background: rgba(249, 115, 22, 0.15); border-radius: 20px; display: inline-flex; align-items: center; justify-content: center; color: #f97316; margin-bottom: 20px; border: 1px solid rgba(249, 115, 22, 0.3); }
        h1 { font-size: 24px; font-weight: 700; margin-bottom: 10px; color: #ffffff; letter-spacing: -0.5px; }
        p { font-size: 14px; color: #94a3b8; line-height: 1.6; margin-bottom: 24px; }
        .info-box { background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 16px; padding: 16px; margin-bottom: 24px; text-align: left; }
        .info-item { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px; }
        .info-item:last-child { margin-bottom: 0; }
        .info-label { color: #64748b; }
        .info-value { color: #cbd5e1; font-weight: 600; }
        
        .activation-form { margin-bottom: 24px; text-align: left; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 8px; }
        .form-input { width: 100%; padding: 12px 16px; background: #0f172a; border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 12px; color: #38bdf8; font-size: 13px; font-family: monospace; transition: all 0.2s; margin-bottom: 12px; }
        .form-input:focus { outline: none; border-color: #f97316; box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.2); }
        
        .activate-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%; background: linear-gradient(135deg, #f97316, #ea580c); color: #ffffff; padding: 14px 24px; border-radius: 12px; font-weight: 700; font-size: 15px; border: none; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 4px 14px rgba(249, 115, 22, 0.3); }
        .activate-btn:hover { background: linear-gradient(135deg, #ea580c, #c2410c); transform: translateY(-1px); }
        
        .support-link { display: inline-block; margin-top: 16px; color: #94a3b8; font-size: 13px; text-decoration: none; transition: color 0.2s; }
        .support-link:hover { color: #f97316; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <h1>{$title}</h1>
        <p>{$description}</p>
        <div class="info-box">
            <div class="info-item">
                <span class="info-label">Domain:</span>
                <span class="info-value">{$domain}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Status:</span>
                <span class="info-value" style="color: #ef4444;">{$status}</span>
            </div>
        </div>

        <form method="POST" action="" class="activation-form">
            <input type="hidden" name="_token" value="{$csrfToken}">
            <label class="form-label">Enter License Key to Activate:</label>
            <input type="text" name="activate_license_key" value="{$enteredKeyEscaped}" class="form-control form-input" placeholder="eyJkb21haW4iOi..." required autocomplete="off">
            <button type="submit" class="activate-btn">
                ⚡ Activate Software
            </button>
        </form>

        <a href="https://wa.me/880960101362" target="_blank" class="support-link">
            Need a License Key? Contact Developer / Support
        </a>
    </div>
</body>
</html>
HTML;
        echo $html;
        exit;
    }
}