@extends('layouts/layoutMaster')

@section('title', __('rooms.booking_title'))

@section('vendor-style')
<link rel="stylesheet" type="text/css" href="../dist/css/style.min.css" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/swiper/swiper.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/ui-carousel.css')}}" />
<style>
    .booking-main-content { display: none; }
</style>
@endsection

@section('content')
<div class="card booking-main-content">
    {{-- Search --}}
    <div class="card-body">
        <form class="dt_adv_search" id="dateFilterForm">
            <div class="d-flex justify-content-between align-items-start flex-wrap">
                @if(auth()->can('room-booking.create'))
                    <div class="mb-2">
                        <button type="button" class="btn btn-danger d-flex align-items-center" id="btnPesanRuangan">
                            <i class="fa fa-plus-square me-2"></i> {{ __('rooms.booking_title') }}
                        </button>
                    </div>
                @endif
                @if(auth()->can('room-booking.filter'))
                    <div class="d-flex flex-wrap align-items-start gap-3 mb-2">
                        @php
                            $filteredLocations = $locations->filter(function($location) {
                                return in_array($location->id, [9, 15]);
                            });
                            if (!isset($selected_location) || !$filteredLocations->contains('id', $selected_location)) {
                                $selected_location = 9;
                            }
                        @endphp

                        <div class="d-flex align-items-center">
                            <label for="lokasi" class="form-label mb-0 me-2">{{ __('common.select_location') }}</label>
                            <select id="lokasi" name="location" class="form-select form-select-md">
                                @foreach ($filteredLocations as $location)
                                    <option value="{{ $location->id }}"
                                            {{ $selected_location == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex align-items-center">
                            <label for="tanggal" class="form-label mb-0 me-2">{{ __('common.select_date') }}</label>
                            <input type="date" id="tanggal" name="date" class="form-select form-select-md"
                                value="{{ $selected_date ?? now()->toDateString() }}">
                        </div>
                        <div>
                            <button type="button" id="btnFilterSearch" class="btn btn-primary">{{ __('common.member.search') }}</button>
                        </div>
                    </div>
                @endif
            </div>
        </form>
    </div>
    <hr class="mt-0">
    {{-- Tabel Ruangan --}}
    <div class="container" style="width: 100%; max-width: 1500px;">
      <div style="padding: 0 0 40px">
        <div id="schedule"></div>
      </div>
    </div>
    <hr class="mt-0">
    {{-- Syarat & Ketentuan --}}
    <div class="card-datatable pt-0">
        <div class="container mt-5" style="width: 100%; max-width: 1500px;">
            <h4 style="color: black;">{{ __('rooms.booking_term_condition_title') }}</h4>
            <ul style="list-style-type: none; padding: 0;">
                @foreach($termconditions as $termcondition)
                    <hr style="border: 1px solid #dddddd; margin: 2px 0;">
                    <li style="font-size: 1.1em; color: #6f6b7d; list-style-type: none; margin-top: 15px;">
                        <span style="display: inline-block; min-width: 2.5em; vertical-align: top;">{{ $termcondition->term_sequence }}.</span>
                        <span style="display: inline-block; width: calc(100% - 3em);">
                            @if(app()->getLocale() == 'en')
                                {!! str_replace(["\r\n", "\n", "\r"], '<br>', trim($termcondition->information_en ?? '')) !!}
                            @else
                                {!! str_replace(["\r\n", "\n", "\r"], '<br>', trim($termcondition->information ?? '')) !!}
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

@if(auth()->can('room-booking.create'))
    <div class="modal fade" id="pesanruanganModal" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-transparent">
                    <div class="modal-title">
                        <i class="fa fa-plus-square me-2"></i>{{ __('rooms.booking_title') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="pesanruangan" class="form-validate">
                        @csrf
                        <input type="hidden" name="bk_id" id="bk_id">
                        <div class="form-group row mb-4">
                            <label class="col-md-3 col-form-label">
                                {{ __('rooms.booking_input_name') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <select id="bk_room_id" name="bk_room_id" class="form-select form-select-lg" style="font-size: 16px;" required>
                                    <option value="">{{ __('common.select_room') }}</option>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->room_id }}"
                                                data-room-max="{{ $room->room_max }}"
                                                data-room-min="{{ $room->room_min }}"
                                                data-room-hour="{{ $room->room_hour }}"
                                                data-room-price="{{ $room->room_price }}">
                                            {{ $room->room_name }}
                                            @if(!empty($room->room_price) && !empty($room->room_hour))
                                                ( {{ 'Rp' . number_format($room->room_price, 0, ',', '.') }} / {{ $room->room_hour }} {{ __('common.hour') }} )
                                            @endif
                                            ( {{ $room->room_min }} - {{ $room->room_max }} {{ __('common.person') }} )
                                        </option>
                                    @endforeach
                                </select>
                                @if($rooms->isEmpty())
                                    <div class="alert alert-warning mt-2">
                                        {{ __('common.no_room_found') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-md-3 col-form-label">
                                {{ __('rooms.booking_input_phonenumber') }}
                            </label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="bk_mobile_phone" id="bk_mobile_phone"
                                    value="{{ session()->get('userData')->master_data_mobile_phone ?? '' }}" readonly
                                    style="border: none; background-color: #f0f0f0; outline: none; cursor: default;">
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-md-3 col-form-label">
                                {{ __('rooms.booking_input_date') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <input type="date" class="form-control" name="bk_startdate" id="bk_startdate" required>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-md-3 col-form-label">
                                {{ __('rooms.booking_input_time') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                @php $firstRoom = $rooms->first(); @endphp
                                <select id="time_slot" name="time_slot" class="form-select form-select-lg" required style="font-size: 16px;">
                                    <option value="">{{ __('common.select_time') }}</option>
                                    @if($firstRoom && isset($timeSlotsByRoom[$firstRoom->room_id]))
                                        @foreach($timeSlotsByRoom[$firstRoom->room_id] as $slot)
                                            @if($slot['value'] !== 'full_day' || auth()->can('room-booking.full_day'))
                                                <option value="{{ $slot['value'] }}">{{ $slot['label'] }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-4" id="duration_container" style="display: none;">
                            <label class="col-md-3 col-form-label">
                                {{ __('rooms.booking_input_duration') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <select id="duration_slot" name="duration_slot" class="form-select form-select-lg" style="font-size: 16px;">
                                    <option value="">{{ __('common.select_duration') }}</option>
                                    {{-- options diisi via JS --}}
                                </select>
                                <input type="hidden" id="duration_slot_hidden" name="duration_slot">
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-md-3 col-form-label">
                                {{ __('rooms.booking_input_purpose') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="bk_purpose" id="bk_purpose" rows="3" required></textarea>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-md-3 col-form-label">
                                {{ __('rooms.booking_input_member') }} <span class="text-danger">*</span>
                            </label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="bk_name" id="bk_name" placeholder="{{ __('common.input_description_member') }}">
                                <div class="invalid-feedback">Please enter member information</div>
                                <div id="search-results" class="dropdown-menu" style="display: none;"></div>
                                <div id="selected-users" class="mt-2"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="save()">{{ __('common.save') }}</button>
                </div>
            </div>
        </div>
    </div>
@endif

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

<div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title bg-transparent">
                    <i class="fas fa-calendar-alt me-2" style="color: #B61614;"></i>
                    <span id="bookingModalLabel" style="color: #B61614; font-weight: bold;"></span> <!-- Elemen untuk title -->
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="bookingInfo"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.min.js" type="text/javascript" language="javascript"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script type="text/javascript" src="../dist/js/jq.schedule.min.js"></script>
<script src="{{asset('assets/vendor/libs/swiper/swiper.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/ui-carousel.js')}}"></script>
<script>
$(document).ready(function() {
    $('#loading').show();
    $('.booking-main-content').hide();

    setTimeout(function() {
        $('#loading').fadeOut(200, function() {
            $('.booking-main-content').fadeIn(200, function() {
                initSchedule();
            });
        });
    }, 1000);
});

const timeSlotsByRoom = @json($timeSlotsByRoom),
      durationsByRoom = @json($durationsByRoom),
      operationalHours = @json($operationalHours),
      url = '{{ url('room/booking') }}';

let selectedUsers = [], debounceTimer;

const parseHour = t => parseInt(t.split(':')[0],10),
      parseMinute = t => parseInt(t.split(':')[1],10),
      toHourMinute = t => t ? t.split(':').slice(0,2).join(':') : '';

function addLog(type, message) {
    const $log = $("<tr />");
    $log.append($("<th />").text(type)).append($("<td />").text(message ? JSON.stringify(message) : ""));
}

function resetForm() {
    $('#pesanruangan')[0].reset();
    selectedUsers = [];
    $('#selected-users').empty();
    $('#duration_slot').html('<option value="">{{ __("common.select_duration") }}</option>');
    $('#duration_container').hide();
}

function save() {
    if (!$("#pesanruangan")[0].checkValidity()) return $("#pesanruangan")[0].reportValidity();

    const startDate = $('#bk_startdate').val(),
          selectedTime = $('#time_slot').val(),
          duration = $('#duration_slot_hidden').val();
          $selectedRoom = $('#bk_room_id option:selected'),
          roomMax = $selectedRoom.data('room-max'),
          roomMin = $selectedRoom.data('room-min'),
          locationId = $('#lokasi').val();

    if (!duration) return toastr.error("Silakan pilih durasi.", "", toastrOptions);

    const totalMembers = selectedUsers.length;
    @if(!auth()->can('room-booking.filtermember'))
        if (totalMembers < roomMin) return toastr.error(`{!! str_replace([':total', ':min'], ['${totalMembers}', '${roomMin}'], __('rooms.booking_members_below_min')) !!}`, "", toastrOptions);
        if (totalMembers > roomMax) return toastr.error(`{!! str_replace([':total', ':max'], ['${totalMembers}', '${roomMax}'], __('rooms.booking_members_above_max')) !!}`, "", toastrOptions);
    @endif

    let hours = operationalHours[locationId] || {start:'08:00:00',end:'17:00:00'};
    let endDateTime = selectedTime === "full_day"
        ? new Date(`${startDate}T${toHourMinute(hours.end)}`)
        : new Date(new Date(`${startDate}T${selectedTime}`).getTime() + (duration * 60000));

    $('#bk_enddate').val(endDateTime.toISOString().slice(0,19).replace('T',' '));
    $('#bk_name').val(selectedUsers.join(','));

    $.ajax({
        url: url + '/save', type: 'POST', data: $('#pesanruangan').serialize(),
        success: data => {
            if (data.status == 'success') {
                $('#pesanruanganModal').modal('hide');
                yswal_success.fire({
                    icon: 'success',
                    title: "{{ __('common.success_notification') }}",
                    text: data.notif_message
                }).then(() => location.reload());
            } else toastr.error(data.message, "", toastrOptions);
        },
        error: xhr => {
            if (xhr.status === 400) {
                const msg = xhr.responseJSON && xhr.responseJSON.message;
                $('#pesanruanganModal').modal('hide');
                if (msg && msg.includes('Anda sudah melebihi jumlah permintaan peminjaman yang diperbolehkan')) {
                    // Swal.fire({icon:'warning',title:'Maksimal Permintaan!',text:msg,confirmButtonText:'OK'});
                    yswal_room_maximum_request.fire({
                        icon: 'error',
                        title: "{{ __('rooms.booking_room_maximum_request_title') }}",
                        text: "{{ __('rooms.booking_room_maximum_request_text') }}"
                    });
                } else if (msg && msg.includes('Anda tidak dapat melakukan peminjaman pada bulan ini')) {
                    // Swal.fire({icon:'warning',title:'Diblokir Sementara!',text:msg,confirmButtonText:'OK'});
                    yswal_room_temporarily_blocked.fire({
                        icon: 'error',
                        title: "{{ __('rooms.booking_room_temporarily_blocked_title') }}",
                        text: "{{ __('rooms.booking_room_temporarily_blocked_text') }}"
                    });
                } else {
                    yswal_room_already_booked.fire({
                        icon: 'warning',
                        title: "{{ __('rooms.booking_room_already_booked_title') }}",
                        text: "{{ __('rooms.booking_room_already_booked_text') }}"
                    });
                }
            } else {
                $('#pesanruanganModal').modal('hide');
                const errors = xhr.responseJSON.errors || {};
                Swal.fire({icon:'warning',title:'Gagal',html:Object.values(errors).map(e=>e[0]).join('<br>'),confirmButtonText:'OK'});
            }
        }
    });
}

function showGallery(images, roomName='') {
    const $container = $('#gallery-swiper-container');
    $container.empty();
    let imgs = [];
    try {
        if (typeof images === 'string') imgs = JSON.parse(images);
        else if (Array.isArray(images)) imgs = images;
    } catch { imgs = []; }

    imgs = imgs
        .filter(img => img && img.trim() !== '')
        .map(img => {
            if (img.startsWith('http')) return img;
            let cleanImg = img.trim().replace(/^\/+/, '');
            if (!cleanImg.startsWith('storage/')) cleanImg = 'storage/' + cleanImg;
            return '/' + cleanImg;
        });

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
}

function initSchedule() {
    const scheduleDataFromServer = {!! $scheduleData !!},
          selectedLocation = $('#lokasi').val() || '9';

    let hours = operationalHours[selectedLocation] || {start:'08:00:00',end:'17:00:00'};

    $("#schedule").timeSchedule({
        startTime: toHourMinute(hours.start),
        endTime: toHourMinute(hours.end),
        widthTime: 600,
        timeLineY: 60,
        verticalScrollbar: 20,
        timeLineBorder: 2,
        bundleMoveWidth: 6,
        draggable: false,
        resizable: false,
        resizableLeft: false,
        rows: scheduleDataFromServer,
        onAppendSchedule: (node, data) => {
            addLog("onAppendSchedule", data);
            node.css({'opacity':'1','cursor':'default'});
            if (data.data.status) node.addClass("sc_bar_status_" + data.data.status.toLowerCase().replace(' ', '_'));
            if (data.data.image) {
                const $img = $('<div class="photo"><img></div>').find("img").attr("src", data.data.image).end();
                node.prepend($img).addClass("sc_bar_photo");
            }
            node.on('click', () => {
                const bookingId = data.data.id;
                if (!bookingId) return alert('ID booking tidak ditemukan.');
                $.get(url + '/info', {id: bookingId}, res => {
                    $('#bookingModalLabel').text(res.modalTitle || 'Detail Booking');
                    $('#bookingInfo').html(res.html);
                    $('#bookingModal').modal('show');
                });
            });
        }
    });
}

$(function() {
    $("#logs").append('<table class="table">');

    $('#btnPesanRuangan').click(() => $('#pesanruanganModal').modal('show'));
    $('#pesanruanganModal').on('hidden.bs.modal', resetForm);

    $('#btnFilterSearch').click(() => {
        const date = $('#tanggal').val(), loc = $('#lokasi').val();
        if (!loc) return toastr.error("Silakan pilih lokasi.", "", toastrOptions);
        let params = [`location=${loc}`];
        if (date) params.push(`date=${date}`);
        window.location.href = `{{ url('room/booking') }}?${params.join('&')}`;
    });

    $('#lokasi').change(function() {
        const loc = $(this).val();
        const date = $('#tanggal').val();
        let params = [`location=${loc}`];
        if (date) params.push(`date=${date}`);
        window.location.href = `{{ url('room/booking') }}?${params.join('&')}`;
    });

    $('#bk_name').on('input', function() {
        clearTimeout(debounceTimer);
        const q = $(this).val();
        if (q.length < 5 || q.length > 10) return $('#search-results').hide();
        debounceTimer = setTimeout(() => {
            $('#search-results').html('<div class="loading">Loading...</div>').show();
            $.ajax({
                url: url + '/search', method: 'GET', data: {search: q}, dataType: 'json',
                success: data => {
                    let results = data.length > 0 ? data.map(item => `<a href="#" class="dropdown-item" data-id="${item.master_data_user}" data-fullname="${item.master_data_fullname}">
                        <div>${item.master_data_number} - ${item.master_data_user} - ${item.master_data_fullname}</div></a>`).join('') : '<div class="dropdown-item">Tidak ada hasil ditemukan</div>';
                    $('#search-results').html(results).show();
                },
                error: () => $('#search-results').html('<div class="dropdown-item">Terjadi kesalahan saat mencari data</div>').show()
            });
        }, 300);
    });

    $(document)
    .on('click', '.dropdown-item', e => {
        e.preventDefault();
        const userId = e.currentTarget.dataset.id, userName = e.currentTarget.dataset.fullname;
        if (!selectedUsers.includes(userName)) {
            selectedUsers.push(userName);
            $('#selected-users').append(`<span class="custom-badge selected-user" data-id="${userName}">${userName}<button type="button" class="close remove-user" aria-label="Close"><i class="fas fa-times"></i></button></span>`);
        }
        $('#bk_name').val('');
        $('#search-results').hide();
    })
    .on('click', '.remove-user', e => {
        const userId = $(e.currentTarget).parent().data('id');
        selectedUsers = selectedUsers.filter(u => u !== userId);
        $(e.currentTarget).parent().remove();
    })
    .on('click', e => {
        if (!$(e.target).closest('#bk_name').length) $('#search-results').hide();
    })
    .on('click', '.view-gallery', e => {
        e.preventDefault();
        showGallery($(e.currentTarget).data('rg-image'), $(e.currentTarget).data('room-name') || '');
    });

    $('#bk_room_id').change(function() {
        const roomId = $(this).val(),
              $timeSlot = $('#time_slot').empty().append('<option value="">{{ __("common.select_time") }}</option>');
        if (roomId && timeSlotsByRoom[roomId]) timeSlotsByRoom[roomId].forEach(slot => $timeSlot.append(`<option value="${slot.value}">${slot.label}</option>`));
        $('#duration_slot').html('<option value="">{{ __("common.select_duration") }}</option>');
        $('#duration_container').hide();
    });

    // $('#time_slot').change(function() {
    //     const roomId = $('#bk_room_id').val(),
    //           selectedTime = $(this).val(),
    //           $durationSelect = $('#duration_slot').html('<option value="">{{ __("common.select_duration") }}</option>'),
    //           $durationContainer = $('#duration_container');

    //     if (!selectedTime || !roomId || !durationsByRoom[roomId] || !durationsByRoom[roomId][selectedTime]) return $durationContainer.hide();

    //     $durationContainer.show();
    //     durationsByRoom[roomId][selectedTime].forEach(d => $durationSelect.append(`<option value="${d.value}">${d.label}</option>`));
    // });

    $('#time_slot').change(function() {
        const roomId = $('#bk_room_id').val(),
            selectedTime = $(this).val(),
            $durationSelect = $('#duration_slot').html('<option value="">{{ __("common.select_duration") }}</option>'),
            $durationContainer = $('#duration_container'),
            $durationHidden = $('#duration_slot_hidden');

        if (selectedTime === 'full_day') {
            // Sembunyikan select, isi hidden, hilangkan required
            $durationContainer.hide();
            $durationSelect.prop('required', false);
            if (roomId && durationsByRoom[roomId] && durationsByRoom[roomId]['full_day']) {
                const fullDayDuration = durationsByRoom[roomId]['full_day'][0].value;
                $durationHidden.val(fullDayDuration);
            }
        } else if (!selectedTime || !roomId || !durationsByRoom[roomId] || !durationsByRoom[roomId][selectedTime]) {
            $durationContainer.hide();
            $durationSelect.prop('required', false);
            $durationHidden.val('');
        } else {
            $durationContainer.show();
            $durationSelect.prop('required', true);
            durationsByRoom[roomId][selectedTime].forEach(d => $durationSelect.append(`<option value="${d.value}">${d.label}</option>`));
            $durationSelect.val('');
            $durationHidden.val('');
        }
    });

    $('#duration_slot').change(function() {
        $('#duration_slot_hidden').val($(this).val());
    });

    // Initial load
    const initialRoomId = $('#bk_room_id').val();
    if (initialRoomId && timeSlotsByRoom[initialRoomId]) {
        const $timeSlot = $('#time_slot').empty().append('<option value="">{{ __("common.select_time") }}</option>');
        timeSlotsByRoom[initialRoomId].forEach(slot => $timeSlot.append(`<option value="${slot.value}">${slot.label}</option>`));
        const $durationSelect = $('#duration_slot').html('<option value="">{{ __("common.select_duration") }}</option>');
        if (durationsByRoom[initialRoomId] && durationsByRoom[initialRoomId]['full_day']) {
            durationsByRoom[initialRoomId]['full_day'].forEach(d => $durationSelect.append(`<option value="${d.value}">${d.label}</option>`));
        }
    }
});

$(document).on('click', 'a.dropdown-item[href$="/logout"]', function(e) {
    window.location.href = $(this).attr('href');
});
</script>
@endsection
