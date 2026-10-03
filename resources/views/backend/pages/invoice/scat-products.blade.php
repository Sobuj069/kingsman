<div class="row">
    @forelse($products as $product)
    @if(env('APP_IMEI') == 'no' && $product->imei == 1)
        @continue
    @endif
    @php
        $stock_qty = product_stock_check($product);
        $pure_stock = (float) product_fake_stock_val($product);
        $is_out_of_stock = ($product->is_service == 0 && $pure_stock <= 0);
    @endphp
    <!-- Start col -->
    <div class="col-6 col-sm-4 col-md-4 col-lg-3 col-xl-3 mb-2">
        <div class="product-bar productcss product {{ $is_out_of_stock ? 'out-of-stock' : '' }}" data-value="{{ $product->id }}" style="position:relative; cursor:pointer;">
            @if ($is_out_of_stock)
                <div class="stock-badge" style="position: absolute; top: 6px; right: 6px; z-index: 1;">
                    <span class="badge badge-danger" style="font-size:9px;">{{ __('Out of Stock') }}</span>
                </div>
            @endif
            <div class="product-head">
                <img src="{{ !empty($product->images) ? url('uploads/products/' . $product->images) : url('backend/images/no_images.png') }}"
                    class="img-fluid"
                    style="height: 100px; width: 100%; object-fit: cover; border-radius: 12px;"
                    alt="{{ $product->name }}">
            </div>
            <div class="product-body py-2" style="padding: 6px 4px;">
                <div class="text-center">
                    <h6 class="mt-1 mb-1" style="font-size: 12px; line-height: 1.3; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">{{ $product->name }}</h6>
                    <small class="font-weight-bold d-block">{{ $product->selling_price }} {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</small>
                    <small class="stock text-muted d-block" style="font-size: 10px;">{{ __('Stock') }}: {{ $stock_qty }}</small>
                </div>
            </div>
        </div>
    </div>
    <!-- End col -->
    @empty
    <div class="col-md-12" style="padding-bottom: 30px;">
        <div class="alert alert-danger text-center" role="alert"> {{ __('Products not available!') }}</div>
    </div>
    @endforelse
</div>

<div class="pagination justify-content-center mt-2">
    {{ $products->onEachSide(0)->links() }}
</div>
