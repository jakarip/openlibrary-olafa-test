@extends('layouts/layoutBlank')

@section('title', 'Register')

@section('vendor-style')
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
@endsection

@section('vendor-script')
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/pages-auth.js') }}"></script>

    <script>
        (function () {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');

            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
        })();

        // Script menampilkan/menyembunyikan section form berdasarkan tipe
        document.addEventListener('DOMContentLoaded', function () {
            $('.select2').select2();

           function setFormDisplay(selected) {
        const sections = ['umum','internasional','alumni','ptasuh','lemdikti'];
        sections.forEach(sec => {
            const sectionEl = document.getElementById(sec);
            if (sectionEl) {
                sectionEl.style.display = 'none';
                sectionEl.querySelectorAll('input, select, textarea').forEach(input => {
                    input.required = false;
                    input.disabled = true;
                });
            }
        });
        if (selected && document.getElementById(selected)) {
            const activeSection = document.getElementById(selected);
            activeSection.style.display = 'block';
            activeSection.querySelectorAll('input, select, textarea').forEach(input => {
                input.required = true;
                input.disabled = false;
            });
        }
    }

           const selectedType = "{{ old('type') }}";
    setFormDisplay(selectedType);

    // 4. Event listener "change" pada select#type
    //    Gunakan jQuery .on('change') agar Select2 memicu event.
    $('#type').on('change', function() {
        setFormDisplay($(this).val());
    });

            // Jika flash message sukses, tampilkan modal
            @if(Session::has('login_log') && Session::get('login_log.status') == 'success')
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            @endif
        });
    </script>
@endsection

