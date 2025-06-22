{{-- filepath: c:\xamppbaru\htdocs\openlibrary\openlibrary-sub\resources\views\reports\practicalwork\add.blade.php --}}
@extends('layouts.layoutMaster')

@section('title', 'Tambah Laporan Magang & KP')

@section('vendor-style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
@endsection

@section('content')

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="container mt-4">
    <div class="card shadow-sm">
        <form id="formAdd" method="POST" action="{{ route('practicalwork.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Card Header -->
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="kt-section__title fs-6 mb-0">Tambah Laporan Magang & KP</h6>
            </div>

            <div class="card-body">
                <!-- 1. Data Laporan -->
                <h6 class="col-lg-7 col-form-label fs-5 fw-bold">1. Data Laporan</h6>
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Jenis Laporan <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <select class="form-select select2 required-field" name="jenis_laporan" required>
                            <option value="">Pilih Jenis Laporan</option>
                            <option value="KP">KP</option>
                            <option value="Magang">Magang</option>
                        </select>
                        <div class="invalid-feedback">Jenis laporan wajib diisi</div>
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Judul Laporan <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <input type="text" class="form-control required-field" name="judul_laporan" required>
                        <div class="invalid-feedback">Judul laporan wajib diisi</div>
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">File Laporan</label>
                    <div class="col-md-9">
                        <input type="file" class="form-control" name="ereport_file" accept=".pdf">
                         <div><span class="fw-bold small">Format yang diterima : pdf. Maksimal size : 10 MB</span></div>
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">File Similarity Laporan</label>
                    <div class="col-md-9">
                        <input type="file" class="form-control" name="ereport_file_similarity" accept=".pdf">
                         <div><span class="fw-bold small">Format yang diterima : pdf. Maksimal size : 10 MB</span></div>
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Surat Balasan Persetujuan</label>
                    <div class="col-md-9">
                        <input type="file" class="form-control" name="ereport_file_approval" accept=".pdf">
                         <div><span class="fw-bold small">Format yang diterima : pdf. Maksimal size : 2 MB</span></div>
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Surat Keterangan Selesai</label>
                    <div class="col-md-9">
                        <input type="file" class="form-control" name="ereport_file_finish" accept=".pdf">
                         <div><span class="fw-bold small">Format yang diterima : pdf. Maksimal size : 2 MB</span></div>
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Surat Keterangan Implementation Arrangement</label>
                    <div class="col-md-9">
                        <input type="file" class="form-control" name="ereport_file_implementation" accept=".pdf">
                         <div><span class="fw-bold small">Format yang diterima : pdf. Maksimal size : 2 MB</span></div>
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Dosen Pembimbing <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <select class="form-select select2 required-field" name="ereport_id_lecturer" required>
                           <option value="">Pilih Dosen Pembimbing</option>
                           @foreach($dosen_pembimbing as $dosen)
                              <option value="{{ $dosen->id }}" {{ (isset($report) && $report->ereport_id_lecturer == $dosen->id) ? 'selected' : '' }}>
                                 {{ $dosen->dosen_pembimbing }}
                              </option>
                           @endforeach
                       </select>
                       <div class="invalid-feedback">Dosen pembimbing wajib dipilih</div>
                   </div>
               </div>
               
                <!-- 2. Data Perusahaan -->
                <h6 class="col-lg-7 col-form-label fs-5 fw-bold mt-4">2. Data Perusahaan</h6>
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Nama Perusahaan <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <input type="text" class="form-control required-field" name="ereport_mentor_company_name" required>
                        <div class="invalid-feedback">Nama perusahaan wajib diisi</div>
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Bidang Industri <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <input type="text" class="form-control required-field" name="ereport_industry_type" required>
                        <div class="invalid-feedback">Bidang industri wajib diisi</div>
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Alamat Perusahaan <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <input type="text" class="form-control required-field" name="ereport_mentor_company_address" required>
                        <div class="invalid-feedback">Alamat perusahaan wajib diisi</div>
                    </div>
                </div>

                <!-- 3. Data Pembimbing Lapangan -->
                <h6 class="col-lg-7 col-form-label fs-5 fw-bold mt-4">3. Data Pembimbing Lapangan</h6>
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Nama Depan <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <input type="text" class="form-control required-field" name="ereport_mentor_front_name" required>
                        <div class="invalid-feedback">Nama depan wajib diisi</div>
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Nama Belakang <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <input type="text" class="form-control required-field" name="ereport_mentor_last_name" required>
                        <div class="invalid-feedback">Nama belakang wajib diisi</div>
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Jabatan/ Posisi <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <input type="text" class="form-control required-field" name="ereport_mentor_position" required>
                        <div class="invalid-feedback">Jabatan wajib diisi</div>
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Pendidikan Terakhir <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <input type="text" class="form-control required-field" name="ereport_mentor_academic" required>
                        <div class="invalid-feedback">Pendidikan terakhir wajib diisi</div>
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">No.Telp/ HP <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <input type="text" class="form-control required-field" name="ereport_mentor_phone" required>
                        <div class="invalid-feedback">Nomor telepon wajib diisi</div>
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Email <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <input type="email" class="form-control required-field" name="ereport_mentor_email" required>
                        <div class="invalid-feedback">Email wajib diisi</div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card-footer bg-white border-top py-3">
                <div class="text-end">
                    <button type="button" class="btn btn-secondary me-3" onclick="window.history.back()">
                        <i class="fas fa-arrow-left me-2"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('vendor-script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
@endsection

@section('page-script')
<script>
$(document).ready(function() {
    // AJAX Select2 untuk dosen pembimbing
    $('select[name="ereport_id_lecturer"]').select2({
        placeholder: "Pilih Dosen Pembimbing",
        allowClear: true,
        width: '100%',
        ajax: {
            url: '{{ route("practicalwork.searchMembers") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { search: params.term };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                           id: item.id,
                            text: item.master_data_fullname + ' (' + item.master_data_number + ')'
                        };
                    })
                };
            },
            cache: true
        }
    });

    // Spinner saat submit
    $('#formAdd').on('submit', function() {
        $('button[type="submit"]').prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin me-2"></i>Loading');
    });
});
</script>
@endsection