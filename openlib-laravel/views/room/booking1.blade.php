@extends('layouts/layoutMaster')

@section('title', __('rooms.booking_title'))

@section('vendor-style')
<link rel="stylesheet" type="text/css" href="../dist/css/style.min.css" />
@endsection

@section('page-style')
@endsection

@section('content')
<div class="card">
    {{-- Search --}}
    <div class="card-body">
        <form class="dt_adv_search" id="dateFilterForm">
            <div class="row">
                <div class="col-12 d-flex justify-content-between align-items-center mb-3 flex-wrap">
                    <div class="col-auto mb-2">
                        <button type="button" class="btn btn-danger d-flex align-items-center mb-5" id="btnPesanRuangan">
                            <i class="fa fa-plus-square me-2"></i> {{ __('rooms.booking_title') }}
                        </button>
                    </div>

                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        @php
                            $filteredLocations = $locations->filter(function($location) {
                                return in_array($location->id, [9, 15]); // hanya tampilkan lokasi dengan id 9 dan 15
                            });

                            // Set default location jika belum ada yang dipilih
                            if (!isset($selected_location) || !$filteredLocations->contains('id', $selected_location)) {
                                $selected_location = 9; // Default ke ID 9
                            }
                        @endphp

                        <div class="d-flex align-items-center mb-2">
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

                        <div class="d-flex align-items-center mb-2">
                            <label for="tanggal" class="form-label mb-0 me-2">{{ __('common.select_date') }}</label>
                            <input type="date" id="tanggal" name="date" class="form-select form-select-md"
                                value="{{ $selected_date ?? now()->toDateString() }}">
                        </div>
                        <div>
                            <button type="button" id="btnFilterSearch" class="btn btn-primary">{{ __('common.member.search') }}</button>
                        </div>
                    </div>

                </div>
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
            <h4 style="color: black;">Ketentuan Peminjaman Ruangan :</h4>
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

<div class="modal fade" id="pesanruanganModal" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="fa fa-plus-square me-2"></i>{{ __('rooms.booking_title') }}</div>
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
                                            data-room-hour="{{ $room->room_hour }}">
                                        {{ $room->room_name }} ( {{ $room->room_min }} - {{ $room->room_max }} {{ __('common.person') }} )
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row mb-4">
                        <label class="col-md-3 col-form-label">
                            {{ __('rooms.booking_input_phonenumber') }}
                        </label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="bk_mobile_phone" id="bk_mobile_phone" value="{{ session()->get('userData')->master_data_mobile_phone ?? '' }}" readonly style="border: none; background-color: #f0f0f0; outline: none; cursor: default;">
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
                            <select id="time_slot" name="time_slot" class="form-select form-select-lg" required style="font-size: 16px;">
                                <option value="">{{ __('common.select_time') }}</option>
                                <option value="full_day">1 {{ __('common.day') }}</option>
                                @for ($hour = 8; $hour <= 18; $hour++)
                                    @for ($minute = 0; $minute < 60; $minute += 30)
                                        <option value="{{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($minute, 2, '0', STR_PAD_LEFT) }}">
                                            {{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($minute, 2, '0', STR_PAD_LEFT) }}
                                        </option>
                                    @endfor
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="form-group row mb-4" id="duration_container" style="display: none;">
                        <label class="col-md-3 col-form-label">
                            {{ __('rooms.booking_input_duration') }} <span class="text-danger">*</span>
                        </label>
                        <div class="col-md-9">
                            <select id="duration_slot" name="duration_slot" class="form-select form-select-lg" required style="font-size: 16px;">
                                <option value="">{{ __('common.select_duration') }}</option>
                            </select>
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

<div class="modal fade" id="detailGallery" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i>Galeri<span id="galleryRoomName"></span></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner" id="imageCarouselInner">
                        <!-- Gambar akan dimasukkan di sini -->
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
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
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.min.js" type="text/javascript" language="javascript"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script type="text/javascript" src="../dist/js/jq.schedule.min.js"></script>
@endsection