@section('content')
    <div class="authentication-wrapper authentication-cover authentication-bg">
        <div class="authentication-inner row">
            <div class="d-none d-lg-flex col-lg-7 p-0">
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center"
                    style="background-color:#F3F3F3">
                    <img src="{{ asset('assets/img/openlibrary/login1.png') }}" class="img-fluid my-5 auth-illustration"
                        style="width:1400px; max-height:100%; max-width:100%">
                    <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}" class="platform-bg">
                </div>
            </div>

            <!-- Registration Form Section -->
            <div class="col-12 col-lg-5 d-flex flex-column  align-items-center p-sm-5 p-4" style="height: 100vh; overflow-y: auto;">
                <div class="w-100 d-flex flex-column" style="max-width: 400px; flex-grow: 1; justify-content: center;">
                    <div class="app-brand mb-4">
                        <a href="{{ url('/') }}" class="app-brand-link gap-2">
                            <img src="{{ asset('assets/img/openlibrary/logo-hires.png') }}" height="60px" class="me-2">
                            <img src="{{ asset('assets/img/openlibrary/logo-telu.png') }}" height="45px" class="me-2">
                        </a>
                    </div>
                    <div class="mb-4" style="margin-top:30px">
                        <h3 class="mb-0 fw-bold"><span class="text-danger">OPEN LIBRARY</span></h3>
                        <h5 class="fw-bold mb-3" style="line-height: 1">Telkom University</h5>
                        <small class="text-secondary">{{ __('common.enter_identity') }}</small>
                    </div>

                    @if(Session::has('login_log') && Session::get('login_log.status') != 'success')
                        <div class="alert alert-{{ Session::get('login_log.status') }} d-flex align-items-center" role="alert">
                            <span class="alert-icon text-{{ Session::get('login_log.status') }} me-2">
                                <i class="ti ti-file-alert ti-xs"></i>
                            </span>
                            {!! Session::get('login_log.text') !!}
                        </div>
                    @endif

                    <!-- Form dengan class needs-validation + novalidate -->
                    <form action="{{ url('register/exe') }}" method="POST" enctype="multipart/form-data"
                        class="needs-validation" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="name">{{ __('common.full_name') }}</label>
                            <input type="text" id="name" name="inp[name]" class="form-control" placeholder="{{ __('common.full_name') }}"
                                required minlength="4" maxlength="100" value="{{ old('inp.name') }}">
                            <div class="invalid-feedback">{{ __('common.full_name_desc') }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="password">Password</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Password"
                                required minlength="8" maxlength="100">
                            <div class="invalid-feedback">{{ __('common.pass_desc') }}.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="phone">{{ __('config.member.phone_number') }}</label>
                            <input type="text" id="phone" name="inp[phone]" class="form-control"
                                placeholder="{{ __('config.member.phone_number_required') }}" pattern="^08\d{7,12}$" required
                                title="{{ __('common.phone_number_title') }}"
                                value="{{ old('inp.phone') }}">
                            <div class="invalid-feedback">
                            {{ __('common.phone_number_desc') }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="type">{{ __('config.member_type.page.title') }}</label>
                            <select name="type" id="type" class="form-control select2" required>
                                <option value="">{{ __('common.choose_member_type') }}</option>
                                <option value="umum" {{ old('type') == 'umum' ? 'selected' : '' }}>{{ __('common.general') }}</option>
                                <option value="internasional" {{ old('type') == 'internasional' ? 'selected' : '' }}>
                                {{ __('common.internationa;') }}</option>
                                <option value="alumni" {{ old('type') == 'alumni' ? 'selected' : '' }}>Alumni Telkom
                                    University</option>
                                <option value="ptasuh" {{ old('type') == 'ptasuh' ? 'selected' : '' }}>PT Asuh / Lemdikti YPT
                                    &amp; PT</option>
                                <option value="lemdikti" {{ old('type') == 'lemdikti' ? 'selected' : '' }}>Lemdikti</option>
                            </select>
                            <div class="invalid-feedback">{{ __('common.choose_member_desc') }}</div>
                        </div>

                        <div id="umum" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label" for="institution_umum">{{ __('common.institution_name') }}</label>
                                <input type="text" name="inp[institution_umum]" id="institution_umum"
                                    class="form-control umum" placeholder="{{ __('common.institution_name') }}"
                                    value="{{ old('inp.institution_umum') }}">
                                <div class="invalid-feedback">{{ __('common.intitutuion_name_desc') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="email_umum">Email</label>
                                <input type="email" name="inp[email_umum]" id="email_umum" class="form-control umum"
                                    placeholder="{{ __('common.email_institution') }}" required maxlength="150"
                                    value="{{ old('inp.email_umum') }}">
                                <div class="invalid-feedback">{{ __('common.email_desc') }}</div>
                                <span class="help-block">{{ __('common.domain_email_desc') }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">KTP</label>
                                <input type="file" name="ktp_umum" class="form-control umum file_uploads" required>
                                <div class="invalid-feedback">{{ __('common.idCard_desc') }} (jpg, png, pdf).</div>
                                <span class="help-block">Format: <strong>jpg, jpeg, png, pdf</strong>; {{ __('common.max_size') }}
                                    <strong>2 MB</strong></span>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">{{ __('common.staff_id') }}</label>
                                <input type="file" name="idcard_umum" class="form-control umum file_uploads">
                                <span class="help-block">Format: <strong>jpg, jpeg, png, pdf</strong>; {{ __('common.max_size') }}
                                    <strong>2 MB</strong></span>
                            </div>
                        </div>

                        <!-- Section untuk anggota Internasional -->
                        <div id="internasional" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label" for="institution_internasional">{{ __('common.institution_name') }}</label>
                                <input type="text" name="inp[institution_internasional]" id="institution_internasional"
                                    class="form-control internasional" placeholder="Your Institution"
                                    value="{{ old('inp.institution_internasional') }}">
                                <div class="invalid-feedback">{{ __('common.intitution_name_desc') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="email_internasional">Email</label>
                                <input type="email" name="inp[email_internasional]" id="email_internasional"
                                    class="form-control internasional" placeholder="Email" required maxlength="150"
                                    value="{{ old('inp.email_internasional') }}">
                                <div class="invalid-feedback">{{ __('common.email_valid_desc') }}</div>
                            </div>
                        </div>

                        <!-- Section untuk anggota Alumni -->
                        <div id="alumni" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label" for="email_alumni">Email</label>
                                <input type="email" name="inp[email_alumni]" id="email_alumni" class="form-control alumni"
                                    placeholder="Email" required maxlength="150" value="{{ old('inp.email_alumni') }}">
                                <div class="invalid-feedback">{{ __('common.email_valid_gmail') }}</div>
                                <span class="help-block">{{ __('common.domain_email_gmail') }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">{{ __('config.member.id_card') }}</label>
                                <input type="file" name="ktp_alumni" class="form-control alumni file_uploads" required>
                                <div class="invalid-feedback">{{ __('common.idCard_desc') }} (jpg, png, pdf).</div>
                                <span class="help-block">Format: <strong>jpg, jpeg, png, pdf</strong>; {{ __('common.max_size') }}
                                    <strong>2 MB</strong></span>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">{{ __('config.member.degree_data') }}</label>
                                <input type="file" name="ijasah" class="form-control alumni file_uploads" required>
                                <div class="invalid-feedback">{{ __('common.email_desc') }} (jpg, png, pdf).</div>
                                <span class="help-block">Format: <strong>jpg, jpeg, png, pdf</strong>; {{ __('common.max_size') }}
                                    <strong>2 MB</strong></span>
                            </div>
                        </div>

                        <!-- Section untuk anggota PT Asuh / Lemdikti -->
                        <div id="ptasuh" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">{{ __('common.choose_intitution') }}</label>
                                <select name="inp[institution_ptasuh]" id="institution_ptasuh" class="form-control select2"
                                    required>
                                    <option value="">{{ __('common.choose_intitution') }}</option>
                                    @if(isset($ptasuh))
                                        @foreach($ptasuh as $key => $value)
                                            <option value="{{ $key }}" {{ old('inp.institution_ptasuh') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="invalid-feedback">{{ __('common.pt_asuh_desc') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="email_ptasuh">Email</label>
                                <input type="email" name="inp[email_ptasuh]" id="email_ptasuh" class="form-control ptasuh"
                                    placeholder="Email" required maxlength="150" value="{{ old('inp.email_ptasuh') }}">
                                <div class="invalid-feedback">{{ __('common.email_desc') }}</div>
                                <span class="help-block">{{ __('common.domain_email_desc') }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">{{ __('config.member.id_card') }}</label>
                                <input type="file" name="ktp_ptasuh" id="ktp_ptasuh"
                                    class="form-control ptasuh file_uploads" required>
                                <div class="invalid-feedback">{{ __('common.idCard_desc') }} (jpg, png, pdf).</div>
                                <span class="help-block">Format: <strong>jpg, jpeg, png, pdf</strong>; {{ __('common.max_size') }}
                                    <strong>2 MB</strong></span>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">{{ __('common.staff_id') }}</label>
                                <input type="file" name="idcard_ptasuh" id="idcard_ptasuh"
                                    class="form-control ptasuh file_uploads" required>
                                <div class="invalid-feedback">{{ __('common.staffID_desc') }} (jpg, png, pdf).</div>
                                <span class="help-block">Format: <strong>jpg, jpeg, png, pdf</strong>; {{ __('common.max_size') }}
                                    <strong>2 MB</strong></span>
                            </div>
                        </div>

                        <div id="lemdikti" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label">{{ __('common.choose_intitution') }}</label>
                                <select name="inp[institution_lemdikti]" id="institution_lemdikti"
                                    class="form-control select2" required>
                                    <option value="">{{ __('common.choose_intitution') }}</option>
                                    @if(isset($lemdikti))
                                        @foreach($lemdikti as $key => $value)
                                            <option value="{{ $key }}" {{ old('inp.institution_lemdikti') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="invalid-feedback">{{ __('common.choose_lemdikti') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="email_lemdikti">Email</label>
                                <input type="email" name="inp[email_lemdikti]" id="email_lemdikti"
                                    class="form-control lemdikti" placeholder="Email" required maxlength="150"
                                    value="{{ old('inp.email_lemdikti') }}">
                                <div class="invalid-feedback">{{ __('common.email_desc') }}</div>
                                <span class="help-block">{{ __('common.domain_email_desc') }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">{{ __('config.member.id_card') }}</label>
                                <input type="file" name="ktp_lemdikti" id="ktp_lemdikti"
                                    class="form-control lemdikti file_uploads" required>
                                <div class="invalid-feedback">{{ __('common.idCard_desc') }} (jpg, png, pdf).</div>
                                <span class="help-block">Format: <strong>jpg, jpeg, png, pdf</strong>; {{ __('common.max_size') }}
                                    <strong>2 MB</strong></span>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">{{ __('common.staff_id') }}</label>
                                <input type="file" name="idcard_lemdikti" id="idcard_lemdikti"
                                    class="form-control lemdikti file_uploads" required>
                                <div class="invalid-feedback">{{ __('common.staffID_desc') }} (jpg, png, pdf).</div>
                                <span class="help-block">Format: <strong>jpg, jpeg, png, pdf</strong>; {{ __('common.max_size') }}
                                    <strong>2 MB</strong></span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-danger w-100">Register <i
                                class="icon-circle-right2 position-right"></i></button>

                        <div class="mt-3 text-center">
                            <span class="help-block">
                            {{ __('common.have_acc') }} <strong><a href="{{ url('login') }}">{{ __('common.please_login') }}</a></strong>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Registration Form Section -->
        </div>
    </div>

   <!-- Modal Sukses  -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content text-center">
      <div class="modal-header border-0">
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body pb-4 mb-2">
        <img 
          src="{{ asset('assets/img/illustrations/boy-with-rocket-light.png') }}" 
          alt="boy-with-rocket" 
          width="200" 
          class="img-fluid mb-3"
        >
        <h5 class="modal-title mb-2" id="successModalLabel">{{ __('common.success_regis') }}</h5>
        <p class="mb-0">
          {!! Session::get('login_log.text') !!}
        </p>
      </div>

      <div class="modal-footer justify-content-center border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="ti ti-check"></i> {{ __('common.close') }}
        </button>
      </div>
    </div>
  </div>
</div>

@endsection