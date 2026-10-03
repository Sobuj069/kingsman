@extends('backend.layouts.master')
@section('section-title', __('User Profile'))
@section('page-title', __('Profile'))

@section('content')
    <div class="flex md:hidden items-center justify-center p-1 bg-slate-100 dark:bg-slate-800/50 rounded-2xl mt-3 mb-4 border border-slate-200 dark:border-slate-700 mx-2">
        <button onclick="switchTab('basic-info', this)" class="tab-btn active-tab flex-1 py-2.5 px-4 rounded-xl text-[12px] font-black uppercase tracking-wider transition-all flex items-center justify-center gap-2 focus:outline-none focus:ring-0">
            <i class="feather icon-user text-sm"></i> {{ __('Info') }}
        </button>
        <button onclick="switchTab('security-settings', this)" class="tab-btn flex-1 py-2.5 px-4 rounded-xl text-[12px] font-black uppercase tracking-wider transition-all text-slate-500 flex items-center justify-center gap-2 focus:outline-none focus:ring-0">
            <i class="feather icon-lock text-sm"></i> {{ __('Security') }}
        </button>
    </div>

    <style>
        .tab-btn { -webkit-tap-highlight-color: transparent !important; }
        .tab-btn:focus, .tab-btn:active, .tab-btn:visited { outline: none !important; box-shadow: none !important; border: none !important; -webkit-tap-highlight-color: transparent !important; }
        .tight-gap > [class*="col-"] { padding-left: 8px !important; padding-right: 8px !important; }
        .tight-gap { margin-left: -8px !important; margin-right: -8px !important; }
    </style>

    <div class="row tight-gap pt-1">
        {{-- Profile Infomation Card --}}
        <div id="basic-info-pane" class="col-md-6 mb-4 tab-pane active">
            <div class="card card_style border-0 shadow-sm overflow-hidden text-left">
                <div class="card-header flex items-center gap-3 bg-slate-50/50 dark:bg-slate-800/50 py-4 px-4 border-b border-slate-100 dark:border-slate-700">
                    <div class="w-10 h-10 flex items-center justify-center bg-blue-500/10 text-blue-500 rounded-xl">
                        <i class="feather icon-user text-lg"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-[15px] font-black text-slate-700 dark:text-slate-200 uppercase tracking-widest">{{ __('Basic Information') }}</h5>
                        <p class="mb-0 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mt-0.5">{{ __('Manage your identity details') }}</p>
                    </div>
                </div>
                <div class="card-body p-4 pt-1">
                    <form action="{{ route('profile.update') }}" method="POST" class="mt-3">
                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}">
                        
                        <div class="form-group mb-4">
                            <label class="text-[12px] font-bold text-slate-600 dark:text-slate-400 mb-2 block uppercase tracking-wide">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                            <div class="relative">
                                <i class="feather icon-edit-2 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input class="form-control !pl-11 h-12 bg-slate-50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700 focus:border-blue-400 rounded-xl transition-all" type="text" name="name" value="{{ $data->name }}" placeholder="Enter full name">
                            </div>
                            @if($errors->has('name'))
                                <div class="errors text-red-500 text-[11px] mt-1 font-bold italic">{{ $errors->first('name') }}</div>
                            @endif
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-[12px] font-bold text-slate-600 dark:text-slate-400 mb-2 block uppercase tracking-wide">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                            <div class="relative">
                                <i class="feather icon-mail absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input class="form-control !pl-11 h-12 bg-slate-50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700 focus:border-blue-400 rounded-xl transition-all" type="email" value="{{ $data->email }}" name="email" placeholder="email@example.com">
                            </div>
                            @if($errors->has('email'))
                                <div class="errors text-red-500 text-[11px] mt-1 font-bold italic">{{ $errors->first('email') }}</div>
                            @endif
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-[12px] font-bold text-slate-600 dark:text-slate-400 mb-2 block uppercase tracking-wide">{{ __('Phone Number') }} <span class="text-danger">*</span></label>
                            <div class="relative">
                                <i class="feather icon-phone absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input type="text" class="form-control !pl-11 h-12 bg-slate-50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700 focus:border-blue-400 rounded-xl transition-all" name="phone" value="{{ $data->phone }}" placeholder="+880 1XXX XXXXXX">
                            </div>
                            @if($errors->has('phone'))
                                <div class="errors text-red-500 text-[11px] mt-1 font-bold italic">{{ $errors->first('phone') }}</div>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary !bg-blue-600 !text-white w-full h-12 flex items-center justify-center gap-2 rounded-xl text-[13px] font-black uppercase tracking-widest shadow-lg shadow-blue-500/30 hover:-translate-y-0.5 transition-all active:scale-[0.98] border-0">
                            <i class="feather icon-check-circle"></i> {{ __('Save Changes') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Password Change Card --}}
        <div id="security-settings-pane" class="col-md-6 mb-4 tab-pane">
            <div class="card card_style border-0 shadow-sm overflow-hidden text-left">
                <div class="card-header flex items-center gap-3 bg-slate-50/50 dark:bg-slate-800/50 py-4 px-4 border-b border-slate-100 dark:border-slate-700">
                    <div class="w-10 h-10 flex items-center justify-center bg-orange-500/10 text-orange-500 rounded-xl">
                        <i class="feather icon-lock text-lg"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 text-[15px] font-black text-slate-700 dark:text-slate-200 uppercase tracking-widest">{{ __('Security Settings') }}</h5>
                        <p class="mb-0 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mt-0.5">{{ __('Ensure your account security') }}</p>
                    </div>
                </div>
                <div class="card-body p-4 pt-1">
                    <form action="{{ route('profile.password') }}" method="POST" class="mt-3">
                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}">
                        
                        <div class="form-group mb-4">
                            <label class="text-[12px] font-bold text-slate-600 dark:text-slate-400 mb-2 block uppercase tracking-wide">{{ __('Current Password') }} <span class="text-danger">*</span></label>
                            <div class="relative">
                                <i class="feather icon-shield absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input class="form-control !pl-11 h-12 bg-slate-50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700 focus:border-blue-400 rounded-xl transition-all" type="password" name="password" id="current_password" placeholder="••••••••">
                                <button type="button" onclick="togglePassword('current_password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-500 transition-colors">
                                    <i class="feather icon-eye text-sm"></i>
                                </button>
                            </div>
                            @if($errors->has('password'))
                                <div class="errors text-red-500 text-[11px] mt-1 font-bold italic">{{ $errors->first('password') }}</div>
                            @endif
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-[12px] font-bold text-slate-600 dark:text-slate-400 mb-2 block uppercase tracking-wide">{{ __('New Password') }} <span class="text-danger">*</span></label>
                            <div class="relative">
                                <i class="feather icon-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input class="form-control !pl-11 h-12 bg-slate-50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700 focus:border-blue-400 rounded-xl transition-all" type="password" name="new_password" id="new_password" placeholder="Create new password">
                                <button type="button" onclick="togglePassword('new_password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-500 transition-colors">
                                    <i class="feather icon-eye text-sm"></i>
                                </button>
                            </div>
                            @if($errors->has('new_password'))
                                <div class="errors text-red-500 text-[11px] mt-1 font-bold italic">{{ $errors->first('new_password') }}</div>
                            @endif
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-[12px] font-bold text-slate-600 dark:text-slate-400 mb-2 block uppercase tracking-wide">{{ __('Confirm New Password') }} <span class="text-danger">*</span></label>
                            <div class="relative">
                                <i class="feather icon-check-square absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input type="password" class="form-control !pl-11 h-12 bg-slate-50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700 focus:border-blue-400 rounded-xl transition-all" name="con_password" id="con_password" placeholder="Confirm new password">
                                <button type="button" onclick="togglePassword('con_password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-500 transition-colors">
                                    <i class="feather icon-eye text-sm"></i>
                                </button>
                            </div>
                            @if($errors->has('con_password'))
                                <div class="errors text-red-500 text-[11px] mt-1 font-bold italic">{{ $errors->first('con_password') }}</div>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-warning !bg-orange-500 !text-white w-full h-12 flex items-center justify-center gap-2 rounded-xl text-[13px] font-black uppercase tracking-widest shadow-lg shadow-orange-500/30 hover:-translate-y-0.5 transition-all active:scale-[0.98] border-0">
                            <i class="feather icon-lock"></i> {{ __('Update Security') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 767px) {
            .tab-pane { display: none; }
            .tab-pane.active { display: block; animation: fadeIn 0.3s ease-in-out; }
            .active-tab { background: white !important; box-shadow: 0 4px 12px rgba(0,0,0,0.08); color: #2563eb !important; border: 1px solid #e2e8f0; }
            body.dark-theme .active-tab { background: #334155 !important; color: #60a5fa !important; border-color: #475569 !important; box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    <script>
        function switchTab(paneId, btn) {
            // Hide all panes on mobile
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            // Show selected pane
            document.getElementById(paneId + '-pane').classList.add('active');
            
            // Toggle button styles
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('active-tab');
                b.classList.remove('text-blue-600');
                b.classList.add('text-slate-500');
            });
            btn.classList.add('active-tab');
            btn.classList.remove('text-slate-500');
        }

        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('icon-eye');
                icon.classList.add('icon-eye-off');
            } else {
                input.type = 'password';
                icon.classList.remove('icon-eye-off');
                icon.classList.add('icon-eye');
            }
        }
    </script>
@endsection
