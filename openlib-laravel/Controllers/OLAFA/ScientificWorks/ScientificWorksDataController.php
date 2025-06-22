<?php

namespace App\Http\Controllers\OLAFA\ScientificWorks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KaryaIlmiahModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;

class ScientificWorksDataController extends Controller
{
    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        return view('olafa.karyaIlmiah.data');
    }

    public function dt()
    {
        $data = KaryaIlmiahModel::getProdiWithKnowledgeCount();

        return datatables($data)
            ->addColumn('action', function ($db) {
                return '<div class="btn-group">
                    <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="' . url('olafa/karya-ilmiah/detail/' . $db->c_kode_prodi) . '"><i class="ti ti-edit ti-sm me-2"></i>Detail Data</a></li>
                    </ul>
                </div>';
            })
            ->rawColumns(['action'])->toJson();

    }

    public function detail(Request $request)
    {

        $nama_prodi = KaryaIlmiahModel::getNamaProdiByKodeProdi($request->id);

        return view('olafa.karyaIlmiah.detail',[
            'kode_prodi' => $request->id,
            'nama_prodi' => $nama_prodi
        ]);
    }
    public function detail_dt(Request $request)
    {
        // dd($request->id);
        $data = KaryaIlmiahModel::getKnowledgeItemsByProdi($request->id);
        // dd($data->first());
        return datatables($data)->rawColumns(['action'])->toJson();
    }
}
