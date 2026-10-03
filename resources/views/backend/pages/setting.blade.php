@extends('backend.layouts.master')

@section('content')
<div class="setting-page">
    <div class="setting-tabs">
        <button onclick="switchTab('business')" id="tab-btn-business" class="tab-btn active-tab">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="16" x="4" y="4" rx="2"/><path d="M9 22V2h6v20"/><path d="M8 12h8"/></svg>
            {{ __('Identity & branding') }}
        </button>
        <button onclick="switchTab('showrooms')" id="tab-btn-showrooms" class="tab-btn inactive-tab">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
            {{ __('Our Showrooms') }}
        </button>
        <button onclick="switchTab('operational')" id="tab-btn-operational" class="tab-btn inactive-tab">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
            {{ __('Operational') }}
        </button>
    </div> 

    <div class="setting-card">

        {{-- Identity Tab --}}
        <div id="tab-business" class="tab-content block">
            <form method="POST" action="{{ route('setting.update') }}" enctype="multipart/form-data">
                @csrf

                <p class="section-label">{{ __('Branding assets') }}</p>

                <div class="upload-row flex flex-col md:flex-row gap-6 md:gap-8">
                    <div class="upload-item">
                        <div class="upload-preview">
                            <img id="image_icon" src="{{ !empty(get_setting('system_icon')) ? url('uploads/logo/' . get_setting('system_icon')) : url('backend/images/favicon.png') }}">
                        </div>
                        <div class="upload-info">
                            <div class="upload-name">{{ __('System icon') }}</div>
                            <div class="upload-hint">{{ __('32×32 recommended') }}</div>
                            <label class="upload-btn">{{ __('Change icon') }}
                                <input type="file" name="system_icon" style="display:none" onchange="readURLIcon(this);">
                            </label>
                        </div>
                    </div>
                    <div class="upload-item">
                        <div class="upload-preview wide">
                            <img id="image_logo" src="{{ !empty(get_setting('system_logo')) ? url('uploads/logo/' . get_setting('system_logo')) : url('backend/images/no_images.png') }}">
                        </div>
                        <div class="upload-info">
                            <div class="upload-name">{{ __('System logo') }}</div>
                            <div class="upload-hint">{{ __('SVG or PNG, wide format') }}</div>
                            <label class="upload-btn">{{ __('Upload logo') }}
                                <input type="file" name="system_logo" style="display:none" onchange="readURLLogo(this);">
                            </label>
                        </div>
                    </div>
                    <div class="upload-item">
                        <div class="upload-preview wide">
                            <img id="image_login_bg" src="{{ !empty(get_setting('login_bg')) ? url('uploads/logo/' . get_setting('login_bg')) : url('backend/images/multishop_bg.png') }}">
                        </div>
                        <div class="upload-info">
                            <div class="upload-name">{{ __('Login Background') }}</div>
                            <div class="upload-hint">{{ __('1920×1080 recommended') }}</div>
                            <label class="upload-btn">{{ __('Upload BG') }}
                                <input type="file" name="login_bg" style="display:none" onchange="readURLLoginBg(this);">
                            </label>
                        </div>
                    </div>
                </div>

                <p class="section-label">{{ __('Store details') }}</p>

                <div class="field-grid grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="field">
                        <label>{{ __('Store name') }}</label>
                        <input type="hidden" name="types[]" value="com_name">
                        <input type="text" name="com_name" value="{{ get_setting('com_name') }}" placeholder="{{ __('e.g. My Store') }}">
                    </div>
                    <div class="field">
                        <label>{{ __('Email') }}</label>
                        <input type="hidden" name="types[]" value="com_email">
                        <input type="email" name="com_email" value="{{ get_setting('com_email') }}" placeholder="{{ __('contact@store.com') }}">
                    </div>
                    <div class="field">
                        <label>{{ __('Phone (Hotline / Call)') }}</label>
                        <input type="hidden" name="types[]" value="com_phone">
                        <input type="text" name="com_phone" value="{{ get_setting('com_phone') }}" placeholder="+8801...">
                    </div>
                    <div class="field">
                        <label>{{ __('WhatsApp Hotline Number') }}</label>
                        <input type="hidden" name="types[]" value="com_whatsapp">
                        <input type="text" name="com_whatsapp" value="{{ get_setting('com_whatsapp') }}" placeholder="{{ __('e.g. 01987258406 (leave empty to use Phone)') }}">
                    </div>
                    <div class="field">
                        <label>{{ __('Currency') }}</label>
                        <input type="hidden" name="types[]" value="com_currency">
                        <input type="text" name="com_currency" value="{{ get_setting('com_currency') }}" placeholder="{{ __('BDT') }}">
                    </div>
                    <div class="field col-span-1 md:col-span-2">
                        <label>{{ __('Address') }}</label>
                        <input type="hidden" name="types[]" value="com_address">
                        <textarea name="com_address" rows="2" placeholder="{{ __('Street, city, country') }}">{{ get_setting('com_address') }}</textarea>
                    </div>
                </div>

                <div class="footer-row">
                    <button type="submit" class="save-btn flex items-center justify-center gap-2">
                        <span class="btn-text">{{ __('Save identity') }}</span>
                        <span class="btn-loader hidden">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Our Showrooms Tab --}}
        <div id="tab-showrooms" class="tab-content hidden">
            <form method="POST" action="{{ route('setting.update') }}">
                @csrf
                <input type="hidden" name="types[]" value="showrooms_list">

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div>
                        <p class="section-label mb-1" style="margin-bottom: 2px;">{{ __('Our Physical Showrooms') }}</p>
                        <p class="text-xs text-gray-500">{{ __('Manage showroom outlets displayed across your website (Footer, About Us, Contact Us, etc.). This is completely independent from POS branches.') }}</p>
                    </div>
                    <button type="button" onclick="addShowroomRow()" class="showroom-add-btn" style="display: inline-flex; align-items: center; gap: 8px; background: #0f172a; color: #ffffff !important; font-size: 13px; font-weight: 600; padding: 9px 18px; border-radius: 8px; border: 1px solid #0f172a; cursor: pointer; box-shadow: 0 2px 6px rgba(15,23,42,0.25);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span style="color: #ffffff !important;">{{ __('+ Add New Showroom') }}</span>
                    </button>
                </div>

                <div id="showrooms-list-container" class="space-y-4 mb-4">
                    @php
                        $showroomsData = function_exists('get_frontend_showrooms') ? get_frontend_showrooms() : collect([]);
                    @endphp

                    @forelse($showroomsData as $index => $showroom)
                        <div class="showroom-card p-4 rounded-xl border border-gray-200 bg-white transition hover:border-gray-300 relative" data-index="{{ $index }}" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 14px;">
                            <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-200" style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 12px;">
                                <div class="flex items-center gap-2" style="display: flex; align-items: center; gap: 8px;">
                                    <span class="showroom-counter" style="width: 24px; height: 24px; border-radius: 50%; background: #fee2e2; color: #ef4444; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 11px;">{{ $loop->iteration }}</span>
                                    <span class="showroom-title-preview" style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #1e293b; letter-spacing: 0.05em;">{{ !empty($showroom->name) ? $showroom->name : 'Showroom #' . ($index + 1) }}</span>
                                </div>
                                <button type="button" onclick="removeShowroomCard(this)" class="showroom-remove-btn" style="display: inline-flex; align-items: center; gap: 4px; background: transparent; border: none; color: #ef4444; font-size: 12px; font-weight: 600; cursor: pointer; padding: 4px 8px; border-radius: 6px;">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    <span>{{ __('Remove') }}</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="field">
                                    <label class="text-xs font-semibold text-gray-700 block mb-1.5">{{ __('Showroom Name') }} <span class="text-red-500">*</span></label>
                                    <input type="text" name="showrooms_list[{{ $index }}][name]" value="{{ $showroom->name ?? '' }}" placeholder="{{ __('e.g. Robe Mirpur 2') }}" class="w-full text-xs showroom-name-input" oninput="updateShowroomTitle(this)" required>
                                </div>
                                <div class="field">
                                    <label class="text-xs font-semibold text-gray-700 block mb-1.5">{{ __('Phone / Mobile') }}</label>
                                    <input type="text" name="showrooms_list[{{ $index }}][phone]" value="{{ $showroom->phone ?? '' }}" placeholder="{{ __('e.g. 01987258406') }}" class="w-full text-xs">
                                </div>
                                <div class="field">
                                    <label class="text-xs font-semibold text-gray-700 block mb-1.5">{{ __('Tag / Badge (Optional)') }}</label>
                                    <input type="text" name="showrooms_list[{{ $index }}][shop_name]" value="{{ $showroom->shop_name ?? '' }}" placeholder="{{ __('e.g. Mirpur Branch or Shop #115') }}" class="w-full text-xs">
                                </div>
                                <div class="field md:col-span-3">
                                    <label class="text-xs font-semibold text-gray-700 block mb-1.5">{{ __('Showroom Address') }} <span class="text-red-500">*</span></label>
                                    <textarea name="showrooms_list[{{ $index }}][address]" rows="2" placeholder="{{ __('e.g. Shop #115, 1st Floor, Mirpur 2 Shopping Complex, Dhaka') }}" class="w-full text-xs" required>{{ $showroom->address ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div id="no-showrooms-notice" class="text-center py-10 px-4 border-2 border-dashed border-gray-200 rounded-2xl text-gray-400" style="padding: 30px; text-align: center; border: 2px dashed #cbd5e1; border-radius: 12px;">
                            <svg style="margin: 0 auto 10px; width: 32px; height: 32px; color: #94a3b8;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            <p class="text-xs font-medium" style="color: #64748b; margin-bottom: 12px;">{{ __('No showrooms added yet.') }}</p>
                            <button type="button" onclick="addShowroomRow()" class="showroom-add-btn" style="display: inline-flex; align-items: center; gap: 6px; background: #0f172a; color: #ffffff !important; font-size: 12px; font-weight: 600; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer;">
                                {{ __('+ Add First Showroom') }}
                            </button>
                        </div>
                    @endforelse
                </div>

                {{-- Bottom Add Button --}}
                <div class="mb-6">
                    <button type="button" onclick="addShowroomRow()" class="showroom-add-btn-large" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 12px; background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 10px; color: #334155; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>{{ __('+ Add Another Showroom') }}</span>
                    </button>
                </div>

                <div class="footer-row">
                    <button type="submit" class="save-btn flex items-center justify-center gap-2">
                        <span class="btn-text">{{ __('Save Showrooms') }}</span>
                        <span class="btn-loader hidden">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Operational Tab --}}
        <div id="tab-operational" class="tab-content hidden">
            <form method="POST" action="{{ route('setting.update') }}">
                @csrf

                <p class="section-label">{{ __('Print & display options') }}</p>

                <div class="field-grid grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="field">
                        <label>{{ __('Barcode style') }}</label>
                        <input type="hidden" name="types[]" value="pro_barcode">
                        <select name="pro_barcode" class="select2">
                            <option value="a4" {{ get_setting('pro_barcode') == 'a4' ? 'selected' : '' }}>{{ __('A4 sheet') }}</option>
                            <option value="single" {{ get_setting('pro_barcode') == 'single' ? 'selected' : '' }}>{{ __('Single thermal') }}</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>{{ __('Invoice branding') }}</label>
                        <input type="hidden" name="types[]" value="inv_logo">
                        <select name="inv_logo" class="select2">
                            <option value="name" {{ get_setting('inv_logo') == 'name' ? 'selected' : '' }}>{{ __('Name only') }}</option>
                            <option value="logo" {{ get_setting('inv_logo') == 'logo' ? 'selected' : '' }}>{{ __('Logo only') }}</option>
                            <option value="both" {{ get_setting('inv_logo') == 'both' ? 'selected' : '' }}>{{ __('Both') }}</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>{{ __('Invoice layout') }}</label>
                        <input type="hidden" name="types[]" value="inv_design">
                        <select name="inv_design" class="select2">
                            <option value="a4" {{ get_setting('inv_design') == 'a4' ? 'selected' : '' }}>{{ __('Standard A4') }}</option>
                            <option value="a5" {{ get_setting('inv_design') == 'a5' ? 'selected' : '' }}>{{ __('Standard A5') }}</option>
                            <option value="pos" {{ get_setting('inv_design') == 'pos' ? 'selected' : '' }}>{{ __('POS 80mm') }}</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>{{ __('Stock detail') }}</label>
                        <input type="hidden" name="types[]" value="inv_details">
                        <select name="inv_details" class="select2">
                            <option value="single" {{ get_setting('inv_details') == 'single' ? 'selected' : '' }}>{{ __('Single shop') }}</option>
                            <option value="branch" {{ get_setting('inv_details') == 'branch' ? 'selected' : '' }}>{{ __('Branch wise') }}</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>{{ __('VAT / Tax Status (VAT Include)') }}</label>
                        <input type="hidden" name="types[]" value="vat_include">
                        <select name="vat_include" class="select2">
                            <option value="no" {{ !is_vat_enabled() ? 'selected' : '' }}>{{ __('Exclude / Disabled (Hide VAT)') }}</option>
                            <option value="yes" {{ is_vat_enabled() ? 'selected' : '' }}>{{ __('Include / Enabled (Show VAT)') }}</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>{{ __('Invoice QR Code') }}</label>
                        <input type="hidden" name="types[]" value="inv_qr_code">
                        <select name="inv_qr_code" class="select2">
                            <option value="yes" {{ get_setting('inv_qr_code', 'yes') == 'yes' ? 'selected' : '' }}>{{ __('Show / Enabled') }}</option>
                            <option value="no" {{ get_setting('inv_qr_code', 'yes') == 'no' ? 'selected' : '' }}>{{ __('Hide / Disabled') }}</option>
                        </select>
                    </div>
                </div>

                @if(env('APP_COURIER_FRAUD_CHECK') == 'yes' && auth()->check() && auth()->user()->isSuperAdmin())
                <p class="section-label mt-6">{{ __('Steadfast Courier API Settings') }}</p>

                <div class="field-grid grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="field">
                        <label>{{ __('Steadfast API Key') }}</label>
                        <input type="hidden" name="types[]" value="steadfast_api_key">
                        <input type="text" name="steadfast_api_key" value="{{ get_setting('steadfast_api_key') ?: env('STEADFAST_API_KEY') }}" placeholder="{{ __('API Key') }}">
                    </div>
                    <div class="field">
                        <label>{{ __('Steadfast Secret Key') }}</label>
                        <input type="hidden" name="types[]" value="steadfast_secret_key">
                        <input type="password" name="steadfast_secret_key" value="{{ get_setting('steadfast_secret_key') ?: env('STEADFAST_SECRET_KEY') }}" placeholder="{{ __('Secret Key') }}">
                    </div>
                </div>

                <p class="section-label mt-6">{{ __('Pathao Courier API Settings') }}</p>

                <div class="field-grid grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="field">
                        <label>{{ __('Pathao Client ID') }}</label>
                        <input type="hidden" name="types[]" value="pathao_client_id">
                        <input type="text" name="pathao_client_id" value="{{ get_setting('pathao_client_id') ?: (env('PATHAO_CLIENT_ID') ?: 'jnegkRrewZ') }}" placeholder="{{ __('Client ID') }}">
                    </div>
                    <div class="field">
                        <label>{{ __('Pathao Client Secret') }}</label>
                        <input type="hidden" name="types[]" value="pathao_client_secret">
                        <input type="password" name="pathao_client_secret" value="{{ get_setting('pathao_client_secret') ?: env('PATHAO_CLIENT_SECRET') }}" placeholder="{{ __('Client Secret') }}">
                    </div>
                    <div class="field">
                        <label>{{ __('Pathao Username / Email') }}</label>
                        <input type="hidden" name="types[]" value="pathao_username">
                        <input type="text" name="pathao_username" value="{{ get_setting('pathao_username') ?: env('PATHAO_USERNAME') }}" placeholder="{{ __('Registered Email or Phone') }}">
                    </div>
                    <div class="field">
                        <label>{{ __('Pathao Password') }}</label>
                        <input type="hidden" name="types[]" value="pathao_password">
                        <input type="password" name="pathao_password" value="{{ get_setting('pathao_password') ?: env('PATHAO_PASSWORD') }}" placeholder="{{ __('Account Password') }}">
                    </div>
                    <div class="field col-span-1 md:col-span-2">
                        <label>{{ __('Pathao Secret Token (Optional)') }}</label>
                        <input type="hidden" name="types[]" value="pathao_secret_token">
                        <input type="text" name="pathao_secret_token" value="{{ get_setting('pathao_secret_token') ?: env('PATHAO_SECRET_TOKEN') }}" placeholder="{{ __('Secret Bearer Token if issued directly') }}">
                    </div>
                </div>

                <p class="section-label mt-6">{{ __('Custom Courier Fraud Checker API Settings') }}</p>

                <div class="field-grid grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="field">
                        <label>{{ __('Fraud API Base URL') }}</label>
                        <input type="hidden" name="types[]" value="courier_fraud_api_url">
                        <input type="text" name="courier_fraud_api_url" value="{{ get_setting('courier_fraud_api_url') }}" placeholder="{{ __('e.g. https://api.fraudchecker.xyz') }}">
                    </div>
                    <div class="field">
                        <label>{{ __('Fraud API Key') }}</label>
                        <input type="hidden" name="types[]" value="courier_fraud_api_key">
                        <input type="password" name="courier_fraud_api_key" value="{{ get_setting('courier_fraud_api_key') }}" placeholder="{{ __('API Authorization Key') }}">
                    </div>
                </div>
                @endif

                <div class="footer-row">
                    <button type="submit" class="save-btn flex items-center justify-center gap-2">
                        <span class="btn-text">{{ __('Apply settings') }}</span>
                        <span class="btn-loader hidden">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection

@push('js')
<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(function(c) {
            c.classList.add('hidden');
            c.classList.remove('block');
        });
        var targetTab = document.getElementById('tab-' + tabId);
        if (targetTab) {
            targetTab.classList.replace('hidden', 'block');
        }

        document.querySelectorAll('.tab-btn').forEach(function(b) {
            b.classList.remove('active-tab');
            b.classList.add('inactive-tab');
        });
        var targetBtn = document.getElementById('tab-btn-' + tabId);
        if (targetBtn) {
            targetBtn.classList.add('active-tab');
            targetBtn.classList.remove('inactive-tab');
        }

        if (window.history.replaceState) {
            window.history.replaceState(null, null, '#' + tabId);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.hash) {
            var hashTab = window.location.hash.replace('#', '');
            if (document.getElementById('tab-' + hashTab)) {
                switchTab(hashTab);
            }
        }
    });

    function readURLIcon(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image_icon').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function readURLLogo(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image_logo').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function readURLLoginBg(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image_login_bg').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function addShowroomRow() {
        var container = document.getElementById('showrooms-list-container');
        var emptyNotice = document.getElementById('no-showrooms-notice');
        if (emptyNotice) {
            emptyNotice.remove();
        }

        var newIndex = Date.now();
        var rowNum = container.querySelectorAll('.showroom-card').length + 1;

        var card = document.createElement('div');
        card.className = 'showroom-card p-4 rounded-xl border border-gray-200 bg-white transition hover:border-gray-300 relative';
        card.setAttribute('data-index', newIndex);
        card.style.cssText = 'background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 14px;';
        card.innerHTML = `
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-200" style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 12px;">
                <div class="flex items-center gap-2" style="display: flex; align-items: center; gap: 8px;">
                    <span class="showroom-counter" style="width: 24px; height: 24px; border-radius: 50%; background: #fee2e2; color: #ef4444; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 11px;">${rowNum}</span>
                    <span class="showroom-title-preview" style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #1e293b; letter-spacing: 0.05em;">New Showroom #${rowNum}</span>
                </div>
                <button type="button" onclick="removeShowroomCard(this)" class="showroom-remove-btn" style="display: inline-flex; align-items: center; gap: 4px; background: transparent; border: none; color: #ef4444; font-size: 12px; font-weight: 600; cursor: pointer; padding: 4px 8px; border-radius: 6px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    <span>Remove</span>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="field">
                    <label class="text-xs font-semibold text-gray-700 block mb-1.5">Showroom Name <span class="text-red-500">*</span></label>
                    <input type="text" name="showrooms_list[${newIndex}][name]" value="" placeholder="e.g. Robe Mirpur 2" class="w-full text-xs showroom-name-input" oninput="updateShowroomTitle(this)" required>
                </div>
                <div class="field">
                    <label class="text-xs font-semibold text-gray-700 block mb-1.5">Phone / Mobile</label>
                    <input type="text" name="showrooms_list[${newIndex}][phone]" value="" placeholder="e.g. 01987258406" class="w-full text-xs">
                </div>
                <div class="field">
                    <label class="text-xs font-semibold text-gray-700 block mb-1.5">Tag / Badge (Optional)</label>
                    <input type="text" name="showrooms_list[${newIndex}][shop_name]" value="" placeholder="e.g. Mirpur Branch or Shop #115" class="w-full text-xs">
                </div>
                <div class="field md:col-span-3">
                    <label class="text-xs font-semibold text-gray-700 block mb-1.5">Showroom Address <span class="text-red-500">*</span></label>
                    <textarea name="showrooms_list[${newIndex}][address]" rows="2" placeholder="e.g. Shop #115, 1st Floor, Mirpur 2 Shopping Complex, Dhaka" class="w-full text-xs" required></textarea>
                </div>
            </div>
        `;
        container.appendChild(card);
        reindexShowrooms();
        
        card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        var nameInput = card.querySelector('.showroom-name-input');
        if (nameInput) {
            nameInput.focus();
        }
    }

    function removeShowroomCard(btn) {
        var card = btn.closest('.showroom-card');
        if (card) {
            card.remove();
            reindexShowrooms();
        }
    }

    function updateShowroomTitle(input) {
        var card = input.closest('.showroom-card');
        if (card) {
            var preview = card.querySelector('.showroom-title-preview');
            var counter = card.querySelector('.showroom-counter');
            var num = counter ? counter.innerText : '1';
            preview.innerText = input.value.trim() ? input.value.trim() : ('Showroom #' + num);
        }
    }

    function reindexShowrooms() {
        var container = document.getElementById('showrooms-list-container');
        var cards = container.querySelectorAll('.showroom-card');
        cards.forEach(function(card, idx) {
            var counter = card.querySelector('.showroom-counter');
            if (counter) counter.innerText = (idx + 1);
            var input = card.querySelector('.showroom-name-input');
            var preview = card.querySelector('.showroom-title-preview');
            if (preview && (!input || !input.value.trim())) {
                preview.innerText = 'Showroom #' + (idx + 1);
            }
        });
    }
</script>
@endpush
