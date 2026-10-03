<script>
    (function () {
        // Global Barcode Scanner Listener for Index / Filter / Report / Table Pages
        let barcodeBuffer = '';
        let lastKeyTime = 0;
        const SCANNER_MAX_DELAY = 60; // Max ms between keypresses for a hardware barcode scanner

        document.addEventListener('keydown', function (e) {
            // Ignore on POS / Cart / Invoice create pages with dedicated barcode handling
            if (document.querySelector('.product_search, #product_search, #barcodeScanner, .pos-scanner-input, #payment_form')) {
                return;
            }

            // Ignore if modifier keys (Ctrl, Alt, Meta) are held down
            if (e.ctrlKey || e.altKey || e.metaKey) {
                return;
            }

            const currentTime = Date.now();
            const timeDiff = currentTime - lastKeyTime;
            lastKeyTime = currentTime;

            // If a dedicated POS line-item product search is present and focused (e.g. invoice create / purchase create), let it handle POS input naturally
            const activeEl = document.activeElement;
            const isPosSearchFocused = activeEl && (
                activeEl.id === 'product_search' || 
                activeEl.id === 'barcodeScanner' || 
                activeEl.classList.contains('pos-scanner-input') || 
                activeEl.classList.contains('product_search') ||
                activeEl.closest('#payment_form')
            );

            if (isPosSearchFocused) {
                return;
            }

            // Detect 'Enter' key signaling end of scan
            if (e.key === 'Enter' || e.keyCode === 13) {
                // If buffer has accumulated fast keystrokes (at least 2 chars)
                if (barcodeBuffer.length >= 2) {
                    const scannedCode = barcodeBuffer.trim();
                    barcodeBuffer = '';

                    // Find primary target input on current index / report / listing page
                    const targetInput = findBarcodeTargetInput();

                    if (targetInput) {
                        e.preventDefault();
                        e.stopPropagation();

                        targetInput.value = scannedCode;
                        
                        // Focus target input
                        targetInput.focus();

                        // If DataTables filter input is found
                        if (targetInput.classList.contains('dataTables-filter-input') || (targetInput.parentElement && targetInput.parentElement.classList.contains('dataTables_filter')) || targetInput.closest('.dataTables_filter')) {
                            if (window.$ && window.$.fn.dataTable) {
                                var dt = window.$(targetInput).closest('.dataTables_wrapper').find('table').DataTable();
                                if (dt) {
                                    dt.search(scannedCode).draw();
                                    return;
                                }
                            }
                            targetInput.dispatchEvent(new Event('input', { bubbles: true }));
                            targetInput.dispatchEvent(new Event('keyup', { bubbles: true }));
                            return;
                        }

                        // If inside a GET filter form, automatically submit to filter the page!
                        const form = targetInput.closest('form');
                        if (form) {
                            const method = (form.getAttribute('method') || form.method || '').toUpperCase();
                            if (method === 'GET') {
                                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                                if (submitBtn) {
                                    submitBtn.click();
                                } else {
                                    form.submit();
                                }
                            } else {
                                targetInput.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                        } else {
                            targetInput.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }
                }
                barcodeBuffer = '';
                return;
            }

            // Single printable character check
            if (e.key && e.key.length === 1) {
                if (timeDiff > SCANNER_MAX_DELAY && barcodeBuffer.length > 0) {
                    // Reset buffer if delay was too long (manual slow typing)
                    barcodeBuffer = '';
                }
                barcodeBuffer += e.key;
            }
        }, true);

        // Helper function to find the appropriate search / barcode input on the page
        function findBarcodeTargetInput() {
            // If the user already focused a visible input field, prefer it
            const activeEl = document.activeElement;
            if (activeEl && activeEl.tagName === 'INPUT' && (activeEl.type === 'text' || activeEl.type === 'search') && isElementVisible(activeEl)) {
                return activeEl;
            }

            // 1. Explicitly marked input
            const explicit = document.querySelector('input[data-barcode-input]:not([type="hidden"]):not([disabled])');
            if (explicit && isElementVisible(explicit)) return explicit;

            // 2. Named barcode input
            const byBarcode = document.querySelector('input[name="barcode"]:not([type="hidden"]):not([disabled])');
            if (byBarcode && isElementVisible(byBarcode)) return byBarcode;

            // 3. Named search_keyword / search
            const byKeyword = document.querySelector('input[name="search_keyword"]:not([type="hidden"]):not([disabled]), input[name="search"]:not([type="hidden"]):not([disabled])');
            if (byKeyword && isElementVisible(byKeyword)) return byKeyword;

            // 4. Barcode filter class
            const byClass = document.querySelector('.barcode-filter-input:not([type="hidden"]):not([disabled])');
            if (byClass && isElementVisible(byClass)) return byClass;

            // 5. Index specific named inputs
            const specificSelectors = [
                'input[name="invoice_no"]:not([type="hidden"]):not([disabled])',
                'input[name="purchase_no"]:not([type="hidden"]):not([disabled])',
                'input[name="claim_no"]:not([type="hidden"]):not([disabled])',
                'input[name="service_no"]:not([type="hidden"]):not([disabled])',
                'input[name="serial_no"]:not([type="hidden"]):not([disabled])',
                'input[name="phone_no"]:not([type="hidden"]):not([disabled])',
                'input[name="phone"]:not([type="hidden"]):not([disabled])'
            ];
            for (let sel of specificSelectors) {
                const el = document.querySelector(sel);
                if (el && isElementVisible(el)) return el;
            }

            // 6. DataTables search box
            const dtInput = document.querySelector('.dataTables_filter input');
            if (dtInput && isElementVisible(dtInput)) return dtInput;

            // 7. First visible text input inside any GET form on the page
            const getFormInput = document.querySelector('form[method="GET"] input[type="text"]:not([disabled]), form[method="get"] input[type="text"]:not([disabled]), form:not([method="POST"]) input[type="text"]:not([disabled])');
            if (getFormInput && isElementVisible(getFormInput)) return getFormInput;

            return null;
        }

        function isElementVisible(el) {
            return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length);
        }
    })();
</script>
