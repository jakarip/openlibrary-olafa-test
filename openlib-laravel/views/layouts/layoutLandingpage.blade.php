<!DOCTYPE html>

<html lang="id"
    class="light-style     "
    dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('assets/') }}/" data-base-url="{{ url('') }}" data-framework="laravel"
    data-template="vertical-menu-theme-default-light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Login | OpenLibrary - Telkom University</title>
    <meta name="description"
        content="" />
    <meta name="keywords"
        content="">
    <!-- laravel CRUD token -->
    <meta name="csrf-token" content="WXmvLjsUnKWZjSJOxGQMdHIwD8UvdCjg4VgTywU5">
    <!-- Canonical SEO -->
    <link rel="canonical" href="">
    <!-- Favicon -->


    <!-- Include Styles -->
    <!-- BEGIN: Theme CSS-->
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

 <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

    <!-- Vendor CSS -->
    {{-- <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" /> --}}
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.3.0/css/all.css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/swiper/swiper.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/front-page.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />

    <!-- Vendor JS -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/swiper/swiper.js') }}"></script>
    <!-- Include Scripts for customizer, helper, analytics, config -->
    @include('layouts/sections/scriptsIncludes')
    <style>
    nav.layout-navbar::after {
        content:none !important;
    }
    .layout-horizontal .bg-menu-theme .menu-inner > .menu-item > .menu-link {
        font-size: 16px;
    }

    .openlib-contact-box {
        z-index: 998;
        opacity: 0;
        visibility: hidden;
        bottom: 102px;

        -ms-transform: translate(0,50px);
        transform: translate(0,50px);
        -webkit-transform: translate(0,50px);
        -moz-transform: translate(0,50px);
        transition: 0.4s ease all;
        -webkit-transition: 0.4s ease all;
        -moz-transition: 0.4s ease all;
        will-change: transform,visibility,opacity;
    }
    .openlib-contact-box.contact-box-active {
        -ms-transform: translate(0,0);
        transform: translate(0,0);
        -webkit-transform: translate(0,0);
        -moz-transform: translate(0,0);
        visibility: visible;
        opacity: 1;

    }
    .openlib-contact-item {
        padding-top: 13px;
        padding-bottom: 13px;
    }
    .openlib-contact-btn, .openlib-contact-btn:hover, .openlib-contact-btn:active, .openlib-contact-btn:focus {
        color: #fff;
        background-color: #B61614;
        border-color: #B61614;
    }
    .openlib-contact-btn > i:before {
        content: "\eaef";
    }
    .openlib-contact-btn.contact-box-active > i:before {
        content: "\eb55"; /*close (ti-x)*/
    }
    .btn-filter {
        padding:0.41rem !important;
    }
    </style>
</head>

<body style="overflow-x:hidden" class="openlibrary-red">
<div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
    <div class="layout-container">
        <!-- Navbar: Start -->
        <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar" style="background-color: #B61614 !important;
            color: #fff !important;">
            <div class="container-xxl">
                @include('layouts/sections/navbar/navbar_homepage')
            </div>
        </nav>

        <div class="layout-page">
            <div class="content-wrapper">
                @include('layouts/sections/menu/homepage')
                @yield('content')
            </div>
        </div>
    </div>
</div>

<!-- Footer: Start -->
<footer class="landing-footer bg-body footer-text">
    <div class="footer-top" style="background-color:#9f1521; color:#fff; border-radius:0px">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <a href="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo-1/front-pages/landing" class="app-brand-link mb-4">
                        <img src="{{ asset('assets/img/openlibrary/logo-h-50.png') }}">
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="fw-bold mb-3">TelU Utama - Gedung Manterawu Lantai 5</div>
                    <div class="mb-3">Jl. Telekomunikasi - Ters. Buah Batu<br>Bandung 40257 Indonesia</div>
                    <div><i class="ti ti-sm me-2 ti-phone-call"></i>081280000110</div>
                    <div><i class="ti ti-sm me-2 ti-device-landline-phone"></i>+6222 756 5929</div>
                    <div><i class="ti ti-sm me-2 ti-mail"></i>library@telkomuniversity.ac.id</div>
                    <div><i class="ti ti-sm me-2 ti-mail"></i>bebas pustaka: bebaspustaka@telkomuniversity.ac.id</div>
                    <div><i class="ti ti-sm me-2 ti-clock"></i>Senin - Jumat Jam 08:00 - 19:30</div>
                </div>
                <div class="col-md-3">
                    <div class="fw-bold mb-3">TelU - Kampus Geger Kalong</div>
                    <div class="mb-3">Jl. Geger Kalong No. 1 Bandung Indonesia</div>
                    <div><i class="ti ti-sm me-2 ti-phone-call"></i>081280000110</div>
                    <div><i class="ti ti-sm me-2 ti-device-landline-phone"></i>+6222 756 5929</div>
                    <div><i class="ti ti-sm me-2 ti-mail"></i>library@telkomuniversity.ac.id</div>
                    <div><i class="ti ti-sm me-2 ti-mail"></i>bebas pustaka: bebaspustaka@telkomuniversity.ac.id</div>
                    <div><i class="ti ti-sm me-2 ti-clock"></i>Senin - Jumat Jam 08:00 - 19:30</div>
                </div>
                <div class="col-md-3">
                    <div class="fw-bold mb-3">TUKJ - Kampus A</div>
                    <div class="mb-3">Jalan Daan Mogot KM.11, RT.1/RW.4, Kedaung Kali Angke, Cengkareng, RT.1/RW.4, Kedaung Kali Angke, Kecamatan Cengkareng, Kota Jakarta Barat, DKI Jakarta</div>
                    <div><i class="ti ti-sm me-2 ti-phone-call"></i>(021)-545-1697</div>
                    <div><i class="ti ti-sm me-2 ti-device-landline-phone"></i>(021)-545-1697</div>
                    <div><i class="ti ti-sm me-2 ti-mail"></i>library@telkomuniversity.ac.id</div>
                    <div><i class="ti ti-sm me-2 ti-clock"></i>Senin - Jumat Jam 08:00 - 19:30</div>
                </div>
                <div class="col-md-3">
                    <div class="fw-bold mb-3">TUKJ - Kampus B</div>
                    <div class="mb-3">Jalan Halimun Raya Nomor 2A, RT 010, RW 006, Kelurahan Guntur, Kecamatan Setiabudi, Kota Jakarta Selatan, Provinsi DKI Jakarta</div>
                    <div><i class="ti ti-sm me-2 ti-phone-call"></i>(021)-545-1697</div>
                    <div><i class="ti ti-sm me-2 ti-device-landline-phone"></i>(021)-545-1697</div>
                    <div><i class="ti ti-sm me-2 ti-mail"></i>library@telkomuniversity.ac.id</div>
                    <div><i class="ti ti-sm me-2 ti-clock"></i>Senin - Jumat Jam 08:00 - 19:30</div>
                </div>
            </div>
        </div>
    </div>
    <!--
    <div class="col-md-2">
                    <a href="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo-1/front-pages/landing" class="app-brand-link mb-4">
                        <img src="{{ asset('assets/img/openlibrary/logo-h-50.png') }}">
                    </a>
                    <p class="footer-text footer-logo-description fw-bold mb-4">Telkom University Library (Open Library)<br>NPP : 3204122D0000002</p>
                    <img src="https://licensebuttons.net/l/by-nc/4.0/88x31.png">
                </div>
    -->
    <div class="footer-bottom py-3">
        <div class="container d-flex flex-wrap justify-content-between flex-md-row flex-column text-center text-md-start">
            <div class="mb-2 mb-md-0">
            <span class="footer-text me-2">©{{ date('Y') }} Telkom University Library (Open Library)</span>
            <span class="footer-text fw-bold">NPP : 3204122D0000002</span>
            </div>
            <div>
            <a href="https://tools.pixinvent.com/github/github-access" class="footer-link me-3" target="_blank">
                <img src="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo/assets/img/front-pages/icons/github-light.png" alt="github icon" data-app-light-img="front-pages/icons/github-light.png" data-app-dark-img="front-pages/icons/github-dark.png" />
            </a>
            <a href="https://www.facebook.com/pixinvents/" class="footer-link me-3" target="_blank">
                <img src="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo/assets/img/front-pages/icons/facebook-light.png" alt="facebook icon" data-app-light-img="front-pages/icons/facebook-light.png" data-app-dark-img="front-pages/icons/facebook-dark.png" />
            </a>
            <a href="https://twitter.com/pixinvents" class="footer-link me-3" target="_blank">
                <img src="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo/assets/img/front-pages/icons/twitter-light.png" alt="twitter icon" data-app-light-img="front-pages/icons/twitter-light.png" data-app-dark-img="front-pages/icons/twitter-dark.png" />
            </a>
            <a href="https://www.instagram.com/pixinvents/" class="footer-link" target="_blank">
                <img src="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo/assets/img/front-pages/icons/instagram-light.png" alt="google icon" data-app-light-img="front-pages/icons/instagram-light.png" data-app-dark-img="front-pages/icons/instagram-dark.png" />
            </a>
            </div>
        </div>
    </div>
</footer>
<!-- Footer: End -->

<!-- Contact Button -->
<div class="openlib-contact" style="position: fixed; bottom: 1rem; right: 1rem; z-index: 1080; width:400px">
    <div class="mb-3 openlib-contact-box">
        <div class="card" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header" style="background-color:rgb(0, 98, 137)">
                <div class="d-flex justify-content-start align-items-center user-name">
                    <div class="avatar-wrapper">
                        <div class="avatar avatar-online me-3">
                            <img src="{{ asset('assets/img/landingpage/openlib-contact-avatar.jpg') }}" alt="" class="rounded-circle">
                        </div>
                    </div>
                    <div class="d-flex flex-column">
                        <a href="app-user-view-account.html" class="text-body text-truncate">
                            <span class="fw-bold text-white ">Mulai Percakapan</span>
                        </a>
                        <small class="text-white">Silahkan hubungi salah satu PIC kami menggunakan Whatsapp</small>
                    </div>
                </div>
            </div>
            <div class="card-body pt-3" style="background:url('{{ asset('assets/img/landingpage/background-whatsapp.jpg') }}') center center / cover no-repeat;">
                <div class="card card-body openlib-contact-item mb-2">
                    <div class="d-flex justify-content-start align-items-center">
                        <div class="avatar-wrapper">
                            <div class="avatar avatar-online me-3">
                                <img src="{{ asset('assets/img/landingpage/openlib-contact-avatar-ai.jpg') }}" alt="Avatar" class="rounded-circle">
                            </div>
                        </div>
                        <div class="d-flex flex-column">
                            <a href="app-user-view-account.html" class="text-body text-truncate">
                                <span class="fw-bold text-blue ">Chat With AI</span>
                            </a>
                            <small class="text-muted">081280000110</small>
                        </div>
                        <img src="{{ asset('assets/img/landingpage/whatsapp_logo_green.svg') }}" width="20" height="20" class="ms-auto">
                    </div>
                </div>
                <div class="card card-body openlib-contact-item mb-2">
                    <div class="d-flex justify-content-start align-items-center">
                        <div class="avatar-wrapper">
                            <div class="avatar avatar-online me-3">
                                <img src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/avatars/2.png" alt="Avatar" class="rounded-circle">
                            </div>
                        </div>
                        <div class="d-flex flex-column">
                            <a href="app-user-view-account.html" class="text-body text-truncate">
                                <span class="fw-bold text-blue ">Hotline Openlibrary</span>
                            </a>
                            <small class="text-muted">081280000110</small>
                        </div>
                        <img src="{{ asset('assets/img/landingpage/whatsapp_logo_green.svg') }}" width="20" height="20" class="ms-auto">
                    </div>
                </div>
                <div class="card card-body openlib-contact-item mb-2">
                    <div class="d-flex justify-content-start align-items-center">
                        <div class="avatar-wrapper">
                            <div class="avatar avatar-online me-3">
                                <img src="https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/avatars/2.png" alt="Avatar" class="rounded-circle">
                            </div>
                        </div>
                        <div class="d-flex flex-column">
                            <a href="app-user-view-account.html" class="text-body text-truncate">
                                <span class="fw-bold text-blue ">Hotline Openlibrary Jakarta</span>
                            </a>
                            <small class="text-muted">081280000110</small>
                        </div>
                        <img src="{{ asset('assets/img/landingpage/whatsapp_logo_green.svg') }}" width="20" height="20" class="ms-auto">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div style="text-align: right">
        <span class="btn btn-label-secondary btn-sm">Butuh Bantuan?<br><strong>Hub Kami</strong></span>
        <button class="btn btn-icon rounded-pill btn-xl openlib-contact-btn">
            <i class="ti fs-large openlib-contact-btn-icon"></i>
        </button>
    </div>
</div>
<!-- Contact Button: End -->

<!-- Include Scripts -->
<!-- BEGIN: Vendor JS-->
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>

<script src="{{ asset('assets/js/main.js') }}"></script>
<!-- BEGIN: Theme JS-->
<!-- END: Theme JS-->
<script src="http://projects.test/openlibrary/public/assets/js/front-main.js"></script>
<!-- BEGIN: Page JS-->
{{--<script src="http://projects.test/openlibrary/public/assets/js/front-page-pricing.js"></script>--}}
@yield('page-script')
<script>
(function($) {
    var wa_time_out, wa_time_in;
    $(".openlib-contact-btn").on("click", function() {
        if ($(".openlib-contact-box").hasClass("contact-box-active")) {
            $(".openlib-contact-box").removeClass("contact-box-active");
            $(".openlib-contact-btn").removeClass("contact-box-active");

            $(".openlib-contact-btn").delay(0).animate({rotate: '360deg'}, 500);

            clearTimeout(wa_time_in);
            if ($(".openlib-contact-box").hasClass("contact-box-lauch")) {
                wa_time_out = setTimeout(function() {
                    $(".openlib-contact-box").removeClass("contact-box-pending");
                    $(".openlib-contact-box").removeClass("contact-box-lauch");
                }, 400);
            }
        } else {
            $(".openlib-contact-box").addClass("contact-box-pending");
            $(".openlib-contact-box").addClass("contact-box-active");
            $(".openlib-contact-btn").addClass("contact-box-active");

            $(".openlib-contact-btn").delay(0).animate({rotate: '-360deg'}, 500);
            clearTimeout(wa_time_out);

            if (!$(".openlib-contact-box").hasClass("contact-box-lauch")) {
                wa_time_in = setTimeout(function() {
                    $(".openlib-contact-box").addClass("contact-box-lauch");
                }, 100);
            }
        }
    });
})(jQuery);
</script>
</body>

</html>
