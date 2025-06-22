@extends('layouts.layoutBlank')

@section('title', 'Reset Password')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
@endsection

@section('content')
    <div class="authentication-wrapper authentication-cover authentication-bg">
        <div class="authentication-inner row">

            <div class="d-none d-lg-flex col-lg-7 p-0">
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                    <img src="{{ asset('assets/img/illustrations/auth-reset-password-illustration-light.png') }}"
                        alt="auth-reset-password-cover" class="img-fluid my-5 auth-illustration">
                    <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}"
                        alt="auth-reset-password-cover" class="platform-bg">
                </div>
            </div>

            <div class="d-flex col-12 col-lg-5 align-items-center p-sm-5 p-4">
                <div class="w-px-400 mx-auto">

                    <div class="app-brand mb-4">
                        <a href="{{ url('/') }}" class="app-brand-link gap-2">
                            <img src="{{ asset('assets/img/openlibrary/logo-hires.png') }}" height="60px" class="me-2">
                            <img src="{{ asset('assets/img/openlibrary/logo-telu.png') }}" height="45px" class="me-2">
                        </a>
                    </div>

                    <h3 class="mb-1">Reset Password 🔒</h3>
                    <p class="mb-4">Masukkan password baru Anda.</p>

                    <!-- Tampilkan error jika ada -->
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

                    <!-- Form Reset Password -->
                    @if (!session('status'))
                        <form action="{{ route('password.update') }}" method="POST" class="mb-3">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="mb-3">
                                <label class="form-label" for="password">Password Baru</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror" placeholder="••••••••"
                                        aria-describedby="password" required />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Konfirmasi Password -->
                            <div class="mb-3">
                                <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        placeholder="••••••••" required />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                </div>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button class="btn btn-danger w-100" type="submit">Update Password</button>
                        </form>
                    @endif
                    <div class="text-center">
                        <a href="{{ url('login') }}" class="d-flex align-items-center justify-content-center">
                            <i class="ti ti-chevron-left scaleX-n1-rtl"></i>
                            Back to login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Sukses -->
    @if(session('status'))
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content text-center">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pb-4 mb-2">
                        <img src="{{ asset('assets/img/illustrations/boy-with-rocket-light.png') }}" alt="success" width="200"
                            class="img-fluid mb-3">
                        <h5 class="modal-title mb-2" id="successModalLabel">Berhasil!</h5>
                        <p class="mb-0">
                            {{ session('status') }}
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
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();

                setTimeout(function () {
                    window.location.href = '{{ url("login") }}';
                }, 3000);
            });
        </script>
    @endif
@endsection