@extends('backend.layouts.master')
@section('section-title', __('Product'))
@section('page-title', __('Add Product'))

@push('css')
@endpush

@if (check_permission('product.update'))
    @section('action-button')
        <a href="{{ route('product.index') }}" class="btn add_list_btn">
            <i class="mr-2 feather icon-list"></i>
            {{ __('All Product') }}
        </a>
    @endsection
@endif

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div>
                        <form id="productCreateForm" class="row g-3 needs-validation" method="POST" action="{{ route('product.store') }}"
                            enctype="multipart/form-data" novalidate>
                            @csrf
                            {{-- Product name --}}
                            <div class="mt-2 col-md-6">
                                <label for="name" class="form-label fw-bold">{{ __('Name *') }}</label>
                                <input type="text" onfocus="this.select()" autofocus class="form-control" name="name"
                                    placeholder="{{ __('Enter Name') }}">
                                <div class="errors">{{ $errors->has('name') ? $errors->first('name') : '' }}</div>
                            </div>

                            {{-- Product barcode  --}}
                            <div class="mt-2 col-md-6">
                                <label for="barcode" class="form-label fw-bold">
                                    {{ __('Barcode') }}
                                    <span class="badge badge-success ml-1 text-white" style="font-size: 11px; font-weight: normal; background-color: #28a745;">
                                        <i class="feather icon-zap"></i> {{ __('Scan to Auto-Save') }}
                                    </span>
                                </label>
                                <input type="text" id="barcode" class="form-control" placeholder="{{ __('Enter or Scan Barcode') }}" name="barcode" autocomplete="off">
                                <div class="errors">{{ $errors->has('barcode') ? $errors->first('barcode') : '' }}</div>
                            </div>

                            {{-- Category --}}
                            <div class="mt-2 col-md-6">
                                <div class="row align-items-end">
                                    <div class="col-10 pr-0">
                                        <label for="unit_id" class="form-label fw-bold mb-1">{{ __('Category *') }}</label>
                                        <select class="select2" name="category_id" id="categoryId">
                                            <option selected value="">{{ __('Select Category') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="errors text-danger text-[10px]">
                                            {{ $errors->has('category_id') ? $errors->first('category_id') : '' }}</div>
                                    </div>
                                    <div class="col-2 pl-1">
                                        <button data-toggle="modal" type="button" data-target="#addModal1"
                                            class="btn extra_btn mb-0 shadow-sm">
                                            <i class="feather icon-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            {{-- Sub Category --}}
                            @if (env('APP_SUB_CATEGORY') == 'yes')
                                <div class="mt-2 col-md-6">
                                    <div class="row align-items-end">
                                        <div class="col-10 pr-0">
                                            <label for="sub_category_id" class="form-label fw-bold mb-1">{{ __('Sub Category') }}</label>
                                            <select class="select2" name="sub_category_id" id="subCategoryId">
                                                <option selected value="">{{ __('Select Sub Category') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-2 pl-1">
                                            <a href="{{ route('sub-category.index') }}" target="_blank" type="button" class="btn extra_btn mb-0 shadow-sm">
                                                <i class="feather icon-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                {{-- Child Category --}}
                                <div class="mt-2 col-md-6">
                                    <div class="row align-items-end">
                                        <div class="col-10 pr-0">
                                            <label for="child_category_id" class="form-label fw-bold mb-1">{{ __('Child Category') }}</label>
                                            <select class="select2" name="child_category_id" id="childCategoryId">
                                                <option selected value="">{{ __('Select Child Category') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-2 pl-1">
                                            <a href="{{ route('child-category.index') }}" target="_blank" type="button" class="btn extra_btn mb-0 shadow-sm">
                                                <i class="feather icon-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            {{-- Brand --}}
                            <div class="mt-2 col-md-6">
                                <div class="row align-items-end">
                                    <div class="col-10 pr-0">
                                        <label for="brand_id" class="form-label fw-bold mb-1">{{ __('Brand') }}</label>
                                        <select class="select2" name="brand_id" id="brandId">
                                            <option selected value="">{{ __('Select Brand') }}</option>
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-2 pl-1">
                                        <button data-toggle="modal" type="button" data-target="#addModal"
                                            class="btn extra_btn mb-0 shadow-sm">
                                            <i class="feather icon-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Unit --}}
                            @if(is_unit_enabled())
                            <div class="mt-2 col-md-3 ">
                                <label for="unit_id" class="form-label fw-bold">{{ __('Main Unit') }}</label>
                                <select class="select2 main_unit" name="unit_id">
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Unit --}}
                            <div class="mt-2 col-md-3 ">
                                <label for="unit_id" class="form-label fw-bold">{{ __('Sub Unit') }}</label>
                                <select name="sub_unit_id" id="" class="form-control sub_unit">
                                    <option value="">{{ __('No Related Unit Found') }}</option>
                                </select>
                            </div>
                            @endif

                            {{-- Unit --}}
                            <div class="mt-2 {{ is_unit_enabled() ? 'col-md-6' : 'col-md-12' }} ">
                                <div class="form-group">
                                    <label for="">{{ __('Low Stock Quantity') }}</label>
                                    <div class="opening_stocks form-row" style="padding-left: 5px; padding-right:5px;">
                                        <input type="text" name="main_qty" value="" class="form-control col"
                                            placeholder="{{ __('Pcs') }}">
                                    </div>
                                </div>
                            </div>
                            {{-- variation --}}
                            @if (env('APP_SC') == 'yes')
                                <div class="mt-2 col-md-6">
                                    <div class="row align-items-end">
                                        <div class="col-10 pr-0">
                                            <label for="size" class="form-label fw-bold mb-1">{{ __('Size') }} </label>
                                            <select class="select2 product_size" name="size[]" multiple data-placeholder="{{ __('Select Size') }}">
                                                <option value=""></option>
                                                @foreach ($sizes as $size)
                                                    <option value="{{ $size->id }}">{{ $size->size }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-2 pl-1">
                                            <button type="button" class="btn extra_btn mb-0 shadow-sm" data-toggle="modal"
                                                data-target="#addSizeModal">
                                                <i class="feather icon-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="errors">{{ $errors->has('size') ? $errors->first('size') : '' }}</div>
                                </div>
                                <div class="mt-2 col-md-6">
                                    <div class="row align-items-end">
                                        <div class="col-10 pr-0">
                                            <label for="color" class="form-label fw-bold mb-1">{{ __('Color') }} </label>
                                            <select class="select2 product_color" name="color[]" multiple data-placeholder="{{ __('Select Color') }}">
                                                <option value=""></option>
                                                @foreach ($colors as $color)
                                                    <option value="{{ $color->id }}"> {{ $color->color }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-2 pl-1">
                                            <button type="button" class="btn extra_btn mb-0 shadow-sm" data-toggle="modal"
                                                data-target="#addColorModal">
                                                <i class="feather icon-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="errors">{{ $errors->has('color') ? $errors->first('color') : '' }}</div>
                                </div>

                                {{-- Dynamic Color Variant Images Upload Section --}}
                                <div id="color_images_container" class="col-md-12 mt-3" style="display: none;">
                                    <div class="card border p-3 shadow-sm" style="background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h6 class="fw-bold mb-0 text-dark">
                                                <i class="feather icon-image text-primary mr-1"></i> {{ __('Color Variant Images (For Frontend Display)') }}
                                            </h6>
                                            <span class="badge badge-primary px-2 py-1 text-xs">{{ __('Storefront Sync') }}</span>
                                        </div>
                                        <p class="text-muted small mb-3">
                                            {{ __('Upload a dedicated product photo for each chosen color. When customers click on a color on the website, this image will automatically be displayed.') }}
                                        </p>
                                        <div class="row" id="color_images_grid"></div>
                                    </div>
                                </div>

                                {{-- Dynamic Size-Wise Custom Pricing Section --}}
                                <div id="size_prices_container" class="col-md-12 mt-3" style="display: none;">
                                    <div class="card border p-3 shadow-sm" style="background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h6 class="fw-bold mb-0 text-dark">
                                                <i class="feather icon-tag text-success mr-1"></i> {{ __('Size-Wise Custom Sale Pricing (e.g. 15ml, 20ml, 50ml or S, M, L)') }}
                                            </h6>
                                            <span class="badge badge-success px-2 py-1 text-xs">{{ __('Custom Size Rates') }}</span>
                                        </div>
                                        <p class="text-muted small mb-3">
                                            {{ __('Set individual Sale Price & Regular/Old Price for each selected size. If left blank, the general product sale price below will be used by default.') }}
                                        </p>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm bg-white mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th style="width: 25%;">{{ __('Size / Volume') }}</th>
                                                        <th style="width: 35%;">{{ __('Regular Price (৳)') }} <small class="text-muted">({{ __('Original/Crossed') }})</small></th>
                                                        <th style="width: 40%;">{{ __('Sale / Offer Price (৳) *') }} <small class="text-success">({{ __('Selling Rate') }})</small></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="size_prices_tbody"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Price --}}
                            <div class="mt-2 col-md-6">
                                <label for="purchase_price" class="form-label fw-bold">{{ __('Purchase Price *') }}</label>
                                <input type="number" step="any" class="form-control" name="purchase_price" id="purchase_price" placeholder="0.00">
                                <div class="errors">
                                    {{ $errors->has('purchase_price') ? $errors->first('purchase_price') : '' }}
                                </div>
                            </div>
                            <div class="mt-2 col-md-6">
                                <label class="form-label fw-bold">{{ __('Sale Price *') }}</label>
                                <input type="number" step="any" class="form-control" name="dis_selling_price" id="selling_price" placeholder="0.00">
                            </div>

                            <div class="mt-2 col-md-6">
                                <label class="form-label fw-bold">{{ __('Discount') }}</label>
                                <input type="text" class="form-control" name="discount" id="discount" placeholder="e.g. 10 or 10%">
                            </div>

                            <div class="mt-2 col-md-6">
                                <label class="form-label fw-bold">{{ __('Discount Price *') }}</label>
                                <input type="number" step="any" class="form-control" name="selling_price" id="discount_price"
                                    readonly placeholder="0.00">

                                <div id="price_error" style="color:red; display:none;">
                                    {{ __('Sale price cannot be below purchase price') }}
                                </div>
                            </div>
                            {{-- status --}}
                            <div class="col-md-6 mt-2" style="margin-right: -6px">
                                <label for="status" class="form-label fw-bold">{{ __('Status *') }}</label>
                                <select class="select2" name="status">
                                    <option value="1">{{ __('Active') }}</option>
                                    <option value="0">{{ __('Deactive') }}</option>
                                </select>
                                <div class="errors">{{ $errors->has('status') ? $errors->first('status') : '' }}</div>
                            </div>
                            @if (env('APP_IMEI') == 'yes')
                            <div class="mt-2 col-md-6  ">
                                <label for="imei" class="form-label fw-bold">{{ __('Has IMEI') }}</label>
                                <select class="select2" name="imei">
                                    <option value="0">{{ __('No') }}</option>
                                    <option value="1">{{ __('Yes') }}</option>
                                </select>
                            </div>
                            @endif
                            @if(is_warranty_enabled())
                            <div class="col-md-12 mt-3 p-3" style="background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label fw-bold text-dark"><i class="feather icon-shield mr-1 text-primary"></i> {{ __('Warranty Preset / Type') }}</label>
                                        <select class="form-control select2" name="warranty_id" id="product_warranty_id">
                                            <option value="">{{ __('No Warranty / Custom') }}</option>
                                            @foreach ($warranties ?? [] as $w)
                                                <option value="{{ $w->id }}" data-duration="{{ $w->duration }}" data-period="{{ $w->period }}">{{ $w->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label fw-bold text-dark">{{ __('Warranty Duration (Value)') }}</label>
                                        <input type="number" name="warranty_value" id="product_warranty_value" class="form-control" placeholder="e.g. 1, 6, 12" min="0">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label fw-bold text-dark">{{ __('Warranty Unit') }}</label>
                                        <select name="warranty_unit" id="product_warranty_unit" class="form-control select2">
                                            <option value="Day">{{ __('Day') }}</option>
                                            <option value="Month" selected>{{ __('Month') }}</option>
                                            <option value="Year">{{ __('Year') }}</option>
                                            <option value="Lifetime">{{ __('Lifetime') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Website Showcase & Homepage Sections --}}
                            <div class="col-md-12 mt-3 mb-2">
                                <div class="card p-3 shadow-sm" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 10px; border: 1px solid #cbd5e1;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="fw-bold mb-0 text-dark">
                                            <i class="feather icon-layout text-primary mr-1"></i> {{ __('Website Showcase & Homepage Sections (ওয়েবসাইট সেকশন)') }}
                                        </h6>
                                        <span class="badge badge-info px-2 py-1 text-xs">{{ __('Storefront Display') }}</span>
                                    </div>
                                    <p class="text-muted small mb-3">
                                        {{ __('Select where this product should appear on the website homepage. Products selected here will automatically be featured in the corresponding homepage sections.') }}
                                    </p>

                                    <div class="row">
                                        <!-- New Arrival Products -->
                                        <div class="col-md-4 col-12 mb-2">
                                            <label class="d-flex align-items-center p-3 rounded bg-white border border-slate-200 cursor-pointer h-100 shadow-xs hover-shadow transition" style="cursor: pointer; gap: 12px; border-radius: 8px;">
                                                <input type="checkbox" name="is_new_arrival" value="1" class="form-check-input mt-0 position-static" style="width: 20px; height: 20px;" {{ old('is_new_arrival', 1) ? 'checked' : '' }}>
                                                <div>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <span class="badge badge-success px-2 py-0.5 text-xs mr-1"><i class="feather icon-sparkles"></i> New</span>
                                                        <strong class="text-dark">{{ __('New Arrival Products') }}</strong>
                                                    </div>
                                                    <small class="text-muted d-block mt-1">{{ __('Showcase in "New Arrival Products" on website home page') }}</small>
                                                </div>
                                            </label>
                                        </div>

                                        <!-- Top Selling Products -->
                                        <div class="col-md-4 col-12 mb-2">
                                            <label class="d-flex align-items-center p-3 rounded bg-white border border-slate-200 cursor-pointer h-100 shadow-xs hover-shadow transition" style="cursor: pointer; gap: 12px; border-radius: 8px;">
                                                <input type="checkbox" name="is_top_selling" value="1" class="form-check-input mt-0 position-static" style="width: 20px; height: 20px;" {{ old('is_top_selling') ? 'checked' : '' }}>
                                                <div>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <span class="badge badge-danger px-2 py-0.5 text-xs mr-1"><i class="feather icon-trending-up"></i> Hot</span>
                                                        <strong class="text-dark">{{ __('Top Selling Products') }}</strong>
                                                    </div>
                                                    <small class="text-muted d-block mt-1">{{ __('Showcase in "Top Selling Products" on website home page') }}</small>
                                                </div>
                                            </label>
                                        </div>

                                        <!-- Featured Product -->
                                        <div class="col-md-4 col-12 mb-2">
                                            <label class="d-flex align-items-center p-3 rounded bg-white border border-slate-200 cursor-pointer h-100 shadow-xs hover-shadow transition" style="cursor: pointer; gap: 12px; border-radius: 8px;">
                                                <input type="checkbox" name="is_featured" value="1" class="form-check-input mt-0 position-static" style="width: 20px; height: 20px;" {{ old('is_featured') ? 'checked' : '' }}>
                                                <div>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <span class="badge badge-warning text-dark px-2 py-0.5 text-xs mr-1"><i class="feather icon-star"></i> Featured</span>
                                                        <strong class="text-dark">{{ __('Featured Product') }}</strong>
                                                    </div>
                                                    <small class="text-muted d-block mt-1">{{ __('Highlight as a featured product across the store') }}</small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                            {{-- Size Guide Settings --}}
                            <div class="col-md-12 mt-3 mb-2">
                                <div class="card p-3 shadow-sm" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 10px; border: 1px solid #cbd5e1;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="fw-bold mb-0 text-dark">
                                            <i class="feather icon-help-circle text-primary mr-1"></i> {{ __('Size Guide Settings (সাইজ গাইড অপশন)') }}
                                        </h6>
                                        <span class="badge badge-primary px-2 py-1 text-xs">{{ __('Storefront Sizing') }}</span>
                                    </div>
                                    <p class="text-muted small mb-3">
                                        {{ __('Enable Size Guide button on this product details page. Only products with Size Guide enabled will display the "SIZE GUIDE" modal button.') }}
                                    </p>
                                    
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="has_size_guide" name="has_size_guide" value="1" {{ old('has_size_guide') ? 'checked' : '' }} onchange="toggleSizeGuideSettings()">
                                                <label class="custom-control-label fw-bold text-dark" for="has_size_guide" style="cursor: pointer;">
                                                    {{ __('Enable Size Guide for this Product (এই প্রোডাক্টে সাইজ গাইড চালু করুন)') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="size_guide_options" style="{{ old('has_size_guide') ? 'display: block;' : 'display: none;' }}">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">{{ __('Size Guide Type / Preset') }}</label>
                                                <select name="size_guide_type" id="size_guide_type" class="form-control select2">
                                                    <option value="panjabi" {{ old('size_guide_type', 'panjabi') == 'panjabi' ? 'selected' : '' }}>{{ __('Panjabi Standard Chart (পাঞ্জাবি)') }}</option>
                                                    <option value="shirt" {{ old('size_guide_type') == 'shirt' ? 'selected' : '' }}>{{ __('Shirt Standard Chart (শার্ট)') }}</option>
                                                    <option value="tshirt" {{ old('size_guide_type') == 'tshirt' ? 'selected' : '' }}>{{ __('T-Shirt / Polo Chart (টি-শার্ট)') }}</option>
                                                    <option value="pants" {{ old('size_guide_type') == 'pants' ? 'selected' : '' }}>{{ __('Pants / Trouser Chart (প্যান্ট)') }}</option>
                                                    <option value="custom_image" {{ old('size_guide_type') == 'custom_image' ? 'selected' : '' }}>{{ __('Custom Size Chart Image Only (কাস্টম ছবি)') }}</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">{{ __('Upload Size Chart Image (Optional / Custom)') }}</label>
                                                <input type="file" name="size_guide_image" id="size_guide_image" class="form-control" accept="image/*">
                                                <small class="text-muted">{{ __('Upload a custom size chart image for this product if needed.') }}</small>
                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <label class="form-label fw-bold">{{ __('Custom Note / Sizing Instructions (Optional)') }}</label>
                                                <textarea name="size_guide_content" class="form-control" rows="2" placeholder="{{ __('e.g. Measurements are in inches. For a regular fit, order your usual size.') }}">{{ old('size_guide_content') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                             <div class="mt-2 col-md-12">
                                 <label for="description" class="form-label fw-bold">{{ __('Description') }}</label>
                                 <textarea class="form-control summernote" id="description" name="description" rows="4"></textarea>
                             </div>

                            {{-- Image --}}
                            <div class="mt-2 col-md-6 ">
                                <label for="image" class="form-label fw-bold">{{ __('Product Images') }}</label>
                                <input class="form-control image-uploadify" type="file" name="images"
                                    accept="image/*" onchange="readURL(this);">
                            </div>

                            <div class="mt-2 col-md-6 ">
                                <img id="image" style="border-radius: 5px"
                                    src="{{ asset('backend/images/no_images.png') }}" width="120px" height="80px" />
                            </div>
                            @if (auth()->user()->branch_id == 1)
                                <div class="mt-2 col-md-12">
                                    <label class="form-label fw-bold">{{ __('Branch *') }}</label>

                                    {{-- All Select Checkbox --}}
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="select_all_branches" checked>
                                        <label class="form-check-label fw-bold" for="select_all_branches">{{ __('Select All') }}</label>
                                    </div>

                                    {{-- Branch Checkboxes --}}
                                    <div id="branch_checkbox_list">
                                        @foreach ($allBranch as $branch)
                                            <div class="form-check">
                                                <input class="form-check-input branch-checkbox" type="checkbox"
                                                    name="branch_id[]" value="{{ $branch->id }}"
                                                    id="branch_{{ $branch->id }}" checked>
                                                <label class="form-check-label" for="branch_{{ $branch->id }}">
                                                    {{ $branch->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="text-danger">
                                        {{ $errors->has('branch_id') ? $errors->first('branch_id') : '' }}
                                    </div>
                                </div>
                            @endif
                            <div class="mt-3 text-center col-12">
                                <button id="submitBtn" class="btn save_btn" type="submit"> {{ __('Save') }} </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('selling_price').addEventListener('input', calculateDiscount);
        document.getElementById('discount').addEventListener('input', calculateDiscount);
        function calculateDiscount() {
            let sale = parseFloat(document.getElementById('selling_price').value) || 0;
            let discountVal = document.getElementById('discount').value.toString();
            let discount = 0;

            if (discountVal.includes('%')) {
                let percent = parseFloat(discountVal.replace('%', '')) || 0;
                discount = (sale * percent) / 100;
            } else {
                discount = parseFloat(discountVal) || 0;
            }

            let finalPrice = sale - discount;
            if (finalPrice < 0) {
                finalPrice = 0;
            }
            document.getElementById('discount_price').value = finalPrice.toFixed(2);
        }
    </script>
    {{-- Add Category Modal --}}
    <form action="#" id="categoryForm" method="POST">
        @csrf
        <x-another-modal title="{{ __('Add Category') }}" sizeClass="modal-md">
            <x-input label="{{ __('Category Name *') }}" type="text" name="name" placeholder="{{ __('Enter Category Name') }}" required />
            @if (auth()->user()->branch_id == 1)
                <div class="mb-3 col-md-12">
                    <label class="form-label fw-bold mb-1">{{ __('Branch *') }}</label>
                    <select name="branch_id[]" class="form-control select2" multiple style="width: 100%">
                        @foreach ($allBranch as $branch_item)
                            <option value="{{ $branch_item->id }}">{{ $branch_item->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </x-another-modal>
    </form>

    {{-- Add Brand Modal --}}
    <form action="#" id="brandForm" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Brand') }}" sizeClass="modal-md">
            <x-input label="{{ __('Brand Name *') }}" type="text" name="name" placeholder="{{ __('Enter Brand Name') }}" required />
            @if (auth()->user()->branch_id == 1)
                <div class="mb-3 col-md-12">
                    <label class="form-label fw-bold mb-1">{{ __('Branch *') }}</label>
                    <select name="branch_id[]" class="form-control select2" multiple style="width: 100%">
                        @foreach ($allBranch as $branch_item)
                            <option value="{{ $branch_item->id }}">{{ $branch_item->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </x-add-modal>
    </form>
    <div class="modal fade" id="addSizeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addSizeForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ __('Add New Size') }}</h5>
                        <!-- ✅ Close button in header -->
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label ml-3">{{ __('Size Name') }}</label>
                            <input type="text" class="form-control" name="size_name" id="size_name"
                                placeholder="{{ __('Size Name') }}">
                        </div>
                        <div id="sizeError" class="text-danger"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn cancel_btn" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn save_btn">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- color Modal -->
    <div class="modal fade" id="addColorModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addColorForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ __('Add New Color') }}</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label ml-3"> {{ __('Color Name') }} </label>
                            <input type="text" class="form-control" name="color_name" placeholder="{{ __('Color Name') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn cancel_btn" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn save_btn">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        // toastr compatibility wrapper for ToastMagic
        if (typeof toastr === 'undefined') {
            window.toastr = {
                success: function(msg) {
                    if (typeof window.toastMagic !== 'undefined') {
                        window.toastMagic.success(msg);
                    } else if (typeof ToastMagic !== 'undefined') {
                        (new ToastMagic()).success(msg);
                    } else {
                        console.log('Success:', msg);
                    }
                },
                error: function(msg) {
                    if (typeof window.toastMagic !== 'undefined') {
                        window.toastMagic.error(msg);
                    } else if (typeof ToastMagic !== 'undefined') {
                        (new ToastMagic()).error(msg);
                    } else {
                        console.error('Error:', msg);
                    }
                }
            };
        }

        $(document).ready(function() {

            $('#addColorForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('color.ajaxStore') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {

                        // Modal hide
                        $('#addColorModal').modal('hide');
                        $('#addColorForm')[0].reset();
                        $('#colorError').text('');
                        if (response.color) {
                            // Create new option
                            var newOption = new Option(response.color.color_name, response.color
                                .id, false, false);

                            // Append to select2
                            $('select.product_color').append(newOption);

                            // ✅ Get current selected values
                            var currentValues = $('select.product_color').val() || [];

                            // ✅ Add new value to selected values array
                            currentValues.push(response.color.id.toString());

                            // ✅ Set multiple values
                            $('select.product_color').val(currentValues).trigger('change');

                            // Refresh select2
                            $('select.product_color').trigger('change.select2');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $('#colorError').text(xhr.responseJSON.errors.color_name[0]);
                        } else {
                            toastr.error('Something went wrong!');
                        }
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {

            $('#addSizeForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('size.ajaxStore') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {

                        // Modal hide
                        $('#addSizeModal').modal('hide');
                        $('#addSizeForm')[0].reset();
                        $('#sizeError').text('');

                        // ✅ Dynamically add new option to select2 WITHOUT page reload
                        if (response.size) {
                            // Create new option
                            var newOption = new Option(response.size.size_name, response.size
                                .id, false, false);

                            // Append to select2
                            $('select.product_size').append(newOption);

                            // ✅ Get current selected values
                            var currentValues = $('select.product_size').val() || [];

                            // ✅ Add new value to selected values array
                            currentValues.push(response.size.id.toString());

                            // ✅ Set multiple values
                            $('select.product_size').val(currentValues).trigger('change');

                            // Refresh select2
                            $('select.product_size').trigger('change.select2');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $('#sizeError').text(xhr.responseJSON.errors.size_name[0]);
                        } else {
                            toastr.error('Something went wrong!');
                        }
                    }
                });
            });
        });
    </script>


    {{-- JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select_all_branches');
            const checkboxes = document.querySelectorAll('.branch-checkbox');

            selectAll.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAll.checked;
                });
            });

            // Sync "Select All" if all are manually selected/deselected
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    selectAll.checked = [...checkboxes].every(cb => cb.checked);
                });
            });
        });
    </script>

    <script type="text/javascript">
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#image')
                        .attr('src', e.target.result)
                        .width(120)
                        .height(80);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            // Attach input/change event listeners for all price & discount fields
            $('#purchase_price, #selling_price, #discount_price, #discount').on('input change', function() {
                var purchasePrice = parseFloat($('#purchase_price').val()) || 0;
                var sellingPrice = parseFloat($('#discount_price').val());
                if (isNaN(sellingPrice) || sellingPrice === 0) {
                    sellingPrice = parseFloat($('#selling_price').val()) || 0;
                }

                // Check if purchase price is greater than selling price (only if selling price is entered)
                if (sellingPrice > 0 && purchasePrice > sellingPrice) {
                    $('#price_error').show(); // Show error message
                    $('#submitBtn').attr('disabled', true); // Disable submit button
                } else {
                    $('#price_error').hide(); // Hide error message
                    $('#submitBtn').attr('disabled', false); // Enable submit button
                }
            });
        });
    </script>
    <script>
        //category modal ajax code
        $(document).ready(function() {
            $('#categoryForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                $.ajax({
                    url: "{{ route('category.store') }}", // Define the route for submission
                    method: 'POST',
                    data: $(this).serialize(), // Serialize form data
                    success: function(response) {
                        $('#addModal1').modal('hide');
                        $('#categoryForm')[0].reset();
                        // Reset and reload the dropdown with Select2 re-initialization
                        if ($('#categoryId').data('select2')) {
                            $('#categoryId').select2('destroy');
                        }
                        $('#categoryId').load(location.href + ' #categoryId>*', function() {
                            $('#categoryId').select2({ width: '100%' });
                            toastr.success(response.message || 'Category created successfully');
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors && errors.name) {
                                toastr.error(errors.name[0]);
                            } else {
                                toastr.error('Validation failed!');
                            }
                        } else {
                            toastr.error('Something went wrong!');
                        }
                    }
                });
            });
        });
    </script>
    <script>
        //Brand modal ajax code
        $(document).ready(function() {
            $('#brandForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                $.ajax({
                    url: "{{ route('brand.store') }}", // Define the route for submission
                    method: 'POST',
                    data: $(this).serialize(), // Serialize form data
                    success: function(response) {
                        $('#addModal').modal('hide');
                        $('#brandForm')[0].reset();
                        // Reset and reload the dropdown with Select2 re-initialization
                        if ($('#brandId').data('select2')) {
                            $('#brandId').select2('destroy');
                        }
                        $('#brandId').load(location.href + ' #brandId>*', function() {
                            $('#brandId').select2({ width: '100%' });
                            toastr.success(response.message || 'Brand created successfully');
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors && errors.name) {
                                toastr.error(errors.name[0]);
                            } else {
                                toastr.error('Validation failed!');
                            }
                        } else {
                            toastr.error('Something went wrong!');
                        }
                    }
                });
            });
        });
    </script>
    <script>
        $('.main_unit').change(function() {
            $('.sub_unit').html('<option value="">{{ __('No Related Unit Found') }}</option>');
            var main_unit_id = $(this).find(':selected').val();
            var main_unit_text = $(this).find(':selected').text();

            let url = "{{ route('product-unit', 'my_id') }}".replace('my_id', main_unit_id);

            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    if (data) {
                        var sub_value = '<option value="">{{ __('Select Unit') }}</option><option value="' + data
                            .related_unit_id + '">' + data.related_unit.name + '</option>';
                        $('.sub_unit').html(sub_value);

                        // Opening Stock
                        var opening_stock = "";
                        opening_stock +=
                            `<input type="text" name="main_qty" value="" class="form-control col" placeholder="${main_unit_text}">`;
                        $('.opening_stocks').html(opening_stock);
                    } else {
                        $('.sub_unit').html('<option value="" selected>{{ __('No Related Unit Found') }}</option>');
                        // opening Stock

                        var opening_stock =
                            `<input type="text" name="main_qty" value="" class="form-control col" placeholder="${main_unit_text}">`;
                        $('.opening_stocks').html(opening_stock);
                    }
                }
            });
        });

        $('.sub_unit').change(function() {
            var sub_unit_id = $(this).find(':selected').val();
            var sub_unit_text = $(this).find(':selected').text();

            var main_unit_id = $('.main_unit').find(':selected').val();
            var main_unit_text = $('.main_unit').find(':selected').text();
            var opening_stock = '';
            if (sub_unit_id == "") {
                opening_stock =
                    `<input type="text" name="main_qty" value="" class="form-control col" placeholder="${main_unit_text}">`;
            } else {
                opening_stock +=
                    `<input type="text" name="main_qty" value="" class="form-control col" placeholder="${main_unit_text}" style="margin-right:5px;">`;
                opening_stock +=
                    `<input type="text" name="sub_qty" value="" class="form-control col" placeholder="${sub_unit_text}">`;
            }

            $('.opening_stocks').html(opening_stock);

        });
    </script>
    <script>
        $(document).ready(function() {
            if ($.fn.summernote) {
                $('#description').summernote({
                    placeholder: '{{ __("Enter Description...") }}',
                    tabsize: 2,
                    height: 150,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture']],
                        ['view', ['fullscreen', 'codeview']]
                    ]
                });
            }

            // Fix Select2 focus and dropdown issue inside Bootstrap modals
            $('#addModal, #addModal1').on('shown.bs.modal', function () {
                $(this).find('.select2').select2({
                    dropdownParent: $(this),
                    width: '100%',
                    placeholder: 'Select Branches',
                    allowClear: true
                });
            });

            // Dynamic Subcategory population
            $('#categoryId').on('change', function() {
                var category_id = $(this).val();
                var $subSelect = $('#subCategoryId');
                var $childSelect = $('#childCategoryId');
                if ($subSelect.length) {
                    if (category_id) {
                        $.ajax({
                            url: "{{ route('sub-category.by-category') }}",
                            type: "GET",
                            data: { category_id: category_id },
                            success: function(data) {
                                $subSelect.empty().append('<option value="">{{ __("Select Sub Category") }}</option>');
                                $.each(data, function(key, value) {
                                    $subSelect.append('<option value="' + value.id + '">' + value.name + '</option>');
                                });
                                if ($.isFunction($.fn.select2)) {
                                    $subSelect.select2({ width: '100%', theme: 'default' });
                                }
                                $subSelect.trigger('change');
                            }
                        });
                    } else {
                        $subSelect.empty().append('<option value="">{{ __("Select Sub Category") }}</option>');
                        if ($.isFunction($.fn.select2)) {
                            $subSelect.select2({ width: '100%', theme: 'default' });
                        }
                        $subSelect.trigger('change');
                        if ($childSelect.length) {
                            $childSelect.empty().append('<option value="">{{ __("Select Child Category") }}</option>');
                            if ($.isFunction($.fn.select2)) {
                                $childSelect.select2({ width: '100%', theme: 'default' });
                            }
                            $childSelect.trigger('change');
                        }
                    }
                }
            });

            // Dynamic Child Category population
            function loadChildCategories(sub_category_id, selectedChildId) {
                var $childSelect = $('#childCategoryId');
                if (!$childSelect.length) return;

                if (sub_category_id) {
                    $.ajax({
                        url: "{{ route('child-category.by-subcategory') }}",
                        type: "GET",
                        data: { sub_category_id: sub_category_id },
                        success: function(data) {
                            $childSelect.empty().append('<option value="">{{ __("Select Child Category") }}</option>');
                            $.each(data, function(key, value) {
                                var isSel = (selectedChildId && String(selectedChildId) === String(value.id)) ? ' selected' : '';
                                $childSelect.append('<option value="' + value.id + '"' + isSel + '>' + value.name + '</option>');
                            });
                            if ($.isFunction($.fn.select2)) {
                                $childSelect.select2({ width: '100%', theme: 'default' });
                            }
                            $childSelect.trigger('change.select2');
                        }
                    });
                } else {
                    $childSelect.empty().append('<option value="">{{ __("Select Child Category") }}</option>');
                    if ($.isFunction($.fn.select2)) {
                        $childSelect.select2({ width: '100%', theme: 'default' });
                    }
                    $childSelect.trigger('change.select2');
                }
            }

            $('#subCategoryId').on('change', function() {
                var sub_category_id = $(this).val();
                loadChildCategories(sub_category_id, null);
            });

            // Warranty auto-fill on preset selection
            $('#product_warranty_id').on('change', function() {
                var selected = $(this).find('option:selected');
                var duration = selected.data('duration');
                var period = selected.data('period');
                if (duration !== undefined && duration !== null && duration !== '') {
                    $('#product_warranty_value').val(duration);
                } else {
                    $('#product_warranty_value').val('');
                }
                if (period) {
                    $('#product_warranty_unit').val(period).trigger('change');
                }
            });

            // ===============================================================
            // 🚀 BARCODE SCANNER & ENTER AUTO-SAVE SYSTEM
            // ===============================================================
            window._isProductSaving = false;

            function prepareAndSubmitProduct() {
                if (window._isProductSaving) return;

                // 1. Sync price & discount fields
                if (typeof calculateDiscount === 'function') {
                    calculateDiscount();
                }

                var saleVal = $('#selling_price').val();
                var discVal = $('#discount_price').val();
                if ((!discVal || discVal === '' || isNaN(parseFloat(discVal))) && saleVal) {
                    $('#discount_price').val(parseFloat(saleVal).toFixed(2));
                }
                if ($('#purchase_price').val() === '' || isNaN(parseFloat($('#purchase_price').val()))) {
                    $('#purchase_price').val('0');
                }

                // 2. Validate essential fields
                var name = $('input[name="name"]').val() ? $('input[name="name"]').val().trim() : '';
                var categoryId = $('select[name="category_id"]').val();

                if (!name) {
                    toastr.warning('{{ __("Please enter Product Name!") }}');
                    $('input[name="name"]').focus();
                    return;
                }

                if (!categoryId) {
                    toastr.warning('{{ __("Please select a Category!") }}');
                    $('#categoryId').select2('open');
                    return;
                }

                // 3. Prevent multiple submits & show loading feedback
                window._isProductSaving = true;
                toastr.success('{{ __("Barcode scanned! Saving product...") }}');
                $('#submitBtn').html('<i class="feather icon-loader mr-1"></i> {{ __("Saving...") }}');

                // 4. Direct native form submit
                setTimeout(function() {
                    var form = document.getElementById('productCreateForm');
                    if (form) {
                        form.submit();
                    }
                }, 100);
            }

            // A. Trigger on Enter key inside Barcode input
            $('#barcode').on('keydown keypress', function(e) {
                if (e.which === 13 || e.keyCode === 13 || e.key === 'Enter') {
                    e.preventDefault();
                    setTimeout(prepareAndSubmitProduct, 50);
                }
            });

            // B. Trigger on Enter key in any input inside the product form
            $('#productCreateForm input').not('textarea').on('keydown', function(e) {
                if (e.which === 13 || e.keyCode === 13 || e.key === 'Enter') {
                    e.preventDefault();
                    setTimeout(prepareAndSubmitProduct, 50);
                }
            });

            // C. Global Barcode Scanner Auto-Detection (even if not focused on barcode field)
            var scannerBuffer = '';
            var lastCharTime = 0;
            var scannerTimer = null;

            $(document).on('keydown', function(e) {
                // Ignore if modal or textarea/summernote is open
                if ($('.modal.show').length > 0 || $(e.target).is('textarea, .note-editable')) {
                    return;
                }

                var now = Date.now();

                // Reset buffer if delay between characters > 100ms (manual typing is slower)
                if (now - lastCharTime > 100) {
                    scannerBuffer = '';
                }
                lastCharTime = now;

                if (e.which === 13 || e.keyCode === 13 || e.key === 'Enter') {
                    if (scannerBuffer.length >= 3) {
                        e.preventDefault();
                        $('#barcode').val(scannerBuffer);
                        scannerBuffer = '';
                        setTimeout(prepareAndSubmitProduct, 50);
                    }
                } else if (e.key && e.key.length === 1 && !e.ctrlKey && !e.altKey && !e.metaKey) {
                    scannerBuffer += e.key;

                    // Clear previous timer and set debounce for scanners without Enter key
                    clearTimeout(scannerTimer);
                    if (scannerBuffer.length >= 4) {
                        scannerTimer = setTimeout(function() {
                            if (scannerBuffer.length >= 4) {
                                if (!$('#barcode').val() || $(e.target).is('#barcode')) {
                                    $('#barcode').val(scannerBuffer);
                                }
                                scannerBuffer = '';
                                prepareAndSubmitProduct();
                            }
                        }, 250);
                    }
                }
            });

            // Color Variant Images Dynamic Handler
            function syncColorImagesGrid() {
                var selectedOptions = $('.product_color option:selected');
                var grid = $('#color_images_grid');
                var container = $('#color_images_container');

                if (selectedOptions.length === 0) {
                    container.slideUp(200);
                    grid.empty();
                    return;
                }

                container.slideDown(200);

                // Collect currently existing card IDs to avoid removing cards that already have selected files
                var activeIds = [];
                selectedOptions.each(function() {
                    var colorId = $(this).val();
                    var colorName = $(this).text().trim();
                    if (!colorId) return;
                    activeIds.push(String(colorId));

                    if ($('#color_img_card_' + colorId).length === 0) {
                        var cardHtml = `
                            <div class="col-lg-4 col-md-6 mb-3 color-img-item" id="color_img_card_${colorId}">
                                <div class="p-2.5 border bg-white rounded shadow-sm position-relative" style="border: 1px solid #cbd5e1 !important;">
                                    <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                                        <span class="badge badge-dark px-2 py-1 text-xs text-truncate" style="max-width: 75%; font-size: 11px;">
                                            <i class="feather icon-disc mr-1"></i>${colorName}
                                        </span>
                                        <small class="text-muted font-weight-bold">Color ID: ${colorId}</small>
                                    </div>
                                    <div class="row gx-2">
                                        <!-- Image 1: Main/Front -->
                                        <div class="col-6 pr-1">
                                            <div class="text-center">
                                                <small class="text-muted font-weight-bold d-block mb-1" style="font-size: 10px;">{{ __('Image 1 (Main)') }}</small>
                                                <div class="mb-1 position-relative" style="height: 95px; background: #f8fafc; border-radius: 6px; overflow: hidden; display: flex; align-items: center; justify-content: center; border: 1px dashed #94a3b8;">
                                                    <img id="preview_color_${colorId}_1" src="{{ asset('backend/images/no_images.png') }}" style="max-height: 100%; max-width: 100%; object-fit: cover;">
                                                </div>
                                                <label class="btn btn-outline-primary btn-sm btn-block mb-0 py-0.5 text-xs" style="cursor: pointer; font-size: 10px;">
                                                    <i class="feather icon-upload"></i> {{ __('Upload 1') }}
                                                    <input type="file" name="color_images[${colorId}]" accept="image/*" class="d-none" onchange="previewColorImage(this, '${colorId}', 1)">
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Image 2: Secondary/Back -->
                                        <div class="col-6 pl-1">
                                            <div class="text-center">
                                                <small class="text-muted font-weight-bold d-block mb-1" style="font-size: 10px;">{{ __('Image 2 (Angle/Back)') }}</small>
                                                <div class="mb-1 position-relative" style="height: 95px; background: #f8fafc; border-radius: 6px; overflow: hidden; display: flex; align-items: center; justify-content: center; border: 1px dashed #94a3b8;">
                                                    <img id="preview_color_${colorId}_2" src="{{ asset('backend/images/no_images.png') }}" style="max-height: 100%; max-width: 100%; object-fit: cover;">
                                                </div>
                                                <label class="btn btn-outline-secondary btn-sm btn-block mb-0 py-0.5 text-xs" style="cursor: pointer; font-size: 10px;">
                                                    <i class="feather icon-upload"></i> {{ __('Upload 2') }}
                                                    <input type="file" name="color_images_two[${colorId}]" accept="image/*" class="d-none" onchange="previewColorImage(this, '${colorId}', 2)">
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        grid.append(cardHtml);
                    }
                });

                // Remove cards for deselected colors
                $('.color-img-item').each(function() {
                    var cardColorId = $(this).attr('id').replace('color_img_card_', '');
                    if (activeIds.indexOf(String(cardColorId)) === -1) {
                        $(this).remove();
                    }
                });
            }

            $('.product_color').on('change', syncColorImagesGrid);
            // Run on load in case pre-selected
            setTimeout(syncColorImagesGrid, 100);

            // Size-Wise Custom Pricing Table Dynamic Handler
            function syncSizePricesTable() {
                var selectedOptions = $('.product_size option:selected');
                var tbody = $('#size_prices_tbody');
                var container = $('#size_prices_container');

                if (selectedOptions.length === 0) {
                    container.slideUp(200);
                    tbody.empty();
                    return;
                }

                container.slideDown(200);

                var activeIds = [];
                selectedOptions.each(function() {
                    var sizeId = $(this).val();
                    var sizeName = $(this).text().trim();
                    if (!sizeId) return;
                    activeIds.push(String(sizeId));

                    if ($('#size_price_row_' + sizeId).length === 0) {
                        var rowHtml = `
                            <tr id="size_price_row_${sizeId}" class="size-price-item">
                                <td class="align-middle font-weight-bold">
                                    <span class="badge badge-dark px-2 py-1 text-xs">${sizeName}</span>
                                </td>
                                <td class="align-middle">
                                    <input type="number" step="any" name="size_dis_selling_price[${sizeId}]" class="form-control form-control-sm" placeholder="e.g. 1500.00">
                                </td>
                                <td class="align-middle">
                                    <input type="number" step="any" name="size_selling_price[${sizeId}]" class="form-control form-control-sm border-success font-weight-bold text-success" placeholder="e.g. 1200.00">
                                </td>
                            </tr>
                        `;
                        tbody.append(rowHtml);
                    }
                });

                // Remove rows for deselected sizes
                $('.size-price-item').each(function() {
                    var rowSizeId = $(this).attr('id').replace('size_price_row_', '');
                    if (activeIds.indexOf(String(rowSizeId)) === -1) {
                        $(this).remove();
                    }
                });
            }

            $('.product_size').on('change', syncSizePricesTable);
            setTimeout(syncSizePricesTable, 100);
        });

        function previewColorImage(input, colorId, slot) {
            slot = slot || 1;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var target = document.getElementById('preview_color_' + colorId + '_' + slot) || document.getElementById('preview_color_' + colorId);
                    if (target) {
                        target.src = e.target.result;
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function toggleSizeGuideSettings() {
            var isChecked = document.getElementById('has_size_guide').checked;
            document.getElementById('size_guide_options').style.display = isChecked ? 'block' : 'none';
        }
    </script>
@endpush
