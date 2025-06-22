@extends('layouts/layoutMaster')

@section('title', __('config.home'))

@section('vendor-style')
@endsection

@section('page-style')

    <!-- Include Styles -->
    <!-- BEGIN: Theme CSS-->
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{asset('assets/vendor/fonts/fontawesome.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/vendor/fonts/tabler-icons.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/vendor/fonts/flag-icons.css')}}" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{asset('assets/vendor/css/rtl/core.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/vendor/css/rtl/theme-default.css')}}" />
    <!-- Page -->
    <link rel="stylesheet" href="{{ url('assets/vendor/libs/swiper/swiper.css') }}" />
    <link rel="stylesheet" href="{{asset('assets/vendor/css/pages/front-page.css')}}">

    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />

    <script src="{{asset('assets/vendor/js/helpers.js')}}"></script>

    <script src="{{asset('assets/vendor/libs/swiper/swiper.js')}}"></script>

    <!-- Include Scripts for customizer, helper, analytics, config -->
    <style>
        nav.layout-navbar::after {
            content: none !important;
        }

        .btn-filter {
            padding: 0.41rem !important;
        }

        .schedule-carousel {
            transition: all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .schedule-card {
            min-height: 100px;
        }

        .carousel-control-vuexy {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .carousel-control-vuexy i {
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .schedule-card-container {
                width: 100% !important;
                max-width: 280px;
            }
        }

        @media (min-width: 769px) and (max-width: 992px) {
            .schedule-card-container {
                width: 45% !important;
                max-width: 320px;
            }
        }

        @media (min-width: 993px) {
            .schedule-card-container {
                width: 30% !important;
                max-width: 300px;
            }
        }

        .schedule-card {
            min-height: 100px;
        }

        #swiper-operational-hours {
            padding: 0 18px;
            overflow: hidden;
            /* Pastikan tidak ada yang terpotong keluar */
        }

        .swiper-pagination-bullet {
            background-color: #dc3545;
            opacity: 0.3;
        }

        .swiper-pagination-bullet-active {
            opacity: 1;
        }
    </style>
@endsection

@section('vendor-script')

    <!-- BEGIN: Theme JS-->
    <!-- END: Theme JS-->
    <!-- BEGIN: Page JS-->
@endsection

@section('page-script')
    <script>
        $(function () {
            const swiper = new Swiper('#swiper-banner', {
                direction: 'horizontal',
                loop: true,
                slidesPerView: 'auto',
                pagination: {
                    clickable: !0,
                    el: '.swiper-pagination'
                },
                navigation: {
                    prevEl: ".swiper-button-prev",
                    nextEl: ".swiper-button-next"
                }
            });

            const swiperbs = new Swiper('#swiper-best-seller', {
                direction: 'horizontal',
                loop: true,
                slidesPerView: 'auto',
                pagination: {
                    clickable: !0,
                    el: '.sp-bs'
                },
                navigation: {
                    prevEl: ".sbp-bs",
                    nextEl: ".sbn-bs"
                }
            });

            // NEW: Swiper for Operational Hours
            const swiperOperational = new Swiper('#swiper-operational-hours', {
                direction: 'horizontal',
                loop: true, // Endless loop
                slidesPerView: 3, // Exactly 3 cards on desktop
                slidesPerGroup: 1, // Move 1 slide at a time (smooth)
                spaceBetween: 20,
                centeredSlides: false,
                watchOverflow: true, // Disable swiper if not enough slides
                autoplay: {
                    delay: 3000, // Auto scroll every 3 seconds
                    disableOnInteraction: false, // Continue autoplay after user interaction
                    pauseOnMouseEnter: true, // Pause on hover
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    // Mobile - exactly 1 card, smooth one by one
                    320: {
                        slidesPerView: 1,
                        slidesPerGroup: 1,
                        spaceBetween: 15,
                    },
                    // Tablet - exactly 2 cards, smooth one by one
                    768: {
                        slidesPerView: 2,
                        slidesPerGroup: 1,
                        spaceBetween: 20,
                    },
                    // Desktop - exactly 3 cards, smooth one by one
                    992: {
                        slidesPerView: 3,
                        slidesPerGroup: 1,
                        spaceBetween: 20,
                    }
                }
            });

            // Existing search code...
            $('#search').keypress(function (e) {
                if (e.which == 13) {
                    submitform();
                }
            });
        });

        function submitform() {
            $('form').submit();
        }
        function filtermodal() {
            $("#frmbox").modal('show');
        }
    </script>


    <script>
        (function ($) {
            var wa_time_out, wa_time_in;
            $(".openlib-contact-btn").on("click", function () {
                if ($(".openlib-contact-box").hasClass("contact-box-active")) {
                    $(".openlib-contact-box").removeClass("contact-box-active");
                    $(".openlib-contact-btn").removeClass("contact-box-active");

                    $(".openlib-contact-btn").delay(0).animate({ rotate: '360deg' }, 500);

                    clearTimeout(wa_time_in);
                    if ($(".openlib-contact-box").hasClass("contact-box-lauch")) {
                        wa_time_out = setTimeout(function () {
                            $(".openlib-contact-box").removeClass("contact-box-pending");
                            $(".openlib-contact-box").removeClass("contact-box-lauch");
                        }, 400);
                    }
                } else {
                    $(".openlib-contact-box").addClass("contact-box-pending");
                    $(".openlib-contact-box").addClass("contact-box-active");
                    $(".openlib-contact-btn").addClass("contact-box-active");

                    $(".openlib-contact-btn").delay(0).animate({ rotate: '-360deg' }, 500);
                    clearTimeout(wa_time_out);

                    if (!$(".openlib-contact-box").hasClass("contact-box-lauch")) {
                        wa_time_in = setTimeout(function () {
                            $(".openlib-contact-box").addClass("contact-box-lauch");
                        }, 100);
                    }
                }
            });
        })(jQuery);
    </script>
@endsection

@section('content')
    <style>
        .bbg {
            width: 150%;
            height: 150%;
            border-radius: 20px;
            position: absolute;
            background: url('https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/23.01.863.jpg');
            background-repeat: no-repeat;
            background-size: 100%;
            filter: blur(18px)
        }

        #swiper-best-seller .swiper-slide {
            height: auto !important;
            width: 200px !important;
        }

        #swiper-best-seller {
            width: 200px !important;
        }

        /*.bbg::before {
                                                                                background: url('https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/23.01.863.jpg'); background-repeat: no-repeat; background-size: 100%; width:100%; height:500px; border-radius:20px; text-align: center; background-position: center;
                                                                                content: '';
                                                                            }*/
    </style>
    <!-- Content: Section 1 Slider: Start -->

    <section class="my-4"
        style="background:url('{{ asset('assets/img/landingpage/bg-catalog.jpg') }}'); background-repeat:no-repeat; background-size: 100%; padding-bottom:0px; ">
        <div class="container my-4" style="top:3rem; position: relative;">
            <div class="card card-body">
                <h1 class="text-center fw-bold">Pencarian Katalog</h1>
                <form name="filter" id="filter" method="get" action="">
                    <input type="hidden" name="page" id="page" value="{{ $request->page }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="input-group input-group-merge input-group-lg">
                            <span class="input-group-text" id="basic-addon-search31" style="border:0px"><i
                                    class="ti ti-search"></i></span>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="judul, kode, pengarang, penerbit, subyek dan jenis" style="border:0px">
                        </div>
                        <div class="input-group input-group-merge input-group-lg" style="width:350px">
                            <span class="input-group-text px-0" style="border:0px"><i
                                    class="ti ti-sort-descending-2"></i></span>
                            <select class="form-select" name="order" id="order" style="border:0px">
                                <option value="entrance_date-desc">Sort: Terbaru</option>
                                <option value="entrance_date-asc">Sort: Terlama</option>
                                <option value="published_year-desc">Sort: Terbitan Baru</option>
                                <option value="published_year-asc">Sort: Terbitan Lama</option>
                                <option value="title-asc">Sort: Judul A-Z</option>
                                <option value="title-desc">Sort: Judul Z-A</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 mt-3" style="border-bottom:solid 1px #ccc"></div>
                    <div class="row align-items-center">
                        <div class="col-md-10">
                            <small class="text-secondary">Filter Data Saat Ini</small><br>
                            @if(!empty($request->author))
                                <div class="badge bg-label-primary">{{ $request->author }}</div>
                                <input type="hidden" name="author" id="author" value="{{ $request->author }}">
                            @endif

                            @if(!empty($request->publisher))
                                <div class="badge bg-label-primary">{{ $request->publisher }}</div>
                                <input type="hidden" name="publisher" id="publisher" value="{{ $request->publisher }}">
                            @endif

                            @if(!empty($request->subject))
                                <div class="badge bg-label-primary">{{ $request->subject }}</div>
                                <input type="hidden" name="subject" id="subject" value="{{ $request->subject }}">
                            @endif

                            @if(!empty($request->classification_code))
                                <div class="badge bg-label-primary">{{ $request->classification_code }}</div>
                                <input type="hidden" name="classification_code" id="classification_code"
                                    value="{{ $request->classification_code }}">
                            @endif

                            @if(!empty($request->knowledge_location))
                                <div class="badge bg-label-primary">{{ $request->knowledge_location }}</div>
                                <input type="hidden" name="author" id="author" value="{{ $request->knowledge_location }}">
                            @endif

                            @if(!empty($request->knowledge_type))
                                <div class="badge bg-label-primary">{{ $request->knowledge_type }}</div>
                                <input type="hidden" name="knowledge_type" id="knowledge_type"
                                    value="{{ $request->knowledge_type }}">
                            @endif

                            @if(!empty($request->published_year))
                                <div class="badge bg-label-primary">{{ $request->published_year }}</div>
                                <input type="hidden" name="published_year" id="published_year"
                                    value="{{ $request->published_year }}">
                            @endif

                            @if(!empty($request->catalog_date))
                                <div class="badge bg-label-primary">{{ $request->catalog_date }}</div>
                                <input type="hidden" name="catalog_date" id="catalog_date" value="{{ $request->catalog_date }}">
                            @endif
                        </div>
                        <div class="col-md-2">
                            <small class="text-secondary">&nbsp;</small><br>
                            <a href="javascript:filtermodal()" class="text-danger fw-semibold float-end">Advanced Search</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <section class="my-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="swiper" id="swiper-banner" style="border-radius:20px; height:500px;">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide"
                                style="background-image:url({{ url('assets/img/banner/4.png') }}); background-size:cover">
                            </div>
                            <div class="swiper-slide"
                                style="background-image:url({{ url('assets/img/banner/1.png') }}); background-size:cover">
                            </div>
                            <div class="swiper-slide"
                                style="background-image:url({{ url('assets/img/banner/2.png') }}); background-size:cover">
                            </div>
                            <div class="swiper-slide"
                                style="background-image:url({{ url('assets/img/banner/3.png') }}); background-size:cover">
                            </div>
                        </div>
                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-next swiper-button-white custom-icon"></div>
                        <div class="swiper-button-prev swiper-button-white custom-icon"></div>
                    </div>
                    <!--<div class="swiper-container swiper-container-horizontal swiper card" id="swiper-with-pagination-cards" style="border-radius:20px">
                                                                                            <div class="swiper-wrapper">
                                                                                                <div class="swiper-slide" style="background: url('{{ asset('assets/img/banner/1.jpg')}}'); background-repeat: no-repeat; background-size: 100%; height:500px; border-radius:20px;">1</div>
                                                                                            </div>
                                                                                        </div>-->
                </div>
                <div class="col-lg-4">
                    <div class="card" style="border-radius:20px;">
                        <div
                            style="background-color: #2B5574; text-align: center; width:100%; height:500px; border-radius:20px">
                            <div class="card-body" style="position: relative;">
                                <h1 class="fw-bolder mb-0" style="color:#fff">Best Seller</h1>
                                <div class="mb-5" style="color:#fff">Based borrowed this month</div>
                                <!--<img src="https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/23.01.863.jpg" style="border-radius:20px; border:solid 3px #fff" height="300">-->
                                <div class="swiper" id="swiper-best-seller">
                                    <div class="swiper-wrapper">
                                        <img class="swiper-slide"
                                            src="https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/23.01.863.jpg"
                                            style="border-radius:20px; border:solid 3px #fff">
                                        <img class="swiper-slide"
                                            src="https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/23.01.863.jpg"
                                            style="border-radius:20px; border:solid 3px #fff">
                                        <img class="swiper-slide"
                                            src="https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/23.01.863.jpg"
                                            style="border-radius:20px; border:solid 3px #fff">
                                    </div>
                                </div>
                                <div class="swiper-pagination sp-bs"></div>
                                <div class="swiper-button-next sbn-bs swiper-button-white custom-icon"></div>
                                <div class="swiper-button-prev sbp-bs swiper-button-white custom-icon"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Content: Section 1 Slider: End -->

    <!-- Content: Section 2 Services: Start -->
    <section class="my-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <a href="https://openlibrary.telkomuniversity.ac.id/home/information/id/53.html" class="">
                        <div class="d-flex align-items-start">
                            <div class="badge rounded bg-label-primary p-2 me-3 rounded"><i class="ti ti-lg ti-books"></i>
                            </div>
                            <div class="">
                                <h5 class="mb-0 fw-bold">E-Catalogue</h5>
                                <small class="text-muted">Download koleksi katalog terbaru</small>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="https://openlibrary.telkomuniversity.ac.id/room/" class="">
                        <div class="d-flex align-items-start">
                            <div class="badge rounded bg-label-primary p-2 me-3 rounded"><i
                                    class="ti ti-lg ti-calendar-pin"></i></div>
                            <div class="">
                                <h5 class="mb-0 fw-bold">Room Reservations</h5>
                                <small class="text-muted">Discussion room reservations</small>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="#" class="">
                        <div class="d-flex align-items-start">
                            <div class="badge rounded bg-label-primary p-2 me-3 rounded"><i
                                    class="ti ti-lg ti-user-question"></i></div>
                            <div class="">
                                <h5 class="mb-0 fw-bold">Ask Librarian</h5>
                                <small class="text-muted">Feel free to contact us</small>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="#" class="">
                        <div class="d-flex align-items-start">
                            <div class="badge rounded bg-label-primary p-2 me-3 rounded"><i
                                    class="ti ti-lg ti-help-hexagon"></i></div>
                            <div class="">
                                <h5 class="mb-0 fw-bold">Library FAQ</h5>
                                <small class="text-muted">Open library frequently ask questions</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-3">
                    <a href="#" class="">
                        <div class="d-flex align-items-start">
                            <div class="badge rounded bg-label-primary p-2 me-3 rounded"><i
                                    class="ti ti-lg ti-file-text"></i></div>
                            <div class="">
                                <h5 class="mb-0 fw-bold">Karya Ilmiah Online</h5>
                                <small class="text-muted">Akses online karya ilmiah mahasiswa</small>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="https://bit.ly/PermohonanKelasLiterasiOpenlib" class="">
                        <div class="d-flex align-items-start">
                            <div class="badge rounded bg-label-primary p-2 me-3 rounded"><i
                                    class="ti ti-lg ti-calendar-time"></i></div>
                            <div class="">
                                <h5 class="mb-0 fw-bold">Kelas Literasi</h5>
                                <small class="text-muted">Pengajuan jadwal kelas literasi</small>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="https://bit.ly/AbsenKelasLiteasi" class="">
                        <div class="d-flex align-items-start">
                            <div class="badge rounded bg-label-primary p-2 me-3 rounded"><i
                                    class="ti ti-lg ti-user-check"></i></div>
                            <div class="">
                                <h5 class="mb-0 fw-bold">Presensi Kelas Literasi</h5>
                                <small class="text-muted">Daftar hadir kelas literasi</small>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="https://bit.ly/PermohonanBimbinganPemustaka" class="">
                        <div class="d-flex align-items-start">
                            <div class="badge rounded bg-label-primary p-2 me-3 rounded"><i
                                    class="ti ti-lg ti-message-2-star"></i></div>
                            <div class="">
                                <h5 class="mb-0 fw-bold">Bimbingan Pemustaka</h5>
                                <small class="text-muted">Pengajuan bimbingan pemustaka</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <!-- Content: Section 2 Services: End -->

    <!-- Content: Section Schedule: Start -->
    <section class="my-5">
        <div class="container">
            <h5 class="text-center mb-4">
                <i class="ti ti-clock me-1"></i> {{__('landingpage.operational_hour')}} —
                <strong>{{ \Carbon\Carbon::now()->format('D, d M') }}</strong>
            </h5>

            <div class="position-relative">
                <!-- Swiper for Operational Hours -->
                <div class="swiper" id="swiper-operational-hours">
                    <div class="swiper-wrapper">
                        @foreach($libraries as $lib)
                            <div class="swiper-slide">
                                <div
                                    class="schedule-card p-4 border border-danger rounded-3 text-center h-100 d-flex flex-column justify-content-center">
                                    <div class="fw-medium text-danger mb-2" style="font-size: 0.95rem; line-height: 1.3;">
                                        {{ $lib['name'] }}
                                    </div>
                                    <div class="fw-bold text-dark" style="font-size: 1.1rem;">
                                        {{ $lib['hours'] }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <!-- Pagination -->
                    <div class="swiper-pagination mt-3"></div>
                </div>
            </div>

            <!-- Show All Jadwal -->
            <div class="text-center mt-4">
                <a href="{{ route('landingpage.schedule')}}" class="text-danger fw-semibold text-decoration-none"
                    style="font-size: 1.05rem; transition: all 0.3s ease;"
                    onmouseover="this.style.color='#b02a37'; this.style.textDecoration='underline';"
                    onmouseout="this.style.color='#dc3545'; this.style.textDecoration='none';">
                    {{__('landingpage.show_all_schedule')}}
                </a>
            </div>
        </div>
    </section>
    <!-- Content: Section Schedule: End -->

    <!-- Content: Section 3 New Books: Start -->
    <section class="my-5 py-5"
        style="background:url('{{ asset('assets/img/landingpage/bg-featured-books.png') }}'); background-repeat:no-repeat; background-size: 100%">
        <div class="container">
            <div class="card-header d-flex justify-content-between">
                <div class="card-title mb-3">
                    <h5 class="mb-0">Popular Books</h5>
                    <small class="text-muted">Discover the popular books (most frequently borrowed) to read that are
                        trending right now.</small>
                </div>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="earningReportsId" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false" fdprocessedid="hp2du">
                        <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="earningReportsId">
                        <a class="dropdown-item" href="javascript:void(0);">View More</a>
                        <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                    </div>
                </div>
                <!-- </div> -->
            </div>
            <div class="row">
                @foreach($popular as $p)
                    <div class="col-md-2">
                        <div class="badge bg-danger"
                            style="position: absolute;margin-top: 13%;border-radius: 0px 5px 5px 0px;font-size: 13px;">
                            <i class="ti ti-calendar me-1"></i>
                            <span class="mt-n1">{{ date('m/Y', strtotime($p->tgl)) }}</span>
                        </div>
                        <img src="{{ @getimagesize('https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/' . $p->cover) ? 'https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/'.$p->cover : url('assets/img/default-book-cover.jpg') }}"
                            style="border-radius:15px; width:200px">
                        <h6 class="mt-3 fw-bold" style="min-height: 61px">{{ $p->title }}</h6>
                        <div class="d-flex align-items-center fw-bold" style="color:#7D70DA"><i
                                class="ti ti-clipboard-check me-1"></i>Tersedia {{ $p->available }} koleksi</div>
                        <div class="d-flex align-items-center fw-bold" style="color:#F67546"><i
                                class="ti ti-bookmarks me-1"></i><small>{{ $p->cat }}</small></div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- Content: Section 3 New Books: End -->

    <!-- Content: Section 3 New Books: Start -->
    <section class="my-5 py-5">
        <div class="container">
            <div class="card-header d-flex justify-content-between">
                <div class="card-title mb-0">
                    <h5 class="mb-0">Earning Reports</h5>
                    <small class="text-muted">Weekly Earnings Overview</small>
                </div>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="earningReportsId" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false" fdprocessedid="hp2du">
                        <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="earningReportsId">
                        <a class="dropdown-item" href="javascript:void(0);">View More</a>
                        <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                    </div>
                </div>
                <!-- </div> -->
            </div>
            <div class="row">
                @for($i = 1; $i <= 6; $i++)
                    <div class="col-md-2">
                        <div class="badge bg-danger"
                            style="position: absolute;margin-top: 30px;border-radius: 0px 5px 5px 0px;font-size: 13px;">
                            <i class="ti ti-calendar me-1"></i>
                            <span class="mt-n1">08/23</span>
                        </div>
                        <img src="https://openlibrary.telkomuniversity.ac.id/uploads/book/cover/23.01.863.jpg"
                            style="border-radius:15px; width:200px">
                        <h6 class="mt-3 fw-bold">Calculus: Early Transcendental Functions, International Metric Edition, 8th
                            Edition</h6>
                        <div class="d-flex align-items-center fw-bold" style="color:#7D70DA"><i
                                class="ti ti-clipboard-check me-1"></i>Tersedia 1 koleksi</div>
                        <div class="d-flex align-items-center fw-bold" style="color:#F67546"><i
                                class="ti ti-bookmarks me-1"></i><small>Buku - Circulation (Dapat Dipinjam)</small></div>
                    </div>
                @endfor
            </div>
        </div>
    </section>
    <!-- Content: Section 3 New Books: End -->

    <!-- Content: Section 3 Books: Start -->
    <section class="my-5">
        <div class="container">
            <div class="card-header pb-0 d-flex justify-content-between mb-lg-n4">
                <div class="card-title mb-0">
                    <h5 class="mb-0">Latest News</h5>
                    <small class="text-muted">Weekly Earnings Overview</small>
                </div>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="earningReportsId" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false" fdprocessedid="hp2du">
                        <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="earningReportsId">
                        <a class="dropdown-item" href="javascript:void(0);">View More</a>
                        <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                    </div>
                </div>
                <!-- </div> -->
            </div>
            <div class="row">
                @for($i = 1; $i <= 4; $i++)
                    <div class="col-md-3">
                        <div class="rounded-2 text-center mb-3">
                            <a
                                href="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo-1/app/academy/course-details">
                                <img class="img-fluid"
                                    src="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo/assets/img/pages/app-academy-tutor-1.png"
                                    alt="tutor image 1">
                            </a>
                        </div>
                        <a href="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo-1/app/academy/course-details"
                            class="h6 fw-bold">Why reading is important for our children?</a>
                        <p class="mt-2" style="font-size:13px; line-height: 1.25; text-align: justify">lorem ipsum dolor sit
                            amet, consectetur adipis lorem ipsum dolor sit amet, consectetur adipis lorem ipsum dolor sit amet,
                            consectetur adipis lorem ipsum dolor sit amet, consectetur adipis</p>
                    </div>
                @endfor
            </div>
        </div>
    </section>
    <!-- Content: Section 3 Books: End -->

    <!-- Content: Section 3 Books: Start -->
    <section class="mt-5 py-3" style="background-color: #f6f8fb">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center gap-3" style="margin:100px">
                <div class="text-center"
                    style="border-radius: 10px; background-color: #fff; padding: 20px; width:250px; max-width: 250px">
                    <div><img src="{{ asset('assets/img/icons/book.png') }}" style="width:90px"></div>
                    <h3 class="fw-bold pt-5" style="line-height: 1.25; margin-bottom: 0px">9,789</h3>
                    <span class="text-muted">Jumlah Judul Buku</span>
                </div>
                <div class="text-center"
                    style="border-radius: 10px; background-color: #fff; padding: 20px; width:250px; max-width: 250px">
                    <div><img src="{{ asset('assets/img/icons/books.png') }}" style="width:90px"></div>
                    <h3 class="fw-bold pt-5" style="line-height: 1.25; margin-bottom: 0px">29,789</h3>
                    <span class="text-muted">Jumlah Eksemplar Buku</span>
                </div>
                <div class="text-center"
                    style="border-radius: 10px; background-color: #fff; padding: 20px; width:250px; max-width: 250px">
                    <div><img src="{{ asset('assets/img/icons/borrow-book.png') }}" style="width:90px"></div>
                    <h3 class="fw-bold pt-5" style="line-height: 1.25; margin-bottom: 0px">139,789</h3>
                    <span class="text-muted">Jumlah Peminjaman Buku</span>
                </div>
                <div class="text-center"
                    style="border-radius: 10px; background-color: #fff; padding: 20px; width:250px; max-width: 250px">
                    <div><img src="{{ asset('assets/img/icons/member.png') }}" style="width:90px"></div>
                    <h3 class="fw-bold pt-5" style="line-height: 1.25; margin-bottom: 0px">39,789</h3>
                    <span class="text-muted">Jumlah Anggota</span>
                </div>
                <div class="text-center"
                    style="border-radius: 10px; background-color: #fff; padding: 20px; width:250px; max-width: 250px">
                    <div><img src="{{ asset('assets/img/icons/visit.png') }}" style="width:90px"></div>
                    <h3 class="fw-bold pt-5" style="line-height: 1.25; margin-bottom: 0px">139,789</h3>
                    <span class="text-muted">Jumlah Kunjungan</span>
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade" id="frmbox" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="get" id="form-modal" class="form-validate">
                    <div class="modal-header bg-transparent">
                        <div class="modal-title"><i class="ti ti-filter me-2"></i>Pencarian Detil Katalog</div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="search" id="search" value="{{ $request->search }}">
                        <input type="hidden" name="order" id="order" value="{{ $request->order }}">
                        <div class="form-group row mb-2">
                            <label class="col-md-3 col-form-label">Pengarang</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="author" id="author">
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-md-3 col-form-label">Penerbit</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="publisher" id="publisher">
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-md-3 col-form-label">Subject</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="subject" id="subject">
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-md-3 col-form-label">Kode Klasifikasi</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="classification_code" id="classification_code">
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-md-3 col-form-label">Lokasi</label>
                            <div class="col-md-9">
                                {{ Form::select('knowledge_location', $knowledge_location, '', ['id' => 'knowledge_location', 'class' => 'form-select']) }}
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-md-3 col-form-label">Jenis Katalog</label>
                            <div class="col-md-9">
                                {{ Form::select('knowledge_type', $knowledge_type, '', ['id' => 'knowledge_type', 'class' => 'form-select']) }}
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-md-3 col-form-label">Tahun Terbit</label>
                            <div class="col-md-9">
                                {{ Form::select('published_year', $published_year, '', ['id' => 'published_year', 'class' => 'form-select']) }}
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-md-3 col-form-label">Tanggal Masuk</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control drp" name="catalog_date" id="catalog_date"
                                    value="" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Bersihkan</button>
                        <button type="submit" class="btn btn-primary"><i class="ti ti-filter ti-sm me-2"></i>Cari</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Content: Section 3 Books: End -->
@endsection