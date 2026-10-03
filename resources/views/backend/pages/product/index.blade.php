@extends('backend.layouts.master')
@section('section-title', __('Product'))
@section('page-title', __('Product List'))
@if (check_permission('product.create'))
    @section('action-button')
        <a href="{{ route('product.create') }}" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Product') }}
        </a>
    @endsection
@endif
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
                font-family: Roboto, sans-serif;
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
        #barcode-page svg, .print_area svg {
            max-width: 100% !important;
            height: 26px !important;
            display: block !important;
            margin: 0 auto !important;
            color-adjust: exact !important;
            -webkit-print-color-adjust: exact !important;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body pt-1">
                    <form action="{{ route('product.index') }}" method="GET">
                        @php
                            $categories = App\Models\Category::get();
                            $produc = App\Models\Product::get();
                        @endphp
                        <div class="row">

                            <div class="col-md-3 mt-1">
                                <input type="text" class="form-control barcode-filter-input" data-barcode-input name="barcode" placeholder="{{ __('Scan Barcode / Product') }}"
                                    value="{{ $barcode ?? '' }}" />
                            </div>

                            <div class="col-md-3 mt-1">
                                <select name="product_id" id="" class="select2">
                                    <option value="">{{ __('All Product') }}</option>
                                    @foreach ($produc as $item)
                                        <option value="{{ $item->id }}"{{ ($product_id ?? '') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mt-1">
                                <select name="category_id" id="" class="select2">
                                    <option value="">{{ __('All Category') }}</option>
                                    @foreach ($categories as $item)
                                        <option
                                            value="{{ $item->id }}"{{ ($category_id ?? '') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if (env('APP_SUB_CATEGORY') == 'yes')
                                <div class="col-md-2 mt-1">
                                    <select name="sub_category_id" id="" class="select2">
                                        <option value="">{{ __('All Sub Category') }}</option>
                                        @foreach ($subCategories ?? [] as $subItem)
                                            <option
                                                value="{{ $subItem->id }}"{{ ($sub_category_id ?? '') == $subItem->id ? 'selected' : '' }}>
                                                {{ $subItem->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-md-2 mt-1">
                                <select name="showcase_filter" class="form-control" style="height: 38px;">
                                    <option value="">{{ __('All Sections') }}</option>
                                    <option value="new_arrival" {{ ($showcase_filter ?? '') == 'new_arrival' ? 'selected' : '' }}>✨ {{ __('New Arrival') }}</option>
                                    <option value="top_selling" {{ ($showcase_filter ?? '') == 'top_selling' ? 'selected' : '' }}>🔥 {{ __('Top Selling') }}</option>
                                    <option value="featured" {{ ($showcase_filter ?? '') == 'featured' ? 'selected' : '' }}>⭐ {{ __('Featured') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2 mt-1">
                                <button type="submit" class="btn add_list_btn">{{ __('Filter') }}</button>
                                <a href="{{ route('product.index') }}" class="btn add_list_btn_reset">{{ __('Reset') }}</a>
                                <a href="" class="btn add_list_btn float-right" onclick="window.print()">{{ __('Print') }}</a>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive mt-3">
                        <table id="datatable-buttons" class="table table-striped">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Branch') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Barcode') }}</th>
                                    @if (env('APP_IMEI') == 'yes')
                                        <th>{{ __('IMEI') }}</th>
                                    @endif
                                    <th>{{ __('Category') }}</th>
                                    @if (env('APP_SUB_CATEGORY') == 'yes')
                                        <th>{{ __('Sub Category') }}</th>
                                    @endif
                                    <th>{{ __('Cost') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th></th>
                                    <th class="header_style_right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $data)
                                    @php
                                        $count_inv = App\Models\InvoiceItem::where('product_id', $data->id)->count();
                                        $count_pur = App\Models\PurchaseItem::where('product_id', $data->id)->count();

                                        $userBranchId = auth()->user()->branch_id;
                                        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

                                        if ($userBranchId == 1) {
                                            if ($filterBranchId) {
                                                $branch_product = App\Models\BranchProduct::where(
                                                    'branch_id',
                                                    $filterBranchId,
                                                )
                                                    ->where('product_id', $data->id)
                                                    ->get();
                                            } else {
                                                $branch_product = App\Models\BranchProduct::where(
                                                    'product_id',
                                                    $data->id,
                                                )->get();
                                            }
                                        } else {
                                            $branch_product = App\Models\BranchProduct::where('product_id', $data->id)
                                                ->where('branch_id', $userBranchId)
                                                ->get();
                                        }
                                    @endphp
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>
                                            @foreach ($branch_product as $branch)
                                                <span class="badge badge-primary">{{ $branch->branch?->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <span class="font-weight-bold">{{ $data->name }}</span>
                                            <div class="mt-1 d-flex flex-wrap" style="gap: 4px;">
                                                @if ($data->is_new_arrival)
                                                    <span class="badge badge-success px-1.5 py-0.5" style="font-size: 10px;">✨ {{ __('New Arrival') }}</span>
                                                @endif
                                                @if ($data->is_top_selling)
                                                    <span class="badge badge-danger px-1.5 py-0.5" style="font-size: 10px;">🔥 {{ __('Top Selling') }}</span>
                                                @endif
                                                @if ($data->is_featured)
                                                    <span class="badge badge-warning text-dark px-1.5 py-0.5" style="font-size: 10px;">⭐ {{ __('Featured') }}</span>
                                                @endif
                                            </div>
                                            @if (env('APP_SC') == 'yes')
                                                @if (count($data->product_variations) > 0)
                                                    <div class="mt-2">
                                                        <select name="variation[]" class="form-control variation_select"
                                                            data-product-id="{{ $data->id }}" style="padding-right: 30px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden; min-width: 120px; font-size: 13px; height: 32px; padding-top: 4px; padding-bottom: 4px;">
                                                            <option value="">{{ __('Select Variation') }}</option>
                                                            @foreach ($data->product_variations as $variation)
                                                                @if ($data->id == $variation->product_id)
                                                                    <option data-variation_id="{{ $variation->id }}"
                                                                        value="{{ $variation->size?->size }}-{{ $variation->color?->color }}">
                                                                        {{ $variation->size?->size }}-{{ $variation->color?->color }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ $data->barcode }}</td>
                                        @if (env('APP_IMEI') == 'yes')
                                        <td>
                                            @if ($data->imei == '1')
                                                <span class="badge badge-success">{{ __('Yes') }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ __('No') }}</span>
                                            @endif
                                        </td>
                                        @endif
                                        <td>{{ $data->category?->name }}</td>
                                        @if (env('APP_SUB_CATEGORY') == 'yes')
                                            <td>{{ $data->subCategory?->name ?? '-' }}</td>
                                        @endif
                                        <td>{{ $data->purchase_price }}</td>
                                        <td>{{ $data->selling_price }}</td>
                                        <td>
                                            <a href="#" class="btn btn-primary-rgba generated_barcode"
                                                data-name="{{ $data->name }}" data-category="{{ $data->category?->name }}" data-code="{{ $data->barcode }}"
                                                data-id="{{ $data->id }}" data-dis_price="{{ $data->dis_selling_price }}" data-discount="{{ $data->discount }}" data-price="{{ $data->selling_price }}">
                                                <i class="fa fa-barcode"></i>
                                            </a>
                                        </td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu">
                                                    @if (check_permission('product.edit'))
                                                        <a href="{{ route('product.edit', $data->id) }}"
                                                            class="dropdown-item text-primary">
                                                            <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                        </a>
                                                    @endif

                                                    @if (check_permission('product.destroy'))
                                                        @if ($count_inv < 1 && $count_pur < 1)
                                                            <a href="#" data-toggle="modal"
                                                                data-target="#deleteModal-{{ $data->id }}"
                                                                class="dropdown-item text-danger">
                                                                <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                            </a>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <form action="{{ route('product.destroy', $data->id) }}" method="POST">
                                        @csrf
                                        <x-delete-modal title="{{ __('Product') }}" id="{{ $data->id }}" />
                                    </form>

                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center no_data_style text-danger">{{ __('No Data Available') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                        {{ $products->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bar_code_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Barcode Settings') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 text-center" style="padding-bottom: 15px; border-bottom: 1px solid #eee;">
                        <label class="mr-3"><input type="checkbox" class="barcode_setting" data-target=".b_company" checked> {{ __('Company') }}</label>
                        <label class="mr-3"><input type="checkbox" class="barcode_setting" data-target=".b_category"> {{ __('Category') }}</label>
                        <label class="mr-3"><input type="checkbox" class="barcode_setting" data-target=".b_name" checked> {{ __('Name') }}</label>
                        <label class="mr-3"><input type="checkbox" class="barcode_setting" data-target=".b_variation" checked> {{ __('Variation') }}</label>
                        <label class="mr-3"><input type="checkbox" class="barcode_setting" data-target=".b_price" checked> {{ __('Price') }}</label>
                        <label><input type="checkbox" class="barcode_setting" data-target=".b_vat"> {{ __('Include VAT') }}</label>
                    </div>
                    <div id="barcode-page">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn cancel_btn" id="modal_cancel">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn save_btn delete" onclick="print_barcode()"><i
                            class="fa fa-print mr-1"></i>
                        {{ __('Print') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Update barcode variation for the correct row
            function getVariationForRow(button) {
                const row = $(button).closest('tr');
                const select = row.find('.variation_select');
                const variation = select.val() || '';
                const variation_id = select.find('option:selected').data('variation_id') || '';
                return {
                    variation,
                    variation_id
                };
            }

            // Barcode button click
            $(document).on('click', '.generated_barcode', function() {
                const name = $(this).data('name');
                const category = $(this).data('category');
                const discount = $(this).data('discount');
                const dis_price = $(this).data('dis_price');
                const price = $(this).data('price');
                const code = $(this).data('code');
                const {
                    variation,
                    variation_id
                } = getVariationForRow(this);
                const full_code = code + "" + variation_id;
                const pro_barcode = "{{ get_setting('pro_barcode') }}" || 'single';
                const company = "{{ $com?->value ?? get_setting('com_name') }}";

                const formattedPrice = new Intl.NumberFormat('en-US', {
                    useGrouping: true,
                    minimumFractionDigits: 2
                }).format(price);
                const formattedDisPrice = new Intl.NumberFormat('en-US', {
                    useGrouping: true,
                    minimumFractionDigits: 2
                }).format(dis_price);
                
                let priceHtml = '';

                if (discount > 0) {
                
                    priceHtml = `
                    <p style="margin-bottom:2px; line-height:9px; margin-top:5px; font-size: 12px; color:black;">
                        <strong>{{ __('Price') }}: <del> ${formattedDisPrice} {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</del></strong>
                    </p>
                    <p style="margin-bottom:2px; line-height:9px; margin-top:5px; font-size: 12px; color:black;">
                        <strong>{{ __('Discount Price') }}: ${formattedPrice} {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong>
                    </p>
                    `;
                
                } else {
                
                    priceHtml = `
                    <p style="margin-bottom:2px; line-height:9px; margin-top:5px; font-size: 12px; color:black;">
                        <strong>{{ __('Price') }}: ${formattedPrice} {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong>
                    </p>
                    `;
                
                }

                $("#barcode-page").html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
                $("#bar_code_modal").modal('show');

                const url = "{{ route('product-barcode', 'value') }}".replace('value', full_code);

                $.get(url, (data) => {
                    let barcode = '';

                    if (pro_barcode === 'a4') {
                        barcode +=
                            `<div class="text-center p-4 print_area" id="barcode"><table class="table table-bordered">`;
                        for (let i = 0; i < 10; i++) {
                            barcode += `<tr>`;
                            for (let j = 0; j < 3; j++) {
                                barcode += `<td>
                            <p class="b_company" style="margin-bottom:1px; line-height:9px; margin-top:5px; font-size: 11px; color:black;"><strong>${company}</strong></p>
                            <p class="b_category" style="margin-bottom:2px; line-height:9px; margin-top:0px; font-size: 9px; color:black;"><strong>${category}</strong></p>
                            <div style="text-align: center; margin: 0;">${data}</div>
                            <p class="b_code" style="margin-bottom:2px; line-height:9px; margin-top:3px; font-size: 9px; color:black;"><strong>${full_code}</strong></p>
                            <p class="b_name" style="margin-bottom:2px; line-height:9px; margin-top:3px; font-size: 9px; color:black;"><strong>${name}</strong></p>
                            <p class="b_variation" style="margin-bottom:2px; line-height:9px; margin-top:3px; font-size: 9px; color:black;"><strong>${variation}</strong></p>
                            <div class="b_price">${priceHtml}</div>
                            <p class="b_vat" style="margin-bottom:2px; line-height:9px; margin-top:2px; font-size: 8px; color:black;"><strong>(including VAT)</strong></p>
                        </td>`;
                            }
                            barcode += `</tr>`;
                        }
                        barcode += `</table></div>`;
                    } else {
                        barcode += `<div class="text-center p-4 print_area" id="barcode" style="padding:0 !important">
                    <table class="table" style="margin-bottom:0 !important;">
                        <tr>
                            <td style="border-top:0 !important;padding:0;">
                                <p class="b_company" style="font-family: Arial, sans-serif;line-height:9px;margin-bottom:1px; font-size: 13px;margin-top:5px; font-weight: 500; text-transform: uppercase; color: black;"><strong>${company}</strong></p>
                                 <p class="b_category" style="margin-bottom:2px; line-height:9px; margin-top:0px; font-size: 9px; color:black;"><strong>${category}</strong></p>
                                <div style="text-align: center; margin: 0;">${data}</div>
                                <p class="b_code" style="margin-bottom:0px; line-height:9px; margin-top:2px; font-size: 10px; color:black;"><strong>${full_code.split('').join(' ')}</strong></p>
                                <p class="b_name" style="margin-bottom:2px; line-height:9px; margin-top:0px; font-size: 9px; color:black;"><strong>${name}</strong></p>
                                <p class="b_variation" style="margin-bottom:2px; line-height:9px; margin-top:-2px; font-size: 9px; color:black;"><strong>${variation}</strong></p>
                                <div class="b_price">${priceHtml}</div>
                                <p class="b_vat" style="margin-bottom:2px; line-height:9px; margin-top:2px; font-size: 8px; color:black;"><strong>(including VAT)</strong></p>
                            </td>
                        </tr>
                    </table>
                </div>`;
                    }

                    $("#barcode-page").html(barcode);
                    $('.barcode_setting').trigger('change');
                }).fail(() => {
                    $("#barcode-page").html('<div class="text-center text-danger p-4">{{ __("Failed to generate barcode.") }}</div>');
                });
            });
            
            $(document).on('change', '.barcode_setting', function() {
                var target = $(this).data('target');
                if ($(this).is(':checked')) {
                    $(target).show();
                } else {
                    $(target).hide();
                }
            });

            // Reset barcode modal on cancel
            $(document).on('click', '#modal_cancel', function() {
                $("#bar_code_modal").modal('hide');
                $('.variation_select').val('');
            });

        });
    </script>

    <script>
        //  Print Barcode
        function print_barcode(id) {
            $("#bar_code_modal").modal('hide');
            $(".modal-backdrop").remove();
            $(".modal").css('display', 'none');

            let printContent = $("#barcode-page").html();
            let printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Barcode Print</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 0;
                            padding: 0px;
                            text-align: center;
                        }
                        .print_area {
                            width: 100%;
                            text-align: center;
                        }
                        svg {
                            max-width: 100% !important;
                            height: 26px !important;
                            display: block !important;
                            margin: 0 auto !important;
                            color-adjust: exact !important;
                            -webkit-print-color-adjust: exact !important;
                        }
                        table {
                            width: 100%;
                            text-align: center;
                        }
                        table td, table th {
                            text-align: center !important;
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                </body>
                </html>
            `);
                printWindow.document.close();
                printWindow.focus();
                printWindow.onload = function() {
                    printWindow.print();
                    printWindow.close();
                };
        }
    </script>
@endpush
