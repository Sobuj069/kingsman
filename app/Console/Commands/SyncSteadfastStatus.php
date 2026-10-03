<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\SerialNumber;
use App\Models\Transaction;
use App\Models\BankTransaction;
use App\Models\ActualPayment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncSteadfastStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steadfast:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Steadfast API statuses and auto-cancel/return pending orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting Steadfast Sync...");

        // Fetch pending online invoices that have a consignment_id
        $invoices = Invoice::where('sale_type', 'Online')
            ->whereNotNull('consignment_id')
            ->where('consignment_id', '!=', '')
            ->whereNotIn('order_status', ['delivered', 'partial_delivered', 'cancelled', 'returned'])
            ->get();

        if ($invoices->isEmpty()) {
            $this->info("No pending Steadfast orders found.");
            return;
        }

        foreach ($invoices as $invoice) {
            $response = status($invoice->consignment_id);
            $newStatus = $response['delivery_status'] ?? $response['status'] ?? null;
            
            // If the API returns 'status' as an HTTP code (like 200), ignore it and use delivery_status.
            if (is_numeric($newStatus) && isset($response['delivery_status'])) {
                $newStatus = $response['delivery_status'];
            }
            
            if (!$newStatus) {
                $this->error("Failed to fetch status for Consignment ID: {$invoice->consignment_id}");
                continue;
            }

            $newStatus = strtolower($newStatus);

            if (in_array($newStatus, ['cancelled', 'returned'])) {
                // Ensure we don't restore stock twice
                if ($invoice->status == 2) {
                    $this->info("Invoice #{$invoice->invoice_no} is already processed as Returned/Cancelled.");
                    continue;
                }

                DB::transaction(function () use ($invoice, $newStatus) {
                    $invoiceItems = InvoiceItem::where('invoice_id', $invoice->id)->get();

                    foreach ($invoiceItems as $item) {
                        $product = Product::with('unit')->find($item->product_id);

                        if ($product && $product->is_service == 0) {
                            if ($product->unit->related_unit == null) {
                                $restoreQty = (float) $item->actual_main;
                            } else {
                                $main = (float) $item->actual_main * (float) $product->unit->related_value;
                                $sub  = (float) ($item->actual_sub ?? 0);
                                $restoreQty = $main + $sub;
                            }

                            // Restore stock using FIFO
                            if ($restoreQty > 0) {
                                restoreToFIFO($item->product_id, $restoreQty, $item->product_variation_id, $item->branch_id);
                            }

                            // Restore IMEI status back to available
                            if ($item->imei) {
                                $imeis = $this->parseImeis($item->imei);
                                SerialNumber::where('product_id', $item->product_id)
                                            ->whereIn('serial', array_map('trim', $imeis))
                                            ->update(['status' => 1]);
                            }
                        }
                    }

                    // Transactions rollback
                    $transactions = Transaction::where('invoice_id', $invoice->id)->get();
                    $bankTransactions = BankTransaction::where('invoice_id', $invoice->id)->get();
                    
                    foreach ($transactions as $transaction) {
                        if ($transaction->actual_pay_id != NULL) {
                            $actualpay = ActualPayment::where('id', $transaction->actual_pay_id)->first();
                            if ($actualpay) {
                                $actualpay->amount -= $transaction->debit;
                                $actualpay->amount <= 0 ? $actualpay->delete() : $actualpay->save();
                            }
                        }
                        $transaction->delete();
                    }
                    
                    foreach ($bankTransactions as $bank) {
                        $bank->delete();
                    }

                    // Update the Invoice
                    $invoice->update([
                        'status' => 2,
                        'order_status' => $newStatus
                    ]);
                });
                
                $this->info("Invoice #{$invoice->invoice_no} sync completed ($newStatus) & stock restored.");
            } else {
                // Update to the latest status if not cancelled/returned
                if ($invoice->order_status !== $newStatus) {
                    $invoice->update(['order_status' => $newStatus]);
                    $this->info("Invoice #{$invoice->invoice_no} updated to $newStatus.");
                } else {
                    $this->info("Invoice #{$invoice->invoice_no} unchanged ($newStatus).");
                }
            }
        }

        $this->info("Steadfast Sync Completed.");
    }

    private function parseImeis(?string $value): array
    {
        $value = trim((string) $value);
        if ($value === '') {
            return [];
        }

        $value = str_replace("\r", "", $value);
        $parts = preg_split('/[,\n]+/', $value) ?: [];

        return array_values(array_filter(array_map(static fn ($v) => trim((string) $v), $parts), static fn ($v) => $v !== ''));
    }
}
