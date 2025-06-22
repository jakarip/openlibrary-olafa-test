@extends('layouts/layoutMaster')

@section('title', __('catalogs.bds_title'))

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/swiper/swiper.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/ui-carousel.css')}}" />
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form class="dt_adv_search">
            <div class="row">
                <div class="col-12">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label for="status" class="form-label">{{ __('common.select_status') }}</label>
                            <select id="status" class="form-select form-select-md">
                                <option value="">{{ __('common.all') }}</option>
                                <option value="Request">Request</option>
                                <option value="Approved">Approved</option>
                                <option value="Not Approved">Not Approved</option>
                                <option value="Process">Process</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <hr class="mt-0">
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table" id="table">
                <thead>
                    <tr class="text-nowrap">
                        <th>{{ __('common.action') }}</th>
                        <th>{{ __('catalogs.bds_table_number') }}</th>
                        <th>{{ __('catalogs.bds_table_date') }}</th>
                        <th>{{ __('catalogs.bds_table_order') }}</th>
                        <th>{{ __('catalogs.bds_table_username') }}</th>
                        <th>{{ __('catalogs.bds_table_name') }}</th>
                        <th>{{ __('catalogs.bds_table_recipient') }}</th>
                        <th>{{ __('catalogs.bds_table_address') }}</th>
                        <th>{{ __('catalogs.bds_table_phonenumber') }}</th>
                        <th>{{ __('catalogs.bds_table_catalognumber') }}</th>
                        <th>{{ __('catalogs.bds_table_barcode') }}</th>
                        <th>{{ __('catalogs.bds_table_photo_courier') }}</th>
                        <th>{{ __('catalogs.bds_table_historystatus') }}</th>
                        <th>{{ __('catalogs.bds_table_status') }}</th>
                        <th>{{ __('catalogs.bds_table_reason') }}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i>{{ __('catalogs.bds_table_historystatus') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="historyList" style="display: flex; flex-direction: column;">
                    <!-- Data akan ditambahkan di sini melalui JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="imageUploadModal" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i>{{ __('catalogs.bds_modal_uploadphotocourier_title') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group row mb-4">
                    <div class="card">
                        <h5 class="card-header">{{ __('catalogs.bds_modal_uploadphotocourier_input') }}</h5>
                        <div class="card-body">
                            <div id="oldImagesPreview" class="row mb-3"></div>
                            <form action="{{ url('catalog/bds/save-gallery') }}" class="dropzone needsclick" id="dropzone-basic" data-rule-required="true">
                                @csrf
                                <div class="dz-message needsclick">
                                    {{ __('rooms.gallery_image_upload_description_title') }}
                                    <span class="note needsclick">{{ __('rooms.gallery_image_upload_description_text') }}</span>
                                </div>
                                <div class="fallback">
                                    <input name="inp[bds_photo_courier]" type="file" accept="image/*" required />
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

<div class="modal fade" id="barcodeInputModal" tabindex="-1" aria-labelledby="barcodeInputModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="barcodeInputModalLabel">{{ __('catalogs.bds_modal_barcode_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="barcodeInputForm">
                    <!-- Input barcode akan ditambahkan di sini secara dinamis -->
                    <div id="barcodeInputsContainer"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="confirmBarcodeInput">{{ __('common.confirmation') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailGallery" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i>{{ __('catalogs.bds_table_photo_courier') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner" id="imageCarouselInner">
                        <!-- Gambar akan dimasukkan di sini -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/swiper/swiper.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/forms-file-upload.js')}}"></script>
<script src="{{asset('assets/js/ui-carousel.js')}}"></script>
<script>
let dTable = null;
let url = '{{ url('catalog/bds') }}';
let dropzoneGaleri = null;
let currentGalleryId = null;
let removedOldImages = [];

$(function() {
 dTable = $('.table').DataTable({
        ajax: {
            url: url+'/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.bds_status = $('#status').val();
            }
        },
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'bds_id', name: 'bds_id', orderable: true, searchable: true },
            { data: 'bds_createdate', name: 'bds_createdate', orderable: true, searchable: true },
            { data: 'bds_number', name: 'bds_number', orderable: true, searchable: true },
            { data: 'master_data_user', name: 'master_data_user', orderable: true, searchable: true },
            { data: 'master_data_fullname', name: 'master_data_fullname', orderable: true, searchable: true },
            { data: 'bds_receiver', name: 'bds_receiver', orderable: true, searchable: true },
            { data: 'bds_address', name: 'bds_address', orderable: true, searchable: true },
            { data: 'bds_phone', name: 'bds_phone', orderable: true, searchable: true },
            { data: 'item_codes', name: 'item_codes', orderable: true, searchable: true },
            { data: 'stock_codes', name: 'stock_codes', orderable: true, searchable: true },
            { data: 'bds_photo_courier', name: 'bds_photo_courier', orderable: true, searchable: true },
            { data: 'history', name: 'history', orderable: true, searchable: true },
            { data: 'bds_status', name: 'bds_status', orderable: true, searchable: true },
            { data: 'bds_reason', name: 'bds_reason', orderable: true, searchable: true }
        ],
        responsive: false,
        scrollX: true,
        pageLength: 10,

    });
    $('#status').on('change', function() {
        dTable.ajax.reload();
    });
    const myDropzoneBasic = new Dropzone(dropzoneBasic, {
        paramName: "inp[bds_photo_courier]", // Nama parameter yang akan diterima oleh server
        maxFilesize: 5,
        acceptedFiles: ".jpeg,.jpg,.png,.gif",
        addRemoveLinks: true,
        dictDefaultMessage: "Seret file atau klik untuk meng-upload gambar.",
        parallelUploads: 1,
        maxFiles: 1,
        success: function (file, response) {
            console.log("File berhasil diupload: ", response);
        },
        error: function (file, response) {
            console.error("Upload gagal: ", response);
        }
    });
});

function status(bds_status, action, id) {
    let newStatus;
    if (action === 'approve') {
        newStatus = 'Approved';
        confirmStatusChange(newStatus, id);
    } else if (action === 'approved') {
        newStatus = 'Process';
        confirmStatusChange(newStatus, id);
    } else if (action === 'process') {
        $.ajax({
            url: url + '/getBookDeliveryServiceBooks/' + id,
            type: 'GET',
            success: function(response) {
                $('#barcodeInputsContainer').empty();
                // Ambil memberid dari response (pastikan backend mengirimkan memberid)
                let memberid = response.memberid || null;
                let items = [];
                response.books.forEach(function(item) {
                    $('#barcodeInputsContainer').append(`
                        <div class="mb-3">
                            <label for="barcode_${item.bdsb_item_code}" class="form-label">{{ __('catalogs.bds_modal_barcode_input') }} ${item.bdsb_item_code}</label>
                            <input type="text" class="form-control" id="barcode_${item.bdsb_item_code}" name="barcode_${item.bdsb_item_code}" data-item="${item.bdsb_item_code}" required>
                        </div>
                    `);
                    items.push(item.bdsb_item_code);
                });

                $('#barcodeInputModal').data('memberid', memberid);
                $('#barcodeInputModal').data('items', items);

                $('#barcodeInputModal').modal('show');
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message, "{{ __('common.message_error_title') }}", toastrOptions);
            }
        });

        $('#barcodeInputModal').on('hidden.bs.modal', function () {
            $('#barcodeInputsContainer').empty();
        });

        $('#confirmBarcodeInput').off('click').on('click', function() {
            let barcodes = [], items = [], validBarcodes = [], invalidBarcodes = [];

            $('#barcodeInputsContainer input').each(function() {
                barcodes.push($(this).val());
                items.push($(this).data('item'));
            });

            let memberid = $('#barcodeInputModal').data('memberid');

            let checkBarcodePromises = barcodes.map((barcode, idx) => {
                return $.ajax({
                    url: url + '/checkEksemplar',
                    type: 'POST',
                    data: {
                        item: items[idx],
                        barcode: barcode,
                        memberid: memberid,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                }).then(response => {
                    if (response.exists) {
                        validBarcodes.push(barcode);
                    } else {
                        invalidBarcodes.push(barcode);
                    }
                });
            });

            Promise.all(checkBarcodePromises).then(() => {
                if (invalidBarcodes.length > 0) {
                    toastr.error("{{ __('catalogs.bds_message_invalid_barcode') }} " + invalidBarcodes.join(', '), "{{ __('common.message_error_title') }}", toastrOptions);
                    return;
                }
                if (validBarcodes.length === barcodes.length) {
                    newStatus = 'Process';
                    confirmStatusChange(newStatus, id, validBarcodes);
                }
                $('#barcodeInputModal').modal('hide');
            }).catch(xhr => {
                toastr.error(xhr.responseJSON.message, "{{ __('common.message_error_title') }}", toastrOptions);
            });
        });
    } else if (action === 'send') {
        newStatus = 'Send';
        confirmStatusChange(newStatus, id);
    } else if (action === 'received') {
        newStatus = 'Received';
        confirmStatusChange(newStatus, id, null, null, true);
    } else if (action === 'completed') {
        newStatus = 'Completed';
        confirmStatusChange(newStatus, id);
    } else if (action === 'notApprove') {
        yswal_reason.fire({
            title: "{{ __('common.message_rejection_prompt_title') }}",
            input: 'textarea',
            inputPlaceholder: "{{ __('common.message_rejection_prompt_text') }}",
            preConfirm: (reason) => {
                if (!reason) {
                    yswal_reason.showValidationMessage("{{ __('common.message_rejection_prompt_validation') }}");
                }
                return reason;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                newStatus = 'Not Approved';
                confirmStatusChange(newStatus, id, null, result.value);
            }
        });
    }
}

function confirmStatusChange(newStatus, id, barcodes = null, reason = null, requiresImage = false) {
        yswal_confirmstatus.fire({
            title: `{{ __('common.message_change_status_prompt_title') }}`,
            text: `{{ __('common.message_change_status_prompt_text') }} ${newStatus}?`
        }).then((result) => {
        if (result.isConfirmed) {
            if (requiresImage) {
                currentGalleryId = id;
                $('#imageUploadModal').modal('show');
            } else {
                updateStatus(id, newStatus, barcodes, reason);
            }
        }
    });
}

function updateStatus(id, newStatus, barcodes = null, reason = null) {
    $.ajax({
        url: url + `/change/${id}`,
        type: 'POST',
        dataType: 'json',
        data: {
            bds_status: newStatus,
            bds_reason: reason,
            barcodes: barcodes,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(e) {
            if (e.message) {
                toastr.success(e.message, 'Success', toastrOptions);
                $('.table').DataTable().ajax.reload(null, false);
            }
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON.message, "{{ __('common.message_error_title') }}", toastrOptions);
        }
    });
}

function history(concatenated_bdss_date, concatenated_bdss_status) {
    $('#historyList').empty().append(`
        <div style="display: flex; justify-content: space-between; padding: 10px 0; font-weight: bold;">
            <div style="flex: 1;">{{ __('catalogs.bds_table_status') }}</div>
            <div style="flex: 1; text-align: right;">{{ __('catalogs.bds_table_date') }}</div>
        </div>
    `);

    const dates = concatenated_bdss_date ? concatenated_bdss_date.split(',') : [];
    const statuses = concatenated_bdss_status ? concatenated_bdss_status.split(',') : [];
    const maxLength = Math.max(dates.length, statuses.length);
    for (let i = 0; i < maxLength; i++) {
        $('#historyList').append(`
            <div style="display: flex; justify-content: space-between; padding: 5px 0;">
                <div style="flex: 1;">${statuses[i] || ''}</div>
                <div style="flex: 1; text-align: right;">${dates[i] || ''}</div>
            </div>
        `);
    }

    $('#historyModal').modal('show');
}

$('#imageUploadModal').on('hidden.bs.modal', function () {
    const dropzoneBasic = Dropzone.forElement('#dropzone-basic');
    if (dropzoneBasic) {
        dropzoneBasic.removeAllFiles(true);
    }

    $('#oldImagesPreview').empty();
    removedOldImages = [];
    currentGalleryId = null;
});

function saveGaleri() {
    const dropzoneBasic = Dropzone.forElement('#dropzone-basic');
    if (dropzoneBasic) {
        let files = dropzoneBasic.getAcceptedFiles();
        if (files.length === 0) {
            toastr.error("{{ __('rooms.message_room_uploadbeforesaving') }}", "{{ __('common.message_error_title') }}", toastrOptions);
            return;
        }
    }

    let formData = new FormData();
    let bds_id = currentGalleryId;
    if (bds_id) {
        formData.append('inp[bds_id]', bds_id);
    }

    if (dropzoneBasic) {
        let files = dropzoneBasic.getAcceptedFiles();
        // Kirim hanya file pertama
        formData.append('inp[bds_photo_courier]', files[0], files[0].name);
    }

    if (removedOldImages.length > 0) {
        formData.append('deleted_images', removedOldImages.join(','));
    }

    $.ajax({
        url: url + '/save-gallery',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(data) {
            if (data.status == 'success') {
                $('#imageUploadModal').modal('hide');
                $('.table').DataTable().ajax.reload(null, false);
                toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions);
            } else if (data.status == 'error') {
                toastr.error(data.message, "{{ __('common.message_error_title') }}", toastrOptions);
            }
        },
        error: function(xhr, status, error) {
            toastr.error("{{ __('common.message_failed_save') }}", "{{ __('common.message_error_title') }}", toastrOptions);
        }
    });
}

function showGallery(images) {
    const $carouselInner = $('#imageCarouselInner').empty();
    try {
        if (typeof images === 'string') images = JSON.parse(images);

        if (Array.isArray(images) && images.length > 0) {
            images.forEach((image, index) => {
                let imgUrl = image;
                if (!imgUrl.startsWith('http') && !imgUrl.startsWith('/storage/')) {
                    imgUrl = '/storage/' + imgUrl.replace(/^storage[\/\\]?/, '');
                }
                $carouselInner.append(`
                    <div class="carousel-item ${index === 0 ? 'active' : ''}">
                        <img src="${imgUrl}" alt="Gambar ${index + 1}" class="d-block w-100" style="max-height: 70vh; object-fit: contain; margin: 0 auto;">
                    </div>
                `);
            });
        } else {
            $carouselInner.html('<div class="text-center text-muted py-5">Tidak ada gambar untuk ditampilkan.</div>');
        }
    } catch (error) {
        console.error("Error displaying gallery:", error);
        $carouselInner.html('<div class="text-center text-danger py-5">Terjadi kesalahan saat menampilkan galeri.</div>');
    }
    $('#detailGallery').modal('show');
}

$(document).on('click', '.courier-photo-gallery', function(e) {
    e.preventDefault();
    const images = $(this).data('images');
    showGallery(images);
});
</script>
@endsection
