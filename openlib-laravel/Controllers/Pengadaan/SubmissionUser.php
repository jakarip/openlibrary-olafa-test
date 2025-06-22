<?php

namespace App\Http\Controllers\Pengadaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TelupressModel;
use App\Models\SubmissionUserModel;
use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class SubmissionUser extends Controller
{

    public function dosenpegawai()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        
        $submission = new SubmissionUserModel();
        $prodi = $submission->getProdi();
        return view('pengadaan.pengajuan.dosen-pegawai.index', ['prodi' => $prodi]);
    }

    public function dosenpegawai_dt(Request $request)
    {
        $param = array();
        $param = [
            'where' => '',
            'order' => '',
            'limit' => ''
        ];

        $status = $request->input('status');
        $created_date_option = $request->input('created_date_option');
        $created_date = $request->input('created_date');

        function formatDate($date)
        {
            if (strpos($date, '-') !== false) {
                // Date is already in Y-m-d format
                return $date;
            } else {
                // Date is in d/m/Y format
                $parts = explode('/', $date);
                return $parts[2] . '-' . $parts[0] . '-' . $parts[1];
            }
        }
    
        $option = "";
		if($created_date_option !='all' )  {
			$temp = explode(' - ',$created_date);

            $date1 = formatDate(trim($temp[0]));
            $date2 = formatDate(trim($temp[1])) . ' 23:59:59';

			$option = "bp_createdate between '$date1' and '$date2'";
		}

        if ($status!=""){ 
			if(empty($param['where'])) 	$param['where'] = "AND (bp_status='".$status."')";
			else $param['where'] .= "AND (bp_status='".$status."')"; 
		} 

		if ($option!=""){ 
			if(empty($param['where'])) 	$param['where'] = "AND (".$option.")";
			else $param['where'] .= "AND (".$option.")";
		}

        
        $submission = new SubmissionUserModel();
        $data = $submission->dtquery($param);

        // Format bp_createdate field
        foreach ($data as &$item) {
            if (isset($item->bp_createdate)) {
                $item->bp_createdate = Carbon::parse($item->bp_createdate)->format('Y-m-d');
            }
        }

        // dd($data);

        return datatables($data)
            ->addColumn('action', function ($row) {
                if ($row->bp_status == 'Request') {
                    return '<div class="btn-group"> 
                            <button type="button" class="btn btn-sm btn-success" title="Approved" onclick="modalApproved(\'' . $row->bp_id . '\',\'Approved\')">Approved</button>
                        </div><br><br>
                        <div class="btn-group"> 
                            <button type="button" class="btn btn-sm btn-danger" title="Not Approved" onclick="modalNotApproved(\'' . $row->bp_id . '\',\'Not Approved\')">Not Approved</button>
                        </div>';
                }
                return '';
            })
            ->addColumn('status', function ($row) {
                if ($row->bp_status == 'Not Approved') {
                    return '<div class="btn-group">
                            <button type="button" class="btn btn-sm btn-info" title="' . $row->bp_status . '">' . $row->bp_status . '</button>
                        </div>';
                } elseif ($row->bp_status == 'Approved') {
                    return '<div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary" title="' . $row->bp_status . '">' . $row->bp_status . '</button>
                        </div>';
                } elseif ($row->bp_status == 'Request') {
                    return '<div class="btn-group">
                            <button type="button" class="btn btn-sm btn-warning" title="' . $row->bp_status . '">' . $row->bp_status . '</button>
                        </div>';
                }
                return '';
            })
            ->rawColumns(['action', 'status'])
            ->toJson();
    }

    public function dosenpegawai_save(Request $request)
    {
        // dd($request->all());
        // dd($request->hasFile('formFileApproval'));
        $submission = new SubmissionUserModel();

        $inp = $request->input('inp');
        $dbs = $submission->find($request->input('id')) ?? new SubmissionUserModel();


        $inp['bp_createdate'] = date('Y-m-d');
        $inp['bp_idmember'] = auth()->user()->id;
        $inp['bp_upload_type'] = 'apps';
        $inp['bp_status'] = 'Request';
        // dd($inp);
        // dd($inp->hasFile('formFileApproval'));
        // Handle file upload for "formFileApproval"
        if ($request->hasFile('formFileApproval') && $request->file('formFileApproval')->isValid()) {
            $file = $request->file('formFileApproval');
            $extension = $file->getClientOriginalExtension();
            $newFileName = auth()->user()->id . '_' . round(microtime(true)) . '.' . $extension;
        
            // Save file to storage/app/pengadaan/byUser/approval using Storage facade
            $filePath = Storage::putFileAs('public/pengadaan/byUser/approval', $file, $newFileName);
        
            // Save file name to database
            $inp['bp_approval_kaprodi_file'] = $newFileName;
        }
        
        if ($request->hasFile('formFileRPS') && $request->file('formFileRPS')->isValid()) {
            $file = $request->file('formFileRPS');
            $extension = $file->getClientOriginalExtension();
            $newFileName = auth()->user()->id . '_' . round(microtime(true)) . '.' . $extension;
        
            // Save file to storage/app/pengadaan/byUser/rps using Storage facade
            $filePath = Storage::putFileAs('public/pengadaan/byUser/rps', $file, $newFileName);
        
            // Save file name to database
            $inp['bp_rps_file'] = $newFileName;
        }

        foreach ($inp as $key => $value) {
            $dbs->$key = $value;
        }
        // dd($dbs);

        $dbs->save();
        return response()->json(['status' => 'success', 'message' => 'Success to save data']);
    }

    public function dosenpegawai_approved(Request $request){
        $id = $request->input('id');
        $bp_status = $request->input('bp_status');

        $submission = new SubmissionUserModel();
        $data = $submission->find($id);

        $item = [
            'bp_status' => $bp_status
        ];

        $item2 = [
            'book_id_prodi' => $data->bp_prodi_id,
            'book_member' => $data->master_data_fullname,
            'book_subject' => $data->bp_matakuliah,
            'book_title' => $data->bp_title,
            'book_author' => $data->bp_author,
            'book_publisher' => $data->bp_publisher,
            'book_published_year' => $data->bp_publishedyear,
            'book_date_prodi_submission' => date('Y-m-d', strtotime($data->bp_createdate)),
            'book_status' => 'pengajuan'
        ];

        $submission->addBookProcurement($item2);

        if ($submission->edit($id, $item)) {
            return response()->json(['status' => 'success', 'message' => 'Data updated successfully']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Failed to update data'], 500);
        }
    
    }
    public function dosenpegawai_not_approved(Request $request){
        $id = $request->input('id');
        $bp_status = $request->input('bp_status');
        $option_reason = $request->input('option_reason');
        $bp_reason = $request->input('bp_reason');

        $submission = new SubmissionUserModel();

        $item = [
            'bp_status' => $bp_status,
            'bp_reason' => $option_reason === 'lainnya' ? $bp_reason : $option_reason
        ];
        // dd($item);

        if ($submission->edit($id, $item)) {
            return response()->json(['status' => 'success', 'message' => 'Data updated successfully']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Failed to update data'], 500);
        }
    }
}
