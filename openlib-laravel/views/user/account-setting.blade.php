@extends('layouts.layoutMaster')

@section('title', 'Account Settings')

@section('vendor-style')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('page-style')
    <style>
    </style>
@endsection

@section('content')
    <div class="container flex-grow-1 container-p-y">
        <div class="row fv-plugins-icon-container">
            <div class="col-md-12">
                <ul class="nav nav-pills flex-column flex-md-row mb-4" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#account" role="tab" aria-selected="true">
                            <i class="ti-xs ti ti-users me-1"></i> Account
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#security" role="tab" aria-selected="false">
                            <i class="ti-xs ti ti-lock me-1"></i> Security
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="account" role="tabpanel">
                        <div class="mb-4">
                            <h5 class="mb-3">Profile Details</h5>

                            <div class="d-flex align-items-start align-items-sm-center gap-4 mb-3">
                                <img src="{{ asset('storage/' . $member->master_data_photo) }}"
                                    onerror="this.onerror=null; this.src='{{ asset('assets/img/avatars/owl.png') }}';"
                                    alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" />

                                
@php
    $roleId = session('activeRole')->ar_id ?? null;
@endphp

                                <div class="button-wrapper">
                                    <button type="button" class="btn btn-primary me-2 mb-3 waves-effect waves-light"
                                        id="selectImageBtn" tabindex="0" data-role="{{ $roleId }}">
                                        <span class="d-none d-sm-block">Upload new photo</span>
                                        <i class="ti ti-upload d-block d-sm-none"></i>
                                    </button>

                                    <button type="button" class="btn btn-label-secondary mb-3 waves-effect"
                                        id="deletePhotoBtn" data-role="{{ $roleId }}">
                                        <i class="ti ti-refresh-dot d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">Reset</span>
                                    </button>
                                    <div class="text-muted">Allowed JPG, GIF or PNG. Max size of 1MB</div>

                                    <input type="file" id="selectImage" name="selectImage" class="d-none"
                                        accept="image/*" />
                                    <canvas id="cropCanvas" style="display: none;"></canvas>
                                    <input type="hidden" name="cropped_photo" id="cropped_photo" />
                                </div>
                            </div>

                            <hr class="my-3">

                            <form id="formAccountSettings" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="cropped_photo" id="cropped_photo" />
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="fullName" class="form-label">Full Name</label>
                                        <input class="form-control" type="text" id="fullName" name="fullName"
                                            value="{{ $member->master_data_fullname }}" autofocus>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="phoneNumber" class="form-label">Phone Number</label>
                                        <input type="text" id="phoneNumber" name="phoneNumber" class="form-control"
                                            value="{{ $member->master_data_mobile_phone }}" placeholder="Phone Number">
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="3" maxlength="255"
                                            placeholder="Your Address here..."
                                            style="max-height: 120px">{{ $member->master_data_address }}</textarea>
                                    </div>

                                </div>
                                <div class="mt-2">
                                    <button type="button" id="saveProfileBtn"
                                        class="btn btn-primary me-2 waves-effect waves-light">Save changes</button>
                                    <button type="reset" class="btn btn-label-secondary waves-effect">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Passwordd -->
                    <div class="tab-pane fade" id="security" role="tabpanel">
                        <div class="mb-4">
                            <h5 class="mb-3">Change Password</h5>

                            <form id="formChangePassword" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="mb-3 col-md-6 form-password-toggle fv-plugins-icon-container">
                                        <label class="form-label" for="currentPassword">Current Password</label>
                                        <div class="input-group input-group-merge has-validation">
                                            <input class="form-control" type="password" name="currentPassword"
                                                id="currentPassword" placeholder="············">
                                            <span class="input-group-text cursor-pointer">
                                                <i class="ti ti-eye-off"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-6 form-password-toggle fv-plugins-icon-container">
                                        <label class="form-label" for="newPassword">New Password</label>
                                        <div class="input-group input-group-merge has-validation">
                                            <input class="form-control" type="password" id="newPassword" name="newPassword"
                                                placeholder="············">
                                            <span class="input-group-text cursor-pointer">
                                                <i class="ti ti-eye-off"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6 form-password-toggle fv-plugins-icon-container">
                                        <label class="form-label" for="confirmPassword">Confirm New Password</label>
                                        <div class="input-group input-group-merge has-validation">
                                            <input class="form-control" type="password" name="confirmPassword"
                                                id="confirmPassword" placeholder="············">
                                            <span class="input-group-text cursor-pointer">
                                                <i class="ti ti-eye-off"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mb-4">
                                    <h6>Password Requirements:</h6>
                                    <ul class="ps-3 mb-0">
                                        <li class="mb-1" id="req-length">
                                            <i class="ti ti-circle-check text-muted"></i>
                                            Minimum 8 characters
                                        </li>
                                        <li class="mb-1" id="req-case">
                                            <i class="ti ti-circle-check text-muted"></i>
                                            At least one uppercase & one lowercase
                                        </li>
                                        <li class="mb-1" id="req-number-symbol">
                                            <i class="ti ti-circle-check text-muted"></i>
                                            At least one number, symbol, or whitespace
                                        </li>
                                        <li class="mb-1" id="req-match">
                                            <i class="ti ti-circle-check text-muted"></i>
                                            New Password & Confirm must match
                                        </li>
                                        <li class="mb-1" id="req-legal">
                                            <i class="ti ti-circle-check text-muted"></i>
                                            No illegal characters
                                        </li>
                                    </ul>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary me-2 waves-effect waves-light">Save
                                        changes</button>
                                    <button type="reset" class="btn btn-label-secondary waves-effect">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- pw -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {

            // Role yang tidak boleh upload atau reset
            const blockedRoles = [2, 13, 14];
            const userRole = parseInt($('#selectImageBtn').data('role'));

            // Upload button
            $('#selectImageBtn').on('click', function (e) {
                if (blockedRoles.includes(userRole)) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Diizinkan',
                        text: 'Role Civitas tidak diperbolehkan untuk mengubah foto profil.'
                    });
                } else {
                    $('#selectImage').click();
                }
            });

            // Delete button
            $('#deletePhotoBtn').on('click', function (e) {
                if (blockedRoles.includes(userRole)) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Diizinkan',
                        text: 'Role Civitas tidak diperbolehkan untuk menghapus foto profil.'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: 'Data tidak bisa dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD3333',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('account-setting.delete-photo') }}",
                            method: "POST",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: 'Foto profil berhasil dihapus.',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function (err) {
                                console.log(err);
                            }
                        });
                    }
                });
            });
            $("#saveProfileBtn").on("click", function (e) {
                e.preventDefault();

                let formData = {
                    _token: "{{ csrf_token() }}",
                    fullName: $("#fullName").val(),
                    phoneNumber: $("#phoneNumber").val(),
                    address: $("#address").val(),
                    cropped_photo: $("#cropped_photo").val()
                };

                $.ajax({
                    url: "{{ route('account-setting.update') }}",
                    type: "POST",
                    data: formData,
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Profil berhasil diperbarui.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = "";
                        $.each(errors, function (key, value) {
                            errorMsg += value + "\n";
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorMsg
                        });
                    }
                });
            });


            $('#deletePhotoBtn').on('click', function () {
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: 'Data tidak bisa dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD3333',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('account-setting.delete-photo') }}",
                            method: "POST",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: 'Foto profil berhasil dihapus.',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function (err) {
                                console.log(err);
                            }
                        });
                    }
                });
            });
            $("#formChangePassword").on("submit", function (e) {
                e.preventDefault(); // Mencegah form submit normal

                let formData = {
                    _token: "{{ csrf_token() }}",
                    currentPassword: $("#currentPassword").val(),
                    newPassword: $("#newPassword").val(),
                    confirmPassword: $("#confirmPassword").val()
                };

                $.ajax({
                    url: "{{ route('account-setting.updatePassword') }}",
                    type: "POST",
                    data: formData,
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            $("#currentPassword").val('');
                            $("#newPassword").val('');
                            $("#confirmPassword").val('');
                        });
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMsg = "";
                            $.each(errors, function (key, value) {
                                errorMsg += value + "\n";
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: errorMsg
                            });
                        } else {
                            // Error lain
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong.'
                            });
                        }
                    }
                });
            });
        });
        

        document.getElementById("selectImage").addEventListener("change", function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = new Image();
                    img.src = e.target.result;
                    img.onload = function () {
                        const canvas = document.getElementById("cropCanvas");
                        const ctx = canvas.getContext("2d");

                        const size = Math.min(img.width, img.height);
                        canvas.width = size;
                        canvas.height = size;

                        const sx = (img.width - size) / 2;
                        const sy = (img.height - size) / 2;

                        ctx.drawImage(img, sx, sy, size, size, 0, 0, size, size);

                        document.getElementById("cropped_photo").value = canvas.toDataURL("image/jpeg");
                        document.getElementById("uploadedAvatar").src = canvas.toDataURL("image/jpeg");
                    };
                };
                reader.readAsDataURL(file);
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const newPassword = document.getElementById('newPassword');
            const confirmPassword = document.getElementById('confirmPassword');

            // Event listener setiap user mengetik
            newPassword.addEventListener('input', checkPasswordRequirements);
            confirmPassword.addEventListener('input', checkPasswordRequirements);

            function checkPasswordRequirements() {
                const pass = newPassword.value;
                const confirmPass = confirmPassword.value;

                // 1. Minimum 8 characters
                if (pass.length >= 8) {
                    setRequirementMet('req-length');
                } else {
                    setRequirementUnmet('req-length');
                }

                // 2. At least one uppercase & one lowercase
                const hasUpper = /[A-Z]/.test(pass);
                const hasLower = /[a-z]/.test(pass);
                if (hasUpper && hasLower) {
                    setRequirementMet('req-case');
                } else {
                    setRequirementUnmet('req-case');
                }

                // 3. At least one number, symbol, or whitespace
                //   \d => angka
                //   \W => non-alphanumeric (symbol/punctuation)
                //   \s => whitespace
                //   Kita gabung ke satu pattern => /[\d\W\s]/
                const hasNumberOrSymbol = /[\d\W\s]/.test(pass);
                if (hasNumberOrSymbol) {
                    setRequirementMet('req-number-symbol');
                } else {
                    setRequirementUnmet('req-number-symbol');
                }

                // 4. New Password & Confirm must match
                if (pass.length > 0 && confirmPass.length > 0 && pass === confirmPass) {
                    setRequirementMet('req-match');
                } else {
                    setRequirementUnmet('req-match');
                }

                // 5. No illegal characters (opsional)
                //    Definisikan 'illegal' sesuai kebutuhan. Contoh:
                //    Hanya izinkan A-Z, a-z, 0-9, spasi, simbol2 umum
                const illegalCharsRegex = /[^A-Za-z0-9!@#$%^&*()_\-=\[\]{};':"\\|,.<>\/?`~\s]/;
                if (illegalCharsRegex.test(pass)) {
                    setRequirementUnmet('req-legal');
                } else {
                    setRequirementMet('req-legal');
                }
            }

            // Fungsi helper untuk menandai requirement terpenuhi
            function setRequirementMet(reqId) {
                const li = document.getElementById(reqId);
                const icon = li.querySelector('i');

                icon.classList.remove('text-muted');
                icon.classList.remove('text-danger');
                icon.classList.add('text-success');
                // Bisa menambahkan ikon fill lain jika mau

                // Jika ingin ubah icon, misal dari "ti ti-circle-check" ke "ti ti-check-filled"
                // icon.classList.remove('ti-circle-check');
                // icon.classList.add('ti-check-filled');
            }

            // Fungsi helper untuk menandai requirement belum terpenuhi
            function setRequirementUnmet(reqId) {
                const li = document.getElementById(reqId);
                const icon = li.querySelector('i');

                // Kembalikan ke warna abu-abu atau merah (sesuai selera)
                icon.classList.remove('text-success');
                icon.classList.add('text-muted');
                // icon.classList.add('text-danger'); // Jika mau jadi merah
                // icon.classList.remove('ti-check-filled');
                // icon.classList.add('ti-circle-check');
            }
        });
    </script>
@endsection
