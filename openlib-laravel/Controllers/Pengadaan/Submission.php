<?php

namespace App\Http\Controllers\Pengadaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TelupressModel;
use App\Models\SubmissionModel;
use Carbon\Carbon;
use App\Imports\UserImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class Submission extends Controller
{
    public function buku()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        // dd($request->all());
        $submission = new SubmissionModel();
        $faculty = $submission->getFaculty();
        $prodi = $submission->getProdi();
        // dd($faculty, $prodi);   
        return view('pengadaan.pengajuan.buku.index', [
            'faculty' => $faculty,
            'prodi' => $prodi,

        ]);
    }

    public function buku_redirect(Request $request)
    {
        // dd($request->all());
        $submission = new SubmissionModel();
        $faculty = $submission->getFaculty();
        $prodi = $submission->getProdi();
        $status = $request->input('status', ''); // Get status from input
        $dateRange = $request->input('date_range', ''); 
        $getFakultas = $request->input('fakultas', ''); 
        $getProdi = $request->input('prodi', ''); 

        return view('pengadaan.pengajuan.buku.index', [
            'faculty' => $faculty,
            'prodi' => $prodi,
            'status' => $status, // Pass status to view
            'date_range' => $dateRange, // Pass date_range to view
            'getFakultas' => $getFakultas, 
            'getProdi' => $getProdi, 
        ]);
    }

    public function buku_dt(Request $request)
    {
        // dd($request->all());
        $param = array();
        $param['where'] = "";
        $param['order'] = "";
        $param['limit'] = "";

        $prodi = $request->input('prodi');
        $type = $request->input('type');
        $status = $request->input('status');
        $faculty = $request->input('faculty');
        $dates_submission_option         = $request->input('dates_submission_option');
        $dates_logistic_option             = $request->input('dates_logistic_option');
        $dates_acceptance_option         = $request->input('dates_acceptance_option');
        $dates_email_confirmed_option     = $request->input('dates_email_confirmed_option');
        $dates_available_option         = $request->input('dates_available_option');
        $dates_submission                 = $request->input('dates_submission');
        $dates_logistic                 = $request->input('dates_logistic');
        $dates_acceptance                 = $request->input('dates_acceptance');
        $dates_email_confirmed             = $request->input('dates_email_confirmed');
        $dates_available                 = $request->input('dates_available');

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

        $option = '';
        if ($dates_submission_option != 'all') {
            $temp = explode(' - ', $dates_submission);

            // dd($temp);
            $date1 = formatDate(trim($temp[0]));
            $date2 = formatDate(trim($temp[1])) . ' 23:59:59';
            $option .= "book_date_prodi_submission between '$date1' and '$date2'";
        }

        // dd($dates_submission_option, $dates_submission, $option);
        // dd($option);

        if ($dates_logistic_option != 'all') {

            $temp = explode(' - ', $dates_logistic);

            $date1 = formatDate(trim($temp[0]));
            $date2 = formatDate(trim($temp[1])) . ' 23:59:59';

            $option .= ($option != "" ? " and" : "") . " book_date_logistic_submission between '$date1' and '$date2'";
        }

        if ($dates_acceptance_option != 'all') {
            $temp = explode(' - ', $dates_acceptance);

            $date1 = formatDate(trim($temp[0]));
            $date2 = formatDate(trim($temp[1])) . ' 23:59:59';

            $option .= ($option != "" ? " and" : "") . " book_date_acceptance between '$date1' and '$date2'";
        }

        if ($dates_available_option != 'all') {
            $temp = explode(' - ', $dates_available);

            $date1 = formatDate(trim($temp[0]));
            $date2 = formatDate(trim($temp[1])) . ' 23:59:59';

            $option .= ($option != "" ? " and" : "") . " book_date_available between '$date1' and '$date2'";
        }

        if ($dates_email_confirmed_option != 'all') {
            $temp = explode(' - ', $dates_email_confirmed);

            $date1 = formatDate(trim($temp[0]));
            $date2 = formatDate(trim($temp[1])) . ' 23:59:59';

            $option .= ($option != "" ? " and" : "") . " book_date_email_confirmed between '$date1' and '$date2'";
        }

        // dd($option);
        // dd($prodi, $type, $status, $faculty);

        if ($status != "") {
            if (empty($param['where']))     $param['where'] = "WHERE (book_status='" . $status . "')";
            else $param['where'] .= "AND (book_status='" . $status . "')";
        }

        if ($type != "") {
            if (empty($param['where']))     $param['where'] = "WHERE (book_type='" . $type . "')";
            else $param['where'] .= "AND (book_type='" . $type . "')";
        }

        if ($prodi != "") {
            if (empty($param['where']))     $param['where'] = "WHERE (book_id_prodi='" . $prodi . "')";
            else $param['where'] .= "AND (book_id_prodi='" . $prodi . "')";
        }

        if ($faculty != "") {
            if (empty($param['where']))     $param['where'] = "WHERE (tmp.c_kode_fakultas='" . $faculty . "')";
            else $param['where'] .= "AND (tmp.c_kode_fakultas='" . $faculty . "')";
        }


        if ($option != "") {
            if (empty($param['where']))     $param['where'] = "WHERE (" . $option . ")";
            else $param['where'] .= "AND (" . $option . ")";
        }

        // dd($param);
        $submission = new SubmissionModel();
        $data = $submission->dtquery($param);

        // dd($data);

        return datatables($data)
            ->addColumn('book_status_view', function ($db) {
                $status_labels = [
                    'r_ketersediaan' => 'bg-primary',
                    's_email' => 'bg-success',
                    'penerimaan' => 'bg-info',
                    'logistik' => 'bg-warning',
                    'pengajuan' => 'bg-danger',
                ];

                $status_name = [
                    'r_ketersediaan' => strtoupper('Ketersediaan buku'),
                    's_email' => strtoupper('Konfirmasi Email'),
                    'penerimaan' => strtoupper('Penerimaan Buku'),
                    'logistik' => strtoupper('Pengajuan ke Logistik'),
                    'pengajuan' => strtoupper('Pengajuan dari Prodi'),
                ];

                $status = '<div class="badge ' . $status_labels[$db->book_status] . '" name="' . $db->book_status . '">' . $status_name[$db->book_status] . '</div> <br>';
                // $status = $db->book_status;

                return $status;
            })
            ->addColumn('action', function ($db) {
                $actions = '';

                if ($db->book_status == 'pengajuan') {
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-primary" onclick="edit(\'' . $db->book_id . '\')" title="Edit Data"><i class="ti ti-edit"></i></a>';
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-danger" onclick="del(\'' . $db->book_id . '\')" title="Delete Data"><i class="ti ti-trash"></i></a>';
                } else if ($db->book_status == 'logistik') {
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-primary" onclick="edit(\'' . $db->book_id . '\')" title="Edit Data"><i class="ti ti-edit"></i></a>';
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-danger" onclick="del(\'' . $db->book_id . '\')" title="Delete Data"><i class="ti ti-trash"></i></a>';
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-info" onclick="showPenerimaanModal(\'' . $db->book_id . '\')" title="Input Tanggal Penerimaan"><i class="ti ti-calendar"></i></a>';
                } else if ($db->book_status == 'penerimaan') {
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-primary" onclick="edit(\'' . $db->book_id . '\')" title="Edit Data"><i class="ti ti-edit"></i></a>';
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-danger" onclick="del(\'' . $db->book_id . '\')" title="Delete Data"><i class="ti ti-trash"></i></a>';
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-info" onclick="showPenerimaanModal(\'' . $db->book_id . '\')" title="Input Tanggal Penerimaan"><i class="ti ti-calendar"></i></a>';
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-primary" onclick="showKetersediaanModal(\'' . $db->book_id . '\')" title="Input Tanggal Ketersediaan Buku"><i class="ti ti-calendar"></i></a>';
                } else if ($db->book_status == 'r_ketersediaan') {
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-primary" onclick="edit(\'' . $db->book_id . '\')" title="Edit Data"><i class="ti ti-edit"></i></a>';
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-danger" onclick="del(\'' . $db->book_id . '\')" title="Delete Data"><i class="ti ti-trash"></i></a>';
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-warning" onclick="editLogisticModal(\'' . $db->book_id . '\')" title="Ubah Data Pengajuan ke Logistik"><i class="ti ti-edit"></i></a>';
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-info" onclick="showPenerimaanModal(\'' . $db->book_id . '\')" title="Input Tanggal Penerimaan"><i class="ti ti-calendar"></i></a>';
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-primary" onclick="showKetersediaanModal(\'' . $db->book_id . '\')" title="Input Tanggal Ketersediaan Buku"><i class="ti ti-calendar"></i></a>';
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-success" onclick="showEmailModal(\'' . $db->book_id . '\')" title="Input Tanggal Konfirmasi Email"><i class="ti ti-calendar"></i></a>';
                } else if ($db->book_status == 's_email') {
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-primary" onclick="edit(\'' . $db->book_id . '\')" title="Edit Data"><i class="ti ti-edit"></i></a>';
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-danger" onclick="del(\'' . $db->book_id . '\')" title="Delete Data"><i class="ti ti-trash"></i></a>';
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-warning" onclick="editLogisticModal(\'' . $db->book_id . '\')" title="Ubah Data Pengajuan ke Logistik"><i class="ti ti-edit"></i></a>';
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-info" onclick="showPenerimaanModal(\'' . $db->book_id . '\')" title="Input Tanggal Penerimaan"><i class="ti ti-calendar"></i></a>';
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-primary" onclick="showKetersediaanModal(\'' . $db->book_id . '\')" title="Input Tanggal Ketersediaan Buku"><i class="ti ti-calendar"></i></a>';
                    $actions .= '<a href="javascript:void(0);" class="badge badge-center me-1 bg-success" onclick="showEmailModal(\'' . $db->book_id . '\')" title="Input Tanggal Konfirmasi Email"><i class="ti ti-calendar"></i></a>';
                }
            
                return $actions;
            })
            ->rawColumns(['book_status_view', 'action'])->toJson();
    }

    public function buku_save(Request $request)
    {

        // dd($request->all());
        $submission = new SubmissionModel();
        $inp = $request->input('inp');
        $dbs = $submission->find($request->input('id')) ?? new SubmissionModel();

        // Format the date 
        $date = Date::createFromFormat('d/m/Y', $inp['book_date_prodi_submission']);
        $inp['book_date_prodi_submission'] = $date->format('Y-m-d');

        $inp['book_status'] = 'pengajuan';
        // dd($inp);

        foreach ($inp as $key => $value) {
            $dbs->$key = $value;
        }
        // dd($dbs);

        $dbs->save();
        return response()->json(['status' => 'success', 'message' => 'Success to save data']);
    }

    public function buku_edit($id)
    {

        // dd($id);
        $data = SubmissionModel::where('book_id', $id)->first();
        // dd($data);  
        return response()->json($data);
    }
    public function buku_logistics(Request $request)
    {

        // dd($request->all());
        $submission = new SubmissionModel();
        $inp = $request->input('inp');
        $selected = json_decode($request->input('selectedIds'));
        $date = Date::createFromFormat('d/m/Y', $inp['book_date_logistic_submission']);
        $inp['book_date_logistic_submission'] = $date->format('Y-m-d');
        $inp['book_date_logistic_process'] = $inp['book_date_logistic_submission'];
        $inp['book_status'] = 'logistik';

        foreach ($selected as $id) {
            $submission = SubmissionModel::find($id);
            if ($submission) {
                foreach ($inp as $key => $value) {
                    $submission->$key = $value;
                }
                $submission->save();
            }
        }

        // dd($inp, $selected);
        // dd($inp);

        return response()->json(['status' => 'success', 'message' => 'Success to update data']);
    }

    public function buku_logistics_update(Request $request)
    {

        // dd($request->all());
        $submission = new SubmissionModel();
        $inp = $request->input('inp');
        $dbs = $submission->find($request->input('id')) ?? new SubmissionModel();

        // Format the date 
        // Check if the date format is correct before formatting
        if (strpos($inp['book_date_logistic_submission'], '/') !== false) {
            $date = Date::createFromFormat('d/m/Y', $inp['book_date_logistic_submission']);
            $inp['book_date_logistic_submission'] = $date->format('Y-m-d');
        }

        if ($dbs->book_status == 'pengajuan')  $inp['book_status']  = 'logistik';

        // dd($inp);

        foreach ($inp as $key => $value) {
            $dbs->$key = $value;
        }
        // dd($dbs);

        $dbs->save();
        return response()->json(['status' => 'success', 'message' => 'Success to save data']);
    }
    public function buku_penerimaan(Request $request)
    {

        // dd($request->all());
        $submission = new SubmissionModel();
        $inp = $request->input('inp');
        $dbs = $submission->find($request->input('id')) ?? new SubmissionModel();

        // Format the date 
        // Check if the date format is correct before formatting
        if (strpos($inp['book_date_acceptance'], '/') !== false) {
            $date = Date::createFromFormat('d/m/Y', $inp['book_date_acceptance']);
            $inp['book_date_acceptance'] = $date->format('Y-m-d');
        }

        if ($dbs->book_status == 'logistik')  $inp['book_status']  = 'penerimaan';

        $inp['book_total_price'] =     $inp['book_copy'] * $inp['book_procurement_price'];
        // dd($inp);

        foreach ($inp as $key => $value) {
            $dbs->$key = $value;
        }
        // dd($dbs);

        $dbs->save();
        return response()->json(['status' => 'success', 'message' => 'Success to save data']);
    }

    public function buku_ketersedian(Request $request)
    {

        // dd($request->all());
        $submission = new SubmissionModel();
        $inp = $request->input('inp');
        $dbs = $submission->find($request->input('id')) ?? new SubmissionModel();

        // Format the date 
        // Check if the date format is correct before formatting
        if (strpos($inp['book_date_available'], '/') !== false) {
            $date = Date::createFromFormat('d/m/Y', $inp['book_date_available']);
            $inp['book_date_available'] = $date->format('Y-m-d');
        }

        if ($dbs->book_status == 'penerimaan')  $inp['book_status']  = 'r_ketersediaan';

        // dd($inp);

        foreach ($inp as $key => $value) {
            $dbs->$key = $value;
        }
        // dd($dbs);

        $dbs->save();
        return response()->json(['status' => 'success', 'message' => 'Success to save data']);
    }

    public function buku_email(Request $request)
    {

        // dd($request->all());
        $submission = new SubmissionModel();
        $inp = $request->input('inp');
        $dbs = $submission->find($request->input('id')) ?? new SubmissionModel();

        // Format the date 
        // Check if the date format is correct before formatting
        if (strpos($inp['book_date_email_confirmed'], '/') !== false) {
            $date = Date::createFromFormat('d/m/Y', $inp['book_date_email_confirmed']);
            $inp['book_date_email_confirmed'] = $date->format('Y-m-d');
        }

        if ($dbs->book_status == 'r_ketersediaan')  $inp['book_status']  = 's_email';

        // dd($inp);

        foreach ($inp as $key => $value) {
            $dbs->$key = $value;
        }
        // dd($dbs);

        $dbs->save();
        return response()->json(['status' => 'success', 'message' => 'Success to save data']);
    }

    public function download()
    {
        $filePath = public_path('template/template_pengajuan_buku.xlsx');

        if (file_exists($filePath)) {
            return response()->download($filePath, 'template_pengajuan_buku.xlsx');
        } else {
            return response()->json(['error' => 'File not found.'], 404);
        }
    }

    public function upload(Request $request)
    {
            Excel::import(new UserImport, $request->file('formFile'));
            return response()->json(['status' => 'success', 'message' => 'File uploaded successfully']);

    }

    public function get_penerimaan($id)
    {
        $data = SubmissionModel::where('book_id', $id)->first();
        $data->id = $id;
        // dd($data);
        return response()->json($data);
    }
    public function get_ketersediaan($id)
    {
        $data = SubmissionModel::where('book_id', $id)->first();
        $data->id = $id;
        // dd($data);
        return response()->json($data);
    }
    public function get_email($id)
    {
        // dd($id);
        $data = SubmissionModel::where('book_id', $id)->first();
        $data->id = $id;
        // dd($data);
        return response()->json($data);
    }
    public function get_logistik($id)
    {
        $data = SubmissionModel::where('book_id', $id)->first();
        $data->id = $id;
        // dd($data);
        return response()->json($data);
    }

    public function buku_delete(Request $request)
    {

        $id = $request->id;
        $data = SubmissionModel::find($id);

        $data->delete();

        return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
    }
    public function getProdi(Request $request)
    {
        // dd($request->all());
        $id = $request->input('facultyId');
        $submission = new SubmissionModel();
        $prodi = $submission->getProdiByFacId($id);

        $temp = [];
        foreach ($prodi as $row) {
            $temp[$row->C_KODE_PRODI] = $row->NAMA_PRODI;
        }

        // dd($temp);  
        return response()->json($temp);
    }
}
