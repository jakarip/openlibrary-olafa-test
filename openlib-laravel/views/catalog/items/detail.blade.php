@extends('layouts/layoutMaster')

@section('title', $stockCode . ' - __('catalogs.koleksi.copy_details'))

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}">
@endsection

@section('page-style')
    <style>
        /* Loading skeleton styles */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: 4px;
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
            margin-bottom: 8px;
        }

        .skeleton-title {
            height: 24px;
            margin-bottom: 12px;
        }

        .skeleton-cover {
            width: 100%;
            height: 300px;
            border-radius: 15px;
        }

        .skeleton-block {
            height: 20px;
            margin-bottom: 10px;
        }

        .catalog-cover {
            border-radius: 15px;
            max-width: 100%;
            height: auto;
        }

        .file-card {
            transition: transform 0.2s ease;
            cursor: pointer;
        }

        .file-card:hover {
            transform: translateY(-2px);
        }

        .text-justify {
            text-align: justify;
        }

        .list-group-item {
            border: 1px solid rgba(0, 0, 0, .125);
        }

        .ebook-section {
            background: url('{{ asset('assets/img/landingpage/bg-banner-01.png')}}');
            background-size: cover;
            border-radius: 20px;
        }

        .file-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .status-badge {
            padding: 2px 6px;

        }
    </style>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('content')
    <section class="">
        <div class="container">
            <!-- Main Info Section -->
            <div class="row" id="mainInfoSection">
                <div class="col-md-2">
                    <!-- Cover Image Skeleton -->
                    <div class="skeleton skeleton-cover" id="coverSkeleton"></div>
                    <!-- Actual Cover (hidden initially) -->
                    <div id="coverContainer" style="display: none;">
                        <img class="img-fluid catalog-cover" id="catalogCover" alt="Cover" style="border-radius:15px">
                    </div>
                </div>
                <div class="col-md-10">
                    <!-- Title Skeleton -->
                    <div class="skeleton skeleton-title mb-3" id="titleSkeleton"></div>
                    <!-- Actual Title (hidden initially) -->
                    <h3 class="fw-bold mb-3" id="catalogTitle" style="display: none;"></h3>

                    <!-- Info Row Skeleton -->
                    <div class="row mb-5" id="infoRowSkeleton">
                        <div class="col-md-3">
                            <div class="skeleton skeleton-block"></div>
                        </div>
                        <div class="col-md-1">
                            <div class="skeleton skeleton-block"></div>
                        </div>
                        <div class="col-md-2">
                            <div class="skeleton skeleton-block"></div>
                        </div>
                        <div class="col-md-3">
                            <div class="skeleton skeleton-block"></div>
                        </div>
                        <div class="col-md-3">
                            <div class="skeleton skeleton-block"></div>
                        </div>
                    </div>

                    <!-- Actual Info Row (hidden initially) -->
                    <div class="row mb-5" id="infoRowContent" style="display: none;">
                        <div class="col-md-3 d-flex flex-wrap">
                            <div class="avatar me-2">
                                <img src="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo/assets/img/avatars/3.png"
                                    alt="Avatar" class="rounded-circle">
                            </div>
                            <div class="ms-1">
                                <span class="text-muted">{{ __('sbkps.author') }}</span>
                                <h6 class="mb-0 fw-bold" id="authorName"></h6>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex flex-wrap">
                            <div class="ms-2">
                                <span class="text-muted">{{ __('catalogs.koleksi.code_stock') }}</span>
                                <h6 class="mb-0 fw-bold" id="stockCode"></h6>
                            </div>
                        </div>
                        <div class="col-md-1 d-flex flex-wrap">
                            <div class="ms-1">
                                <span class="text-muted">Status</span>
                                <h6 class="mb-0 fw-bold" id="stockStatus"></h6>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex flex-wrap">
                            <div class="ms-1">
                                <span class="text-muted">{{ __('catalogs.tipe') }}</span>
                                <h6 class="mb-0 fw-bold" id="knowledgeType"></h6>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex flex-wrap">
                            <div class="ms-1">
                                <span class="text-muted">{{ __('config.holiday.location') }}</span>
                                <h6 class="mb-0 fw-bold" id="stockLocation"></h6>
                            </div>
                        </div>
                    </div>

                    <!-- Abstract Skeleton -->
                    <div class="text-justify" id="abstractSkeleton">
                        <div class="skeleton skeleton-text"></div>
                        <div class="skeleton skeleton-text"></div>
                        <div class="skeleton skeleton-text"></div>
                        <div class="skeleton skeleton-text"></div>
                    </div>

                    <!-- Actual Abstract (hidden initially) -->
                    <div class="text-justify" id="abstractContent" style="display: none;"></div>
                </div>
            </div>

            <!-- Detail Info Cards -->
            <div class="row mt-5 list-group list-group-horizontal-md" id="detailCards">
                <!-- Loading skeleton cards -->
                <div class="col-md-4 list-group-item p-4 text-heading" id="stockInfoSkeleton">
                    <div class="skeleton skeleton-title mb-4"></div>
                    <div class="skeleton skeleton-text mb-2"></div>
                    <div class="skeleton skeleton-text mb-2"></div>
                    <div class="skeleton skeleton-text mb-2"></div>
                    <div class="skeleton skeleton-text"></div>
                </div>
                <div class="col-md-4 list-group-item p-4 text-heading" id="catalogInfoSkeleton">
                    <div class="skeleton skeleton-title mb-4"></div>
                    <div class="skeleton skeleton-text mb-2"></div>
                    <div class="skeleton skeleton-text mb-2"></div>
                    <div class="skeleton skeleton-text"></div>
                </div>
                <div class="col-md-4 list-group-item p-4 text-heading" id="categorySkeleton">
                    <div class="skeleton skeleton-title mb-4"></div>
                    <div class="skeleton skeleton-text mb-2"></div>
                    <div class="skeleton skeleton-text mb-2"></div>
                    <div class="skeleton skeleton-text"></div>
                </div>
            </div>

            <!-- Actual Detail Cards (hidden initially) -->
            <div class="row mt-5 list-group list-group-horizontal-md" id="detailCardsContent" style="display: none;">
                <!-- Stock Info Card -->
                <div class="col-md-4 list-group-item p-4 text-heading">
                    <h5 class="m-0 me-2 pt-1 mb-4 d-flex align-items-center fw-bold text-danger">
                        <i class="ti ti-package ti-md ms-n1 me-2"></i> {{ __('catalogs.koleksi.copy_details') }}
                    </h5>
                    <div class="row">
                        <div class="col-md-3">{{ __('config.classification.input.code') }}</div>
                        <div class="col-md-9 fw-semibold" id="detailStockCode"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">RFID</div>
                        <div class="col-md-9 fw-semibold" id="stockRfid"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">Status</div>
                        <div class="col-md-9 fw-semibold" id="detailStockStatus"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">{{ __('catalogs.entry_date') }}</div>
                        <div class="col-md-9 fw-semibold" id="entranceDate"></div>
                    </div>
                </div>

                <!-- Catalog Info Card -->
                <div class="col-md-4 list-group-item p-4 text-heading">
                    <h5 class="m-0 me-2 pt-1 mb-4 d-flex align-items-center fw-bold text-danger">
                        <i class="ti ti-books ti-md ms-n1 me-2"></i> {{ __('catalogs.catalog_info') }}
                    </h5>
                    <div class="row">
                        <div class="col-md-3">{{ __('config.classification.input.code') }}</div>
                        <div class="col-md-9 fw-semibold" id="catalogCode"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">ISBN</div>
                        <div class="col-md-9 fw-semibold" id="isbn"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">{{ __('catalogs.katalog.langauge') }}</div>
                        <div class="col-md-9 fw-semibold" id="language"></div>
                    </div>
                </div>

                <!-- Category Card -->
                <div class="col-md-4 list-group-item p-4 text-heading">
                    <h5 class="m-0 me-2 pt-1 mb-4 d-flex align-items-center fw-bold text-danger">
                        <i class="ti ti-category ti-md ms-n1 me-2"></i> {{ __('catalogs.category') }}
                    </h5>
                    <div class="row">
                        <div class="col-md-3">{{ __('catalogs.classification') }}</div>
                        <div class="col-md-9 fw-semibold" id="detailClassification"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">Subject</div>
                        <div class="col-md-9 fw-semibold" id="subject"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">Alt Subject</div>
                        <div class="col-md-9 fw-semibold" id="altSubject"></div>
                    </div>
                </div>
            </div>

            <!-- Second Row Cards -->
            <div class="row mt-3 list-group list-group-horizontal-md" id="secondRowCards">
                <!-- Loading skeleton cards -->
                <div class="col-md-4 list-group-item p-4 text-heading" id="publisherSkeleton">
                    <div class="skeleton skeleton-title mb-4"></div>
                    <div class="skeleton skeleton-text mb-2"></div>
                    <div class="skeleton skeleton-text mb-2"></div>
                    <div class="skeleton skeleton-text"></div>
                </div>
                <div class="col-md-4 list-group-item p-4 text-heading" id="circulationSkeleton">
                    <div class="skeleton skeleton-title mb-4"></div>
                    <div class="skeleton skeleton-text mb-2"></div>
                    <div class="skeleton skeleton-text mb-2"></div>
                    <div class="skeleton skeleton-text"></div>
                </div>
                <div class="col-md-4 list-group-item p-4 text-heading" id="locationSkeleton">
                    <div class="skeleton skeleton-title mb-4"></div>
                    <div class="skeleton skeleton-text mb-2"></div>
                    <div class="skeleton skeleton-text mb-2"></div>
                    <div class="skeleton skeleton-text"></div>
                </div>
            </div>

            <!-- Actual Second Row Cards (hidden initially) -->
            <div class="row mt-3 list-group list-group-horizontal-md" id="secondRowCardsContent" style="display: none;">
                <!-- Publisher Card -->
                <div class="col-md-4 list-group-item p-4 text-heading">
                    <h5 class="m-0 me-2 pt-1 mb-4 d-flex align-items-center fw-bold text-danger">
                        <i class="ti ti-speakerphone ti-md ms-n1 me-2"></i> {{ __('catalogs.publisher') }}
                    </h5>
                    <div class="row">
                        <div class="col-md-3">{{ __('catalogs.publisher') }}</div>
                        <div class="col-md-9 fw-semibold" id="publisherName"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">{{ __('catalogs.katalog.city') }}</div>
                        <div class="col-md-9 fw-semibold" id="publisherCity"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">{{ __('catalogs.year') }}</div>
                        <div class="col-md-9 fw-semibold" id="publishedYear"></div>
                    </div>
                </div>

                <!-- Circulation Card -->
                <div class="col-md-4 list-group-item p-4 text-heading">
                    <h5 class="m-0 me-2 pt-1 mb-4 d-flex align-items-center fw-bold text-danger">
                        <i class="ti ti-bookmarks ti-md ms-n1 me-2"></i> {{ __('catalogs.koleksi.administration') }}
                    </h5>
                    <div class="row">
                        <div class="col-md-3">{{ __('catalogs.rental_cost') }}</div>
                        <div class="col-md-9 fw-semibold" id="supplier"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">{{ __('catalogs.return.fine_day') }}</div>
                        <div class="col-md-9 fw-semibold" id="stockPrice"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">{{ __('catalogs.koleksi.source') }}</div>
                        <div class="col-md-9 fw-semibold" id="origination"></div>
                    </div>
                </div>

                <!-- Location Card -->
                <div class="col-md-4 list-group-item p-4 text-heading">
                    <h5 class="m-0 me-2 pt-1 mb-4 d-flex align-items-center fw-bold text-danger">
                        <i class="ti ti-map-pin ti-md ms-n1 me-2"></i> {{ __('catalogs.koleksi.copy_location') }}
                    </h5>
                    <div id="locationInfo">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- E-Book/Flippingbook Section (Dynamic) -->
            <div class="row" id="ebookSection" style="display: none;">
                <div class="col-md-12 pricing-free-trial mt-5 ebook-section">
                    <div class="position-relative">
                        <div
                            class="d-flex justify-content-between flex-column-reverse flex-lg-row align-items-center py-4 px-3">
                            <div class="text-center text-lg-start me-5 ms-3">
                                <h4 class="text-danger mb-1 fw-bold">{{ __('catalogs.katalog.download') }} File E-Book / Flippingbook</h4>
                                <p class="mb-1">{{ __('catalogs.katalog.ebook_info') }}</p>

                                <div class="row mt-3" id="ebookFilesContainer">
                                    <!-- E-book files will be populated here -->
                                </div>
                            </div>
                            <!-- image -->
                            <div class="text-center ms-5">
                                <img src="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo/assets/img/illustrations/girl-sitting-with-laptop.png"
                                    class="img-fluid" alt="E-Book Image" width="500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Softcopy Files Section -->
            <div class="row" id="softcopySection" style="display: none;">
                <div class="col-md-12">
                    <h4 class="text-danger mb-1 fw-bold">Softcopy Files</h4>
                    <p class="mb-1">{{ __('catalogs.katalog.file_digital') }}</p>
                </div>
            </div>
            <div class="row" id="softcopyFilesContainer">
                <!-- Softcopy files will be populated here -->
            </div>
        </div>
    </section>
