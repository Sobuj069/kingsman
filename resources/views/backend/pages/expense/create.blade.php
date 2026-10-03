@extends('backend.layouts.master')
@section('section-title', __('Expense'))
@section('page-title', __('Add Expense'))

@if (check_permission('expense.update'))
    @section('action-button')
        <a href="{{ route('expense.index') }}" class="btn add_list_btn">
            <i class="mr-2 feather icon-list"></i>
            {{ __('All Expense') }}
        </a>
    @endsection
@endif

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div class="">
                        <form class="row g-3 needs-validation" method="POST" action="{{ route('expense.store') }}"
                            enctype="multipart/form-data">
                            @csrf

                            @if (auth()->user()->branch_id == 1)
                                <div class="col-md-6 mt-2">
                                    <label for="" class="form-label fw-bold">{{ __('Branch *') }}</label>
                                    <select class="select2" name="branch_id">
                                        @foreach ($allBranch as $branch)
                                            <option value={{ $branch->id }}>{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="errors">
                                        {{ $errors->has('branch_id') ? $errors->first('branch_id') : '' }}</div>

                                </div>
                            @endif
                            {{-- Purchase Date --}}
                            <div class="mt-2 {{ auth()->user()->branch_id == 1 ? 'col-md-6' : 'col-md-12' }}">
                                <label class="form-label font-weight-bold">{{ __('Date *') }}</label>
                                <input type="date" class="form-control" value="{{ date('Y-m-d') }}" name="date"
                                    required>
                                <div class="errors">{{ $errors->has('date') ? $errors->first('date') : '' }}</div>
                            </div>

                            {{-- Category --}}
                            <div class="mt-2 col-md-6">
                                <label for="category_id" class="form-label fw-bold d-flex justify-content-between">
                                    <span>{{ __('Select Category *') }}</span>
                                    <a href="#" data-toggle="modal" data-target="#quickAddCategoryModal" class="text-orange-400 font-weight-bold" style="font-size: 13px;">
                                        <i class="mr-1 feather icon-plus"></i>{{ __('Quick Add') }}
                                    </a>
                                </label>
                                <select class="select2" name="category_id" id="category_select_dropdown">
                                    <option selected value="">{{ __('Select Category') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <div class="errors">{{ $errors->has('category_id') ? $errors->first('category_id') : '' }}</div>
                            </div>

                            {{-- Account --}}
                            <div class="mt-2 col-md-6">
                                <label for="bank_id" class="form-label fw-bold">{{ __('Select Bank Account') }}</label>
                                <select class="select2" name="bank_id">
                                    <option selected value="">{{ __('Select Bank Account') }}</option>
                                    @foreach ($bank_accounts as $bank_account)
                                        <option value={{ $bank_account->id }}>{{ $bank_account->bank_name }}</option>
                                    @endforeach
                                </select>
                                <div class="errors">{{ $errors->has('bank_id') ? $errors->first('bank_id') : '' }}</div>
                            </div>

                            {{-- Amount --}}
                            <div class="mt-2 col-md-6">
                                <label for="amount" class="form-label fw-bold">{{ __('Amount *') }}</label>
                                <input type="number" class="form-control" name="amount" placeholder="{{ __('Enter Amount') }}"
                                    required>
                                <div class="errors">{{ $errors->has('amount') ? $errors->first('amount') : '' }}</div>
                            </div>

                            {{-- Note --}}
                            <div class="mt-2 col-md-6">
                                <label class="form-label fw-bold">{{ __('Note') }}</label>
                                <textarea class="form-control" name="note" rows="2"></textarea>
                            </div>

                            <div class="mt-3 text-center col-12">
                                <button class="btn save_btn" type="submit"> {{ __('Save') }} </button>
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
                reader.onload = function(e) {
                    $('#image')
                        .attr('src', e.target.result)
                        .width(120)
                        .height(80);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Quick add category AJAX handler
        document.addEventListener('DOMContentLoaded', function() {
            $('#btn-submit-quick-category').on('click', function(e) {
                e.preventDefault();
                var name = $('#quick_category_name').val().trim();
                if(!name) {
                    alert('Please enter a category name');
                    return;
                }

                $.ajax({
                    url: "{{ route('expense.category-store') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        name: name
                    },
                    success: function(response) {
                        if(response.success) {
                            // Clear input
                            $('#quick_category_name').val('');
                            // Add option to select dropdown
                            var newOption = new Option(response.category.name, response.category.id, true, true);
                            $('#category_select_dropdown').append(newOption).trigger('change');
                            // Close modal
                            $('#quickAddCategoryModal').modal('hide');
                        }
                    },
                    error: function(xhr) {
                        alert('Error adding category. It might already exist.');
                    }
                });
            });
        });
    </script>

    <!-- Quick Add Category Modal -->
    <div class="modal fade" id="quickAddCategoryModal" tabindex="-1" role="dialog" aria-labelledby="quickAddCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-white" style="background: #1e293b; border: 1px solid rgba(255,255,255,0.1);">
                <div class="modal-header border-b border-slate-700/50">
                    <h5 class="modal-title text-white font-weight-bold" id="quickAddCategoryModalLabel">{{ __('Quick Add Expense Category') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 text-left">
                        <label class="form-label font-weight-bold text-white">{{ __('Category Name *') }}</label>
                        <input type="text" class="form-control text-white" style="background: #0f172a; border: 1px solid #334155;" id="quick_category_name" placeholder="{{ __('Enter Category Name') }}" required>
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-700/50">
                    <button type="button" class="btn btn-secondary text-white" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn save_btn" id="btn-submit-quick-category">{{ __('Save') }}</button>
                </div>
            </div>
        </div>
    </div>

@endsection
