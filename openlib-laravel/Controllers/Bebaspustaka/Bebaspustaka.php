<?php

namespace App\Http\Controllers\Bebaspustaka;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BebasPustakaModel;
use App\Models\FreeLetterModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
;

class BebasPustaka extends Controller
{
    public function index_delete_file()
    {
        return view('bebasPustaka.delete');
    }

    public function auto_data(Request $request)
    {
        $query = strtolower($request->input('q'));
        $bebaspustakamodel = new BebasPustakaModel();
        $dt = $bebaspustakamodel->member($query);

        $arr = array();
        foreach ($dt as $row) {
            $tab['id'] = $row->memberid;
            $tab['name'] = $row->master_data_user . " - " . $row->master_data_number . " - " . $row->master_data_fullname . " (" . $row->NAMA_PRODI . ")";
            $arr[] = $tab;
        }
        return response()->json($arr);
    }

    public function dt(Request $request)
    {
        $selectedItem = $request->input('selectedItem');
        $id = $selectedItem['id'];
        $bebaspustakamodel = new BebasPustakaModel();
        $data = $bebaspustakamodel->dokumen($id);

        return datatables($data)
            ->addColumn('action', function ($db) {
                return '<div class="btn-group">
                    <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="' . url('bebas-pustaka/delete_detail/' . $db->id) . '"><i class="ti ti-edit ti-sm me-2"></i> Detail Data</a></li>
                        <li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>
                        </ul>
                    </div>';
            })
            ->rawColumns(['action'])->toJson();
    }
    public function dt_delete_file(Request $request)
    {

        $id = $request->id;
        $bebaspustakamodel = new BebasPustakaModel();
        $data = $bebaspustakamodel->getDocument($id);

        return datatables($data)
            ->addColumn('action', function ($db) {
                return '<button type="button" class="btn btn-danger" onclick="del(\'' . $db->id . '\')">
                                <i class="ti ti-trash "></i>
                        </button>';
            })
            ->rawColumns(['action'])->toJson();
    }


    public function delete_detail($id)
    {

        return view('bebasPustaka.deleteDetail', ['id' => $id]);
    }

    public function delete_file(Request $request)
    {
        $id = $request->input('id');
        $bebaspustakamodel = new BebasPustakaModel();
        $bebaspustakamodel->deleteFile($id);
        return response()->json(['status' => 'success', 'message' => 'Success to delete data']);

    }

    public function delete_document(Request $request)
    {
        $id = $request->input('id');
        $bebaspustakamodel = new BebasPustakaModel();
        $bebaspustakamodel->deleteDocument($id);
        return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
    }

    public function index_sbkpprint()
    {
        return view('bebasPustaka.sbkpprint');
    }

    public function dt_sbkpprint(Request $request)
    {
        // Validate and set dates
        $start = $request->input('startDate');
        $end = $request->input('endDate');

        $query = FreeLetterModel::query();

        if ($start && $end) {
            $query->whereBetween('created_at', ["$start 00:00:00", "$end 23:59:59"]);
        }

        $freeLetters = $query->select('id', 'letter_number', 'member_number', 'name', 'donated_item_title', 'donated_item_author', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();


        return datatables($freeLetters)->toJson();
    }

    public function auto_data_sbkpprint(Request $request)
    {
        $query = strtolower($request->input('q'));
        $bebaspustakamodel = new BebasPustakaModel();
        $dt = $bebaspustakamodel->member($query);

        $arr = array();
        foreach ($dt as $row) {
            $tab['id'] = $row->memberid;
            $tab['name'] = $row->master_data_user . " - " . $row->master_data_number . " - " . $row->master_data_fullname . " (" . $row->NAMA_PRODI . ")";
            $tab['username'] = $row->master_data_user;
            $arr[] = $tab;
        }
        return response()->json($arr);
    }

    public function save_sbkpprint(Request $request)
    {
        try {
            // dd($request->all());

            $username = $request->input('username');
            $donated_item_title = $request->input('judul');
            $donated_item_author = $request->input('pengarang');

            $freeLetter = new FreeLetterModel();
            $prodi = $freeLetter->getProdiFak($username)->first();

            if (($prodi->amnesty == 0 && $prodi->sisa <= 0) || $prodi->amnesty != 0) {
                $freeLetter->letter_number = '/AKD26/PUS/' . date('Y');
                $freeLetter->created_at = now();
                $freeLetter->created_by = session()->get('userData')->master_data_user;
                $freeLetter->course_code = $prodi->master_data_course ?: 0;
                $freeLetter->is_member = 1;
                $freeLetter->registration_number = $prodi->id;
                $freeLetter->name = $prodi->master_data_fullname;
                $freeLetter->donated_item_title = $donated_item_title;
                $freeLetter->donated_item_author = $donated_item_author;
                $freeLetter->member_number = $username;

                // dd($freeLetter);
                $status['status'] = '2';
                DB::table('member')->where('id', $prodi->id)->update($status);

                $freeLetter->save();

                return response()->json(['status' => 'success', 'message' => 'Success to save data']);
            } else {
                return response()->json(['status' => 'denda', 'message' => 'Member has outstanding penalties']);
            }

        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }
    public function print_letter($id)
    {
        $dt = FreeLetterModel::findOrFail($id);
        return view('bebasPustaka.print_letter', compact('dt'));
    }

}
