<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\ReturnTbl;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CourierFraudCheckerService
{
    /**
     * Check fraud and delivery history of a customer by phone or customer ID.
     *
     * @param string|int $identifier Phone number or Customer ID
     * @return array
     */
    public function checkFraud($identifier)
    {
        $customer = null;
        $phone = null;

        if (is_numeric($identifier) && strlen((string)$identifier) < 10) {
            $customer = Customer::find($identifier);
            if ($customer) {
                $phone = $customer->phone;
            }
        } else {
            $phone = (string)$identifier;
            $customer = Customer::where('phone', $phone)
                ->orWhere('phone', 'LIKE', '%' . substr($phone, -10))
                ->first();
        }

        // Clean & normalize phone number
        $cleanPhone = $this->normalizePhone($phone ?? $identifier);

        // 1. Fetch Internal POS History
        $internalStats = $this->getInternalStats($customer, $cleanPhone);

        // 2. Fetch Steadfast Courier Fraud Data (API with Caching + Local DB)
        $steadfastStats = $this->getSteadfastFraudData($cleanPhone, $customer);

        // 3. Fetch Pathao Courier Fraud Data (API + Local DB)
        $pathaoStats = $this->getPathaoFraudData($cleanPhone, $customer);

        // 4. Fetch General / Multi-Courier Aggregator Data (Fallback)
        $aggregatorStats = $this->getAggregatorFraudData($cleanPhone);

        // Aggregate All Data
        $totalOrders = $internalStats['total_orders'] 
            + ($steadfastStats['total_parcels'] ?? 0) 
            + ($pathaoStats['total_parcels'] ?? 0) 
            + ($aggregatorStats['total_parcels'] ?? 0);

        $totalDelivered = $internalStats['delivered'] 
            + ($steadfastStats['delivered'] ?? 0) 
            + ($pathaoStats['delivered'] ?? 0) 
            + ($aggregatorStats['delivered'] ?? 0);

        $totalCancelled = $internalStats['returned'] 
            + ($steadfastStats['cancelled'] ?? 0) 
            + ($pathaoStats['cancelled'] ?? 0) 
            + ($aggregatorStats['cancelled'] ?? 0);

        // Calculate Overall Success Rate
        $successRate = $totalOrders > 0 ? round(($totalDelivered / $totalOrders) * 100, 1) : 100;

        // Evaluate Risk Level
        $riskEvaluation = $this->evaluateRiskLevel($totalOrders, $totalDelivered, $totalCancelled, $successRate);

        return [
            'success'               => true,
            'phone'                 => $cleanPhone,
            'customer_id'           => $customer?->id,
            'customer_name'         => $customer?->name ?? 'Customer (' . $cleanPhone . ')',
            'risk_level'            => $riskEvaluation['level'],       // low_risk, medium_risk, high_risk, new_customer
            'risk_label'            => $riskEvaluation['label'],       // Safe, Moderate Risk, High Risk (Fraud Alert!)
            'badge_class'           => $riskEvaluation['badge_class'], // success, warning, danger, secondary
            'badge_bg'              => $riskEvaluation['badge_bg'],
            'overall_success_rate'  => $successRate,
            'total_parcels'         => $totalOrders,
            'total_delivered'       => $totalDelivered,
            'total_cancelled'       => $totalCancelled,
            'warning_message'       => $riskEvaluation['warning_message'],
            'sources'               => [
                'internal'  => $internalStats,
                'steadfast' => $steadfastStats,
                'pathao'    => $pathaoStats,
                'courier_api' => $aggregatorStats,
            ]
        ];
    }

    /**
     * Clean and normalize BD phone numbers to 11 digits format (e.g. 01700000000)
     */
    public function normalizePhone($phone)
    {
        if (!$phone) return '';
        // Strip non-numeric characters
        $clean = preg_replace('/[^0-9]/', '', (string)$phone);
        
        // Remove 880 prefix if present
        if (str_starts_with($clean, '8801')) {
            $clean = substr($clean, 2);
        } elseif (str_starts_with($clean, '88001')) {
            $clean = substr($clean, 3);
        }

        // Add leading zero if missing for 10-digit number
        if (strlen($clean) === 10 && str_starts_with($clean, '1')) {
            $clean = '0' . $clean;
        }

        return $clean;
    }

    /**
     * Internal POS history calculation
     */
    protected function getInternalStats($customer, $phone)
    {
        if (!$customer && $phone) {
            $customer = Customer::where('phone', 'LIKE', "%{$phone}%")->first();
        }

        if (!$customer) {
            return [
                'available'    => false,
                'total_orders' => 0,
                'delivered'    => 0,
                'returned'     => 0,
                'success_rate' => 0,
            ];
        }

        $totalInvoices = Invoice::where('customer_id', $customer->id)->count();
        $totalReturns  = ReturnTbl::where('customer_id', $customer->id)->count();
        $delivered = max(0, $totalInvoices - $totalReturns);

        $successRate = $totalInvoices > 0 ? round(($delivered / $totalInvoices) * 100, 1) : 100;

        return [
            'available'    => true,
            'total_orders' => $totalInvoices,
            'delivered'    => $delivered,
            'returned'     => $totalReturns,
            'success_rate' => $successRate,
        ];
    }

    /**
     * Steadfast Courier Fraud Checker API Integration (With 1-Hour Cache to prevent 429 Rate Limits)
     */
    protected function getSteadfastFraudData($phone, $customer = null)
    {
        $apiTotal = 0;
        $apiDelivered = 0;
        $apiCancelled = 0;
        $isAvailable = false;

        if ($phone && strlen($phone) >= 11) {
            $cacheKey = 'sf_fraud_api_' . $phone;

            $cachedApi = Cache::get($cacheKey);
            if ($cachedApi && is_array($cachedApi)) {
                $apiTotal = (int)($cachedApi['total_parcels'] ?? 0);
                $apiDelivered = (int)($cachedApi['total_delivered'] ?? 0);
                $apiCancelled = (int)($cachedApi['total_cancelled'] ?? 0);
                $isAvailable = true;
            } else {
                try {
                    $apiKey = function_exists('get_setting') ? get_setting('steadfast_api_key') : env('STEADFAST_API_KEY');
                    $secretKey = function_exists('get_setting') ? get_setting('steadfast_secret_key') : env('STEADFAST_SECRET_KEY');

                    $headers = [
                        'Accept' => 'application/json',
                    ];

                    if ($apiKey && $secretKey) {
                        $headers['Api-Key'] = $apiKey;
                        $headers['Secret-Key'] = $secretKey;
                    }

                    $urls = [
                        "https://portal.packzy.com/api/v1/fraud_check/{$phone}",
                        "https://packzy.com/api/v1/fraud_check/{$phone}",
                        "https://portal.steadfast.com.bd/api/v1/fraud_check/{$phone}"
                    ];

                    foreach ($urls as $url) {
                        try {
                            $response = Http::withHeaders($headers)->timeout(5)->get($url);
                            if ($response->successful()) {
                                $data = $response->json();
                                if (isset($data['total_parcels']) || isset($data['data'])) {
                                    $resData = $data['data'] ?? $data;
                                    $apiTotal = (int)($resData['total_parcels'] ?? 0);
                                    $apiDelivered = (int)($resData['total_delivered'] ?? $resData['success_parcels'] ?? 0);
                                    $apiCancelled = (int)($resData['total_cancelled'] ?? $resData['cancelled_parcels'] ?? 0);
                                    $isAvailable = true;

                                    // Cache successful response for 1 hour
                                    Cache::put($cacheKey, [
                                        'total_parcels' => $apiTotal,
                                        'total_delivered' => $apiDelivered,
                                        'total_cancelled' => $apiCancelled,
                                    ], 3600);

                                    break;
                                }
                            }
                        } catch (\Exception $ex) {
                            continue;
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning("Steadfast Fraud Check API call failed: " . $e->getMessage());
                }
            }
        }

        // Local POS Steadfast Courier orders
        $localTotal = 0;
        $localDelivered = 0;
        $localCancelled = 0;

        if ($customer) {
            $steadfastInvoices = Invoice::where('customer_id', $customer->id)
                ->where('courier_type', 'LIKE', '%stead%')
                ->get();
            
            $localTotal = $steadfastInvoices->count();
            if ($localTotal > 0 && !$isAvailable) {
                $isAvailable = true;
                foreach ($steadfastInvoices as $inv) {
                    if (in_array(strtolower((string)$inv->order_status), ['returned', 'cancelled', 'return'])) {
                        $localCancelled++;
                    } else {
                        $localDelivered++;
                    }
                }
            }
        }

        if ($apiTotal > 0) {
            $total = $apiTotal;
            $delivered = $apiDelivered;
            $cancelled = $apiCancelled;
        } else {
            $total = $localTotal;
            $delivered = $localDelivered;
            $cancelled = $localCancelled;
        }

        $successRate = $total > 0 ? round(($delivered / $total) * 100, 1) : null;

        return [
            'available'     => $isAvailable,
            'total_parcels' => $total,
            'delivered'     => $delivered,
            'cancelled'     => $cancelled,
            'success_rate'  => $successRate,
        ];
    }

    /**
     * Pathao Courier Fraud / History API Integration
     */
    protected function getPathaoFraudData($phone, $customer = null)
    {
        $apiTotal = 0;
        $apiDelivered = 0;
        $apiCancelled = 0;
        $isAvailable = false;

        if ($phone && strlen($phone) >= 11) {
            try {
                $token = function_exists('getPathaoAccessToken') ? getPathaoAccessToken() : null;
                if ($token) {
                    $urls = [
                        "https://api-hermes.pathao.com/albatross/api/v1/fraud-check/{$phone}",
                        "https://api-hermes.pathao.com/albatross/api/v1/user/fraud-check?phone={$phone}"
                    ];

                    foreach ($urls as $url) {
                        try {
                            $response = Http::withToken($token)->timeout(4)->get($url);
                            if ($response->successful()) {
                                $data = $response->json();
                                if (isset($data['total_parcels']) || isset($data['data'])) {
                                    $resData = $data['data'] ?? $data;
                                    $apiTotal = (int)($resData['total_parcels'] ?? 0);
                                    $apiDelivered = (int)($resData['delivered'] ?? $resData['total_delivered'] ?? 0);
                                    $apiCancelled = (int)($resData['cancelled'] ?? $resData['total_cancelled'] ?? 0);
                                    $isAvailable = true;
                                    break;
                                }
                            }
                        } catch (\Exception $ex) {
                            continue;
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Pathao Fraud Check API call failed: " . $e->getMessage());
            }
        }

        // Local POS Pathao Courier orders
        $localTotal = 0;
        $localDelivered = 0;
        $localCancelled = 0;

        if ($customer) {
            $pathaoInvoices = Invoice::where('customer_id', $customer->id)
                ->where('courier_type', 'LIKE', '%pathao%')
                ->get();

            $localTotal = $pathaoInvoices->count();
            if ($localTotal > 0 && !$isAvailable) {
                $isAvailable = true;
                foreach ($pathaoInvoices as $inv) {
                    if (in_array(strtolower((string)$inv->order_status), ['returned', 'cancelled', 'return'])) {
                        $localCancelled++;
                    } else {
                        $localDelivered++;
                    }
                }
            }
        }

        if ($apiTotal > 0) {
            $total = $apiTotal;
            $delivered = $apiDelivered;
            $cancelled = $apiCancelled;
        } else {
            $total = $localTotal;
            $delivered = $localDelivered;
            $cancelled = $localCancelled;
        }

        $successRate = $total > 0 ? round(($delivered / $total) * 100, 1) : null;

        return [
            'available'     => $isAvailable,
            'total_parcels' => $total,
            'delivered'     => $delivered,
            'cancelled'     => $cancelled,
            'success_rate'  => $successRate,
        ];
    }

    /**
     * Aggregator Public Courier Fraud Checker (Fallback API)
     */
    protected function getAggregatorFraudData($phone)
    {
        if (!$phone || strlen($phone) < 11) {
            return ['available' => false, 'total_parcels' => 0, 'delivered' => 0, 'cancelled' => 0];
        }

        try {
            // General Bangladesh Courier Fraud Checker API Endpoint
            $customApiUrl = function_exists('get_setting') ? get_setting('courier_fraud_api_url') : null;
            $customApiKey  = function_exists('get_setting') ? get_setting('courier_fraud_api_key') : null;

            if ($customApiUrl) {
                $req = Http::timeout(4);
                if ($customApiKey) {
                    $req->withHeaders(['Authorization' => 'Bearer ' . $customApiKey]);
                }
                $response = $req->get("{$customApiUrl}/check/{$phone}");

                if ($response->successful()) {
                    $res = $response->json();
                    $total = (int)($res['total_parcels'] ?? 0);
                    $delivered = (int)($res['delivered'] ?? 0);
                    $cancelled = (int)($res['cancelled'] ?? 0);
                    return [
                        'available'     => true,
                        'total_parcels' => $total,
                        'delivered'     => $delivered,
                        'cancelled'     => $cancelled,
                        'success_rate'  => $total > 0 ? round(($delivered / $total) * 100, 1) : null,
                    ];
                }
            }
        } catch (\Exception $e) {
            // Silent fallback
        }

        return ['available' => false, 'total_parcels' => 0, 'delivered' => 0, 'cancelled' => 0];
    }

    /**
     * Evaluate overall Risk Level based on aggregated stats
     */
    protected function evaluateRiskLevel($totalOrders, $totalDelivered, $totalCancelled, $successRate)
    {
        if ($totalOrders === 0) {
            return [
                'level'           => 'new_customer',
                'label'           => 'New / No Courier History',
                'badge_class'     => 'badge-secondary',
                'badge_bg'        => '#64748b',
                'warning_message' => null,
            ];
        }

        // Fraud rules:
        // High risk if cancelled > delivered AND totalCancelled >= 1, or successRate < 50%
        if (($totalCancelled >= 1 && $totalCancelled >= $totalDelivered) || $successRate < 50.0) {
            return [
                'level'           => 'high_risk',
                'label'           => 'High Risk (Fraud Alert!)',
                'badge_class'     => 'badge-danger',
                'badge_bg'        => '#ef4444',
                'warning_message' => "⚠️ Fraud Warning: This customer has {$totalCancelled} cancelled/returned orders out of {$totalOrders} total orders (" . (100 - $successRate) . "% return rate). Advance payment recommended!",
            ];
        }

        // Medium risk if success rate is between 50% and 79%, or has some returns
        if ($successRate < 80.0 || $totalCancelled >= 1) {
            return [
                'level'           => 'medium_risk',
                'label'           => 'Moderate Risk',
                'badge_class'     => 'badge-warning',
                'badge_bg'        => '#f59e0b',
                'warning_message' => "⚠️ Caution: Customer has {$totalCancelled} returned/cancelled parcels ({$successRate}% success rate).",
            ];
        }

        // Low Risk / Safe
        return [
            'level'           => 'low_risk',
            'label'           => 'Safe / Reliable Customer',
            'badge_class'     => 'badge-success',
            'badge_bg'        => '#10b981',
            'warning_message' => null,
        ];
    }
}
