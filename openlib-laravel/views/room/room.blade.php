@extends('layouts/layoutMaster')

@section('title', __('rooms.room_title'))

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/swiper/swiper.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/ui-carousel.css')}}" />
<style>
    .select2-container {
        z-index: 9999 !important;
    }
</style>
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/swiper/swiper.js')}}"></script>
@endsection

@section('content')
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="ruangan-tab" data-bs-toggle="tab" href="#ruangan" role="tab" aria-controls="ruangan" aria-selected="true">{{ __('rooms.room_title') }}</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="galeri-tab" data-bs-toggle="tab" href="#galeri" role="tab" aria-controls="galeri" aria-selected="false">{{ __('rooms.gallery_title') }}</a>
    </li>
</ul>

<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="ruangan" role="tabpanel" aria-labelledby="ruangan-tab">
        <div class="card-body">
            <form class="dt_adv_search">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-12">
                                <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                    <div style="flex: 1; margin-right: 16px;">
                                        <label for="lokasi" class="form-label">{{ __('common.select_location') }}</label>
                                        <select id="lokasi" class="form-select form-select-md">
                                            <option value="">{{ __('common.all') }}</option>
                                            @foreach ($locations as $location)
                                            <option value="{{ $location->id }}" {{ $location->id == 9 ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div style="flex: 1; text-align: right; font-size: 14px;">
                                        {!! __('common.red_row_instruction', ['color' => '<span style="color:#dc3545;">' . __('common.red') . '</span>']) !!}<br>
                                        <span style="margin-top: 8px; display: inline-block;">
                                            {!! __('common.action_row_instruction', ['color' => '<span style="color:#7367f0;">' . __('common.action_button') . '</span>']) !!}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-datatable table-responsive pt-0 mt-4">
            <hr class="mt-4">
            <table class="datatables-basic table" id="ruanganTable">
                <thead>
                    <tr>
                        <th width="10%">{{ __('common.action') }}</th>
                        <th>{{ __('rooms.room_table_roomname') }}</th>
                        <th>{{ __('rooms.room_table_minimumcapacity') }}</th>
                        <th>{{ __('rooms.room_table_maximumcapacity') }}</th>
                        <th>{{ __('rooms.room_table_information') }}</th>
                        <th>{{ __('common.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <div class="tab-pane fade" id="galeri" role="tabpanel" aria-labelledby="galeri-tab">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table" id="galeriTable">
                <thead>
                    <tr>
                        <th width="10%">{{ __('common.action') }}</th>
                        <th>{{ __('rooms.gallery_table_roomname') }}</th>
                        <th>{{ __('rooms.gallery_table_gallery') }}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="frmbox" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i>{{ __('rooms.room_title') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm" class="form-validate">
                    @csrf
                    <input type="hidden" name="id" id="roomId">
                    <div class="form-group row mb-4">
                        <label class="col-md-3 col-form-label">{{ __('rooms.room_form_roomname') }} <span class="text-danger">*</span> </label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[room_name]" id="room_name" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-md-3 col-form-label">{{ __('rooms.room_form_roomcapacity') }} <span class="text-danger">*</span> </label>
                        <div class="col-md-9">
                            <input type="number" class="form-control" name="inp[room_capacity]" id="room_capacity" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-md-3 col-form-label">{{ __('rooms.room_form_minimumcapacity') }} <span class="text-danger">*</span> </label>
                        <div class="col-md-9">
                            <input type="number" class="form-control" name="inp[room_min]" id="room_min" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-md-3 col-form-label">{{ __('rooms.room_form_maximumcapacity') }} <span class="text-danger">*</span> </label>
                        <div class="col-md-9">
                            <input type="number" class="form-control" name="inp[room_max]" id="room_max" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-md-3 col-form-label">{{ __('rooms.room_form_price') }}</label>
                        <div class="col-md-9">
                            <input type="number" class="form-control" name="inp[room_price]" id="room_price" data-rule-required="false" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-md-3 col-form-label">{{ __('rooms.room_form_duration') }}</label>
                        <div class="col-md-9">
                            <input type="number" class="form-control" name="inp[room_hour]" id="room_hour" data-rule-required="false">
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-md-3 col-form-label">{{ __('rooms.room_form_information') }} <span class="text-danger">*</span> </label>
                        <div class="col-md-9">
                            <textarea class="form-control" name="inp[room_description]" id="room_description" rows="4" data-rule-required="true"></textarea>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-md-3 col-form-label">{{ __('common.select_location') }} <span class="text-danger">*</span> </label>
                        <div class="col-md-9">
                            <select id="room_id_location" name="inp[room_id_location]" class="select2 form-select form-select-lg" data-rule-required="true">
                                <option value="" disabled selected>{{ __('common.select_location') }}</option>
                                @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-primary waves-effect waves-light" onclick="saveRuangan()">{{ __('common.save') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="frmboxGaleri" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i>{{ __('rooms.gallery_title') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frmboxgaleri" class="form-validate" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="galleryId">
                    <div class="form-group row mb-4">
                        <label class="col-md-3 col-form-label">{{ __('rooms.gallery_form_roomname') }} <span class="text-danger">*</span> </label>
                        <div class="col-md-9">
                            <select id="rg_room_id" name="inp[rg_room_id]" class="select2 form-select form-select-lg" data-rule-required="true">
                                <option value="" disabled selected>{{ __('common.select_room') }}</option>
                                @foreach ($rooms as $room)
                                <option value="{{ $room->room_id }}">{{ $room->room_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
                <div class="form-group row mb-4">
                    <div class="card">
                        <h5 class="card-header">{{ __('rooms.gallery_form_uploadimage') }} <span class="text-danger">*</span></h5>
                        <div class="card-body">
                            <div id="oldImagesPreview" class="row mb-3"></div>
                            <form action="{{ url('room/room/save-gallery') }}" class="dropzone needsclick" id="dropzone-multi">
                                @csrf
                                <div class="dz-message needsclick">
                                    {{ __('rooms.gallery_image_upload_description_title') }}
                                    <span class="note needsclick">{{ __('rooms.gallery_image_upload_description_text') }}</span>
                                </div>
                                <div class="fallback">
                                    <input name="file" type="file" multiple />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-primary waves-effect waves-light" onclick="saveGaleri()">{{ __('common.save') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailGallery" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i>{{ __('common.detail_gallery') }}<span id="galleryRoomName"></span></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="gallery-swiper-container">
                    <!-- Swiper will be injected here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
    <script src="{{asset('assets/js/forms-file-upload.js')}}"></script>
    <script src="{{asset('assets/js/ui-carousel.js')}}"></script>
    <script>
    let url = '{{ url('room/room') }}';
    let dropzoneGaleri = null;
    let currentGalleryId = null;
    let removedOldImages = [];

    $(function() {
        let ruanganTable = $('#ruanganTable').DataTable({
            ajax: {
                url: url + '/ruangan',
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    d.location = $('#lokasi').val();
                }
            },
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' },
                { data: 'room_name_formatted', name: 'room_name', orderable: false, searchable: false },
                { data: 'room_min', name: 'room_min', orderable: false, searchable: false },
                { data: 'room_max', name: 'room_max', orderable: false, searchable: false },
                { data: 'room_description', name: 'room_description', orderable: false, searchable: false },
                { data: 'room_active_formatted', name: 'room_active', orderable: false, searchable: false }
            ],
            responsive: false,
            scrollX: true,
        });

        let galeriTable = $('#galeriTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            scrollX: true,
            ajax: {
                url: url + '/galeri',
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' },
                { data: 'room_name', name: 'room_name', orderable: true, searchable: true },
                { data: 'rg_image', name: 'rg_image', orderable: false, searchable: false }
            ],
            responsive: false,
            scrollX: true,
        });

        function addButtonsBasedOnTab() {
            $('.dtb').empty();
            let activeTab = $('a[data-bs-toggle="tab"].active').attr('href');
            if (activeTab === "#ruangan") {
                @if(auth() -> can('room-roomdata-room.create'))
                $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="addRuangan()"><i class="ti ti-file-plus ti-sm me-1"></i> {{ __('rooms.room_adddata') }}</button>`);
                @endif
            } else if (activeTab === "#galeri") {
                @if(auth() -> can('room-roomdata-gallery.create'))
                $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="addGaleri()"><i class="ti ti-file-plus ti-sm me-1"></i> {{ __('rooms.rooms.gallery_adddata') }}</button>`);
                @endif
            }
        }

        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            let targetTab = $(e.target).attr("href");
            if (targetTab === "#galeri") {
                $('#galeriTable').DataTable().ajax.reload();
            } else if (targetTab === "#ruangan") {
                $('#ruanganTable').DataTable().ajax.reload();
            }
            addButtonsBasedOnTab();
        });

        addButtonsBasedOnTab();

        $('#lokasi').on('change', function() {
            ruanganTable.ajax.reload();
        });

        const dropzoneMulti = document.querySelector('#dropzone-multi');
            if (dropzoneMulti) {
            const myDropzoneMulti = new Dropzone(dropzoneMulti, {
                paramName: "inp[rg_image]", // Nama parameter yang akan diterima oleh server
                maxFilesize: 5,
                acceptedFiles: ".jpeg,.jpg,.png,.gif",
                addRemoveLinks: true,
                dictDefaultMessage: "Seret file atau klik untuk meng-upload gambar.",
                parallelUploads: 1,
                success: function (file, response) {
                console.log("File berhasil diupload: ", response);
                },
                error: function (file, response) {
                console.error("Upload gagal: ", response);
                }
            });
        }
    });

    // Function Ruangan
    function addRuangan() {
        _reset();
        $("#frmbox").modal('show');
    }

    function _reset() {
        $('#frm')[0].reset();
        $('#frm').validate().resetForm();
        $('#frmboxgaleri')[0].reset();
        $('#frmboxgaleri').validate().resetForm();
        $('.select2').val(null).trigger('change');
        if (dropzoneGaleri) dropzoneGaleri.removeAllFiles();
        $('#deletedImages').remove();
    }

    function saveRuangan() {
        if ($("#frm").valid()) {
            let formData = new FormData($('#frm')[0]);

            $.ajax({
                url: url + '/save-ruangan',
                type: 'post',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.status == 'success') {
                        $('#frmbox').modal('hide');
                        $('#ruanganTable').DataTable().ajax.reload(null, false);
                        toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions);

                        $.get(url + '/list', function(rooms) {
                            let $select = $('#rg_room_id');
                            let selected = $select.val();
                            $select.empty();
                            $select.append('<option value="" disabled selected>{{ __("common.select_room") }}</option>');
                            $.each(rooms, function(i, room) {
                                $select.append('<option value="'+room.room_id+'">'+room.room_name+'</option>');
                            });
                            $select.val(selected).trigger('change');
                        });
                    }
                }
            });
        }
    }

    function edit(roomId) {
        $.ajax({
            url: url + '/get/' + roomId,
            type: 'get',
            dataType: 'json',
            success: function(e) {
                _reset();

                $('#roomId').val(e.room_id);
                $('#roomName').val(e.room_name);
                $('#roomMin').val(e.room_min);
                $('#roomMax').val(e.room_max);
                $('#roomPrice').val(e.room_price);
                $('#roomHour').val(e.room_hour);
                $('#roomCapacity').val(e.room_capacity);
                $('#roomDescription').val(e.room_description);
                $('#roomLocation').val(e.room_id_location).trigger('change');

                $.each(e, function(key, value) {
                    if ($('#' + key).hasClass("select2"))
                        $('#' + key).val(value).trigger('change');
                    else
                        $('#' + key).val(value);
                });

                $('#frmbox').modal('show')
            }
        });
    }

    function del(id) {
        yswal_delete.fire({
            title: "{{ __('common.message_delete_prompt_title') }}",
            text: "{{ __('common.message_delete_prompt_text') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url + '/delete',
                    data: {
                        id: id
                    },
                    type: 'delete',
                    dataType: 'json',
                    success: function(e) {
                        if (e.status == 'success') {
                            $('#ruanganTable').DataTable().ajax.reload(null, false);
                            toastr.success("{{ __('common.message_delete_title') }}", "{{ __('common.message_success_delete') }}", toastrOptions);

                            $.get(url + '/list', function(rooms) {
                                let $select = $('#rg_room_id');
                                let selected = $select.val();
                                $select.empty();
                                $select.append('<option value="" disabled selected>{{ __("common.select_room") }}</option>');
                                $.each(rooms, function(i, room) {
                                    $select.append('<option value="'+room.room_id+'">'+room.room_name+'</option>');
                                });
                                // Jika ruangan yang dipilih sudah dihapus, reset value
                                if (!$select.find('option[value="'+selected+'"]').length) {
                                    $select.val('').trigger('change');
                                } else {
                                    $select.val(selected).trigger('change');
                                }
                            });
                        }
                    }
                });
            }
        })
    }

    function disable(roomId) {
        let table = $('#ruanganTable').DataTable();
        let rowData = table.rows().data().toArray().find(row => row.action.includes(roomId));
        let roomNameFormatted = rowData ? rowData.room_name_formatted : '';

        yswal_disable.fire({
            title: "{{ __('common.message_deactivate_prompt_title') }}",
            text: "{{ __('common.message_deactivate_prompt_text') }} " + roomNameFormatted + "?",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url + '/toggle-status',
                    type: 'post',
                    data: {
                        room_id: roomId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#ruanganTable').DataTable().ajax.reload(null, false);
                            toastr.success("{{ __('common.message_deactivate_prompt_title_success') }}", "{{ __('common.message_success_deactivate') }}", toastrOptions);
                        } else {
                            toastr.error("{{ __('common.message_error_title') }}", "{{ __('common.message_failed_deactivate') }}", toastrOptions);
                        }
                    }
                });
            }
        });
    }

    function activate(roomId) {
        let table = $('#ruanganTable').DataTable();
        let rowData = table.rows().data().toArray().find(row => row.action.includes(roomId));
        let roomNameFormatted = rowData ? rowData.room_name_formatted : '';

        yswal_activate.fire({
            title: "{{ __('common.message_activate_prompt_title') }}",
            text: "{{ __('common.message_activate_prompt_text') }} " + roomNameFormatted + "?",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url + '/toggle-status',
                    type: 'post',
                    data: {
                        room_id: roomId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#ruanganTable').DataTable().ajax.reload(null, false);
                            toastr.success("{{ __('common.message_activate_prompt_title_success') }}", "{{ __('common.message_success_activate') }}", toastrOptions);
                        } else {
                            toastr.error("{{ __('common.message_error_title') }}", "{{ __('common.message_failed_activate') }}", toastrOptions);
                        }
                    }
                });
            }
        });
    }

    // Function Galeri
    function addGaleri() {
        _reset();
        currentGalleryId = null;
        $('#galleryId').val('');
        $('#rg_room_id').val('').trigger('change');
        removedOldImages = [];
        $('#oldImagesPreview').empty();
        $("#frmboxGaleri").modal('show');
    }

    function saveGaleri() {
        if ($("#frmboxgaleri").valid()) {
            let formData = new FormData($('#frmboxgaleri')[0]);
            let roomId = $('#rg_room_id').val();
            // Tambahkan room_id ke formData
            formData.append('inp[rg_room_id]', roomId);
            // Tambahkan index gambar lama yang dihapus
            if (removedOldImages.length > 0) {
                formData.append('deleted_images', removedOldImages.join(','));
            }
            // Ambil file dari Dropzone
            const dropzoneMulti = Dropzone.forElement('#dropzone-multi');
            if (dropzoneMulti) {
                let files = dropzoneMulti.getAcceptedFiles();
                if (files.length === 0 && $('#oldImagesPreview .old-image-item').length === 0) {
                    toastr.error("{{ __('rooms.message_room_uploadbeforesaving') }}", "{{ __('common.message_error_title') }}", toastrOptions);
                    return;
                }
                files.forEach((file, idx) => {
                    formData.append('inp[rg_image][]', file, file.name);
                });
            } else {
                // Jika dropzone tidak ada, cek input file manual
                let fileInput = $('#frmboxgaleri input[type="file"][name="inp[rg_image][]"]');
                if (fileInput.length && !fileInput[0].files.length && $('#oldImagesPreview .old-image-item').length === 0) {
                    toastr.error("{{ __('rooms.message_room_uploadbeforesaving') }}", "{{ __('common.message_error_title') }}", toastrOptions);
                    return;
                }
            }

            $.ajax({
                url: url + '/save-gallery',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.status == 'success') {
                        $('#frmboxGaleri').modal('hide');
                        $('#galeriTable').DataTable().ajax.reload(null, false);
                        toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions);
                    } else {
                        toastr.error(data.message || 'Terjadi kesalahan.', data.status || "{{ __('common.message_error_title') }}", toastrOptions);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error("{{ __('common.message_failed_save') }}", "{{ __('common.message_error_title') }}", toastrOptions);
                }
            });
        }
    }

    function delGallery(id) {
        yswal_delete.fire({
            title: "{{ __('common.message_delete_prompt_title') }}",
            text: "{{ __('common.message_delete_prompt_text') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url + '/deleteGallery',
                    data: {
                        id: id
                    },
                    type: 'delete',
                    dataType: 'json',
                    success: function(e) {
                        if (e.status == 'success') {
                            $('#galeriTable').DataTable().ajax.reload(null, false);
                            toastr.success("{{ __('common.message_delete_title') }}", "{{ __('common.message_success_delete') }}", toastrOptions);
                        }
                    }
                });
            }
        })
    }

    function editGallery(galleryId) {
        _reset();
        currentGalleryId = galleryId;
        $('#galleryId').val(galleryId);
        removedOldImages = [];
        $('#oldImagesPreview').empty();

        $.ajax({
            url: url + '/getGallery/' + galleryId,
            type: 'get',
            dataType: 'json',
            success: function(e) {
                $('#rg_room_id').val(e.rg_room_id).trigger('change');
                // Tampilkan gambar lama
                if (e.rg_image && e.rg_image.length > 0) {
                    e.rg_image.forEach(function(image, idx) {
                        const imgUrl = image;
                        const imgHtml = `
                            <div class="col-md-3 mb-2 old-image-item" data-index="${idx}">
                                <div class="position-relative">
                                    <img src="${imgUrl}" class="img-thumbnail" style="width:100%;height:120px;object-fit:cover;">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 remove-old-image" data-index="${idx}" title="Hapus gambar">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                        $('#oldImagesPreview').append(imgHtml);
                    });
                }
                // Event hapus gambar lama
                $('#oldImagesPreview').on('click', '.remove-old-image', function() {
                    const idx = $(this).data('index');
                    removedOldImages.push(idx);
                    $(this).closest('.old-image-item').remove();
                });
                // Reset Dropzone
                const dropzoneMulti = Dropzone.forElement('#dropzone-multi');
                if (dropzoneMulti) dropzoneMulti.removeAllFiles(true);

                $('#frmboxGaleri').modal('show');
            },
            error: function(xhr, status, error) {
                toastr.error("{{ __('rooms.message_room_failedgallery') }}", "{{ __('common.message_error_title') }}", toastrOptions);
            }
        });
    }

    function showGallery(galleryId) {
        const $container = $('#gallery-swiper-container');
        $container.empty();

        if (!galleryId) {
            $container.html('<div class="text-center text-muted py-5">Tidak ada gambar untuk ditampilkan.</div>');
            $('#galleryRoomName').text('');
            $('#detailGallery').modal('show');
            return;
        }

        $.ajax({
            url: url + '/detail/' + galleryId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                let imgs = response.images || [];
                let roomName = response.room_name || '';

                imgs = imgs.filter(img => img && img.trim() !== '');

                if (!imgs.length) {
                    $container.html('<div class="text-center text-muted py-5">Tidak ada gambar untuk ditampilkan.</div>');
                } else if (imgs.length === 1) {
                    $container.html(`
                        <div class="swiper" id="swiper-with-arrows">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide" style="background-image:url('${imgs[0]}');background-size:contain;background-repeat:no-repeat;background-position:center;height:60vh;"></div>
                            </div>
                        </div>
                    `);
                } else {
                    $container.html(`
                        <div class="swiper" id="swiper-with-arrows">
                            <div class="swiper-wrapper">
                                ${imgs.map(img => `<div class="swiper-slide" style="background-image:url('${img}');background-size:contain;background-repeat:no-repeat;background-position:center;height:60vh;"></div>`).join('')}
                            </div>
                            <div class="swiper-button-next swiper-button-black custom-icon"></div>
                            <div class="swiper-button-prev swiper-button-black custom-icon"></div>
                        </div>
                    `);
                }

                $('#galleryRoomName').text(roomName ? ' - ' + roomName : '');
                $('#detailGallery').modal('show');

                if (imgs.length > 1) {
                    setTimeout(() => {
                        new Swiper('#swiper-with-arrows', {
                            navigation: {
                                nextEl: '.swiper-button-next',
                                prevEl: '.swiper-button-prev',
                            },
                            loop: false,
                        });
                    }, 100);
                }
            },
            error: function() {
                $container.html('<div class="text-center text-muted py-5">Tidak ada gambar untuk ditampilkan.</div>');
                $('#galleryRoomName').text('');
                $('#detailGallery').modal('show');
            }
        });
    }

    $('#frmboxGaleri').on('hidden.bs.modal', function () {
        const dropzoneMulti = Dropzone.forElement('#dropzone-multi');
        if (dropzoneMulti) {
            dropzoneMulti.removeAllFiles(true);
        }

        $('#oldImagesPreview').empty();

        removedOldImages = [];
        currentGalleryId = null;

        $('#frmboxgaleri')[0].reset();
        $('#frmboxgaleri').validate().resetForm();
        $('#galleryId').val('');
        $('#rg_room_id').val('').trigger('change');
    });
    </script>
@endsection
