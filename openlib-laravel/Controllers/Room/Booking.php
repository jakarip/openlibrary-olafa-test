<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use App\Models\Room\BookingModel;
use App\Models\Room\RoomGalleryModel;
use App\Models\Room\RoomHourModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Booking extends Controller
{

    public function index(Request $request)
    {
        if(!auth()->can('room-booking.view')){
            return redirect('/home');
        }

        $roomHours = RoomHourModel::where('rh_status', 1)->get();
        $operationalHours = [];
        foreach ($roomHours as $hour) {
            $operationalHours[$hour->rh_id_location] = [
                'start' => $hour->rh_starthour,
                'end' => $hour->rh_endhour
            ];
        }

        $termconditions = DB::connection('mysql')->table('room.term_conditions')->select('id', 'information', 'term_sequence', 'information_en')->get();
        $rooms = DB::connection('mysql')->table('room.room')
            ->select('room_id', 'room_name', 'room_max', 'room_min', 'room_id_location', 'room_hour', 'room_active', 'room_price')
            ->where('room_active', '!=', 1)
            ->get();

        $galleries = DB::connection('mysql')
            ->table('room.room_gallery')
            ->select('rg_id', 'rg_room_id', 'rg_image')
            ->get();

        // Format galleries by room
        $galleriesByRoom = [];
        foreach ($galleries as $gallery) {
            $images = array_map(function($img) {
                $img = ltrim($img);
                if (!str_starts_with($img, 'storage/')) {
                    $img = 'storage/' . ltrim($img, '/');
                }
                return asset($img);
            }, explode(',', $gallery->rg_image));
            $galleriesByRoom[$gallery->rg_room_id][] = [
                'rg_id' => $gallery->rg_id,
                'images' => $images
            ];
        }

        $locations = DB::connection('mysql')->table('item_location')->get();

        $locationId = $request->input('location');
        if (!$locationId) {
            $locationId = 9;
        }
        $date = $request->input('date') ?? now()->toDateString();

        $bookingModel = new BookingModel();
        $bookingsQuery = $bookingModel->newQuery();

        if ($locationId) {
            $rooms = $rooms->where('room_id_location', $locationId);
            $roomIds = $rooms->pluck('room_id')->toArray();
            $bookingsQuery->whereIn('bk_room_id', $roomIds)
                ->whereDate('bk_startdate', $date)
                ->whereNotIn('bk_status', ['Cancel', 'Not Attend', 'Not Approved']);
        } else {
            $bookingsQuery->whereDate('bk_startdate', $date)
                ->whereNotIn('bk_status', ['Cancel', 'Not Attend', 'Not Approved']);
        }

        $bookings = $bookingsQuery->get();

        // Format data untuk schedule
        $scheduleData = [];
        foreach ($rooms as $index => $room) {
            $roomBookings = $bookings->where('bk_room_id', $room->room_id);

            $schedules = [];
            foreach ($roomBookings as $booking) {
                $schedules[] = [
                    'start' => date('H:i', strtotime($booking->bk_startdate)),
                    'end' => date('H:i', strtotime($booking->bk_enddate)),
                    'text' => $booking->bk_status,
                    'data' => [
                        'id' => $booking->bk_id,
                        'status' => $booking->bk_status,
                        'username' => $booking->bk_username,
                        'name' => $booking->bk_name,
                        'purpose' => $booking->bk_purpose,
                        'startdate' => $booking->bk_startdate,
                        'enddate' => $booking->bk_enddate
                    ],
                ];
            }

            // Get gallery for this room
            $roomGalleries = $galleriesByRoom[$room->room_id] ?? [];
            $galleryLinks = [];

            if (!empty($roomGalleries)) {
                foreach ($roomGalleries as $gallery) {
                    $galleryLinks[] = '<a href="#" class="view-gallery" style="color: #2b2b2b;" data-rg-id="' . $gallery['rg_id'] .
                        '" data-rg-room-id="' . $room->room_id .
                        '" data-rg-image=\'' . json_encode($gallery['images']) .
                        '\' data-room-name="' . htmlspecialchars($room->room_name, ENT_QUOTES) . // Tambahkan ini
                        '">' . __('rooms.booking_see_gallery') . '</a>';
                }
            }

            // Title logic sesuai permintaan
            if (!empty($room->room_price) && !empty($room->room_hour)) {
                $title = $room->room_name . ' (Rp' . number_format($room->room_price, 0, ',', '.') . ' / ' . $room->room_hour . ' ' . __('common.hour') . ') ';
            } else {
                $title = $room->room_name . ' (Maks: +- ' . $room->room_max . ' ' . __('common.person') . ') ';
            }
            if (count($galleryLinks) > 0) {
                $title .= implode(' | ', $galleryLinks);
            } else {
                $title .= '<span class="text-muted"></span>';
            }

            $scheduleData[$index] = [
                'title' => $title,
                'schedule' => $schedules
            ];
        }

        [$timeSlotsByRoom, $durationsByRoom] = $this->getTimeSlotsAndDurations($rooms, $operationalHours);

        return view('room.booking', [
            'termconditions' => $termconditions,
            'rooms' => $rooms,
            'galleriesByRoom' => $galleriesByRoom,
            'bookings' => $bookings,
            'locations' => $locations,
            'selected_location' => $locationId,
            'selected_date' => $date,
            'scheduleData' => json_encode($scheduleData),
            'operationalHours' => $operationalHours,
            'timeSlotsByRoom' => $timeSlotsByRoom,
            'durationsByRoom' => $durationsByRoom,
        ]);
    }

    public function getbyid(Request $request)
    {
        return BookingModel::find($request->id)->toJson();
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $data = (new BookingModel())->searchMembers($search);

        return response()->json($data);
    }

    public function save(Request $request)
    {
        $rules = [
            'bk_room_id' => 'required|integer',
            'bk_purpose' => 'required|string',
            'bk_name' => 'nullable|string',
            'bk_startdate' => 'required|date',
            'time_slot' => 'required|string',
        ];
        if ($request->input('time_slot') !== 'full_day') {
            $rules['duration_slot'] = 'required|integer';
        }
        $request->validate($rules);

        Log::info('Booking data: ', $request->all());

        try {
            if (!auth()->can('room-booking.max2request')) {
                $userId = session()->get('userData')->master_data_user;

                $now = now();
                $notAttendCount = BookingModel::where('bk_username', $userId)
                    ->where('bk_status', 'Not Attend')
                    ->whereMonth('bk_startdate', $now->month)
                    ->whereYear('bk_startdate', $now->year)
                    ->count();
                if ($notAttendCount >= 2) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Anda tidak dapat melakukan peminjaman pada bulan ini, dikarenakan anda telah 2x melakukan peminjaman tetapi tidak hadir pada hari peminjaman.'
                    ], 400);
                }

                $countRequest = BookingModel::where('bk_username', $userId)
                    ->where('bk_status', 'Request')
                    ->count();
                if ($countRequest >= 2) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Anda sudah melebihi jumlah permintaan peminjaman yang diperbolehkan. Maksimal hanya diperbolehkan 2x permintaan peminjaman ruangan. Silahkan menunggu admin untuk melakukan proses pada request jadwal peminjaman anda yang sebelumnya'
                    ], 400);
                }
            }

            $startDateTime = new \Carbon\Carbon($request->bk_startdate);
            $endDateTime = null;

            $room = DB::connection('mysql')->table('room.room')
                ->where('room_id', $request->bk_room_id)
                ->first();

            $startHour = '08:00:00';
            $endHour = '17:00:00';

            if ($room) {
                $roomHour = DB::connection('mysql')->table('room.room_hour')
                    ->where('rh_id_location', $room->room_id_location)
                    ->where('rh_status', 1)
                    ->first();

                if ($roomHour) {
                    $startHour = $roomHour->rh_starthour;
                    $endHour = $roomHour->rh_endhour;
                }
            }

            if ($request->time_slot === 'full_day') {
                // Set waktu mulai dan selesai sesuai jam operasional
                [$startH, $startM] = explode(':', $startHour);
                [$endH, $endM] = explode(':', $endHour);
                $startDateTime->setTime($startH, $startM);
                $endDateTime = $startDateTime->copy()->setTime($endH, $endM);

                // Hitung durasi full_day dalam menit dan set ke request
                $durationMinutes = ($endH * 60 + $endM) - ($startH * 60 + $startM);
                $request->merge(['duration_slot' => $durationMinutes]);
            } else {
                $timeSlot = $request->time_slot;
                $startDateTime->setTimeFromTimeString($timeSlot);
                $durationMinutes = (int)$request->duration_slot;
                $endDateTime = $startDateTime->copy()->addMinutes($durationMinutes);
            }

            $existingBooking = BookingModel::where('bk_room_id', $request->bk_room_id)
                ->whereNotIn('bk_status', ['Cancel', 'Not Attend', 'Not Approved'])
                ->where(function ($query) use ($startDateTime, $endDateTime) {
                    $query->whereBetween('bk_startdate', [$startDateTime, $endDateTime])
                        ->orWhereBetween('bk_enddate', [$startDateTime, $endDateTime])
                        ->orWhere(function ($query) use ($startDateTime, $endDateTime) {
                            $query->where('bk_startdate', '<', $startDateTime)
                                ->where('bk_enddate', '>', $endDateTime);
                        });
                })
                ->first();

            if ($existingBooking) {
                $existingEndDateTime = \Carbon\Carbon::parse($existingBooking->bk_enddate);
                if (!$existingEndDateTime->eq($startDateTime)) {
                    return response()->json(['status' => 'error', 'message' => 'Ruangan sudah dibooking pada waktu tersebut.'], 400);
                }
            }

            $booking = new BookingModel();
            $booking->fill($request->all());

            $booking->bk_mobile_phone = session()->get('userData')->master_data_mobile_phone;
            $names = explode(',', $request->bk_name);
            $booking->bk_total = count($names);
            $booking->bk_status = 'Request';
            $booking->bk_startdate = $startDateTime;
            $booking->bk_enddate = $endDateTime;
            $booking->bk_username = session()->get('userData')->master_data_user;
            $booking->bk_memberid = session()->get('userData')->id;
            $booking->bk_createdby = session()->get('userData')->master_data_user;
            $booking->bk_createdate = now();
            $booking->save();

            $startDate = date('d-m-Y', strtotime($booking->bk_startdate));
            $startTime = date('H:i', strtotime($booking->bk_startdate));
            $endTime = date('H:i', strtotime($booking->bk_enddate));
            $roomName = $room ? $room->room_name : '';

            // Notifikasi ke Member
            // $messages = "Anda telah melakukan permintaan peminjaman ruangan " . $roomName . ", Tanggal : " . $startDate . ", Pada jam : " . $startTime . " - " . $endTime . ". Akan dikonfirmasi jika telah diproses.";
            $messages = __('rooms.booking_notification_success', [
                'roomName' => $roomName,
                'startDate' => $startDate,
                'startTime' => $startTime,
                'endTime' => $endTime,
            ]);

            $itemnotif = [
                'notif_id_member' => $booking->bk_username,
                'notif_type' => 'ruangan',
                'notif_content' => $messages,
                'notif_date' => now(),
                'notif_status' => 'unread',
                'notif_id_detail' => $booking->bk_id,
            ];

            DB::table('notification_mobile')->insert($itemnotif);

            // Notifikasi ke Admin (hanya insert ke DB, tanpa push)
            // $adminIds = ['12186', '123126', '109765'];
            // foreach ($adminIds as $adminId) {
            //     DB::table('notification_mobile')->insert([
            //         'notif_id_member' => $adminId,
            //         'notif_type' => 'ruangan',
            //         'notif_content' => $booking->bk_username . ' Request Peminjaman Ruangan',
            //         'notif_date' => now(),
            //         'notif_status' => 'unread',
            //         'notif_id_detail' => $booking->bk_id,
            //     ]);
            // }

            return response()->json([
                'status' => 'success',
                'message' => 'Success to save data',
                'notif_message' => $messages
            ]);
        } catch (\Throwable $th) {
            Log::error('Error saving booking: ' . $th->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to save data: ' . $th->getMessage()], 500);
        }
    }

    private function getTimeSlotsAndDurations($rooms, $operationalHours)
    {
        $timeSlotsByRoom = [];
        $durationsByRoom = [];
        $canFullDay = auth()->can('room-booking.full_day'); // Tambahkan ini

        foreach ($rooms as $room) {
            $locationId = $room->room_id_location;
            $hours = $operationalHours[$locationId] ?? ['start' => '08:00:00', 'end' => '17:00:00'];
            $startHour = (int)substr($hours['start'], 0, 2);
            $startMinute = (int)substr($hours['start'], 3, 2);
            $endHour = (int)substr($hours['end'], 0, 2);
            $endMinute = (int)substr($hours['end'], 3, 2);

            $slots = [];
            $fullDayLabel = "1 " . __('common.day') . " (" . substr($hours['start'], 0, 5) . " - " . substr($hours['end'], 0, 5) . ")";
            if ($canFullDay) { // Hanya tambahkan full_day jika punya akses
                $slots[] = [
                    'value' => 'full_day',
                    'label' => $fullDayLabel
                ];
            }

            if ($room->room_price && $room->room_hour) {
                $roomHourInMinutes = $room->room_hour * 60;
                $closingTimeInMinutes = $endHour * 60 + $endMinute;
                $openingTimeInMinutes = $startHour * 60 + $startMinute;
                for (
                    $time = $openingTimeInMinutes;
                    $time <= $closingTimeInMinutes - $roomHourInMinutes;
                    $time += 30
                ) {
                    $hour = floor($time / 60);
                    $minute = $time % 60;
                    $timeStr = sprintf('%02d:%02d', $hour, $minute);
                    $slots[] = [
                        'value' => $timeStr,
                        'label' => $timeStr
                    ];
                }
            } else {
                for ($hour = $startHour; $hour < $endHour; $hour++) {
                    for ($minute = 0; $minute < 60; $minute += 30) {
                        $time = sprintf('%02d:%02d', $hour, $minute);
                        $slots[] = [
                            'value' => $time,
                            'label' => $time
                        ];
                    }
                }
            }
            $timeSlotsByRoom[$room->room_id] = $slots;

            // Durasi
            $durations = [];
            // Full day
            $start = $startHour * 60 + $startMinute;
            $end = $endHour * 60 + $endMinute;
            $durationMinutes = $end - $start;
            if ($canFullDay) { // Hanya tambahkan full_day jika punya akses
                $durations['full_day'] = [
                    [
                        'value' => $durationMinutes,
                        'label' => $fullDayLabel
                    ]
                ];
            }

            // Per slot
            if ($room->room_price && $room->room_hour) {
                $durationMinutes = $room->room_hour * 60;
                $jam = floor($durationMinutes / 60);
                $menit = $durationMinutes % 60;
                if ($jam > 0 && $menit > 0) {
                    $label = "$jam jam $menit " . __('common.minute') . " / $durationMinutes " . __('common.minute') . "";
                } elseif ($jam > 0) {
                    $label = "$jam jam / $durationMinutes " . __('common.minute') . "";
                } else {
                    $label = "$durationMinutes " . __('common.minute') . "";
                }
                foreach ($slots as $slot) {
                    if ($slot['value'] !== 'full_day') {
                        $durations[$slot['value']] = [
                            [
                                'value' => $durationMinutes,
                                'label' => $label
                            ]
                        ];
                    }
                }
            } else {
                foreach ($slots as $slot) {
                    if ($slot['value'] === 'full_day') continue;
                    [$hour, $minute] = explode(':', $slot['value']);
                    $currentTimeInMinutes = $hour * 60 + $minute;
                    $availableTime = $end - $currentTimeInMinutes;
                    $slotDurations = [];
                    if ($availableTime >= 120) {
                        $slotDurations = [30, 60, 90, 120];
                    } elseif ($availableTime >= 90) {
                        $slotDurations = [30, 60, 90];
                    } elseif ($availableTime >= 60) {
                        $slotDurations = [30, 60];
                    } elseif ($availableTime >= 30) {
                        $slotDurations = [30];
                    }
                    foreach ($slotDurations as $d) {
                        $slotDurationsArr[] = [
                            'value' => $d,
                            'label' => "$d " . __('common.minute') . ""
                        ];
                    }
                    $durations[$slot['value']] = $slotDurationsArr ?? [];
                    unset($slotDurationsArr);
                }
            }
            $durationsByRoom[$room->room_id] = $durations;
        }
        return [$timeSlotsByRoom, $durationsByRoom];
    }

    public function bookingInfoHtml(Request $request)
    {
        $bookingId = $request->input('id');
        $booking = \App\Models\Room\BookingModel::find($bookingId);

        if (!$booking) {
            return response()->json(['status' => 'error', 'html' => '<div>Data booking tidak ditemukan.</div>']);
        }

        // Format tanggal dan jam
        $startDate = \Carbon\Carbon::parse($booking->bk_startdate);
        $endDate = \Carbon\Carbon::parse($booking->bk_enddate);

        $date = $startDate->translatedFormat('d F Y');
        $startTime = $startDate->format('H:i');
        $endTime = $endDate->format('H:i');

        // Status badge
        $statusBadge = [
            'Request' => 'background-color: #ff9122; color: #fff;',
            'Approved' => 'background-color: #319db5; color: #fff;',
            'Attend' => 'background-color: #18a689; color: #fff;'
        ];
        $status = $booking->bk_status;
        $badgeStyle = isset($statusBadge[$status]) ? $statusBadge[$status] : 'background-color: #6c757d; color: #fff;';
        $badgeHtml = '<span class="badge" style="'.$badgeStyle.'; padding: 0.5rem 1rem; font-size: 0.9rem;">'.$status.'</span>';

        // Peserta
        $participants = $booking->bk_name
            ? collect(explode(',', $booking->bk_name))->map(function($n) { return '- ' . trim($n); })->implode('<br>')
            : '' . __('rooms.booking_info_no_participants') . '';

        $labelName = __('rooms.booking_info_name');
        $labelStatus = __('rooms.booking_info_status');
        $labelMembers = __('rooms.booking_info_member_name');
        $labelPurpose = __('rooms.booking_info_purpose');

        // Siapkan variabel untuk heredoc
        $bk_username = $booking->bk_username ? $booking->bk_username : 'Tidak ada nama';
        $bk_purpose = $booking->bk_purpose ? $booking->bk_purpose : 'Tidak ada tujuan';

        $html = <<<HTML
            <table class="w-100" style="border: none; border-collapse: collapse; font-size: 0.9rem; line-height: 2;">
                <tr style="border: none;">
                    <td style="width: 200px; font-weight: bold; vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">{$labelName}</td>
                    <td style="width: 10px; vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">:</td>
                    <td style="vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">{$bk_username}</td>
                </tr>
                <tr style="border: none;">
                    <td style="width: 200px; font-weight: bold; vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">{$labelStatus}</td>
                    <td style="width: 10px; vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">:</td>
                    <td style="vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">
                        {$badgeHtml}
                    </td>
                </tr>
                <tr style="border: none;">
                    <td style="width: 200px; font-weight: bold; vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">{$labelMembers}</td>
                    <td style="width: 10px; vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">:</td>
                    <td style="vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">
                        {$participants}
                    </td>
                </tr>
                <tr style="border: none;">
                    <td style="width: 200px; font-weight: bold; vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">{$labelPurpose}</td>
                    <td style="width: 10px; vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">:</td>
                    <td style="vertical-align: middle; text-align: left; border: none; font-size: 0.9rem;">{$bk_purpose}</td>
                </tr>
            </table>
        HTML;

        // Untuk judul modal
        $modalTitle = "{$date} {$startTime} - {$endTime}";

        return response()->json([
            'status' => 'success',
            'html' => $html,
            'modalTitle' => $modalTitle
        ]);
    }
}
