<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Use the global ToastMagic instance provided by the package
        if (typeof ToastMagic !== 'undefined') {
            window.toastMagic = new ToastMagic();
            
            @if(session('success'))
                window.toastMagic.success("{{ session('success') }}");
            @endif
            @if(session('error'))
                window.toastMagic.error("{{ session('error') }}");
            @endif
            @if(session('status'))
                window.toastMagic.info("{{ session('status') }}");
            @endif
            @if(session('warning'))
                window.toastMagic.warning("{{ session('warning') }}");
            @endif

            // Validation errors
            @if(isset($errors) && method_exists($errors, 'any') && $errors->any())
                @foreach($errors->all() as $error)
                    window.toastMagic.error("{{ $error }}");
                @endforeach
            @endif
        } else {
            console.error('ToastMagic is not defined. Please check if scripts are loaded correctly.');
        }
    });
</script>
