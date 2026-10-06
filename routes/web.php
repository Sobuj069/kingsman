<?php

use App\Models\Customer;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\OthersController;
use App\Http\Controllers\PathaoController;
use App\Http\Controllers\OwnershipController;
use App\Http\Controllers\Backend\PDFController;
use App\Http\Controllers\Backend\SmsController;
use App\Http\Controllers\Backend\SizeController;
use App\Http\Controllers\Backend\UnitController;
use App\Http\Controllers\Backend\UsedController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\RackController;
use App\Http\Controllers\Backend\PlatformController;
use App\Http\Controllers\Backend\MarketingController;
use App\Http\Controllers\Backend\ColorController;
use App\Http\Controllers\Backend\BranchController;
use App\Http\Controllers\Backend\DamageController;
use App\Http\Controllers\Backend\ReportController;
use App\Http\Controllers\Backend\ExpenseController;
use App\Http\Controllers\Backend\AssetController;
use App\Http\Controllers\Backend\InvoiceController;
use App\Http\Controllers\Backend\QuotationController;
use App\Http\Controllers\Backend\PaymentController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\ServiceController;
use App\Http\Controllers\Backend\ServiceReceiveController;
use App\Http\Controllers\Backend\ServiceInvoiceController;
use App\Http\Controllers\Backend\ServiceCenterController;
use App\Http\Controllers\Backend\WarrantyController;
use App\Http\Controllers\Backend\WarrantyClaimController;
use App\Http\Controllers\Backend\WarrantyDeliveryController;
use App\Http\Controllers\Backend\SupplierClaimController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\SubCategoryController;
use App\Http\Controllers\Backend\ChildCategoryController;
use App\Http\Controllers\Backend\BannerController;
use App\Http\Controllers\Backend\CustomerController;
use App\Http\Controllers\Backend\WebOrderController;
use App\Http\Controllers\Backend\DiscountGroupController;
use App\Http\Controllers\Backend\EmployeeController;
use App\Http\Controllers\Backend\PurchaseController;
use App\Http\Controllers\Backend\SupplierController;
use App\Http\Controllers\BusinessSettingsController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\VariationController;
use App\Http\Controllers\Backend\ReturnSaleController;
use App\Http\Controllers\Backend\BankAccountController;
use App\Http\Controllers\Backend\StockAdjustController;
use App\Http\Controllers\Backend\StockAuditController;
use App\Http\Controllers\Backend\UsedProductController;
use App\Http\Controllers\Backend\UserProfileController;
use App\Http\Controllers\Backend\StockTransferController;
use App\Http\Controllers\Backend\ReturnPurchaseController;
use App\Http\Controllers\Backend\BankTransactionController;
use App\Http\Controllers\Backend\RolesPermissionController;
use App\Http\Controllers\Backend\ActivityLogController;
use App\Http\Controllers\Backend\UsedProductPurchaseController;
use App\Http\Controllers\Backend\AIStockAuditorController;
use App\Http\Controllers\Backend\AIChatbotController;
use App\Http\Controllers\Backend\PayrollController;
use App\Http\Controllers\Backend\DepartmentController;
use App\Http\Controllers\Backend\DesignationController;
use App\Http\Controllers\Backend\LeaveTypeController;
use App\Http\Controllers\Backend\LeaveApplicationController;
use App\Http\Controllers\Backend\AttendanceController;
use App\Http\Controllers\Backend\SuperAdminSettingsController;
use App\Http\Controllers\Backend\PreOrderController;
use App\Http\Controllers\FrontendController;

Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/products', [FrontendController::class, 'products'])->name('products.index');
Route::get('/category/{slug}', [FrontendController::class, 'category'])->name('category.products');
Route::get('/collection/{slug?}', [FrontendController::class, 'category'])->name('collection.products');
Route::get('/product/{id}', [FrontendController::class, 'productDetails'])->name('product.details')->whereNumber('id');
Route::get('/cart', [FrontendController::class, 'cart'])->name('cart');
Route::post('/cart/validate-stock', [FrontendController::class, 'validateCartStock'])->name('cart.validate-stock');
Route::get('/wishlist', [FrontendController::class, 'wishlist'])->name('frontend.wishlist');
Route::get('/my-wishlist', [FrontendController::class, 'wishlist']);
Route::get('/checkout', [FrontendController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [FrontendController::class, 'processCheckout'])->name('checkout.process');
Route::get('/order-confirmation/{order_id?}', [FrontendController::class, 'orderConfirmation'])->name('order.confirmation');
Route::get('/order-success/{order_id?}', [FrontendController::class, 'orderConfirmation'])->name('order.success');
Route::get('/search', [FrontendController::class, 'search'])->name('frontend.search');

// Static Information & Policy Routes
Route::get('/about-us', [FrontendController::class, 'about'])->name('frontend.about');
Route::get('/about', [FrontendController::class, 'about']);
Route::get('/contact-us', [FrontendController::class, 'contact'])->name('frontend.contact');
Route::get('/contact', [FrontendController::class, 'contact']);
Route::get('/return-policy', [FrontendController::class, 'returnPolicy'])->name('frontend.return-policy');
Route::get('/privacy-policy', [FrontendController::class, 'privacyPolicy'])->name('frontend.privacy-policy');
Route::get('/refund-policy', [FrontendController::class, 'refundPolicy'])->name('frontend.refund-policy');
Route::get('/terms-conditions', [FrontendController::class, 'terms'])->name('frontend.terms');
Route::get('/terms', [FrontendController::class, 'terms']);

// CLear
Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');

    return view('auth.login');
});

