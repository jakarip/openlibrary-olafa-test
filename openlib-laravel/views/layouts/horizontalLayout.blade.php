@isset($pageConfigs)
{!! Helper::updatePageConfig($pageConfigs) !!}
@endisset
@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/commonMaster' )
@php

$menuHorizontal = true;
$navbarFull = true;

/* Display elements */
$isNavbar = ($isNavbar ?? true);
$isMenu = ($isMenu ?? true);
$isFlex = ($isFlex ?? false);
$isFooter = ($isFooter ?? true);
$customizerHidden = ($customizerHidden ?? '');
$pricingModal = ($pricingModal ?? false);

/* HTML Classes */
$menuFixed = (isset($configData['menuFixed']) ? $configData['menuFixed'] : '');
$navbarFixed = (isset($configData['navbarFixed']) ? $configData['navbarFixed'] : '');
$footerFixed = (isset($configData['footerFixed']) ? $configData['footerFixed'] : '');
$menuCollapsed = (isset($configData['menuCollapsed']) ? $configData['menuCollapsed'] : '');

/* Content classes */
if(empty($containerx)) {
    $container = 'container-xxl'; //($container ?? 'container-xxl');
    $containerNav = 'container-xxl';
} else {
    $container = $containerx;
    $containerNav = $containerx;
}


@endphp

@section('layoutContent')
<div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
  <div class="layout-container">

    <!-- BEGIN: Navbar-->
    @if ($isNavbar)
    @include('layouts/sections/navbar/navbar')
    @endif
    <!-- END: Navbar-->


    <!-- Layout page -->
    <div class="layout-page">

      <!-- Content wrapper -->
      <div class="content-wrapper">

        @if ($isMenu)
        @include('layouts/sections/menu/horizontalMenu')
        @endif

        <!-- Content -->
        @if ($isFlex)
        <div class="{{$container}} d-flex align-items-stretch flex-grow-1 p-0">
          @else
          <div class="{{$container}} flex-grow-1 container-p-y">
            @endif

            <!-- content title -->
            <div class="row mb-4">
                <div class="col-sm-6 align-items-center">
                    <div class="d-flex justify-content-start align-self-center">
                        {{-- <a href="@yield('url-back')"class="btn btn-icon rounded-pill btn-primary waves-effect waves-light me-3">
                            @if (trim($__env->yieldContent('url-back')))
                            <i class="ti ti-chevron-left"></i>
                            @else
                            <i class="ti ti-home-2"></i>
                            @endif
                        </a> --}}
                        <a href="{{ url('/') }}" class="btn btn-icon rounded-pill btn-primary waves-effect waves-light me-3">
                          @if (trim($__env->yieldContent('url-back')))
                              <i class="ti ti-chevron-left"></i>
                          @else
                              <i class="ti ti-home-2"></i>
                          @endif
                        </a>
                        @if (trim($__env->yieldContent('title')))
                        <div>
                            <h4 class="mb-0" style="line-height: 1.3; color:#333; font-size:16px">@yield('title')</h4>
                            <div class="small text-muted" style="line-height: 1">
                                <a href="{{ url('/') }}" style="text-decoration:none" class="text-danger">Back to Home</a>
                            </div>
                        </div>
                        @elseif (trim($__env->yieldContent('title2')))
                            <div>
                                <h4 class="mb-0" style="line-height: 1.3; color:#333; font-size:16px">@yield('title2')</h4>
                                <div class="small text-muted" style="line-height: 1">
                                    <a href="{{ url('/document') }}" style="text-decoration:none" class="text-danger">Back to Document</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6 justify-content-end d-flex ytitle-left">
                    <div class="d-flex align-items-center">
                        <div class="badge bg-label-primary p-2 me-3 rounded"><i class="ti ti-calendar"></i></div>
                        <div class="">
                            <h6 class="mb-0" style="line-height: 1">{{ date('d F Y') }}</h6>
                            <small class="text-muted">Tahun 2023 TW 1</small>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end content title -->

            @yield('content')


          </div>
          <!-- / Content -->

          <!-- Footer -->
          @if ($isFooter)
          @include('layouts/sections/footer/footer-front')
          @endif
          <!-- / Footer -->
          <div class="content-backdrop fade"></div>
        </div>
        <!--/ Content wrapper -->
      </div>
      <!-- / Layout page -->
    </div>
    <!-- / Layout Container -->

    @if ($isMenu)
    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
    @endif
    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>
  </div>
  <!-- / Layout wrapper -->
  @endsection
