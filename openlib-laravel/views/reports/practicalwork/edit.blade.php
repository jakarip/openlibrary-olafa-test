{{-- filepath: resources/views/reports/practicalwork/edit.blade.php --}}
@extends('layouts.layoutMaster')

@section('title', 'Laporan Magang & KP')

@section('vendor-style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<!-- Add font-awesome CDN for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
@endsection

@section('content')
@php
    $member_class_id = $member_class_id ?? auth()->user()->member_class_id;
    // Mahasiswa hanya bisa edit jika status belum READY FOR REVIEW
    $readonly = ($member_class_id == 2 && $report->ereport_status == 'READY FOR REVIEW') ? 'readonly' : '';
    $disabled = ($member_class_id == 2 && $report->ereport_status == 'READY FOR REVIEW') ? 'disabled' : '';
    // Dosen/admin hanya bisa edit status & revisi, field lain readonly
    $readonly_dosen = (in_array($member_class_id, [1,9])) ? 'readonly' : '';
    $disabled_dosen = (in_array($member_class_id, [1,9])) ? 'disabled' : '';
@endphp
<div class="container mt-4">
    <div class="card shadow-sm">
        <form id="formEdit" method="POST" action="{{ route('practicalwork.update', $report->ereport_id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="ereport_id" value="{{ $report->ereport_id }}">

            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card-header bg-white border-bottom py-3">
                <h6 class="kt-section__title fs-6 mb-0">Detail Laporan Magang & KP</h6>
            </div>

            <div class="card-body pt-4">
                <h6 class="fs-5 fw-bold mb-3 mt-2">1. Data Laporan</h6>
                <!-- Jenis Laporan -->
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Jenis Laporan <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        @if($readonly || $readonly_dosen)
                            <input type="text" class="form-control-plaintext" value="{{ $report->ereport_type }}">
                        @else
                            <select class="form-select select2" name="ereport_type" required>
                                <option value="KP" {{ $report->ereport_type == 'KP' ? 'selected' : '' }}>KP</option>
                                <option value="Magang" {{ $report->ereport_type == 'Magang' ? 'selected' : '' }}>Magang</option>
                            </select>
                        @endif
                    </div>
                </div>
                <!-- Status -->
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Status <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        <select class="form-select select2" name="ereport_status" required style="min-height: 100px;" {{ $disabled }}>
                            @foreach($status as $option)
                                <option value="{{ $option }}" {{ $report->ereport_status == $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- Judul Laporan -->
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Judul Laporan <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        @if($readonly || $readonly_dosen)
                            <input type="text" class="form-control-plaintext" value="{{ $report->ereport_title }}">
                        @else
                            <input type="text" class="form-control" name="ereport_title" value="{{ $report->ereport_title ?? '' }}" required>
                        @endif
                    </div>
                </div>
                <!-- File Laporan -->
<div class="row mb-3 align-items-center">
    <label class="col-md-3 col-form-label text-start">File Laporan</label>
    <div class="col-md-9">
        @if($report->ereport_file)
            <a href="{{ asset('storage/'.$report->ereport_file) }}" target="_blank" title="View File Laporan PDF" style="display:inline-block;">
                <i class="fa-solid fa-file-pdf" style="font-size: 24px; color: red;"></i>
            </a>
        @endif
        @if($member_class_id == 2 && $report->ereport_status != 'READY FOR REVIEW')
            <input type="file" class="form-control mt-2" name="ereport_file" accept=".pdf">
            <div><span class="fw-bold small">Format yang diterima : pdf. Maksimal size : 10 MB</span></div>
        @endif
    </div>
</div>

<!-- File Similarity -->
<div class="row mb-3 align-items-center">
    <label class="col-md-3 col-form-label text-start">File Similarity Laporan</label>
    <div class="col-md-9">
        @if($report->ereport_file_similarity)
            <a href="{{ asset('storage/'.$report->ereport_file_similarity) }}" target="_blank" title="View File Similarity PDF" style="display:inline-block;">
                <i class="fa-solid fa-file-pdf" style="font-size: 24px; color: red;"></i>
            </a>
        @endif
        @if($member_class_id == 2 && $report->ereport_status != 'READY FOR REVIEW')
            <input type="file" class="form-control mt-2" name="ereport_file_similarity" accept=".pdf">
            <div><span class="fw-bold small">Format yang diterima : pdf. Maksimal size : 10 MB</span></div>
        @endif
    </div>
</div>

<!-- Surat Balasan Persetujuan -->
<div class="row mb-3 align-items-center">
    <label class="col-md-3 col-form-label text-start">Surat Balasan Persetujuan</label>
    <div class="col-md-9">
        @if($report->ereport_file_approval)
            <a href="{{ asset('storage/'.$report->ereport_file_approval) }}" target="_blank" title="View Surat Balasan Persetujuan PDF" style="display:inline-block;">
                <i class="fa-solid fa-file-pdf" style="font-size: 24px; color: red;"></i>
            </a>
        @endif
        @if($member_class_id == 2 && $report->ereport_status != 'READY FOR REVIEW')
            <input type="file" class="form-control mt-2" name="ereport_file_approval" accept=".pdf">
            <div><span class="fw-bold small">Format yang diterima : pdf. Maksimal size : 2 MB</span></div>
        @endif
    </div>
</div>

<!-- Surat Keterangan Selesai -->
<div class="row mb-3 align-items-center">
    <label class="col-md-3 col-form-label text-start">Surat Keterangan Selesai</label>
    <div class="col-md-9">
        @if($report->ereport_file_finish)
            <a href="{{ asset('storage/'.$report->ereport_file_finish) }}" target="_blank" title="View Surat Keterangan Selesai PDF" style="display:inline-block;">
                <i class="fa-solid fa-file-pdf" style="font-size: 24px; color: red;"></i>
            </a>
        @endif
        @if($member_class_id == 2 && $report->ereport_status != 'READY FOR REVIEW')
            <input type="file" class="form-control mt-2" name="ereport_file_finish" accept=".pdf">
            <div><span class="fw-bold small">Format yang diterima : pdf. Maksimal size : 2 MB</span></div>
        @endif
    </div>
</div>

<!-- Surat Keterangan Implementation Arrangement -->
<div class="row mb-3 align-items-center">
    <label class="col-md-3 col-form-label text-start">Surat Keterangan Implementation Arrangement</label>
    <div class="col-md-9">
        @if($report->ereport_file_implementation)
            <a href="{{ asset('storage/'.$report->ereport_file_implementation) }}" target="_blank" title="View Surat Keterangan Implementation Arrangement PDF" style="display:inline-block;">
                <i class="fa-solid fa-file-pdf" style="font-size: 24px; color: red;"></i>
            </a>
        @endif
        @if($member_class_id == 2 && $report->ereport_status != 'READY FOR REVIEW')
            <input type="file" class="form-control mt-2" name="ereport_file_implementation" accept=".pdf">
            <div><span class="fw-bold small">Format yang diterima : pdf. Maksimal size : 2 MB</span></div>
        @endif
    </div>
</div>
                <!-- Dosen Pembimbing -->
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Dosen Pembimbing <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        @if($readonly || $readonly_dosen)
                            <input type="text" class="form-control-plaintext" value="{{ $report->dosen_pembimbing }}">
                        @else
                            <select class="form-select select2" name="ereport_id_lecturer" required>
                                @if($report->ereport_id_lecturer && $report->dosen_pembimbing)
                                    <option value="{{ $report->ereport_id_lecturer }}" selected>
                                        {{ $report->dosen_pembimbing }}
                                    </option>
                                @endif
                            </select>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-body pt-4">
                <h6 class="fs-5 fw-bold mb-3">2. Data Perusahaan</h6>
                <!-- Nama Perusahaan -->
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Nama Perusahaan <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        @if($readonly || $readonly_dosen)
                            <input type="text" class="form-control-plaintext" value="{{ $report->ereport_mentor_company_name }}">
                        @else
                            <input type="text" class="form-control" name="ereport_mentor_company_name" value="{{ $report->ereport_mentor_company_name ?? '' }}" required>
                        @endif
                    </div>
                </div>
                <!-- Bidang Industri -->
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Bidang Industri <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        @if($readonly || $readonly_dosen)
                            <input type="text" class="form-control-plaintext" value="{{ $report->ereport_industry_type }}">
                        @else
                            <input type="text" class="form-control" name="ereport_industry_type" value="{{ $report->ereport_industry_type ?? '' }}" required>
                        @endif
                    </div>
                </div>
                <!-- Alamat Perusahaan -->
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Alamat Perusahaan <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        @if($readonly || $readonly_dosen)
                            <input type="text" class="form-control-plaintext" value="{{ $report->ereport_mentor_company_address }}">
                        @else
                            <input type="text" class="form-control" name="ereport_mentor_company_address" value="{{ $report->ereport_mentor_company_address ?? '' }}" required>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-body pt-4">
                <h6 class="fs-5 fw-bold mb-3">3. Data Pembimbing Lapangan</h6>
                <!-- Nama Depan -->
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Nama Depan <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        @if($readonly || $readonly_dosen)
                            <input type="text" class="form-control-plaintext" value="{{ $report->ereport_mentor_front_name }}">
                        @else
                            <input type="text" class="form-control" name="ereport_mentor_front_name" value="{{ $report->ereport_mentor_front_name ?? '' }}" required>
                        @endif
                    </div>
                </div>
                <!-- Nama Belakang -->
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Nama Belakang <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        @if($readonly || $readonly_dosen)
                            <input type="text" class="form-control-plaintext" value="{{ $report->ereport_mentor_last_name }}">
                        @else
                            <input type="text" class="form-control" name="ereport_mentor_last_name" value="{{ $report->ereport_mentor_last_name ?? '' }}" required>
                        @endif
                    </div>
                </div>
                <!-- Jabatan/Posisi -->
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Jabatan/ Posisi <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        @if($readonly || $readonly_dosen)
                            <input type="text" class="form-control-plaintext" value="{{ $report->ereport_mentor_position }}">
                        @else
                            <input type="text" class="form-control" name="ereport_mentor_position" value="{{ $report->ereport_mentor_position ?? '' }}" required>
                        @endif
                    </div>
                </div>
                <!-- Pendidikan Terakhir -->
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Pendidikan Terakhir <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        @if($readonly || $readonly_dosen)
                            <input type="text" class="form-control-plaintext" value="{{ $report->ereport_mentor_academic }}">
                        @else
                            <input type="text" class="form-control" name="ereport_mentor_academic" value="{{ $report->ereport_mentor_academic ?? '' }}" required>
                        @endif
                    </div>
                </div>
                <!-- No. Telp/HP -->
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">No.Telp/ HP <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        @if($readonly || $readonly_dosen)
                            <input type="text" class="form-control-plaintext" value="{{ $report->ereport_mentor_phone }}">
                        @else
                            <input type="text" class="form-control" name="ereport_mentor_phone" value="{{ $report->ereport_mentor_phone ?? '' }}" required>
                        @endif
                    </div>
                </div>
                <!-- Email -->
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Email <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                        @if($readonly || $readonly_dosen)
                            <input type="email" class="form-control-plaintext" value="{{ $report->ereport_mentor_email }}">
                        @else
                            <input type="email" class="form-control" name="ereport_mentor_email" value="{{ $report->ereport_mentor_email ?? '' }}" required>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 4. Revisi --}}
            @if(
                in_array($member_class_id, [1,9]) || 
                ($member_class_id == 2 && in_array($report->ereport_status, ['NEED FOR REVISION', 'READY FOR REVIEW']))
            )
            <div class="card-body pt-4">
                <h6 class="fs-5 fw-bold mb-3">4. Revisi</h6>
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-start">Catatan Revisi</label>
    <div class="col-md-9">
        @if(in_array($member_class_id, [1,9]))
            <textarea class="form-control" name="ereport_revision" rows="4" style="min-height: 100px;">{{ $report->ereport_revision ?? '' }}</textarea>
        @elseif($member_class_id == 2 && $report->ereport_status == 'NEED FOR REVISION')
            <div class="form-control-plaintext" style="min-height: 100px; white-space: pre-line;">{{ $report->ereport_revision ?? '-' }}</div>
        @else
            <div class="form-control-plaintext" style="min-height: 100px; white-space: pre-line;">{{ $report->ereport_revision ?? '-' }}</div>
        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- 5. History Status --}}
            @if(
                in_array($member_class_id, [1,9]) || 
                ($member_class_id == 2 && in_array($report->ereport_status, ['NEED FOR REVISION', 'READY FOR REVIEW']))
            )
            <div class="card-body pt-4">
    <h6 class="fs-5 fw-bold mb-3">5. History Status</h6>
    <div class="row mb-3">
        <div class="col-md-9 offset-md-3">
            <div class="table-responsive">
                <table class="table table-bordered w-100 mb-0">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Anggota</th>
                            <th>Fullname</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ereport_status as $ereport)
                            <tr>
                                <td>{{ $ereport->ereport_status }}</td>
                                <td>{{ $ereport->master_data_number ?? 'N/A' }}</td>
                                <td>{{ $ereport->master_data_fullname }}</td>
                                <td>{{ $ereport->created_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

            <div class="card-footer bg-white border-top py-3">
                <div class="text-end">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">Kembali</button>
                    @if(!($member_class_id == 2 && $report->ereport_status == 'READY FOR REVIEW'))
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Pilih Opsi",
        allowClear: true,
        width: '100%'
    });

    // Jika dosen/admin mengisi catatan revisi, status otomatis jadi NEED FOR REVISION
    @if($member_class_id == 9 || $member_class_id == 1)
    $('textarea[name="ereport_revision"]').on('input', function() {
        if ($(this).val().trim() !== '') {
            $('select[name="ereport_status"]').val('NEED FOR REVISION').trigger('change');
        }
    });
    @endif

    // Select2 AJAX untuk dosen pembimbing
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
});
</script>
@endsection