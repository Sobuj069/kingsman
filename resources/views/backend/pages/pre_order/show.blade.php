@extends('backend.layouts.master')

@section('page-title', __('Pre-Order Details') . ' - ' . $preOrder->pre_order_no)

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="row mb-4">
                <div class="col-12 flex justify-between items-center">
                    <div>
                        <h4 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            {{ __('Pre-Order Details') }} — <span class="text-orange-500">{{ $preOrder->pre_order_no }}</span>
                        </h4>
                        <p class="text-sm text-slate-500 mt-1">
                            {{ __('Created:') }} {{ $preOrder->created_at?->format('d M Y, h:i A') }}
                            @if($preOrder->user)
                                &nbsp;·&nbsp; {{ __('By:') }} {{ $preOrder->user->name }}
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('pre-orders.index') }}" class="btn btn-light btn-sm px-3 py-2 font-semibold">
                        <i class="feather icon-arrow-left me-1"></i> {{ __('Back to List') }}
                    </a>
                </div>
            </div>

            <div class="row g-4">

                {{-- Left: Info Card --}}
                <div class="col-md-4">
                    <div class="card p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 h-100">
                        <h6 class="text-xs font-bold uppercase text-slate-400 mb-3 tracking-wider">{{ __('Order Information') }}</h6>

                        <div class="space-y-3">
                            <div class="flex justify-between items-start py-2 border-b border-slate-100 dark:border-slate-700">
                                <span class="text-xs font-semibold text-slate-500 uppercase">{{ __('Pre-Order No') }}</span>
                                <span class="font-bold text-orange-500">{{ $preOrder->pre_order_no }}</span>
                            </div>
                            <div class="flex justify-between items-start py-2 border-b border-slate-100 dark:border-slate-700">
                                <span class="text-xs font-semibold text-slate-500 uppercase">{{ __('Status') }}</span>
                                @if ($preOrder->status == 'pending')
                                    <span class="px-2.5 py-1 text-xs font-bold text-amber-700 bg-amber-100 rounded-full">{{ __('Pending') }}</span>
                                @elseif ($preOrder->status == 'converted')
                                    <span class="px-2.5 py-1 text-xs font-bold text-emerald-700 bg-emerald-100 rounded-full">{{ __('Converted') }}</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold text-rose-700 bg-rose-100 rounded-full">{{ __('Cancelled') }}</span>
                                @endif
                            </div>
                            <div class="flex justify-between items-start py-2 border-b border-slate-100 dark:border-slate-700">
                                <span class="text-xs font-semibold text-slate-500 uppercase">{{ __('Customer') }}</span>
                                <div class="text-end">
                                    <div class="font-semibold text-slate-800 dark:text-white text-sm">{{ $preOrder->customer?->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-slate-500">{{ $preOrder->customer?->phone ?? '' }}</div>
                                </div>
                            </div>
                            <div class="flex justify-between items-start py-2 border-b border-slate-100 dark:border-slate-700">
                                <span class="text-xs font-semibold text-slate-500 uppercase">{{ __('Branch') }}</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-300 text-sm">{{ $preOrder->branch?->name ?? 'Main Branch' }}</span>
                            </div>
                            <div class="flex justify-between items-start py-2 border-b border-slate-100 dark:border-slate-700">
                                <span class="text-xs font-semibold text-slate-500 uppercase">{{ __('Total Amount') }}</span>
                                <span class="font-bold text-slate-900 dark:text-white text-base">TK {{ number_format($preOrder->total_amount, 2) }}</span>
                            </div>
                            @if ($preOrder->note)
                            <div class="py-2">
                                <span class="text-xs font-semibold text-slate-500 uppercase d-block mb-1">{{ __('Note') }}</span>
                                <p class="text-sm text-slate-600 dark:text-slate-300 mb-0">{{ $preOrder->note }}</p>
                            </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700 flex flex-col gap-2">
                            @if ($preOrder->status == 'pending')
                                <a href="{{ route('invoice.create', ['pre_order_id' => $preOrder->id, 'convert_mode' => 1]) }}" class="btn btn-success btn-sm w-100 font-bold">
                                    <i class="feather icon-check-circle me-1"></i> {{ __('Convert to Sale') }}
                                </a>
                                <a href="{{ route('pre-orders.edit', $preOrder->id) }}" class="btn btn-outline-warning btn-sm w-100 font-semibold">
                                    <i class="feather icon-edit me-1"></i> {{ __('Edit Pre-Order') }}
                                </a>
                                <form method="POST" action="{{ route('pre-orders.cancel', $preOrder->id) }}" onsubmit="return confirm('{{ __('Cancel this pre-order?') }}')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100 font-semibold">
                                        <i class="feather icon-x me-1"></i> {{ __('Cancel Pre-Order') }}
                                    </button>
                                </form>
                            @elseif ($preOrder->status == 'cancelled')
                                <form method="POST" action="{{ route('pre-orders.destroy', $preOrder->id) }}" onsubmit="return confirm('{{ __('Permanently delete this pre-order?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm w-100 font-bold">
                                        <i class="feather icon-trash-2 me-1"></i> {{ __('Delete Permanently') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Right: Items Table --}}
                <div class="col-md-8">
                    <div class="card bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 overflow-hidden">
                        <div class="p-4 border-b border-slate-100 dark:border-slate-700">
                            <h5 class="font-bold text-slate-800 dark:text-white mb-0 flex items-center gap-2">
                                <i class="feather icon-package text-orange-500"></i>
                                {{ __('Pre-Order Items') }}
                                <span class="badge bg-orange-100 text-orange-700 ms-1">{{ $preOrder->items->count() }}</span>
                            </h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-xs">
                                    <tr>
                                        <th class="py-3 px-4">#</th>
                                        <th class="py-3 px-4">{{ __('Product') }}</th>
                                        <th class="py-3 px-4 text-center">{{ __('Qty') }}</th>
                                        <th class="py-3 px-4 text-end">{{ __('Unit Price') }}</th>
                                        <th class="py-3 px-4 text-end">{{ __('Subtotal') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 text-sm">
                                    @foreach ($preOrder->items as $i => $item)
                                        <tr>
                                            <td class="py-3 px-4 text-slate-400 font-semibold">{{ $i + 1 }}</td>
                                            <td class="py-3 px-4">
                                                <div class="font-semibold text-slate-800 dark:text-white">
                                                    {{ $item->product?->name ?? __('Deleted Product') }}
                                                </div>
                                                @if($item->product?->code)
                                                    <div class="text-xs text-slate-400">{{ __('Code:') }} {{ $item->product->code }}</div>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                <span class="badge bg-slate-100 text-slate-700 border border-slate-200 font-bold px-2.5 py-1">
                                                    {{ $item->quantity }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-end font-semibold text-slate-600 dark:text-slate-300">
                                                TK {{ number_format($item->unit_price, 2) }}
                                            </td>
                                            <td class="py-3 px-4 text-end font-bold text-slate-900 dark:text-white">
                                                TK {{ number_format($item->subtotal, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-slate-50 dark:bg-slate-900/30">
                                    <tr>
                                        <td colspan="4" class="py-3 px-4 text-end text-xs font-bold uppercase text-slate-500">
                                            {{ __('Grand Total') }}
                                        </td>
                                        <td class="py-3 px-4 text-end font-bold text-orange-500 text-base">
                                            TK {{ number_format($preOrder->total_amount, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
