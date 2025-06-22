<?php

namespace App\Http\Controllers\Pengadaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TelupressModel;
use App\Models\SubmissionModel;
use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class Telupress extends Controller
{
    public function telupress()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        $telupress = new TelupressModel();
        $prodi = $telupress->getprodi();

        $temp = [];
        foreach ($prodi as $row) {
            $formattedName = ucwords(strtolower($row->NAMA_FAKULTAS)) . ' - ' . ucwords(strtolower($row->NAMA_PRODI));
            $temp[$row->C_KODE_PRODI] = $formattedName;
        }
        $data = [
            'prodi' => $temp,
        ];

        // dd($data);

        return view('pengadaan.pengajuan.tel-u-press.index', $data);
    }

    public function telupress_dt(Request $request)
    {
        // dd($request->all());
        $status = $request->input('status');
        $prodi = $request->input('prodi');
        // dd($status, $prodi);

        $param = array();
        $param['where'] = "";

        if (!empty($status)) {
            if (empty($param['where']))     $param['where'] = "WHERE (book_status='" . $status . "')";
            else $param['where'] .= "AND (book_status='" . $status . "')";
        }

        if (!empty($prodi)) {
            if (empty($param['where']))     $param['where'] = "WHERE (book_id_prodi='" . $prodi . "')";
            else $param['where'] .= "AND (book_id_prodi='" . $prodi . "')";
        }

        // dd($param);

        $telupress = new TelupressModel();
        $data = $telupress->dtquery($param);

        // dd($data);
        return datatables($data)
            ->editColumn('book_status', function ($db) {
                $status_labels = [
                    '1' => 'bg-danger',
                    '2' => 'bg-warning',
                    '3' => 'bg-info',
                    '4' => 'bg-label-primary',
                    '5' => 'bg-primary',
                    '6' => 'bg-label-danger',
                    '7' => 'bg-success'
                ];

                $status_type = [
                    '' => 'Semua Jenis Status',
                    '1' => 'Pengajuan Naskah',
                    '2' => 'Review Naskah',
                    '3' => 'Editing & Proofread',
                    '4' => 'Layout',
                    '5' => 'ISBN',
                    '6' => 'Cetak',
                    '7' => 'Sudah Diterima'
                ];

                $conclusion_labels = [
                    'default' => 'bg-success',
                    'negative' => 'bg-danger'
                ];

                $status = '<div class="badge ' . $status_labels[$db->book_status] . '">' . $status_type[$db->book_status] . '</div> <br>';

                if ($db->book_status == '6' || $db->book_status == '7') {
                    $conclusion = '<div class="badge ' . ($db->proses_step6 < 0 ? $conclusion_labels['negative'] : $conclusion_labels['default']) . '">' . ($db->proses_step6 < 0 ? 'Melewati SLA' : 'Memenuhi SLA') . '</label>';
                    return $status . '<br>' . $conclusion;
                }

                return $status;
            })
            ->editColumn('total_proses_naskah_cetak', function ($db) {
                $jml_hari_kerja = "-";
        
                if (!empty($db->book_startdate_realization_step_1) && !empty($db->book_enddate_realization_step_6)) {
                    $start_date = new \DateTime($db->book_startdate_realization_step_1);
                    $end_date = new \DateTime($db->book_enddate_realization_step_6);
                    $end_date->modify('+1 day'); // Include the end date in the calculation
        
                    $interval = new \DateInterval('P1D');
                    $date_range = new \DatePeriod($start_date, $interval, $end_date);
        
                    $workdays = 0;
                    foreach ($date_range as $date) {
                        if ($date->format('N') < 6) { // Exclude weekends (Saturday = 6, Sunday = 7)
                            $workdays++;
                        }
                    }
        
                    $jml_hari_kerja = $workdays;
                }
        
                return $jml_hari_kerja;
            })
            ->addColumn('action', function ($db) {
                return '<div class="btn-group">
                    <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->book_id . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>
                        <li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->book_id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>
                    </ul>
                </div>';
            })
            ->rawColumns(['book_status','action'])->toJson();
    }

    public function telupress_auto_data(Request $request)
    {

        $query = strtolower($request->input('q'));
        $telupress = new TelupressModel();
        $dt = $telupress->getmemberbyname($query);

        // dd($dt);
        $arr = array();
        foreach ($dt as $row) {
            $tab['id'] = $row->id;
            $tab['name'] = "(" . $row->master_data_number . ") - " . $row->master_data_fullname;
            $arr[] = $tab;
        }
        // dd($arr);   
        return response()->json($arr);
    }

    public function telupress_save(Request $request)
    {
        // dd($request->input('inp'));
        $telupress = new TelupressModel();
        $inp = $request->input('inp');
        $dbs = $telupress->find($request->input('id')) ?? new TelupressModel();

        // Format the dates
        $inp['book_startdate_realization_step_1'] = ($inp['book_startdate_realization_step_1'] != "" ? Date::createFromFormat('d/m/Y', $inp['book_startdate_realization_step_1'])->format('Y-m-d') : null);
        $inp['book_enddate_realization_step_1'] = ($inp['book_enddate_realization_step_1'] != "" ? Date::createFromFormat('d/m/Y', $inp['book_enddate_realization_step_1'])->format('Y-m-d') : null);
        $inp['book_startdate_realization_step_2'] = ($inp['book_startdate_realization_step_2'] != "" ? Date::createFromFormat('d/m/Y', $inp['book_startdate_realization_step_2'])->format('Y-m-d') : null);
        $inp['book_enddate_realization_step_2'] = ($inp['book_enddate_realization_step_2'] != "" ? Date::createFromFormat('d/m/Y', $inp['book_enddate_realization_step_2'])->format('Y-m-d') : null);
        $inp['book_startdate_realization_step_3'] = ($inp['book_startdate_realization_step_3'] != "" ? Date::createFromFormat('d/m/Y', $inp['book_startdate_realization_step_3'])->format('Y-m-d') : null);
        $inp['book_enddate_realization_step_3'] = ($inp['book_enddate_realization_step_3'] != "" ? Date::createFromFormat('d/m/Y', $inp['book_enddate_realization_step_3'])->format('Y-m-d') : null);
        $inp['book_startdate_realization_step_4'] = ($inp['book_startdate_realization_step_4'] != "" ? Date::createFromFormat('d/m/Y', $inp['book_startdate_realization_step_4'])->format('Y-m-d') : null);
        $inp['book_enddate_realization_step_4'] = ($inp['book_enddate_realization_step_4'] != "" ? Date::createFromFormat('d/m/Y', $inp['book_enddate_realization_step_4'])->format('Y-m-d') : null);
        $inp['book_startdate_realization_step_5'] = ($inp['book_startdate_realization_step_5'] != "" ? Date::createFromFormat('d/m/Y', $inp['book_startdate_realization_step_5'])->format('Y-m-d') : null);
        $inp['book_enddate_realization_step_5'] = ($inp['book_enddate_realization_step_5'] != "" ? Date::createFromFormat('d/m/Y', $inp['book_enddate_realization_step_5'])->format('Y-m-d') : null);
        $inp['book_startdate_realization_step_6'] = ($inp['book_startdate_realization_step_6'] != "" ? Date::createFromFormat('d/m/Y', $inp['book_startdate_realization_step_6'])->format('Y-m-d') : null);
        $inp['book_enddate_realization_step_6'] = ($inp['book_enddate_realization_step_6'] != "" ? Date::createFromFormat('d/m/Y', $inp['book_enddate_realization_step_6'])->format('Y-m-d') : null);
        $inp['book_received_date'] = ($inp['book_received_date'] != "" ? Date::createFromFormat('d/m/Y', $inp['book_received_date'])->format('Y-m-d') : null);

        $step = array();

        $step = [
            '1' => '3',
            '2' => '40',
            '3' => '20',
            '4' => '20',
            '5' => '7',
            '6' => '10'
        ];

        $inp['book_startdate_target_step_1'] = $inp['book_startdate_realization_step_1'];
        $inp['book_enddate_target_step_1'] =  date('Y-m-d', strtotime($inp['book_startdate_target_step_1'] . '+ ' . ($step[1] - 1) . ' weekdays'));
        $inp['book_startdate_target_step_2'] =  date('Y-m-d', strtotime($inp['book_enddate_target_step_1'] .  '+ 1 weekdays'));
        $inp['book_enddate_target_step_2'] =  date('Y-m-d', strtotime($inp['book_startdate_target_step_2'] . '+ ' . ($step[2] - 1) . ' weekdays'));
        $inp['book_startdate_target_step_3'] =  date('Y-m-d', strtotime($inp['book_enddate_target_step_2'] .  '+ 1 weekdays'));
        $inp['book_enddate_target_step_3'] =  date('Y-m-d', strtotime($inp['book_startdate_target_step_3'] . '+ ' . ($step[3] - 1) . ' weekdays'));
        $inp['book_startdate_target_step_4'] =  date('Y-m-d', strtotime($inp['book_enddate_target_step_3'] .  '+ 1 weekdays'));
        $inp['book_enddate_target_step_4'] =  date('Y-m-d', strtotime($inp['book_startdate_target_step_4'] . '+ ' . ($step[3] - 1) . ' weekdays'));
        $inp['book_startdate_target_step_5'] =  date('Y-m-d', strtotime($inp['book_enddate_target_step_4'] .  '+ 1 weekdays'));
        $inp['book_enddate_target_step_5'] =  date('Y-m-d', strtotime($inp['book_startdate_target_step_5'] . '+ ' . ($step[3] - 1) . ' weekdays'));
        $inp['book_startdate_target_step_6'] =  date('Y-m-d', strtotime($inp['book_enddate_target_step_5'] .  '+ 1 weekdays'));
        $inp['book_enddate_target_step_6'] =  date('Y-m-d', strtotime($inp['book_startdate_target_step_6'] . '+ ' . ($step[3] - 1) . ' weekdays'));

        $inp['book_status'] = '1';
        if ($inp['book_startdate_realization_step_2'] != "") $inp['book_status'] = '2';
        if ($inp['book_startdate_realization_step_3'] != "") $inp['book_status'] = '3';
        if ($inp['book_startdate_realization_step_4'] != "") $inp['book_status'] = '4';
        if ($inp['book_startdate_realization_step_5'] != "") $inp['book_status'] = '5';
        if ($inp['book_startdate_realization_step_6'] != "") $inp['book_status'] = '6';
        if ($inp['book_received_date'] != "") $inp['book_status'] = '7';
        // dd($inp);

        // dd($dbs);
        foreach ($inp as $key => $value) {
            $dbs->$key = $value;
        }
        // dd($request->input('inp'),$dbs);
        // dd($dbs);
        $dbs->save();
        return response()->json(['status' => 'success', 'message' => 'Success to save data']);
    }


    public function telupress_delete(Request $request)
    {
        $id = $request->id;
        $data = TelupressModel::find($id);

        $data->delete();

        return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
    }

    public function telupress_edit($id)
    {

        $data = TelupressModel::where('book_id', $id)->first();
        // dd($data);  
        return response()->json($data);
    }

    public function telupress_update($id, Request $request)
    {

        // dd($request->all());
        $dbs = TelupressModel::where('book_id', $id)->first();
        $inp = $request->input('inp');


        // Function to format date
        function formatDate($date) {
                if (strpos($date, '-') !== false) {
                    // Date is already in Y-m-d format
                    return Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
                } else {
                    // Date is in d/m/Y format
                    return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
                }
            }

        // Format the dates
        $inp['book_startdate_realization_step_1'] = formatDate($inp['book_startdate_realization_step_1']);
        $inp['book_enddate_realization_step_1'] = formatDate($inp['book_enddate_realization_step_1']);
        $inp['book_startdate_realization_step_2'] = formatDate($inp['book_startdate_realization_step_2']);
        $inp['book_enddate_realization_step_2'] = formatDate($inp['book_enddate_realization_step_2']);
        $inp['book_startdate_realization_step_3'] = formatDate($inp['book_startdate_realization_step_3']);
        $inp['book_enddate_realization_step_3'] = formatDate($inp['book_enddate_realization_step_3']);
        $inp['book_startdate_realization_step_4'] = formatDate($inp['book_startdate_realization_step_4']);
        $inp['book_enddate_realization_step_4'] = formatDate($inp['book_enddate_realization_step_4']);
        $inp['book_startdate_realization_step_5'] = formatDate($inp['book_startdate_realization_step_5']);
        $inp['book_enddate_realization_step_5'] = formatDate($inp['book_enddate_realization_step_5']);
        $inp['book_startdate_realization_step_6'] = formatDate($inp['book_startdate_realization_step_6']);
        $inp['book_enddate_realization_step_6'] = formatDate($inp['book_enddate_realization_step_6']);
        $inp['book_received_date'] = formatDate($inp['book_received_date']);
        
		$inp['book_startdate_target_step_1'] = date('Y-m-d', strtotime($inp['book_startdate_target_step_1']));
		$inp['book_enddate_target_step_1'] = ($inp['book_enddate_target_step_1']!=""? date('Y-m-d', strtotime($inp['book_enddate_target_step_1'])):null);
		$inp['book_startdate_target_step_2'] = ($inp['book_startdate_target_step_2']!=""? date('Y-m-d', strtotime($inp['book_startdate_target_step_2'])):null);
		$inp['book_enddate_target_step_2'] = ($inp['book_enddate_target_step_2']!=""? date('Y-m-d', strtotime($inp['book_enddate_target_step_2'])):null); 
		$inp['book_startdate_target_step_3'] = ($inp['book_startdate_target_step_3']!=""? date('Y-m-d', strtotime($inp['book_startdate_target_step_3'])):null);
		$inp['book_enddate_target_step_3'] = ($inp['book_enddate_target_step_3']!=""? date('Y-m-d', strtotime($inp['book_enddate_target_step_3'])):null);
		$inp['book_startdate_target_step_4'] = ($inp['book_startdate_target_step_4']!=""? date('Y-m-d', strtotime($inp['book_startdate_target_step_4'])):null);
		$inp['book_enddate_target_step_4'] = ($inp['book_enddate_target_step_4']!=""? date('Y-m-d', strtotime($inp['book_enddate_target_step_4'])):null);
		$inp['book_startdate_target_step_5'] = ($inp['book_startdate_target_step_5']!=""? date('Y-m-d', strtotime($inp['book_startdate_target_step_5'])):null);
		$inp['book_enddate_target_step_5'] = ($inp['book_enddate_target_step_5']!=""? date('Y-m-d', strtotime($inp['book_enddate_target_step_5'])):null);
		$inp['book_startdate_target_step_6'] = ($inp['book_startdate_target_step_6']!=""? date('Y-m-d', strtotime($inp['book_startdate_target_step_6'])):null);
		$inp['book_enddate_target_step_6'] = ($inp['book_enddate_target_step_6']!=""? date('Y-m-d', strtotime($inp['book_enddate_target_step_6'])):null);

		$inp['book_received_date'] = ($inp['book_received_date']!=""? date('Y-m-d', strtotime($inp['book_received_date'])):null);


        $inp['book_status'] = '1';
        if ($inp['book_startdate_realization_step_2'] != "") $inp['book_status'] = '2';
        if ($inp['book_startdate_realization_step_3'] != "") $inp['book_status'] = '3';
        if ($inp['book_startdate_realization_step_4'] != "") $inp['book_status'] = '4';
        if ($inp['book_startdate_realization_step_5'] != "") $inp['book_status'] = '5';
        if ($inp['book_startdate_realization_step_6'] != "") $inp['book_status'] = '6';
        if ($inp['book_received_date'] != "") $inp['book_status'] = '7';

        foreach ($inp as $key => $value) {
            $dbs->$key = $value;
        }

        // dd($dbs);
        $dbs->save();
        return response()->json(['status' => 'success', 'message' => 'Success to update data']);
    }
}
