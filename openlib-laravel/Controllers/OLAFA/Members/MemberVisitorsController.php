<?php

namespace App\Http\Controllers\OLAFA\Members;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AnggotaModel;
use App\Models\MemberAttendanceModel;
use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;

class MemberVisitorsController extends Controller
{
    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        return view('olafa.anggota.pengunjung');
    }

    public function dt(Request $request, MemberAttendanceModel $anggotaModel)
    {
        $data = [];
        $data['jurusan'] = $anggotaModel->getallProdi();

        // Validate and set dates
        $start = $request->input('startDate');
        $end = $request->input('endDate');

        if ($start && $end) {
            $dates = [$start, $end];
        } else {
            $dates = [date('Y-m') . "-01", date('Y-m-d')];
        }

        $no = 1;
        foreach (array_slice($data['jurusan'], 0, 10) as $row) {
            // $dayResult = $anggotaModel->getJumlahPenunjungByTanggal($row->c_kode_prodi, $dates, 'day');
            $dayCount = $anggotaModel->getJumlahPengunjungByTanggal($row->c_kode_prodi, $dates, 'day');
            // $nightResult = $anggotaModel->getJumlahPenunjungByTanggal($row->c_kode_prodi, $dates, 'night');
            $nightCount = $anggotaModel->getJumlahPengunjungByTanggal($row->c_kode_prodi, $dates, 'night');
            // $data['day'][$no] = $dayResult[0]->total ?? 0;
            $data['day'][$no] = $dayCount;
            // $data['night'][$no] = $nightResult[0]->total ?? 0;
            $data['night'][$no] = $nightCount;
            $no++;
        }

        $dosenDayCount = $anggotaModel->getJumlahPengunjungDosenPegawaiByTanggal($dates, 'day');
        $dosenNightCount = $anggotaModel->getJumlahPengunjungDosenPegawaiByTanggal($dates, 'night');

        $data['day'][$no] = $dosenDayCount;
        $data['night'][$no] = $dosenNightCount;

        $no++;

        $publicDayCount = $anggotaModel->getJumlahPengunjungPublicByTanggal($dates, 'day');
        $publicNightCount = $anggotaModel->getJumlahPengunjungPublicByTanggal($dates, 'night');

        $data['day'][$no] = $publicDayCount;
        $data['night'][$no] = $publicNightCount;

        // Convert data to array of arrays for DataTables
        $formattedData = [];
        foreach (array_slice($data['jurusan'], 0, 10) as $index => $jurusan) {
            $formattedData[] = [
                'prodi' => $jurusan->nama_prodi,
                'fakultas' => $jurusan->nama_fakultas,
                'day' => $data['day'][$index + 1],
                'night' => $data['night'][$index + 1],
            ];
        }

        // Add dosen and public counts to formatted data
        $formattedData[] = [
            'prodi' => '',
            'fakultas' => 'Dosen/Pegawai',
            'day' => $dosenDayCount,
            'night' => $dosenNightCount,
        ];

        $formattedData[] = [
            'prodi' => '',
            'fakultas' => 'Public',
            'day' => $publicDayCount,
            'night' => $publicNightCount,
        ];

        return datatables()->of(collect($formattedData))->toJson();
    }
}
