@extends('layouts.layoutMaster')

@section('title', 'Laporan Magang & KP')

@section('vendor-style')
@endsection

@section('page-style')
<style>
   .select2-container {
        z- index: 99999;
    }

    .card {
        z-index: 0;
        border-radius: 12px;
        padding: 20px;
    }

    #searchForm .form-select,
    #searchForm button {
        font-size: 1rem;
        padding: 10px;
        border-radius: 8px;
    }

    .table-responsive {
        max-height: 600px;
        overflow-y: auto;
        overflow-x: auto;
        display: block;
    }

    .custom-table {
        width: 100%;
        table-layout: auto;
    }

    .custom-table th,
    .custom-table td {
        padding: 14px;
        vertical-align: middle;
        white-space: normal;
        word-wrap: break-word;
    }

    .custom-table thead {
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
    }

    .export-button {
        float: right;
    }

    .badge-kp {
    background: #0d6efd;
    color: #fff;
    font-weight: bold;
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 0.95em;
    display: inline-block;
}
.badge-magang {
    background: #fd7e14;
    color: #fff;
    font-weight: bold;
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 0.95em;
    display: inline-block;
}
.badge-status {
    font-weight: bold;
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 0.95em;
    display: inline-block;
}
.badge-ondraft { background: #6c757d; color: #fff; }
.badge-needrev { background: #ffc107; color: #212529; }
.badge-ready { background: #0dcaf0; color: #212529; }
.badge-approved { background: #198754; color: #fff; }
.badge-success { background: #28a745; color: #fff; }
</style>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card w-100">
    <h5 class="card-header">Advanced Search</h5>
    <div class="card-body">
        <form id="searchForm">
            @csrf
            <div class="row g-3 align-items-center">
                @if(auth()->can('report-practical-work.filter'))
                <div class="col-md-3">
                    <select id="jenis_laporan" class="form-select">
                        <option value="">Semua Jenis Laporan</option>
                        <option value="KP">KP</option>
                        <option value="MAGANG">MAGANG</option>
                    </select>
                </div>
                @endif
                 @if(auth()->can('report-practical-work.filter_search'))
                <div class="col-md-3">
                    <select id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="ON DRAFT">ON DRAFT</option>
                        <option value="NEED FOR REVISION">NEED FOR REVISION</option>
                        <option value="READY FOR REVIEW">READY FOR REVIEW</option>
                        <option value="APPROVED">APPROVED</option>
                        <option value="SUCCESS">SUCCESS</option>
                    </select>
                </div>
                @endif
                @if(auth()->can('report-practical-work.filter_search'))
                <div class="col-md-3">
                    <select id="prodi" class="form-select">
                        <option value="">Semua Program Studi</option>
                        @isset($prodi)
                            @foreach($prodi as $row)
                                <option value="{{ $row->NAMA_PRODI }}">{{ $row->NAMA_PRODI }}</option>
                            @endforeach
                        @else
                            <option value="" disabled>Data tidak tersedia</option>
                        @endisset
                    </select>
                </div>
                @endif
                 @if(auth()->can('report-practical-work.filter_search'))
                <div class="col-md-2 d-flex align-items-center">
                    <input type="checkbox" name="onlyforme" id="onlyforme" value="1" class="me-2">
                    <label for="onlyforme" class="mb-0 fw-bold">Dokumen hanya untuk saya</label>
                </div>
                @endif
                <div class="col-md-1">
                    <button type="button" class="btn btn-primary w-100" id="filter">Filter</button>
                </div>
            </div>
        </form>
    </div>
    <hr>
    <div class="card-datatable table-responsive">
        <table class="table table-striped custom-table" id="dataTable">
            <thead>
                <tr>
                    <th>Aksi</th>
                    <th>Jenis Laporan</th>
                    <th>NIM</th>
                    <th>Mahasiswa</th>
                    <th>Program Studi</th>
                    <th>Judul Laporan</th>
                    <th>Dosen Pembimbing</th>
                    <th>Perusahaan</th>
                    <th>Bidang Industri</th>
                    <th>Alamat</th>
                    <th>Pembimbing Lapangan</th>
                    <th>Pendidikan</th>
                    <th>Kontak</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <!-- DataTables AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" ereport_id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEdit">
                    @csrf
                    <input type="hidden" name="ereport_id">
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" class="form-control" name="judul">
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('page-script')
<script>
$(document).ready(function() {
    let dataTable = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        scrollX: true,
        responsive: true,
        columnDefs: [
            { width: "80px", targets: -1 }
        ],
        ajax: {
            url: '{{ url('report/practical-work/dt') }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.jenis_laporan = $('#jenis_laporan').val();
                d.status = $('#status').val();
                d.prodi = $('#prodi').val();
                d.onlyforme = $('#onlyforme').is(':checked') ? 1 : 0;
            }
        },
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false },
            {
        data: 'ereport_type', name: 'ereport_type',
        render: function(data) {
            if (data === 'KP') return '<span class="badge-kp">KP</span>';
            if (data === 'MAGANG' || data === 'Magang') return '<span class="badge-magang">Magang</span>';
            return data;
        }
    },
            { data: 'master_data_number', name: 'master_data_number' },
            { data: 'master_data_fullname', name: 'master_data_fullname' },
            { data: 'nama_prodi', name: 'nama_prodi' },
            { data: 'ereport_title', name: 'ereport_title' },
            { data: 'dosen_pembimbing', name: 'dosen_pembimbing' },
            { data: 'ereport_mentor_company_name', name: 'ereport_mentor_company_name' },
            { data: 'ereport_industry_type', name: 'ereport_industry_type' },
            { data: 'ereport_mentor_company_address', name: 'ereport_mentor_company_address' },
            { data: 'mentor_name', name: 'mentor_name' },
            { data: 'ereport_mentor_academic', name: 'ereport_mentor_academic' },
            { data: 'mentor_contact', name: 'mentor_contact' },
            {
        data: 'ereport_status', name: 'ereport_status',
        render: function(data) {
            if (data === 'ON DRAFT') return '<span class="badge-status badge-ondraft">ON DRAFT</span>';
            if (data === 'NEED FOR REVISION') return '<span class="badge-status badge-needrev">NEED FOR REVISION</span>';
            if (data === 'READY FOR REVIEW') return '<span class="badge-status badge-ready">READY FOR REVIEW</span>';
            if (data === 'APPROVED') return '<span class="badge-status badge-approved">APPROVED</span>';
            if (data === 'SUCCESS') return '<span class="badge-status badge-success">SUCCESS</span>';
            return data;
        }
    },
        ],
        order: [[0, 'desc']],
        responsive: false,
        scrollX: true,
    });
    @if(auth()->can('report-practical-work.create'))
    $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="window.location.href='{{ route('practicalwork.add') }}'"><i class="ti ti-file-plus ti-sm me-1"></i> {{ auth()->user()->name }} New Record </button>`)
    @endif

    // Reload DataTable hanya saat tombol Cari ditekan
    $('#filter').on('click', function() {
        dataTable.ajax.reload();
    });

    $(document).ready(function() {
    $('#formAdd').on('submit', function(e) {
        e.preventDefault(); // Mencegah form submit secara default
        let formData = new FormData(this); // Mengambil data form

        $.ajax({
            url: '{{ route("practicalwork.store") }}', // URL tujuan
            type: 'POST',
            data: formData,
            processData: false, // Penting untuk FormData
            contentType: false, // Penting untuk FormData
            success: function(response) {
                Swal.fire('Success', response.message, 'success'); // Tampilkan pesan sukses
                window.location.href = '{{ route("practicalwork.data") }}'; // Redirect setelah sukses
            },
            error: function(response) {
                Swal.fire('Error', 'Gagal menyimpan data', 'error'); // Tampilkan pesan error
                console.log(response); // Cetak error ke konsol
            }
        });
    });
});

$(document).on('click', '.edit-btn', function() {
    var id = $(this).data('id');  // Ambil ID laporan dari tombol Edit


    $.ajax({
        url: 'practical-work/getbyid/' + id, // Sesuaikan URL dengan rute yang benar
        type: 'GET',
        success: function(response) {
            if (response.data) {
                // Isi form Edit dengan data laporan yang diambil
                $('#judul_laporan').val(response.data.ereport_title);
                $('#jenis_laporan').val(response.data.ereport_type);
                $('#status').val(response.data.ereport_status);
                // Tambahkan lainnya sesuai form yang ada

                // Tampilkan modal edit
                $('#editModal').modal('show');
            } else {
                alert('Data tidak ditemukan');
            }
        },
        error: function() {
            alert('Terjadi kesalahan saat mengambil data');
        }
    });
});



$(document).on('click', '.delete-btn', function () {
    let id = $(this).data('id');
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ url("report/practical-work/delete") }}',
                type: 'POST',
                data: { ereport_id: id, _token: $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    Swal.fire('Deleted!', response.message, 'success');
                    $('#dataTable').DataTable().ajax.reload();
                },
                error: function(response) {
                    Swal.fire('Error', response.responseJSON.message, 'error');
                }
            });
        }
    });
});

    // AJAX: Simpan Edit
    $('#formEdit').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: "/report/practical-work/update",
            type: "PUT",
            data: $(this).serialize(),
            success: function(response) {
                $('#modalEdit').modal('hide');
                dataTable.ajax.reload();
                Swal.fire("Berhasil!", "Laporan telah diperbarui!", "success");
            },
            error: function() {
                Swal.fire("Error!", "Gagal menyimpan perubahan!", "error");
            }
        });
    });
});
</script>
@endsection