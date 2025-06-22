@extends('layouts.layoutMaster')

@section('title', 'Notifikasi Member')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <script src="{{ asset('assets/vendor/libs/toastr/toastr.js') }}"></script>
@endsection

@section('page-style')
    <style>
        .modal {
            overflow: visible !important;
        }

        .modal .select2-container {
            z-index: 99999 !important;
        }

        .select2-dropdown {
            z-index: 99999 !important;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Notifikasi Member</h5>
            <div>
                <button class="btn btn-success me-2" onclick="addNotification()">
                    <i class="ti ti-plus me-1"></i> Tambah Notifikasi
                </button>
            </div>
        </div>

        <div class="card-body">
            <!-- Jika diperlukan filter, bisa ditambahkan di sini -->
            <form id="filterForm">
                <div class="row">
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" id="filterBtn" class="btn btn-primary">
                            <i class="ti ti-search me-1"></i> Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Notifikasi -->
        <div class="card-datatable table-responsive overflow-auto pt-0">
            <table class="datatables-basic table border-top" id="notificationTable">
                <thead>
                    <tr id="tableHeader">
                        <th width="10%">Aksi</th>
                        <th>Member</th>
                        <th>Judul Notifikasi</th>
                        <th>Konten</th>
                        <th>Status Terkirim</th>
                        <th>Tanggal Dibuat</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Notifikasi -->
    <div class="modal fade" id="addNotificationModal" tabindex="-1" aria-labelledby="addNotificationLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-add-notification">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Tambah Notifikasi</h3>
                        <p class="text-muted">Lengkapi informasi berikut untuk menambahkan notifikasi baru.</p>
                    </div>

                    <!-- Form tambah notifikasi -->
                    <form id="addNotificationForm" class="row g-3 needs-validation" novalidate>
                        @csrf
                        <!-- Pilih Member -->
                        <div class="col-12">
                            <label class="form-label" for="addNotificationMember">Member <span
                                    class="text-danger">*</span></label>
                            <select id="addNotificationMember" name="member_id" class="form-select select2" required>
                                <option value="">Pilih Member</option>
                                {{-- Asumsikan kamu sudah mengirimkan data member ke view jika diperlukan,
                                atau bisa juga menggunakan AJAX search --}}
                                @foreach ($memberNotifications->pluck('member')->unique('id') as $member)
                                    @if($member)
                                        <option value="{{ $member->id }}">{{ $member->master_data_fullname }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Silakan pilih member.</div>
                        </div>

                        <!-- Judul Notifikasi -->
                        <div class="col-12">
                            <label class="form-label" for="addNotificationTitle">Judul Notifikasi <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="addNotificationTitle" name="title" class="form-control"
                                placeholder="Masukkan judul notifikasi" required>
                            <div class="invalid-feedback">Judul notifikasi wajib diisi.</div>
                        </div>

                        <!-- Konten Notifikasi -->
                        <div class="col-12">
                            <label class="form-label" for="addNotificationContent">Konten <span
                                    class="text-danger">*</span></label>
                            <textarea id="addNotificationContent" name="content" class="form-control"
                                placeholder="Masukkan konten notifikasi" required></textarea>
                            <div class="invalid-feedback">Konten notifikasi wajib diisi.</div>
                        </div>

                        <!-- Status Terkirim -->
                        <div class="col-12">
                            <label class="form-label" for="addNotificationSent">Status Terkirim</label>
                            <select id="addNotificationSent" name="sent" class="form-select">
                                <option value="0" selected>Belum Terkirim</option>
                                <option value="1">Terkirim</option>
                            </select>
                        </div>

                        <div class="col-12 text-center mt-4">
                            <button type="button" class="btn btn-label-primary me-3" data-bs-dismiss="modal">
                                <span class="align-middle">Kembali</span>
                            </button>
                            <button type="button" class="btn btn-primary" onclick="saveNotification()">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Notifikasi -->
    <div class="modal fade" id="editNotificationModal" tabindex="-1" aria-labelledby="editNotificationLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-edit-notification">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Edit Notifikasi</h3>
                        <p class="text-muted">Perbarui informasi notifikasi yang diperlukan.</p>
                    </div>

                    <!-- Form edit notifikasi -->
                    <form id="editNotificationForm" class="row g-3 needs-validation" novalidate>
                        @csrf
                        <input type="hidden" id="editNotificationId" name="id">

                        <!-- Pilih Member -->
                        <div class="col-12">
                            <label class="form-label" for="editNotificationMember">Member <span
                                    class="text-danger">*</span></label>
                            <select id="editNotificationMember" name="member_id" class="form-select select2" required>
                                <option value="">Pilih Member</option>
                                @foreach ($memberNotifications->pluck('member')->unique('id') as $member)
                                    @if($member)
                                        <option value="{{ $member->id }}">{{ $member->master_data_fullname }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Silakan pilih member.</div>
                        </div>

                        <!-- Judul Notifikasi -->
                        <div class="col-12">
                            <label class="form-label" for="editNotificationTitle">Judul Notifikasi <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="editNotificationTitle" name="title" class="form-control"
                                placeholder="Masukkan judul notifikasi" required>
                            <div class="invalid-feedback">Judul notifikasi wajib diisi.</div>
                        </div>

                        <!-- Konten Notifikasi -->
                        <div class="col-12">
                            <label class="form-label" for="editNotificationContent">Konten <span
                                    class="text-danger">*</span></label>
                            <textarea id="editNotificationContent" name="content" class="form-control"
                                placeholder="Masukkan konten notifikasi" required></textarea>
                            <div class="invalid-feedback">Konten notifikasi wajib diisi.</div>
                        </div>

                        <!-- Status Terkirim -->
                        <div class="col-12">
                            <label class="form-label" for="editNotificationSent">Status Terkirim</label>
                            <select id="editNotificationSent" name="sent" class="form-select">
                                <option value="0">Belum Terkirim</option>
                                <option value="1">Terkirim</option>
                            </select>
                        </div>

                        <div class="col-12 text-center mt-4">
                            <button type="button" class="btn btn-label-primary me-3" data-bs-dismiss="modal">
                                <span class="align-middle">Kembali</span>
                            </button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
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
        let notificationTable = null;

        $(document).ready(function () {
            $('.select2').select2();

            // Inisialisasi DataTable untuk notifikasi
            notificationTable = $('#notificationTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                responsive: false,
                autoWidth: false,
                ajax: {
                    url: '{{ url("member/notification/dt") }}',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: function (d) {
                        return d;
                    }
                },
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'member_id', name: 'member_id' },  // Akan menampilkan nama member sesuai editColumn di controller
                    { data: 'title', name: 'title' },
                    { data: 'content', name: 'content' },
                    {
                        data: 'sent', name: 'sent', render: function (data) {
                            return data == 1 ? 'Terkirim' : 'Belum Terkirim';
                        }
                    },
                    { data: 'created_at', name: 'created_at' }
                ]
            });

            $('#filterBtn').on('click', function () {
                notificationTable.ajax.reload();
            });

            // Event untuk tombol edit dan delete
            $(document).on('click', '.edit-btn', function () {
                let id = $(this).data('id');
                editNotification(id);
            });

            $(document).on('click', '.delete-btn', function () {
                let id = $(this).data('id');
                deleteNotification(id);
            });
        });

        function addNotification() {
            $('#addNotificationForm')[0].reset();
            $('#addNotificationForm').removeClass('was-validated');
            $('#addNotificationModal').modal('show');
        }

        function saveNotification() {
            const form = $('#addNotificationForm');
            if (!form[0].checkValidity()) {
                form[0].classList.add('was-validated');
                return;
            }
            const formData = form.serialize();
            const saveButton = $('button[onclick="saveNotification()"]');
            saveButton.prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: '{{ url("member/notification/insert") }}',
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: formData,
                success: function (response) {
                    $('#addNotificationModal').modal('hide');
                    toastr.success(response.message || 'Notifikasi berhasil ditambahkan!', 'Sukses', { closeButton: true, progressBar: true });
                    notificationTable.ajax.reload();
                },
                error: function (xhr) {
                    console.error('Error:', xhr.responseText);
                    toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan saat menambahkan notifikasi.', 'Gagal', { closeButton: true, progressBar: true });
                },
                complete: function () {
                    saveButton.prop('disabled', false).text('Simpan');
                }
            });
        }

        function editNotification(id) {
            $.get('{{ url("member/notification/edit") }}/' + id, function (data) {
                if (!data) {
                    alert('Data tidak ditemukan.');
                    return;
                }
                $('#editNotificationId').val(data.id);
                $('#editNotificationMember').val(data.member_id).trigger('change');
                $('#editNotificationTitle').val(data.title);
                $('#editNotificationContent').val(data.content);
                $('#editNotificationSent').val(data.sent);
                $('#editNotificationModal').modal('show');
            }).fail(function (xhr) {
                alert('Terjadi kesalahan saat mengambil data.');
                console.error('Error:', xhr.responseText);
            });
        }

        $('#editNotificationForm').on('submit', function (e) {
            e.preventDefault();
            const form = this;
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            const formData = $(form).serialize();
            const submitBtn = $(form).find('button[type="submit"]');
            submitBtn.prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: '{{ url("member/notification/update") }}/' + $('#editNotificationId').val(),
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: formData,
                success: function (response) {
                    $('#editNotificationModal').modal('hide');
                    toastr.success(response.message || 'Notifikasi berhasil diperbarui!', 'Sukses', { closeButton: true, progressBar: true });
                    notificationTable.ajax.reload();
                },
                error: function (xhr) {
                    console.error('Error:', xhr.responseText);
                    toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan saat memperbarui notifikasi.', 'Gagal', { closeButton: true, progressBar: true });
                },
                complete: function () {
                    submitBtn.prop('disabled', false).text('Simpan');
                }
            });
        });

        function deleteNotification(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("member/notification/delete") }}/' + id,
                        type: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            notificationTable.ajax.reload();
                            toastr.success(response.message || 'Notifikasi berhasil dihapus!', 'Sukses', { closeButton: true, progressBar: true });
                        },
                        error: function (xhr) {
                            console.error('Error:', xhr.responseText);
                            toastr.error(xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus notifikasi.', 'Gagal', { closeButton: true, progressBar: true });
                        }
                    });
                }
            });
        }
    </script>
@endsection