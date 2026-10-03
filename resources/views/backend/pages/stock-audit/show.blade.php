@extends('backend.layouts.master')
@section('page-title', __('Stock Audit Details - ') . $audit->audit_no)

@section('content')
<div class="p-6 bg-slate-50 min-h-screen">
    
    <!-- Top Action Bar -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6 bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-800">{{ $audit->audit_no }}</h1>
                <span class="px-3 py-1 text-xs font-bold bg-emerald-100 text-emerald-700 rounded-full uppercase">
                    {{ $audit->status }}
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                {{ __('Date:') }} {{ \Carbon\Carbon::parse($audit->date)->format('d M, Y') }} | 
                {{ __('Branch:') }} {{ $audit->branch->name ?? 'Main Branch' }} | 
                {{ __('Auditor:') }} {{ $audit->auditor->name ?? 'N/A' }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('stock-audit.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition flex items-center gap-1.5">
                <i class="feather icon-arrow-left"></i>
                {{ __('Back to List') }}
            </a>
            <a href="{{ route('stock-audit.print', $audit->id) }}" target="_blank" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl transition shadow-md hover:shadow-lg flex items-center gap-2">
                <i class="feather icon-printer text-lg"></i>
                {{ __('Print Report') }}
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
            <span class="text-xs font-semibold uppercase text-slate-400 block mb-1">{{ __('Total Items') }}</span>
            <h3 class="text-2xl font-black text-slate-800">{{ $audit->total_items }}</h3>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-emerald-100 shadow-sm">
            <span class="text-xs font-semibold uppercase text-emerald-600 block mb-1">{{ __('Matched Items') }}</span>
            <h3 class="text-2xl font-black text-emerald-600">{{ $audit->matched_items }}</h3>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-red-100 shadow-sm">
            <span class="text-xs font-semibold uppercase text-red-600 block mb-1">{{ __('Discrepancy Items') }}</span>
            <h3 class="text-2xl font-black text-red-600">{{ $audit->discrepancy_items }}</h3>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-amber-100 shadow-sm">
            <span class="text-xs font-semibold uppercase text-amber-600 block mb-1">{{ __('Deficit Qty') }}</span>
            <h3 class="text-2xl font-black text-amber-600">{{ number_format($audit->total_deficit_qty, 2) }}</h3>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-blue-100 shadow-sm">
            <span class="text-xs font-semibold uppercase text-blue-600 block mb-1">{{ __('Surplus Qty') }}</span>
            <h3 class="text-2xl font-black text-blue-600">{{ number_format($audit->total_surplus_qty, 2) }}</h3>
        </div>
    </div>

    @if($audit->note)
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-6">
        <span class="text-xs font-bold uppercase text-slate-400 block mb-1">{{ __('Audit Note') }}</span>
        <p class="text-slate-700 text-sm italic">{{ $audit->note }}</p>
    </div>
    @endif

    <!-- Audit Item Details Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-100 font-bold text-slate-800">
            <i class="feather icon-list text-emerald-600 mr-1"></i> {{ __('Audit Item Details') }}
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100/70 text-slate-600 uppercase text-[11px] font-bold tracking-wider">
                        <th class="py-3.5 px-4">#</th>
                        <th class="py-3.5 px-4">{{ __('Product Name') }}</th>
                        <th class="py-3.5 px-4 text-center">{{ __('Unit') }}</th>
                        <th class="py-3.5 px-4 text-center">{{ __('System Stock') }}</th>
                        <th class="py-3.5 px-4 text-center">{{ __('Scanned Qty') }}</th>
                        <th class="py-3.5 px-4 text-center">{{ __('Physical Stock') }}</th>
                        <th class="py-3.5 px-4 text-center">{{ __('Variance') }}</th>
                        <th class="py-3.5 px-4 text-center">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach($audit->items as $key => $item)
                    @php
                        $diff = $item->diff_qty;
                    @endphp
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3.5 px-4 font-semibold text-slate-400">{{ $key + 1 }}</td>
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-slate-800">{{ $item->product->name ?? 'N/A' }}</div>
                            <div class="text-xs text-slate-400">código/barcode: {{ $item->product->code ?? $item->product->barcode ?? 'N/A' }}</div>
                        </td>
                        <td class="py-3.5 px-4 text-center text-slate-600 font-medium">
                            {{ $item->product->unit->name ?? '' }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-bold text-slate-700">
                            {{ number_format($item->system_qty, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-extrabold text-emerald-700">
                            {{ number_format($item->scanned_qty, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-bold text-slate-900">
                            {{ number_format($item->physical_qty, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-black {{ abs($diff) < 0.001 ? 'text-emerald-600' : ($diff < 0 ? 'text-red-600' : 'text-blue-600') }}">
                            {{ $diff > 0 ? '+'.number_format($diff, 2) : number_format($diff, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if(abs($diff) < 0.001)
                                <span class="px-2.5 py-1 text-xs font-bold text-emerald-700 bg-emerald-100 rounded-full">
                                    <i class="feather icon-check"></i> Match
                                </span>
                            @elseif($diff < 0)
                                <span class="px-2.5 py-1 text-xs font-bold text-red-700 bg-red-100 rounded-full">
                                    <i class="feather icon-minus"></i> Short ({{ abs($diff) }})
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-bold text-blue-700 bg-blue-100 rounded-full">
                                    <i class="feather icon-plus"></i> Surplus (+{{ $diff }})
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
