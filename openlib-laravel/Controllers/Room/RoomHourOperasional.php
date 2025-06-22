<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use App\Models\Room\RoomHourModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoomHourOperasional extends Controller
{
    public function index()
    {
        if(!auth()->can('room-houroperasional.view')){
            return redirect('/home');
        }

        $rooms = DB::connection('mysql')->table('room.room')->get();
        $locations = DB::connection('mysql')->table('item_location')->get();

        return view('room.houroperasional', [
            'rooms' => $rooms,
            'locations' => $locations
        ]);
    }

    public function dt(Request $request)
    {
        $locationId = $request->input('location');
        $data = (new RoomHourModel())->getRoomsHour();

        return datatables($data)
            ->addColumn('action', function ($db) {
                $btn = '';
                if (auth()->canAtLeast([
                    'room-houroperasional.edit',
                    'room-houroperasional.delete',
                    'room-houroperasional.activate'
                ])) {
                    $btn = '<div class="btn-group">
                        <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">';
                    if (auth()->can('room-houroperasional.edit') || auth()->can('room-houroperasional.update')) {
                        $btn .= '<li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->rh_id . '\')"><i class="ti ti-edit ti-sm me-2"></i> ' . __('common.edit_data') . ' </a></li>';
                    }
                    if (auth()->can('room-houroperasional.delete')) {
                        $btn .= '<li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->rh_id . '\')"><i class="ti ti-trash me-2"></i> ' . __('common.delete_data') . ' </a></li>';
                    }
                    if ($db->rh_status == 0 && auth()->can('room-houroperasional.activate')) {
                        $btn .= '<li class="d-flex"><a class="dropdown-item d-flex align-items-center text-success" href="javascript:activate(\'' . $db->rh_id . '\')"><i class="ti ti-check me-2"></i> ' . __('common.activate') . ' </a></li>';
                    }
                    $btn .= '</ul></div>';
                }
                return $btn;
            })
            ->addColumn('location_name', function ($db) {
                return $db->location_name ?? '-';
            })
            ->addColumn('active_formatted', function ($db) {
                $badgeClass = '';
                $statusText = '';

                if ($db->rh_status == 1) {
                    $badgeClass = 'success';
                    $statusText = __('common.active');
                } else if ($db->rh_status == 0) {
                    $badgeClass = 'danger';
                    $statusText = __('common.not_active');
                }

                return '<span class="badge text-bg-' . $badgeClass . '">' . $statusText . '</span>';
            })
            ->rawColumns(['action', 'active_formatted', 'location_name'])
            ->toJson();
    }

    public function getbyid($id)
    {
        $data = RoomHourModel::find($id);
        return response()->json($data);
    }

    public function save(Request $request)
    {
        try {
            $inp = $request->inp;
            $rh_id = $request->rh_id;

            $dbs = $rh_id ? RoomHourModel::find($rh_id) : new RoomHourModel();

            foreach ($inp as $key => $value) {
                $dbs->$key = $value;
            }

            // Set rh_status default 0 jika data baru
            if (!$rh_id) {
                $dbs->rh_status = 0;
            }

            $dbs->save();

            return response()->json(['status' => 'success', 'message' => 'Success to save data']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Failed to save data', 'error' => $th->getMessage()]);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->id;

        $data = RoomHourModel::find($id);
        $data->delete();

        return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
    }

    public function toggleRoomStatus(Request $request)
    {
        $roomHourId = $request->input('rh_id');
        $room = RoomHourModel::find($roomHourId);

        if ($room) {
            // Jika ingin mengaktifkan (dari 0 ke 1)
            if ($room->rh_status == 0) {
                // Nonaktifkan semua jenis weekday lain pada lokasi yang sama
                $jenis = strtolower($room->rh_name);
                $location = $room->rh_id_location;

                $jenisLain = [];
                if ($jenis == 'weekday') {
                    $jenisLain = ['weekday_libur', 'weekday_ramadan'];
                } elseif ($jenis == 'weekday_libur') {
                    $jenisLain = ['weekday', 'weekday_ramadan'];
                } elseif ($jenis == 'weekday_ramadan') {
                    $jenisLain = ['weekday', 'weekday_libur'];
                }

                if (!empty($jenisLain)) {
                    RoomHourModel::where('rh_id_location', $location)
                        ->whereIn('rh_name', $jenisLain)
                        ->update(['rh_status' => 0]);
                }
                $room->rh_status = 1;
            } else {
                // Jika ingin menonaktifkan
                $room->rh_status = 0;
            }
            $room->save();

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'Room not found']);
    }
}