@endsection

@section('page-script')
    <script>
        // Define stock data dari server
        const stockData = {
            id: {{ $id }},
            code: '{{ $code }}'
        };

        document.addEventListener('DOMContentLoaded', function () {
            // Load data via AJAX
            loadStockData();
        });

        function loadStockData() {
            const ajaxUrl = `/catalog/items/detail`;

            $.ajax({
                url: ajaxUrl,
                method: 'GET',
                data: { id: stockData.id },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                timeout: 15000,
                success: function (response) {
                    if (response.success && response.data) {
                        populateAllContent(response.data);
                    } else {
                        showErrorMessage('Gagal memuat data eksemplar: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', { xhr, status, error });

                    let errorMsg = 'Gagal memuat data eksemplar';
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
            // Hide skeletons and show content
            hideSkeletons();

            // Populate main info
            populateMainInfo(data);

            // Populate detail cards
            populateDetailCards(data);

            // Handle softcopy files
            handleSoftcopyFiles(data);

            console.log('All content loaded successfully!');
        }

        function hideSkeletons() {
            // Hide skeleton elements
            document.getElementById('coverSkeleton').style.display = 'none';
            document.getElementById('titleSkeleton').style.display = 'none';
            document.getElementById('infoRowSkeleton').style.display = 'none';
            document.getElementById('abstractSkeleton').style.display = 'none';
            document.getElementById('stockInfoSkeleton').style.display = 'none';
            document.getElementById('catalogInfoSkeleton').style.display = 'none';
            document.getElementById('categorySkeleton').style.display = 'none';
            document.getElementById('publisherSkeleton').style.display = 'none';
            document.getElementById('circulationSkeleton').style.display = 'none';
            document.getElementById('locationSkeleton').style.display = 'none';

            // Show actual content
            document.getElementById('coverContainer').style.display = 'block';
            document.getElementById('catalogTitle').style.display = 'block';
            document.getElementById('infoRowContent').style.display = 'flex';
            document.getElementById('abstractContent').style.display = 'block';
            document.getElementById('detailCardsContent').style.display = 'flex';
            document.getElementById('secondRowCardsContent').style.display = 'flex';
        }

        function populateMainInfo(data) {
            // Cover (dari catalog)
            const coverImg = document.getElementById('catalogCover');
            const coverUrl = data.knowledge_item?.cover_url || '{{ asset("assets/img/default-book-cover.jpg") }}';
            const defaultCover = '{{ asset("assets/img/default-book-cover.jpg") }}';

            coverImg.src = coverUrl;
            coverImg.alt = `Cover ${data.knowledge_item?.title || 'Unknown'}`;
            coverImg.onerror = function () {
                this.onerror = null;
                this.src = defaultCover;
            };

            // Title (dari catalog)
            document.getElementById('catalogTitle').textContent = data.knowledge_item?.title || 'Unknown Title';

            // Author (dari catalog)
            document.getElementById('authorName').textContent = data.knowledge_item?.author || 'Unknown Author';

            // Stock Code
            document.getElementById('stockCode').textContent = data.code || 'N/A';

            // Stock Status
            const statusBadge = generateStatusBadge(data.status, data.status_label);
            document.getElementById('stockStatus').innerHTML = statusBadge;

            // Knowledge Type
            document.getElementById('knowledgeType').textContent = data.knowledge_type?.name || 'N/A';

            // Stock Location
            document.getElementById('stockLocation').textContent = data.item_location?.name || 'N/A';

            // Abstract (dari catalog)
            if (data.knowledge_item?.abstract_content && data.knowledge_item.abstract_content.trim()) {
                document.getElementById('abstractContent').innerHTML = data.knowledge_item.abstract_content.replace(/\n/g, '<br>');
            } else {
                document.getElementById('abstractContent').innerHTML = '<p class="text-muted">Tidak ada abstrak tersedia.</p>';
            }
        }

        function populateDetailCards(data) {
            // Stock Info details
            document.getElementById('detailStockCode').textContent = data.code || 'N/A';
            document.getElementById('stockRfid').textContent = data.rfid || 'Tidak ada';
            document.getElementById('detailStockStatus').innerHTML = generateStatusBadge(data.status, data.status_label);
            document.getElementById('entranceDate').textContent = data.entrance_date_formatted || 'N/A';

            // Catalog info
            document.getElementById('catalogCode').textContent = data.knowledge_item?.code || 'N/A';
            document.getElementById('isbn').textContent = data.knowledge_item?.isbn || '-';
            document.getElementById('language').textContent = data.knowledge_item?.language || 'Tidak disebutkan';

            // Category
            const detailClassification = data.knowledge_item?.classification ?
                `${data.knowledge_item.classification.code} - ${data.knowledge_item.classification.name}` : '-';
            document.getElementById('detailClassification').textContent = detailClassification;
            document.getElementById('subject').textContent = data.knowledge_item?.subject_name || '-';
            document.getElementById('altSubject').textContent = data.knowledge_item?.alternate_subject || '-';

            // Publisher
            document.getElementById('publisherName').textContent = data.knowledge_item?.publisher_name || '-';
            document.getElementById('publisherCity').textContent = data.knowledge_item?.publisher_city || '-';
            document.getElementById('publishedYear').textContent = data.knowledge_item?.published_year || '-';

            // Administration
            document.getElementById('supplier').textContent = data.supplier || '-';
            document.getElementById('stockPrice').textContent = data.price ? 'Rp ' + new Intl.NumberFormat('id-ID').format(data.price) : '-';
            document.getElementById('origination').textContent = data.origination === 1 ? 'Pembelian' : data.origination === 2 ? 'Sumbangan' : '-';

            // Location
            const locationInfo = document.getElementById('locationInfo');

            // Handle multiple email addresses separated by semicolon
            let emailContent = '';
            if (data.item_location?.email && data.item_location.email.includes(';')) {
                const emails = data.item_location.email.split(';');
                emails.forEach(email => {
                    const trimmedEmail = email.trim();
                    if (trimmedEmail) {
                        emailContent += `<div><i class="ti ti-sm me-3 ti-mail"></i> ${trimmedEmail}</div>`;
                    }
                });
            } else {
                emailContent = `<div><i class="ti ti-sm me-3 ti-mail"></i> ${data.item_location?.email || 'N/A'}</div>`;
            }

            locationInfo.innerHTML = `
                                <div>
                                    <i class="ti ti-building-bank ti-sm me-3"></i> ${data.item_location?.name || 'N/A'}
                                </div>
                                <div>
                                    <div><i class="ti ti-sm me-3 ti-phone-call"></i> ${data.item_location?.phone || 'N/A'}</div>
                                </div>
                                ${emailContent}
                            `;
        }

        function handleSoftcopyFiles(data) {
            if (!data.knowledge_item?.softcopy_files || data.knowledge_item.softcopy_files.length === 0) {
                return; // No softcopy files
            }

            // Separate E-book/Flippingbook files from other softcopy files
            const ebookTypes = ['ebook', 'flipbook', 'e-book', 'e-book.pdf'];
            const ebookFiles = [];
            const otherFiles = [];

            data.knowledge_item.softcopy_files.forEach(file => {
                const fileName = file.filename.toLowerCase();
                const uploadTypeName = (file.upload_type.name || '').toLowerCase();
                const uploadTypeTitle = (file.upload_type.title || '').toLowerCase();

                // Check if it's an ebook/flipbook file
                if (ebookTypes.some(type =>
                    fileName.includes(type) ||
                    uploadTypeName.includes(type) ||
                    uploadTypeTitle.includes(type)
                )) {
                    ebookFiles.push(file);
                } else {
                    otherFiles.push(file);
                }
            });

            // Show E-book section if there are ebook files
            if (ebookFiles.length > 0) {
                showEbookSection(ebookFiles);
            }

            // Show Softcopy section if there are other files
            if (otherFiles.length > 0) {
                showSoftcopySection(otherFiles);
            }
        }

        function showEbookSection(ebookFiles) {
            const ebookSection = document.getElementById('ebookSection');
            const container = document.getElementById('ebookFilesContainer');

            ebookSection.style.display = 'block';

            let html = '';
            ebookFiles.forEach(file => {
                const downloadCountText = file.download_count_text || 'belum pernah diunduh';
                html += `
                                    <div class="col-md-4">
                                        <div class="card card-body file-card" onclick="downloadFile('${file.download_url}')">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar flex-shrink-0 me-3">
                                                    <span class="avatar-initial rounded bg-label-primary">
                                                        <i class="ti ti-file-text ti-md"></i>
                                                    </span>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-0">${file.upload_type.title} (${file.filename})</h6>
                                                    <small class="text-truncate text-muted">${downloadCountText}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
            });

            container.innerHTML = html;
        }

        function showSoftcopySection(otherFiles) {
            const softcopySection = document.getElementById('softcopySection');
            const container = document.getElementById('softcopyFilesContainer');

            softcopySection.style.display = 'block';

            let html = '';
            otherFiles.forEach(file => {
                const downloadCountText = file.download_count_text || '{{ __"catalogs.koleksi.never_download") }}';

                html += `
                                    <div class="col-md-4">
                                        <div class="card mb-4 h-75 file-card" onclick="downloadFile('${file.download_url}')">
                                            <div class="card-body d-flex justify-content-between align-items-top">
                                                <div class="mb-0 me-3">
                                                    <div class="text-body fw-semibold mb-0">${file.upload_type.title} (${file.filename})</div>
                                                    <small class="text-muted">${downloadCountText}</small>
                                                </div>
                                                <div class="card-icon">
                                                    <i class="ti ti-file-text ti-lg text-primary"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
            });

            container.innerHTML = html;
        }

        function generateStatusBadge(status, label) {
            let badgeClass = 'bg-secondary';

            switch (parseInt(status)) {
                case 1: // Available
                case 6: // Lost Replaced
                    badgeClass = 'bg-success';
                    break;
                case 2: // Borrowed
                    badgeClass = 'bg-warning text-dark';
                    break;
                case 3: // Damaged
                    badgeClass = 'bg-danger';
                    break;
                case 4: // Lost
                    badgeClass = 'bg-dark';
                    break;
                case 5: // Expired
                    badgeClass = 'bg-secondary';
                    break;
                case 7: // Processing
                    badgeClass = 'bg-info';
                    break;
                case 8: // Reserve
                    badgeClass = 'bg-primary';
                    break;
                case 9: // Weeding
                    badgeClass = 'bg-secondary';
                    break;
            }

            return `<span class="badge ${badgeClass} status-badge">${label || 'Unknown'}</span>`;
        }

        function downloadFile(downloadUrl) {
            window.open(downloadUrl, '_blank');
        }

        function showErrorMessage(message) {
            // Replace main content with error message
            document.querySelector('.container').innerHTML = `
                                <div class="text-center py-5">
                                    <i class="ti ti-alert-circle display-1 text-danger mb-3"></i>
                                    <h5 class="text-danger mb-3">{{ __('catalogs.koleksi.fail_load_data') }}</h5>
                                    <p class="text-muted mb-3">${message}</p>
                                    <button class="btn btn-primary" onclick="window.location.reload()">
                                        <i class="ti ti-refresh me-1"></i>{{ __('catalogs.try_again') }}
                                    </button>
                                </div>
                            `;
        }

        // Global refresh function
        window.refreshStockData = function () {
            loadStockData();
        };
    </script>
@endsection