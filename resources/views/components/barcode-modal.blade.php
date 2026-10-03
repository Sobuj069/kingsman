@props([
    'id' => '',
    'barcode' => '',
    'name' => '',
    'price' => '',
])
@php
    $generatorSVG = new Picqer\Barcode\BarcodeGeneratorSVG();
@endphp
<div class="modal fade" id="barcodeModal-{{$id}}" tabindex="-1" role="dialog" aria-labelledby="Delete"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="px-4 modal-content">
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row justify-content-md-center" id="barcode-page-{{$id}}">
                        <div class="col-sm-12 text-center" style="color:black;"><strong>{{ config('app.name') }}</strong></div>
                        <div class="col-sm-12 text-center">{!! $generatorSVG->getBarcode($barcode, $generatorSVG::TYPE_CODE_128, 1.5, 40) !!}</div>
                        <div class="col-sm-12 text-center" style="color:black;"><strong>{{ $name }}</strong></div>
                        <div class="col-sm-12 text-center" style="color:black;"><strong>{{ $price }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</strong></div>
                        @if(is_vat_enabled())
                        <div class="col-sm-12 text-center" style="color:black; font-size: 11px;"><strong>(including VAT)</strong></div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success delete" onclick="print_barcode({{ $id }})"><i class="fa fa-print"></i>
                    Print</button>
            </div>
        </div>
    </div>
</div>
