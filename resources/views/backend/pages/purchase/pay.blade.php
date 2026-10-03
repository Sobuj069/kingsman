@extends('backend.layouts.master')
@section('section-title', 'Purchase')
@section('page-title', 'Pay')
@if (check_permission('purchase.index'))
    @section('action-button')
        <a href="{{ route('purchase.index') }}" class="btn btn-primary-rgba">
            <i class="mr-2 feather icon-list"></i>
            Purchase List
        </a>
    @endsection
@endif
@section('content')
    <div class="card">
        <div class="card-body">
            <form class="needs-validation" method="POST" action="{{ route('storePurchaseLog') }}"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="Due Paid">
                <div class="form-row">
                    {{-- Payment Method --}}
                    <div class="col-md-5">
                        <label class="form-label font-weight-bold">Bank Account</label>
                        <select class="select2" name="bank_id" required>
                            @foreach ($bank_accounts as $bank_account)
                                <option value="{{ $bank_account->id }}">
                                    {{ $bank_account->bank_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-group text-center mt-3">
                            <h5>Payment Method </h5>
                            <strong class="change_name">Cash</strong>
                            <div class="form-group mt-3">
                                <button type="button" class="btn btn-success btn-md full_pay" style="width: 100%">
                                    <i class="feather icon-dollar-sign"></i>
                                    Full Payment</button>
                            </div>
                            <div class="input-group mt-3"> <span class="input-group-text">Amount</span>
                                <input type="number" step="any" min="1" value="" name="paid_amount" class="form-control pay_amount" required>
                            </div>
                        </div>
                        <div class="form-group mt-3 text-left">
                            <label for="" class="form-label fw-bold"> Note</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table table-border">
                            <table class="table">
                                <tr>
                                    <td class="fw-bold">Purchase Date</td>
                                    <td class="float-right">{{ $purchase->date }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Purchase No</td>
                                    <td class="float-right">{{ $purchase->purchase_no }}</td>
                                    <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">
                                </tr>
                                <tr>
                                    <td class="fw-bold">Supplier Name</td>
                                    <input type="hidden" name="supplier_id" value="{{ $purchase->supplier_id }}">
                                    <td class="float-right">{{ $purchase->supplier->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created By</td>
                                    <td class="float-right">{{ $purchase->user->name }}</td>
                                </tr>
                            </table>

                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchase->purchaseItems as $item)
                                        @php
                                            $product = App\Models\Product::where('id', $item->product_id)->with('unit.related_unit')->first();
                                            if ($product->unit->related_unit  == null) {
                                                $qty = $item->main_qty.' '.$product->unit->name;
                                            } else {
                                                $sub_qty = ($item->sub_qty==Null)?0:$item->sub_qty;
                                                $qty = $item->main_qty.' '.$product->unit->name.' '.$sub_qty.' '.$product->unit->related_unit->name;
                                            }
                                        @endphp
                                        <tr class="text-center">
                                            <td>{{ $item->product->name }}</td>
                                            <td><small>{{ $qty }}</small></td>
                                            <td>{{ $item->rate }}</td>
                                            <td>{{ $item->subtotal }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="fw-bold text-right"> Sub Total </td>
                                        <td class="text-center">{{ $purchase->estimated_amount }}</td>
                                    </tr>
                                    @if ($purchase->discount > 0)
                                        <tr>
                                            <td colspan="3" class="fw-bold text-right"> Discount </td>
                                            <td class="text-center">{{ $purchase->discount }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="3" class="fw-bold text-right"> Payable Amount </td>
                                        <td class="text-center total_amount">{{ $purchase->total_amount }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="fw-bold text-right"> Total Paid </td>
                                        <td class="text-center total_paid">{{ $purchase->total_paid }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="fw-bold text-right"> Total Due </td>
                                        <td class="text-center total_due">{{ $purchase->total_due }}</td>
                                        <input type="hidden" name="due_amount" value="0">
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="form-row text-right">
                    <div class="col-md-12 ">
                        <a href="{{ url('purchase') }}" class="btn btn-danger btn-md">
                            <i class="bx bx-x"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-success btn-md">
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
        $(document).ready(function() {
            //set max value for amount
            $('.pay_amount').attr('max', $('.total_due').text());
        });
        // payment_method_id is not 1 then show payment reference input field
        $("select[name='bank_id']").change(function() {
            //get selected payment method text
            var payment_method_name = $(this).find(':selected').text();
            $(".change_name").text(payment_method_name);
        });

        //full paid button click
        $(".full_pay").click(function() {
            //get total due
            let total_due = $('.total_due').text();
            if (total_due > 0) {
                //set total due to pay amount
                $('.pay_amount').val(total_due);

                //set total due 0
                $('.total_due').text(0);
                $("input[name='due_amount']").val(0);

                //set total_paid total_paid + total_due
                let total_paid = $('.total_paid').text();
                let new_total_paid = parseFloat(total_paid) + parseFloat(total_due);
                $('.total_paid').text(new_total_paid);
            }
        });

        //amount input field keyup and change
        $('.pay_amount').on("keyup change", function() {

            //get pay amount
            let pay_amount = $(this).val();

            //get total due
            let total_due = {{ $purchase->total_due }};

            //get total paid
            let total_paid = {{ $purchase->total_paid }};



            //subtract pay amount from total due
            let new_total_due = parseFloat(total_due) - parseFloat(pay_amount);

            //add pay amount to total paid
            let new_total_paid = parseFloat(total_paid) + parseFloat(pay_amount);

            //set new total due
            $('.total_due').text(new_total_due);
            $("input[name='due_amount']").val(new_total_due);

            //set new total paid
            $('.total_paid').text(new_total_paid);

            if (pay_amount == '') {
                $('.total_due').text(total_due);
                $('.total_paid').text(total_paid);
            }
            //check pay amount is greater than total due
            if (pay_amount > total_due) {
                //set pay amount to total due
                $(this).val(total_due);
                //set total due 0
                $('.total_due').text(0);
                //set total paid total paid + total due
                $('.total_paid').text($('.total_amount').text());

            }
        });
    </script>
@endpush
