@extends('backend.layouts.master')
@section('section-title', 'Invoice')
@section('page-title', 'Pay')
@if (check_permission('invoice.index'))
    @section('action-button')
        <a href="{{ route('invoice.index') }}" class="btn add_list_btn">
            <i class="mr-2 feather icon-list"></i>
            Invoice List
        </a>
    @endsection
@endif
@section('content')
    <div class="card card_style">
        <div class="card-body">
            <form id="paymentForm" class="needs-validation" method="POST" action="{{ route('storeInvoiceLog') }}"
                enctype="multipart/form-data"> 
                @csrf
                <input type="hidden" name="type" value="Due Paid">
                <div class="form-row">
                    {{-- Payment Method --}}
                    <div class="col-md-5">
                        <label class="form-label font-weight-bold text-black">Bank Account</label>
                        <select class="select2" name="bank_id" required>
                            @foreach ($bank_accounts as $bank_account)
                                <option value="{{ $bank_account->id }}">
                                    {{ $bank_account->bank_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-group text-center mt-3">
                            <h5>Payment Method 
                                <strong class="change_name" 
                                    style="margin-left: 150px;background:#12b76a;padding:6px 10px;border-radius:25px;color:white;font-size:14px">
                                    Cash
                                </strong>
                            </h5>
                            <div class="input-group mt-3"> 
                                <span class="input-group-text barcod_style">Amount</span>
                                <input type="number" step="any" min="1" value="" name="paid_amount" class="form-control pay_amount" required>
                            </div>
                        </div>
                        <div class="form-group mt-3 text-left">
                            <label for="" class="form-label fw-bold"> Note</label>
                            <textarea name="note" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="table table-border">
                            <table class="table">
                                <tr>
                                    <td class="fw-bold">Invoice Date</td>
                                    <td class="float-right">{{ $invoice->date }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Invoice No</td>
                                    <td class="float-right">{{ $invoice->invoice_no }}</td>
                                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                                </tr>
                                <tr>
                                    <td class="fw-bold">Customer Name</td>
                                    <input type="hidden" name="customer_id" value="{{ $invoice->customer_id }}">
                                    <td class="float-right">{{ $invoice->customer->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created By</td>
                                    <td class="float-right">{{ $invoice->user->name }}</td>
                                </tr>
                            </table>

                            <table class="table">
                                <thead>
                                    <tr class="text-center">
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoice->invoiceItems as $item)
                                        @php
                                            $product = App\Models\Product::where('id', $item->product_id)->with('unit.related_unit')->first();
                                            if ($product->unit?->related_unit  == null) {
                                                $qty = $item->main_qty.' '.$product->unit?->name;
                                            } else {
                                                $sub_qty = ($item->sub_qty==Null)?0:$item->sub_qty;
                                                $qty = $item->main_qty.' '.$product->unit?->name.' '.$sub_qty.' '.$product->unit?->related_unit->name;
                                            }
                                        @endphp
                                        <tr class="text-center">
                                            <td>{{ $item->product->name }}</td>
                                            <td><small>{{ $qty }}</small></td>
                                            <td>{{ $item->rate }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</td>
                                            <td>{{ $item->subtotal }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="fw-bold text-right"> Sub Total </td>
                                        <td class="text-center">{{ $invoice->estimated_amount }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</td>
                                    </tr>
                                    @if ($invoice->discount > 0)
                                        <tr>
                                            <td colspan="3" class="fw-bold text-right"> Discount </td>
                                            <td class="text-center">{{ $invoice->discount }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="3" class="fw-bold text-right"> Payable Amount </td>
                                        <td class="text-center total_amount">{{ $invoice->total_amount }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="fw-bold text-right"> Total Paid </td>
                                        <td class="text-center total_paid">{{ $invoice->total_paid }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="fw-bold text-right"> Total Due </td>
                                        <td class="text-center total_due">{{ $invoice->total_due }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</td>
                                        <input type="hidden" name="due_amount" value="0">
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="form-row text-right">
                    <div class="col-md-12">
                        <a href="{{ url('invoice') }}" class="btn cancel_btn btn-md">
                            <i class="bx bx-x"></i>
                            Cancel
                        </a>
                        <button type="button" id="payNowButton" class="btn save_btn btn-md">
                            <i class="feather icon-save"></i>
                            Pay Now
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
<script>
    document.getElementById("payNowButton").addEventListener("click", function (e) {
        e.preventDefault(); // Prevent the default button behavior
        const form = document.getElementById("paymentForm");
        form.submit(); // Explicitly submit the form
    });

    $(document).ready(function () {
        $('.pay_amount').attr('max', $('.total_due').text());

        $("select[name='bank_id']").change(function () {
            var payment_method_name = $(this).find(':selected').text();
            $(".change_name").text(payment_method_name);
        });

        $('.pay_amount').on("keyup change", function () {
            let pay_amount = $(this).val();
            let total_due = {{ $invoice->total_due }};
            let total_paid = {{ $invoice->total_paid }};

            let new_total_due = parseFloat(total_due) - parseFloat(pay_amount);
            let new_total_paid = parseFloat(total_paid) + parseFloat(pay_amount);

            $('.total_due').text(new_total_due);
            $("input[name='due_amount']").val(new_total_due);
            $('.total_paid').text(new_total_paid);

            if (pay_amount === '') {
                $('.total_due').text(total_due);
                $('.total_paid').text(total_paid);
            }

            if (pay_amount > total_due) {
                $(this).val(total_due);
                $('.total_due').text(0);
                $('.total_paid').text($('.total_amount').text());
            }
        });
    });
</script>
@endpush
