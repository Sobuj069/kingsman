@extends('backend.layouts.master')
@section('page-title', __('Stock Audit (স্টক অডিট)'))

@push('css')
<style>
    .audit-container {
        padding: 20px;
        background: #f8fafc;
        min-height: calc(100vh - 100px);
    }
    .badge-match {
        background-color: #10b981;
        color: #fff;
    }
    .badge-short {
        background-color: #ef4444;
        color: #fff;
    }
    .badge-surplus {
        background-color: #3b82f6;
        color: #fff;
    }
    .scan-box-pulse {
        animation: pulse-ring 1.5s infinite;
    }
    @keyframes pulse-ring {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
</style>
@endpush

@section('content')
<div class="audit-container">
    <form action="{{ route('stock-audit.store') }}" method="POST" id="auditForm">
        @csrf
        
        <!-- Header & Action Bar -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6 bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ __('Stock Audit') }}
                </h1>
                <p class="text-sm text-slate-500 mt-0.5">{{ __('Scan or search products to enter physical stock and compare with system stock') }}</p>
            </div>
            
            <div class="flex items-center gap-3">
                @if($userBranchId == 1)
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">{{ __('Audit Branch') }}</label>
                    <select name="branch_id" id="branchIdSelect" class="form-select text-sm rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 py-2">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ $activeBranchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" name="branch_id" id="branchIdSelect" value="{{ $activeBranchId }}">
                @endif

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">{{ __('Audit Date') }}</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-input text-sm rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                
                <div class="self-end">
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl transition shadow-md hover:shadow-lg flex items-center gap-2">
                        <i class="feather icon-save"></i>
                        {{ __('Save Stock Audit') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Real-time Stats Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3.5 mb-6">
            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
                    <i class="feather icon-package"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-semibold uppercase text-slate-400 block truncate">{{ __('Total Items') }}</span>
                    <h3 class="text-2xl font-black text-slate-800" id="statTotalItems">0</h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-indigo-100 shadow-sm flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
                    <i class="feather icon-layers"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-semibold uppercase text-indigo-600 block truncate">{{ __('Total Stock Qty') }}</span>
                    <h3 class="text-2xl font-black text-indigo-700" id="statTotalSystemStock">0</h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-blue-100 shadow-sm flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
                    <i class="feather icon-check-square"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-semibold uppercase text-blue-600 block truncate">{{ __('Total Physical Qty') }}</span>
                    <h3 class="text-2xl font-black text-blue-700" id="statTotalPhysicalStock">0</h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-emerald-100 shadow-sm flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
                    <i class="feather icon-check-circle"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-semibold uppercase text-emerald-600 block truncate">{{ __('Matched Items') }}</span>
                    <h3 class="text-2xl font-black text-emerald-600" id="statMatchedItems">0</h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-red-100 shadow-sm flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
                    <i class="feather icon-alert-triangle"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-semibold uppercase text-red-600 block truncate">{{ __('Shortage Items') }}</span>
                    <h3 class="text-2xl font-black text-red-600" id="statShortItems">0</h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-sm flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
                    <i class="feather icon-arrow-up-right"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-semibold uppercase text-sky-600 block truncate">{{ __('Surplus Items') }}</span>
                    <h3 class="text-2xl font-black text-sky-600" id="statSurplusItems">0</h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-amber-100 shadow-sm flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
                    <i class="feather icon-hash"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-semibold uppercase text-amber-600 block truncate">{{ __('Total Deficit Qty') }}</span>
                    <h3 class="text-2xl font-black text-amber-600" id="statTotalDeficitQty">0</h3>
                </div>
            </div>
        </div>

        <!-- Scanner & Search Box -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 mb-6">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('Scan barcode, IMEI, size/color variation barcode or type product name/code') }}
                </label>
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <input type="text" id="barcodeScanner" placeholder="{{ __('Scan barcode, IMEI, size/color barcode or type product name and press Enter...') }}" 
                            class="w-full pl-12 pr-4 py-3.5 text-lg rounded-xl border-2 border-emerald-400 focus:border-emerald-600 focus:ring-4 focus:ring-emerald-100 scan-box-pulse transition">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="feather icon-maximize text-xl"></i>
                        </span>
                    </div>
                    <button type="button" id="btnManualScan" class="px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-base rounded-xl transition shadow flex items-center gap-1.5 whitespace-nowrap">
                        <i class="feather icon-plus"></i> {{ __('Add') }}
                    </button>
                </div>
                <div id="scanFeedbackMsg" class="mt-2 text-sm font-semibold hidden"></div>
            </div>
        </div>

        <!-- Audit Items Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
            <div class="p-4 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <i class="feather icon-list text-emerald-600"></i>
                    {{ __('Audit Item Table') }}
                </h3>
                <button type="button" id="btnClearAll" class="text-xs text-red-600 hover:text-red-700 font-semibold flex items-center gap-1">
                    <i class="feather icon-trash-2"></i> {{ __('Clear Table') }}
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="auditTable">
                    <thead>
                        <tr class="bg-slate-100/70 text-slate-600 uppercase text-[11px] font-bold tracking-wider">
                            <th class="py-3.5 px-4">#</th>
                            <th class="py-3.5 px-4">{{ __('Product Name & Code') }}</th>
                            <th class="py-3.5 px-4 text-center">{{ __('Unit') }}</th>
                            <th class="py-3.5 px-4 text-center">{{ __('System Qty') }}</th>
                            <th class="py-3.5 px-4 text-center">{{ __('Scanned Qty') }}</th>
                            <th class="py-3.5 px-4 text-center min-w-[140px]">{{ __('Physical Qty') }}</th>
                            <th class="py-3.5 px-4 text-center">{{ __('Variance / Diff') }}</th>
                            <th class="py-3.5 px-4 text-center">{{ __('Status') }}</th>
                            <th class="py-3.5 px-4 text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody id="auditTableBody" class="divide-y divide-slate-100 text-sm">
                        <tr id="emptyRow">
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                <i class="feather icon-inbox text-4xl mb-2 block"></i>
                                {{ __('No products scanned yet. Please scan barcode above.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Note Section -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
            <label class="block text-sm font-semibold text-slate-700 mb-1">{{ __('Audit Note (Optional)') }}</label>
            <textarea name="note" rows="2" placeholder="{{ __('Type any note regarding this audit...') }}" class="w-full form-textarea rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
        </div>

    </form>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const scannerInput = document.getElementById('barcodeScanner');
        const auditTableBody = document.getElementById('auditTableBody');
        const emptyRow = document.getElementById('emptyRow');
        const btnClearAll = document.getElementById('btnClearAll');

        // Auto focus scanner on page load
        if (scannerInput) {
            scannerInput.focus();
        }

        // Object map to store rows: productId -> row element
        const auditItems = {};

        // Scanner input enter event
        if (scannerInput) {
            scannerInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = this.value.trim();
                    if (query) {
                        processScan(query);
                        this.value = '';
                    }
                }
            });
        }

        const btnManualScan = document.getElementById('btnManualScan');
        const scanFeedbackMsg = document.getElementById('scanFeedbackMsg');

        // Button scan click
        if (btnManualScan && scannerInput) {
            btnManualScan.addEventListener('click', function () {
                const query = scannerInput.value.trim();
                if (query) {
                    processScan(query);
                    scannerInput.value = '';
                }
            });
        }

        function showFeedback(msg, isSuccess = true) {
            if (!scanFeedbackMsg) return;
            scanFeedbackMsg.classList.remove('hidden', 'text-emerald-600', 'text-red-600');
            scanFeedbackMsg.classList.add(isSuccess ? 'text-emerald-600' : 'text-red-600');
            scanFeedbackMsg.innerHTML = isSuccess ? `✅ ${msg}` : `❌ ${msg}`;
            setTimeout(() => {
                scanFeedbackMsg.classList.add('hidden');
            }, 4000);
        }

        // Scan API Request
        function processScan(query) {
            const branchSelect = document.getElementById('branchIdSelect');
            const branchId = branchSelect ? branchSelect.value : '';

            fetch(`{{ route('stock-audit.product-scan') }}?query=${encodeURIComponent(query)}&branch_id=${encodeURIComponent(branchId)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        addProductToAudit(data.product, 1);
                        showFeedback(`"${data.product.name}" added to audit list! (Stock Qty: ${data.product.system_qty_formatted || data.product.system_qty})`, true);
                    } else {
                        showFeedback(data.message || 'Product not found!', false);
                    }
                    if (scannerInput) scannerInput.focus();
                })
                .catch(err => {
                    console.error(err);
                    showFeedback('Error searching product!', false);
                    if (scannerInput) scannerInput.focus();
                });
        }

        // Add or Update product in audit table
        function addProductToAudit(product, scanIncrement = 1) {
            if (emptyRow) {
                emptyRow.style.display = 'none';
            }

            const pId = product.id;

            if (auditItems[pId]) {
                // Product already in table
                const item = auditItems[pId];
                item.scanned += scanIncrement;
                
                if (scanIncrement > 0) {
                    if (!item.isManuallyEdited) {
                        item.physical = item.scanned;
                    } else {
                        item.physical += scanIncrement;
                    }
                }

                updateRowDisplay(pId);
            } else {
                // New Product
                auditItems[pId] = {
                    id: pId,
                    name: product.name,
                    code: product.code || product.barcode || 'N/A',
                    unit: product.unit_name || '',
                    systemQty: parseFloat(product.system_qty) || 0,
                    systemQtyFormatted: product.system_qty_formatted || (product.system_qty + ' ' + (product.unit_name || '')),
                    scanned: scanIncrement,
                    physical: scanIncrement,
                    isManuallyEdited: false
                };

                renderNewRow(pId);
            }

            updateSummaryStats();
        }

        // Render new row HTML
        function renderNewRow(pId) {
            const item = auditItems[pId];
            const rowCount = Object.keys(auditItems).length;

            const tr = document.createElement('tr');
            tr.id = `audit-row-${pId}`;
            tr.className = "hover:bg-slate-50 transition";

            tr.innerHTML = `
                <td class="py-3 px-4 font-semibold text-slate-400 row-index">${rowCount}</td>
                <td class="py-3 px-4">
                    <input type="hidden" name="product_id[]" value="${item.id}">
                    <input type="hidden" name="system_qty[]" value="${item.systemQty}" id="sysQtyInput-${pId}">
                    <input type="hidden" name="scanned_qty[]" value="${item.scanned}" id="scannedQtyInput-${pId}">
                    
                    <div class="font-bold text-slate-800">${item.name}</div>
                    <div class="text-xs text-slate-400">code/barcode: ${item.code}</div>
                </td>
                <td class="py-3 px-4 text-center font-medium text-slate-600">${item.unit}</td>
                <td class="py-3 px-4 text-center">
                    <span class="px-3 py-1 bg-slate-100 text-slate-800 font-bold rounded-lg" title="Stock Report Qty">${item.systemQtyFormatted || item.systemQty}</span>
                </td>
                <td class="py-3 px-4 text-center">
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 font-extrabold rounded-lg" id="scannedDisplay-${pId}">${item.scanned}</span>
                </td>
                <td class="py-3 px-4 text-center">
                    <input type="number" step="any" name="physical_qty[]" value="${item.physical}" id="physicalInput-${pId}" 
                        class="w-24 text-center font-bold form-input text-base rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 physical-input"
                        data-id="${pId}">
                </td>
                <td class="py-3 px-4 text-center font-black text-base" id="diffDisplay-${pId}">0</td>
                <td class="py-3 px-4 text-center" id="statusBadge-${pId}"></td>
                <td class="py-3 px-4 text-center">
                    <button type="button" class="text-red-500 hover:text-red-700 font-bold p-1 rounded-lg hover:bg-red-50 btn-remove-row" data-id="${pId}">
                        <i class="feather icon-x text-lg"></i>
                    </button>
                </td>
            `;

            auditTableBody.appendChild(tr);

            // Bind input change listener
            const physInput = tr.querySelector('.physical-input');
            physInput.addEventListener('input', function () {
                const val = parseFloat(this.value);
                auditItems[pId].physical = isNaN(val) ? 0 : val;
                auditItems[pId].isManuallyEdited = true;
                updateRowDisplay(pId);
                updateSummaryStats();
            });

            // Bind remove button listener
            tr.querySelector('.btn-remove-row').addEventListener('click', function () {
                delete auditItems[pId];
                tr.remove();
                if (Object.keys(auditItems).length === 0 && emptyRow) {
                    emptyRow.style.display = '';
                }
                reindexRows();
                updateSummaryStats();
            });

            updateRowDisplay(pId);
        }

        // Update single row calculations & badges
        function updateRowDisplay(pId) {
            const item = auditItems[pId];
            const diff = item.physical - item.systemQty;

            const scannedInput = document.getElementById(`scannedQtyInput-${pId}`);
            if (scannedInput) scannedInput.value = item.scanned;

            const scannedDisp = document.getElementById(`scannedDisplay-${pId}`);
            if (scannedDisp) scannedDisp.innerText = item.scanned;

            const physInput = document.getElementById(`physicalInput-${pId}`);
            if (physInput && document.activeElement !== physInput) {
                physInput.value = item.physical;
            }

            const diffDisplay = document.getElementById(`diffDisplay-${pId}`);
            const statusBadge = document.getElementById(`statusBadge-${pId}`);

            if (Math.abs(diff) < 0.001) {
                if (diffDisplay) {
                    diffDisplay.innerText = "0";
                    diffDisplay.className = "py-3 px-4 text-center font-black text-emerald-600";
                }
                if (statusBadge) {
                    statusBadge.innerHTML = `<span class="px-2.5 py-1 text-xs font-bold rounded-full badge-match"><i class="feather icon-check"></i> Match</span>`;
                }
            } else if (diff < 0) {
                if (diffDisplay) {
                    diffDisplay.innerText = diff.toFixed(2);
                    diffDisplay.className = "py-3 px-4 text-center font-black text-red-600";
                }
                if (statusBadge) {
                    statusBadge.innerHTML = `<span class="px-2.5 py-1 text-xs font-bold rounded-full badge-short"><i class="feather icon-minus"></i> Short (${Math.abs(diff)})</span>`;
                }
            } else {
                if (diffDisplay) {
                    diffDisplay.innerText = `+${diff.toFixed(2)}`;
                    diffDisplay.className = "py-3 px-4 text-center font-black text-blue-600";
                }
                if (statusBadge) {
                    statusBadge.innerHTML = `<span class="px-2.5 py-1 text-xs font-bold rounded-full badge-surplus"><i class="feather icon-plus"></i> Surplus (+${diff})</span>`;
                }
            }
        }

        // Reindex row numbers
        function reindexRows() {
            const rows = auditTableBody.querySelectorAll('tr:not(#emptyRow)');
            rows.forEach((row, idx) => {
                const indexCell = row.querySelector('.row-index');
                if (indexCell) indexCell.innerText = idx + 1;
            });
        }

        // Update overall stats counters
        function updateSummaryStats() {
            let total = 0;
            let totalSystemQty = 0;
            let totalPhysicalQty = 0;
            let matched = 0;
            let short = 0;
            let surplus = 0;
            let totalDeficitQty = 0;

            Object.values(auditItems).forEach(item => {
                total++;
                totalSystemQty += (parseFloat(item.systemQty) || 0);
                totalPhysicalQty += (parseFloat(item.physical) || 0);

                const diff = item.physical - item.systemQty;
                if (Math.abs(diff) < 0.001) {
                    matched++;
                } else if (diff < 0) {
                    short++;
                    totalDeficitQty += Math.abs(diff);
                } else {
                    surplus++;
                }
            });

            const elTotal = document.getElementById('statTotalItems');
            if (elTotal) elTotal.innerText = total;

            const elTotalSys = document.getElementById('statTotalSystemStock');
            if (elTotalSys) {
                // If integer, show integer, else 2 decimal places
                elTotalSys.innerText = Number.isInteger(totalSystemQty) ? totalSystemQty : totalSystemQty.toFixed(2);
            }

            const elTotalPhys = document.getElementById('statTotalPhysicalStock');
            if (elTotalPhys) {
                elTotalPhys.innerText = Number.isInteger(totalPhysicalQty) ? totalPhysicalQty : totalPhysicalQty.toFixed(2);
            }

            const elMatched = document.getElementById('statMatchedItems');
            if (elMatched) elMatched.innerText = matched;

            const elShort = document.getElementById('statShortItems');
            if (elShort) elShort.innerText = short;

            const elSurplus = document.getElementById('statSurplusItems');
            if (elSurplus) elSurplus.innerText = surplus;

            const elDeficit = document.getElementById('statTotalDeficitQty');
            if (elDeficit) {
                elDeficit.innerText = Number.isInteger(totalDeficitQty) ? totalDeficitQty : totalDeficitQty.toFixed(2);
            }
        }

        // Clear all
        if (btnClearAll) {
            btnClearAll.addEventListener('click', function () {
                if (confirm('Are you sure you want to clear all items from the audit list?')) {
                    Object.keys(auditItems).forEach(k => delete auditItems[k]);
                    auditTableBody.innerHTML = '';
                    if (emptyRow) {
                        auditTableBody.appendChild(emptyRow);
                        emptyRow.style.display = '';
                    }
                    updateSummaryStats();
                }
            });
        }
    });
</script>
@endpush