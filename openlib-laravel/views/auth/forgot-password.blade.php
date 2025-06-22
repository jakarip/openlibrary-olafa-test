@extends('layouts.layoutBlank')

@section('title', 'Forgot Password')

@section('vendor-style')
    <!-- Vendor CSS jika perlu -->
@endsection

@section('page-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
@endsection

@section('vendor-script')
    <!-- Vendor JS jika perlu -->
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/pages-auth.js') }}"></script>
@endsection

@section('content')
    <div class="authentication-wrapper authentication-cover authentication-bg">
        <div class="authentication-inner row">

            <!-- Left Image Section -->
            <div class="d-none d-lg-flex col-lg-7 p-0">
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                    <img src="{{ asset('assets/img/illustrations/auth-forgot-password-illustration-light.png') }}"
                        alt="auth-forgot-password-cover" class="img-fluid my-5 auth-illustration">
                    <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}"
                        alt="auth-forgot-password-cover" class="platform-bg">
                </div>
            </div>
            <!-- /Left Image Section -->

            <!-- Forgot Password Form -->
            <div class="d-flex col-12 col-lg-5 align-items-center p-sm-5 p-4">
                <div class="w-px-400 mx-auto">

                    <!-- Logo -->
                    <div class="app-brand mb-4">
                        <a href="{{ url('/') }}" class="app-brand-link gap-2">
                            <img src="{{ asset('assets/img/openlibrary/logo-hires.png') }}" height="60px" class="me-2">
                            <img src="{{ asset('assets/img/openlibrary/logo-telu.png') }}" height="45px" class="me-2">
                        </a>
                    </div>
                    <!-- /Logo -->

                    <h3 class="mb-1">Forgot Password? 🔒</h3>
                    <p class="mb-4">Enter your email and we'll send you instructions to reset your password</p>

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger d-flex align-items-center text-break"
                            style="word-wrap: break-word; white-space: normal;" role="alert">
                            <span class="alert-icon text-danger me-2">
                                <i class="ti ti-file-alert ti-xs"></i>
                            </span>
                            @foreach ($errors->all() as $error)
                                <p class="mb-0">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form id="formAuthentication" class="mb-3" action="{{ route('password.email') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" placeholder="Enter your email" autofocus value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <button class="btn btn-danger d-grid w-100 waves-effect waves-light" type="submit">
                            Send Reset Link
                        </button>
                    </form>
                    <!-- /Form -->

                    <div class="text-center">
                        <a href="{{ url('login') }}" class="d-flex align-items-center justify-content-center">
                            <i class="ti ti-chevron-left scaleX-n1-rtl"></i>
                            Back to login
                        </a>
                    </div>
                </div>
            </div>
            <!-- /Forgot Password Form -->
        </div>
    </div>
@endsection