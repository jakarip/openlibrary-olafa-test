@extends('layouts/layoutBlank')

@section('title', 'Login')

@section('vendor-style')
  <!-- Vendor -->
@endsection

@section('page-style')
  <!-- Page -->
  <link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-auth.css')}}">
@endsection

@section('vendor-script')
@endsection

@section('page-script')
  <script src="{{asset('assets/js/pages-auth.js')}}"></script>
@endsection

@section('content')
  <div class="authentication-wrapper authentication-cover authentication-bg">
    <div class="authentication-inner row">
    <div class="d-none d-lg-flex col-lg-7 p-0">
      <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center"
      style="background-color:#F3F3F3">
      <img src="{{ asset('assets/img/openlibrary/login1.png') }}" class="img-fluid my-5 auth-illustration"
        style="width:1400px; max-height: 100%; max-width:100%">

      <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}" class="platform-bg">
      </div>
    </div>
    <!-- /Left Text -->

    <!-- Login -->
    <div class="d-flex col-12 col-lg-5 align-items-center p-sm-5 p-4">
      <div class="w-px-400 mx-auto">
      <div class="app-brand mb-4">
        <a href="{{url('/')}}" class="app-brand-link gap-2">
        <img src="{{ asset('assets/img/openlibrary/logo-hires.png') }}" height="60px" class="me-2">
        <img src="{{ asset('assets/img/openlibrary/logo-telu.png') }}" height="45px" class="me-2">
        </a>
      </div>
      <div class="mb-4" style="margin-top:30px">
        <h3 class="mb-0 fw-bold"><span class="text-danger">OPEN LIBRARY</span></h3>
        <h5 class="fw-bold mb-3" style="line-height: 1">Telkom University</h5>
        <small class="text-secondary">{{ __('common.login_description') }}</small>
      </div>
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
      <form id="formAuthentication" class="mb-3" action="{{url('login/exe')}}" method="POST">
        @csrf
        <div class="mb-3">
        <label for="username" class="form-label">{{ __('common.email_or_usn') }}</label>
        <input type="text" class="form-control @error('username') is-invalid @enderror" name="username"
          placeholder="Username" value="{{ old('username') }}" autofocus>
        @error('username')
      <div class="invalid-feedback">
        {{ $message }}
      </div>
    @enderror
        </div>

        <div class="mb-3 form-password-toggle">
        <div class="d-flex justify-content-between">
          <label class="form-label" for="password">Password</label>
          <a href="{{url('auth/forgot-password')}}">
          <small>{{ __('common.forgot_pass') }}</small>
          </a>
        </div>
        <div class="input-group input-group-merge">
          <input type="password" id="passwordx" class="form-control @error('password') is-invalid @enderror"
          name="password" placeholder="••••••••" aria-describedby="password" />
          <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
          @error('password')
        <div class="invalid-feedback">
        {{ $message }}
        </div>
      @enderror
        </div>
        </div>

        <button type="submit" class="btn btn-danger" style="width: 100%;"><i class="ti ti-login pe-1"></i>
        Login</button>
      </form>




      <div class="mt-3 text-center">
        <span class="help-block">
        {{ __('common.no_acc') }} <strong><a href="{{ url('register') }}">{{ __('common.regis_acc') }}</a></strong>
        </span>
      </div>
      </div>
    </div>
    </div>
  </div>
  <!-- Modal "Akun Belum Aktif" -->
  @if(session('inactive'))
    <div class="modal fade" id="inactiveModal" tabindex="-1" aria-labelledby="inactiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content text-center">
      <div class="modal-header border-0">
      <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pb-4 mb-2">
      <img src="{{ asset('assets/img/illustrations/girl-doing-yoga-light.png') }}" alt="inactive" width="200"
      class="img-fluid mb-3">
      <h5 class="modal-title mb-2" id="inactiveModalLabel">{{ __('common.acc_not_active') }}</h5>
      <p class="mb-0">
      {{ session('inactive') }}
      </p>
      </div>
      <div class="modal-footer justify-content-center border-0">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
      <i class="ti ti-check"></i> OK
      </button>
      </div>
    </div>
    </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
    var inactiveModal = new bootstrap.Modal(document.getElementById('inactiveModal'));
    inactiveModal.show();
    });
    </script>
  @endif
@endsection