<?php

namespace App\Http\Controllers\OLAFA\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanUtilisasiModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ReportUtilizationController extends Controller
{
    public function index (){

        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        $laporan = new LaporanUtilisasiModel();
        $prodi = $laporan->prodi();

        // dd($prodi);
        return view('olafa.laporan.index',['prodi' => $prodi]);
    }

    public function ajax(Request $request, LaporanUtilisasiModel $laporanModel)  {

        $start = $request->input('startDate')." 00:00:00";
        $end = $request->input('endDate')." 23:59:59";
        $prodi = $request->input('prodi');

        // $start = date("2021-09-01 00:00:00");
        // $end = date("2021-10-01 23:59:59");
        // dd($start, $end, $prodi);

        $data = [];
        $data['report']['pengunjung'] = $laporanModel->pengunjung($start, $end, $prodi)->total;
        $data['report']['peminjaman'] = $laporanModel->peminjaman($start, $end, $prodi)->total;
        $data['report']['pengembalian'] = $laporanModel->pengembalian($start, $end, $prodi)->total;
        $data['report']['bebaspustaka'] = $laporanModel->bebaspustaka($start, $end, $prodi)->total;

        $data['report']['4'] = $laporanModel->tapa_based_on_bebaspustaka_date($start, $end, '4', $prodi)->total;
        $data['report']['3'] = $laporanModel->tapa_based_on_bebaspustaka_date($start, $end, '3', $prodi)->total;
        $data['report']['52'] = $laporanModel->tapa_based_on_bebaspustaka_date($start, $end, '52', $prodi)->total;
        $data['report']['64'] = $laporanModel->tapa_based_on_bebaspustaka_date($start, $end, '64', $prodi)->total;
        $data['report']['53'] = $laporanModel->tapa_based_on_bebaspustaka_date($start, $end, '53', $prodi)->total;
        $data['report']['91'] = $laporanModel->tapa_based_on_bebaspustaka_date($start, $end, '91', $prodi)->total;
        // $data['sumbangan_buku'] = $laporanModel->sumbangan_buku($start, $end, $prodi);
        // $data['sumbangan_ebook'] = $laporanModel->sumbangan_ebook($start, $end, $prodi);

        // Transform tapa_readonly data
        $tapaReadonly = $laporanModel->tapa_transaksi_readonly($end);
        $transformedTapaReadonly = [];
        foreach ($tapaReadonly as $item) {
            $transformedTapaReadonly[] = [
                'id' => $item->id,
                'type' => $item->type,
                'year' => $item->year,
                'data' => [
                    [
                        'januari' => $item->januari,
                        'februari' => $item->februari,
                        'maret' => $item->maret,
                        'april' => $item->april,
                        'mei' => $item->mei,
                        'juni' => $item->juni,
                        'juli' => $item->juli,
                        'agustus' => $item->agustus,
                        'september' => $item->september,
                        'oktober' => $item->oktober,
                        'november' => $item->november,
                        'desember' => $item->desember,
                    ]
                ]
            ];
        }
        $data['report']['tapa_readonly'] = $transformedTapaReadonly;

        // Transform ebook_readonly data
        $ebookReadonly = $laporanModel->ebook_transaksi_readonly($end);
        $transformedEbookReadonly = [];
        foreach ($ebookReadonly as $item) {
            $transformedEbookReadonly[] = [
                'id' => $item->id,
                'type' => $item->type,
                'year' => $item->year,
                'data' => [
                    [
                        'januari' => $item->januari,
                        'februari' => $item->februari,
                        'maret' => $item->maret,
                        'april' => $item->april,
                        'mei' => $item->mei,
                        'juni' => $item->juni,
                        'juli' => $item->juli,
                        'agustus' => $item->agustus,
                        'september' => $item->september,
                        'oktober' => $item->oktober,
                        'november' => $item->november,
                        'desember' => $item->desember,
                    ]
                ]
            ];
        }
        $data['report']['ebook_readonly'] = $transformedEbookReadonly;

        // Transform visitor_openlib data
        $visitorOpenlib = $laporanModel->visitor_openlib($end);
        $transformedVisitorOpenlib = [];
        foreach ($visitorOpenlib as $item) {
            $transformedVisitorOpenlib[] = [
                'id' => $item->id,
                'type' => $item->type,
                'year' => $item->year,
                'data' => [
                    [
                        'januari' => $item->januari,
                        'februari' => $item->februari,
                        'maret' => $item->maret,
                        'april' => $item->april,
                        'mei' => $item->mei,
                        'juni' => $item->juni,
                        'juli' => $item->juli,
                        'agustus' => $item->agustus,
                        'september' => $item->september,
                        'oktober' => $item->oktober,
                        'november' => $item->november,
                        'desember' => $item->desember,
                    ]
                ]
            ];
        }
        $data['report']['visitor_openlib'] = $transformedVisitorOpenlib;

        // Transform visitor_eproc data
        $visitorEproc = $laporanModel->visitor_eproc($end);
        $transformedVisitorEproc = [];
        foreach ($visitorEproc as $item) {
            $transformedVisitorEproc[] = [
                'id' => $item->id,
                'type' => $item->type,
                'year' => $item->year,
                'data' => [
                    [
                        'januari' => $item->januari,
                        'februari' => $item->februari,
                        'maret' => $item->maret,
                        'april' => $item->april,
                        'mei' => $item->mei,
                        'juni' => $item->juni,
                        'juli' => $item->juli,
                        'agustus' => $item->agustus,
                        'september' => $item->september,
                        'oktober' => $item->oktober,
                        'november' => $item->november,
                        'desember' => $item->desember,
                    ]
                ]
            ];
        }
        $data['report']['visitor_eproc'] = $transformedVisitorEproc;

       


        // dd($data);
        return response()->json($data);
    }
}
