@extends('backend.layouts.master')
@section('page-title', __('Stock Audit History'))

@section('content')
<div class="p-6 bg-slate-50 min-h-screen">

    <!-- Header Section -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6 bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i class="feather icon-clipboard text-emerald-600"></i>
                {{ __('Stock Audit History') }}
            </h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ __('View all completed stock audit reports and details') }}</p>
        </div>

        <a href="{{ route('stock-audit.create') }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl transition shadow-md hover:shadow-lg flex items-center gap-2">
            <i class="feather icon-plus-circle text-lg"></i>
            {{ __('Start New Stock Audit') }}
        </a>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 mb-6">
        <form method="GET" action="{{ route('stock-audit.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">{{ __('Audit No') }}</label>
                <input type="text" name="audit_no" value="{{ request('audit_no') }}" placeholder="SA-00001" class="w-full text-sm rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">{{ __('Start Date') }}</label>
                <input type="date" name="startDate" value="{{ request('startDate') }}" class="w-full text-sm rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">{{ __('End Date') }}</label>
                <input type="date" name="endDate" value="{{ request('endDate') }}" class="w-full text-sm rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl transition flex items-center justify-center gap-1.5">
                    <i class="feather icon-filter"></i> {{ __('Filter') }}
                </button>
                <a href="{{ route('stock-audit.index') }}" class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition">
                    {{ __('Reset') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Audits List Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100/70 text-slate-600 uppercase text-[11px] font-bold tracking-wider">
                        <th class="py-3.5 px-4">#</th>
                        <th class="py-3.5 px-4">{{ __('Audit No') }}</th>
                        <th class="py-3.5 px-4">{{ __('Date') }}</th>
                        <th class="py-3.5 px-4">{{ __('Branch') }}</th>
                        <th class="py-3.5 px-4">{{ __('Auditor') }}</th>
                        <th class="py-3.5 px-4 text-center">{{ __('Total Items') }}</th>
                        <th class="py-3.5 px-4 text-center">{{ __('Matched') }}</th>
                        <th class="py-3.5 px-4 text-center">{{ __('Discrepancy') }}</th>
                        <th class="py-3.5 px-4 text-center">{{ __('Total Deficit') }}</th>
                        <th class="py-3.5 px-4 text-center">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($audits as $key => $audit)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3.5 px-4 font-semibold text-slate-400">{{ $audits->firstItem() + $key }}</td>
                        <td class="py-3.5 px-4">
                            <a href="{{ route('stock-audit.show', $audit->id) }}" class="font-bold text-emerald-600 hover:underline">
                                {{ $audit->audit_no }}
                            </a>
                        </td>
                        <td class="py-3.5 px-4 text-slate-700 font-medium">
                            {{ \Carbon\Carbon::parse($audit->date)->format('d M, Y') }}
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-slate-700">
                            {{ $audit->branch->name ?? 'Main Branch' }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-600">
                            {{ $audit->auditor->name ?? 'N/A' }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                            {{ $audit->total_items }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="px-2.5 py-1 text-xs font-bold text-emerald-700 bg-emerald-100 rounded-full">
                                {{ $audit->matched_items }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="px-2.5 py-1 text-xs font-bold {{ $audit->discrepancy_items > 0 ? 'text-red-700 bg-red-100' : 'text-slate-600 bg-slate-100' }} rounded-full">
                                {{ $audit->discrepancy_items }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center font-bold text-red-600">
                            {{ number_format($audit->total_deficit_qty, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('stock-audit.show', $audit->id) }}" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition" title="{{ __('View Details') }}">
                                    <i class="feather icon-eye text-base"></i>
                                </a>
                                <a href="{{ route('stock-audit.print', $audit->id) }}" target="_blank" class="p-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg transition" title="{{ __('Print Report') }}">
                                    <i class="feather icon-printer text-base"></i>
                                </a>
                                <form action="{{ route('stock-audit.destroy', $audit->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this stock audit record?') }}');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition" title="{{ __('Delete') }}">
                                        <i class="feather icon-trash-2 text-base"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="py-12 text-center text-slate-400">
                            <i class="feather icon-inbox text-4xl block mb-2"></i>
                            {{ __('No stock audit records found.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($audits->hasPages())
        <div class="p-4 bg-slate-50 border-t border-slate-100">
            {{ $audits->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
