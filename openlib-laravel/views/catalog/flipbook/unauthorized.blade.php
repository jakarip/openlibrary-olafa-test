@extends('layouts/layoutMaster')

@section('title', __('catalogs.flipbook.acc_denied'))

@section('content')
    <div class="container-xxl">
        <div class="misc-wrapper">
            <h2 class="mb-1 mt-4">{{ __('catalogs.flipbook.acc_denied') }} 🔐</h2>
            <p class="mb-4 mx-2">
            {{ __('catalogs.flipbook.no_permission') }}
            </p>
            <p class="mb-4 mx-2">
            {{ __('catalogs.flipbook.page_direct') }} <span id="countdown">5</span> {{ __('catalogs.flipbook.seconds') }}...
            </p>
            <a href="{{ $redirectUrl }}" class="btn btn-primary mb-4">{{ __('common.back') }}</a>
            <div class="mt-4">
                <img src="{{ asset('assets/img/illustrations/page-misc-error.png') }}" alt="page-misc-error" width="225"
                    class="img-fluid">
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        // Countdown redirect
        let countdown = 5;
        const countdownElement = document.getElementById('countdown');

        const timer = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;

            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = "{{ $redirectUrl }}";
            }
        }, 1000);
    </script>
@endsection