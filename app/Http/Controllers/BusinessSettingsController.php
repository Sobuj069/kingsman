<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Artisan;

class BusinessSettingsController extends Controller
{
    public function index()
    {
        return view('backend.pages.setting');
    }

    public function update(Request $request)
    {
        if ($request->has('types') && is_array($request->types)) {
            foreach ($request->types as $key => $type) {
                $val = $request[$type] ?? null;
                if (is_array($val)) {
                    if ($type === 'showrooms_list') {
                        $val = array_values(array_filter($val, function ($item) {
                            return !empty($item['name']) || !empty($item['address']);
                        }));
                    }
                    $val = json_encode($val, JSON_UNESCAPED_UNICODE);
                }

                $business_settings = BusinessSetting::where('type', $type)->first();
                if ($business_settings != null) {
                    $business_settings->value = $val;
                } else {
                    $business_settings = new BusinessSetting;
                    $business_settings->type = $type;
                    $business_settings->value = $val;
                }
                $business_settings->save();
            }
        }
    
        // Handle System Icon Upload
        if ($request->hasFile('system_icon')) {
            $this->uploadAndResizeImage($request->file('system_icon'), 'system_icon', 556, 244);
        }
    
        // Handle System Logo Upload
        if ($request->hasFile('system_logo')) {
            $this->uploadAndResizeImage($request->file('system_logo'), 'system_logo', 556, 244);
        }

        // Handle Login Background Upload
        if ($request->hasFile('login_bg')) {
            $this->uploadAndResizeImage($request->file('login_bg'), 'login_bg', 1920, 1080);
        }
    
        // Clear cache
        \Illuminate\Support\Facades\Cache::forget('business_settings');
        \Illuminate\Support\Facades\Cache::forget('frontend_global_categories');
        \Illuminate\Support\Facades\Cache::forget('frontend_global_showrooms');
        \Illuminate\Support\Facades\Cache::flush();
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
    
        session()->flash('success', 'Successfully System Settings Updated!');
        return redirect()->back();
    }
    
    /**
     * Upload and resize an image using Intervention Image v2
     */
    private function uploadAndResizeImage($image, $type, $width, $height)
    {
        $business_settings = BusinessSetting::where('type', $type)->first();
        $ext = strtolower($image->getClientOriginalExtension());
        $imgName = date('YmdHis') . '_' . rand(100, 999) . '.' . ($ext ?: 'png');

        $destinationPath = public_path('uploads/logo/');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // Check if extension is ico/svg or try Intervention Image with fallback
        if (in_array($ext, ['ico', 'svg'])) {
            $image->move($destinationPath, $imgName);
        } else {
            try {
                // Resize and save image
                $resizedImage = Image::make($image->getRealPath());
                $resizedImage->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio(); // Maintain aspect ratio
                    $constraint->upsize(); // Prevent upsizing
                })->save($destinationPath . $imgName);
            } catch (\Throwable $e) {
                // Fallback to direct file upload if GD cannot decode image format (e.g. .ico)
                $image->move($destinationPath, $imgName);
            }
        }

        // Delete old image if exists
        if ($business_settings && !empty($business_settings->value) && file_exists($destinationPath . $business_settings->value)) {
            @unlink($destinationPath . $business_settings->value);
        }

        // Save new image name to database
        if ($business_settings) {
            $business_settings->value = $imgName;
            $business_settings->save();
        } else {
            BusinessSetting::create([
                'type' => $type,
                'value' => $imgName
            ]);
        }
    }
}
