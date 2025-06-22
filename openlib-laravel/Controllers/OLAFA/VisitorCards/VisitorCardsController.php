<?php

namespace App\Http\Controllers\OLAFA\VisitorCards;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RfidregModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;;


class VisitorCardsController extends Controller
{
    public function index(Request $request)
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        return view('olafa.rfidreg.index');
    }

    public function ajax_data(Request $request){

        $data = DB::table('rfid_not_same_with_igracias as rf')
                ->join('member', 'rf.username_id', '=', 'member.id')
                ->select('rf.*', 'member.master_data_fullname')
                ->orderBy('member.master_data_fullname', 'asc')
                ->get();

        return datatables($data)
        ->addColumn('action', function ($db) {
            return '<div class="btn-group">
                <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->id . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>
                    <li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>
                </ul>
            </div>';
        })
        ->rawColumns(['action'])->toJson();
    }
    public function ajax_image(Request $request){

        $data = DB::table('rfid_not_in_db')
                ->orderBy('description', 'asc')
                ->get();

        return datatables($data)
        ->addColumn('action', function ($db) {
            return '<div class="btn-group">
                <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>
                </ul>
            </div>';
        })
        ->rawColumns(['action'])->toJson();
    }

    public function getById($id){
        $rfidregmodel = new RfidregModel();

        $data = $rfidregmodel->getById($id);
        return response()->json($data);
    }

    public function auto_data(Request $request)
    {

        $query = strtolower($request->input('q'));
        $rfidregmodel = new RfidregModel();
        $dt = $rfidregmodel->member($query);
    
        $arr = array();
        foreach ($dt as $row){
            $tab['id'] = $row->master_data_user;
            $tab['name'] = $row->master_data_user . " - " . $row->master_data_fullname;
            $arr[] = $tab;
		}
        return response()->json($arr);
    }

    public function save(Request $request){
        try {

            $type = $request->input('type');
            $inp = $request->input('inp');
            $id = $inp['id'];

            if ($type === 'form_satu') {
                $rfidregmodel = new RfidregModel();

                // Check if RFID already exists with a different ID
                $existingRfid = DB::table('rfid_not_same_with_igracias')
                ->where('rfid', $inp['rfid'])
                ->where('id', '!=', $id)
                ->first();

                if ($existingRfid) {
                    return response()->json(['status' => 'error', 'message' => 'RFID already exists']);
                }

                if ($id){
                    DB::table('rfid_not_same_with_igracias')->where('id', $id)->update($inp);
                }
                else{
                    if ($rfidregmodel->checkInsert($inp)->isNotEmpty()) {
                        return response()->json(['status' => 'error', 'message' => 'Username is Exist']);
                    }else {
                        $status	= "success";
                        $dt = $rfidregmodel->getRfid('', $inp['username'])->first();
                        $member = $rfidregmodel->getMember($inp['username'])->first();
                        if (!$member) {
                            if ($dt->member_type_id == '5') {
                                $user = $rfidregmodel->getUser('mahasiswa', $inp['username'])->first();
                                if ($user) {
                                    $master_data_course = $user->c_kode_prodi;
                                    $member_type_api = $rfidregmodel->getMemberTypeApi('MAHASISWA', $user->c_kode_prodi)->first();
                                } else {
                                    $status = "failed";
                                }
                            } else {
    
                                $user = $rfidregmodel->getUser('pegawai', $dt->c_username)->first();
                                if ($user) {
                                    $master_data_course = null;
                                    $member_type_api = $rfidregmodel->getMemberTypeApi('PEGAWAI', $user->c_kode_status_pegawai)->first();
                                } else {
                                    $status = "failed";
                                }
                            }

                            if ($status=="success"){
                                $data = array( 
                                    "member_type_id" 		=> $member_type_api->member_type_id,
                                    "member_class_id" 		=> $member_type_api->member_class_id,
                                    "master_data_user" 		=> $dt->c_username,
                                    "master_data_email" 	=> $dt->email,
                                    "master_data_course" 	=> $master_data_course,
                                    "master_data_fullname" 	=> $dt->fullname,
                                    "status" 				=> "1",
                                    "created_at" 			=> date("Y-m-d")
                                );
                            
                                $item['username_id'] = $rfidregmodel->addMember($data);
                                if ($rfidregmodel->addRfidNotSameWithIgracias($item)) {
                                    return response()->json(['status' => 'success', 'message' => 'Success to save data for form_satu']);
                                };
                            }
                            else {
                                return response()->json(['status' => 'error', 'message' => 'Unknown form type']);
                            } 
    
                        }else {
                            $inp['username_id'] = $member->id;
                            // Insert new record
                            DB::table('rfid_not_same_with_igracias')->insert([
                                'username_id' => $inp['username_id'],
                                'username' => $inp['username'],
                                'rfid' => $inp['rfid']
                            ]);
                        } 
                    }
                }
                return response()->json(['status' => 'success', 'message' => 'Success to save data for form_satu', 'type' => $type]);
            
            } elseif ($type === 'form_dua') {
                // Handle form_dua
                $data = [
                    'rfid' => $inp['rfid'],
                    'description' => $inp['description']
                ];
    
                if ($request->id) {
                    // Update existing record
                    DB::table('rfid_not_in_db')->where('id', $request->id)->update($data);
                } else {
                    // Insert new record
                    DB::table('rfid_not_in_db')->insert($data);
                }
    
                return response()->json(['status' => 'success', 'message' => 'Success to save data for form_dua', 'type' => $type]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Unknown form type']);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }

    public function delete(Request $request){
        $id = $request->id;
        $type = $request->input('type');
        if ($type === 'form_satu') {
            // Handle form_satu
            DB::table('rfid_not_same_with_igracias')->where('id', $id)->delete();
        } elseif ($type === 'form_dua') {
            // Handle form_dua
            DB::table('rfid_not_in_db')->where('id', $id)->delete();
        } else {
            return response()->json(['status' => 'error', 'message' => 'Unknown form type']);
        }
        
        return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
    }
}
