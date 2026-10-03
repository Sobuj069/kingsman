<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->select('id', 'name', 'phone', 'email', 'address', 'total_point');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'invoice_id');
    }

    public function bankTransactions()
    {
        return $this->hasMany(BankTransaction::class, 'invoice_id')->orderBy('date', 'asc');
    }

    //created by
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by')->select('id', 'name');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->select('id', 'name');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function installment()
    {
        return $this->hasOne(Installment::class);
    }

    public function editLogs()
    {
        return $this->hasMany(InvoiceEditLog::class, 'invoice_id')->orderBy('created_at', 'desc');
    }




    public static function getFakeSum($query, $column = 'total_amount')
    {
        // যদি কোয়েরি বিল্ডার হয়, তাহলে তা এক্সিকিউট করবেন, কিন্তু ফিল্টারিং পরে করবেন
        if ($query instanceof \Illuminate\Database\Eloquent\Builder) {
            $invoices = $query->get();
        } elseif ($query instanceof \Illuminate\Support\Collection) {
            $invoices = $query;
        } else {
            return 0;
        }

        // শুধু আসল (নন-ফেক) ইনভয়েস রাখুন
        $realInvoices = self::filterRealInvoices($invoices);

        return $realInvoices->sum($column);
    }

    /**
     * একটি কালেকশন থেকে শুধু আসল ইনভয়েস রিটার্ন করে (ফেক বাদ দিয়ে)
     */
    public static function filterRealInvoices($invoices)
    {
        if ($invoices instanceof \Illuminate\Database\Eloquent\Builder) {
            $invoices = $invoices->get();
        }

        $user = auth()->user();
        if (!$user || $user->fake_sale_percentage <= 0) {
            // ফেক সেল নেই, সব ইনভয়েস আসল
            return $invoices;
        }

        // ব্রাঞ্চ অনুযায়ী গ্রুপ করুন
        $grouped = $invoices->groupBy('branch_id');
        $real = collect();

        foreach ($grouped as $branchId => $branchInvoices) {
            // ব্রাঞ্চের ইনভয়েসগুলো সর্বশেষ ক্রমে সাজান (তারিখ অনুযায়ী)
            $sorted = $branchInvoices->sortByDesc('created_at');
            $total = $sorted->count();
            $fakeCount = ceil(($total * $user->fake_sale_percentage) / 100);

            // সর্বশেষ $fakeCount টি হলো ফেক – সেগুলো বাদ দিন, বাকিগুলো রাখুন
            $realInvoicesForBranch = $sorted->slice($fakeCount); // প্রথম fakeCount টি বাদ

            $real = $real->merge($realInvoicesForBranch);
        }

        return $real;
    }

    public static function filterByFakeSale($invoices)
    {
        if ($invoices instanceof \Illuminate\Database\Eloquent\Builder) {
            $invoices = $invoices->orderBy('created_at', 'desc')->get();
        } else if ($invoices instanceof \Illuminate\Support\Collection) {
            $invoices = $invoices->sortByDesc('created_at');
        }

        $user = auth()->user();
        if (!$user) {
            return $invoices;
        }

        $grouped = $invoices->groupBy('branch_id');
        $filtered = collect();

        foreach ($grouped as $branchId => $branchInvoices) {
            if ($user->fake_sale_percentage > 0) {
                $count = $branchInvoices->count();
                $limit = ceil(($count * $user->fake_sale_percentage) / 100);
                // skip() দিয়ে সর্বশেষ $limit টি ফেক ইনভয়েস বাদ দিন
                $filtered = $filtered->merge($branchInvoices->skip($limit));
            } else {
                $filtered = $filtered->merge($branchInvoices);
            }
        }
        return $filtered;
    }
}
