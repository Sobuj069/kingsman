<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\StockAuditorService;
use App\Services\GeminiService;
use Illuminate\Http\Request;

class AIStockAuditorController extends Controller
{
    protected $auditor;
    protected $gemini;

    public function __construct(StockAuditorService $auditor, GeminiService $gemini)
    {
        $this->auditor = $auditor;
        $this->gemini = $gemini;
    }

    public function index()
    {
        $discrepancies = $this->auditor->getDiscrepancyList();
        return view('backend.pages.ai-auditor.index', compact('discrepancies'));
    }

    public function audit($id)
    {
        try {
            $auditData = $this->auditor->auditProduct($id);
            $analysis = $this->gemini->analyzeDiscrepancy($auditData);

            return response()->json([
                'success' => true,
                'data' => $auditData,
                'analysis' => $analysis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Audit failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function fix($id)
    {
        try {
            $newStock = $this->auditor->fixStock($id);
            return response()->json([
                'success' => true,
                'message' => 'Stock fixed successfully!',
                'new_stock' => $newStock
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fix stock: ' . $e->getMessage()
            ], 500);
        }
    }
}
