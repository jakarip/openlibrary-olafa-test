<?php

namespace App\Http\Controllers\OLAFA\LibraryMaterials;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BahanPustakaModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class LibraryMaterialDataController extends Controller
{
    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        return view('olafa.bahanPustaka.data');
    }

    public function dt(Request $request)
    {
        $year = $request->input('year');
        $growYear = $request->input('grow_year');
        $faculty = $request->input('faculty');

        $bahanPustakaModel = new BahanPustakaModel();
        $data = $bahanPustakaModel->getBahanPustaka($year, $growYear, $faculty);
        $total = $bahanPustakaModel->totalCollection($year, $growYear, $faculty);
        // dd($request->input());
        // dd($data);

        return datatables($data)
            ->addColumn('action', function ($db) use ($year, $growYear) {
                return '<div class="btn-group">
                    <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="d-flex" ><a class="dropdown-item d-flex  align-items-center" href="' . url('olafa/bahan-pustaka/detail/' . $db->c_kode_prodi) .'/'. $year. '"><i class="ti ti-edit ti-sm me-2"></i> Detail Data</a></li>
                    </ul>
                </div>';
            })
            ->editColumn('judul_fisik', function ($db) {
                return '<div class="td_right"><small>' . $db->judul_fisik . ' ' . 'judul' . '</small></div>';
            })
            ->editColumn('eks_fisik', function ($db) {
                return '<div class="td_right"><small>' . $db->eks_fisik . ' ' . 'Eksemplar' . '</small></div>';
            })
            ->editColumn('judul', function ($db) {
                return '<div class="td_right"><small>' . $db->judul . ' ' .'Judul' . '</small></div>';
            })
            ->editColumn('eks', function ($db) {
                return '<div class="td_right"><small>' . $db->eks . ' ' .'Judul' . '</small></div>';
            })
            ->editColumn('total_judul', function ($db) {
                return '<div class="td_right"><small>' . ($db->judul_fisik + $db->judul) . ' ' . 'Judul' . '</small></div>';
            })
            ->editColumn('total_eks', function ($db) {
                return '<div class="td_right"><small>' . ($db->eks_fisik + $db->eks) . ' ' .'Eksemplar' . '</small></div>';
            })
            ->editColumn('mk', function ($db) {
                return '<div class="td_right"><small>' . $db->mk . ' ' . 'Matakuliah' . '</small></div>';
            })
            ->editColumn('mkadabuku', function ($db) {
                return '<div class="td_right"><small>' . $db->mkadabuku . ' ' . 'Matakuliah' . '</small></div>';
            })
            ->editColumn('percentage', function ($db) {
                $percentage = $db->mk > 0 ? number_format((float)$db->mkadabuku / $db->mk * 100, 2, '.', '') : 0;
                return '<div class="td_right"><small>' . $percentage . '%</small></div>';
            })
            ->with('total', $total)
            ->rawColumns(['action', 'judul_fisik', 'eks_fisik', 'judul', 'eks', 'total_judul', 'total_eks', 'mk', 'mkadabuku', 'percentage'])
            ->toJson();
    }
    public function detail(Request $request)
    {

        $bahanPustakaModel = new BahanPustakaModel();
        $nama_prodi = $bahanPustakaModel->getJurusanByKodeJur($request->id);
        $kode_prodi = $request->id;
        $tahun = $request->year;

        // dd($kode_prodi, $tahun);

        return view('olafa.bahanPustaka.detail',[
            'kode_prodi' => $kode_prodi,
            'tahun' => $tahun,
            'nama_prodi' => $nama_prodi
        ]);
    }

    public function detail_dt(Request $request)
    {
        // dd($request->year);
        // dd($request->id);

        $bahanPustakaModel = new BahanPustakaModel();

        $data = $bahanPustakaModel->getMKByKodeJur($request->id, $request->year);
        // dd($data);
        return datatables($data)
            ->addColumn('action', function ($db) {
                return '<div class="btn-group">
                    <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="d-flex"><a class="dropdown-item d-flex  align-items-center" href="javascript:showModal(\'' . $db->id . '\', \'book\')"><i class="ti ti-book ti-sm me-2"></i>'. __('olafas.library_materials.book_detail') .'</a></li>
                        <li class="d-flex"><a class="dropdown-item  d-flex  align-items-center" href="javascript:showModal(\'' . $db->id . '\', \'ebook\')"><i class="ti ti-book ti-sm me-2"></i>'. __('olafas.library_materials.ebook_detail') . '</a></li>
                    </ul>
                </div>';
            })
            ->rawColumns(['action'])->toJson();
    }

    public function detail_buku(Request $request)
    {
        $bahanPustakaModel = new BahanPustakaModel();
        $data = $bahanPustakaModel->getBukuRef($request->id_mk,$request->type);
        // dd($data);

        return datatables($data)->toJson();
        // return response()->json($data);
    }
}
