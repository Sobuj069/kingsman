@extends('backend.layouts.master')
@section('section-title', 'Edit Product')
@section('page-title', 'Product Edit')

@if (check_permission('product.update'))
    @section('action-button')
        <a href="{{ route('product.index') }}" class="btn add_list_btn">
            <i class="mr-2 feather icon-list"></i>
            All Product
        </a>
    @endsection
@endif

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="border rounded">
                    <form class="row g-3 needs-validation" method="POST" action="{{ route('product.update', $data->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        {{-- Product name --}}
                        <div class="mt-2 col-md-6">
                            <label for="name" class="form-label fw-bold">Product Name *</label>
                            <input type="text" class="form-control" name="name"
                                placeholder="Enter product name" value="{{ $data->name }}">
                            <div class="errors">{{ ($errors->has('name'))?$errors->first('name'):''; }}</div>
                        </div>

                        {{-- Product barcode  --}}
                        <div class="mt-2 col-md-6">
                            <label for="barcode" class="form-label fw-bold">Product Barcode</label>
                            <input type="text" class="form-control"
                                placeholder="Enter product barcode" name="barcode" value="{{ $data->barcode }}">
                            <div class="errors">{{ ($errors->has('barcode'))?$errors->first('barcode'):''; }}</div>
                        </div>

                        {{-- Category --}}
                        <div class="mt-2 col-md-6">
                            <label for="unit_id" class="form-label fw-bold">Category *</label>
                            <select class="select2" name="category_id">
                                <option selected value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value={{ $category->id }} {{ ($category->id == $data->category_id)?"selected":""; }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="errors">{{ ($errors->has('category_id'))?$errors->first('category_id'):''; }}</div>
                        </div>

                        {{-- Brand --}}
                        <div class="mt-2 col-md-6">
                            <label for="brand_id" class="form-label fw-bold">Brand</label>
                            <select class="select2" name="brand_id">
                                <option selected value="">Select Brand</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ ($brand->id == $data->brand_id)?"selected":""; }}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Price --}}
                        <div class="mt-2 col-md-6">
                            <label for="purchase_price" class="form-label fw-bold">Purchase Price *</label>
                            <input type="number" class="form-control" name="purchase_price" value="{{ $data->purchase_price }}">
                            <div class="errors">{{ ($errors->has('purchase_price'))?$errors->first('purchase_price'):''; }}</div>
                        </div>
                        <div class="mt-2 col-md-6">
                            <label for="selling_price" class="form-label fw-bold">Sale Price *</label>
                            <input type="number" class="form-control" name="selling_price" value="{{ $data->selling_price }}">
                            <div class="errors">{{ ($errors->has('selling_price'))?$errors->first('selling_price'):''; }}</div>
                        </div>

                        {{-- Description --}}
                        <div class="mt-2 col-md-12">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control" name="description" rows="2">{{ $data->description }}</textarea>
                        </div>

                        {{-- Image --}}
                        <div class="mt-2 col-md-6">
                            <label for="image" class="form-label fw-bold">Product
                                Images</label>
                            <input class="form-control image-uploadify" type="file"
                                name="images" accept="image/*" onchange="readURL(this);">
                        </div>

                        <div class="mt-2 col-md-6">
                            <img id="image" src="{{ (!empty($data->images))?url('uploads/products/'.$data->images):url('public/backend/images/no_images.png') }}" width="120px" height="80px" />
                            <input type="hidden" name="old_image" value="{{ $data->images }}">
                        </div>

                        <div class="mt-3 text-center col-12">
                            <button class="btn btn-primary" type="submit"> Save </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function readURL(input) {
        if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#image')
                .attr('src', e.target.result)
                .width(80)
                .height(80);
        };
        reader.readAsDataURL(input.files[0]);
        }
    }
</script>

@endsection
