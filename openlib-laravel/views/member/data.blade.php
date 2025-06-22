@extends('layouts.layoutMaster')

@section('title', __('memberships.data_member'))

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <script src="{{ asset('assets/vendor/libs/toastr/toastr.js') }}"></script>
@endsection

@section('page-style')
    <style>
        .select2-dropdown {
            z-index: 99999 !important;
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>{{ __('memberships.data_member')}}</h5>
            <div>
                <button class="btn btn-danger me-2" onclick="addMember()">
                    <i class="ti ti-user-plus me-1"></i> {{__('config.member.form.add')}}
                </button>
                <button class="btn btn-label-primary waves-effect" data-bs-toggle="modal"
                    data-bs-target="#columnSettingsModal">
                    <i class="ti ti-settings"></i> {{__('config.member.select_additional_data')}}
                </button>
            </div>
        </div>

        <div class="card-body">
            <form id="filterForm">
                <div class="row">
                    <div class="col-md-4">
                        <label for="memberType" class="form-label">{{__('config.file_type.input.member_type')}}</label>
                        <select id="memberType" class="form-select select2">
                            <option value="">{{__('common.all')}}</option>
                            @foreach ($memberTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" class="form-select select2">
                            <option value="">{{__('common.all')}}</option>
                            <option value="1">{{__('common.active')}}</option>
                            <option value="2">{{__('common.in_active')}}</option>
                            <option value="3">Suspended</option>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" id="filterBtn" class="btn btn-primary">
                            <i class="ti ti-search me-1"></i> {{__('config.file_type.input.member_type')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Container table dengan scroll horizontal -->
        <div class="card-datatable table-responsive overflow-auto pt-0">
            <table class="datatables-basic table border-top" id="memberTable">
                <thead>
                    <tr id="tableHeader">
                        <th width="10%">{{ __('common.action')}}</th>
                        <th>{{ __('config.member.full_name')}}</th>
                        <th>Email</th>
                        <th>{{ __('config.member.phone_number')}}</th>
                        <th>{{ __('config.workflow_designer.input.member_type')}}</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Member -->
    <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('config.member.form.add')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">{{__('memberships.complete_information')}}</p>

                    <form id="addMemberForm" class="row g-3 needs-validation" novalidate>
                        @csrf

                        <!-- Fullname (required) -->
                        <div class="col-12">
                            <label class="form-label" for="addMemberFullName">
                                {{__('config.member.full_name')}} <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="addMemberFullName" name="master_data_fullname" class="form-control"
                                required />
                            <div class="invalid-feedback">
                                {{__('config.member.full_name_required')}}
                            </div>
                        </div>

                        <!-- Email (required) -->
                        <div class="col-md-6">
                            <label class="form-label" for="addMemberEmail">
                                Email <span class="text-danger">*</span>
                            </label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                <input type="email" id="addMemberEmail" name="master_data_email" class="form-control"
                                    placeholder="example@domain.com" required />
                                <div class="invalid-feedback">
                                    {{__('config.member.email_invalid')}}
                                </div>
                            </div>
                        </div>

                        <!-- No. Handphone (required) -->
                        <div class="col-md-6">
                            <label class="form-label" for="addMemberPhone">
                                {{__('config.member.phone_number')}} <span class="text-danger">*</span>
                            </label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="ti ti-phone"></i></span>
                                <input type="text" id="addMemberPhone" name="master_data_mobile_phone" class="form-control"
                                    placeholder="08xxxxxxxx" pattern="^08\d{7,12}$"
                                    title="{{__('common.phone_number_title')}}" required />
                                <div class="invalid-feedback">
                                    {{__('config.member.phone_number_required')}}
                                </div>
                            </div>
                        </div>

                        <!-- Tipe Member (required) -->
                        <div class="col-md-6">
                            <label class="form-label" for="addMemberType">
                                {{__('config.workflow_designer.input.member_type')}} <span class="text-danger">*</span>
                            </label>
                            <select id="addMemberType" name="member_type_id" class="form-select select2" required>
                                <option value="" selected>{{__('config.member.select_type')}}</option>
                                @foreach ($memberTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                {{__('memberships.select_type_member')}}
                            </div>
                        </div>

                        <!-- Status (required) -->
                        <div class="col-md-6">
                            <label class="form-label" for="addMemberStatus">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select id="addMemberStatus" name="status" class="form-select select2" required>
                                <option value="1">{{__('config.member.status_active')}}</option>
                                <option value="2">{{__('config.member.status_inactive')}}</option>
                                <option value="3">{{__('config.member.status_suspended')}}</option>
                            </select>
                            <div class="invalid-feedback">
                                {{__('config.member.select_status')}}
                            </div>
                        </div>

                        <!-- Kelas Member (default = Anggota, tidak wajib) -->
                        <div class="col-md-6">
                            <label class="form-label" for="addMemberClass">{{__('config.member.class')}}</label>
                            <select id="addMemberClass" name="member_class_id" class="form-select select2">
                                <option value="">{{__('config.member.select_class')}}</option>
                                @foreach ($memberClasses as $class)
                                    <option value="{{ $class->id }}" @if(strtolower($class->name) === 'anggota') selected @endif>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Additional Data (opsional) -->
                        <div class="col-12 mt-3">
                            <h5>{{__('config.member.additional_data')}}</h5>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="addMemberNumber">{{__('config.member.number')}}</label>
                            <input type="text" id="addMemberNumber" name="master_data_user" class="form-control"
                                placeholder="{{__('memberships.placeholder_member_number')}}" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label"
                                for="addSubscriptionStatus">{{__('config.member.subs_status')}}</label>
                            <input type="text" id="addSubscriptionStatus" name="master_data_status" class="form-control"
                                placeholder="{{__('memberships.placeholder_member_status')}}" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="addMemberIjasah">{{__('config.member.degree_data')}}</label>
                            <input type="text" id="addMemberIjasah" name="master_data_ijasah" class="form-control"
                                placeholder="{{__('memberships.placeholder_member_degree')}}" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="addMemberKTP">{{__('config.member.id_card')}}</label>
                            <input type="text" id="addMemberKTP" name="master_data_ktp" class="form-control"
                                placeholder="{{__('memberships.placeholder_member_idCard')}}" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="addMemberKTM">{{__('config.member.student_card')}}</label>
                            <input type="text" id="addMemberKTM" name="master_data_idcard" class="form-control"
                                placeholder="{{__('memberships.placeholder_member_studentCard')}}" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        {{ __('common.cancel')}}
                    </button>
                    <button type="button" class="btn btn-primary" onclick="saveMember()">
                        {{ __('common.save')}}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Member -->
    <div class="modal fade" id="editUser" tabindex="-1" aria-labelledby="editUserLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('config.member.edit')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">{{ __('config.member.edit_info')}}</p>

                    <form id="editUserForm" class="row g-3 needs-validation" novalidate>
                        @csrf
                        <!-- Hidden input untuk menyimpan ID member -->
                        <input type="hidden" id="editMemberId" name="id" />

                        <!-- Fullname (Required) -->
                        <div class="col-12">
                            <label class="form-label" for="modalEditUserFullName">
                                {{__('config.member.full_name')}} <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="modalEditUserFullName" name="master_data_fullname" class="form-control"
                                required />
                            <div class="invalid-feedback">
                                {{__('config.member.full_name_info')}}
                            </div>
                        </div>

                        <!-- Email (Required) -->
                        <div class="col-md-6">
                            <label class="form-label" for="modalEditUserEmail">
                                Email <span class="text-danger">*</span>
                            </label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                <input type="email" id="modalEditUserEmail" name="master_data_email" class="form-control"
                                    placeholder="example@domain.com" required />
                                <div class="invalid-feedback">
                                    {{__('config.member.email_info')}}
                                </div>
                            </div>
                        </div>

                        <!-- No. Handphone (required) -->
                        <div class="col-md-6">
                            <label class="form-label" for="modalEditUserPhone">
                                {{__('config.member.phone_number')}} <span class="text-danger">*</span>
                            </label>
                            <div class="input-group has-validation">
                                <span class="input-group-text"><i class="ti ti-phone"></i></span>
                                <input type="text" id="modalEditUserPhone" name="master_data_mobile_phone"
                                    class="form-control" placeholder="08xxxxxxxx" pattern="^08\d{7,12}$"
                                    title="{{__('common.phone_number_title')}}" required />
                                <div class="invalid-feedback">
                                    {{__('config.member.phone_number_info')}}
                                </div>
                            </div>
                        </div>

                        <!-- Tipe Member (required) -->
                        <div class="col-md-6">
                            <label class="form-label" for="addMemberType">
                                {{__('config.workflow_designer.input.member_type')}} <span class="text-danger">*</span>
                            </label>
                            <select id="modalEditUserType" name="member_type_id" class="form-select select2" required>
                                @foreach ($memberTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status (required) -->
                        <div class="col-md-6">
                            <label class="form-label" for="addMemberStatus">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select id="modalEditUserStatus" name="status" class="form-select select2" required>
                                <option value="1">{{__('config.member.status_active')}}</option>
                                <option value="2">{{__('config.member.status_inactive')}}</option>
                                <option value="3">{{__('config.member.status_suspended')}}</option>
                            </select>
                        </div>

                        <!-- Additional Data (Opsional) -->
                        <div class="col-12 mt-3">
                            <h5>{{__('config.member.additional_data')}}</h5>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="modalEditUserNumber">{{__('config.member.number')}}</label>
                            <input type="text" id="modalEditUserNumber" name="master_data_user" class="form-control"
                                placeholder="{{__('memberships.placeholder_member_number')}}" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="modalEditUserSubscriptionStatus">{{__('config.member.subs_status')}}</label>
                            <input type="text" id="modalEditUserSubscriptionStatus" name="master_data_status"
                                class="form-control" placeholder="{{__('memberships.placeholder_member_status')}}" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="modalEditUserIjasah">{{__('config.member.degree_data')}}</label>
                            <input type="text" id="modalEditUserIjasah" name="master_data_ijasah" class="form-control"
                                placeholder="{{__('memberships.placeholder_member_degree')}}" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="modalEditUserKTP">{{__('config.member.id_card')}}</label>
                            <input type="text" id="modalEditUserKTP" name="master_data_ktp" class="form-control"
                                placeholder="{{__('memberships.placeholder_member_idCard')}}" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="modalEditUserKTM">{{__('memberships.placeholder_member_studentCard')}}</label>
                            <input type="text" id="modalEditUserKTM" name="master_data_idcard" class="form-control"
                                placeholder="{{__('memberships.placeholder_member_number')}}" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        {{ __('common.cancel')}}
                    </button>
                    <button type="submit" form="editUserForm" class="btn btn-primary">
                        {{ __('common.save')}}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reject Member -->
    <div class="modal fade" id="rejectMemberModal" tabindex="-1" aria-labelledby="rejectMemberModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectMemberModalLabel">{{ __('memberships.reject')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="rejectMemberForm">
                        <div class="mb-3">
                            <label for="rejectReason" class="form-label">{{ __('memberships.reason_rejection')}}</label>
                            <textarea class="form-control" id="rejectReason" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('common.cancel')}}</button>
                    <button type="button" class="btn btn-danger" id="rejectSubmitBtn">{{ __('memberships.reject')}}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal File Preview -->
    <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filePreviewModalLabel">{{ __('memberships.review_file')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="filePreviewContent">
                    <!-- Konten file akan dimuat di sini -->
                </div>
                <div class="modal-footer">
                    <a id="openInNewTab" href="#" target="_blank" class="btn btn-primary">{{ __('memberships.new_page')}}</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('memberships.close')}}</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Pilihan Kolom Tambahan -->
    <div class="modal fade" id="columnSettingsModal" tabindex="-1" aria-labelledby="columnSettingsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="ti ti-settings me-2"></i> {{__('config.member.select_additional_data')}}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="columnSettingsForm">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="selectAllColumns">
                            <label class="form-check-label fw-bold"
                                for="selectAllColumns">{{ __('config.member.select_all')}}</label>
                        </div>
                        <hr>
                        <div class="row">
                            <!-- Kolom Tambahan Kiri -->
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox" value="master_data_user"
                                        id="nomorAnggota">
                                    <label class="form-check-label"
                                        for="nomorAnggota">{{__('config.member.number')}}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox"
                                        value="master_data_status" id="statusLangganan">
                                    <label class="form-check-label"
                                        for="statusLangganan">{{__('config.member.subs_status')}}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox"
                                        value="master_data_ijasah" id="dataIjasah">
                                    <label class="form-check-label"
                                        for="dataIjasah">{{__('config.member.degree_data')}}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox" value="master_data_ktp"
                                        id="dataKTP">
                                    <label class="form-check-label" for="dataKTP">{{__('config.member.id_card')}}</label>
                                </div>
                            </div>
                            <!-- Kolom Tambahan Kanan -->
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox"
                                        value="master_data_idcard" id="dataKTM">
                                    <label class="form-check-label"
                                        for="dataKTM">{{__('config.member.student_card')}}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox" value="created_at"
                                        id="tglDibuat">
                                    <label class="form-check-label"
                                        for="tglDibuat">{{__('config.member.date_created')}}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox" value="updated_at"
                                        id="tglDiperbarui">
                                    <label class="form-check-label"
                                        for="tglDiperbarui">{{__('config.member.date_updated')}}</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i> {{ __('common.cancel')}}
                    </button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" id="applyColumns">
                        <i class="ti ti-check me-1"></i> {{__('common.apply')}}
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
@endsection

@section('page-script')
    <script>
        let memberTable = null;

        $(document).ready(function () {
            const defaultColumns = [
                { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' },
                { data: 'master_data_fullname', name: 'master_data_fullname' },
                { data: 'master_data_email', name: 'master_data_email' },
                { data: 'master_data_mobile_phone', name: 'master_data_mobile_phone' },
                { data: 'member_type_name', name: 'member_type_name' },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data) {
                        let badgeClass = '', statusText = '';
                        switch (parseInt(data)) {
                            case 1: badgeClass = 'bg-label-success'; statusText = 'Active'; break;
                            case 2: badgeClass = 'bg-label-warning'; statusText = 'Tidak Aktif'; break;
                            case 3: badgeClass = 'bg-label-danger'; statusText = 'Suspended'; break;
                            default: badgeClass = 'bg-label-secondary'; statusText = 'Unknown';
                        }
                        return `<span class="badge ${badgeClass}">${statusText}</span>`;
                    }
                }
            ];

            const additionalColumns = {
                master_data_user: { data: 'master_data_user', name: 'master_data_user' },
                master_data_status: { data: 'master_data_status', name: 'master_data_status' },
                master_data_ktp: {
                    data: 'master_data_ktp',
                    render: function (path) {
                        if (!path || path === '-') return '-';
                        let fileUrl = '/storage/' + path;
                        return `
                                                                                    <button class="btn btn-icon btn-label-primary view-file" data-url="${fileUrl}">
                                                                                        <i class="ti ti-eye"></i>
                                                                                    </button>`;
                    }
                },

                master_data_ijasah: {
                    data: 'master_data_ijasah',
                    name: 'master_data_ijasah',
                    class: 'text-center',
                    render: function (path) {
                        if (!path || path === '-') return '-';
                        let fileUrl = '/storage/' + path;
                        return `
                                                                                    <button class="btn btn-icon btn-label-primary view-file" data-url="${fileUrl}">
                                                                                        <i class="ti ti-eye"></i>
                                                                                    </button>`;
                    }
                },

                master_data_idcard: {
                    data: 'master_data_idcard',
                    name: 'master_data_idcard',
                    class: 'text-center',
                    render: function (path) {
                        if (!path || path === '-') return '-';
                        let fileUrl = '/storage/' + path;
                        return `
                                                                                <button class="btn btn-icon btn-label-primary view-file" data-url="${fileUrl}">
                                                                                    <i class="ti ti-eye"></i>
                                                                                </button>`;
                    }
                },

                created_at: { data: 'created_at', name: 'created_at' },
                updated_at: { data: 'updated_at', name: 'updated_at' }
            };
            $(document).on('click', '.approve-btn', function () {
                let id = $(this).data('id');
                $.ajax({
                    url: '/member/approve/' + id,
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        toastr.success(response.message, 'Sukses', { closeButton: true, progressBar: true });
                        if (memberTable) {
                            memberTable.ajax.reload();
                        }
                    },
                    error: function (xhr) {
                        toastr.error('Terjadi kesalahan saat mengaktifkan member.', 'Gagal', { closeButton: true, progressBar: true });
                    }
                });
            });

            // Event handler untuk tombol reject
            $(document).on('click', '.reject-btn', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: '{{__("memberships.reject")}}',
                    text: '{{__("memberships.reason_rejection")}}:',
                    input: 'textarea',
                    inputPlaceholder: '{{__("memberships.reason_rejection")}}...',
                    showCancelButton: true,
                    confirmButtonText: '{{__("memberships.reject_title")}}',
                    cancelButtonText: '{{__("common.cancel")}}',
                    inputValidator: (value) => {
                        if (!value) {
                            return '{{__("memberships.required_reason")}}';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/member/reject/' + id,
                            type: 'POST',
                            data: { reason: result.value },
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            success: function (response) {
                                Swal.fire('Sukses', response.message, 'success');
                                if (memberTable) {
                                    memberTable.ajax.reload();
                                }
                            },
                            error: function (xhr) {
                                Swal.fire('Gagal', xhr.responseJSON.message || 'Terjadi kesalahan saat menolak member.', 'error');
                            }
                        });
                    }
                });
            });


            $(document).on('click', '.view-file', function () {
                let fileUrl = $(this).data('url');
                showModal(fileUrl);
            });

            async function fetchAndCacheFile(url) {
                try {
                    console.log("Fetching file:", url);
                    let response = await fetch(url, { method: 'GET', mode: 'cors' });

                    if (!response.ok) throw new Error(`Failed to fetch file: ${response.statusText}`);

                    let blob = await response.blob();
                    console.log("File fetched successfully:", url);

                    return blob;
                } catch (error) {
                    console.error("Fetch error:", error);
                    return null;
                }
            }

            async function showModal(url) {
                console.log("Fetching file:", url);

                // Set tombol "Buka di Halaman Baru"
                $("#openInNewTab").attr("href", url);

                // Coba ambil dari LocalStorage dulu
                let cachedFile = localStorage.getItem(url);

                if (cachedFile) {
                    console.log("Loaded from cache:", url);
                    displayContent(cachedFile);
                } else {
                    try {
                        let blob = await fetchAndCacheFile(url);

                        if (!blob) {
                            console.log("Blob is null, using fallback.");
                            displayContent(url, true);
                            return;
                        }

                        let objectURL = URL.createObjectURL(blob);
                        console.log("Created Object URL:", objectURL);
                        displayContent(objectURL);

                        // Simpan ke LocalStorage (Hanya jika ukurannya kecil < 5MB)
                        let reader = new FileReader();
                        reader.readAsDataURL(blob);
                        reader.onloadend = function () {
                            console.log("Saving to LocalStorage:", url);
                            localStorage.setItem(url, reader.result);
                        };

                    } catch (error) {
                        console.error("Error fetching file:", error);
                        displayContent(url, true);
                    }
                }
            }

            function displayContent(fileUrl, isFallback = false) {
                console.log("Displaying file:", fileUrl);

                let content = '';

                if (!fileUrl) {
                    content = `<p class="text-center text-danger">
                                                                                                                                                                                                        <i class="ti ti-alert-triangle"></i> {{__('memberships.file_cannot_displayed')}}
                                                                                                                                                                                                    </p>`;
                } else if (fileUrl.startsWith("blob:") || fileUrl.startsWith("data:image") || fileUrl.endsWith(".jpg") || fileUrl.endsWith(".png") || fileUrl.endsWith(".jpeg")) {
                    console.log("Rendering image:", fileUrl);
                    content = `<img src="${fileUrl}" class="img-fluid rounded" alt="Preview">`;
                } else if (fileUrl.startsWith("data:application/pdf") || fileUrl.endsWith(".pdf")) {
                    console.log("Rendering PDF:", fileUrl);
                    content = `<iframe src="${fileUrl}" width="100%" height="500px" style="border: none;"></iframe>`;
                } else if (fileUrl.endsWith(".mp4") || fileUrl.endsWith(".webm") || fileUrl.endsWith(".ogg")) {
                    console.log("Rendering Video:", fileUrl);
                    content = `<video controls width="100%">
                                                                                                                                                                                                                                                      <source src="${fileUrl}" type="video/mp4">
                                                                                                                                                                                                                                                      Your browser does not support the video tag.
                                                                                                                                                                                                                                                   </video>`;
                } else if (fileUrl.endsWith(".mp3") || fileUrl.endsWith(".wav") || fileUrl.endsWith(".ogg")) {
                    console.log("Rendering Audio:", fileUrl);
                    content = `<audio controls>
                                                                                                                                                                                                                                                      <source src="${fileUrl}" type="audio/mpeg">
                                                                                                                                                                                                                                                      Your browser does not support the audio element.
                                                                                                                                                                                                                                                   </audio>`;
                } else {
                    console.log("Rendering as Download Link:", fileUrl);
                    content = `<p class="text-center">
                                                                                                        <i class="ti ti-file"></i> {{__('memberships.file_not_display_modal')}} <br>
                                                                                                        <a href="${fileUrl}" target="_blank" class="btn btn-label-primary mt-2">
                                                                                                        <i class="ti ti-external-link"></i> {{__('memberships.new_page')}}
                                                                                                        </a>
                                                                                                    </p>`;
                }

                $('#filePreviewContent').html(content);
                $('#filePreviewModal').modal('show');
            }


            // Fungsi untuk mendapatkan tipe file berdasarkan URL
            async function getFileType(url) {
                try {
                    let response = await fetch(url, { method: 'HEAD' }); // Request hanya metadata (tanpa download full file)
                    let contentType = response.headers.get('Content-Type');

                    if (contentType.includes('image')) return 'image';
                    if (contentType.includes('pdf')) return 'pdf';
                    if (contentType.includes('video')) return 'video';
                    if (contentType.includes('audio')) return 'audio';
                    if (contentType.includes('text') || contentType.includes('html')) return 'text';

                    return 'unknown';
                } catch (error) {
                    console.error('Error fetching file type:', error);
                    return 'unknown';
                }
            }


            let selectedColumns = [];

            // Fungsi untuk mereset struktur table
            function resetTableStructure() {
                $("#memberTable").empty();
                $("#memberTable").append(
                    `
                                                                                                    <thead>
                                                                                                    <tr id="tableHeader">
                                                                                                    <th width="10%">{{ __('common.action')}}</th>
                                                                                                    <th>{{ __('config.member.full_name')}}</th>
                                                                                                    <th>Email</th>
                                                                                                    <th>{{ __('config.member.phone_number')}}</th>
                                                                                                    <th>{{ __('config.workflow_designer.input.member_type')}}</th>
                                                                                                    <th>Status</th>
                                                                                                    </tr>
                                                                                                    </thead>
                                                                                                    <tbody></tbody>
                                                                                                    `
                );
            }

            function updateTableHeader() {
                let defaultHeaders = `

                                                                                                        <th width="10%">{{ __('common.action')}}</th>
                                                                                                        <th>{{ __('config.member.full_name')}}</th>
                                                                                                        <th>Email</th>
                                                                                                        <th>{{ __('config.member.phone_number')}}</th>
                                                                                                        <th>{{ __('config.workflow_designer.input.member_type')}}</th>
                                                                                                        <th>Status</th>

                                                                                                    `;
                const additionalHeaders = {
                    master_data_user: '<th>{{__("config.member.number")}}</th>',
                    master_data_status: '<th>{{__("config.member.subs_status")}}</th>',
                    master_data_ijasah: '<th{{__("config.member.degree_data")}}</th>',
                    master_data_ktp: '<th>{{__("config.member.id_card")}}</th>',
                    master_data_idcard: '<th>{{__("config.member.student_card")}}</th>',
                    created_at: '<th>{{__("sbkps.created_date")}}</th>',
                    updated_at: '<th>{{__("config.holiday.updatedAt")}}</th>'
                };

                selectedColumns.forEach(col => {
                    if (additionalHeaders[col]) {
                        defaultHeaders += additionalHeaders[col];
                    }
                });
                $('#memberTable thead tr').html(defaultHeaders);
            }

            function initDataTable() {
                if ($.fn.dataTable.isDataTable('#memberTable')) {
                    $('#memberTable').DataTable().clear().destroy();
                }

                resetTableStructure();
                updateTableHeader();

                // Simpan instance DataTable ke variabel global memberTable
                memberTable = $('#memberTable').DataTable({
                    processing: true,
                    serverSide: true,
                    scrollX: true,
                    responsive: false,
                    autoWidth: false,
                    ajax: {
                        url: '{{ url("member/data/dt") }}',
                        type: 'POST',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        data: function (d) {
                            d.member_type_id = $('#memberType').val();
                            d.status = $('#status').val();
                            d.selected_columns = selectedColumns;
                        }
                    },
                    columns: [
                        ...defaultColumns,
                        ...selectedColumns.map(col => additionalColumns[col])
                    ]
                });
            }


            // Event saat tombol "Terapkan" pada modal kolom tambahan diklik
            $('#applyColumns').on('click', function () {
                selectedColumns = $('.column-checkbox:checked').map(function () {
                    return this.value;
                }).get();

                initDataTable();

                $('#columnSettingsModal').modal('hide');
            });

            $('#selectAllColumns').on('change', function () {
                $('.column-checkbox').prop('checked', $(this).is(':checked'));
            });

            $('#filterBtn').on('click', function () {
                $('#memberTable').DataTable().ajax.reload();
            });

            $(document).on('click', '.edit-btn', function () {
                let id = $(this).data('id');
                editMember(id);
            });

            $(document).on('click', '.delete-btn', function () {
                let id = $(this).data('id');
                deleteMember(id);
            });

            // Fungsi untuk mengambil data dari server dan isi form
            function editMember(id) {
                $.get('{{ url("member/data/edit") }}/' + id, function (data) {
                    if (!data) {
                        alert('Data tidak ditemukan.');
                        return;
                    }

                    // Isi form dengan data dari server
                    $('#editMemberId').val(data.id);
                    $('#modalEditUserFullName').val(data.master_data_fullname);
                    $('#modalEditUserEmail').val(data.master_data_email);
                    $('#modalEditUserPhone').val(data.master_data_mobile_phone);
                    $('#modalEditUserType').val(data.member_type_id).trigger('change');
                    $('#modalEditUserStatus').val(data.status).trigger('change');

                    // Additional Data
                    $('#modalEditUserNumber').val(data.master_data_user);
                    $('#modalEditUserSubscriptionStatus').val(data.master_data_status);
                    $('#modalEditUserIjasah').val(data.master_data_ijasah);
                    $('#modalEditUserKTP').val(data.master_data_ktp);
                    $('#modalEditUserKTM').val(data.master_data_idcard);

                    const editForm = document.getElementById('editUserForm');
                    editForm.classList.remove('was-validated');

                    $('#editUser').modal('show');
                }).fail(function (xhr) {
                    alert('Terjadi kesalahan saat mengambil data.');
                    console.error('Error:', xhr.responseText);
                });
            }

            $('#editUserForm').on('submit', function (e) {
                e.preventDefault();

                const form = this;

                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    return;
                }

                let formData = $(form).serialize();
                let submitBtn = $(form).find('button[type="submit"]');

                submitBtn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: '{{ url("member/data/update") }}/' + $('#editMemberId').val(),
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: formData,
                    success: function (response) {
                        $('#editUser').modal('hide');
                        toastr.success('Data berhasil diperbarui!', 'Sukses', {
                            closeButton: true,
                            progressBar: true
                        });

                        if (memberTable) {
                            memberTable.ajax.reload();
                        }
                    },
                    error: function (xhr) {
                        console.error('Error:', xhr.responseText);
                        toastr.error('Terjadi kesalahan saat memperbarui data.', 'Gagal', {
                            closeButton: true,
                            progressBar: true
                        });
                    },
                    complete: function () {
                        submitBtn.prop('disabled', false).text('Simpan');
                    }
                });
            });

            function deleteMember(id) {
                Swal.fire({
                    title: '{{__("common.message_delete_prompt_title")}}',
                    text: "{{__('memberships.data_cannot_restore')}}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '{{__("common.delete_data")}}',
                    cancelButtonText: '{{__("common.cancel")}}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url("member/data/delete") }}/' + id,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                memberTable.ajax.reload();
                                toastr.success('Member berhasil dihapus!', 'Sukses', { closeButton: true, progressBar: true });
                            },
                            error: function (xhr) {
                                console.error('Error:', xhr.responseText);
                                toastr.error('Terjadi kesalahan saat menghapus data.', 'Gagal', { closeButton: true, progressBar: true });
                            }
                        });
                    }
                });
            }


            initDataTable();
        });

        function addMember() {
            $('#addMemberForm')[0].reset();

            $('#addMemberType, #addMemberStatus, #addMemberClass').val(null).trigger('change');

            $('#addMemberModal').modal('show');
        }

        function saveMember() {
            const form = $('#addMemberForm');

            if (!form[0].checkValidity()) {
                form[0].classList.add('was-validated');
                return;
            }
            const formData = form.serialize();

            const saveButton = $('button[onclick="saveMember()"]');
            saveButton.prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: '{{ url("member/data/insert") }}',
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: formData,
                success: function (response) {
                    $('#addMemberModal').modal('hide');

                    toastr.success(response.message || 'Member berhasil ditambahkan!', 'Sukses', {
                        closeButton: true,
                        progressBar: true
                    });

                    $('#memberTable').DataTable().ajax.reload();
                },
                error: function (xhr) {
                    console.error('Error:', xhr.responseText);

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON?.errors;
                        if (errors) {
                            const errorMessages = Object.values(errors)
                                .map(msgArray => msgArray.join(', '))
                                .join('<br>');
                            toastr.error(errorMessages, 'Validasi Gagal', {
                                closeButton: true,
                                progressBar: true,
                                timeOut: 10000
                            });
                        } else {
                            toastr.error(xhr.responseJSON.message || 'Terjadi kesalahan validasi.', 'Gagal', {
                                closeButton: true,
                                progressBar: true
                            });
                        }
                    } else {
                        toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan saat menambahkan member.', 'Gagal', {
                            closeButton: true,
                            progressBar: true
                        });
                    }
                },
                complete: function () {
                    saveButton.prop('disabled', false).text('Simpan');
                }
            });
        }
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('addMemberForm');
            form.addEventListener('submit', function (e) {
                if (!this.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                this.classList.add('was-validated');
            }, false);
        });
        document.addEventListener('DOMContentLoaded', function () {
            const editUserForm = document.getElementById('editUserForm');
            editUserForm.addEventListener('submit', function (event) {
                if (!editUserForm.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                editUserForm.classList.add('was-validated');
            }, false);
        });
        $(document).on('click', '.my-dropdown-toggle', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const $btn = $(this);
            const $btnGroup = $btn.closest('.my-btn-group');
            const $menu = $btnGroup.find('.dropdown-menu').first();

            if ($menu.hasClass('portal-open')) {
                closePortalDropdown($menu);
                return;
            }

            $('.dropdown-menu.portal-open').each(function () {
                closePortalDropdown($(this));
            });

            openPortalDropdown($btn, $menu);
        });

        function openPortalDropdown($btn, $menu) {
            if (!$menu.data('original-parent')) {
                $menu.data('original-parent', $menu.parent());
            }

            $('body').append($menu);

            const rect = $btn[0].getBoundingClientRect();

            $menu.css({
                position: 'absolute',
                top: (rect.bottom + window.scrollY) + 'px',
                left: (rect.left + window.scrollX) + 'px',
                display: 'block',
                zIndex: 2
            }).addClass('portal-open');

            $(document).on('click.portalDropdown', function (ev) {
                if (!$(ev.target).closest($menu).length && ev.target !== $btn[0]) {
                    closePortalDropdown($menu);
                    $(document).off('click.portalDropdown');
                }
            });
        }

        function closePortalDropdown($menu) {
            $menu.removeClass('portal-open').hide();
            const originalParent = $menu.data('original-parent');
            if (originalParent) {
                originalParent.append($menu);
            }
            $(document).off('click.portalDropdown');
        }

        $(document).on('click', '.dropdown-menu a', function () {
            const $menu = $(this).closest('.dropdown-menu');
            closePortalDropdown($menu);
        });

    </script>
@endsection