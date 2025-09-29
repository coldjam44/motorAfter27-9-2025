@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ __('messages.add_new_auction') }}</h4>
                    <a href="{{ route('admin.home') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left me-1"></i> {{ __('messages.back') }}
                    </a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ti ti-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="ti ti-alert-circle me-2"></i>
                            <strong>{{ __('validation.please_fix_errors') }}</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="ti ti-alert-circle me-2"></i>
                            <strong>{{ __('messages.error') }}:</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.auctions.store') }}" method="POST" enctype="multipart/form-data" id="auctionForm" class="needs-validation" novalidate>
                        @csrf
                        
                        <!-- API Configuration Aside -->
                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#apiTokenSection" aria-expanded="false">
                                <i class="ti ti-settings me-1"></i>{{ __('messages.api_configuration') }}
                            </button>
                        </div>
                        
                        <div class="collapse mb-4" id="apiTokenSection">
                            <div class="card border-secondary">
                                <div class="card-body">
                                    <h6 class="card-title text-secondary">{{ __('messages.api_configuration') }}</h6>
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('messages.api_token') }}</label>
                                        <input type="text" class="form-control form-control-sm" name="api_token" value="{{ old('api_token') }}"
                                               placeholder="{{ __('messages.api_token_placeholder') }}">
                                        <small class="text-muted">{{ __('messages.api_token_help') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Basic Information Section -->
                            <div class="col-12">
                                <div class="section-header mb-4">
                                    <h5 class="text-primary mb-1">
                                        <i class="ti ti-info-circle me-2"></i>{{ __('messages.basic_information') }}
                                    </h5>
                                    <small class="text-muted">{{ __('messages.enter_basic_details') }}</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">{{ __('validation.attributes.title') }}</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           name="title" value="{{ old('title') }}"
                                           placeholder="{{ __('messages.title_placeholder') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label required">{{ __('validation.attributes.category') }}</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    name="category_id" required>
                                <option value="">{{ __('messages.choose_category') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ app()->getLocale() == 'ar' ? $category->name_ar : $category->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">{{ __('messages.car_model') }}</label>
                                    <select class="form-select @error('car_model') is-invalid @enderror" 
                                            name="car_model" id="car_model" required>
                                        <option value="">{{ __('messages.choose_car_model') }}</option>
                                        @foreach($carModels as $model)
                                            <option value="{{ $model->id }}" {{ old('car_model') == $model->id ? 'selected' : '' }}>
                                                {{ app()->getLocale() == 'ar' ? $model->value_ar : $model->value_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('car_model')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">{{ __('messages.phone_number') }}</label>
                                    <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" 
                                           name="phone_number" value="{{ old('phone_number') }}" 
                                           placeholder="{{ __('messages.phone_placeholder') }}" required>
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-4">
                                    <label class="form-label required">{{ __('messages.description') }}</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              name="description" rows="4" 
                                              placeholder="{{ __('messages.description_placeholder') }}" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Location Section -->
                            <div class="col-12">
                                <div class="section-header mb-4">
                                    <h5 class="text-primary mb-1">
                                        <i class="ti ti-map-pin me-2"></i>{{ __('messages.location_information') }}
                                    </h5>
                                    <small class="text-muted">{{ __('messages.location_info_desc') }}</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required">{{ __('validation.attributes.country') }}</label>
                                    <select class="form-select @error('country_id') is-invalid @enderror" 
                                            name="country_id" id="country_id" required onchange="this.form.submit()">
                                        <option value="">{{ __('messages.choose_country') }}</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                                {{ app()->getLocale() == 'ar' ? $country->name_ar : $country->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('country_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required">{{ __('validation.attributes.city') }}</label>
                                    <select class="form-select @error('city_id') is-invalid @enderror" 
                                            name="city_id" id="city_id" required>
                                        <option value="">{{ __('messages.choose_city') }}</option>
                                        @if(old('country_id'))
                                            @php
                                                $cities = \App\Models\city::where('country_id', old('country_id'))->get();
                                            @endphp
                                            @foreach($cities as $city)
                                                <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                                    @if(app()->getLocale() == 'ar')
                                                        {{ $city->name_ar ?? $city->name_en }}
                                                    @else
                                                        {{ $city->name_en ?? $city->name_ar }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('city_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">{{ __('messages.select_country_first_to_load_cities') }}</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('messages.kilometers') }}</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('kilometer') is-invalid @enderror" 
                                               name="kilometer" value="{{ old('kilometer') }}" 
                                               placeholder="{{ __('messages.kilometers_placeholder') }}" min="0">
                                        <span class="input-group-text">{{ __('messages.km') }}</span>
                                    </div>
                                    @error('kilometer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-4">
                                    <label class="form-label required">{{ __('messages.address') }}</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                           name="address" value="{{ old('address') }}" 
                                           placeholder="{{ __('messages.address_placeholder') }}" required>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Pricing Section -->
                            <div class="col-12">
                                <div class="section-header mb-4">
                                    <h5 class="text-primary mb-1">
                                        <i class="ti ti-currency-dollar me-2"></i>{{ __('messages.pricing_auction_settings') }}
                                    </h5>
                                    <small class="text-muted">{{ __('messages.pricing_info_desc') }}</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required">{{ __('messages.basic_price') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                               name="price" id="basic_price" value="{{ old('price') }}" 
                                               placeholder="{{ __('messages.price_placeholder') }}" required min="1" step="0.01">
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required">{{ __('messages.starting_price') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control @error('starting_price') is-invalid @enderror" 
                                               name="starting_price" id="starting_price" value="{{ old('starting_price') }}" 
                                               placeholder="{{ __('messages.starting_price_placeholder') }}" required min="1" step="0.01">
                                    </div>
                                    @error('starting_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required">{{ __('messages.bid_increment') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control @error('bid_increment') is-invalid @enderror" 
                                               name="bid_increment" value="{{ old('bid_increment', 50) }}" 
                                               placeholder="{{ __('messages.bid_increment_placeholder') }}" required min="1" step="0.01">
                                    </div>
                                    @error('bid_increment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">{{ __('messages.start_time') }}</label>
                                    <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" 
                                           name="start_time" id="start_time" value="{{ old('start_time') }}" required>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">{{ __('messages.end_time') }}</label>
                                    <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" 
                                           name="end_time" id="end_time" value="{{ old('end_time') }}" required>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Images Section -->
                            <div class="col-12">
                                <div class="section-header mb-4">
                                    <h5 class="text-primary mb-1">
                                        <i class="ti ti-photo me-2"></i>{{ __('messages.images_section') }}
                                    </h5>
                                    <small class="text-muted">{{ __('messages.images_info_desc') }}</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label required">{{ __('messages.main_image') }}</label>
                                    <input type="file" class="form-control @error('main_image') is-invalid @enderror" 
                                           name="main_image" accept="image/*" required>
                                    @error('main_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">{{ __('messages.supported_formats') }}</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label">{{ __('messages.sub_images') }} ({{ __('messages.optional') }})</label>
                                    <input type="file" class="form-control @error('sub_images.*') is-invalid @enderror" 
                                           name="sub_images[]" accept="image/*" multiple>
                                    @error('sub_images.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">{{ __('messages.max_images_hint') }}</small>
                                </div>
                            </div>

                            <!-- Car Specifications Section -->
                            <div class="col-12">
                                <div class="section-header mb-4">
                                    <h5 class="text-primary mb-1">
                                        <i class="ti ti-settings me-2"></i>{{ __('messages.car_specifications') }}
                                    </h5>
                                    <small class="text-muted">{{ __('messages.specs_info_desc') }}</small>
                                </div>
                            </div>

                            @if(!empty($categoryFields) && count($categoryFields) > 0)
                                @foreach($categoryFields as $index => $field)
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                {{ app()->getLocale() == 'ar' ? $field->field_ar : $field->field_en }}
                                                @if($field->required ?? false) <span class="text-danger">*</span> @endif
                                            </label>
                                            <select class="form-select @error("fields.{$index}.category_field_value_id") is-invalid @enderror" 
                                                    name="fields[{{ $index }}][category_field_value_id]">
                                                <option value="">{{ __('messages.choose_option') }}</option>
                                                @if(isset($field->values))
                                                    @foreach($field->values as $value)
                                                        <option value="{{ $value->id }}" {{ old("fields.{$index}.category_field_value_id") == $value->id ? 'selected' : '' }}>
                                                            {{ app()->getLocale() == 'ar' ? $value->value_ar : $value->value_en }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <input type="hidden" name="fields[{{ $index }}][category_field_id]" value="{{ $field->id }}">
                                            @error("fields.{$index}.category_field_value_id")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="ti ti-info-circle me-2"></i>
                                        {{ __('messages.no_specifications_available') }}
                                    </div>
                                </div>
                            @endif

                            <!-- Submit Section -->
                            <div class="col-12 mt-5">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('admin.home') }}" class="btn btn-outline-secondary">
                                        <i class="ti ti-x me-1"></i> {{ __('messages.cancel') }}
                                    </a>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.auctions.create') }}" class="btn btn-outline-secondary">
                                            <i class="ti ti-refresh me-1"></i> {{ __('messages.clear_form') }}
                                        </a>
                                        <!-- Submit button moved to aside -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Aside Section with Submit Button -->
            <div class="col-lg-3">
                <aside class="auction-actions-aside">
                    <div class="card border-primary">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-primary">
                                <i class="ti ti-bolt me-2"></i>{{ __('messages.quick_actions') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <button type="submit" form="auctionForm" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="ti ti-gavel me-2"></i>
                                {{ __('messages.create_auction') }}
                            </button>
                            <small class="text-muted d-block">
                                {{ __('messages.create_auction_help') }}
                            </small>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</div>



<style>
/* Section Headers */
.section-header h5 {
    color: #495057;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}

/* Form Enhancements */
.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}

.form-label.required::after {
    content: ' *';
    color: #dc3545;
}

/* Input Group Enhancements */
.input-group-text {
    background: #f8f9fa;
    border-color: #dee2e6;
}

/* Button Enhancements */
.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Alert Improvements */
.alert {
    border-radius: 0.5rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem;
    }
    
    .card-body {
        padding: 1rem;
    }
}
</style>

@endsection