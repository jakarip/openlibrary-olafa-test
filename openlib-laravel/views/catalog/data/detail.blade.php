@extends('layouts/layoutMaster')

@section('title', $catalogTitle . ' - ' . __('catalogs.katalog.detail'))

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
            background: url("{{ asset('assets/img/landingpage/bg-banner-01.png') }}");
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

        /* File item styles */
        .file-item-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .file-item-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .file-item-card.restricted {
            background-color: #f8f9fa;
            opacity: 0.8;
        }

        .file-info {
            flex: 1;
        }

        .file-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .file-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .file-name {
            color: #666;
            font-size: 0.875rem;
            margin-bottom: 5px;
        }

        .file-stats {
            color: #999;
            font-size: 0.8rem;
        }

        .btn-file-action {
            min-width: 140px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .file-item-card {
                padding: 15px;
            }

            .file-actions {
                margin-top: 15px;
                width: 100%;
            }

            .btn-file-action {
                flex: 1;
                min-width: auto;
            }
        }

        /* Text truncate for long file names */
        .text-truncate-custom {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 100%;
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
                        <div class="col-md-1 d-flex flex-wrap">
                            <div class="ms-1">
                                <span class="text-muted">{{ __('config.classification.input.code') }}</span>
                                <h6 class="mb-0 fw-bold" id="catalogCode"></h6>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex flex-wrap">
                            <div class="ms-1">
                                <span class="text-muted">{{ __('catalogs.classification') }}</span>
                                <h6 class="mb-0 fw-bold" id="classificationInfo"></h6>
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
                                <span class="text-muted">{{ __('catalogs.availability') }}</span>
                                <h6 class="mb-0 fw-bold" id="stockAvailability"></h6>
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
                <div class="col-md-4 list-group-item p-4 text-heading" id="authorSkeleton">
                    <div class="skeleton skeleton-title mb-4"></div>
                    <div class="skeleton skeleton-text mb-2"></div>
                    <div class="skeleton skeleton-text mb-2"></div>
                    <div class="skeleton skeleton-text mb-2"></div>
                    <div class="skeleton skeleton-text"></div>
                </div>
                <div class="col-md-4 list-group-item p-4 text-heading" id="bookInfoSkeleton">
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
                <!-- Author Card -->
                <div class="col-md-4 list-group-item p-4 text-heading">
                    <h5 class="m-0 me-2 pt-1 mb-4 d-flex align-items-center fw-bold text-danger">
                        <i class="ti ti-user-star ti-md ms-n1 me-2"></i> {{ __('sbkps.author') }}
                    </h5>
                    <div class="row">
                        <div class="col-md-3">{{ __('sbkps.author') }}</div>
                        <div class="col-md-9 fw-semibold" id="detailAuthor"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">{{ __('catalogs.tipe') }}</div>
                        <div class="col-md-9 fw-semibold" id="authorType"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">{{ __('catalogs.editor') }}</div>
                        <div class="col-md-9 fw-semibold" id="editor"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">{{ __('catalogs.katalog.translator') }}</div>
                        <div class="col-md-9 fw-semibold" id="translator"></div>
                    </div>
                </div>

                <!-- Book Info Card -->
                <div class="col-md-4 list-group-item p-4 text-heading">
                    <h5 class="m-0 me-2 pt-1 mb-4 d-flex align-items-center fw-bold text-danger">
                        <i class="ti ti-books ti-md ms-n1 me-2"></i> {{ __('catalogs.katalog.book_information') }}
                    </h5>
                    <div class="row">
                        <div class="col-md-3">ISBN</div>
                        <div class="col-md-9 fw-semibold" id="isbn"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">{{ __('catalogs.collation') }}</div>
                        <div class="col-md-9 fw-semibold" id="collation"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">{{ __('catalogs.katalog.language') }}</div>
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
                        <i class="ti ti-bookmarks ti-md ms-n1 me-2"></i> {{ __('catalogs.circulation') }}
                    </h5>
                    <div class="row">
                        <div class="col-md-3">{{ __('catalogs.rental_cost') }}</div>
                        <div class="col-md-9 fw-semibold" id="rentCost"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">{{ __('catalogs.return.fine_day') }}</div>
                        <div class="col-md-9 fw-semibold" id="penaltyCost"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">{{ __('catalogs.tipe') }}</div>
                        <div class="col-md-9 fw-semibold" id="circulationType"></div>
                    </div>
                </div>

                <!-- Location Card -->
                <div class="col-md-4 list-group-item p-4 text-heading">
                    <h5 class="m-0 me-2 pt-1 mb-4 d-flex align-items-center fw-bold text-danger">
                        <i class="ti ti-map-pin ti-md ms-n1 me-2"></i> {{ __('catalogs.book_location') }}
                    </h5>
                    <div id="locationInfo">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- E-Book/Flippingbook Section (Combined All Files) -->
            <div class="row" id="ebookSection" style="display: none;">
                <div class="col-md-12 pricing-free-trial mt-5 ebook-section">
                    <div class="position-relative">
                        <div
                            class="d-flex justify-content-between flex-column-reverse flex-lg-row align-items-center py-4 px-3">
                            <div class="text-center text-lg-start me-5 ms-3 w-100">
                                <h4 class="text-danger mb-1 fw-bold">{{ __('catalogs.katalog.download') }} File E-Book /
                                    Flippingbook</h4>
                                <p class="mb-3">{{ __('catalogs.katalog.ebook_info') }}</p>

                                <div class="row" id="allFilesContainer">
                                    <!-- All files will be populated here vertically -->
                                </div>
                            </div>
                            <!-- image -->
                            <div class="text-center ms-5 d-none d-lg-block">
                                <img src="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo/assets/img/illustrations/girl-sitting-with-laptop.png"
                                    class="img-fluid" alt="E-Book Image" width="500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page-script')
    <script>
        // Define catalog data dari server
        const catalogData = {
            id: "{{ $catalogId }}",
            slug: "{{ $catalogSlug }}",
            code: "{{ $catalogCode }}",
            title: "{{ $catalogTitle }}"
        };

        document.addEventListener('DOMContentLoaded', function () {
            // Load data via AJAX
            loadCatalogData();
        });

        function loadCatalogData() {
            const ajaxUrl = `/catalog/detail/${catalogData.id}/${catalogData.slug}`;

            // Tampilkan overlay loading sebelum AJAX
            $('#loading').show();

            $.ajax({
                url: ajaxUrl,
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                timeout: 15000,
                success: function (response) {
                    // Sembunyikan overlay loading setelah sukses
                    $('#loading').hide();
                    if (response.success && response.data) {
                        populateAllContent(response.data);
                    } else {
                        showErrorMessage('Gagal memuat data katalog: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function (xhr, status, error) {
                    // Sembunyikan overlay loading setelah error
                    $('#loading').hide();
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
            // Hide skeletons and show content
            hideSkeletons();

            // Populate main info
            populateMainInfo(data);

            // Populate detail cards
            populateDetailCards(data);

            // Handle all files (combined)
            handleAllFiles(data);

            console.log('All content loaded successfully!');
        }

        function hideSkeletons() {
            // Hide skeleton elements
            document.getElementById('coverSkeleton').style.display = 'none';
            document.getElementById('titleSkeleton').style.display = 'none';
            document.getElementById('infoRowSkeleton').style.display = 'none';
            document.getElementById('abstractSkeleton').style.display = 'none';
            document.getElementById('authorSkeleton').style.display = 'none';
            document.getElementById('bookInfoSkeleton').style.display = 'none';
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
            // Cover
            const coverImg = document.getElementById('catalogCover');
            const coverUrl = data.cover_url || '{{ asset("assets/img/default-book-cover.jpg") }}';
            const defaultCover = '{{ asset("assets/img/default-book-cover.jpg") }}';

            coverImg.src = coverUrl;
            coverImg.alt = `Cover ${data.title}`;
            coverImg.onerror = function () {
                this.onerror = null;
                this.src = defaultCover;
            };

            // Title
            document.getElementById('catalogTitle').textContent = data.title;

            // Author
            document.getElementById('authorName').textContent = data.author;

            // Code
            document.getElementById('catalogCode').textContent = data.code;

            // Classification
            const classificationText = data.classification ?
                `${data.classification.code} - ${data.classification.name}` : 'N/A';
            document.getElementById('classificationInfo').textContent = classificationText;

            // Knowledge Type
            document.getElementById('knowledgeType').textContent = data.knowledge_type_name || 'N/A';

            // Stock Availability
            const stockSummary = data.stock_summary;
            const availabilityText = `${stockSummary.available} Tersedia dari ${stockSummary.total} Eksemplar`;
            document.getElementById('stockAvailability').innerHTML =
                `<span class="fw-bold text-primary">${stockSummary.available}</span> Tersedia dari <span class="fw-bold text-primary">${stockSummary.total}</span> Eksemplar`;

            // Abstract
            if (data.abstract_content && data.abstract_content.trim()) {
                document.getElementById('abstractContent').innerHTML = data.abstract_content.replace(/\n/g, '<br>');
            } else {
                document.getElementById('abstractContent').innerHTML = '<p class="text-muted">Tidak ada abstrak tersedia.</p>';
            }
        }

        function populateDetailCards(data) {
            // Author details
            document.getElementById('detailAuthor').textContent = data.author;
            document.getElementById('authorType').textContent = data.author_type_text || 'N/A';
            document.getElementById('editor').textContent = data.editor || '-';
            document.getElementById('translator').textContent = data.translator || '-';

            // Book info
            document.getElementById('isbn').textContent = data.isbn || '-';
            document.getElementById('collation').textContent = data.collation || '-';
            document.getElementById('language').textContent = data.language || 'Tidak disebutkan';

            // Category
            const detailClassification = data.classification ?
                `${data.classification.code} - ${data.classification.name}` : '-';
            document.getElementById('detailClassification').textContent = detailClassification;
            document.getElementById('subject').textContent = data.subject_name || '-';
            document.getElementById('altSubject').textContent = data.alternate_subject || '-';

            // Publisher
            document.getElementById('publisherName').textContent = data.publisher_name || '-';
            document.getElementById('publisherCity').textContent = data.publisher_city || '-';
            document.getElementById('publishedYear').textContent = data.published_year || '-';

            // Circulation
            document.getElementById('rentCost').textContent = data.rent_cost_formatted || 'IDR 0,00';
            document.getElementById('penaltyCost').textContent = data.penalty_cost_formatted || 'IDR 0,00';
            document.getElementById('circulationType').textContent = data.knowledge_type_name || 'N/A';

            // Location
            const locationInfo = document.getElementById('locationInfo');

            // Handle multiple email addresses separated by semicolon
            let emailContent = '';
            if (data.location_email && data.location_email.includes(';')) {
                // Split emails by semicolon and create separate entries for each
                const emails = data.location_email.split(';');
                emails.forEach(email => {
                    const trimmedEmail = email.trim();
                    if (trimmedEmail) {
                        emailContent += `<div><i class="ti ti-sm me-3 ti-mail"></i> ${trimmedEmail}</div>`;
                    }
                });
            } else {
                // Single email or no email
                emailContent = `<div><i class="ti ti-sm me-3 ti-mail"></i> ${data.location_email || 'N/A'}</div>`;
            }

            locationInfo.innerHTML = `
                                    <div>
                                        <i class="ti ti-building-bank ti-sm me-3"></i> ${data.location_name || 'N/A'}
                                    </div>
                                    <div>
                                        <div><i class="ti ti-sm me-3 ti-phone-call"></i> ${data.location_phone || 'N/A'}</div>
                                    </div>
                                    ${emailContent}
                                `;
        }

        function handleAllFiles(data) {
            if (!data.softcopy_files || data.softcopy_files.length === 0) {
                return; // No files available
            }

            // Show the ebook section (which now contains all files)
            document.getElementById('ebookSection').style.display = 'block';
            const container = document.getElementById('allFilesContainer');

            let html = '';

            // Sort files alphabetically by filename
            const sortedFiles = data.softcopy_files.sort((a, b) => {
                return a.filename.toLowerCase().localeCompare(b.filename.toLowerCase());
            });

            // Display all files vertically
            sortedFiles.forEach(file => {
                // Check file permissions
                const canRead = file.can_read;
                const canDownload = file.can_download;

                // Check if file is PDF for flipbook capability
                const fileName = file.filename.toLowerCase();
                const fileExt = fileName.split('.').pop();
                const isPDF = fileExt === 'pdf';

                // Determine icon based on file type
                let fileIcon = 'ti-file';
                if (isPDF) {
                    fileIcon = 'ti-file-text';
                } else if (['doc', 'docx'].includes(fileExt)) {
                    fileIcon = 'ti-file-description';
                } else if (['xls', 'xlsx'].includes(fileExt)) {
                    fileIcon = 'ti-file-spreadsheet';
                } else if (fileName.includes('flipbook') || fileName.includes('ebook') || fileName.includes('e-book')) {
                    fileIcon = 'ti-book-2';
                }

                // Stats
                const downloadCount = file.download_count || 0;
                const viewCount = file.view_count || 0;
                const statusText = `{{ __("catalogs.katalog.visited") }} ${viewCount}x, {{ __("catalogs.katalog.downloaded") }} ${downloadCount}x`;

                // Check if has flipbook capability (PDF and can read)
                const hasFlipbook = isPDF && file.flipbook_url && canRead;

                // Card class based on access
                const cardClass = (!canRead && !canDownload) ? 'file-item-card restricted' : 'file-item-card';

                html += `
                        <div class="col-12">
                            <div class="${cardClass}">
                                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center">
                                    <div class="d-flex align-items-center flex-grow-1 mb-3 mb-md-0">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded ${(!canRead && !canDownload) ? 'bg-label-secondary' : 'bg-label-primary'}">
                                                <i class="ti ${fileIcon} ti-md"></i>
                                            </span>
                                        </div>
                                        <div class="file-info">
                                            <h6 class="file-title mb-1 text-truncate-custom">${file.upload_type.title || 'File'}</h6>
                                            <div class="file-name text-truncate-custom">${file.filename}</div>
                                            <div class="file-stats">${statusText}</div>
                                            ${file.upload_date_formatted ? `<div class="file-stats">Upload: ${file.upload_date_formatted}</div>` : ''}

                                            <!-- Keterangan akses -->
                                            ${!canRead && !canDownload ?
                        '<div class="text-danger small mt-1"><i class="ti ti-lock me-1"></i>{{ __("catalogs.katalog.no_access") }}</div>' :
                        canRead && !canDownload ?
                            '<div class="text-warning small mt-1"><i class="ti ti-eye me-1"></i>{{ __("catalogs.katalog.view_only") }}</div>' :
                            canDownload ?
                                '<div class="text-success small mt-1"><i class="ti ti-check me-1"></i>{{ __("catalogs.katalog.can_both") }}</div>' :
                                ''
                    }
                                        </div>
                                    </div>

                                    <div class="file-actions">
                    `;

                // Add action buttons based on permissions
                if (!canRead && !canDownload) {
                    // No access at all - single disabled button
                    html += `
                            <button class="btn btn-sm btn-label-secondary btn-file-action" disabled>
                                <i class="ti ti-lock me-1"></i>{{ __("catalogs.katalog.no_access_desc") }}
                            </button>
                        `;
                } else {
                    // Has some access

                    // Show flipbook button for PDF files with read access
                    if (hasFlipbook) {
                        html += `
                                <button class="btn btn-sm btn-label-primary btn-file-action" onclick="openFlipbook('${file.flipbook_url}')">
                                    <i class="ti ti-book-2 me-1"></i>{{ __("catalogs.katalog.view_flipbook") }}
                                </button>
                            `;
                    }

                    // Show download button
                    if (canDownload && file.download_url) {
                        html += `
                                <button class="btn btn-sm btn-label-success btn-file-action" onclick="downloadFile('${file.download_url}')">
                                    <i class="ti ti-download me-1"></i>{{ __("catalogs.katalog.downloaded") }} File
                                </button>
                            `;
                    } else if (!canDownload && canRead) {
                        // Can read but can't download - show locked download button dengan teks yang jelas
                        html += `
                                <button class="btn btn-sm btn-label-danger btn-file-action" disabled>
                                    <i class="ti ti-lock me-1"></i>{{ __("catalogs.katalog.cant_download") }}
                                </button>
                            `;
                    }
                }

                html += `
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
            });

            container.innerHTML = html;
        }

        function openFlipbook(flipbookUrl) {
            try {
                // Open flipbook in new tab
                const newWindow = window.open(flipbookUrl, '_blank', 'noopener,noreferrer');

                // Log view attempt (async)
                fetch('/catalog/log-access', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify({
                        catalog_id: catalogData.id,
                        action: 'view_flipbook'
                    })
                }).catch(error => console.error('Failed to log access:', error));

                if (!newWindow) {
                    // Fallback if popup blocked
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: '{{ __("catalogs.katalog.pop_block") }}',
                            text: '{{ __("catalogs.katalog.allow_pop") }}',
                            showCancelButton: true,
                            confirmButtonText: '{{ __("catalogs.katalog.open_tab") }}',
                            cancelButtonText: '{{ __("common.cancel") }}'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = flipbookUrl;
                            }
                        });
                    } else {
                        // Simple fallback
                        if (confirm('{{ __("catalogs.katalog.pop_block") }}. {{ __("catalogs.katalog.open_tab") }}?')) {
                            window.location.href = flipbookUrl;
                        }
                    }
                }
            } catch (error) {
                console.error('Error opening flipbook:', error);
                showToast('error', 'Gagal membuka flipbook');
            }
        }

        function downloadFile(downloadUrl) {
            try {
                // Create temporary link for download
                const link = document.createElement('a');
                link.href = downloadUrl;
                link.target = '_blank';
                link.rel = 'noopener,noreferrer';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Log download attempt
                fetch('/catalog/log-access', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify({
                        catalog_id: catalogData.id,
                        action: 'download_file'
                    })
                }).catch(error => console.error('Failed to log access:', error));

                showToast('success', 'Download dimulai...');
            } catch (error) {
                console.error('Download error:', error);
                showToast('error', 'Gagal mengunduh file');
            }
        }

        function showToast(type, message) {
            if (typeof Swal !== 'undefined') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });

                Toast.fire({
                    icon: type,
                    title: message
                });
            } else {
                alert(message);
            }
        }

        function showErrorMessage(message) {
            // Replace main content with error message
            document.querySelector('.container').innerHTML = `
                                    <div class="text-center py-5">
                                        <i class="ti ti-alert-circle display-1 text-danger mb-3"></i>
                                        <h5 class="text-danger mb-3">{{ __("common.failed") }}</h5>
                                        <p class="text-muted mb-3">${message}</p>
                                        <button class="btn btn-primary" onclick="window.location.reload()">
                                            <i class="ti ti-refresh me-1"></i>{{ __("catalogs.try_again") }}
                                        </button>
                                    </div>
                                `;
        }

        // Global refresh function
        window.refreshCatalogData = function () {
            loadCatalogData();
        };
    </script>
@endsection