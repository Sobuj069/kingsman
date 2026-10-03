@extends('backend.layouts.master')
@section('section-title', 'Expense')
@section('page-title', 'Expense Edit')

@if (check_permission('expense.update'))
    @section('action-button')
        <a href="{{ route('expense.index') }}" class="btn btn-primary-rgba">
            <i class="mr-2 feather icon-list"></i>
            All Expense
        </a>
    @endsection
@endif

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="border rounded">
                    <form class="row g-3 needs-validation" method="POST" action="{{ route('expense.update', $data->id) }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Purchase Date --}}
                        <div class="mt-2 col-md-4">
                            <label class="form-label font-weight-bold">Date *</label>
                            <input type="date" class="form-control" value="{{ $data->date }}" name="date" required>
                            <div class="errors">{{ ($errors->has('date'))?$errors->first('date'):''; }}</div>
                        </div>

                        {{-- Expense name --}}
                        <div class="mt-2 col-md-4">
                            <label for="name" class="form-label fw-bold">Name *</label>
                            <input type="text" class="form-control" name="name" placeholder="Enter Expense Name" value="{{ $data->name }}">
                            <div class="errors">{{ ($errors->has('name'))?$errors->first('name'):''; }}</div>
                        </div>

                        {{-- Category --}}
                        <div class="mt-2 col-md-4">
                            <label for="category_id" class="form-label fw-bold">Select Category *</label>
                            <select class="select2" name="category_id">
                                <option selected value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ ($category->id == $data->category_id)?"selected":""; }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="errors">{{ ($errors->has('category_id'))?$errors->first('category_id'):''; }}</div>
                        </div>

                        {{-- Account --}}
                        <div class="mt-2 col-md-6">
                            <label for="bank_id" class="form-label fw-bold">Select Bank Account</label>
                            <select class="select2" name="bank_id">
                                <option selected value="">Select Bank Account</option>
                                @foreach ($bank_accounts as $bank_account)
                                    <option value="{{ $bank_account->id }}" {{ ($bank_account->id == $data->bank_id)?"selected":""; }}>{{ $bank_account->bank_name }}</option>
                                @endforeach
                            </select>
                            <div class="errors">{{ ($errors->has('bank_id'))?$errors->first('bank_id'):''; }}</div>
                        </div>

                        {{-- Amount --}}
                        <div class="mt-2 col-md-6">
                            <label for="amount" class="form-label fw-bold">Amount *</label>
                            <input type="number" class="form-control" name="amount" placeholder="Enter Amount" value="{{ $data->amount }}" required>
                            <div class="errors">{{ ($errors->has('amount'))?$errors->first('amount'):''; }}</div>
                        </div>

                        {{-- Note --}}
                        <div class="mt-2 col-md-12">
                            <label class="form-label fw-bold">Note</label>
                            <textarea class="form-control" name="note" rows="2">{{ $data->note }}</textarea>
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
