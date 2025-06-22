@extends('layouts/layoutMaster')

@section('title', 'Flipbook - ' . $catalog->title)

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('real3dflipbook/build/css/flipbook.min.css') }}">
@endsection

@section('page-style')
    <style>
        .screenshot-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            z-index: 99999;
            display: none;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            backdrop-filter: blur(10px);
        }

        /* Add more aggressive print blocking */
        @media print {
            * {
                display: none !important;
            }

            .screenshot-overlay {
                display: flex !important;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 9999999;
                background: white;
            }
        }

        /* Security styles - Disable text selection, right-click, etc */
        body {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-touch-callout: none;
            -webkit-tap-highlight-color: transparent;
        }

        /* Disable print */
        @media print {
            body {
                display: none !important;
            }
        }

        /* Full height container */
        .flipbook-wrapper {
            height: calc(100vh - 120px);
            background: #f8f9fa;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }

        /* Header styles */
        .flipbook-header {
            background: white;
            color: white;
            padding: 1rem;
            border-radius: 8px 8px 0 0;
        }

        /* Security overlay */
        .security-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            flex-direction: column;
        }

        /* Loading animation */
        .loading-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 100;
            text-align: center;
        }

        /* Flipbook container full height */
        #flipbookContainer {
            width: 100%;
            height: calc(100vh - 180px);
            min-height: 600px;
        }

        /* Hide scrollbars */
        ::-webkit-scrollbar {
            display: none;
        }

        * {
            scrollbar-width: none;
        }

        /* Error container */
        .error-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            flex-direction: column;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .flipbook-wrapper {
                height: calc(100vh - 80px);
            }

            #flipbookContainer {
                height: calc(100vh - 140px);
                min-height: 400px;
            }

            .flipbook-header h5 {
                font-size: 1rem;
            }
        }

        /* Custom flipbook styles */
        .flipbook-container .loading {
            background: rgba(255, 255, 255, 0.9);
        }

        /* Disable selection on flipbook */
        .flipbook-container * {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
        }

        /* Hide specific flipbook buttons and menus */
        .flowpaper_tbbutton.flowpaper_bttnMore,
        .flowpaper_bttnMore,
        .flowpaper_menu,
        .flowpaper_bttnMore_black,
        .flowpaper_tbbutton_menu,
        .real3dflipbook-menu-button,
        .real3dflipbook-menu,
        .menu-button,
        .fullscreen-button,
        .sound-button,
        .autoplay-button,
        .real3dflipbook-fullscreen-toggle,
        .fullscreenButton,
        .autoplayButton,
        .soundButton,
        .menu {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }

        /* Additional CSS untuk memastikan menu kontekstual tidak muncul */
        .real3dflipbook-contextmenu,
        .flowpaper_contextmenu {
            display: none !important;
            visibility: hidden !important;
        }

        /* Override any plugin's dynamic styling */
        .real3dflipbook-menuOverlay,
        .menuOverlay {
            display: none !important;
        }

        /* Force removal of menu elements */
        #flipbookContainer .real3dflipbook-menu-button,
        #flipbookContainer .real3dflipbook-menu,
        #flipbookContainer .menu-button,
        #flipbookContainer .fullscreen-button,
        #flipbookContainer .sound-button,
        #flipbookContainer .autoplay-button {
            display: none !important;
        }

        /* Screenshot prevention overlay */
        .screenshot-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #fff;
            z-index: 99999;
            display: none;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
    </style>
@endsection

@section('vendor-script')
    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM="
        crossorigin="anonymous"></script>
    <script src="{{ asset('real3dflipbook/build/js/flipbook.min.js') }}"></script>
@endsection

