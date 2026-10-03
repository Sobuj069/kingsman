@extends('backend.layouts.master')
@section('section-title', __('AI Stock Auditor'))
@section('page-title', __('Stock Health Monitoring'))

@push('css')
<style>
    .audit-card {
        transition: all 0.3s ease;
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .audit-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .status-badge {
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .status-healthy { background: #d4edda; color: #155724; }
    .status-warning { background: #fff3cd; color: #856404; }
    .status-danger { background: #f8d7da; color: #721c24; }
    
    #analysis-container {
        display: none;
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
        border-left: 5px solid #6e42c1;
    }
    .ai-bubble {
        background: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        position: relative;
    }
    .ai-bubble::before {
        content: "✨ AI Insights";
        display: block;
        font-weight: bold;
        color: #6e42c1;
        margin-bottom: 10px;
        font-size: 0.9rem;
    }
    .loading-shimmer {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        height: 20px;
        border-radius: 4px;
        margin-bottom: 10px;
    }
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    .btn-fix {
        background: #28a745;
        color: white;
        border: none;
    }
    .btn-fix:hover {
        background: #218838;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="card audit-card bg-primary text-white">
            <div class="card-body">
                <h4>Stock Monitoring AI ✨</h4>
                <p>এটি আপনার ইনভেন্টরির প্রতিটি প্রোডাক্টের ট্রানজ্যাকশন হিস্ট্রি এনালাইসিস করে স্টকের গরমিল খুঁজে বের করে।</p>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card audit-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('Product') }}</th>
                                <th>{{ __('Theoretical Stock') }}</th>
                                <th>{{ __('Actual Stock') }}</th>
                                <th>{{ __('Discrepancy') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                function formatStockQty($qty, $product) {
                                    if ($product->unit && $product->unit->related_unit_id && $product->unit->related_value) {
                                        $mainUnitName = $product->unit->name;
                                        $subUnit = \App\Models\Unit::find($product->unit->related_unit_id);
                                        $subUnitName = $subUnit ? $subUnit->name : '';
                                        $relatedValue = $product->unit->related_value;
                                        
                                        $mainQty = floor(abs($qty) / $relatedValue);
                                        $subQty = abs($qty) % $relatedValue;
                                        
                                        $sign = $qty < 0 ? '-' : '';
                                        
                                        if ($mainQty > 0 && $subQty > 0) {
                                            return $sign . $mainQty . ' ' . $mainUnitName . ' ' . $subQty . ' ' . $subUnitName;
                                        } elseif ($mainQty > 0) {
                                            return $sign . $mainQty . ' ' . $mainUnitName;
                                        } elseif ($subQty > 0) {
                                            return $sign . $subQty . ' ' . $subUnitName;
                                        } else {
                                            return '0 ' . $mainUnitName;
                                        }
                                    }
                                    return $qty . ' ' . ($product->unit->name ?? '');
                                }
                            @endphp
                            @forelse($discrepancies as $item)
                                <tr>
                                    <td><strong>{{ $item['product']->name }}</strong></td>
                                    <td>{{ formatStockQty($item['theoretical_stock'], $item['product']) }}</td>
                                    <td>{{ formatStockQty($item['actual_stock'], $item['product']) }}</td>
                                    <td>
                                        <span class="text-danger font-weight-bold">
                                            {{ formatStockQty($item['discrepancy'], $item['product']) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-danger">Gormil Detected</span>
                                    </td>
                                    <td>
                                        <div class="flex gap-2">
                                            <button class="btn btn-purple btn-sm audit-btn" 
                                                    onclick="runAudit({{ $item['product']->id }})">
                                                <i class="feather icon-cpu"></i> Audit
                                            </button>
                                            <button class="btn btn-fix btn-sm" 
                                                    onclick="fixStock({{ $item['product']->id }})">
                                                <i class="feather icon-check-circle"></i> Fix Stock
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <img src="https://cdn-icons-png.flaticon.com/512/1548/1548682.png" width="80" class="mb-3 opacity-50">
                                        <h5 class="text-muted">No Stock Discrepancies Found! All healthy.</h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AI Audit Modal -->
<div class="modal fade" id="auditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold">✨ AI Stock Investigation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="audit-loading" style="display: none;">
                    <div class="loading-shimmer" style="width: 80%;"></div>
                    <div class="loading-shimmer" style="width: 100%;"></div>
                    <div class="loading-shimmer" style="width: 90%;"></div>
                    <p class="text-center text-muted mt-3 italic">AI ট্রানজ্যাকশন এবং অ্যাক্টিভিটি লগ এনালাইসিস করছে...</p>
                </div>
                
                <div id="audit-result" style="display: none;">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded text-center">
                                <small class="text-muted d-block">Theoretical</small>
                                <h4 id="res-theoretical">0</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded text-center">
                                <small class="text-muted d-block">Actual</small>
                                <h4 id="res-actual">0</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-danger-soft rounded text-center">
                                <small class="text-danger d-block">Discrepancy</small>
                                <h4 id="res-diff" class="text-danger">0</h4>
                            </div>
                        </div>
                    </div>

                    <div class="ai-bubble mt-4">
                        <div id="ai-analysis-text" style="white-space: pre-line; line-height: 1.6;">
                            <!-- AI content goes here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <input type="hidden" id="current-audit-id">
                <button type="button" class="btn btn-fix" onclick="fixStock($('#current-audit-id').val())">Auto-Fix This Stock</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function runAudit(productId) {
        if(!productId || productId == 0) return alert('Invalid Product ID');
        
        $('#current-audit-id').val(productId);
        $('#auditModal').modal('show');
        $('#audit-loading').show();
        $('#audit-result').hide();

        $.get("{{ url('ai-auditor/audit') }}/" + productId, function(response) {
            if(response.success) {
                $('#res-theoretical').text(response.data.theoretical_stock_formatted);
                $('#res-actual').text(response.data.actual_stock_formatted);
                $('#res-diff').text(response.data.discrepancy_formatted);
                
                $('#ai-analysis-text').html(response.analysis);
                
                $('#audit-loading').hide();
                $('#audit-result').fadeIn();
            } else {
                Swal.fire('Error', response.message || 'Audit failed!', 'error');
                $('#auditModal').modal('hide');
            }
        }).fail(function(xhr) {
            let errorMsg = 'Error connecting to auditor service.';
            if(xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            Swal.fire('Error', errorMsg, 'error');
            $('#auditModal').modal('hide');
        });
    }

    function fixStock(productId) {
        if(!productId) return;

        Swal.fire({
            title: 'আপনি কি নিশ্চিত?',
            text: "সিস্টেম ক্যালকুলেশন অনুযায়ী ডাটাবেসের স্টক আপডেট করা হবে।",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'হ্যাঁ, ঠিক করুন!',
            cancelButtonText: 'না'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'প্রসেসিং...',
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                $.get("{{ url('ai-auditor/fix') }}/" + productId, function(response) {
                    if(response.success) {
                        Swal.fire('সফল!', 'স্টক সফলভাবে আপডেট করা হয়েছে।', 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('ভুল!', response.message, 'error');
                    }
                });
            }
        })
    }
</script>
@endpush
