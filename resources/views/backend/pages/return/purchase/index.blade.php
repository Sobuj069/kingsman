@extends('backend.layouts.master')
@section('section-title', __('Return'))
@section('page-title', __('Return Purchase List'))
{{-- @if (check_permission('product.create'))
    @section('action-button')
        <a href="#" data-toggle="modal" data-target="#addModal" class="btn btn-primary-rgba">
            <i class="mr-2 feather icon-plus"></i>
            Search Return
        </a>
    @endsection
@endif --}}
@push('css')
<style>
    @media print {
        @page {
        size: auto;
        }

        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: Roboto,sans-serif;
        }

        .print_area {
            position: absolute;
            top: 0;
            width: 100%;
        }

        .print_area * {
            visibility: visible !important;
        }
    }
</style>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-body card_style mb-2" style="margin-top: -3px" id="h-hide">
                <form action="{{ url('rtnPurchase')}}">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <input type="text" name="barcode" class="form-control barcode-filter-input" data-barcode-input
                                placeholder="{{ __('Scan Barcode / Purchase / Product') }}" value="{{ $barcode ?? '' }}">
                        </div>
                        <div class="form-group col-md-3">
                            <select class="select2" name="supplier_id">
                                <option selected value="">{{ __('Select Supplier') }}</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ ($supplier->id == ($supplier_id ?? '')) ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="date" name="startDate" class="form-control" value="{{ $startDate ?? '' }}">
                        </div>
                        <div class="form-group col-md-3">
                            <input type="date" name="endDate" class="form-control" value="{{ $endDate ?? '' }}">
                        </div>
                    </div>
                    <div class="form-row mt-2">
                        <div class="form-group col-12">
                            <button class="btn add_list_btn" type="submit">
                                    <i class="fa fa-sliders"></i> {{ __('Filter') }}
                            </button>
                            <a href="{{ url('rtnPurchase') }}" class="btn add_list_btn_reset">{{ __('Reset') }}</a>
                            <a href="" class="btn add_list_btn float-right" onclick="window.print()">{{ __('Print') }}</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-striped">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Purchase No') }}</th>
                                    <th>{{ __('Branch') }}</th>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Qty') }}</th>
                                    <th>{{ __('Return By') }}</th>
                                    <th>{{ __('Supplier') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th class="header_style_right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_amt = 0;
                                @endphp
                                @forelse ($returns as $key=> $item)
                                    @php
                                        $returnItems= App\Models\ReturnPurchaseItem::where('rtnPurchase_id',$item->id)->get();
                                        // $product = App\Models\Product::where('id',$item->product_id)->first();
                                        $total_amt += $item->total_return;
                                    @endphp
                                    <tr>
                                        <td class="table_data_style_left">{{ $key+1 }}</td>
                                        <td>{{ date('Y-m-d',strtotime($item->date))  }}</td>
                                        <td>{{ $item->purchase?->purchase_no ?? 'Deleted' }}</td>
                                        <td>{{ $item->branch?->name }}</td>
                                        <td>
                                            @foreach ($returnItems as $rtn)
                                                <ul>
                                                    <li>{{ $rtn->product?->name }} @if($rtn->product_variation_id != null) ({{ $rtn->product_variation?->size?->size}} - {{ $rtn->product_variation?->color?->color}}) @endif</li>
                                                </ul>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($returnItems as $rtn)
                                                <ul>
                                                    <li>
                                                        @if ($rtn->product?->unit?->related_unit == null)
                                                            {{ $rtn->main_qty }} {{ $rtn->product?->unit?->name }}
                                                        @else
                                                            {{ $rtn->main_qty }} {{ $rtn->product?->unit?->name }} - 
                                                            {{ $rtn->sub_qty }} {{ $rtn->product?->unit?->related_unit?->name }}
                                                        @endif
                                                        @if (!empty($rtn->imei))
                                                            <br><small class="text-muted">{{ str_replace("\n", ", ", $rtn->imei) }}</small>
                                                        @endif
                                                    </li>
                                                </ul>
                                            @endforeach
                                        </td>
                                        <td>{{ $item->user?->name }}</td>
                                        <td>{{ $item->supplier?->name }}</td>
                                        <td>{{ $item->total_return }}</td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    @if (check_permission('purchase.destroy'))
                                                        <a class="dropdown-item text-danger" href="#" data-toggle="modal"
                                                            data-target="#deleteModal-{{$item->id}}">
                                                            <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- delete modal --}}
                                            <form action="{{ route('return.purchase.delete', $item->id) }}" method="GET">
                                                @csrf
                                                <x-delete-modal title="{{ __('Return') }}" id="{{ $item->id }}" />
                                                    
                                                    <input type="hidden" name="return_id" id="" value="{{$item->id}}">
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center text-danger no_data_style">{{ __('No Data Available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="text-dark text-right footer_style">
                                    <td colspan="7" class="table_data_style_left text-white"><strong>{{ __('Total') }}: </strong></td>
                                    <td colspan="2" class="text-white"> <strong>{{ number_format($total_amt,2) }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</strong></td>
                                    <td colspan="1" class="table_data_style_right"></td>
                                </tr>
                            </tfoot>
                        </table>
                        {{-- {{ $returns->onEachSide(1)->links() }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bar_code_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body" id="barcode-page">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success delete" onclick="print_barcode()"><i class="fa fa-print"></i>
                        {{ __('Print') }}</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Add Modal --}}
    {{-- <form action="{{ route('return.search') }}" method="get">
        <x-add-modal title="Search Invoice" sizeClass="modal-lg">
            <x-input label="Invoice" type="text" name="invoice" placeholder="Enter Invoice No" required md="12" />
        </x-add-modal>
    </form> --}}
@endsection

@push('js')
    <script>
        $(document).on('click', '.generated_barcode', function() {
            let name = $(this).attr('data-name');
            let code = $(this).attr('data-code');
            let price = $(this).attr('data-price');

            let pro_barcode = "{{ get_setting('pro_barcode') }}";

            let url = "{{ route('product-barcode', 'value') }}".replace('value', code);

            $.get(url, (data) => {
                let company = "{{ config('app.name') }}";
                
                let barcode = '';
                if (pro_barcode == 'a4') {
                    barcode += `<div class="text-center p-4 print_area" id="barcode">
                        <table class="table table-bordered">`;
                            for ($i = 0; $i < 10; $i++) {
                                barcode += `<tr>`;

                                for ($j = 0; $j < 3; $j++) {
                                    barcode +=
                                    `<td>
                                        <p style="margin-bottom:2px; line-height:9px; margin-top:10px; font-size: 9px; color:black;"><strong>${company}</strong></p>
                                        ${data}
                                        <p style="margin-bottom:2px; line-height:9px; margin-top:3px; font-size: 9px; color:black;"><strong>${name}</strong></p>
                                        <p style="margin-bottom:2px; line-height:9px; margin-top:3px; font-size: 9px; color:black;"><strong>${price} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</strong></p>
                                    </td>`;
                                }

                                barcode += `</tr>`;
                            }
                        barcode += `
                        </table>
                    </div>`;
                } else {
                    barcode += `<div class="text-center p-4 print_area" id="barcode" style="padding:0 !important">
                        <table class="table" style="margin-bottom:0 !important;">
                            <tr>
                                <td style="border-top:0 !important;padding:0;">
                                <p style="margin-bottom:2px; line-height:9px; margin-top:10px; font-size: 9px; color:black;"><strong>${company}</strong></p>

                                ${data}

                                <p style="margin-bottom:2px; line-height:9px; margin-top:2px; font-size: 9px; color:black;">${code}</p>

                                <p style="margin-bottom:2px; line-height:9px; margin-top:-2px; font-size: 9px; color:black;"><strong>${name}</strong></p>

                                <p style="margin-bottom:2px; line-height:9px; margin-top:0px; font-size: 12px; color:black;"><strong>${price} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</strong></p>
                                </td>
                            </tr>
                        </table>
                    </div>`;
                }

                $("#barcode-page").html(barcode);
            });

            $("#bar_code_modal").modal('show');
        });

    </script>

    <script>
        //  Print Barcode
        function print_barcode(id) {
            $("#bar_code_modal").modal('hide');
            $(".modal-backdrop").remove();
            $(".modal").css('display', 'none');

            let mainDocBody = $('.main-container').html();
            let printDoc = $("#barcode-page").html();
            $(".main-container").html(printDoc);
            $("body").attr('style', '');
            window.print();
            $(".main-container").html(mainDocBody);
        }
    </script>
@endpush
