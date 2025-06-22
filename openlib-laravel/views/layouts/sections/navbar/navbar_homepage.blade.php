<!--  Brand demo (display only for navbar-full and hide on below xl) -->
<div class="navbar-brand app-brand demo d-none d-xl-flex py-0">
    <a href="{{url('/')}}" class="app-brand-link gap-2">
        <span class="app-brand-logo demo"></span>
    </a>
</div>

<!-- ! Not required for layout-without-menu -->
<div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none  ">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
        <i class="ti ti-menu-2 ti-sm"></i>
    </a>
</div>

<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <ul class="navbar-nav flex-row align-items-center">
        {{--<li class="nav-item me-2">
            <div class="input-group input-group-merge rounded-pill" style="width:500px">
                <span class="input-group-text"><i class="ti ti-search"></i></span>
                <input type="text" class="form-control" placeholder="Find Book, Article, Journals">
            </div>
        </li>
        <li class="nav-item me-2">
            <div class="d-flex align-items-center me-3">
                <i class="ti ti-building-community ti-md me-2"></i>
                <div>
                    <small class="mb-0" style="line-height: 0.8">
                        Sistem Informasi
                    </small>
                    <h6 class="mb-0" style="font-size:16px; line-height: 0.8; color:#fff">
                        Open Library Telkom University
                    </h6>
                </div>
            </div>
        </li>
        <li class="nav-item me-2">
            <div class="d-flex align-items-center me-3">
                <i class="ti ti-user-circle ti-md me-2"></i>
                <div>
                    <small class="mb-0" style="line-height: 0.8">
                        Pegawai
                    </small>
                    <h6 class="mb-0" style="font-size:16px; line-height: 0.8; color:#fff">
                        YUDHI NUGROHO ADI
                    </h6>
                </div>
            </div>
        </li>--}}
    </ul>

    <ul class="navbar-nav flex-row align-items-center ms-auto">
        <!-- Language -->
        <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                <i class="fi fi-id fis rounded-circle me-1 fs-3"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                <a class="dropdown-item" href="http://projects.test/openlibrary/public/change-language/id" data-language="id">
                    <i class="fi fi-id fis rounded-circle me-1 fs-3"></i>
                    <span class="align-middle">Indonesia</span>
                </a>
                </li>
                <li>
                <a class="dropdown-item" href="http://projects.test/openlibrary/public/change-language/en" data-language="en">
                    <i class="fi fi-us fis rounded-circle me-1 fs-3"></i>
                    <span class="align-middle">English</span>
                </a>
                </li>
            </ul>
        </li>
        {{--<!--/ Language -->
        <li class="nav-item me-2 me-xl-0">
        <a class="nav-link hide-arrow" href="javascript:void(0);" id="style-switcher-toggle-red" style="color:#B61614" aria-label="Red Themes" data-bs-original-title="Red Themes">
            <i class="ti ti-md ti-shirt-filled"></i>
        </a>
        </li>
        <!-- Quick links  -->
    <!--<li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-2 me-xl-0">
        <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
            <i class='ti ti-user-circle ti-md me-2'></i>
            <div>
            <small class="mb-0 text-secondary" style="line-height: 0.8">Login Sebagai</small>
            <h6 class="mb-0" style="font-size:14px; line-height: 0.8">Pegawai, Kepala Sekolah, Evaluator</h6>
            </div>
        </a>
        </li>-->
        <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-2 me-xl-0">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
            <i class="ti ti-layout-grid-add ti-md"></i>
        </a>--}}
        {{--<div class="dropdown-menu dropdown-menu-end py-0">
            <div class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center py-3">
                <h5 class="text-body mb-0 me-auto">Shortcuts</h5>
                <a href="javascript:void(0)" class="dropdown-shortcuts-add text-body" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Add shortcuts" data-bs-original-title="Add shortcuts"><i class="ti ti-sm ti-apps"></i></a>
            </div>
            </div>
            <div class="dropdown-shortcuts-list scrollable-container ps">
            <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                    <i class="ti ti-calendar fs-4"></i>
                </span>
                <a href="http://projects.test/openlibrary/public/app/calendar" class="stretched-link">Calendar</a>
                <small class="text-muted mb-0">Appointments</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                    <i class="ti ti-file-invoice fs-4"></i>
                </span>
                <a href="http://projects.test/openlibrary/public/app/invoice/list" class="stretched-link">Invoice App</a>
                <small class="text-muted mb-0">Manage Accounts</small>
                </div>
            </div>
            <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                    <i class="ti ti-users fs-4"></i>
                </span>
                <a href="http://projects.test/openlibrary/public/app/user/list" class="stretched-link">User App</a>
                <small class="text-muted mb-0">Manage Users</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                    <i class="ti ti-lock fs-4"></i>
                </span>
                <a href="http://projects.test/openlibrary/public/app/access-roles" class="stretched-link">Role Management</a>
                <small class="text-muted mb-0">Permission</small>
                </div>
            </div>
            <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                    <i class="ti ti-chart-bar fs-4"></i>
                </span>
                <a href="http://projects.test/openlibrary/public" class="stretched-link">Dashboard</a>
                <small class="text-muted mb-0">User Profile</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                    <i class="ti ti-settings fs-4"></i>
                </span>
                <a href="http://projects.test/openlibrary/public/pages/account-settings-account" class="stretched-link">Setting</a>
                <small class="text-muted mb-0">Account Settings</small>
                </div>
            </div>
            <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                    <i class="ti ti-help fs-4"></i>
                </span>
                <a href="http://projects.test/openlibrary/public/pages/help-center-landing" class="stretched-link">Help Center</a>
                <small class="text-muted mb-0">FAQs &amp; Articles</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                    <i class="ti ti-square fs-4"></i>
                </span>
                <a href="http://projects.test/openlibrary/public/modal-examples" class="stretched-link">Modals</a>
                <small class="text-muted mb-0">Useful Popups</small>
                </div>
            </div>
            <div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; right: 0px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div></div></div>
        </div>
        </li>
        <!-- Quick links -->

        <!-- Notification -->
        <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
            <i class="ti ti-bell ti-md"></i>
            <span class="badge bg-danger rounded-pill badge-notifications">5</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end py-0">
            <li class="dropdown-menu-header border-bottom">
            <div class="dropdown-header d-flex align-items-center py-3">
                <h5 class="text-body mb-0 me-auto">Notification</h5>
                <a href="javascript:void(0)" class="dropdown-notifications-all text-body" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Mark all as read" data-bs-original-title="Mark all as read"><i class="ti ti-mail-opened fs-4"></i></a>
            </div>
            </li>
            <li class="dropdown-notifications-list scrollable-container ps">
            <ul class="list-group list-group-flush">
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                    <div class="avatar">
                        <img src="http://projects.test/openlibrary/public/assets/img/avatars/1.png" alt="" class="h-auto rounded-circle">
                    </div>
                    </div>
                    <div class="flex-grow-1">
                    <h6 class="mb-1">Congratulation Lettie 🎉</h6>
                    <p class="mb-0">Won the monthly best seller gold badge</p>
                    <small class="text-muted">1h ago</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                    <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                    <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="ti ti-x"></span></a>
                    </div>
                </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                    <div class="avatar">
                        <span class="avatar-initial rounded-circle bg-label-danger">CF</span>
                    </div>
                    </div>
                    <div class="flex-grow-1">
                    <h6 class="mb-1">Charles Franklin</h6>
                    <p class="mb-0">Accepted your connection</p>
                    <small class="text-muted">12hr ago</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                    <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                    <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="ti ti-x"></span></a>
                    </div>
                </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                    <div class="avatar">
                        <img src="http://projects.test/openlibrary/public/assets/img/avatars/2.png" alt="" class="h-auto rounded-circle">
                    </div>
                    </div>
                    <div class="flex-grow-1">
                    <h6 class="mb-1">New Message ✉️</h6>
                    <p class="mb-0">You have new message from Natalie</p>
                    <small class="text-muted">1h ago</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                    <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                    <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="ti ti-x"></span></a>
                    </div>
                </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                    <div class="avatar">
                        <span class="avatar-initial rounded-circle bg-label-success"><i class="ti ti-cart"></i></span>
                    </div>
                    </div>
                    <div class="flex-grow-1">
                    <h6 class="mb-1">Whoo! You have new order 🛒 </h6>
                    <p class="mb-0">ACME Inc. made new order $1,154</p>
                    <small class="text-muted">1 day ago</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                    <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                    <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="ti ti-x"></span></a>
                    </div>
                </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                    <div class="avatar">
                        <img src="http://projects.test/openlibrary/public/assets/img/avatars/9.png" alt="" class="h-auto rounded-circle">
                    </div>
                    </div>
                    <div class="flex-grow-1">
                    <h6 class="mb-1">Application has been approved 🚀 </h6>
                    <p class="mb-0">Your ABC project application has been approved.</p>
                    <small class="text-muted">2 days ago</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                    <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                    <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="ti ti-x"></span></a>
                    </div>
                </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                    <div class="avatar">
                        <span class="avatar-initial rounded-circle bg-label-success"><i class="ti ti-chart-pie"></i></span>
                    </div>
                    </div>
                    <div class="flex-grow-1">
                    <h6 class="mb-1">Monthly report is generated</h6>
                    <p class="mb-0">July monthly financial report is generated </p>
                    <small class="text-muted">3 days ago</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                    <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                    <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="ti ti-x"></span></a>
                    </div>
                </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                    <div class="avatar">
                        <img src="http://projects.test/openlibrary/public/assets/img/avatars/5.png" alt="" class="h-auto rounded-circle">
                    </div>
                    </div>
                    <div class="flex-grow-1">
                    <h6 class="mb-1">Send connection request</h6>
                    <p class="mb-0">Peter sent you connection request</p>
                    <small class="text-muted">4 days ago</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                    <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                    <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="ti ti-x"></span></a>
                    </div>
                </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                    <div class="avatar">
                        <img src="http://projects.test/openlibrary/public/assets/img/avatars/6.png" alt="" class="h-auto rounded-circle">
                    </div>
                    </div>
                    <div class="flex-grow-1">
                    <h6 class="mb-1">New message from Jane</h6>
                    <p class="mb-0">Your have new message from Jane</p>
                    <small class="text-muted">5 days ago</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                    <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                    <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="ti ti-x"></span></a>
                    </div>
                </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                    <div class="avatar">
                        <span class="avatar-initial rounded-circle bg-label-warning"><i class="ti ti-alert-triangle"></i></span>
                    </div>
                    </div>
                    <div class="flex-grow-1">
                    <h6 class="mb-1">CPU is running high</h6>
                    <p class="mb-0">CPU Utilization Percent is currently at 88.63%,</p>
                    <small class="text-muted">5 days ago</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                    <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                    <a href="javascript:void(0)" class="dropdown-notifications-archive"><span class="ti ti-x"></span></a>
                    </div>
                </div>
                </li>
            </ul>
            <div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; right: 0px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div></div></li>
            <li class="dropdown-menu-footer border-top">
            <a href="javascript:void(0);" class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40 mb-1 align-items-center">
                View all notifications
            </a>
            </li>
        </ul>
        </li>
        <!--/ Notification -->

        <!-- User -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
            <div class="avatar avatar-online">
            <img src="http://projects.test/openlibrary/public/assets/img/avatars/1.png" alt="" class="h-auto rounded-circle">
            </div>
        </a>--}}
        {{--<ul class="dropdown-menu dropdown-menu-end">
            <li>
            <a class="dropdown-item" href="">
                <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                    <div class="avatar avatar-online">
                    <img src="http://projects.test/openlibrary/public/assets/img/avatars/1.png" alt="" class="h-auto rounded-circle">
                    </div>
                </div>
                <div class="flex-grow-1">
                    <span class="fw-semibold d-block">YUDHI NUGROHO ADI</span>
                    <small class="text-muted">Administrator</small>
                </div>
                </div>
            </a>
            </li>
            
            <li>
            <div class="dropdown-divider"></div>
            </li>
                        <li>
            <a class="dropdown-item" href="http://projects.test/openlibrary/public/login/logout">
                <i class="ti ti-logout me-2"></i>
                <span class="align-middle">Logout</span>
            </a>
            </li>
            <form method="POST" id="logout-form" action="">
            <input type="hidden" name="_token" value="Q4189qUgDHp3yjGNvS8hvgfLkjgLBkYknW0AoDJu" spellcheck="false">              </form>
                        </ul>
        </li>
        <!--/ User -->--}}
        <li class="nav-item">
            <a href="{{ url('login') }}" class="btn btn-label-dark rounded-pill waves-effect" style="margin:9px">
                Login Member <i class="ti ti-logout ms-2"></i>
            </a>
        </li>
    </ul>
</div>

{{--<!-- Search Small Screens -->
<div class="navbar-search-wrapper search-input-wrapper container-xxl d-none">
<span class="twitter-typeahead" style="position: relative; display: inline-block;"><input type="text" class="form-control search-input border-0 tt-input" placeholder="Search..." aria-label="Search..." spellcheck="false" autocomplete="off" dir="auto" style="position: relative; vertical-align: top;"><pre aria-hidden="true" style="position: absolute; visibility: hidden; white-space: pre; font-family: &quot;Public Sans&quot;, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Oxygen, Ubuntu, Cantarell, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; font-size: 15px; font-style: normal; font-variant: normal; font-weight: 400; word-spacing: 0px; letter-spacing: 0px; text-indent: 0px; text-rendering: auto; text-transform: none;"></pre><div class="tt-menu navbar-search-suggestion ps" style="position: absolute; top: 100%; left: 0px; z-index: 100; display: none;"><div class="tt-dataset tt-dataset-pages"></div><div class="tt-dataset tt-dataset-files"></div><div class="tt-dataset tt-dataset-members"></div><div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; right: 0px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div></div></div></span>
<i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
</div>--}}