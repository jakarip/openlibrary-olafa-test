@extends('layouts.layoutMaster')

@section('title', 'User Profile')

@section('vendor-style')
@endsection

@section('page-style')
@endsection

@section('content')
    <div class="container flex-grow-1 container-p-y">
        <div class="row align-items-stretch">
            <!-- Kolom Kiri: Profile Card -->
            <div class=" col-lg-4 col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <img src="{{ $member->master_data_photo ? asset('storage/' . $member->master_data_photo) : asset('assets/img/avatars/owl.png') }}"
                            alt="user image" class="rounded user-profile-img mb-3" style="max-width: 120px;"
                            onerror="this.onerror=null; this.src='{{ asset('assets/img/avatars/owl.png') }}';">
                        <h4>{{ $member->master_data_fullname ?? 'No Name' }}</h4>
                        <ul class="list-unstyled d-flex flex-wrap justify-content-center gap-3 mb-3">
                            <li class="d-flex align-items-center gap-2">
                                <i class="ti ti-id-badge-2"></i>
                                <span>{{ $member->master_data_type ?? '-' }}</span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <i class="ti ti-tag"></i>
                                <span>{{ $member->memberType->name ?? '-' }}</span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <i class="ti ti-calendar"></i>
                                <span>
                                    Joined
                                    @if($member->created_at instanceof \Carbon\Carbon)
                                        {{ $member->created_at->format('Y') }}
                                    @else
                                        {{ substr($member->created_at, 0, 4) }}
                                    @endif
                                </span>
                            </li>
                        </ul>
                        <a href="{{ url('user/account-setting') }}" class="btn btn-primary waves-effect waves-light">
                            <i class="ti ti-user-cog me-1"></i>Account Settings
                        </a>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: About & Contact -->
            <div class="col-lg-8 col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <h5 class="card-title">About</h5>
                                <hr />
                                <ul class="list-unstyled mb-4 mt-3">
                                    <li class="d-flex align-items-center mb-3 flex-wrap">
                                        <i class="ti ti-user text-heading me-2"></i>
                                        <span class="fw-medium text-heading me-2">Full Name:</span>
                                        <span class="text-break">
                                            {{ $member->master_data_fullname }}
                                        </span>
                                    </li>
                                    <li class="d-flex align-items-center mb-3 flex-wrap">
                                        <i class="ti ti-check text-heading me-2"></i>
                                        <span class="fw-medium text-heading me-2">Account Status:</span>
                                        @if($member->status == 1)
                                            <span class="badge bg-label-success">Active</span>
                                        @elseif($member->status == 2)
                                            <span class="badge bg-label-warning">Pending/Inactive</span>
                                        @else
                                            <span class="badge bg-label-secondary">Unknown</span>
                                        @endif
                                    </li>
                                    <li class="d-flex align-items-center mb-3 flex-wrap">
                                        <i class="ti ti-id-badge-2 text-heading me-2"></i>
                                        <span class="fw-medium text-heading me-2">Member Type:</span>
                                        <span class="text-break">
                                            {{ $member->master_data_type ?? '-' }}
                                        </span>
                                    </li>
                                    <li class="d-flex align-items-center mb-3 flex-wrap">
                                        <i class="ti ti-tag text-heading me-2"></i>
                                        <span class="fw-medium text-heading me-2">Member Class:</span>
                                        <span class="text-break">
                                            {{ $member->memberType->name ?? '-' }}
                                        </span>
                                    </li>
                                    <li class="d-flex align-items-center mb-3 flex-wrap">
                                        <i class="ti ti-file-description text-heading me-2"></i>
                                        <span class="fw-medium text-heading me-2">Membership Status:</span>
                                        <span class="text-break">
                                            {{ $member->master_data_status ?? '-' }}
                                        </span>
                                    </li>
                                    <li class="d-flex align-items-center gap-2">
                                        <i class="ti ti-calendar"></i>
                                        <span class="fw-medium text-heading ">Joined:</span>
                                        <span>
                                            @if ($member->created_at instanceof \Carbon\Carbon)
                                                {{ $member->created_at->format('d F Y') }}
                                            @else
                                                {{-- Jika bukan instance Carbon, kita parse secara manual --}}
                                                {{ \Carbon\Carbon::parse($member->created_at)->format('d F Y') }}
                                            @endif
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <!-- Contact -->
                            <div class="col-md-6">
                                <h5 class="card-title">Contact</h5>
                                <hr />
                                <ul class="list-unstyled mb-4 mt-3">
                                    <li class="d-flex align-items-center mb-3 flex-wrap">
                                        <i class="ti ti-phone-call me-2"></i>
                                        <span class="fw-medium text-heading me-2">Phone:</span>
                                        <span class="text-break">
                                            {{ $member->master_data_mobile_phone ?? '-' }}
                                        </span>
                                    </li>
                                    <li class="d-flex align-items-center mb-3 flex-wrap">
                                        <i class="ti ti-mail me-2"></i>
                                        <span class="fw-medium text-heading me-2">Email:</span>
                                        <span class="text-break">
                                            {{ $member->master_data_email }}
                                        </span>
                                    </li>
                                    <li class="d-flex align-items-center mb-3 flex-wrap">
                                        <i class="ti ti-flag me-2"></i>
                                        <span class="fw-medium text-heading me-2">Address:</span>
                                        <span class="text-break">
                                            {{ $member->master_data_address ?? '-' }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
@endsection

@section('page-script')
@endsection