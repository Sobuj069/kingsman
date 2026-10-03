@extends('backend.layouts.master')
@section('section-title', __('Activity Log'))
@section('page-title', __('System Activity Logs'))

@section('action-button')
    @if(auth()->user()->role_id == 1 || auth()->user()->isSuperAdmin())
    <form action="{{ route('activity-log.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all logs?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <i class="feather icon-trash-2 mr-2"></i> {{ __('Clear All Logs') }}
        </button>
    </form>
    @endif
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30 card_style">
            <div class="card-body">
                <form action="{{ route('activity-log.index') }}" method="GET">
                    <div class="row h-hide">
                        <div class="col-md-3 col-12 mt-3">
                            <label>{{ __('Action') }}</label>
                            <input type="text" class="form-control" name="action" value="{{ request('action') }}" placeholder="{{ __('Search Action...') }}" />
                        </div>
                        <div class="col-md-3 col-12 mt-3">
                            <label>{{ __('Start Date') }}</label>
                            <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}" />
                        </div>
                        <div class="col-md-3 col-12 mt-3">
                            <label>{{ __('End Date') }}</label>
                            <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}" />
                        </div>
                        <div class="col-md-3 col-12 mt-3 d-flex align-items-end">
                            <button type="submit" class="btn add_list_btn w-100">{{ __('Filter') }}</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive mt-4">
                    <table class="table table-striped table-bordered text-center">
                        <thead class="header_bg">
                            <tr>
                                <th class="header_style_left">#</th>
                                <th>{{ __('Time') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Action') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('IP Address') }}</th>
                                <th>{{ __('Model Info') }}</th>
                                <th class="header_style_right">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $key => $log)
                                <tr>
                                    <td>{{ $logs->firstItem() + $key }}</td>
                                    <td>{{ $log->created_at->format('M d, Y h:i A') }} <br> <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small></td>
                                    <td>
                                        @if($log->user)
                                            <strong>{{ $log->user->name }}</strong> <br>
                                            <span class="badge badge-secondary">{{ $log->user->role?->name }}</span>
                                        @else
                                            <span class="text-danger">{{ __('System/Guest') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = 'info';
                                            if(str_contains(strtolower($log->action), 'delete')) $badgeClass = 'danger';
                                            if(str_contains(strtolower($log->action), 'create')) $badgeClass = 'success';
                                            if(str_contains(strtolower($log->action), 'update')) $badgeClass = 'warning';
                                        @endphp
                                        <span class="badge badge-{{ $badgeClass }}">{{ $log->action }}</span>
                                    </td>
                                    <td class="text-left" style="max-width: 300px; white-space: normal;">
                                        {{ $log->description }}
                                    </td>
                                    <td>{{ $log->ip_address }}</td>
                                    <td>
                                        @if($log->model_type)
                                            @php
                                                $modelName = class_basename($log->model_type);
                                            @endphp
                                            <span class="text-info">{{ $modelName }}</span> (ID: {{ $log->model_id }})
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                                {{ __('Action') }}
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="javascript:void(0)" onclick="showLogDetails({{ $log->id }})">
                                                    <i class="feather icon-eye mr-2"></i> {{ __('View Details') }}
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <!-- Hidden Data for Modal -->
                                        <div id="log-data-{{ $log->id }}" style="display:none;">
                                            {!! json_encode([
                                                'time' => $log->created_at->format('M d, Y h:i:s A'),
                                                'user' => $log->user?->name ?? 'System',
                                                'action' => $log->action,
                                                'description' => $log->description,
                                                'ip' => $log->ip_address,
                                                'model' => $log->model_type ? class_basename($log->model_type) . " (ID: {$log->model_id})" : '-',
                                                'payload' => $log->data ? json_decode($log->data, true) : null
                                            ]) !!}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-danger">{{ __('No logs found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Log Details Modal -->
<div class="modal fade" id="logDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header header_bg">
                <h5 class="modal-title text-white">{{ __('Activity Details') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>{{ __('User') }}:</strong> <span id="detail-user"></span></p>
                        <p><strong>{{ __('Time') }}:</strong> <span id="detail-time"></span></p>
                        <p><strong>{{ __('IP Address') }}:</strong> <span id="detail-ip"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>{{ __('Action') }}:</strong> <span id="detail-action"></span></p>
                        <p><strong>{{ __('Model') }}:</strong> <span id="detail-model"></span></p>
                    </div>
                </div>
                <hr>
                <p><strong>{{ __('Description') }}:</strong></p>
                <div class="alert alert-light border" id="detail-description"></div>
                
                <div id="payload-section" style="display:none;">
                    <hr>
                    <p><strong>{{ __('Detailed Data') }}:</strong></p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped" id="detail-data-table">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('Field') }}</th>
                                    <th>{{ __('Value') }}</th>
                                </tr>
                            </thead>
                            <tbody id="detail-data-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
function showLogDetails(id) {
    // Show loading state or clear previous content
    const payloadSection = document.getElementById('payload-section');
    const tableBody = document.getElementById('detail-data-body');
    tableBody.innerHTML = '<tr><td colspan="2" class="text-center">Loading...</td></tr>';
    const existingItems = document.getElementById('detail-items-container');
    if (existingItems) existingItems.remove();
    
    // Fetch details via AJAX
    fetch(`{{ url('activity-log') }}/${id}/details`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('detail-user').innerText = data.user;
            document.getElementById('detail-time').innerText = data.time;
            document.getElementById('detail-ip').innerText = data.ip;
            document.getElementById('detail-action').innerText = data.action;
            document.getElementById('detail-model').innerText = data.model;
            document.getElementById('detail-description').innerText = data.description;
            
            tableBody.innerHTML = '';

            if (data.payload) {
                payloadSection.style.display = 'block';
                let itemsHtml = '';
                
                for (const [key, value] of Object.entries(data.payload)) {
                    const lowerKey = key.toLowerCase();
                    // Check if it's an items list (array of objects)
                    if (Array.isArray(value) && value.length > 0 && typeof value[0] === 'object' && (lowerKey.includes('item') || lowerKey.includes('product'))) {
                        itemsHtml += `<div class="mt-4">
                            <h6>${key.replace(/_/g, ' ').toUpperCase()}</h6>
                            <table class="table table-sm table-bordered">
                                <thead class="bg-secondary text-white">
                                    <tr>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Rate</th>
                                        <th>IMEI</th>
                                    </tr>
                                </thead>
                                <tbody>`;
                        
                        value.forEach(item => {
                            const productName = item.product ? item.product.name : (item.product_id || '-');
                            const qty = item.main_qty || item.qty || item.stock_qty || item.total_qty || '-';
                            const rate = item.rate || item.sale_rate || '-';
                            const imei = item.imei || '-';
                            
                            itemsHtml += `<tr>
                                <td>${productName}</td>
                                <td>${qty}</td>
                                <td>${rate}</td>
                                <td style="font-size: 11px;">${imei}</td>
                            </tr>`;
                        });
                        
                        itemsHtml += `</tbody></table></div>`;
                        continue;
                    }

                    let displayValue = value;
                    if (typeof value === 'object' && value !== null) {
                        // If it's a model relationship (like customer, user, etc), show the name
                        displayValue = value.name || value.username || value.invoice_no || value.transfer_no || JSON.stringify(value);
                    }
                    
                    const row = `<tr>
                        <td class="font-weight-bold text-capitalize">${key.replace(/_/g, ' ')}</td>
                        <td class="text-break">${displayValue || '-'}</td>
                    </tr>`;
                    tableBody.insertAdjacentHTML('beforeend', row);
                }
                
                if (itemsHtml) {
                    const existingItems = document.getElementById('detail-items-container');
                    if (existingItems) existingItems.remove();
                    payloadSection.insertAdjacentHTML('beforeend', `<div id="detail-items-container">${itemsHtml}</div>`);
                }
            } else {
                payloadSection.style.display = 'none';
                tableBody.innerHTML = '<tr><td colspan="2" class="text-center text-muted">No detailed data captured for this action.</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error fetching log details:', error);
            tableBody.innerHTML = '<tr><td colspan="2" class="text-center text-danger">Error loading details.</td></tr>';
        });
    
    $('#logDetailsModal').modal('show');
}
</script>
@endpush
@endsection