@section('content')

    <div class="container-fluid">
        <!-- Security Warning Overlay -->
        <div class="security-overlay" id="securityOverlay">
            <div class="text-center">
                <i class="ti ti-shield-lock display-1 mb-3"></i>
                <h4>{{ __('catalogs.flipbook.unsafe') }}</h4>
                <p>{{ __('catalogs.flipbook.unsafe_desc') }}</p>
                <p>{{ __('catalogs.flipbook.close_page') }}</p>
                <div class="spinner-border mt-3" role="status">
                    <span class="visually-hidden">{{ __('common.loading') }}...</span>
                </div>
            </div>
        </div>

        <!-- Screenshot Prevention Overlay -->
        <div class="screenshot-overlay" id="screenshotOverlay">
            <div class="text-center">
                <i class="ti ti-camera-off display-1 mb-3 text-danger"></i>
                <h4>{{ __('common.loading') }}</h4>
                <p>{{ __('catalogs.flipbook.ss_detected_desc') }}</p>
                <button class="btn btn-primary mt-3" onclick="hideScreenshotOverlay()">
                {{ __('catalogs.flipbook.back_document') }}
                </button>
            </div>
        </div>

        <!-- Flipbook Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="flipbook-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-2 mb-md-0">
                            <h5 class="mb-1 text-black text-dark">{{ $catalog->title }}</h5>
                            <small class="opacity-75 text-black text-dark">
                                <i class="ti ti-user me-1"></i>{{ $catalog->author }}
                                <span class="mx-2">•</span>
                                <i class="ti ti-file me-1"></i>{{ $file->kif_file }}
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('catalog.detail', ['id' => $catalog->id, 'slug' => Str::slug($catalog->title)]) }}"
                                class="btn btn-outline-primary btn-sm">
                                <i class="ti ti-arrow-left me-1"></i>{{ __('common.back') }}
                            </a>
                            @if($canDownload)
                                <button id="downloadBtn" class="btn btn-primary btn-sm">
                                    <i class="ti ti-download me-1"></i>{{ __('catalogs.katalog.download') }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flipbook Container -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="flipbook-wrapper">
                            <!-- Loading State -->
                            <div class="loading-container" id="loadingContainer">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="visually-hidden">{{ __('common.loading') }}...</span>
                                </div>
                                <p class="text-muted">{{ __('common.loading') }} flipbook...</p>
                            </div>

                            <!-- Error State -->
                            <div class="error-container d-none" id="errorContainer">
                                <i class="ti ti-alert-circle display-1 text-danger mb-3"></i>
                                <h5 class="text-danger mb-2">{{ __('catalogs.flipbook.fail') }}</h5>
                                <p class="text-muted mb-3" id="errorMessage">{{ __('catalogs.flipbook.fail_desc') }}</p>
                                @if($canDownload)
                                    <button class="btn btn-primary" onclick="downloadFile()">
                                        <i class="ti ti-download me-1"></i>{{ __('catalogs.flipbook.download_file') }}
                                    </button>
                                @endif
                                <a href="{{ route('catalog.detail', ['id' => $catalog->id, 'slug' => Str::slug($catalog->title)]) }}"
                                    class="btn btn-outline-primary ms-2">
                                    <i class="ti ti-arrow-left me-1"></i>{{ __('common.back') }}
                                </a>
                            </div>

                            <!-- Flipbook will be rendered here -->
                            <div id="flipbookContainer"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Screenshot Prevention Overlay -->
    <div class="screenshot-overlay" id="screenshotOverlay">
        <div class="text-center">
            <i class="ti ti-camera-off display-1 mb-3 text-danger"></i>
            <h4>{{ __('catalogs.flipbook.ss_detected') }}</h4>
            <p>{{ __('catalogs.flipbook.ss_not_allowed') }}</p>
            <p class="text-danger">{{ __('catalogs.flipbook.violation_id') }} {{ $securityToken }}</p>
            <button class="btn btn-primary mt-3" onclick="hideScreenshotOverlay()">
            {{ __('catalogs.flipbook.back_document') }}
            </button>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            let securityViolations = 0;
            let flipbookInitialized = false;
            const securityToken = '{{ $securityToken }}';

            // Override fungsi plugin yang menampilkan menu
            // Tunggu plugin dimuat
            setTimeout(function () {
                if (typeof $.fn.flipBook !== 'undefined') {
                    // Override fungsi menu pada flipBook
                    if ($.fn.flipBook.showMenu) {
                        $.fn.flipBook.showMenu = function () { return false; };
                    }

                    // Matikan fungsi terkait menu dan fitur yang tidak diinginkan
                    if (typeof window.FLIPBOOK !== 'undefined') {
                        if (window.FLIPBOOK.MenuButton) {
                            window.FLIPBOOK.MenuButton.prototype.enable = function () { return false; };
                        }
                        if (window.FLIPBOOK.FullscreenButton) {
                            window.FLIPBOOK.FullscreenButton.prototype.enable = function () { return false; };
                        }
                        if (window.FLIPBOOK.SoundButton) {
                            window.FLIPBOOK.SoundButton.prototype.enable = function () { return false; };
                        }
                        if (window.FLIPBOOK.AutoplayButton) {
                            window.FLIPBOOK.AutoplayButton.prototype.enable = function () { return false; };
                        }
                    }

                    // Hapus elemen yang tidak diinginkan secara berkala
                    setInterval(function () {
                        $('.real3dflipbook-menu-button, .real3dflipbook-menu, .flowpaper_bttnMore, .menu-button, .fullscreen-button, .sound-button, .autoplay-button').remove();
                    }, 500);
                }
            }, 1000);

            // Security initialization
            initializeEnhancedSecurity();

            // Initialize flipbook
            initializeFlipbook();

            setupAdvancedScreenshotDetection()
            // Security Functions
            function initializeEnhancedSecurity() {
                // Block ALL keyboard shortcuts - more comprehensive than before
                $(document).on('keydown', function (e) {
                    // Only allow Tab and arrow keys for accessibility
                    if (e.which !== 9 && !(e.which >= 37 && e.which <= 40)) {
                        e.preventDefault();

                        // List of forbidden keys that trigger security warning
                        const forbiddenKeys = [
                            44,  // PrtScn
                            91,  // Windows key
                            92,  // Windows key right
                            93,  // Menu key
                            123  // F12
                        ];

                        // Forbidden key combinations
                        const forbiddenCombos = [
                            e.ctrlKey && e.shiftKey && (e.which === 73 || e.which === 74 || e.which === 67 || e.which === 83), // Ctrl+Shift+I, J, C, S
                            e.ctrlKey && (e.which === 80 || e.which === 83 || e.which === 85 || e.which === 44), // Ctrl+P, S, U, PrtScn
                            e.altKey && e.which === 44, // Alt+PrtScn
                            e.metaKey && e.which === 44 // Cmd+PrtScn
                        ];

                        if (forbiddenKeys.includes(e.which) || forbiddenCombos.some(combo => combo)) {
                            showSecurityWarning('Keyboard shortcut detected');
                        }

                        return false;
                    }
                });


                // Disable right-click
                $(document).on('contextmenu', function (e) {
                    e.preventDefault();
                    showSecurityWarning();
                    return false;
                });

                // Disable print
                window.addEventListener('beforeprint', function (e) {
                    e.preventDefault();
                    showSecurityWarning();
                    return false;
                });

                // Detect DevTools (basic detection)
                setInterval(function () {
                    if (window.outerHeight - window.innerHeight > 200 ||
                        window.outerWidth - window.innerWidth > 200) {
                        showSecurityWarning();
                    }
                }, 1000);

                // Disable text selection and dragging
                document.onselectstart = function () { return false; };
                document.ondragstart = function () { return false; };

                // Block clipboard operations
                document.addEventListener('copy', function (e) {
                    e.preventDefault();
                    showSecurityWarning();
                    return false;
                });

                document.addEventListener('cut', function (e) {
                    e.preventDefault();
                    showSecurityWarning();
                    return false;
                });

                document.addEventListener('paste', function (e) {
                    e.preventDefault();
                    showSecurityWarning();
                    return false;
                });
            }

            // Setup screenshot detection
            function setupAdvancedScreenshotDetection() {
                // Method 1: Canvas-based detection (detects when canvas data is read)
                try {
                    const canvas = document.createElement('canvas');
                    canvas.width = 1;
                    canvas.height = 1;
                    const ctx = canvas.getContext('2d');
                    ctx.fillStyle = 'white';
                    ctx.fillRect(0, 0, 1, 1);

                    // Override toDataURL to detect screenshots
                    const originalToDataURL = HTMLCanvasElement.prototype.toDataURL;
                    HTMLCanvasElement.prototype.toDataURL = function (type) {
                        showScreenshotOverlay('Canvas capture detected');
                        return originalToDataURL.apply(this, arguments);
                    }
                } catch (e) {
                    console.log('Canvas detection setup failed', e);
                }

                // Method 2: Detect via CSS and animation
                const styleElement = document.createElement('style');
                styleElement.textContent = `
                                                        @keyframes screenshotDetection {
                                                            from { opacity: 0.99; }
                                                            to { opacity: 1; }
                                                        }

                                                        body {
                                                            animation: screenshotDetection 1ms infinite;
                                                            animation-timing-function: steps(1);
                                                        }

                                                        @media print {
                                                            body {
                                                                display: none !important;
                                                            }
                                                            #screenshotOverlay {
                                                                display: flex !important;
                                                            }
                                                        }
                                                    `;
                document.head.appendChild(styleElement);

                // Method 3: Detect via visibility change events
                document.addEventListener('visibilitychange', function () {
                    if (document.visibilityState === 'hidden') {
                        // User may be taking a screenshot or switching apps to do so
                        setTimeout(function () {
                            // Give a slight delay to check if it was a legitimate tab switch
                            if (document.visibilityState === 'visible') {
                                showScreenshotOverlay('Potential screenshot attempt detected');
                            }
                        }, 300);
                    }
                });

                // Method 4: Detect fullscreen changes (used by some screenshot tools)
                document.addEventListener('fullscreenchange', function () {
                    if (!document.fullscreenElement) {
                        showScreenshotOverlay('Fullscreen exit detected - possible screenshot');
                    }
                });

                // Method 5: Watermark the page with security token
                const watermark = document.createElement('div');
                watermark.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:1000;opacity:0.03;';
                watermark.innerHTML = `<div style="position:absolute;top:0;left:0;width:100%;height:100%;background:url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"300\" height=\"300\"><text x=\"50\" y=\"150\" font-family=\"Arial\" font-size=\"10\" fill=\"black\">${securityToken}</text></svg>') repeat;"></div>`;
                document.body.appendChild(watermark);
            }

            // Enhanced screenshot overlay
            function showScreenshotOverlay(reason = 'Screenshot detected') {
                const overlay = document.getElementById('screenshotOverlay');
                overlay.style.display = 'flex';

                // Log the attempt
                console.log('Security violation: ' + reason);

                // Report the violation to server
                // try {
                //     fetch('/catalog/report-security-violation', {
                //         method: 'POST',
                //         headers: {
                //             'Content-Type': 'application/json',
                //             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                //         },
                //         body: JSON.stringify({
                //             token: securityToken,
                //             catalog_id: {{ $catalog->id }},
                //             file_id: {{ $file->kif_id }},
                //             reason: reason
                //         })
                //     }).catch(e => console.error('Failed to report violation', e));
                // } catch (e) {
                //     console.error('Error reporting violation', e);
                // }

                // Show more serious warning after multiple violations
                securityViolations++;
                if (securityViolations >= 3) {
                    document.getElementById('securityOverlay').style.display = 'flex';
                    setTimeout(function () {
                        window.location.href = '{{ route("catalog.detail", ["id" => $catalog->id, "slug" => Str::slug($catalog->title)]) }}';
                    }, 3000);
                } else {
                    // Auto-hide after 5 seconds for first violations
                    setTimeout(hideScreenshotOverlay, 5000);
                }
            }

            // Hide screenshot overlay with enhanced security
            window.hideScreenshotOverlay = function () {
                document.getElementById('screenshotOverlay').style.display = 'none';

                // Add white flash effect when returning to content
                const flashElement = document.createElement('div');
                flashElement.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:white;z-index:9999;';
                document.body.appendChild(flashElement);

                setTimeout(() => {
                    flashElement.style.opacity = '0';
                    flashElement.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => document.body.removeChild(flashElement), 500);
                }, 100);
            }


            // Show screenshot prevention overlay
            function showScreenshotOverlay() {
                document.getElementById('screenshotOverlay').style.display = 'flex';

                // Auto-hide after 5 seconds
                setTimeout(hideScreenshotOverlay, 5000);
            }

            // Hide screenshot overlay
            window.hideScreenshotOverlay = function () {
                document.getElementById('screenshotOverlay').style.display = 'none';
            }

            function showSecurityWarning() {
                securityViolations++;

                if (securityViolations >= 3) {
                    document.getElementById('securityOverlay').style.display = 'flex';
                    setTimeout(function () {
                        window.location.href = '{{ route("catalog.detail", ["id" => $catalog->id, "slug" => Str::slug($catalog->title)]) }}';
                    }, 3000);
                } else {
                    // Show toast warning
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: '{{ __("catalogs.flipbook.warning") }}',
                            text: `{{ __("catalogs.flipbook.action_not_permitted") }} ${securityViolations}/3`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                }
            }

            // Initialize Flipbook
            function initializeFlipbook() {
                const fileName = '{{ $file->kif_file }}';
                const fileExtension = fileName.toLowerCase().split('.').pop();

                // Check if file is PDF
                if (fileExtension === 'pdf') {
                    const pdfUrl = '{{ $fileUrl }}';

                    try {
                        // Initialize PDF flipbook with all security options
                        $('#flipbookContainer').flipBook({
                            pdfUrl: '{{ $fileUrl }}',
                            height: '100%',
                            width: '100%',
                            enableDownload: {{ $canDownload ? 'true' : 'false' }},
                            enablePrint: false,
                            enableShare: false,
                            autoHeight: true,
                            responsive: true,
                            backgroundColor: '#f8f9fa',

                            // Disable ALL buttons and controls we don't want
                            btnSound: { enabled: true },
                            btnExpand: { enabled: false },
                            btnDownloadPages: { enabled: false },
                            btnDownloadPdf: { enabled: {{ $canDownload ? 'true' : 'false' }} },
                            btnPrint: { enabled: false },
                            btnBookmark: { enabled: false },
                            btnToc: { enabled: false },
                            btnShare: { enabled: false },
                            btnSelect: { enabled: false },
                            btnSearch: { enabled: false },
                            btnThumbs: { enabled: true }, // Biarkan ini aktif untuk navigasi thumbnail
                            btnAutoplay: { enabled: true },
                            btnMenu: { enabled: false },
                            rightClickMenu: false,

                            // Disable fullscreen
                            btnExpand: { enabled: true },

                            // Disable sound
                            sound: true,
                            btnSound: { enabled: true },

                            // Disable autoplay
                            autoplay: false,
                            btnAutoplay: { enabled: true },

                            // Disable keyboard controls
                            keyboard: false,

                            // Additional security
                            selectable: false,
                            zoomSize: 1.5, // Limit max zoom
                            zoomMin: 0.8, // Limit min zoom

                            // Set other features as needed
                            webgl: true,

                            // Callbacks
                            onLoadComplete: function () {
                                $('#loadingContainer').hide();
                                flipbookInitialized = true;
                                console.log('Flipbook loaded successfully');

                                // Remove unwanted elements when loaded
                                removeUnwantedElements();
                            },
                            onLoadError: function (error) {
                                console.error('Flipbook load error:', error);
                                showError('Gagal memuat file PDF. File mungkin rusak atau tidak dapat diakses.');
                            }
                        });

                        // Timeout fallback
                        setTimeout(function () {
                            if (!flipbookInitialized) {
                                console.warn('Flipbook loading timeout');
                                showError('Timeout saat memuat flipbook. Silakan coba lagi atau download file.');
                            }
                        }, 15000); // 15 second timeout

                    } catch (error) {
                        console.error('Error initializing flipbook:', error);
                        showError('Gagal menginisialisasi flipbook: ' + error.message);
                    }
                } else {
                    // Non-PDF files
                    showError('File "' + fileName + '" tidak mendukung tampilan flipbook. Hanya file PDF yang didukung.');
                }
            }

            // Aggressively remove unwanted elements
            function removeUnwantedElements() {
                // Immediate removal
                $('.real3dflipbook-menu-button, .real3dflipbook-menu, .flowpaper_bttnMore, .menu-button, .fullscreen-button, .sound-button, .autoplay-button').remove();

                // Set up persistent observer to keep removing elements
                const observer = new MutationObserver(function (mutations) {
                    mutations.forEach(function (mutation) {
                        if (mutation.addedNodes.length) {
                            const unwantedElements = document.querySelectorAll(
                                '.real3dflipbook-menu-button, .real3dflipbook-menu, .flowpaper_bttnMore, ' +
                                '.menu-button, .fullscreen-button, .sound-button, .autoplay-button, ' +
                                '.real3dflipbook-fullscreen-toggle'
                            );
                            unwantedElements.forEach(el => el.remove());
                        }
                    });
                });

                // Observe the entire document
                observer.observe(document.body, {
                    childList: true,
                    subtree: true,
                    attributes: true,
                    attributeFilter: ['class', 'style']
                });

                // Also run periodic cleanup
                setInterval(removeUnwantedElements, 2000);
            }

            function showError(message) {
                $('#loadingContainer').hide();
                $('#errorMessage').text(message);
                $('#errorContainer').removeClass('d-none');
            }

            // Download button
            $('#downloadBtn').on('click', function () {
                downloadFile();
            });

            // Download function
            window.downloadFile = function () {
                @if($canDownload)
                    // Open download URL in new window and log the download
                    const downloadUrl = '{{ route("catalog.download", ["code" => $catalog->code, "filename" => $file->kif_file]) }}';
                    window.open(downloadUrl, '_blank');
                @else
                                                                                                                                                                                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Akses Ditolak',
                            text: 'Anda tidak memiliki izin untuk mengunduh file ini.',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        alert('Anda tidak memiliki izin untuk mengunduh file ini.');
                    }
                @endif
                                                                                            };

            // Page visibility change (detect tab switching)
            $(document).on('visibilitychange', function () {
                if (document.hidden) {
                    console.log('Tab switched - security check');
                }
            });

            // Window blur detection
            $(window).on('blur', function () {
                console.log('Window lost focus - security check');
            });

            // Prevent image saving
            $(document).on('dragstart', 'img', function (e) {
                e.preventDefault();
                showSecurityWarning();
                return false;
            });

            // Additional security for flipbook content
            $(document).on('DOMNodeInserted', function (e) {
                if (flipbookInitialized) {
                    // Apply security to newly inserted flipbook elements
                    $(e.target).find('*').addBack().each(function () {
                        $(this).attr('oncontextmenu', 'return false;');
                        $(this).css({
                            '-webkit-user-select': 'none',
                            '-moz-user-select': 'none',
                            '-ms-user-select': 'none',
                            'user-select': 'none'
                        });
                    });
                }
            });
        });
    </script>
@endsection