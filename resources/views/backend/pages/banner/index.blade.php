@extends('backend.layouts.master')
@section('section-title', __('Banner Management'))
@section('page-title', __('Banners List'))

@section('action-button')
    @if (check_permission('banner.index') || auth()->user()->isSuperAdmin())
        <a href="#" data-toggle="modal" data-target="#addBannerModal" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add New Banner') }}
        </a>
    @endif
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <!-- Position Filter Pills / Tabs -->
            <div class="card m-b-20 card_style">
                <div class="card-body p-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('banner.index') }}" 
                               class="btn btn-sm {{ !request()->filled('position') ? 'btn-primary font-weight-bold' : 'btn-outline-secondary' }}">
                                {{ __('All Banners') }} ({{ $positionCounts['all'] ?? 0 }})
                            </a>
                            <a href="{{ route('banner.index', ['position' => 'hero']) }}" 
                               class="btn btn-sm {{ request('position') === 'hero' ? 'btn-primary font-weight-bold' : 'btn-outline-secondary' }}">
                                <i class="feather icon-image mr-1"></i> {{ __('Hero Slider (Top)') }} ({{ $positionCounts['hero'] ?? 0 }})
                            </a>
                            <a href="{{ route('banner.index', ['position' => 'promo_dual']) }}" 
                               class="btn btn-sm {{ request('position') === 'promo_dual' ? 'btn-primary font-weight-bold' : 'btn-outline-secondary' }}">
                                <i class="feather icon-grid mr-1"></i> {{ __('Dual Promo (Mid 1)') }} ({{ $positionCounts['promo_dual'] ?? 0 }})
                            </a>
                            <a href="{{ route('banner.index', ['position' => 'promo_festive']) }}" 
                               class="btn btn-sm {{ request('position') === 'promo_festive' ? 'btn-primary font-weight-bold' : 'btn-outline-secondary' }}">
                                <i class="feather icon-layout mr-1"></i> {{ __('Festive 3-Grid (Mid 2)') }} ({{ $positionCounts['promo_festive'] ?? 0 }})
                            </a>
                            <a href="{{ route('banner.index', ['position' => 'category']) }}" 
                               class="btn btn-sm {{ request('position') === 'category' ? 'btn-primary font-weight-bold' : 'btn-outline-secondary' }}">
                                <i class="feather icon-tag mr-1"></i> {{ __('Category Banners') }} ({{ $positionCounts['category'] ?? 0 }})
                            </a>
                        </div>

                        <!-- Search Box -->
                        <form action="{{ route('banner.index') }}" method="GET" class="form-inline mt-2 mt-md-0">
                            @if(request()->filled('position'))
                                <input type="hidden" name="position" value="{{ request('position') }}">
                            @endif
                            <div class="input-group">
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="{{ __('Search banners...') }}" value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-sm btn-primary" type="submit">
                                        <i class="feather icon-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Banners Table Card -->
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped text-center align-middle">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left" style="width: 50px;">{{ __('#') }}</th>
                                    <th style="width: 140px;">{{ __('Preview') }}</th>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Position / Section') }}</th>
                                    <th>{{ __('Target Link') }}</th>
                                    <th style="width: 80px;">{{ __('Order') }}</th>
                                    <th style="width: 100px;">{{ __('Status') }}</th>
                                    <th class="header_style_right" style="width: 100px;">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($banners as $banner)
                                    <tr>
                                        <td class="table_data_style_left font-weight-bold">{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ $banner->image_url }}" target="_blank" class="d-inline-block">
                                                <img src="{{ $banner->image_url }}" 
                                                     alt="{{ $banner->title }}" 
                                                     class="rounded shadow-sm border" 
                                                     style="max-width: 120px; max-height: 60px; object-fit: cover;">
                                            </a>
                                        </td>
                                        <td class="text-left font-weight-bold">
                                            <span>{{ $banner->title }}</span>
                                        </td>
                                        <td>
                                            @if($banner->position === 'hero')
                                                <span class="badge badge-primary px-2 py-1 font-weight-bold">
                                                    <i class="feather icon-image mr-1"></i> Hero Slider (Top)
                                                </span>
                                            @elseif($banner->position === 'promo_dual')
                                                <span class="badge badge-success px-2 py-1 font-weight-bold">
                                                    <i class="feather icon-grid mr-1"></i> Dual Promo (Mid 1)
                                                </span>
                                            @elseif($banner->position === 'promo_festive')
                                                <span class="badge badge-info px-2 py-1 font-weight-bold">
                                                    <i class="feather icon-layout mr-1"></i> Festive 3-Grid (Mid 2)
                                                </span>
                                            @elseif($banner->position === 'category')
                                                <span class="badge badge-warning px-2 py-1 font-weight-bold text-white">
                                                    <i class="feather icon-tag mr-1"></i> Category Banner
                                                </span>
                                            @else
                                                <span class="badge badge-secondary px-2 py-1 font-weight-bold">
                                                    {{ ucfirst($banner->position) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-left text-muted small">
                                            @if($banner->link)
                                                <a href="{{ $banner->link }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 220px;" title="{{ $banner->link }}">
                                                    <i class="feather icon-link mr-1"></i> {{ $banner->link }}
                                                </a>
                                            @else
                                                <span class="text-muted italic">None</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-light border font-weight-bold px-2">{{ $banner->order }}</span>
                                        </td>
                                        <td>
                                            <form action="{{ route('banner.status', $banner->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $banner->status == 1 ? 'btn-success' : 'btn-secondary' }} py-0 px-2" style="font-size: 11px; border-radius: 12px;" title="{{ __('Click to toggle status') }}">
                                                    {{ $banner->status == 1 ? __('Active') : __('Inactive') }}
                                                </button>
                                            </form>
                                        </td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton-{{ $banner->id }}" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton-{{ $banner->id }}">
                                                    <a href="#" data-toggle="modal" data-target="#editModal-{{ $banner->id }}" class="dropdown-item text-primary">
                                                        <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                    </a>
                                                    <a href="#" data-toggle="modal" data-target="#deleteModal-{{ $banner->id }}" class="dropdown-item text-danger">
                                                        <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center no_data_style text-danger py-4">
                                            {{ __('No Banners Found. Click "Add New Banner" to create one.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Modals Outside Table -->
                    @foreach ($banners as $banner)
                        <!-- Edit Modal -->
                        <form action="{{ route('banner.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <x-edit-modal title="{{ __('Edit Banner') }} - {{ $banner->title }}" id="{{ $banner->id }}">
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Banner Title *') }}</label>
                                    <input type="text" class="form-control" name="title" required value="{{ $banner->title }}" placeholder="e.g. Eid Royal Arrival">
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Position / Section *') }}</label>
                                    <select class="form-control" name="position" required>
                                        <option value="hero" {{ $banner->position === 'hero' ? 'selected' : '' }}>{{ __('Hero Slider (Top Carousel)') }}</option>
                                        <option value="promo_dual" {{ $banner->position === 'promo_dual' ? 'selected' : '' }}>{{ __('Dual Promo (Mid-Page 2 Cards)') }}</option>
                                        <option value="promo_festive" {{ $banner->position === 'promo_festive' ? 'selected' : '' }}>{{ __('Festive 3-Grid Showcase (Mid-Page 3 Cards)') }}</option>
                                        <option value="category" {{ $banner->position === 'category' ? 'selected' : '' }}>{{ __('Category Page Banner') }}</option>
                                        <option value="general" {{ $banner->position === 'general' ? 'selected' : '' }}>{{ __('General Banner') }}</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Banner Image') }}</label>
                                    <div class="mb-2">
                                        <img src="{{ $banner->image_url }}" alt="Preview" class="rounded border shadow-sm" style="max-height: 90px; max-width: 100%; object-fit: cover;">
                                    </div>
                                    <input type="file" class="form-control-file border p-1 rounded" name="image" accept="image/*">
                                    <small class="text-muted d-block mt-1">{{ __('Leave blank to keep existing image. Supported: JPG, PNG, WEBP (Max 5MB)') }}</small>
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Destination Link / URL') }}</label>
                                    <input type="text" class="form-control" name="link" value="{{ $banner->link }}" placeholder="e.g. /category/kabli-set or https://...">
                                    <small class="text-muted">{{ __('When clicked on frontend, users will navigate to this link.') }}</small>
                                </div>
                                <div class="row px-3">
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Sort Order') }}</label>
                                        <input type="number" class="form-control" name="order" value="{{ $banner->order }}" min="0">
                                    </div>
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Status') }}</label>
                                        <select class="form-control" name="status">
                                            <option value="1" {{ $banner->status == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                                            <option value="0" {{ $banner->status == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </x-edit-modal>
                        </form>

                        <!-- Delete Modal -->
                        <form action="{{ route('banner.destroy', $banner->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-delete-modal title="{{ __('Banner') }}" id="{{ $banner->id }}" />
                        </form>
                    @endforeach

                    <div class="pagination justify-content-center mt-3">
                        {{ $banners->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Banner Modal -->
    <form action="{{ route('banner.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <x-add-modal title="{{ __('Add New Banner') }}" id="addBannerModal">
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Banner Title *') }}</label>
                <input type="text" class="form-control" name="title" required placeholder="e.g. Eid Festive Collection 2026">
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Position / Section *') }}</label>
                <select class="form-control" name="position" required>
                    <option value="hero" {{ request('position') === 'hero' ? 'selected' : '' }}>{{ __('Hero Slider (Top Carousel)') }}</option>
                    <option value="promo_dual" {{ request('position') === 'promo_dual' ? 'selected' : '' }}>{{ __('Dual Promo (Mid-Page 2 Cards)') }}</option>
                    <option value="promo_festive" {{ request('position') === 'promo_festive' ? 'selected' : '' }}>{{ __('Festive 3-Grid Showcase (Mid-Page 3 Cards)') }}</option>
                    <option value="category" {{ request('position') === 'category' ? 'selected' : '' }}>{{ __('Category Page Banner') }}</option>
                    <option value="general">{{ __('General Banner') }}</option>
                </select>
                <small class="text-muted d-block mt-1">
                    <span class="font-weight-bold">Guide:</span> 
                    <strong>Hero Slider:</strong> 1920x650px wide. 
                    <strong>Dual Promo:</strong> 600x400px. 
                    <strong>Festive 3-Grid:</strong> 8-col wide left + 2 stacked right.
                </small>
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Banner Image *') }}</label>
                <input type="file" class="form-control-file border p-1 rounded" name="image" accept="image/*" required>
                <small class="text-muted d-block mt-1">{{ __('Supported formats: JPG, PNG, WEBP, GIF, SVG (Max 5MB)') }}</small>
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Destination Link / URL') }}</label>
                <input type="text" class="form-control" name="link" placeholder="e.g. /category/kabli-set or https://...">
                <small class="text-muted">{{ __('Optional link when the banner is clicked by customer.') }}</small>
            </div>
            <div class="row px-3">
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Sort Order') }}</label>
                    <input type="number" class="form-control" name="order" value="0" min="0">
                </div>
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Status') }}</label>
                    <select class="form-control" name="status">
                        <option value="1" selected>{{ __('Active') }}</option>
                        <option value="0">{{ __('Inactive') }}</option>
                    </select>
                </div>
            </div>
        </x-add-modal>
    </form>
@endsection
