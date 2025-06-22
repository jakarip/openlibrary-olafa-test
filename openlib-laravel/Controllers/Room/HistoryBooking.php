<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use App\Models\Room\HistoryBookingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;

class HistoryBooking extends Controller
{
    public function index()
    {
        if(!auth()->can('room-historybooking.view')){
            return redirect('/home');
        }

        $locations = DB::connection('mysql')->table('item_location')->get();
        $selectedLocationId = 9;

        return view('room.historybooking', [
            'locations' => $locations,
            'selectedLocationId' => $selectedLocationId
        ]);
    }

    public function dt(Request $request)
    {
        $locationId = $request->input('location');

        $user = auth()->user();
        $memberClassId = $user->member_class_id ?? null;
        $memberId = $user->id ?? null;

        $viewAllStatus = auth()->can('room-historybooking.viewstatus');

        $data = (new HistoryBookingModel())->getHistoryBookings($locationId, $memberClassId, $memberId, $viewAllStatus);

        return datatables($data)
            ->addColumn('bk_total', function ($db) {
                if(auth()->can('room-historybooking.view')) {
                    return $db->bk_total . ' ' . __('common.person');
                }
                return '<span class="text-muted">0 ' . __('common.person') . '</span>';
            })
            ->addColumn('bk_status', function ($db) {
                if(auth()->can('room-historybooking.view')) {
                    $status = strtolower($db->bk_status);
                    $badgeClass = 'bg-warning';
                    if ($status === 'attend') {
                        $badgeClass = 'btn-success';
                    } elseif ($status === 'approved') {
                        // $badgeClass = 'btn-primary';
                        $badgeClass = '';
                        return '<span class="badge" style="background-color:#319db5;color:#fff;">' . $db->bk_status . '</span>';
                    } elseif ($status === 'not approved') {
                        $badgeClass = 'btn-secondary';
                    } elseif ($status === 'cancel') {
                        $badgeClass = 'btn-light';
                    } elseif ($status === 'not attend') {
                        $badgeClass = 'btn-danger';
                    }
                }
                return '<span class="badge ' . $badgeClass . '">' . $db->bk_status . '</span>';
            })
            ->addColumn('action', function ($db) {
                $btn = '';
                if ($db->bk_status == 'Request' && auth()->canAtLeast(['room-historybooking.approve', 'room-historybooking.notApprove', 'room-historybooking.cancel'])) {
                    $btn .= '<div class="btn-group-vertical">';
                    if(auth()->can('room-historybooking.approve')) {
                        $btn .= '<button type="button" class="btn btn-sm text-center mb-1" style="font-size: 10px; background-color: #18a689; color: #fff; border: none;" title="approve" onclick="status(\'' . $db->bk_status . '\', \'approve\', ' . $db->bk_id . ')">' . __('common.approved') . '</button>';
                    }
                    if(auth()->can('room-historybooking.notApprove')) {
                        $btn .= '<button type="button" class="btn btn-sm text-center mb-1" style="font-size: 10px; background-color: #b066ff; color: #fff; border: none;" title="notApprove" onclick="status(\'' . $db->bk_status . '\', \'notApprove\', ' . $db->bk_id . ')">' . __('common.not_approved') . '</button>';
                    }
                    if(auth()->can('room-historybooking.cancel')) {
                        $btn .= '<button type="button" class="btn btn-sm text-center" style="font-size: 10px; background-color: #e0e6eb; color: #222; border: none;" title="cancel" onclick="status(\'' . $db->bk_status . '\', \'cancel\', ' . $db->bk_id . ')">' . __('common.cancel') . '</button>';
                    }
                    $btn .= '</div>';
                }
                else if ($db->bk_status == 'Approved' && auth()->canAtLeast(['room-historybooking.attend', 'room-historybooking.notAttend'])) {
                    $btn .= '<div class="btn-group-vertical">';
                    if(auth()->can('room-historybooking.attend')) {
                        $btn .= '<button type="button" class="btn btn-sm text-center mb-1" style="font-size: 10px; background-color: #18a689; color: #fff; border: none;" title="attend" onclick="status(\'' . $db->bk_status . '\', \'attend\', ' . $db->bk_id . ')">Attend</button>';
                    }
                    if(auth()->can('room-historybooking.notAttend')) {
                        $btn .= '<button type="button" class="btn btn-sm text-center mb-1" style="font-size: 10px; background-color: #c75757; color: #fff; border: none;" title="notAttend" onclick="status(\'' . $db->bk_status . '\', \'notAttend\', ' . $db->bk_id . ')">Not Attend</button>';
                    }
                    $btn .= '</div>';
                }
                return $btn;
            })
            ->rawColumns(['action', 'bk_total', 'bk_status'])->toJson();
    }

    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'bk_status' => 'required|string',
            'bk_reason' => 'nullable|string',
        ]);

        $booking = HistoryBookingModel::find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $booking->bk_status = $request->bk_status;

        if ($request->bk_status === 'Not Approved' && $request->bk_reason) {
            $booking->bk_reason = $request->bk_reason;
        }

        $booking->save();

        $startDate = date('d-m-Y', strtotime($booking->bk_startdate));
        $startTime = date('H:i', strtotime($booking->bk_startdate));
        $endTime = date('H:i', strtotime($booking->bk_enddate));

        $messages = "";
        switch ($request->bk_status) {
            case 'Approved':
                $messages = "Permintaan peminjaman ".$booking->room_name." pada tanggal ".$startDate." ".$startTime." - ".$endTime." telah disetujui.";
                break;
            case 'Cancel':
                $messages = "Permintaan peminjaman ".$booking->room_name." pada tanggal ".$startDate." ".$startTime." - ".$endTime." telah dibatalkan.";
                break;
            case 'Not Approved':
                $messages = "Mohon maaf, permintaan peminjaman ".$booking->room_name." pada tanggal ".$startDate." ".$startTime." - ".$endTime." tidak disetujui karena ".$request->bk_reason;
                break;
            default:
                $messages = "Status peminjaman ".$booking->room_name." pada tanggal ".$startDate." ".$startTime." - ".$endTime." telah diubah menjadi ".$request->bk_status;
                break;
        }

        $itemnotif = [
            'notif_id_member' => $booking->bk_username,
            'notif_type' => 'ruangan',
            'notif_content' => $messages,
            'notif_date' => date('Y-m-d H:i:s'),
            'notif_status' => 'unread',
            'notif_id_detail' => $id,
        ];

        DB::table('notification_mobile')->insert($itemnotif);

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function getbyid(Request $request)
    {
        return HistoryBookingModel::find($request->id)->toJson();
    }
}
