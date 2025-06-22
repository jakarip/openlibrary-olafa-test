<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use App\Models\Room\BlacklistModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use DateTime;

class Blacklist extends Controller
{
    public function index()
    {
        if(!auth()->can('room-blacklist.view')){
            return redirect('/home');
        }

        return view('room.blacklist');
    }

    public function dt(Request $request)
    {
        $data = (new BlacklistModel())->showBlacklistDetails();

        return datatables($data)
            ->addColumn('action', function ($db) {
                if(auth()->can('room-blacklist.delete')) {
                    return '<div class="btn-group">
                                <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:void(0);" onclick="deleteByUsername(\'' . $db->bl_username . '\')"><i class="ti ti-trash me-2"></i>' . __('common.delete_data') . '</a></li>
                                </ul>
                            </div>';
                }

                return '';
            })
            ->addColumn('bl_date', function ($db) {
                if(auth()->can('room-blacklist.view')) {
                    $date = new DateTime($db->bl_date);
                    return $date->format('d-m-Y');
                }

                return '<span class="text-muted">Unauthorized</span>';
            })
            ->rawColumns(['action', 'bl_date'])
            ->toJson();
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $data = (new BlacklistModel())->searchMembers($search);

        return response()->json($data);
    }

    public function getbyid(Request $request)
    {
        return BlacklistModel::find($request->id)->toJson();
    }

    public function save(Request $request)
    {
        try {
            $inp = $request->inp;
            $bl_username = $request->bl_username;

            $existingData = DB::connection('mysql')
                            ->table('room.blacklist')
                            ->where('bl_username', $bl_username)
                            ->first();

            if ($existingData) {
                return response()->json(['status' => 'error', 'message' => __('rooms.blacklist_message_usernamealready_title')]);
            } else {
                DB::connection('mysql')
                    ->table('room.blacklist')
                    ->insert($inp + [
                        'bl_username' => $bl_username,
                    ]);
            }

            return response()->json(['status' => 'success', 'message' => 'Success to save data']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Failed to save data']);
        }
    }

    public function delete(Request $request)
    {
        $bl_username = $request->input('bl_username');

        if (!$bl_username) {
            return response()->json(['status' => 'error', 'message' => 'Username is required']);
        }

        $deleted = DB::connection('mysql')
                    ->table('room.blacklist')
                    ->where('bl_username', $bl_username)
                    ->delete();

        if ($deleted) {
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        }

        return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }
}
