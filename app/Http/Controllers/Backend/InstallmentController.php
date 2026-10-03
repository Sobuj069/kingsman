<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Installment;
use App\Models\InstallmentSchedule;
use App\Models\Invoice;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstallmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (env('APP_INSTALLMENT') !== 'yes' && auth()->user()->branch_id != 1) {
                abort(404, 'Installment system is disabled.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = Installment::with(['customer', 'invoice', 'schedules']);

        // Search/Filter
        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $installments = $query->latest()->paginate(20);
        $customers = \App\Models\Customer::select('id', 'name', 'phone')->get();

        return view('backend.pages.installment.index', compact('installments', 'customers'));
    }

    public function show($id)
    {
        $installment = Installment::with(['customer', 'invoice', 'schedules'])->findOrFail($id);
        $bankAccounts = BankAccount::where('status', 1)->get();

        return view('backend.pages.installment.show', compact('installment', 'bankAccounts'));
    }

    public function todayDue()
    {
        $today = Carbon::today()->toDateString();
        $schedules = InstallmentSchedule::with(['installment.customer', 'installment.invoice'])
            ->where('due_date', $today)
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);
        
        $bankAccounts = BankAccount::where('status', 1)->get();

        return view('backend.pages.installment.today-due', compact('schedules', 'bankAccounts'));
    }

    public function todayCollection()
    {
        $today = Carbon::today()->toDateString();
        $schedules = InstallmentSchedule::with(['installment.customer', 'installment.invoice'])
            ->where('paid_date', $today)
            ->where('status', 'paid')
            ->latest()
            ->paginate(20);

        return view('backend.pages.installment.today-collection', compact('schedules'));
    }

    public function overdueList()
    {
        $today = Carbon::today()->toDateString();
        $schedules = InstallmentSchedule::with(['installment.customer', 'installment.invoice'])
            ->where('due_date', '<', $today)
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        $bankAccounts = BankAccount::where('status', 1)->get();

        return view('backend.pages.installment.overdue', compact('schedules', 'bankAccounts'));
    }

    public function completed()
    {
        $installments = Installment::with(['customer', 'invoice'])
            ->where('status', 'completed')
            ->latest()
            ->paginate(20);

        return view('backend.pages.installment.completed', compact('installments'));
    }

    public function collectPayment(Request $request, $scheduleId)
    {
        $request->validate([
            'bank_id' => 'required|exists:bank_accounts,id',
            'paid_amount' => 'required|numeric|min:0.01',
            'paid_date' => 'required|date',
        ]);

        $schedule = InstallmentSchedule::with('installment.invoice')->findOrFail($scheduleId);

        if ($schedule->status === 'paid') {
            return redirect()->back()->with('error', 'This installment is already paid!');
        }

        DB::transaction(function () use ($request, $schedule) {
            $installment = $schedule->installment;
            $invoice = $installment->invoice;

            // 1. Update Schedule Item
            $schedule->paid_amount = $request->paid_amount;
            $schedule->paid_date = $request->paid_date;
            $schedule->status = 'paid';
            $schedule->save();

            // Rolling adjustment for future scheduled payments (difference is positive if underpaid, negative if overpaid)
            $difference = $schedule->amount - $request->paid_amount;
            $adjustment = $difference;

            if ($adjustment != 0) {
                $futureSchedules = InstallmentSchedule::where('installment_id', $installment->id)
                    ->where('installment_no', '>', $schedule->installment_no)
                    ->where('status', 'pending')
                    ->orderBy('installment_no', 'asc')
                    ->get();

                foreach ($futureSchedules as $futureSchedule) {
                    if ($adjustment > 0) {
                        // Underpaid: Add all remaining adjustment to the immediate next schedule, and we are done.
                        $futureSchedule->amount += $adjustment;
                        $futureSchedule->save();
                        $adjustment = 0;
                        break;
                    } else {
                        // Overpaid: Subtract from future schedules
                        $originalAmount = $futureSchedule->amount;
                        $newAmount = $originalAmount + $adjustment; // adjustment is negative
                        
                        if ($newAmount >= 0) {
                            $futureSchedule->amount = $newAmount;
                            if ($newAmount == 0) {
                                $futureSchedule->status = 'paid';
                                $futureSchedule->paid_date = $request->paid_date;
                                $futureSchedule->paid_amount = 0.00;
                            }
                            $futureSchedule->save();
                            $adjustment = 0;
                            break;
                        } else {
                            // Remaining adjustment keeps rolling
                            $futureSchedule->amount = 0;
                            $futureSchedule->status = 'paid';
                            $futureSchedule->paid_date = $request->paid_date;
                            $futureSchedule->paid_amount = 0.00;
                            $futureSchedule->save();
                            $adjustment = $newAmount; // still negative
                        }
                    }
                }
            }

            // 2. Update Invoice totals
            $invoice->total_paid = $invoice->total_paid + $request->paid_amount;
            $invoice->total_due = max(0, $invoice->total_due - $request->paid_amount);
            if ($invoice->total_due <= 0) {
                $invoice->status = 1; // Paid status
            }
            $invoice->save();

            // 3. Create Bank Transaction
            $bank_transaction = new BankTransaction();
            $bank_transaction->trans_type = 'deposit';
            $bank_transaction->pay_type = 'invpay';
            $bank_transaction->branch_id = $invoice->branch_id;
            $bank_transaction->date = $request->paid_date;
            $bank_transaction->bank_id = $request->bank_id;
            $bank_transaction->invoice_id = $invoice->id;
            $bank_transaction->amount = $request->paid_amount;
            $bank_transaction->created_by = auth()->user()->id;
            $bank_transaction->save();

            // 4. Create Ledger/Customer Transaction
            $transaction = new Transaction();
            $transaction->transaction_type = 'Received from Customer';
            $transaction->branch_id = $invoice->branch_id;
            $transaction->date = $request->paid_date;
            $transaction->bank_id = $request->bank_id;
            $transaction->invoice_id = $invoice->id;
            $transaction->customer_id = $invoice->customer_id;
            $transaction->debit = NULL;
            $transaction->credit = $request->paid_amount;
            $transaction->created_by = auth()->user()->id;
            $transaction->save();

            // 5. Update Parent Installment Status
            // Check if all schedules are paid
            $pendingSchedulesCount = InstallmentSchedule::where('installment_id', $installment->id)
                ->where('status', 'pending')
                ->count();

            if ($pendingSchedulesCount === 0) {
                $installment->status = 'completed';
                $installment->save();
            }
        });

        return redirect()->back()->with('success', 'Installment payment collected successfully!');
    }
}
