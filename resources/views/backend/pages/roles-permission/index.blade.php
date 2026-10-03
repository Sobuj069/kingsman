@extends('backend.layouts.master')
@section('section-title', __('Role Management'))
@section('page-title', __('Role & Permission List'))
@if (check_permission('roles-permission.create'))
    @section('action-button')
        <a class="btn add_list_btn" href="{{ route('roles-permission.create') }}">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Role & Permission') }}
        </a>
    @endsection
@endif
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table text-center">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('ID') }}</th>
                                    <th>{{ __('Role Name') }}</th>
                                    <th>{{ __('Total User') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="header_style_right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>{{ $role->users->count() }}</td>
                                        <td>
                                            <input type="checkbox" data-toggle="toggle" data-on="{{ __('Active') }}"
                                                class="status-update" {{ $role->status == 1 ? 'checked' : '' }}
                                                data-off="{{ __('Inactive') }}" data-onstyle="success" data-offstyle="danger"
                                                data-id="{{ $role->id }}" data-model="Role">
                                        </td>
                                        <td class="table_data_style_right">
                                            <a href="{{ route('roles-permission.edit', $role->id) }}"
                                                class="btn btn-primary-rgba">
                                                <i class="feather icon-edit"></i>
                                            </a>
                                        </td>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
