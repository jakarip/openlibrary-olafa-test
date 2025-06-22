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

class MemberCirculationController extends Controller
{
    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        return view('olafa.anggota.sirkulasi');
    }

    public function dt(Request $request, MemberAttendanceModel $anggotaModel)
    {
        $dates = $request->input('selectedDate') ?? date('m-Y');

        $data = [];
        $data['jurusan'] = collect($anggotaModel->getallProdi())->take(7);
        // $data['jurusan'] = collect($anggotaModel->getallProdi());
        $results = [];

        $no = 1;  
        foreach($data['jurusan'] as $row) { 
            $downloads = $anggotaModel->getJumlahDownloadByTanggal($row->c_kode_prodi, $dates); 
            $peminjaman = $anggotaModel->getJumlahPeminjamanByTanggal($row->c_kode_prodi, $dates);
            $pengembalian = $anggotaModel->getJumlahPengembalianByTanggal($row->c_kode_prodi, $dates);
            $results[] = [
                'fakultas' => $row->nama_fakultas,
                'prodi' => $row->nama_prodi,
                'downloads' => $downloads,
                'peminjaman' => $peminjaman,
                'pengembalian' => $pengembalian,
            ];
            $no++;
        }
        
        $lectureDownload = $anggotaModel->getJumlahDownloadNonMahasiswaByTanggal($dates);
        $lectureRent = $anggotaModel->getJumlahPeminjamanNonMahasiswaByTanggal($dates);
        $lectureReturn = $anggotaModel->getJumlahPengembalianNonMahasiswaByTanggal($dates);

        $results[] = [
            'fakultas' => 'Lecture/Employee',
            'prodi' => '',
            'downloads' => $lectureDownload,
            'peminjaman' => $lectureRent,
            'pengembalian' => $lectureReturn,
        ];


        return datatables()->of(collect($results))->toJson();
    }
}
