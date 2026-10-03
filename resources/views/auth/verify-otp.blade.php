<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="MultiShop POS & Inventory Management Software">
    <meta name="keywords" content="pos, inventory, multishop, software, admin">
    <meta name="author" content="Atrytech Information Technology">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>{{ get_setting('com_name') }} | Verify OTP</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ (!empty(get_setting('system_icon')))?url('uploads/logo/'.get_setting('system_icon')):url('backend/images/no_images.png') }}">

    <!-- Tailwind & Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- ToastMagic CSS -->
    {!! ToastMagic::styles() !!}
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-100 p-4 relative overflow-x-hidden font-sans">

    <!-- Background Image -->
    <div class="fixed inset-0 z-0">
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
                {{ __('Security Verification') }}
            </h2>
            <p class="text-gray-500 text-[14.5px] leading-relaxed px-4">
                {{ __('A security verification code has been sent to your Telegram account.') }}
            </p>
        </div>

        <form action="{{ url('/verify-otp') }}" method="POST" class="w-full space-y-5 mt-4" id="otpForm">
            @csrf

            <!-- OTP Input Field -->
            <div class="space-y-2">
                <label for="otp" class="text-[12px] font-semibold text-gray-700 ml-4 flex justify-center">{{ __('Enter 6-Digit OTP') }}</label>
                <div class="relative group flex justify-center">
                    <input
                        type="text"
                        name="otp"
                        id="otp"
                        placeholder="000000"
                        required
                        autofocus
                        maxlength="6"
                        pattern="\d{6}"
                        class="w-48 text-center py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-800 placeholder-gray-300 outline-none focus:outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-500/10 focus:ring-offset-0 transition-all duration-300 ease-in-out text-2xl font-bold tracking-[0.5em] pl-[0.5em]"
                        autocomplete="one-time-code"
                        inputmode="numeric"
                    >
                </div>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                id="verifyBtn"
                class="w-full py-3 bg-[#FF5C00] hover:bg-[#e65300] text-white text-sm font-bold rounded-full shadow-lg shadow-orange-500/30 transform active:scale-95 transition-all duration-300 ease-in-out mt-2 cursor-pointer flex items-center justify-center space-x-2"
            >
                <span id="btnText">{{ __('Verify & Log In') }}</span>
                <div id="btnLoader" class="hidden">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </button>
            
            <div class="text-center mt-3">
                <a href="{{ url('/') }}" class="text-[12px] text-gray-500 hover:text-orange-600 transition-colors">
                    {{ __('Back to login') }}
                </a>
            </div>
        </form>
    </div>

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

                // Status Session
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
        document.getElementById('otpForm').addEventListener('submit', function() {
            const btn = document.getElementById('verifyBtn');
            const text = document.getElementById('btnText');
            const loader = document.getElementById('btnLoader');
            
            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-not-allowed');
            text.innerText = "{{ __('Verifying...') }}";
            loader.classList.remove('hidden');
            
            document.body.style.cursor = 'wait';
        });

        // Numeric input only helper
        document.getElementById('otp').addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>

</html>
