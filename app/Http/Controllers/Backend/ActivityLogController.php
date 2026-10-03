<?php

namespace App\Http\Controllers\Backend;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Branch filtering
        if ($userBranchId != 1) {
            $query->where('branch_id', $userBranchId);
        } elseif ($filterBranchId) {
            $query->where('branch_id', $filterBranchId);
        }

        // Action filtering
        if ($request->action) {
            $query->where('action', 'LIKE', "%{$request->action}%");
        }

        // Date filtering
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->paginate(30);

        return view('backend.pages.activity-log.index', compact('logs'));
    }

    public function getDetails($id)
    {
        $log = ActivityLog::findOrFail($id);
        $payload = $log->data ? json_decode($log->data, true) : null;

        // If payload is empty but we have model info, try to fetch current data
        if (!$payload && $log->model_type && $log->model_id) {
            try {
                $modelClass = $log->model_type;
                $instance = $modelClass::find($log->model_id);
                if ($instance) {
                    // Try to load items if it's an Invoice or Transfer
                    if ($modelClass == \App\Models\Invoice::class) {
                        $instance->load('customer', 'invoiceItems.product');
                    } elseif ($modelClass == \App\Models\Transfer::class) {
                        $instance->load('fromBranch', 'toBranch', 'transferItems.product');
                    } elseif ($modelClass == \App\Models\ReturnTbl::class) {
                        $instance->load('customer', 'returnItems.product');
                    } elseif ($modelClass == \App\Models\ReturnPurchase::class) {
                        $instance->load('supplier', 'returnPurchaseItems.product');
                    } elseif ($modelClass == \App\Models\Purchase::class) {
                        $instance->load('supplier', 'purchaseItems.product');
                    } elseif ($modelClass == \App\Models\Product::class) {
                        $instance->load('category', 'brand');
                    } elseif ($modelClass == \App\Models\Damage::class) {
                        $instance->load('damageItems.product');
                    } elseif ($modelClass == \App\Models\User::class) {
                        $instance->load('role');
                    }
                    $payload = $instance->toArray();
                }
            } catch (\Exception $e) {
                // Silently fail if model no longer exists
            }
        }

        return response()->json([
            'time' => $log->created_at->format('M d, Y h:i:s A'),
            'user' => $log->user?->name ?? 'System',
            'action' => $log->action,
            'description' => $log->description,
            'ip' => $log->ip_address,
            'model' => $log->model_type ? class_basename($log->model_type) . " (ID: {$log->model_id})" : '-',
            'payload' => $payload
        ]);
    }

    public function destroyAll()
    {
        if (auth()->user()->role_id != 1 && !auth()->user()->isSuperAdmin()) {
            session()->flash('error', 'Only super admin can clear logs');
            return back();
        }
        
        ActivityLog::truncate();
        session()->flash('success', 'Activity logs cleared successfully');
        return back();
    }
}
