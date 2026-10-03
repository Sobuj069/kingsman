<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\CourierFraudCheckerService;
use Illuminate\Http\Request;

class CustomerFraudCheckController extends Controller
{
    protected $fraudChecker;

    public function __construct(CourierFraudCheckerService $fraudChecker)
    {
        $this->fraudChecker = $fraudChecker;
    }

    /**
     * Handle AJAX fraud check request for a customer identifier (ID or Phone)
     */
    public function check($identifier)
    {
        if (empty($identifier) || $identifier == 1) {
            return response()->json([
                'success' => true,
                'is_walkin' => true,
                'risk_level' => 'new_customer',
                'risk_label' => 'Walk-in Customer',
                'badge_class' => 'badge-secondary',
                'badge_bg' => '#64748b',
                'overall_success_rate' => 100,
                'total_parcels' => 0,
                'total_delivered' => 0,
                'total_cancelled' => 0,
                'warning_message' => null,
            ]);
        }

        $result = $this->fraudChecker->checkFraud($identifier);

        return response()->json($result);
    }
}