// OTP Verification Routes
Route::get('/verify-otp', [\App\Http\Controllers\Backend\OtpVerificationController::class, 'show'])->name('otp.verify');
Route::post('/verify-otp', [\App\Http\Controllers\Backend\OtpVerificationController::class, 'verify']);

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    // Branch Selection Routes
    Route::get('/select-branch', [App\Http\Controllers\Backend\BranchSelectionController::class, 'index'])->name('branch.select');
    Route::post('/set-branch',  [App\Http\Controllers\Backend\BranchSelectionController::class, 'select'])->name('branch.set');
});

// ইনভয়েস বারকোড জেনারেট করার জন্য আলাদা রাউট
Route::get('/invoice-barcode/{code}', function ($code) {
    $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
    $barcode = $generator->getBarcode($code, $generator::TYPE_CODE_128);
    return response($barcode)->header('Content-Type', 'image/png');
})->name('invoice.barcode');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'permission', 'branch.check'])->group(function () {
    // Route::controller(DashboardController::class)->group(function (){

    //     Route::get('/dashboard', 'index')->name('dashboard');
    //     Route::get('/dashboard/today', 'today')->name('dashboard.dashboard');
    // });
    Route::controller(DashboardController::class)->group(function () {

        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/dashboard/today', 'today')->name('dashboard.dashboard');

        Route::get('/dashboard/filter', 'filterData')->name('dashboard.filter');
        Route::get('/dashboard/product-sales-summary', 'productSalesSummary')->name('dashboard.product-sales-summary');
        Route::get('/dashboard/product-sales-summary/print', 'printProductSalesSummary')->name('dashboard.product-sales-summary.print');
        Route::get('/dashboard/product-sales-summary/pdf', 'downloadProductSalesSummaryPDF')->name('dashboard.product-sales-summary.pdf');

    });
    Route::post('/download-note', [DashboardController::class, 'downloadPDF'])->name('note.download');

    // --------------------- POS ---------------------
    Route::controller(InvoiceController::class)->group(function () {
        Route::post('/invoice/log/store',  'storeInvoiceLog')->name('storeInvoiceLog');
        Route::get('/invoice/pay/{id}', 'invoicePay')->name('invoice.pay');
        Route::get('/invoice/eedit/{id}', 'invoiceEdit')->name('inv.edit');
        Route::post('/invoice/update/{id}', 'invoiceUpdate')->name('invoice.up');
        Route::get('/invoice/exchange/{id}', 'invoiceExchange')->name('invoice.exchange');
        Route::get('/print/invoice/{id}', 'printInvoice')->name('invoice.print');
        Route::get('/print/invoice/due/{id}', 'dueInvoicePrint')->name('due.invoice.print');
        Route::post('/invoice/return/{id}', 'returnInvoice')->name('invoice.return');
        Route::post('/invoice/return/amount/{id}', 'returnAmount')->name('invoice.return.amount');
        Route::post('/invoice/exchange/update', 'updateExchangeInvoice')->name('invoice.ex.update');
        Route::get('/invoice/online/sale', 'onlineSale')->name('invoice.online.sale');
        Route::post('/invoice/online/clear-selected-dues', 'clearSelectedDues')->name('invoice.online.clear-selected-dues');
        Route::get('/invoice/offline-data', 'getOfflineData')->name('invoice.offline-data');
        // POS Hold System
        Route::post('/invoice/hold/store', 'holdStore')->name('invoice.hold.store');
        Route::get('/invoice/hold/list', 'holdList')->name('invoice.hold.list');
        Route::match(['post', 'delete'], '/invoice/hold/delete/{id}', 'holdDelete')->name('invoice.hold.delete');
        Route::get('/invoice/hold/get/{id}', 'holdGet')->name('invoice.hold.get');
        // Route::get('/invoice/due/print/{id}', 'dueInvoicePrint')->name('due.invoice.print');

    });
    Route::resource('invoice', InvoiceController::class);

    // --------------------> Web Order System (Incoming Website Orders) <--------------------
    Route::controller(WebOrderController::class)->group(function () {
        Route::get('/web-orders', 'index')->name('web-orders.index');
        Route::post('/web-orders/update/{id}', 'update')->name('web-orders.update');
        Route::post('/web-orders/send-to-courier/{id}', 'sendToCourier')->name('web-orders.send-to-courier');
        Route::post('/web-orders/bulk-courier', 'bulkSendToCourier')->name('web-orders.bulk-courier');
        Route::post('/web-orders/cancel/{id}', 'cancel')->name('web-orders.cancel');
        Route::delete('/web-orders/delete/{id}', 'destroy')->name('web-orders.destroy');
    });

    // --------------------> Pre-Order System (APP_ONLINE == 'yes') <--------------------
    Route::controller(PreOrderController::class)->group(function () {
        Route::get('/pre-orders', 'index')->name('pre-orders.index');
        Route::get('/pre-order/show/{id}', 'show')->name('pre-orders.show');
        Route::post('/pre-order/store', 'store')->name('pre-orders.store');
        Route::get('/pre-order/edit/{id}', 'edit')->name('pre-orders.edit');
        Route::post('/pre-order/update/{id}', 'update')->name('pre-orders.update');
        Route::post('/pre-order/convert/{id}', 'convertToSale')->name('pre-orders.convert');
        Route::post('/pre-order/cancel/{id}', 'cancel')->name('pre-orders.cancel');
        Route::delete('/pre-order/delete/{id}', 'destroy')->name('pre-orders.destroy');
    });
    Route::get('/print/quotation/{id}', [QuotationController::class, 'printQuotation'])->name('quotation.print');
    Route::resource('quotation', QuotationController::class);

    // --------------------> Purchase <--------------------
    Route::controller(PurchaseController::class)->group(function () {
        Route::post('/purchase/log/store',  'storePurchaseLog')->name('storePurchaseLog');
        Route::get('/purchase/pay/{id}', 'purchasePay')->name('purchase.pay');
        Route::get('/purchase/edit/{id}', 'purchaseEdit')->name('purchase.edit');
        Route::post('/purchase/update/{id}', 'purchaseUpdate')->name('purchase.updat');
        Route::get('/print/purchase/{id}', 'printPurchase')->name('purchase.print');
        Route::get('/print/return/purchase/{id}', 'printReturnPurchase')->name('purchase.return.print');
        Route::get('/imei-print/purchase/{id}', 'imeiPrintPurchase')->name('purchase.imei-print');

        Route::get('/purchase/search', 'search')->name('purchase.search');
    });
    Route::resource('purchase', PurchaseController::class)->except(['edit']);
    Route::get('/get-purchase-no', [PurchaseController::class, 'getPurchaseNo'])->name('get.purchase.no');


    // ----------------->  used product section <----------------
    Route::resource('usedProduct', UsedProductController::class);
    Route::resource('usedPurchase', UsedProductPurchaseController::class);
    Route::resource('used', UsedController::class);

    // --------------------> customers & discount groups <--------------------
    Route::resource('customer', CustomerController::class)->except(['show', 'edit', 'create']);
    Route::resource('discount-group', DiscountGroupController::class);
    Route::get('/customer/upcoming-wishlist', [CustomerController::class, 'upcomingWishlist'])->name('customer.upcoming-wishlist');
    Route::get('/customer/previous-due', [CustomerController::class, 'getPreviousDue'])->name('customer.previous.due');
    Route::get('/get-suppliers-by-branch', [SupplierController::class, 'getSuppliersByBranch'])->name('get.suppliers.by.branch');
    Route::get('/get-customer-by-branch', [CustomerController::class, 'getCustomersByBranch'])->name('get.customers.by.branch');

    // --------------------> installments --------------------
    Route::controller(\App\Http\Controllers\Backend\InstallmentController::class)->group(function () {
        Route::get('/installments', 'index')->name('installments.index');
        Route::get('/installments/today-due', 'todayDue')->name('installments.today-due');
        Route::get('/installments/today-collection', 'todayCollection')->name('installments.today-collection');
        Route::get('/installments/overdue', 'overdueList')->name('installments.overdue');
        Route::get('/installments/completed', 'completed')->name('installments.completed');
        Route::get('/installments/{id}', 'show')->name('installments.show');
        Route::post('/installments/schedule/{id}/collect', 'collectPayment')->name('installments.collect');
    });


    // --------------------> branch <--------------------
    Route::resource('branch', BranchController::class);

    Route::post('/switch-branch', [BranchController::class, 'switch'])->name('switch.branch');

    // --------------------> transfer <--------------------
    Route::resource('transfer', StockTransferController::class);
    Route::resource('stock-adjust', StockAdjustController::class);

    // --------------------> Stock Audit <--------------------
    Route::controller(StockAuditController::class)->prefix('stock-audit')->name('stock-audit.')->group(function () {
        Route::get('/product-scan', 'productScan')->name('product-scan');
        Route::get('/product-suggestions', 'productSuggestions')->name('product-suggestions');
        Route::get('/load-category-products', 'loadCategoryProducts')->name('load-category-products');
        Route::get('/print/{id}', 'print')->name('print');
    });
    Route::resource('stock-audit', StockAuditController::class);

    Route::controller(StockTransferController::class)->group(function () {
        Route::post('transfer/receive/{id}', 'transferReceive')->name('transfer.receive');
        Route::post('transfer/cancel/{id}', 'transferCancel')->name('transfer.cancel');
        Route::get('transfer/product/print/{id}', 'transferPrint')->name('transfer.print');
    });
    Route::controller(EmployeeController::class)->group(function () {
        Route::get('employee/salary/details/{id}', 'salaryDetails')->name('employee.salary.details');
        Route::post('employee/payment/{id}', 'PaymentStore')->name('employee.payment.store');
    });

    Route::get('/customer/points/{id}', function ($id) {
        if (env('APP_LOYALTY') != 'yes') {
            return response()->json(['total_point' => 0, 'point_rate' => 0.75, 'point_value' => 0]);
        }
        $customer = Customer::find($id);
        $points = $customer ? (float)$customer->total_point : 0;
        $rate = 0.75;
        $value = round($points * $rate, 2);
        return response()->json([
            'total_point' => $points,
            'point_rate'  => $rate,
            'point_value' => $value
        ]);
    });

    // Customer Quick History for POS
    Route::get('/customer/quick-history/{id}', [CustomerController::class, 'quickHistory'])->name('customer_quick_history');
    Route::get('/customer/fraud-check/{identifier}', [\App\Http\Controllers\Backend\CustomerFraudCheckController::class, 'check'])->name('customer.fraud-check');

    // --------------------> employee <--------------------
    Route::resource('employee', EmployeeController::class);

    // --------------------> Payroll Module <--------------------
    Route::group(['prefix' => 'payroll', 'as' => 'payroll.'], function () {
        // Department
        Route::resource('department', DepartmentController::class);
        
        // Designation
        Route::resource('designation', DesignationController::class);

        // Leave Type
        Route::resource('leave-type', LeaveTypeController::class);

        // Leave Application
        Route::resource('leave-application', LeaveApplicationController::class);
        Route::post('leave-application/status/{id}', [LeaveApplicationController::class, 'updateStatus'])->name('leave-application.status');

        // Attendance
        Route::resource('attendance', AttendanceController::class);
        
        // Salary Sheet
        Route::controller(PayrollController::class)->prefix('salary-sheet')->as('salary-sheet.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/generate', 'generate')->name('generate');
            Route::post('/pay/{id}', 'pay')->name('pay');
            Route::post('/update/{id}', 'update')->name('update');
        });
    });

    // --------------------> ownership <--------------------
    Route::resource('ownership', OwnershipController::class);

    // --------------------> suppliers <--------------------
    Route::resource('supplier', SupplierController::class)->except(['show', 'edit', 'create']);

    // --------------------> units <--------------------
    Route::resource('unit', UnitController::class)->except(['show', 'edit', 'create']);

    // --------------------> category <--------------------
    Route::resource('category', CategoryController::class)->except(['show', 'edit', 'create']);

    // --------------------> subcategory <--------------------
    Route::get('/get-subcategories-by-category', [SubCategoryController::class, 'getByCategory'])->name('sub-category.by-category');
    Route::resource('sub-category', SubCategoryController::class)->except(['show', 'edit', 'create']);

    // --------------------> child category <--------------------
    Route::get('/get-childcategories-by-subcategory', [ChildCategoryController::class, 'getBySubCategory'])->name('child-category.by-subcategory');
    Route::resource('child-category', ChildCategoryController::class)->except(['show', 'edit', 'create']);

    // --------------------> color <--------------------
    Route::resource('color', ColorController::class);
    Route::post('/color/ajax-store', [ColorController::class, 'ajaxStore'])->name('color.ajaxStore');

    // --------------------> color <--------------------
    Route::resource('size', SizeController::class);
    Route::post('/size/ajax-store', [SizeController::class, 'ajaxStore'])->name('size.ajaxStore');

    // --------------------> variation <--------------------
    Route::resource('variation', VariationController::class);

    // --------------------> brands <--------------------
    Route::resource('brand', BrandController::class)->except(['show', 'edit', 'create']);

    // --------------------> rack --------------------
    Route::resource('rack', RackController::class)->except(['show', 'edit', 'create']);
    Route::post('/rack/ajax-store', [RackController::class, 'ajaxStore'])->name('rack.ajaxStore');
    Route::post('/product/{id}/update-racks', [ProductController::class, 'updateRacks'])->name('product.update-racks');

    // --------------------> platforms <--------------------
    Route::resource('platform', PlatformController::class)->except(['show', 'edit', 'create']);

    // --------------------> banners <--------------------
    Route::post('/banner/{id}/status', [BannerController::class, 'toggleStatus'])->name('banner.status');
    Route::resource('banner', BannerController::class)->except(['show', 'edit', 'create']);

    // --------------------> marketing & ad costs <--------------------
    Route::controller(MarketingController::class)->group(function () {
        Route::get('ad-cost', 'index')->name('ad-cost.index');
        Route::get('ad-cost/create', 'create')->name('ad-cost.create');
        Route::post('ad-cost', 'store')->name('ad-cost.store');
        Route::delete('ad-cost/{id}', 'destroy')->name('ad-cost.destroy');
        Route::get('roi-tracking', 'roi')->name('roi.tracking');
        Route::get('courier-tracking', 'courierTracking')->name('courier.tracking');
    });

    // --------------------> sms <--------------------
    Route::resource('sms', SmsController::class)->except(['show', 'edit', 'create']);

    Route::get('/generate-variations', [VariationController::class, 'generateVariations']);
    Route::get('/variations', [VariationController::class, 'getVariations']);

    // --------------------> products <--------------------
    // Route::resource('product', ProductController::class);
    Route::controller(ProductController::class)->group(function () {
        Route::get('/product', 'index')->name('product.index');
        Route::get('/product/create', 'create')->name('product.create');
        Route::post('/product/store', 'store')->name('product.store');
        // Route::get('/product/edit/{}','edit/')->name('product.edit/');
        Route::get('/product/edit/{id}', 'edit')->name('product.edit');
        Route::post('/product/update/{id}', 'update')->name('product.update');
        Route::post('/product/destroy/{id}', 'destroy')->name('product.destroy');
        Route::get('/product/search', 'search')->name('product.search');
        Route::get('/multiple/barcode', 'multipleBarcode')->name('multiple.barcode');
        Route::post('/multiple/barcode/print', 'print')->name('multiple.barcode-print')->withoutMiddleware('permission');
        Route::post('/product/update-description/{id}', 'updateDescription')->name('product.update-description');
    });

    // --------------------> Service controller <--------------------
    Route::resource('service', ServiceController::class);
    Route::resource('service-receive', ServiceReceiveController::class);
    Route::resource('service-invoice', ServiceInvoiceController::class);

    // --------------------> Warranty Management <--------------------
    Route::get('warranty/get-all', [WarrantyController::class, 'getWarranties'])->name('warranty.get-all');
    Route::resource('warranty', WarrantyController::class);
    Route::resource('service-center', ServiceCenterController::class);
    Route::get('warranty-claim/ajax-search', [WarrantyClaimController::class, 'ajaxSearch'])->name('warranty-claim.ajax-search');
    Route::resource('warranty-claim', WarrantyClaimController::class);
    Route::get('warranty-claim/check/{id}', [WarrantyClaimController::class, 'check'])->name('warranty-claim.check');
    Route::post('warranty-claim/check/{id}', [WarrantyClaimController::class, 'updateChecking'])->name('warranty-claim.update-check');
    
    Route::resource('warranty-delivery', WarrantyDeliveryController::class);
    Route::resource('supplier-claim', SupplierClaimController::class);

    // --------------------> Bank Account <--------------------
    Route::resource('bank-account', BankAccountController::class);

    // --------------------> Bank Transaction <--------------------
    Route::controller(BankTransactionController::class)->group(function () {
        // --------------------> Deposit <--------------------
        Route::get('/deposit', 'deposit')->name('deposit-create');
        Route::post('/deposit', 'depositStore')->name('deposit-store');

        // --------------------> Withdraw <--------------------
        Route::get('/withdraw', 'withdraw')->name('withdraw-create');
        Route::post('/withdraw', 'withdrawStore')->name('withdraw-store');

        // --------------------> Bank Transfer <--------------------
        Route::get('/bank-transfer-index', 'bankTransferIndex')->name('bank-transfer-index');
        Route::post('/bank-transfer', 'bankTransferStore')->name('bank-transfer-store');

        // --------------------> Transaction <--------------------
        Route::get('/transaction-history', 'transactionHistory')->name('transaction-history');
    });

    // --------------------> Payment <--------------------
    Route::controller(PaymentController::class)->prefix('payment')->name('payment.')->group(function () {
        // --------------------> Deposit <--------------------
        Route::get('/customer', 'payCustomer')->name('pay-customer');
        Route::post('/customer/store', 'payCustomerStore')->name('pay-customer-store');

        // --------------------> Deposit <--------------------
        Route::get('/supplier', 'paySupplier')->name('pay-supplier');
        Route::post('/supplier/store', 'paySupplierStore')->name('pay-supplier-store');

        // --------------------> destroy <--------------------
        Route::match(['get', 'post', 'delete'], '/customer/destroy/{id}', 'customerDestroy')->name('customer-destroy');
        Route::match(['get', 'post', 'delete'], '/supplier/destroy/{id}', 'supplierDestroy')->name('supplier-destroy');
    });

    // --------------------> expense <--------------------
    Route::resource('expense', ExpenseController::class);
    Route::controller(ExpenseController::class)->prefix('expense')->name('expense.')->group(function () {
        // --------------------> category <--------------------
        Route::get('/category/index', 'categoryIndex')->name('category-index');
        Route::post('/category/store', 'categoryStore')->name('category-store');
        Route::post('/category/update/{id}', 'categoryUpdate')->name('category-update');
        Route::delete('/category/destroy/{id}', 'categoryDestroy')->name('category-destroy');
    });

    // --------------------> asset <--------------------
    Route::resource('asset', AssetController::class)->except(['show', 'edit', 'update']);

    // --------------------> Reports <--------------------
    Route::controller(ReportController::class)->prefix('report')->name('report.')->group(function () {
        Route::get('/stock', 'stock')->name('stock');
        Route::get('used/stock', 'usedStock')->name('used-stock');
        Route::get('/daily', 'daily')->name('daily');
        Route::get('/sale', 'sale')->name('sale');
        Route::get('/item-sale', 'itemSale')->name('item-sale');
        Route::get('/service', 'service')->name('service');
        Route::get('/product-sale', 'productSale')->name('product.sale');
        Route::get('/purchase', 'purchase')->name('purchase');
        Route::get('/supplier/ledger', 'supplierLedger')->name('supplier-ledger');
        Route::get('/customer/ledger', 'customerLedger')->name('customer-ledger');
        Route::get('/ownership/ledger', 'ownerShipLedger')->name('ownership-ledger');
        Route::get('/account/ledger', 'accountLedger')->name('account-ledger');
        Route::get('/profit-loss', 'profitLoss')->name('profit-loss');
        Route::get('/supplier/due', 'supplierDue')->name('supplier-due');
        Route::get('/customer/due', 'customerDue')->name('customer-due');
        Route::get('/customer/full-report', 'customerFullReport')->name('customer.full-report');
        Route::get('/low-stock', 'low_stock')->name('low.stock');
        Route::get('/user/sell', 'user_sell')->name('user.sell');
        Route::get('/daily/stock', 'dailyStock')->name('daily.stock');
        Route::get('/inventory/register', 'inventoryRegister')->name('inventory.register');
        Route::get('/vat', 'vat')->name('vat');
        Route::get('/discount', 'discount')->name('discount');
        Route::get('/top-selling', 'topSelling')->name('top-selling');
    });
    //------------------------- pdf ------------------
    Route::controller(PDFController::class)->prefix('report')->name('report.')->group(function () {
        Route::get('/stock/pdf', 'stock_pdf')->name('stock.pdf');
        Route::get('/daily/pdf', 'daily_pdf')->name('daily.pdf');
        Route::get('/user/sell/pdf', 'pdf')->name('user.sell.pdf');
    });

    // ----------------------- AI Stock Auditor ---------------------
    Route::controller(AIStockAuditorController::class)->prefix('ai-auditor')->name('ai-auditor.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/audit/{id}', 'audit')->name('audit');
        Route::get('/fix/{id}', 'fix')->name('fix');
    });

    // ----------------------- AI Chatbot ---------------------
    Route::controller(AIChatbotController::class)->prefix('ai-chatbot')->name('ai-chatbot.')->group(function () {
        Route::post('/chat', 'chat')->name('chat');
        Route::post('/clear', 'clearHistory')->name('clear');
    });

    // ----------------------- BotMan Chatbot ---------------------
    Route::match(['get', 'post'], '/botman', [\App\Http\Controllers\Backend\BotManController::class, 'handle'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::get('/botman/chat', function () {
        return view('backend.layouts.includes.chatbot-frame');
    });

    // ----------------------- Activity Log ---------------------
    Route::group(['prefix' => 'activity-log', 'as' => 'activity-log.'], function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::get('/{id}/details', [ActivityLogController::class, 'getDetails'])->name('details');
        Route::delete('/clear', [ActivityLogController::class, 'destroyAll'])->name('clear');
    });

    // --------------------> users <--------------------
    Route::resource('user', UserController::class);

    // Managers Route ARE HERE...
    Route::controller(BusinessSettingsController::class)->prefix('setting')->name('setting.')->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::post('/update', 'update')->name('update');
    });

    // Super Admin Settings
    Route::controller(SuperAdminSettingsController::class)->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/settings', 'index')->name('settings');
        Route::get('/settings/env', fn() => redirect()->route('super-admin.settings'))->name('settings.env.get');
        Route::post('/settings/env', 'updateEnv')->name('settings.env');
        Route::post('/settings/sidebar', 'updateSidebar')->name('settings.sidebar');
    });

    // --------------------> roles & permission <--------------------
    Route::controller(RolesPermissionController::class)->group(function () {
        Route::get('roles-permission', 'index')->name('roles-permission.index');
        Route::get('roles-permission/create', 'create')->name('roles-permission.create');
        Route::post('roles-permission', 'store')->name('roles-permission.store');
        Route::get('roles-permission/{id}/edit', 'edit')->name('roles-permission.edit');
        Route::put('roles-permission/{id}', 'update')->name('roles-permission.update');
    });

    // --------------------> others <--------------------
    Route::controller(OthersController::class)->group(function () {
        Route::get('/status-update', 'statusUpdate')->name('status.update');
        Route::get('/get/product-by-supplier/{supplier_id}', 'getProductBySupplier')->name('get.productBySupplier');
        Route::get('/get/product-search', 'productSearch')->name('product-search');
        Route::get('/get/sc-product-search', 'scProductSearch')->name('sc-product-search');
        Route::get('/get/used/product-search', 'usedProductSearch')->name('used-product-search');
        Route::get('/get/search-product-id/{my_id}', 'productSearchDetails')->name('search-product-id');
        Route::get('/get/sc-search-product-id/{my_id}', 'scproductSearchDetails')->name('sc-search-product-id');
        Route::get('/get/used/search-product-id/{my_id}', 'usedProductSearchDetails')->name('used-search-product-id');
        Route::get('/get/trans/search-product-id/{product_id}/{branch_id}', 'transProductSearchDetails')->name('trans-search-product-id');
        Route::get('/get/product-barcode/{code}', 'productBarcode')->name('product-barcode');
        Route::get('/get/product/unit/{my_id}', 'productUnit')->name('product-unit');
        Route::get('/get/pos-products', 'posProducts')->name('posProducts');
        Route::get('/get/pos/product-id/{my_id}', 'productPosDetails')->name('pos-product-id');
        Route::get('/get/pos/sc-product-id/{my_id}', 'productScPosDetails')->name('sc-pos-product-id');
        Route::get('/get/serial-search', 'serialSearch')->name('serial-search');
        Route::get('/get/search-serial-id/{my_id}', 'serialSearchDetails')->name('search-serial-id');
        Route::post('/get/check-imei-duplicates', 'checkImeiDuplicates')->name('check-imei-duplicates');
        Route::get('/get/invoice-item-candidate-imeis', 'getInvoiceItemCandidateImeis')->name('get-invoice-item-candidate-imeis');
        //transfer
        Route::get('/get/to/account', 'getToAccount')->name('get-to-account');
        Route::get('get/account/balance', 'getAccountBalance')->name('get-account-balance');

        Route::get('/customer/previous/due', 'findCustomerDue')->name('customer.previous.due.find');
        Route::get('/customer/vehicles', 'getCustomerVehicles')->name('customer.vehicles');
        // payment
        Route::get('customer/account/balance/{my_id}', 'getCustomerAccountBalance')->name('customer-account-balance');
        Route::get('supplier/account/balance/{my_id}', 'getSupplierAccountBalance')->name('supplier-account-balance');
        // download.backup
        Route::get('/download/backup', 'downloadBackup')->name('status.download.backup');
    });
    Route::controller(UserProfileController::class)->group(function () {
        Route::get('/user/profile', 'index')->name('profile');
        Route::post('/user/profile/update', 'update')->name('profile.update');
        Route::post('/user/profile/change/password', 'changePassword')->name('profile.password');
    });

    Route::resource('pathao', PathaoController::class);
    Route::post('/pathao/order', [PathaoController::class, 'placeOrder'])->name('pathao.order');
    Route::get('/pathao/order/status/{id}', [PathaoController::class, 'checkOrderStatus']);
    Route::get('/fetch-zones', [PathaoController::class, 'getZonesByCity'])->name('pathao.zones');
    Route::get('/fetch-areas', [PathaoController::class, 'getAreasByCity'])->name('pathao.areas');


    // ----------------------- return ---------------------
    Route::controller(ReturnSaleController::class)->group(function () {
        Route::get('return/sale', 'index')->name('return.sale');
        Route::get('return/sale/{id}', 'create')->name('return.create');
        Route::post('return/sale/submit', 'insert')->name('return.insert');
        Route::get('return/sale/delete/{id}', 'delete')->name('return.delete');
    });

    Route::controller(ReturnPurchaseController::class)->group(function () {
        Route::get('return/purchase/delete/{id}', 'destroy')->name('return.purchase.delete');
    });

    Route::resource('rtnPurchase', ReturnPurchaseController::class);
    // ----------------------- damage ---------------------
    Route::controller(DamageController::class)->group(function () {
        Route::get('damage', 'index')->name('damage.index');
        Route::get('damage/create', 'create')->name('damage.create');
        Route::post('damage/create/submit', 'insert')->name('damage.insert');
        Route::post('damage/delete/{id}', 'softDelete')->name('damage.delete');
    });
});
Route::get('/customer/invoice/copy/{id}', [InvoiceController::class,'customerPrintInvoice'])->name('printNewInvoice');

Route::get('lang/{lang}', [App\Http\Controllers\LanguageController::class, 'switchLang']);
