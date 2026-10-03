@extends('backend.layouts.master')
@section('section-title', __('User Management'))
@section('page-title', __('Add User'))
@section('action-button')
    <a href="{{ route('user.index') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-list"></i>
        {{ __('User List') }}
    </a>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <form class="needs-validation" action="{{ route('user.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            {{-- Name --}}
                            <div class="mb-3 col-md-12">
                                <label for="validationCustom01" class="form-label font-weight-bold">
                                    {{ __('Name *') }}
                                </label>
                                <input type="text" class="form-control" id="validationCustom01"
                                    placeholder="{{ __('Enter User Name') }}" name="name" required>
                            </div>
                            {{-- Email --}}
                            <div class="mb-3 col-md-6">
                                <label for="validationCustom02" class="form-label font-weight-bold">
                                    {{ __('Email *') }}
                                </label>
                                <input type="email" class="form-control" id="validationCustom02"
                                    placeholder="{{ __('Enter User Email') }}" name="email" required>
                            </div>
                            {{-- Phone --}}
                            <div class="mb-3 col-md-6">
                                <label for="validationCustom03" class="form-label font-weight-bold">
                                    {{ __('Phone *') }}
                                </label>
                                <input type="text" class="form-control" id="validationCustom03"
                                    placeholder="{{ __('Enter User Phone') }}" name="phone" required>
                            </div>
                            {{-- Password --}}
                            <div class="mb-3 col-md-6">
                                <label for="validationCustom04" class="form-label font-weight-bold">
                                    {{ __('Password *') }}
                                </label>
                                <input type="password" class="form-control" id="validationCustom04"
                                    placeholder="{{ __('Enter User Password') }}" name="password" required>
                            </div>
                            {{-- Confirm Password --}}
                            <div class="mb-3 col-md-6">
                                <label for="validationCustom05" class="form-label font-weight-bold">
                                    {{ __('Confirm Password *') }}
                                </label>
                                <input type="password" class="form-control" id="validationCustom05"
                                    placeholder="{{ __('Enter User Confirm Password') }}" name="password_confirmation" required>
                            </div>
                            {{-- User Role --}}
                            <div class="mb-3 col-md-6">
                                <label for="validationCustom06" class="form-label font-weight-bold">
                                    {{ __('User Role *') }}
                                </label>
                                <select class="select2" id="validationCustom06" name="role_id" required>
                                    <option value="">{{ __('Select Role') }}</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- User Status --}}
                            <div class="mb-3 col-md-6">
                                <label for="validationCustom07" class="form-label font-weight-bold">
                                    {{ __('User Status *') }}
                                </label>
                                <select class="select2" id="validationCustom07" name="status" required>
                                    <option value="">{{ __('Select Status') }}</option>
                                    <option value="1">{{ __('Active') }}</option>
                                    <option value="0">{{ __('Inactive') }}</option>
                                </select>
                            </div>
                            {{-- User branch --}}
                            @php
                                $userRoleName = strtolower(auth()->user()->role->name ?? '');
                                $isAdminOrSuperAdmin = in_array($userRoleName, ['admin', 'super admin', 'superadmin', 'master admin']);
                            @endphp
                            @if($isAdminOrSuperAdmin)
                            <div class="mb-3 col-md-6">
                                <label for="validationCustom08" class="form-label font-weight-bold">
                                    {{ __('Branch *') }}
                                </label>
                                <select class="select2" id="validationCustom08" name="branch_id" required>
                                    <option value="">{{ __('Select Branch') }}</option>
                                    @foreach ($branchs as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @else
                            <div class="mb-3 col-md-6">
                                <label class="form-label font-weight-bold">{{ __('Branch *') }}</label>
                                <input type="text" class="form-control" value="{{ auth()->user()->branch->name ?? '' }}" readonly>
                                <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                            </div>
                            @endif
                            {{-- Address --}}
                            <div class="mb-3 col-md-6">
                                <label for="validationCustom09" class="form-label font-weight-bold">
                                    {{ __('Address *') }}
                                </label>
                                <textarea class="form-control" id="validationCustom09" rows="3" placeholder="{{ __('Enter User Address') }}" name="address"
                                    required></textarea>
                            </div>
                            {{-- Image --}}
                            <div class="col-md-6 mb-3">
                                <label for="image_input" class="form-label font-weight-bold">
                                    {{ __('Profile Image') }}
                                </label>
                                <input class="form-control" id="image_input" type="file" name="image"
                                    onchange="document.getElementById('showImage').src = window.URL.createObjectURL(this.files[0]); document.getElementById('showImage').style.display='block';">
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-center">
                                <img id="showImage" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover; display: none; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);"
                                    src="" alt="Preview" />
                            </div>
                        </div>
                        <hr>
                        <div class="mt-4 form-group d-flex justify-content-center">
                            <button class="btn save_btn" type="submit">{{ __('Add User') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
