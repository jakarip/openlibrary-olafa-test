@extends('layouts/layoutMaster')

@section('title', __('catalogs.katalog.edit_title'))

@section('vendor-style')<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.css') }}">
@endsection

@section('page-style')
    <style>
        .wizard-navigation {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .wizard-tabs {
            border: none;
            gap: 0;
        }

        .wizard-tab {
            border: none !important;
            background: transparent !important;
            border-radius: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .wizard-tab-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 16px 8px;
            text-align: center;
            position: relative;
            min-height: 80px;
            justify-content: center;
        }

        .wizard-tab-icon {
            position: relative;
            width: 40px;
            height: 40px;
            border: 2px solid #dee2e6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            background: white;
            transition: all 0.3s ease;
            font-size: 18px;
            color: #6c757d;
        }

        .wizard-tab.active .wizard-tab-icon {
            border-color: #696cff;
            background: #696cff;
            color: white;
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(105, 108, 255, 0.3);
        }

        .wizard-tab-title {
            font-weight: 600;
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 2px;
            transition: all 0.3s ease;
        }

        .wizard-tab.active .wizard-tab-title {
            color: #696cff;
            font-weight: 700;
        }

        .wizard-tab-subtitle {
            font-size: 10px;
            color: #adb5bd;
            line-height: 1.2;
        }

        .wizard-tab.active .wizard-tab-subtitle {
            color: #696cff;
        }

        .wizard-progress .progress {
            background-color: #e9ecef;
        }

        .wizard-progress .progress-bar {
            background: linear-gradient(90deg, #696cff 0%, #5a67d8 100%);
            transition: width 0.3s ease;
        }

        .form-control:focus {
            border-color: #696cff;
            box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.25);
        }

        .file-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
        }

        .file-info {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 5px;
        }

        .current-file {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 12px;
            margin-top: 8px;
        }
    </style>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.js') }}"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- Perbaikan bagian header card di edit.blade.php -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0"><i class="ti ti-edit me-2"></i>{{ __('catalogs.katalog.edit') }}: {{ $catalog->code }}</h5>
                        <small class="text-muted">{{ $catalog->title }}</small>
                    </div>
                    <div>
                        @php
                            // Generate slug jika belum ada
                            if (!$catalog->slug) {
                                $slug = \Illuminate\Support\Str::slug($catalog->title);
                                // Cek uniqueness
                                $count = \App\Models\KnowledgeItemModel::where('slug', 'like', $slug . '%')
                                    ->where('id', '!=', $catalog->id)
                                    ->count();
                                if ($count > 0) {
                                    $slug = $slug . '-' . ($count + 1);
                                }
                                $catalog->slug = $slug;
                                $catalog->save();
                            }
                        @endphp

                        <a href="{{ route('catalog.detail', ['id' => $catalog->id, 'slug' => $catalog->slug]) }}"
                            class="btn btn-label-info me-2">
                            <i class="ti ti-eye me-1"></i> {{ __('catalogs.view_detail') }}
                        </a>
                        <a href="{{ route('catalog') }}" class="btn btn-label-secondary">
                            <i class="ti ti-arrow-left me-1"></i> {{ __('common.back') }}
                        </a>
                    </div>
                </div>


                <form action="{{ route('catalog.update', $catalog->id) }}" method="POST" enctype="multipart/form-data"
                    id="catalogForm">
                    @csrf
                    @method('PUT')

                    <div class="card-body p-0">
                        <!-- Progress Bar -->
                        <div class="wizard-progress px-4 py-3 border-bottom">
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar" role="progressbar" style="width: 16.67%" id="wizardProgressBar">
                                </div>
                            </div>
                            <div class="wizard-steps mt-2">
                                <small class="text-muted">
                                {{ __('catalogs.katalog.langkah') }} <span id="currentStep">1</span> {{ __('catalogs.katalog.from') }} <span id="totalSteps">6</span>
                                </small>
                            </div>
                        </div>

                        <!-- Wizard Navigation -->
                        <div class="wizard-navigation bg-light px-4 py-3 border-bottom">
                            <ul class="nav nav-pills nav-fill wizard-tabs" id="catalogTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active wizard-tab" id="basic-tab" data-bs-toggle="tab"
                                        data-bs-target="#basic" type="button" role="tab" data-step="1">
                                        <div class="wizard-tab-content">
                                            <div class="wizard-tab-icon">
                                                <i class="ti ti-info-circle"></i>
                                            </div>
                                            <div class="wizard-tab-text">
                                                <div class="wizard-tab-title">{{ __('catalogs.basic_information') }}</div>
                                                <small class="wizard-tab-subtitle">{{ __('catalogs.classification_type') }}</small>
                                            </div>
                                        </div>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link wizard-tab" id="pustaka-tab" data-bs-toggle="tab"
                                        data-bs-target="#pustaka" type="button" role="tab" data-step="2">
                                        <div class="wizard-tab-content">
                                            <div class="wizard-tab-icon">
                                                <i class="ti ti-book"></i>
                                            </div>
                                            <div class="wizard-tab-text">
                                                <div class="wizard-tab-title">{{ __('catalogs.bibliography') }}</div>
                                                <small class="wizard-tab-subtitle">{{ __('catalogs.title_code') }}</small>
                                            </div>
                                        </div>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link wizard-tab" id="pengarang-tab" data-bs-toggle="tab"
                                        data-bs-target="#pengarang" type="button" role="tab" data-step="3">
                                        <div class="wizard-tab-content">
                                            <div class="wizard-tab-icon">
                                                <i class="ti ti-user"></i>
                                            </div>
                                            <div class="wizard-tab-text">
                                                <div class="wizard-tab-title">{{ __('catalogs.stock_table_author') }}</div>
                                                <small class="wizard-tab-subtitle">{{ __('catalogs.bahanpustaka_table_author') }} & {{ __('catalogs.publisher') }}</small>
                                            </div>
                                        </div>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link wizard-tab" id="sirkulasi-tab" data-bs-toggle="tab"
                                        data-bs-target="#sirkulasi" type="button" role="tab" data-step="4">
                                        <div class="wizard-tab-content">
                                            <div class="wizard-tab-icon">
                                                <i class="ti ti-refresh"></i>
                                            </div>
                                            <div class="wizard-tab-text">
                                                <div class="wizard-tab-title">{{ __('catalogs.circulation') }}</div>
                                                <small class="wizard-tab-subtitle">{{ __('catalogs.date_cost') }}</small>
                                            </div>
                                        </div>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link wizard-tab" id="instansi-tab" data-bs-toggle="tab"
                                        data-bs-target="#instansi" type="button" role="tab" data-step="5">
                                        <div class="wizard-tab-content">
                                            <div class="wizard-tab-icon">
                                                <i class="ti ti-building"></i>
                                            </div>
                                            <div class="wizard-tab-text">
                                                <div class="wizard-tab-title">{{ __('catalogs.instansi') }}</div>
                                                <small class="wizard-tab-subtitle">{{ __('catalogs.bahanpustaka_table_faculty') }} & {{ __('catalogs.bahanpustaka_table_subject') }}</small>
                                            </div>
                                        </div>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link wizard-tab" id="konten-tab" data-bs-toggle="tab"
                                        data-bs-target="#konten" type="button" role="tab" data-step="6">
                                        <div class="wizard-tab-content">
                                            <div class="wizard-tab-icon">
                                                <i class="ti ti-file-text"></i>
                                            </div>
                                            <div class="wizard-tab-text">
                                                <div class="wizard-tab-title">{{ __('catalogs.katalog.konten') }}</div>
                                                <small class="wizard-tab-subtitle">Cover & File</small>
                                            </div>
                                        </div>
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <!-- Tab Contents -->
                        <div class="wizard-content p-4">
                            <div class="tab-content" id="catalogTabsContent">
                                <!-- Step 1: Informasi Dasar -->
                                <div class="tab-pane fade show active" id="basic" role="tabpanel">
                                    <div class="wizard-step-header mb-4">
                                        <h6 class="mb-1"><i class="ti ti-info-circle me-2"></i>{{ __('catalogs.katalog.basic_information') }}</h6>
                                        <p class="text-muted mb-0">{{ __('catalogs.katalog.information_desc') }}</p>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('catalogs.classification') }} <span class="text-danger">*</span></label>
                                            <select name="classification_code_id" class="form-select select2-basic"
                                                required>
                                                <option value="">{{ __('catalogs.katalog.choose_classification') }}</option>
                                            </select>
                                            @error('classification_code_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('catalogs.katalog.classification_type') }} <span
                                                    class="text-danger">*</span></label>
                                            <select name="knowledge_type_id" class="form-select select2-basic" required>
                                                <option value="">{{ __('catalogs.choose_collection_type') }}</option>
                                            </select>
                                            @error('knowledge_type_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('config.holiday.location') }} <span class="text-danger">*</span></label>
                                            <select name="item_location_id" class="form-select select2-basic" required>
                                                <option value="">{{ __('common.select_location') }}</option>
                                            </select>
                                            @error('item_location_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('catalogs.katalog.main_subject') }} <span
                                                    class="text-danger">*</span></label>
                                            <select name="knowledge_subject_id" class="form-select select2-basic" required>
                                                <option value="">{{ __('catalogs.katalog.choose_subject') }}</option>
                                            </select>
                                            @error('knowledge_subject_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">{{ __('catalogs.katalog.other_subject') }}</label>
                                            <textarea name="alternate_subject" class="form-control" rows="3"
                                                placeholder="{{ __('catalogs.katalog.separate_comma') }}">{{ old('alternate_subject', $catalog->alternate_subject) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2: Pustaka -->
                                <div class="tab-pane fade" id="pustaka" role="tabpanel">
                                    <div class="wizard-step-header mb-4">
                                        <h6 class="mb-1"><i class="ti ti-book me-2"></i>{{ __('catalogs.katalog.bib_info') }}</h6>
                                        <p class="text-muted mb-0">{{ __('catalogs.katalog.bib_desc') }}</p>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">{{ __('catalogs.title') }} <span class="text-danger">*</span></label>
                                            <textarea name="title" class="form-control" required rows="2"
                                                placeholder="{{ __('catalogs.katalog.catalog_title') }}">{{ old('title', $catalog->title) }}</textarea>
                                            @error('title')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('catalogs.katalog.code') }}</label>
                                            <input type="text" class="form-control bg-light" readonly
                                                value="{{ $catalog->code }}">
                                            <div class="form-text">
                                                <i class="ti ti-info-circle me-1"></i>
                                                {{ __('catalogs.katalog.code_desc') }} {{ $catalog->stock_total }} {{ __('catalogs.copies') }}
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('catalogs.collation') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="collation" class="form-control" required
                                                value="{{ old('collation', $catalog->collation) }}"
                                                placeholder="{{ __('catalogs.katalog.physical_desc') }}">
                                            @error('collation')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">ISBN</label>
                                            <input type="text" name="isbn" class="form-control"
                                                value="{{ old('isbn', $catalog->isbn) }}"
                                                placeholder="International Standard Book Number">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('catalogs.katalog.language') }}</label>
                                            <input type="text" name="language" class="form-control"
                                                value="{{ old('language', $catalog->language) }}"
                                                placeholder="{{ __('catalogs.katalog.used_language') }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 3: Pengarang -->
                                <div class="tab-pane fade" id="pengarang" role="tabpanel">
                                    <div class="wizard-step-header mb-4">
                                        <h6 class="mb-1"><i class="ti ti-user me-2"></i>{{ __('catalogs.bahanpustaka_table_author') }} & {{ __('catalogs.publisher') }}</h6>
                                        <p class="text-muted mb-0">{{ __('catalogs.katalog.author_desc') }}</p>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <h6 class="text-primary"><i class="ti ti-user me-2"></i>{{ __('catalogs.bahanpustaka_table_author') }}</h6>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('sbkps.name') }} <span class="text-danger">*</span></label>
                                            <textarea name="author" class="form-control" required rows="2"
                                                placeholder="{{ __('catalogs.author_name') }}">{{ old('author', $catalog->author) }}</textarea>
                                            @error('author')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('catalogs.tipe') }} <span class="text-danger">*</span></label>
                                            <select name="author_type" class="form-select" required>
                                                <option value="">{{ __('catalogs.choose_author_type') }}</option>
                                                <option value="1" {{ old('author_type', $catalog->author_type) == '1' ? 'selected' : '' }}>{{ __('catalogs.individual') }}</option>
                                                <option value="2" {{ old('author_type', $catalog->author_type) == '2' ? 'selected' : '' }}>{{ __('catalogs.organization') }}</option>
                                                <option value="3" {{ old('author_type', $catalog->author_type) == '3' ? 'selected' : '' }}>Conference</option>
                                            </select>
                                            @error('author_type')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('catalogs.editor_supervisor') }}</label>
                                            <input type="text" name="editor" class="form-control"
                                                value="{{ old('editor', $catalog->editor) }}"
                                                placeholder="{{ __('catalogs.editor_name') }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('catalogs.translator') }}</label>
                                            <input type="text" name="translator" class="form-control"
                                                value="{{ old('translator', $catalog->translator) }}"
                                                placeholder="{{ __('catalogs.translator_name') }}">
                                        </div>

                                        <div class="col-12">
                                            <h6 class="text-primary mt-3"><i class="ti ti-building me-2"></i>{{ __('catalogs.publisher') }}</h6>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">{{ __('sbkps.name') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="publisher_name" class="form-control" required
                                                value="{{ old('publisher_name', $catalog->publisher_name) }}"
                                                placeholder="{{ __('catalogs.publisher_name') }}">
                                            @error('publisher_name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">{{ __('catalogs.katalog.city') }}</label>
                                            <input type="text" name="publisher_city" class="form-control"
                                                value="{{ old('publisher_city', $catalog->publisher_city) }}"
                                                placeholder="{{ __('catalogs.publisher_city') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">{{ __('catalogs.year') }} <span class="text-danger">*</span></label>
                                            <select name="published_year" class="form-select" required id="publishedYear">
                                                <option value="">{{ __('catalogs.choose_year') }}</option>
                                            </select>
                                            @error('published_year')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 4: Sirkulasi -->
                                <div class="tab-pane fade" id="sirkulasi" role="tabpanel">
                                    <div class="wizard-step-header mb-4">
                                        <h6 class="mb-1"><i class="ti ti-refresh me-2"></i>{{ __('catalogs.katalog.circulation') }}</h6>
                                        <p class="text-muted mb-0">{{ __('catalogs.katalog.circulation_desc') }}</p>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('catalogs.entry_date') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="entrance_date" class="form-control" required
                                                value="{{ old('entrance_date', $catalog->entrance_date ? $catalog->entrance_date->format('Y-m-d') : '') }}">
                                            @error('entrance_date')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('catalogs.katalog.status_acc') }} <span
                                                    class="text-danger">*</span></label>
                                            <select name="origination" class="form-select" required>
                                                <option value="">{{ __('catalogs.select_source') }}</option>
                                                <option value="1" {{ old('origination', $catalog->origination) == '1' ? 'selected' : '' }}>{{ __('catalogs.katalog.purchase') }}</option>
                                                <option value="2" {{ old('origination', $catalog->origination) == '2' ? 'selected' : '' }}>{{ __('catalogs.katalog.donation') }}</option>
                                            </select>
                                            @error('origination')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('catalogs.katalog.supplier') }}</label>
                                            <input type="text" name="supplier" class="form-control"
                                                value="{{ old('supplier', $catalog->supplier) }}"
                                                placeholder="{{ __('catalogs.katalog.supplier_name') }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('catalogs.katalog.purchase_price') }}</label>
                                            <input type="number" name="price" class="form-control" min="0"
                                                value="{{ old('price', $catalog->price) }}" placeholder="{{ __('catalogs.katalog.purchase_price') }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('catalogs.katalog.rental_price') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" name="rent_cost" class="form-control" min="0" required
                                                value="{{ old('rent_cost', $catalog->rent_cost) }}"
                                                placeholder="{{ __('catalogs.katalog.purchase_price') }}">
                                            @error('rent_cost')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('catalogs.fine_fee') }} <span class="text-danger">*</span></label>
                                            <input type="number" name="penalty_cost" class="form-control" min="0" required
                                                value="{{ old('penalty_cost', $catalog->penalty_cost) }}"
                                                placeholder="{{ __('catalogs.return.fine_day') }}">
                                            @error('penalty_cost')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('config.location.input.total_collection') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" name="stock_total" class="form-control" min="1" required
                                                value="{{ old('stock_total', $catalog->stock_total) }}"
                                                placeholder="{{ __('catalogs.number_copies') }}">
                                            <div class="form-text">
                                                <i class="ti ti-alert-triangle me-1 text-warning"></i>
                                                {{ __('catalogs.katalog.copy_stock') }}
                                            </div>
                                            @error('stock_total')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 5: Instansi -->
                                <div class="tab-pane fade" id="instansi" role="tabpanel">
                                    <div class="wizard-step-header mb-4">
                                        <h6 class="mb-1"><i class="ti ti-building me-2"></i>{{ __('catalogs.katalog.information_instansi') }}</h6>
                                        <p class="text-muted mb-0">{{ __('catalogs.katalog.faculty_desc') }}
                                        </p>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('catalogs.bahanpustaka_table_faculty') }}</label>
                                            <select name="faculty_code" class="form-select select2-basic">
                                                <option value="">{{ __('catalogs.katalog.choose_faculty') }}</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('catalogs.bahanpustaka_table_studyprogram') }}</label>
                                            <select name="course_code" class="form-select select2-basic">
                                                <option value="">{{ __('catalogs.choose_study_program') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 6: Konten - Update untuk multiple softcopy edit -->
                                <div class="tab-pane fade" id="konten" role="tabpanel">
                                    <div class="wizard-step-header mb-4">
                                        <h6 class="mb-1"><i class="ti ti-file-text me-2"></i>{{ __('catalogs.katalog.konten') }} & File</h6>
                                        <p class="text-muted mb-0">{{ __('catalogs.katalog.upload_cover') }} </p>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">Cover</label>
                                            <input type="file" name="cover_image" class="form-control" accept="image/*">
                                            <div class="form-text">{{ __('catalogs.katalog.change_cover') }}</div>

                                            @if($catalog->cover_path)
                                                <div class="current-file">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-photo me-2 text-primary"></i>
                                                        <div>
                                                            <div class="fw-medium">{{ __('catalogs.current_cover') }}</div>
                                                            <small class="text-muted">{{ $catalog->cover_path }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <div id="cover-preview"></div>
                                        </div>

                                        <!-- Multiple Softcopy Section -->
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0"><i class="ti ti-files me-2"></i>Softcopy Files</h6>
                                                <button type="button" class="btn btn-sm btn-primary" id="addSoftcopyBtn">
                                                    <i class="ti ti-plus me-1"></i> {{ __('catalogs.katalog.add_softcopy') }}
                                                </button>
                                            </div>

                                            <!-- Existing Softcopy Files -->
                                            <div id="existingSoftcopyContainer">
                                                <!-- Will be populated by JavaScript -->
                                            </div>

                                            <!-- New Softcopy Files Container -->
                                            <div id="softcopyContainer">
                                                <!-- New softcopy items will be added here dynamically -->
                                            </div>

                                            <!-- Template for new softcopy item (hidden) -->
                                            <template id="softcopyTemplate">
                                                <div class="softcopy-item border rounded p-3 mb-3" data-index=""
                                                    data-type="new">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <h6 class="mb-0 text-success">
                                                            <i class="ti ti-plus me-1"></i>{{ __('catalogs.katalog.new_softcopy') }} <span
                                                                class="softcopy-number"></span>
                                                        </h6>
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-danger remove-softcopy-btn">
                                                            <i class="ti ti-trash"></i> {{ __('common.delete') }}
                                                        </button>
                                                    </div>

                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label">{{ __('catalogs.softcopy_type') }} <span
                                                                    class="text-danger">*</span></label>
                                                            <select class="form-select softcopy-type-select" required>
                                                                <option value="">{{ __('catalogs.katalog.choose_type') }}</option>
                                                            </select>
                                                            <div class="invalid-feedback"></div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label">File <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="file" class="form-control softcopy-file-input"
                                                                required>
                                                            <div class="form-text softcopy-extension-info">
                                                            {{ __('catalogs.katalog.softcopy_type') }}
                                                            </div>
                                                            <div class="invalid-feedback"></div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="softcopy-preview"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- Template for existing softcopy item (hidden) -->
                                            <template id="existingSoftcopyTemplate">
                                                <div class="existing-softcopy-item border rounded p-3 mb-3" data-file-id="">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <h6 class="mb-0 text-primary">
                                                            <i class="ti ti-file-text me-1"></i><span
                                                                class="softcopy-title"></span>
                                                        </h6>
                                                        <div class="btn-group">
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-primary download-existing-btn">
                                                                <i class="ti ti-download"></i> {{ __('catalogs.katalog.download') }}
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger remove-existing-btn">
                                                                <i class="ti ti-trash"></i> {{ __('common.delete') }}
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="existing-file-info">
                                                        <div class="d-flex align-items-center">
                                                            <i class="ti ti-file-text me-2 text-primary"></i>
                                                            <div>
                                                                <div class="fw-medium existing-filename"></div>
                                                                <small class="text-muted existing-upload-date"></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">{{ __('catalogs.abstract_content') }}</label>
                                            <textarea name="abstract_content" class="form-control" rows="6"
                                                placeholder="{{ __('catalogs.abstract_content') }}">{{ old('abstract_content', $catalog->abstract_content) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card-footer border-top">
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="button" class="btn btn-label-secondary" id="wizardPrevBtn"
                                    style="display: none;">
                                    <i class="ti ti-arrow-left me-1"></i> {{ __('catalogs.katalog.prev') }}
                                </button>
                            </div>

                            <div>
                                <a href="{{ route('catalog.detail', ['id' => $catalog->id, 'slug' => $catalog->slug]) }}"
                                    class="btn btn-label-secondary me-2">
                                    <i class="ti ti-x me-1"></i> {{ __('common.cancel') }}
                                </a>
                                <button type="button" class="btn btn-primary" id="wizardNextBtn">
                                {{ __('catalogs.katalog.next') }} <i class="ti ti-arrow-right ms-1"></i>
                                </button>
                                <button type="submit" class="btn btn-primary" id="wizardSubmitBtn" style="display: none;">
                                    <i class="ti ti-check me-1"></i> {{ __('catalogs.update_catalog') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        let currentStep = 1;
        const totalSteps = 6;
        let formOptions = {};
        const catalogData = @json($catalog);
        let selectedUploadTypes = new Set();
        let existingSoftcopyFiles = [];

        $(document).ready(function () {
            // Initialize Select2
            $('.select2-basic').select2();

            // Load form options
            loadFormOptions();

            // Wizard navigation
            setupWizardNavigation();

            // File upload handlers
            setupFileHandlers();

            // Form submission
            $('#catalogForm').on('submit', function (e) {
                if (!validateAllSteps()) {
                    e.preventDefault();
                    toastr.error('Mohon lengkapi semua field yang wajib diisi');
                }
            });
        });

        function loadFormOptions() {
            $.get('/catalog/data/get-form-options')
                .done(function (response) {
                    if (response.success) {
                        formOptions = response.data;
                        populateDropdowns();
                        setCurrentValues();
                    }
                })
                .fail(function () {
                    toastr.error('Gagal memuat data dropdown');
                });
        }
        // Function untuk load existing softcopy files
        function loadExistingSoftcopyFiles() {
            // Get existing files via AJAX
            $.ajax({
                url: `/catalog/detail`,
                method: 'GET',
                data: { id: catalogData.id },
                success: function (response) {
                    if (response.success && response.data.softcopy_files) {
                        existingSoftcopyFiles = response.data.softcopy_files;
                        displayExistingSoftcopyFiles();

                        // Add existing upload types to selected set
                        existingSoftcopyFiles.forEach(file => {
                            if (file.upload_type && file.upload_type.id) {
                                selectedUploadTypes.add(file.upload_type.id.toString());
                            }
                        });

                        updateAddButtonState();
                    }
                },
                error: function () {
                    console.error('Failed to load existing softcopy files');
                }
            });
        }

        // Function untuk display existing softcopy files
        function displayExistingSoftcopyFiles() {
            const container = document.getElementById('existingSoftcopyContainer');
            const template = document.getElementById('existingSoftcopyTemplate');

            container.innerHTML = '';

            existingSoftcopyFiles.forEach(file => {
                const clone = template.content.cloneNode(true);
                const item = clone.querySelector('.existing-softcopy-item');

                // Set attributes
                item.setAttribute('data-file-id', file.id);

                // Populate content
                item.querySelector('.softcopy-title').textContent = file.upload_type.title;
                item.querySelector('.existing-filename').textContent = file.filename;
                item.querySelector('.existing-upload-date').textContent =
                    'Diupload: ' + new Date(file.upload_date).toLocaleDateString('id-ID');

                // Add event listeners
                item.querySelector('.download-existing-btn').addEventListener('click', function () {
                    window.open(file.download_url, '_blank');
                });

                item.querySelector('.remove-existing-btn').addEventListener('click', function () {
                    removeExistingSoftcopyFile(file.id, file.upload_type.id);
                });

                container.appendChild(item);
            });
        }

        // Function untuk remove existing softcopy file
        function removeExistingSoftcopyFile(fileId, uploadTypeId) {
            Swal.fire({
                title: '{{ __("catalogs.katalog.delete_confirm") }}',
                text: "{{ __('catalogs.katalog.delete_softcopy') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ __("common.delete") }}',
                cancelButtonText: '{{ __("common.cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/catalog/delete-softcopy-file/${fileId}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.success) {
                                toastr.success(response.message);

                                // Remove from UI
                                document.querySelector(`[data-file-id="${fileId}"]`).remove();

                                // Remove from selectedUploadTypes
                                selectedUploadTypes.delete(uploadTypeId.toString());

                                // Update existing files array
                                existingSoftcopyFiles = existingSoftcopyFiles.filter(file => file.id !== fileId);

                                // Re-populate all selects
                                document.querySelectorAll('.softcopy-type-select').forEach(select => {
                                    const currentValue = select.value;
                                    populateSoftcopyTypes(select);
                                    select.value = currentValue;
                                });

                                updateAddButtonState();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function (xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Gagal menghapus file');
                        }
                    });
                }
            });
        }

        // Function untuk add new softcopy item (sama seperti di add.blade.php)
        function addSoftcopyItem() {
            const template = document.getElementById('softcopyTemplate');
            const container = document.getElementById('softcopyContainer');

            const clone = template.content.cloneNode(true);
            const softcopyItem = clone.querySelector('.softcopy-item');

            softcopyItem.setAttribute('data-index', softcopyIndex);
            softcopyItem.querySelector('.softcopy-number').textContent = softcopyIndex + 1;

            const typeSelect = softcopyItem.querySelector('.softcopy-type-select');
            const fileInput = softcopyItem.querySelector('.softcopy-file-input');

            typeSelect.name = `softcopy_type_${softcopyIndex}`;
            fileInput.name = `softcopy_file_${softcopyIndex}`;

            populateSoftcopyTypes(typeSelect);
            setupSoftcopyEventListeners(softcopyItem, softcopyIndex);
            container.appendChild(softcopyItem);

            softcopyIndex++;
            updateAddButtonState();
        }

        // Function untuk remove new softcopy item
        function removeSoftcopyItem(index) {
            const item = document.querySelector(`.softcopy-item[data-index="${index}"]`);
            if (item) {
                const typeSelect = item.querySelector('.softcopy-type-select');
                if (typeSelect.value) {
                    selectedUploadTypes.delete(typeSelect.value);
                }

                item.remove();
                updateSoftcopyNumbering();
                updateAddButtonState();

                document.querySelectorAll('.softcopy-type-select').forEach(select => {
                    const currentValue = select.value;
                    populateSoftcopyTypes(select);
                    select.value = currentValue;
                });
            }
        }

        // Function untuk populate softcopy types (sama seperti di add.blade.php)
        function populateSoftcopyTypes(selectElement) {
            let options = '<option value="">{{ __("catalogs.katalog.choose_type") }}</option>';

            if (formOptions.upload_types) {
                formOptions.upload_types.forEach(type => {
                    const isDisabled = selectedUploadTypes.has(type.id.toString()) &&
                        selectElement.value !== type.id.toString();
                    const disabledAttr = isDisabled ? 'disabled' : '';

                    options += `<option value="${type.id}" data-extension="${type.extension}" ${disabledAttr}>${type.title}</option>`;
                });
            }

            selectElement.innerHTML = options;
        }

        // Function untuk setup event listeners (sama seperti di add.blade.php)
        function setupSoftcopyEventListeners(item, index) {
            const typeSelect = item.querySelector('.softcopy-type-select');
            const fileInput = item.querySelector('.softcopy-file-input');
            const removeBtn = item.querySelector('.remove-softcopy-btn');
            const preview = item.querySelector('.softcopy-preview');
            const extensionInfo = item.querySelector('.softcopy-extension-info');

            typeSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const extension = selectedOption.dataset.extension;

                const oldValue = this.dataset.oldValue;
                if (oldValue) {
                    selectedUploadTypes.delete(oldValue);
                }

                if (this.value) {
                    selectedUploadTypes.add(this.value);
                    this.dataset.oldValue = this.value;
                }

                if (extension) {
                    extensionInfo.innerHTML = `{{ __('catalogs.katalog.file_format') }} <strong>.${extension}</strong>`;
                    fileInput.setAttribute('accept', `.${extension}`);
                } else {
                    extensionInfo.textContent = '{{ __("catalogs.katalog.softcopy_type") }}';
                    fileInput.setAttribute('accept', '.pdf,.doc,.docx');
                }

                fileInput.value = '';
                preview.innerHTML = '';

                document.querySelectorAll('.softcopy-type-select').forEach(select => {
                    if (select !== typeSelect) {
                        const currentValue = select.value;
                        populateSoftcopyTypes(select);
                        select.value = currentValue;
                    }
                });

                updateAddButtonState();
            });

            fileInput.addEventListener('change', function () {
                const file = this.files[0];
                preview.innerHTML = '';

                if (file) {
                    const selectedType = typeSelect.options[typeSelect.selectedIndex];
                    const expectedExtension = selectedType.dataset.extension;
                    const fileExtension = file.name.split('.').pop().toLowerCase();

                    if (!typeSelect.value) {
                        toastr.error('{{ __("catalogs.katalog.softcopy_type") }}');
                        this.value = '';
                        return;
                    }

                    if (expectedExtension && fileExtension !== expectedExtension.toLowerCase()) {
                        toastr.error(`{{ __('catalogs.katalog.file_format') }}  .${expectedExtension}`);
                        this.value = '';
                        return;
                    }

                    const fileSize = (file.size / 1024).toFixed(2);
                    const sizeUnit = fileSize > 1024 ? `${(fileSize / 1024).toFixed(2)} MB` : `${fileSize} KB`;

                    preview.innerHTML = `
                                <div class="file-info mt-2">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-file-text me-2 text-success"></i>
                                        <div>
                                            <div class="fw-medium">${file.name}</div>
                                            <small class="text-muted">{{ __('catalogs.katalog.size') }}  ${sizeUnit} | Format: .${fileExtension}</small>
                                        </div>
                                    </div>
                                </div>
                            `;
                }
            });

            removeBtn.addEventListener('click', function () {
                removeSoftcopyItem(index);
            });
        }

        // Function untuk update numbering
        function updateSoftcopyNumbering() {
            const items = document.querySelectorAll('.softcopy-item[data-type="new"]');
            items.forEach((item, index) => {
                item.querySelector('.softcopy-number').textContent = index + 1;
            });
        }

        // Function untuk update add button state
        function updateAddButtonState() {
            const addBtn = document.getElementById('addSoftcopyBtn');
            const availableTypes = formOptions.upload_types ? formOptions.upload_types.length : 0;
            const selectedCount = selectedUploadTypes.size;

            if (selectedCount >= availableTypes) {
                addBtn.disabled = true;
                addBtn.innerHTML = '<i class="ti ti-check me-1"></i> {{ __('catalogs.katalog.all_type_info') }}';
            } else {
                addBtn.disabled = false;
                addBtn.innerHTML = '<i class="ti ti-plus me-1"></i> {{ __('catalogs.katalog.add_softcopy') }} ';
            }
        }

        // Function untuk collect softcopy data
        function collectSoftcopyData() {
            const softcopyData = [];
            const items = document.querySelectorAll('.softcopy-item[data-type="new"]');

            items.forEach((item, index) => {
                const typeSelect = item.querySelector('.softcopy-type-select');
                const fileInput = item.querySelector('.softcopy-file-input');

                if (typeSelect.value && fileInput.files.length > 0) {
                    softcopyData.push({
                        upload_type_id: typeSelect.value,
                        index: item.getAttribute('data-index')
                    });
                }
            });

            return softcopyData;
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function () {
            // Load existing files when form options are loaded
            const originalPopulateDropdowns = window.populateDropdowns;
            window.populateDropdowns = function () {
                originalPopulateDropdowns();
                window.formOptions = formOptions;
                loadExistingSoftcopyFiles();
            };

            // Add softcopy button
            document.getElementById('addSoftcopyBtn').addEventListener('click', addSoftcopyItem);

            // Form submit - collect softcopy data
            document.getElementById('catalogForm').addEventListener('submit', function (e) {
                const softcopyData = collectSoftcopyData();

                if (softcopyData.length > 0) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'softcopy_data';
                    hiddenInput.value = JSON.stringify(softcopyData);

                    this.appendChild(hiddenInput);
                }
            });
        });
        function populateDropdowns() {
            // Knowledge Types
            let typeOptions = '<option value="">{{ __("catalogs.choose_collection_type") }}</option>';
            formOptions.knowledge_types?.forEach(type => {
                typeOptions += `<option value="${type.id}">${type.name}</option>`;
            });
            $('select[name="knowledge_type_id"]').html(typeOptions);

            // Classifications
            let classOptions = '<option value="">{{ __("catalogs.katalog.choose_classification") }}</option>';
            formOptions.classifications?.forEach(classification => {
                let displayText = classification.code;
                if (classification.name?.trim()) {
                    displayText = `${classification.code} - ${classification.name}`;
                }
                classOptions += `<option value="${classification.id}">${displayText}</option>`;
            });
            $('select[name="classification_code_id"]').html(classOptions);

            // Subjects
            let subjectOptions = '<option value="">{{ __("catalogs.katalog.choose_subject") }}</option>';
            formOptions.subjects?.forEach(subject => {
                subjectOptions += `<option value="${subject.id}">${subject.name}</option>`;
            });
            $('select[name="knowledge_subject_id"]').html(subjectOptions);

            // Locations
            let locationOptions = '<option value="">{{ __("common.select_location") }}</option>';
            formOptions.locations?.forEach(location => {
                locationOptions += `<option value="${location.id}">${location.name}</option>`;
            });
            $('select[name="item_location_id"]').html(locationOptions);

            // Faculty
            let facultyOptions = '<option value="">{{ __("catalogs.katalog.choose_faculty") }}</option>';
            formOptions.fakultas?.forEach(fakultas => {
                let displayName = fakultas.SINGKATAN ?
                    `${fakultas.SINGKATAN} - ${fakultas.NAMA_FAKULTAS}` :
                    fakultas.NAMA_FAKULTAS;
                facultyOptions += `<option value="${fakultas.C_KODE_FAKULTAS}">${displayName}</option>`;
            });
            $('select[name="faculty_code"]').html(facultyOptions);

            // Program Studi
            let prodiOptions = '<option value="">{{ __("catalogs.choose_study_program") }}</option>';
            formOptions.prodi?.forEach(prodi => {
                prodiOptions += `<option value="${prodi.C_KODE_PRODI}">${prodi.NAMA_PRODI}</option>`;
            });
            $('select[name="course_code"]').html(prodiOptions);

            // Upload Types
            let uploadTypeOptions = '<option value="">{{ __("catalogs.katalog.choose_type") }}</option>';
            formOptions.upload_types?.forEach(type => {
                uploadTypeOptions += `<option value="${type.id}" data-extension="${type.extension}">${type.title}</option>`;
            });
            $('select[name="softcopy_type_id"]').html(uploadTypeOptions);

            // Published Year
            let yearOptions = '<option value="">{{ __("catalogs.choose_year") }}</option>';
            const currentYear = new Date().getFullYear();
            for (let year = currentYear; year >= 1975; year--) {
                yearOptions += `<option value="${year}">${year}</option>`;
            }
            $('#publishedYear').html(yearOptions);
        }

        function setCurrentValues() {
            // Set selected values from existing catalog data
            if (catalogData.knowledge_type_id) {
                $('select[name="knowledge_type_id"]').val(catalogData.knowledge_type_id).trigger('change');
            }

            if (catalogData.classification_code_id) {
                $('select[name="classification_code_id"]').val(catalogData.classification_code_id).trigger('change');
            }

            if (catalogData.knowledge_subject_id) {
                $('select[name="knowledge_subject_id"]').val(catalogData.knowledge_subject_id).trigger('change');
            }

            if (catalogData.item_location_id) {
                $('select[name="item_location_id"]').val(catalogData.item_location_id).trigger('change');
            }

            if (catalogData.faculty_code) {
                $('select[name="faculty_code"]').val(catalogData.faculty_code).trigger('change');
            }

            if (catalogData.course_code) {
                $('select[name="course_code"]').val(catalogData.course_code).trigger('change');
            }

            if (catalogData.published_year) {
                $('#publishedYear').val(catalogData.published_year).trigger('change');
            }
        }

        function setupWizardNavigation() {
            $('#wizardNextBtn').on('click', nextStep);
            $('#wizardPrevBtn').on('click', prevStep);

            $('.wizard-tab').on('click', function (e) {
                e.preventDefault();
                const targetStep = parseInt($(this).data('step'));
                goToStep(targetStep);
            });
        }

        function nextStep() {
            if (currentStep >= totalSteps) return;

            if (validateStep(currentStep)) {
                currentStep++;
                showStep(currentStep);
                updateProgress();
                updateButtons();
            }
        }

        function prevStep() {
            if (currentStep <= 1) return;

            currentStep--;
            showStep(currentStep);
            updateProgress();
            updateButtons();
        }

        function goToStep(targetStep) {
            if (targetStep > currentStep) {
                // Validate all steps between current and target
                for (let step = currentStep; step < targetStep; step++) {
                    if (!validateStep(step)) {
                        toastr.error(`{{ __('catalogs.katalog.please_complete_step') }} ${step} {{ __('catalogs.katalog.first') }}`);
                        return;
                    }
                }
            }

            currentStep = targetStep;
            showStep(currentStep);
            updateProgress();
            updateButtons();
        }

        function showStep(step) {
            $('.tab-pane').removeClass('show active');
            $('.wizard-tab').removeClass('active');

            const targetTab = $(`.wizard-tab[data-step="${step}"]`);
            const targetPane = $(targetTab.data('bs-target'));

            targetTab.addClass('active');
            targetPane.addClass('show active');
        }

        function updateProgress() {
            const progress = (currentStep / totalSteps) * 100;
            $('#wizardProgressBar').css('width', `${progress}%`);
            $('#currentStep').text(currentStep);
        }

        function updateButtons() {
            if (currentStep === 1) {
                $('#wizardPrevBtn').hide();
            } else {
                $('#wizardPrevBtn').show();
            }

            if (currentStep === totalSteps) {
                $('#wizardNextBtn').hide();
                $('#wizardSubmitBtn').show();
            } else {
                $('#wizardNextBtn').show();
                $('#wizardSubmitBtn').hide();
            }
        }

        function validateStep(step) {
            const stepValidation = {
                1: ['classification_code_id', 'knowledge_type_id', 'item_location_id', 'knowledge_subject_id'],
                2: ['title', 'collation'],
                3: ['author', 'author_type', 'publisher_name', 'published_year'],
                4: ['entrance_date', 'origination', 'rent_cost', 'penalty_cost', 'stock_total'],
                5: [],
                6: []
            };

            const requiredFields = stepValidation[step] || [];
            let isValid = true;

            requiredFields.forEach(fieldName => {
                const field = $(`[name="${fieldName}"]`);
                const value = field.val();

                if (!value || value.trim() === '') {
                    isValid = false;
                    field.addClass('is-invalid');
                } else {
                    field.removeClass('is-invalid').addClass('is-valid');
                }
            });

            return isValid;
        }

        function validateAllSteps() {
            let allValid = true;
            for (let step = 1; step <= totalSteps; step++) {
                if (!validateStep(step)) {
                    allValid = false;
                }
            }
            return allValid;
        }

        function setupFileHandlers() {
            // Cover preview
            $('input[name="cover_image"]').on('change', function () {
                const file = this.files[0];
                const preview = $('#cover-preview');

                preview.empty();

                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        preview.html(`<img src="${e.target.result}" class="file-preview img-thumbnail" style="max-width: 200px; max-height: 200px;">`);
                    };
                    reader.readAsDataURL(file);

                    preview.append(`<div class="file-info mt-2">{{ __('catalogs.katalog.new_file') }} ${file.name} (${Math.round(file.size / 1024)} KB)</div>`);
                }
            });

            // Softcopy type change
            $('select[name="softcopy_type_id"]').on('change', function () {
                const selectedOption = $(this).find('option:selected');
                const extension = selectedOption.data('extension');

                if (extension) {
                    $('#softcopy-extension-info').html(`{{ __('catalogs.katalog.file_format') }} <strong>.${extension}</strong>`);
                    $('input[name="softcopy_file"]').attr('accept', `.${extension}`);
                } else {
                    $('#softcopy-extension-info').text('{{ __("catalogs.katalog.update_softcopy") }}');
                    $('input[name="softcopy_file"]').attr('accept', '.pdf,.doc,.docx');
                }

                $('input[name="softcopy_file"]').val('');
                $('#softcopy-preview').empty();
            });

            // Softcopy preview
            $('input[name="softcopy_file"]').on('change', function () {
                const file = this.files[0];
                const preview = $('#softcopy-preview');
                const selectedType = $('select[name="softcopy_type_id"] option:selected');

                preview.empty();

                if (file) {
                    const fileExtension = file.name.split('.').pop().toLowerCase();
                    const expectedExtension = selectedType.data('extension');

                    if (!selectedType.val()) {
                        toastr.error('{{ __("catalogs.katalog.softcopy_type") }}');
                        $(this).val('');
                        return;
                    }

                    if (expectedExtension && fileExtension !== expectedExtension.toLowerCase()) {
                        toastr.error(`{{ __("catalogs.katalog.file_format") }} .${expectedExtension}`);
                        $(this).val('');
                        return;
                    }

                    const fileSize = (file.size / 1024).toFixed(2);
                    const sizeUnit = fileSize > 1024 ? `${(fileSize / 1024).toFixed(2)} MB` : `${fileSize} KB`;

                    preview.html(`
                                                        <div class="file-info mt-2">
                                                            <div class="d-flex align-items-center">
                                                                <i class="ti ti-file-text me-2 text-primary"></i>
                                                                <div>
                                                                    <div class="fw-medium">{{ __('catalogs.katalog.new_file') }} ${file.name}</div>
                                                                    <small class="text-muted">{{ __('catalogs.katalog.size') }} ${sizeUnit} | Format: .${fileExtension}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    `);
                }
            });
        }
    </script>
@endsection