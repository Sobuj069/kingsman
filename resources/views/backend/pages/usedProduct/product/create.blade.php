@extends('backend.layouts.master')
@section('section-title', __('Used Product'))
@section('page-title', __('Add Product'))

@if (check_permission('product.update'))
    @section('action-button')
        <a href="{{ route('usedProduct.index') }}" class="btn add_list_btn">
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
                    <div class="border rounded">
                        <form class="row g-3 needs-validation" method="POST" action="{{ route('usedProduct.store') }}"
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
                                <label for="barcode" class="form-label fw-bold">{{ __('Barcode') }}</label>
                                <input type="text" class="form-control" placeholder="{{ __('Enter Barcode') }}" name="barcode">
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
                                    <div class="col-2 pl-2">
                                        <button data-toggle="modal" type="button" data-target="#addModal1"
                                            class="btn extra_btn shadow-sm">
                                            <i class="feather icon-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

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
                                    <div class="col-2 pl-2">
                                        <button data-toggle="modal" type="button" data-target="#addModal"
                                            class="btn extra_btn shadow-sm">
                                            <i class="feather icon-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Unit --}}
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

                            {{-- Unit --}}
                            {{-- <div class="mt-2 col-md-6 ">
                                <div class="form-group">
                                    <label for="">Low Stock Quantity</label>
                                    <div class="opening_stocks form-row" style="padding-left: 5px; padding-right:5px;">
                                        <input type="text" name="main_qty" value="" class="form-control col"
                                            placeholder="Pices">
                                    </div>
                                </div>
                            </div> --}}

                            {{-- Price --}}
                            <div class="mt-2 col-md-6 ">
                                <label for="purchase_price" class="form-label fw-bold">{{ __('Purchase Price *') }}</label>
                                <input type="number" step="any" class="form-control" name="purchase_price">
                                <div class="errors">
                                    {{ $errors->has('purchase_price') ? $errors->first('purchase_price') : '' }}</div>
                            </div>
                            {{-- <div class="mt-2 col-md-6">
                                <label for="selling_price" class="form-label fw-bold">Sale Price *</label>
                                <input type="number" class="form-control" name="selling_price">
                                <div class="errors">
                                    {{ $errors->has('selling_price') ? $errors->first('selling_price') : '' }}</div>
                            </div> --}}

                            <div class="col-md-6 mt-2" style="margin-right: -6px">
                                <label for="status" class="form-label fw-bold">{{ __('Status *') }}</label>
                                <select class="select2" name="status">
                                    <option value="1">{{ __('Active') }}</option>
                                    <option value="0">{{ __('Deactive') }}</option>
                                </select>
                                <div class="errors">{{ $errors->has('status') ? $errors->first('status') : '' }}</div>
                            </div>
                            <div class="mt-2 col-md-6  ">
                                <label for="description" class="form-label fw-bold">{{ __('Description') }}</label>
                                <textarea class="form-control" name="description" rows="2"></textarea>
                            </div>

                            {{-- Image --}}
                            {{-- <div class="mt-2 col-md-6 ">
                                <label for="image" class="form-label fw-bold">Product
                                    Images</label>
                                <input class="form-control image-uploadify" type="file" name="images"
                                    accept="image/*" onchange="readURL(this);">
                            </div>

                            <div class="mt-2 col-md-6 " >
                                <img id="image" src="{{ url('backend/images/no_images.png') }}" width="120px"
                                    height="80px" />
                            </div> --}}

                            <div class="mt-3 text-center col-12">
                                <a href="{{ route('usedProduct.index') }}" class="btn" style="padding: 5px 20px; background: #f04438;color:white; border-radius:5px">{{ __('Cancel') }}</a>
                                <button class="btn save_btn" type="submit" style="padding: 5px 20px; background: #12b76a;color:white; border-radius:5px;margin-left: 5px"> {{ __('Save') }} </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Add Category Modal --}}
    <form action="#" id="categoryForm" method="POST">
        @csrf
        <x-another-modal title="{{ __('Add Category') }}" sizeClass="modal-md">
            <x-input label="{{ __('Category Name *') }}" type="text" name="name" placeholder="{{ __('Enter Category Name') }}" required />
        </x-another-modal>
    </form>

    {{-- Add Brand Modal --}}
    <form action="#" id="brandForm" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Brand') }}" sizeClass="modal-md">
            <x-input label="{{ __('Brand Name:') }}" type="text" name="name" placeholder="{{ __('Enter Brand Name') }}" required />
        </x-add-modal>
    </form>



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
                        // Reset and reload the dropdown
                        $('#categoryId').load(location.href + ' #categoryId>*', function() {
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
                        // Reset and reload the dropdown
                        $('#brandId').load(location.href + ' #brandId>*', function() {
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
            $('.sub_unit').html('<option value="">No Related Unit Found</option>');
            var main_unit_id = $(this).find(':selected').val();
            var main_unit_text = $(this).find(':selected').text();

            let url = "{{ route('product-unit', 'my_id') }}".replace('my_id', main_unit_id);

            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    if (data) {
                        var sub_value = '<option value="">Select Unit</option><option value="' + data
                            .related_unit_id + '">' + data.related_unit.name + '</option>';
                        $('.sub_unit').html(sub_value);

                        // Opening Stock
                        var opening_stock = "";
                        opening_stock +=
                            `<input type="text" name="main_qty" value="" class="form-control col" placeholder="${main_unit_text}">`;
                        $('.opening_stocks').html(opening_stock);
                    } else {
                        $('.sub_unit').html('<option value="" selected>No Related Unit Found</option>');
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
@endpush
