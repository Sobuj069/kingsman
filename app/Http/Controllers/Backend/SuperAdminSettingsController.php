<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SuperAdminSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->check() && auth()->user()->role && in_array(auth()->user()->role->slug, ['superadmin', 'super-admin'])) {
                return $next($request);
            }
            abort(403, 'Unauthorized action.');
        });
    }

    public function index()
    {
        $hiddenModules = json_decode(get_setting('hidden_sidebar_modules'), true) ?: [];
        
        return view('backend.pages.superadmin.settings', compact('hiddenModules'));
    }

    public function updateEnv(Request $request)
    {
        $keys = ['APP_SC', 'APP_IMEI', 'APP_WARRANTY', 'APP_VAT', 'APP_ONLINE', 'APP_REF_INV', 'APP_SERVICE', 'HIDE_ADMIN_BRANCH', 'APP_INSTALLMENT', 'APP_AUTOMOBILE', 'APP_AUTO_PRINT', 'APP_MOBILE_SCANNER', 'APP_LOYALTY', 'APP_COURIER_FRAUD_CHECK', 'APP_ZATCA', 'APP_QR_CODE', 'APP_CUSTOMER_PHONE_ONLY', 'HIDE_CUSTOMER_DATES', 'SHOW_COST_RATE_IN_POS', 'APP_POS_AUTO_FULL_VIEW', 'APP_LICENSE_CHECK', 'APP_DISCOUNT_GROUP', 'APP_SUB_CATEGORY', 'APP_INVOICE_NOTE', 'APP_BRANCH_SWITCH', 'APP_RACK', 'APP_UNIT'];

        
        $envUpdates = [];
        foreach ($keys as $key) {
            $envUpdates[$key] = $request->has($key) ? 'yes' : 'no';
        }

        $this->updateEnvFileBatch($envUpdates);

        try {
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
        } catch (\Exception $e) {
            // Ignore artisan clear error if dev server process reloads
        }

        session()->flash('success', 'Environment settings updated successfully.');
        return back();
    }

    public function updateSidebar(Request $request)
    {
        $hidden = $request->input('hidden_modules', []);
        
        $setting = BusinessSetting::where('type', 'hidden_sidebar_modules')->first();
        if ($setting != null) {
            $setting->value = json_encode($hidden);
        } else {
            $setting = new BusinessSetting;
            $setting->type = 'hidden_sidebar_modules';
            $setting->value = json_encode($hidden);
        }
        $setting->save();

        Cache::forget('business_settings');
        try {
            Artisan::call('cache:clear');
        } catch (\Exception $e) {}

        session()->flash('success', 'Sidebar customization updated successfully.');
        return back();
    }

    private function updateEnvFileBatch(array $updates)
    {
        $path = base_path('.env');

        if (!file_exists($path)) {
            return;
        }

        $content = file_get_contents($path);

        foreach ($updates as $key => $value) {
            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}=\"{$value}\"", $content);
            } else {
                $content .= "\n{$key}=\"{$value}\"";
            }
        }

        file_put_contents($path, $content);
    }
}
