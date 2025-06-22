<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use App\Models\Room\HistoryModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;

class History extends Controller
{
    public function index()
    {
        if(!auth()->can('room-history.view')){
            return redirect('/home');
        }

        $rooms = DB::connection('mysql')->table('room.room')->get();
        return view('room.history', [
            'rooms' => $rooms
        ]);
    }

    public function dt(Request $request)
    {
        $roomId = $request->input('room');
        $status = $request->input('bk_status');
        $data = (new HistoryModel())->getHistory($roomId, $status);

        return datatables($data)
            ->addColumn('action', function ($db) {
                if(auth()->can('room-history.payment')) {
                    return '<div class="btn-group-vertical">
                                <button type="button" class="btn btn-sm text-start mb-1" style="font-size: 10px; background-color:#18a689; color:#fff;" title="payment" onclick="status(\'' . $db->bk_payment . '\', \'payment\', ' . $db->bk_id . ')">' . __('rooms.history_table_payment') . '</button>
                            </div>';
                }

                return '';
            })
            ->addColumn('bk_total', function ($db) {
                if(auth()->can('room-history.view')) {
                    return $db->bk_total . ' ' . __('common.person');
                }

                return '<span class="text-muted">0 ' . __('common.person') . '</span>';
            })
            ->addColumn('bk_status', function ($db) {
                if(auth()->can('room-history.view')) {
                    $status = strtolower($db->bk_status);
                    $badgeClass = 'bg-warning';
                    if ($status === 'attend') {
                        $badgeClass = '';
                        return '<span class="badge" style="background-color:#b066ff;color:#fff;">' . $db->bk_status . '</span>';
                    } elseif ($status === 'approved') {
                        $badgeClass = 'btn-primary';
                    } elseif ($status === 'not approved') {
                        $badgeClass = 'btn-secondary';
                    } elseif ($status === 'cancel') {
                        $badgeClass = 'btn-light';
                    } elseif ($status === 'not attend') {
                        $badgeClass = 'btn-danger';
                    }
                }
                return '<span class="badge ' . $badgeClass . '">' . $db->bk_status . '</span>';

                return '<span class="text-muted">Unauthorized</span>';
            })
            ->addColumn('bk_payment', function ($db) {
                if(auth()->can('room-history.view')) {
                    if (is_null($db->bk_payment) || $db->bk_payment == 0) {
                        return '';
                    }
                    return 'Rp ' . number_format($db->bk_payment, 0, ',', '.');
                }

                return '<span class="text-muted">Unauthorized</span>';
            })
            ->rawColumns(['action', 'bk_total' ,'bk_status', 'bk_payment'])
            ->toJson();
    }

    public function getbyid(Request $request)
    {
        return HistoryModel::find($request->id)->toJson();
    }

    public function save(Request $request)
    {
        try {
            $inp = $request->inp;
            $bkId = $request->bk_id;
            $dbs = HistoryModel::find($bkId);

            if (!$dbs) {
                return response()->json(['status' => 'error', 'message' => 'Data not found']);
            }
            // Update bk_payment dengan nilai yang baru
            $dbs->bk_payment = $inp['bk_payment'];

            $dbs->save();

            return response()->json(['status' => 'success', 'message' => 'Success to save data']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Failed to save data']);
        }
    }
}
