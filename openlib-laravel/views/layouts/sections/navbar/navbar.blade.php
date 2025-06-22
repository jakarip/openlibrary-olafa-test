@php
  $containerNav = (isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact') ? 'container-xxl' : 'container-fluid';
  $navbarDetached = ($navbarDetached ?? '');
@endphp

<!-- Navbar -->
@if(isset($navbarDetached) && $navbarDetached == 'navbar-detached')
  <nav
    class="layout-navbar {{$containerNav}} navbar navbar-expand-xl {{$navbarDetached}} align-items-center bg-navbar-theme"
    id="layout-navbar">
  @endif
  @if(isset($navbarDetached) && $navbarDetached == '')
    <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="{{$containerNav}}">
  @endif

      <!--  Brand demo (display only for navbar-full and hide on below xl) -->
      @if(isset($navbarFull))
      <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
      <a href="{{url('/')}}" class="app-brand-link gap-2">
        <span class="app-brand-logo demo">
        <!-- @include('_partials.macros',["height"=>20]) -->
        </span>
        <span class="app-brand-text demo menu-text fw-bold">
        <!-- {{config('variables.templateName')}} -->

        </span>
      </a>
      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
        <i class="ti ti-x ti-sm align-middle"></i>
      </a>
      </div>
    @endif

      <!-- ! Not required for layout-without-menu -->
      @if(!isset($navbarHideToggle))
      <div
      class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
      <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
        <i class="ti ti-menu-2 ti-sm"></i>
      </a>
      </div>
    @endif

      <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

        @if(!isset($menuHorizontal))
      <!-- Search -->
      <div class="navbar-nav align-items-center">
        <div class="nav-item navbar-search-wrapper mb-0">
        <a class="nav-item nav-link search-toggler d-flex align-items-center px-0" href="javascript:void(0);">
          <i class="ti ti-search ti-md me-2"></i>
          <span class="d-none d-md-inline-block text-muted">Search (Ctrl+/)</span>
        </a>
        </div>
      </div>
      <!-- /Search -->
    @endif
        <ul class="navbar-nav flex-row align-items-center ms-auto">
          <!-- Language -->
          <!-- <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
              <i class='ti ti-language rounded-circle ti-md'></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{url('lang/en')}}" data-language="en" data-text-direction="ltr">
                  <span class="align-middle">English</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item {{ app()->getLocale() === 'fr' ? 'active' : '' }}" href="{{url('lang/fr')}}" data-language="fr" data-text-direction="ltr">
                  <span class="align-middle">French</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item {{ app()->getLocale() === 'ar' ? 'active' : '' }}" href="{{url('lang/ar')}}" data-language="ar" data-text-direction="rtl">
                  <span class="align-middle">Arabic</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item {{ app()->getLocale() === 'de' ? 'active' : '' }}" href="{{url('lang/de')}}" data-language="de" data-text-direction="ltr">
                  <span class="align-middle">German</span>
                </a>
              </li>
            </ul>
          </li> -->
          <!-- Language -->
          <!--<li class="nav-item dropdown-language dropdown me-2 me-xl-0">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
              <i class='fi fi-id fis rounded-circle me-1 fs-3'></i>
            </a>-->
          <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
              <i class='ti ti-language rounded-circle ti-md'></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <a class="dropdown-item {{ session()->get('locale') === 'id' ? 'active' : '' }}"
                  href="{{url('change-language/id')}}" data-language="id">
                  <i class="fi fi-id fis rounded-circle me-1 fs-3"></i>
                  <span class="align-middle">Indonesia</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item {{ session()->get('locale') === 'en' ? 'active' : '' }}"
                  href="{{url('change-language/en')}}" data-language="en">
                  <i class="fi fi-us fis rounded-circle me-1 fs-3"></i>
                  <span class="align-middle">English</span>
                </a>
              </li>
            </ul>
          </li>
          <!--/ Language -->


          <!-- Style Switcher -->
          <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
              <i class='ti ti-md'></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
              <li>
                <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                  <span class="align-middle"><i class='ti ti-sun me-2'></i>Light</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                  <span class="align-middle"><i class="ti ti-moon me-2"></i>Dark</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                  <span class="align-middle"><i class="ti ti-device-desktop me-2"></i>System</span>
                </a>
              </li>
            </ul>
          </li>
          <!--/ Style Switcher -->



          @if(isset($menuHorizontal))
        <!-- Search -->
        <!-- <li class="nav-item navbar-search-wrapper me-2 me-xl-0">
      <a class="nav-link search-toggler" href="javascript:void(0);">
      <i class="ti ti-search ti-md"></i>
      </a>
      </li> -->
        <!-- /Search -->
      @endif


          <!--Shop -->

          <!-- Notification -->
          <!--/ Shop -->
          @if(!Auth::check() or empty(auth()->getPermissions()))
        <!--User -->
        <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
        <a class="dropdown-item" href="{{ Route::has('login') ? route('login') : url('login') }}">
          <i class='ti ti-login me-2'></i>
          <span class="align-middle">Login</span>
        </a>
        </li>
        <!--/ User -->
        <!--User -->
        <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
        <a class="dropdown-item" href="{{ Route::has('register') ? route('register') : url('register') }}">
          <i class='menu-icon tf-icons ti ti-id-badge-2'></i>
          <span class="align-middle">Register</span>
        </a>
        </li>
        <!--/ User -->
      @else



      <!-- User -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
      <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
        <div class="avatar avatar-online">
        <img
          src="{{ auth()->user()->master_data_photo ? asset('storage/' . auth()->user()->master_data_photo) : asset('assets/img/avatars/owl.png') }}"
          alt="User Avatar" class="h-40 rounded-circle"
          onerror="this.onerror=null; this.src='{{ asset('assets/img/avatars/owl.png') }}';">
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
        <a class="dropdown-item"
          href="{{ Route::has('profile.show') ? route('profile.show') : url('user/profile') }}">
          <div class="d-flex">
          <div class="flex-shrink-0 me-3">
            <div class="avatar avatar-online">
            <img
              src="{{ auth()->user()->master_data_photo ? asset('storage/' . auth()->user()->master_data_photo) : asset('assets/img/avatars/owl.png') }}"
              alt="User Avatar" class="h-40 rounded-circle"
              onerror="this.onerror=null; this.src='{{ asset('assets/img/avatars/owl.png') }}';">
            </div>
          </div>
          <div class="flex-grow-1">
            <span class="fw-semibold d-block">{{ auth()->user()->master_data_fullname }}</span>
            <small class="text-muted">{{ auth()->getActiveRole()->ar_name }}</small>
          </div>
          </div>
        </a>
        </li>
        <li>
        <div class="dropdown-divider"></div>
        </li>
        <li>
        <a class="dropdown-item" href="{{url('transaction-list')}}">
          <span class="d-flex align-items-center align-middle">
          <i class="flex-shrink-0 ti ti-credit-card me-2 ti-sm"></i>
          <span class="flex-grow-1 align-middle">{{__('common.transaction_list')}}</span>
          </span> </a>
        </li>
        <li>
        <div class="dropdown-divider"></div>
        </li>
        <li>
        <a class="dropdown-item" href="{{url('digital-book-collection')}}">
          <span class="d-flex align-items-center align-middle">
          <i class="flex-shrink-0 ti ti-book me-2 ti-sm"></i>
          <span class="flex-grow-1 align-middle">{{__('common.digital_book_collection')}}</span>
          </span> </a>
        </li>


        <li>
        <div class="dropdown-divider"></div>
        </li>
        @if (Auth::check())
      <li>
      <a class="dropdown-item" href="{{ route('logout') }}">
        <i class='ti ti-logout me-2'></i>
        <span class="align-middle">Logout</span>
      </a>
      </li>
    @else
    <li>
    <a class="dropdown-item" href="{{ route('login') }}">
      <i class='ti ti-login me-2'></i>
      <span class="align-middle">Login</span>
    </a>
    </li>
  @endif
      </ul>
      </li>
      <!--/ User -->
    @endif
        </ul>
      </div>

      <!-- Search Small Screens -->
      <div class="navbar-search-wrapper search-input-wrapper {{ isset($menuHorizontal) ? $containerNav : '' }} d-none">
        <input type="text" class="form-control search-input {{ isset($menuHorizontal) ? '' : $containerNav }} border-0"
          placeholder="Search..." aria-label="Search...">
        <i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
      </div>
      @if(isset($navbarDetached) && $navbarDetached == '')
    </div>
  @endif
  </nav>
  <!-- / Navbar -->