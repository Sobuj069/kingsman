@extends('backend.layouts.master')
@section('section-title', __('Service'))
@section('page-title', __('Add Service'))

@if (check_permission('service.index'))
    @section('action-button')
        <a href="{{ route('service.index') }}" class="btn add_list_btn">
            <i class="mr-2 feather icon-list"></i>
            {{ __('All Service') }}
        </a>
    @endsection
@endif

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div class="border rounded">
                        <form class="row g-3 needs-validation" method="POST" action="{{ route('service.store') }}"
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

                            <div class="mt-2 col-md-6">
                                <label for="selling_price" class="form-label fw-bold">{{ __('Price *') }}</label>
                                <input type="number" step="any" class="form-control" name="selling_price" placeholder="{{ __('Enter Selling Price') }}">
                                <div class="errors">
                                    {{ $errors->has('selling_price') ? $errors->first('selling_price') : '' }}</div>
                            </div>

                            <div class="mt-2 col-md-6">
                                <label for="purchase_price" class="form-label fw-bold">{{ __('Cost Price *') }}</label>
                                <input type="number" step="any" class="form-control" name="purchase_price" placeholder="{{ __('Enter Cost Price') }}">
                                <div class="errors">
                                    {{ $errors->has('purchase_price') ? $errors->first('purchase_price') : '' }}</div>
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
                            <div class="mt-2 col-md-12  ">
                                <label for="description" class="form-label fw-bold">{{ __('Description') }}</label>
                                <textarea class="form-control" name="description" id="description" rows="5"></textarea>
                            </div>
                            @if (auth()->user()->branch_id == 1)
                                <div class="mt-2 col-md-12">
                                    <label class="form-label fw-bold">{{ __('Branch *') }}</label>

                                    {{-- All Select Checkbox --}}
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="select_all_branches"
                                            {{ (!is_array(old('branch_id')) && !$errors->any()) || (is_array(old('branch_id')) && count(old('branch_id')) == count($allBranch)) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="select_all_branches">{{ __('Select All') }}</label>
                                    </div>

                                    {{-- Branch Checkboxes --}}
                                    <div id="branch_checkbox_list">
                                        @foreach ($allBranch as $branch)
                                            <div class="form-check">
                                                <input class="form-check-input branch-checkbox" type="checkbox"
                                                    name="branch_id[]" value="{{ $branch->id }}"
                                                    id="branch_{{ $branch->id }}"
                                                    {{ (!is_array(old('branch_id')) && !$errors->any()) || (is_array(old('branch_id')) && in_array($branch->id, old('branch_id'))) ? 'checked' : '' }}>
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
                                <button class="btn save_btn" type="submit"> {{ __('Save') }} </button>
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

{{-- JavaScript --}}
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

    $(document).ready(function() {
        if ($.fn.summernote) {
            $('#description').summernote({
                height: 180,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'table']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        }
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
