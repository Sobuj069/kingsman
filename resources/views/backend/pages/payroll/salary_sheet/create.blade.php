@extends('backend.layouts.master')
@section('section-title', __('Salary Sheet'))
@section('page-title', __('Generate Salary Sheet'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card m-b-30 card_style shadow-lg border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 font-weight-bold"><i class="feather icon-settings mr-2"></i> {{ __('Generation Parameters') }}</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('payroll.salary-sheet.generate') }}" method="POST">
                        @csrf
                        <div class="form-group mb-4">
                            <label class="font-weight-bold">{{ __('Select Branch') }} <span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-control select2" required style="width: 100%">
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ auth()->user()->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('Select the branch for which you want to generate salary.') }}</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold">{{ __('Select Year') }} <span class="text-danger">*</span></label>
                                    <select name="year" class="form-control select2" required style="width: 100%">
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold">{{ __('Select Month') }} <span class="text-danger">*</span></label>
                                    <select name="month" class="form-control select2" required style="width: 100%">
                                        @foreach($months as $num => $name)
                                            <option value="{{ $num }}" {{ date('n') == $num ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info border-0 shadow-sm mb-4">
                            <i class="feather icon-info mr-2"></i> {{ __('Generating a salary sheet will create pending salary records for all active employees in the selected branch for the specified month.') }}
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                                <i class="feather icon-zap mr-2"></i> {{ __('Generate Now') }}
                            </button>
                            <a href="{{ route('payroll.salary-sheet.index') }}" class="btn btn-light btn-lg px-4 ml-2">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });
    });
</script>
@endpush