@section('page-script')
<script>
    let dTable = null;
    let url = '{{ url('room/booking') }}';
    let debounceTimer;
    let selectedUsers = [];

    // Define operational hours in global scope
    const defaultHours = { start: 8, end: 19 };
    const ramadanHours = { start: 8, end: 14 };
    const locationHours = {
        9: { start: 8, end: 19 },  // Location ID 9
        15: { start: 8, end: 16 }  // Location ID 15
    };

    // Check if current date is during Ramadan period
    function checkRamadanPeriod() {
        const currentDate = new Date();
        const year = currentDate.getFullYear();
        const ramadanStart = new Date(`${year}-03-10`); // Adjust with actual Ramadan dates
        const ramadanEnd = new Date(`${year}-03-30`);   // Adjust with actual Ramadan dates
        return currentDate >= ramadanStart && currentDate <= ramadanEnd;
    }

    const isRamadan = checkRamadanPeriod();

    // Format hours for display (8 becomes "08:00")
    function formatTime(hour) {
        return hour < 10 ? `0${hour}:00` : `${hour}:00`;
    }

    function addLog(type, message) {
        const $log = $("<tr />");
        $log.append($("<th />").text(type));
        $log.append($("<td />").text(message ? JSON.stringify(message) : ""));
    }

    function resetForm() {
        $('#pesanruangan')[0].reset();
        selectedUsers = [];
        $('#selected-users').empty();
        $('#duration_slot').html('<option value="">{{ __("common.select_duration") }}</option>');
        $('#duration_container').hide();
    }

    function showBookingInfo(booking) {
        if (!booking) return;
        const bookingInfo = JSON.parse(booking);

        const formatDate = (dateString) => {
            if (dateString.includes('T')) {
                return new Date(dateString);
            }
            const [datePart, timePart] = dateString.split(' ');
            const [year, month, day] = datePart.split('-');
            const [hours, minutes] = timePart.split(':');
            return new Date(year, month-1, day, hours, minutes);
        };

        const startDate = formatDate(bookingInfo.bk_startdate);
        const endDate = formatDate(bookingInfo.bk_enddate);

        const date = startDate.toLocaleDateString('id-ID', {
            year: 'numeric', month: 'long', day: 'numeric'
        });
        const startTime = startDate.toLocaleTimeString('id-ID', {
            hour: '2-digit', minute: '2-digit'
        });
        const endTime = endDate.toLocaleTimeString('id-ID', {
            hour: '2-digit', minute: '2-digit'
        });

        $('#bookingModalLabel').text(`${date} ${startTime} - ${endTime}`);

        const statusBadge = {
            'Request': 'background-color: #ff9122; color: #fff;',
            'Approved': 'background-color: #319db5; color: #fff;',
            'Attend': 'background-color: #18a689; color: #fff;',
            'Cancel': 'background-color: #dc3545; color: #fff;',
            'Not Approved': 'background-color: #6c757d; color: #fff;'
        };

        const getStatusBadge = (status) => {
            const style = statusBadge[status] || 'background-color: #6c757d; color: #fff;';
            return `<span class="badge" style="${style}; padding: 0.5rem 1rem; font-size: 0.9rem;">${status}</span>`;
        };

        const participants = bookingInfo.bk_name
            ? bookingInfo.bk_name.split(',').map(name => `- ${name.trim()}`).join('<br>')
            : 'Tidak ada peserta';

        const modalContent = `
            <table class="w-100" style="border: none; border-collapse: collapse; font-size: 0.9rem; line-height: 2;">
                <tr style="border: none;">
                    <td style="width: 200px; font-weight: bold; vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">Nama</td>
                    <td style="width: 10px; vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">:</td>
                    <td style="vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">${bookingInfo.bk_username || 'Tidak ada nama'}</td>
                </tr>
                <tr style="border: none;">
                    <td style="width: 200px; font-weight: bold; vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">Status</td>
                    <td style="width: 10px; vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">:</td>
                    <td style="vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">
                        ${getStatusBadge(bookingInfo.bk_status)}
                    </td>
                </tr>
                <tr style="border: none;">
                    <td style="width: 200px; font-weight: bold; vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">Nama Anggota</td>
                    <td style="width: 10px; vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">:</td>
                    <td style="vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">
                        ${participants}
                    </td>
                </tr>
                <tr style="border: none;">
                    <td style="width: 200px; font-weight: bold; vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">Tujuan</td>
                    <td style="width: 10px; vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">:</td>
                    <td style="vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">${bookingInfo.bk_purpose || 'Tidak ada tujuan'}</td>
                </tr>
            </table>
        `;
        $('#bookingInfo').html(modalContent);
        $('#bookingModal').modal('show');
    }

    function save() {
        if (!$("#pesanruangan")[0].checkValidity()) {
            $("#pesanruangan")[0].reportValidity();
            return;
        }

        const startDate = $('#bk_startdate').val();
        const selectedTime = $('#time_slot').val();
        const duration = $('#duration_slot').val();
        const selectedRoom = $('#bk_room_id option:selected');
        const roomMax = selectedRoom.data('room-max');
        const locationId = $('#lokasi').val();

        if (!duration) {
            toastr.error("Silakan pilih durasi.", "", toastrOptions);
            return;
        }

        const totalMembers = selectedUsers.length;
        if (totalMembers > roomMax) {
            toastr.error(`Jumlah anggota (${totalMembers}) melebihi kapasitas ruangan (${roomMax}).`, "", toastrOptions);
            return;
        }

        // Get operational hours based on location
        let operationalHours = locationHours[locationId] || defaultHours;
        if (isRamadan) {
            operationalHours = ramadanHours;
        }

        let endDateTime;
        if (selectedTime === "full_day") {
            // For full day, use the operational end time
            endDateTime = new Date(startDate + 'T' + formatTime(operationalHours.end));
        } else {
            const startDateTime = new Date(startDate + 'T' + selectedTime);
            endDateTime = new Date(startDateTime.getTime() + (duration * 60000));
        }

        const endDateFormatted = endDateTime.toISOString().slice(0, 19).replace('T', ' ');
        $('#bk_enddate').val(endDateFormatted);
        $('#bk_name').val(selectedUsers.join(','));

        $.ajax({
            url: url + '/save',
            type: 'POST',
            data: $('#pesanruangan').serialize(),
            success: function(data) {
                if (data.status == 'success') {
                    $('#pesanruanganModal').modal('hide');
                    toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions);
                    location.reload();
                } else {
                    toastr.error(data.message, "", toastrOptions);
                }
            },
            error: function(xhr) {
                if (xhr.status === 400) {
                    toastr.error('Ruangan sudah dibooking pada waktu tersebut. Silakan pilih waktu lain.', "", toastrOptions);
                } else {
                    const errors = xhr.responseJSON.errors;
                    const errorMessages = [];
                    for (let key in errors) {
                        errorMessages.push(errors[key][0]);
                    }
                    toastr.error(errorMessages.join(', '), "", toastrOptions);
                }
            }
        });
    }

    function showGallery(images) {
        const $carouselInner = $('#imageCarouselInner');
        $carouselInner.empty();

        try {
            if (typeof images === 'string') {
                images = JSON.parse(images);
            }

            if (Array.isArray(images) && images.length > 0) {
                images.forEach((image, index) => {
                    $carouselInner.append(`
                        <div class="carousel-item ${index === 0 ? 'active' : ''}">
                            <img src="${image.startsWith('http') ? image : `/${image.trim()}`}"
                                 alt="Gambar ${index + 1}"
                                 class="d-block w-100"
                                 style="max-height: 70vh; object-fit: contain; margin: 0 auto;">
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

    $(function() {
        const scheduleDataFromServer = {!! $scheduleData !!};
        const selectedLocation = $('#lokasi').val() || '9'; // Default ke ID 9 jika tidak ada lokasi yang dipilih

        // Determine the operational hours based on location and Ramadan status
        let operationalHours = locationHours[selectedLocation] || defaultHours;
        if (isRamadan) {
            operationalHours = ramadanHours;
        }

        // Initialize the schedule with dynamic operational hours
        const $sc = $("#schedule").timeSchedule({
            startTime: formatTime(operationalHours.start),
            endTime: formatTime(operationalHours.end),
            widthTime: 60 * 10,
            timeLineY: 60,
            verticalScrollbar: 20,
            timeLineBorder: 2,
            bundleMoveWidth: 6,
            draggable: false,
            resizable: false,
            resizableLeft: false,
            rows: scheduleDataFromServer,
            onChange: function(node, data) { addLog("onChange", data); },
            onInitRow: function(node, data) { addLog("onInitRow", data); },
            onClick: function(node, data) { addLog("onClick", data); },
            onAppendRow: function(node, data) { addLog("onAppendRow", data); },
            onAppendSchedule: function(node, data) {
                addLog("onAppendSchedule", data);
                node.css({ 'opacity': '1', 'cursor': 'default' });

                if (data.data.status) {
                    const statusClass = "sc_bar_status_" + data.data.status.toLowerCase().replace(' ', '_');
                    node.addClass(statusClass);
                }

                if (data.data.image) {
                    const $img = $('<div class="photo"><img></div>');
                    $img.find("img").attr("src", data.data.image);
                    node.prepend($img);
                    node.addClass("sc_bar_photo");
                }

                node.on('click', function() {
                    showBookingInfo(JSON.stringify({
                        bk_username: data.data.username,
                        bk_name: data.data.name,
                        bk_status: data.data.status,
                        bk_purpose: data.data.purpose,
                        bk_startdate: data.data.startdate,
                        bk_enddate: data.data.enddate
                    }));
                });
            }
        });

        // Initialize logs
        $("#logs").append('<table class="table">');

        // Event Handlers
        $('#btnPesanRuangan').on('click', function() {
            $('#pesanruanganModal').modal('show');
        });

        $('#pesanruanganModal').on('hidden.bs.modal', resetForm);

        $('#lokasi').on('change', function() {
            const locationId = $(this).val();
            if (!locationId) return; // Pastikan ada lokasi yang dipilih

            const currentDate = $('#tanggal').val();
            let params = [`location=${locationId}`];
            if (currentDate) params.push(`date=${currentDate}`);

            window.location.href = `{{ url('room/booking') }}?${params.join('&')}`;
        });

        $('#btnFilterSearch').on('click', function() {
            const selectedDate = $('#tanggal').val();
            const currentLocation = $('#lokasi').val();

            if (!currentLocation) {
                toastr.error("Silakan pilih lokasi.", "", toastrOptions);
                return;
            }

            let params = [`location=${currentLocation}`];
            if (selectedDate) params.push(`date=${selectedDate}`);

            window.location.href = `{{ url('room/booking') }}?${params.join('&')}`;
        });

        // Member Search Functionality
        $('#bk_name').on('input', function() {
            clearTimeout(debounceTimer);
            const query = $(this).val();

            debounceTimer = setTimeout(function() {
                if (query.length < 5 || query.length > 10) {
                    $('#search-results').hide();
                    return;
                }

                $('#search-results').html('<div class="loading">Loading...</div>').show();

                $.ajax({
                    url: url + '/search',
                    method: 'GET',
                    data: { search: query },
                    dataType: 'json',
                    success: function(data) {
                        let results = '';
                        if (data.length > 0) {
                            data.forEach(item => {
                                results += `<a href="#" class="dropdown-item" data-id="${item.master_data_user}" data-fullname="${item.master_data_fullname}">
                                            <div>${item.master_data_number} - ${item.master_data_user} - ${item.master_data_fullname}</div>
                                            </a>`;
                            });
                        } else {
                            results = '<div class="dropdown-item">Tidak ada hasil ditemukan</div>';
                        }
                        $('#search-results').html(results).show();
                    },
                    error: function() {
                        $('#search-results').html('<div class="dropdown-item">Terjadi kesalahan saat mencari data</div>').show();
                    }
                });
            }, 300);
        });

        $(document)
            .on('click', '.dropdown-item', function(e) {
                e.preventDefault();
                const selectedUser = $(this).data('id');
                const selectedFullname = $(this).data('fullname');

                if (!selectedUsers.includes(selectedFullname)) {
                    selectedUsers.push(selectedFullname);
                    $('#selected-users').append(
                        `<span class="custom-badge selected-user" data-id="${selectedFullname}">
                            ${selectedFullname}
                            <button type="button" class="close remove-user" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </span>`
                    );
                }

                $('#bk_name').val('');
                $('#search-results').hide();
            })
            .on('click', '.remove-user', function() {
                const userId = $(this).parent().data('id');
                selectedUsers = selectedUsers.filter(user => user !== userId);
                $(this).parent().remove();
            })
            .on('click', function(e) {
                if (!$(e.target).closest('#bk_name').length) {
                    $('#search-results').hide();
                }
            })
            .on('click', '.view-gallery', function(e) {
                e.preventDefault();
                const rgImage = $(this).data('rg-image');
                const roomName = $(this).closest('.fc-daygrid-event').find('.fc-event-title').text().split('(')[0].trim();
                $('#galleryRoomName').text(roomName);
                showGallery(rgImage);
            });

        function updateTimeSlots(locationId) {
            const $timeSlot = $('#time_slot');
            $timeSlot.empty();
            $timeSlot.append('<option value="">{{ __('common.select_time') }}</option>');

            // Get operational hours based on location
            let operationalHours = locationHours[locationId] || defaultHours;
            if (isRamadan) {
                operationalHours = ramadanHours;
            }

            // Add full day option with location-specific hours
            const fullDayLabel = `1 Hari (${formatTime(operationalHours.start)} - ${formatTime(operationalHours.end)})`;
            $timeSlot.append(`<option value="full_day">${fullDayLabel}</option>`);

            // Add time slots based on operational hours
            for (let hour = operationalHours.start; hour < operationalHours.end; hour++) {
                for (let minute = 0; minute < 60; minute += 30) {
                    const time = `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
                    $timeSlot.append(`<option value="${time}">${time}</option>`);
                }
            }
        }

        $('#lokasi').on('change', function() {
            const locationId = $(this).val();
            if (!locationId) return; // Pastikan ada lokasi yang dipilih

            updateTimeSlots(locationId);

            // Reset duration slot when location changes
            $('#duration_slot').html('<option value="">{{ __("common.select_duration") }}</option>');
            $('#duration_container').hide();
        });

        $('#time_slot').on('change', function() {
            const selectedTime = $(this).val();
            const $durationSelect = $('#duration_slot');
            const $durationContainer = $('#duration_container');
            const selectedRoom = $('#bk_room_id option:selected');
            const roomHour = selectedRoom.data('room-hour');
            const locationId = $('#lokasi').val();

            $durationSelect.html('<option value="">{{ __("common.select_duration") }}</option>');

            if (!selectedTime) {
                $durationContainer.hide();
                return;
            }

            $durationContainer.show();

            let operationalHours = locationHours[locationId] || defaultHours;
            if (isRamadan) {
                operationalHours = ramadanHours;
            }

            const closingHour = operationalHours.end;

            if (selectedTime === "full_day") {
                // Full day booking - set duration to cover operational hours
                const durationMinutes = (closingHour - operationalHours.start) * 60;
                const fullDayLabel = `1 Hari (${formatTime(operationalHours.start)} - ${formatTime(operationalHours.end)})`;
                $durationSelect.append(`<option value="${durationMinutes}">${fullDayLabel}</option>`);
            } else if (roomHour) {
                const [hour, minute] = selectedTime.split(':').map(Number);
                const roomHourInMinutes = roomHour * 60;
                const closingTimeInMinutes = closingHour * 60;
                const currentTimeInMinutes = hour * 60 + minute;
                const availableTime = closingTimeInMinutes - currentTimeInMinutes;
                const adjustedRoomHourInMinutes = Math.min(roomHourInMinutes, availableTime);

                $durationSelect.append(`<option value="${adjustedRoomHourInMinutes}">${adjustedRoomHourInMinutes} menit</option>`);
            } else {
                const [hour, minute] = selectedTime.split(':').map(Number);
                let durations = [];

                // Adjust durations based on operational hours
                if (hour < closingHour - 2 || (hour === closingHour - 2 && minute === 0)) {
                    durations = [30, 60, 90, 120];
                } else if (hour === closingHour - 2 && minute === 30) {
                    durations = [30, 60, 90];
                } else if (hour === closingHour - 1 && minute === 0) {
                    durations = [30, 60];
                } else if (hour === closingHour - 1 && minute === 30) {
                    durations = [30];
                }

                durations.forEach(duration => {
                    $durationSelect.append(`<option value="${duration}">${duration} menit</option>`);
                });
            }
        });

        // Initial setup
        const initialLocationId = $('#lokasi').val() || '9'; // Default ke ID 9
        updateTimeSlots(initialLocationId);
    });
</script>
@endsection
