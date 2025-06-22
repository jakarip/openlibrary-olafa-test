<?php

namespace App\Http\Controllers\Pengadaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DashboardPengadaanModel;
use App\Models\SubmissionModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;;

class DashboardPengadaan extends Controller
{
    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        return view('pengadaan.dashboard.index');
    }

    private function formatDate($date)
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

    public function ajax(Request $request, DashboardPengadaanModel $DashboardModel)
    {
        
        // Extract the date range from the request
        $dateRange = $request->input('year');
        list($start, $end) = explode(' - ', $dateRange);

        // Format the dates
        $startDate = $this->formatDate($start);
        $endDate = $this->formatDate($end);
        // dd($startDate, $endDate);
        $data = array();
        $data['total'] = $DashboardModel->total_pengajuan($startDate, $endDate);
        $data['total_telupress'] = $DashboardModel->total_pengajuan_telupress($startDate, $endDate);
        $data['rerata_penerimaan'] = $DashboardModel->rerata_hari_status_penerimaan($startDate, $endDate);
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        // dd($data);
        // Return the result as JSON
        return response()->json($data);
    }

    public function detail()
    {
        $submission = new SubmissionModel();
        $faculty = $submission->getFaculty();
        $prodi = $submission->getProdi();
        // dd($faculty, $prodi);   
        return view('pengadaan.dashboard.detail', [
            'faculty' => $faculty,
            'prodi' => $prodi,

        ]);
    }

    public function ajax_detail(Request $request, DashboardPengadaanModel $DashboardModel)
    {
        $dateRange = $request->input('year');
        $faculty = $request->input('faculty');
        $prodi = $request->input('prodi');
        // dd($dateRange, $faculty, $prodi);
        list($start, $end) = explode(' - ', $dateRange);

        // Format the dates
        $startDate = $this->formatDate($start);
        $endDate = $this->formatDate($end);

        $submission = new SubmissionModel();
        $data = [];

        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        $data['faculty'] = $submission->getFaculty();
        if ($faculty == 'all' || $faculty == '') {
            $data['info'] = 'faculty_all';
            foreach ($data['faculty'] as $row) {
                $dt = $DashboardModel->total_pengajuan_faculty($startDate, $endDate, $row->c_kode_fakultas);
                $data['total'][$row->c_kode_fakultas] = $dt;
                $data['rerata_penerimaan'][$row->c_kode_fakultas] = $DashboardModel->rerata_hari_status_penerimaan_faculty($startDate, $endDate, $row->c_kode_fakultas);
            }
        } else {
            $data['prodi'] = $submission->getProdiByFacId($faculty);
            if ($prodi == 'all' || $prodi == '') {
                $prod = $data['prodi'];
                $data['prod'] = $data['prodi'];
            } else {
                $prod = $submission->getProdiByProdId($prodi);
                $data['prod'] = $prod;
            }

            foreach ($prod as $row) {
                $dt =$DashboardModel->total_pengajuan_prodi($startDate, $endDate, $row->C_KODE_PRODI);
                $data['total'][$row->C_KODE_PRODI] = $dt;
                $data['rerata_penerimaan'][$row->C_KODE_PRODI] = $DashboardModel->rerata_hari_status_penerimaan_prodi($startDate, $endDate, $row->C_KODE_PRODI);
            }
        }
        // dd($data);
        return response()->json($data);

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
