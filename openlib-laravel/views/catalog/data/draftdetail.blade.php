@extends('layouts/layoutMaster')

@section('title', '__('catalogs.katalog.detail') - ' . $catalogTitle)


@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}">
@endsection

@section('page-style')
    <style>
        .catalog-cover {
            width: 100%;
            max-width: 300px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .catalog-cover:hover {
            transform: scale(1.05);
        }

        .detail-card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
        }

        .detail-card .card-header {
            background: white;
            border-radius: 12px 12px 0 0;
            padding: 1.25rem;
            margin-bottom: 10px;
        }

        .info-table {
            margin-bottom: 0;
        }

        .info-table td {
            padding: 0.75rem 0;
            border: none;
            vertical-align: top;
        }

        .info-table td:first-child {
            font-weight: 600;
            color: #5a6169;
            width: 30%;
            padding-right: 1rem;
        }

        .stock-badge {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
        }

        .stock-items-table {
            font-size: 0.875rem;
        }

        .stock-items-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #5a6169;
            border-bottom: 2px solid #e9ecef;
        }

        .stock-items-table td {
            border-bottom: 1px solid #f0f0f0;
        }

        .stock-code {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            background: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        .abstract-content {
            background: #f8f9fa;
            border-left: 4px solid #696cff;
            padding: 1.5rem;
            border-radius: 0 8px 8px 0;
            line-height: 1.6;
        }

        .softcopy-file-item {
            transition: all 0.3s ease;
            background: #ffffff;
            border: 1px solid #e9ecef !important;
        }

        .softcopy-file-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: #696cff !important;
        }

        .file-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .swal-wide {
            width: 90% !important;
            max-width: 1200px !important;
        }

        /* Loading skeleton styles */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .skeleton-text {
            height: 16px;
            border-radius: 4px;
            margin-bottom: 8px;
        }

        .skeleton-title {
            height: 24px;
            border-radius: 4px;
            margin-bottom: 12px;
        }

        .skeleton-cover {
            width: 200px;
            height: 300px;
            border-radius: 8px;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            content: "›";
            color: #8592a3;
        }

        @media (max-width: 768px) {
            .info-table td:first-child {
                width: 40%;
                font-size: 0.875rem;
            }

            .catalog-cover {
                max-width: 200px;
            }
        }
    </style>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('content')
    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('catalog') }}">
                            <i class="ti ti-books me-1"></i>{{ __('catalogs.katalog') }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $catalogCode }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card detail-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-info-circle me-2"></i>{{ __('catalogs.catalog_info') }}
                    </h5>
                </div>
                <div class="card-body" id="basicInfoContent">
                    <!-- LOADING SKELETON -->
                    <div class="row">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            <div class="skeleton skeleton-cover mx-auto"></div>
                        </div>
                        <div class="col-md-8">
                            <div class="skeleton skeleton-title mb-3"></div>
                            <div class="skeleton skeleton-text mb-2"></div>
                            <div class="skeleton skeleton-text mb-2"></div>
                            <div class="skeleton skeleton-text mb-2"></div>
                            <div class="skeleton skeleton-text mb-2"></div>
                            <div class="skeleton skeleton-text mb-2"></div>
                            <div class="skeleton skeleton-text mb-2"></div>
                            <div class="skeleton skeleton-text mb-2"></div>
                            <div class="skeleton skeleton-text"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Information -->
            <div class="card detail-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-package me-2"></i>{{ __('catalogs.stock_info') }}
                    </h5>
                </div>
                <div class="card-body" id="stockInfoContent">
                    <!-- LOADING SKELETON -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2">
                                <div class="skeleton" style="width: 80px; height: 28px; border-radius: 14px;"></div>
                                <div class="skeleton" style="width: 100px; height: 28px; border-radius: 14px;"></div>
                                <div class="skeleton" style="width: 90px; height: 28px; border-radius: 14px;"></div>
                                <div class="skeleton" style="width: 85px; height: 28px; border-radius: 14px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="skeleton" style="height: 200px; border-radius: 8px;"></div>
                </div>
            </div>

            <!-- Softcopy Section - Will be shown if exists -->
            <div class="card detail-card mb-4" id="softcopySection" style="display: none;">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-files me-2"></i>Softcopy Files
                    </h5>
                </div>
                <div class="card-body" id="softcopyFilesContent">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>

            <!-- Abstract - Will be shown if exists -->
            <div class="card detail-card mb-4" id="abstractSection" style="display: none;">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-file-description me-2"></i>{{ __('catalogs.abstract') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="abstract-content" id="abstractContent">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="card detail-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-info-square me-2"></i>{{ __('catalogs.additional_info') }}
                    </h5>
                </div>
                <div class="card-body" id="additionalInfoContent">
                    <!-- LOADING SKELETON -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="skeleton skeleton-text mb-2"></div>
                            <div class="skeleton skeleton-text mb-2"></div>
                            <div class="skeleton skeleton-text mb-2"></div>
                            <div class="skeleton skeleton-text"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="skeleton skeleton-text mb-2"></div>
                            <div class="skeleton skeleton-text mb-2"></div>
                            <div class="skeleton skeleton-text mb-2"></div>
                            <div class="skeleton skeleton-text"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Action Buttons -->
            <div class="card detail-card mb-4">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('catalog') }}" class="btn btn-label-secondary">
                            <i class="ti ti-arrow-left me-2"></i>{{ __('catalogs.katalog.back_to_list') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card detail-card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="ti ti-chart-bar me-2"></i>{{ __('catalogs.katalog.fast_statistic') }}
                    </h6>
                </div>
                <div class="card-body" id="quickStatsContent">
                    <!-- LOADING SKELETON -->
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="skeleton" style="height: 32px; width: 40px; margin: 0 auto 8px;"></div>
                                <div class="skeleton skeleton-text" style="height: 12px;"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="skeleton" style="height: 32px; width: 40px; margin: 0 auto 8px;"></div>
                            <div class="skeleton skeleton-text" style="height: 12px;"></div>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="skeleton" style="height: 28px; width: 30px; margin: 0 auto 8px;"></div>
                                <div class="skeleton skeleton-text" style="height: 12px;"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="skeleton" style="height: 28px; width: 30px; margin: 0 auto 8px;"></div>
                            <div class="skeleton skeleton-text" style="height: 12px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Info -->
            <div class="card detail-card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="ti ti-currency-dollar me-2"></i>{{ __('catalogs.financial_info') }}
                    </h6>
                </div>
                <div class="card-body" id="financialInfoContent">
                    <!-- LOADING SKELETON -->
                    <div class="skeleton skeleton-text mb-2"></div>
                    <div class="skeleton skeleton-text mb-2"></div>
                    <div class="skeleton skeleton-text mb-2"></div>
                    <div class="skeleton skeleton-text"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        // Define catalog data dari server
        const catalogData = {
            id: {{ $catalogId }},
            slug: '{{ $catalogSlug }}',
            code: '{{ $catalogCode }}',
            title: '{{ $catalogTitle }}'
        };

        document.addEventListener('DOMContentLoaded', function () {
            // LANGSUNG LOAD DATA VIA AJAX BEGITU PAGE READY
            loadCatalogData();
        });

        function loadCatalogData() {
            // Build AJAX URL dengan slug
            const ajaxUrl = `/catalog/detail/${catalogData.id}/${catalogData.slug}`;

            $.ajax({
                url: ajaxUrl,
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                timeout: 15000,
                success: function (response) {
                    if (response.success && response.data) {
                        populateAllContent(response.data);
                    } else {
                        showErrorMessage('Gagal memuat data katalog: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', { xhr, status, error });

                    let errorMsg = 'Gagal memuat data katalog';
                    if (status === 'timeout') {
                        errorMsg = 'Request timeout - mohon refresh halaman';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.status) {
                        errorMsg = `HTTP Error ${xhr.status}: ${xhr.statusText}`;
                    }

                    showErrorMessage(errorMsg);
                }
            });
        }

        function populateAllContent(data) {
            // 1. Basic Information
            populateBasicInfo(data);

            // 2. Stock Information
            populateStockInfo(data);

            // 3. Softcopy Files (if exists)
            if (data.has_softcopy && data.softcopy_files.length > 0) {
                populateSoftcopyFiles(data.softcopy_files);
                document.getElementById('softcopySection').style.display = 'block';
            }

            // 4. Abstract (if exists)
            if (data.abstract_content && data.abstract_content.trim()) {
                document.getElementById('abstractContent').innerHTML = data.abstract_content.replace(/\n/g, '<br>');
                document.getElementById('abstractSection').style.display = 'block';
            }

            // 5. Additional Information
            populateAdditionalInfo(data);

            // 6. Quick Stats
            populateQuickStats(data);

            // 7. Financial Info
            populateFinancialInfo(data);

            console.log('All content loaded successfully!');
        }

        function populateBasicInfo(data) {
            const coverUrl = data.cover_url || '{{ asset("assets/img/default-book-cover.jpg") }}';
            const defaultCover = '{{ asset("assets/img/default-book-cover.jpg") }}';

            const html = `
                                <div class="row">
                                    <div class="col-md-4 text-center mb-4 mb-md-0">
                                        <img src="${coverUrl}" alt="Cover ${data.title}" class="catalog-cover"
                                            onerror="this.onerror=null; this.src='${defaultCover}'">
                                    </div>
                                    <div class="col-md-8">
                                        <table class="table info-table">
                                            <tr>
                                                <td>{{ __('catalogs.katalog.code') }}:</td>
                                                <td><span class="stock-code">${data.code}</span></td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('catalogs.title') }}:</td>
                                                <td><strong>${data.title}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('sbksp.author') }}:</td>
                                                <td>${data.author}</td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('catalogs.publisher') }}:</td>
                                                <td>${data.publisher_name}${data.publisher_city ? ', ' + data.publisher_city : ''} (${data.published_year})</td>
                                            </tr>
                                            <tr>
                                                <td>ISBN:</td>
                                                <td>${data.isbn || 'Tidak tersedia'}</td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('catalogs.tipe') }}:</td>
                                                <td><span class="badge bg-primary">${data.knowledge_type_name}</span></td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('config.classification.input.subject') }}:</td>
                                                <td>${data.subject_name}</td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('config.holiday.location') }}:</td>
                                                <td>${data.location_name}</td>
                                            </tr>
                                            ${data.fakultas_name ? `
                                            <tr>
                                                <td>{{ __('catalogs.bahanpustaka_table_faculty') }}:</td>
                                                <td>${data.fakultas_name}</td>
                                            </tr>
                                            ` : ''}
                                            ${data.prodi_name ? `
                                            <tr>
                                                <td>{{ __('catalogs.bahanpustaka_table_studyprogram') }}:</td>
                                                <td>${data.prodi_name}</td>
                                            </tr>
                                            ` : ''}
                                        </table>
                                    </div>
                                </div>
                            `;

            document.getElementById('basicInfoContent').innerHTML = html;
        }

        function populateStockInfo(data) {
            const stockSummary = data.stock_summary;
            const stockItems = data.stock_items;

            let badgesHtml = `
                                <span class="stock-badge badge bg-info">
                                    <i class="ti ti-books me-1"></i>Total: ${stockSummary.total}
                                </span>
                            `;

            if (stockSummary.available > 0) {
                badgesHtml += `
                                    <span class="stock-badge badge bg-success">
                                        <i class="ti ti-check me-1"></i>{{ __('catalogs.avail') }}: ${stockSummary.available}
                                    </span>
                                `;
            }

            if (stockSummary.borrowed > 0) {
                badgesHtml += `
                                    <span class="stock-badge badge bg-warning text-dark">
                                        <i class="ti ti-user me-1"></i>{{ __('catalogs.borrowed') }}: ${stockSummary.borrowed}
                                    </span>
                                `;
            }

            if (stockSummary.damaged > 0) {
                badgesHtml += `
                                    <span class="stock-badge badge bg-danger">
                                        <i class="ti ti-alert-triangle me-1"></i>{{ __('catalogs.damaged') }}: ${stockSummary.damaged}
                                    </span>
                                `;
            }

            if (stockSummary.lost > 0) {
                badgesHtml += `
                                    <span class="stock-badge badge bg-dark">
                                        <i class="ti ti-x me-1"></i>{{ __('catalogs.lost') }}: ${stockSummary.lost}
                                    </span>
                                `;
            }

            let tableHtml = '';
            if (stockItems.length > 0) {
                tableHtml = `
                                    <div class="table-responsive">
                                        <table class="table stock-items-table">
                                            <thead>
                                                <tr>
                                                    <th width="50%">{{ __('catalogs.katalog.copies_code') }}</th>
                                                    <th width="25%">Status</th>
                                                    <th width="25%">RFID</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                `;

                stockItems.forEach(item => {
                    let badgeClass = 'bg-secondary';
                    if (item.status == 1 || item.status == 6) badgeClass = 'bg-success';
                    else if (item.status == 2) badgeClass = 'bg-warning text-dark';
                    else if (item.status == 3) badgeClass = 'bg-danger';
                    else if (item.status == 4) badgeClass = 'bg-dark';

                    tableHtml += `
                                        <tr>
                                            <td><span class="stock-code">${item.code}</span></td>
                                            <td><span class="badge ${badgeClass}">${item.status_label}</span></td>
                                            <td>${item.rfid || '-'}</td>
                                        </tr>
                                    `;
                });

                tableHtml += `
                                            </tbody>
                                        </table>
                                    </div>
                                `;
            } else {
                tableHtml = `
                                    <div class="text-center py-4">
                                        <i class="ti ti-package-off display-4 text-muted mb-3"></i>
                                        <p class="text-muted">{{ __('catalogs.katalog.not_avail') }}</p>
                                    </div>
                                `;
            }

            const html = `
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="d-flex flex-wrap gap-2">
                                            ${badgesHtml}
                                        </div>
                                    </div>
                                </div>
                                ${tableHtml}
                            `;

            document.getElementById('stockInfoContent').innerHTML = html;
        }

        function populateSoftcopyFiles(files) {
            let html = '';

            files.forEach(file => {
                const fileExtension = file.filename.split('.').pop().toUpperCase();

                let iconClass = 'ti-file-text';
                let colorClass = 'text-primary';

                switch (fileExtension.toLowerCase()) {
                    case 'pdf':
                        iconClass = 'ti-file-type-pdf';
                        colorClass = 'text-danger';
                        break;
                    case 'doc':
                    case 'docx':
                        iconClass = 'ti-file-type-doc';
                        colorClass = 'text-info';
                        break;
                    case 'ppt':
                    case 'pptx':
                        iconClass = 'ti-file-type-ppt';
                        colorClass = 'text-warning';
                        break;
                }

                const statusBadge = file.exists ?
                    '<span class="badge bg-success">{{ __("catalogs.stock_available") }}</span>' :
                    '<span class="badge bg-warning">{{ __("catalogs.katalog.file_missing") }}</span>';

                html += `
                    <div class="softcopy-file-item border rounded p-3 mb-3">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="file-icon bg-light rounded p-3">
                                    <i class="ti ${iconClass} ${colorClass}" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <div class="col">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="mb-1">${file.upload_type.title}</h6>
                                        <p class="text-muted mb-1">${file.filename}</p>
                                        <small class="text-muted">
                                            <i class="ti ti-calendar me-1"></i>{{ __("catalogs.katalog.uploaded_at") }}: ${file.upload_date_formatted}
                                        </small>
                                    </div>
                                    <div class="col-auto">
                                        <div class="d-flex align-items-center gap-2">
                                            ${statusBadge}
                                            <div class="d-flex gap-1">
                                                ${file.exists ? `
                                                    <button class="btn btn-outline-primary btn-sm" onclick="previewSoftcopyFile(${file.id}, '${file.upload_type.title}', '${fileExtension}')">
                                                        <i class="ti ti-eye me-1"></i>Preview
                                                    </button>
                                                    <a href="${file.download_url}" class="btn btn-primary btn-sm">
                                                        <i class="ti ti-download me-1"></i>{{ __('catalogs.katalog.download') }}
                                                    </a>
                                                ` : `
                                                    <button class="btn btn-secondary btn-sm" disabled>
                                                        <i class="ti ti-alert-triangle me-1"></i>{{ __('catalogs.katalog.file_missing') }}
                                                    </button>
                                                `}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            document.getElementById('softcopyFilesContent').innerHTML = html;
        }

        function populateAdditionalInfo(data) {
            let alternateSubjectsHtml = '';
            if (data.alternate_subject) {
                const subjects = data.alternate_subject.split(',');
                alternateSubjectsHtml = `
                                    <div class="mt-3">
                                        <strong>{{ __("catalogs.katalog.other_subject") }}:</strong>
                                        <div class="mt-2">
                                            ${subjects.map(subject => `<span class="badge bg-light text-dark me-1 mb-1">${subject.trim()}</span>`).join('')}
                                        </div>
                                    </div>
                                `;
            }

            const html = `
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table info-table">
                                            <tr>
                                                <td>{{ __("catalogs.collation") }}:</td>
                                                <td>${data.collation}</td>
                                            </tr>
                                            <tr>
                                                <td>{{ __("catalogs.katalog.language") }}:</td>
                                                <td>${data.language || 'Tidak disebutkan'}</td>
                                            </tr>
                                            ${data.editor ? `
                                            <tr>
                                                <td>{{ __("catalogs.editor") }}:</td>
                                                <td>${data.editor}</td>
                                            </tr>
                                            ` : ''}
                                            ${data.translator ? `
                                            <tr>
                                                <td>{{ __("catalogs.katalog.translator") }}:</td>
                                                <td>${data.translator}</td>
                                            </tr>
                                            ` : ''}
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table info-table">
                                            <tr>
                                                <td>{{ __("catalogs.katalog.author_type") }}:</td>
                                                <td>${data.author_type_text}</td>
                                            </tr>
                                            <tr>
                                                <td>Asal Perolehan:</td>
                                                <td>
                                                    ${data.origination == 1 ?
                    '<span class="badge bg-info">Pembelian</span>' :
                    '<span class="badge bg-success">Sumbangan</span>'
                }
                                                </td>
                                            </tr>
                                            ${data.supplier ? `
                                            <tr>
                                                <td>Pemasok:</td>
                                                <td>${data.supplier}</td>
                                            </tr>
                                            ` : ''}
                                            <tr>
                                                <td>Tanggal Masuk:</td>
                                                <td>${data.entrance_date_formatted || '-'}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                ${alternateSubjectsHtml}
                            `;

            document.getElementById('additionalInfoContent').innerHTML = html;
        }

        function populateQuickStats(data) {
            const stockSummary = data.stock_summary;

            const html = `
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h4 class="text-primary mb-1">${stockSummary.total}</h4>
                                            <small class="text-muted">Total Eksemplar</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-success mb-1">${stockSummary.available}</h4>
                                        <small class="text-muted">Tersedia</small>
                                    </div>
                                </div>
                                <hr>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h5 class="text-warning mb-1">${stockSummary.borrowed}</h5>
                                            <small class="text-muted">Dipinjam</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="text-danger mb-1">${stockSummary.damaged + stockSummary.lost}</h5>
                                        <small class="text-muted">Rusak/Hilang</small>
                                    </div>
                                </div>
                            `;

            document.getElementById('quickStatsContent').innerHTML = html;
        }

        function populateFinancialInfo(data) {
            let html = '<table class="table info-table">';

            if (data.price) {
                html += `
                                    <tr>
                                        <td>Harga Beli:</td>
                                        <td><strong>${data.price_formatted}</strong></td>
                                    </tr>
                                `;
            }

            html += `
                                <tr>
                                    <td>Biaya Sewa:</td>
                                    <td>${data.rent_cost_formatted}</td>
                                </tr>
                                <tr>
                                    <td>Denda Harian:</td>
                                    <td>${data.penalty_cost_formatted}</td>
                                </tr>
                            `;

            if (data.total_value_formatted) {
                html += `
                                    <tr>
                                        <td>Nilai Total:</td>
                                        <td><strong>${data.total_value_formatted}</strong></td>
                                    </tr>
                                `;
            }

            html += '</table>';

            document.getElementById('financialInfoContent').innerHTML = html;
        }

        function showErrorMessage(message) {
            // Replace all skeleton content with error message
            const errorHtml = `
                                <div class="text-center py-5">
                                    <i class="ti ti-alert-circle display-1 text-danger mb-3"></i>
                                    <h5 class="text-danger mb-3">Gagal Memuat Data</h5>
                                    <p class="text-muted mb-3">${message}</p>
                                    <button class="btn btn-primary" onclick="loadCatalogData()">
                                        <i class="ti ti-refresh me-1"></i>Coba Lagi
                                    </button>
                                </div>
                            `;

            document.getElementById('basicInfoContent').innerHTML = errorHtml;
            document.getElementById('stockInfoContent').innerHTML = '';
            document.getElementById('additionalInfoContent').innerHTML = '';
            document.getElementById('quickStatsContent').innerHTML = '';
            document.getElementById('financialInfoContent').innerHTML = '';
        }

        // Preview softcopy function (same as before)
        function previewSoftcopyFile(fileId, title, extension) {
            $.ajax({
                url: `/catalog/preview-softcopy-file/${fileId}`,
                type: 'GET',
                timeout: 10000,
                success: function (response) {
                    if (response.success) {
                        const fileInfo = response.data;

                        let previewContent = `
                                            <div class="text-center mb-3">
                                                <i class="ti ti-file-text display-4 text-primary"></i>
                                                <h5 class="mt-2">${title}</h5>
                                                <p class="text-muted">
                                                    Ukuran: ${fileInfo.size_formatted} | 
                                                    Format: ${extension}
                                                </p>
                                            </div>
                                        `;

                        if (extension.toLowerCase() === 'pdf') {
                            previewContent += `
                                                <div class="text-center mb-3">
                                                    <iframe src="${fileInfo.url}" width="100%" height="500px" style="border: 1px solid #ddd; border-radius: 6px;"></iframe>
                                                </div>
                                            `;
                        } else {
                            previewContent += `
                                                <div class="alert alert-info text-center">
                                                    <i class="ti ti-info-circle me-2"></i>
                                                    Preview tidak tersedia untuk format ${extension}. 
                                                    Silakan download file untuk membuka.
                                                </div>
                                            `;
                        }

                        previewContent += `
                                            <div class="text-center">
                                                <a href="${fileInfo.download_url}" class="btn btn-primary">
                                                    <i class="ti ti-download me-2"></i>Download File
                                                </a>
                                            </div>
                                        `;

                        Swal.fire({
                            title: `Preview: ${title}`,
                            html: previewContent,
                            width: '80%',
                            showCloseButton: true,
                            showConfirmButton: false,
                            customClass: {
                                container: 'swal-wide'
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function (xhr) {
                    let errorMessage = 'Gagal memuat preview file';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        // Global refresh function
        window.refreshCatalogData = function () {
            loadCatalogData();
        };
    </script>
@endsection