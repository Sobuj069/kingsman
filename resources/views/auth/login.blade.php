<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="MultiShop POS & Inventory Management Software">
    <meta name="keywords" content="pos, inventory, multishop, software, admin">
    <meta name="author" content="Atrytech Information Technology">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>{{ get_setting('com_name') }} | Log in</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ (!empty(get_setting('system_icon')))?url('uploads/logo/'.get_setting('system_icon')):url('backend/images/no_images.png') }}">

    <!-- Tailwind & Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- ToastMagic CSS -->
    {!! ToastMagic::styles() !!}
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-100 p-4 relative overflow-x-hidden font-sans">

    <!-- Background Image -->
    <div class="fixed inset-0 z-0 animate-fade-in">
        <img
            src="{{ (!empty(get_setting('login_bg')))?url('uploads/logo/'.get_setting('login_bg')):url('backend/images/multishop_bg.png') }}"
            alt="MultiShop POS & Inventory Background"
            class="w-full h-full object-cover"
            style="filter: blur(1.5px); transform: scale(1.02);"
        />
        <div class="absolute inset-0 bg-black/20"></div>
    </div>

    <!-- Login Card -->
    <div class="w-full max-w-[450px] bg-white rounded-xl shadow-2xl z-10 p-5 md:p-6 relative overflow-hidden flex flex-col items-center animate-zoom-in">

        <!-- Logo -->
        <div class="-mt-5 animate-slide-up delay-100">
            <a href="{{ url('/') }}" class="block">
                <img
                    class="h-16 w-auto object-contain"
                    src="{{ (!empty(get_setting('system_logo')))?url('uploads/logo/'.get_setting('system_logo')):url('backend/images/fastLogo.jpeg') }}"
                    alt="{{ get_setting('com_name') ?? 'Logo' }}"
                >
            </a>
        </div>

        <!-- Header Text -->
        <div class="text-center animate-slide-up delay-200">
            <h2 class="text-2xl font-bold text-gray-900 mb-1">
                {{ __('MultiShop POS & Inventory Admin') }}
            </h2>
            <p class="text-gray-500 text-[14.5px] leading-relaxed px-4">
                {{ __('Manage your sales, stock, and multi-shop operations with ease and precision.') }}
            </p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="w-full space-y-4 animate-slide-up delay-300" style="opacity: 0">
            @csrf

            <!-- Email Field -->
            <div class="space-y-1">
                <label for="email" class="text-[12px] font-semibold text-gray-700 ml-4">{{ __('Admin Email') }}</label>
                <div class="relative group">
                    <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-orange-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </span>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ env('APP_MODE') == 'demo' ? 'admin@fastit.com' : old('email') }}"
                        placeholder="{{ __('Enter your email') }}"
                        required
                        autofocus
                        class="w-full pl-12 pr-6 py-3 bg-gray-50 border border-gray-200 rounded-full text-gray-800 placeholder-gray-400 outline-none focus:outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 focus:ring-offset-0 transition-all duration-300 ease-in-out text-sm"
                    >
                </div>
            </div>

            <!-- Password Field -->
            <div class="space-y-1">
                <label for="passwordInput" class="text-[12px] font-semibold text-gray-700 ml-4">{{ __('Password') }}</label>
                <div class="relative group">
                    <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-orange-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </span>
                    <input
                        type="password"
                        name="password"
                        id="passwordInput"
                        value="{{ env('APP_MODE') == 'demo' ? '12345678' : '' }}"
                        placeholder="••••••••"
                        required
                        class="w-full pl-12 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-full text-gray-800 placeholder-gray-400 outline-none focus:outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 focus:ring-offset-0 transition-all duration-300 ease-in-out text-sm"
                    >
                    <button type="button" onclick="togglePassword()" class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-500 transition-colors focus:outline-none cursor-pointer">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye">
                            <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Remember Me + Forgot Password -->
            <div class="flex items-center justify-between px-4">
                <div class="flex items-center space-x-2 cursor-pointer group">
                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        class="w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-0 focus:ring-offset-0 cursor-pointer"
                    >
                    <label for="remember" class="text-[12px] text-gray-500 cursor-pointer group-hover:text-gray-700">{{ __('Remember me') }}</label>
                </div>
                <a href="https://wa.me/8801901166585" target="_blank" class="text-orange-600 font-semibold text-[12px] hover:text-orange-500 transition-colors">{{ __('Forgot password?') }}</a>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                id="loginBtn"
                class="w-full py-3 bg-[#FF5C00] hover:bg-[#e65300] text-white text-sm font-bold rounded-full shadow-lg shadow-orange-500/30 transform active:scale-95 transition-all duration-300 ease-in-out mt-2 cursor-pointer flex items-center justify-center space-x-2"
            >
                <span id="btnText">{{ __('Log In to Dashboard') }}</span>
                <div id="btnLoader" class="hidden">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </button>
        </form>

        @if (env('APP_MODE')=='demo')
            <div class="mt-4 text-center w-full">
                <p class="mb-0 mt-3 text-center" style="font-size: 25px; font-weight: bold; font-family: cursive;">CALL FOR ORDER</p>
                <p class="mb-0 mt-1 text-center"><span style="font-size: 20px; font-weight: bold; font-family: cursive;">01784-159071</span></p>
            </div>
        @endif

        <!-- Social Links -->
        <div class="mt-4 text-center w-full animate-slide-up delay-400">
            <div class="flex items-center justify-center space-x-2 sm:space-x-4 pt-3">

                <!-- WhatsApp -->
                <a href="https://wa.me/8801901166585" target="_blank"
                    class="px-2.5 sm:px-5 py-1.5 sm:py-2 rounded-full bg-green-50 text-green-600 flex items-center justify-center space-x-1 sm:space-x-2 font-semibold text-[11px] sm:text-[14px] hover:bg-green-500 hover:text-white transition-all duration-300 transform hover:-translate-y-1 shadow-sm hover:shadow-green-500/30 whitespace-nowrap"
                    title="{{ __('WhatsApp Support') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                    </svg>
                    <span>{{ __('Support') }}</span>
                </a>

                <!-- Facebook -->
                <a href="https://www.facebook.com/fastit.com.bd" target="_blank"
                    class="px-2.5 sm:px-5 py-1.5 sm:py-2 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center space-x-1 sm:space-x-2 font-semibold text-[11px] sm:text-[14px] hover:bg-blue-600 hover:text-white transition-all duration-300 transform hover:-translate-y-1 shadow-sm hover:shadow-blue-500/30 whitespace-nowrap"
                    title="{{ __('Facebook Page') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                    </svg>
                    <span>{{ __('Facebook') }}</span>
                </a>

                <!-- Website -->
                <a href="https://fastit.com.bd/" target="_blank"
                    class="px-2.5 sm:px-5 py-1.5 sm:py-2 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center space-x-1 sm:space-x-2 font-semibold text-[11px] sm:text-[14px] hover:bg-[#FF5C00] hover:text-white transition-all duration-300 transform hover:-translate-y-1 shadow-sm hover:shadow-orange-500/30 whitespace-nowrap"
                    title="{{ __('Visit Website') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                        <path d="M2 12h20"/>
                    </svg>
                    <span>{{ __('Website') }}</span>
                </a>
            </div>
        </div>

    </div>

    <!-- Toggle Password Script -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById("passwordInput");
            const eyeIcon = document.getElementById("eyeIcon");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.innerHTML = '<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/>';
            } else {
                passwordInput.type = "password";
                eyeIcon.innerHTML = '<path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0z"/><circle cx="12" cy="12" r="3"/>';
            }
        }
    </script>

    <!-- ToastMagic JS -->
    <script src="{{ asset('backend') }}/js/jquery.min.js"></script>
    {!! ToastMagic::scripts() !!}

    @if ($errors->any() || session('success') || session('status') || session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const toastMagic = new ToastMagic();
                
                // Validation Errors
                @foreach ($errors->all() as $error)
                    toastMagic.error("{{ $error }}");
                @endforeach

                // Success Session
                @if(session('success'))
                    toastMagic.success("{{ session('success') }}");
                @endif

                // Status Session (Logout specific)
                @if(session('status'))
                    toastMagic.info("{{ session('status') }}");
                @endif

                // Error Session
                @if(session('error'))
                    toastMagic.error("{{ session('error') }}");
                @endif
            });
        </script>
    @endif
    <!-- Form Loading Script -->
    <script>
        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            const text = document.getElementById('btnText');
            const loader = document.getElementById('btnLoader');
            
            // Start Loading State
            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-not-allowed');
            text.innerText = "{{ __('Logging in...') }}";
            loader.classList.remove('hidden');
            
            // Smooth transition for the body
            document.body.style.cursor = 'wait';
        });
    </script>
</body>

</html>
