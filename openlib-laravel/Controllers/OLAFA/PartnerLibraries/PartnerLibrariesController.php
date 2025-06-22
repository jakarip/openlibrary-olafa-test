<?php

namespace App\Http\Controllers\OLAFA\PartnerLibraries;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OLAFA\PartnerLibraries\PartnerLibrariesService;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
// use Illuminate\Http\File;

class PartnerLibrariesController extends Controller
{
    protected $service;

    public function __construct(PartnerLibrariesService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        return view('olafa.sumberPustaka.index');
    }

    public function dt()
    {
        $data = $this->service->getAllLibraries();

        return datatables()->of(collect($data))
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
    
    public function save(Request $request)
    {
        $request->validate([
            'inp.logo' => 'nullable|mimes:png,jpg,jpeg,webp',
        ]);

        $inp = $request->inp;
        $id = $request->id;

        // Simpan data dan logo
        $this->service->saveLibrary($inp, $request->file('inp.logo'), $id);

        return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan']);
    }

    public function getById($id)
    {
        $data = $this->service->getLibraryById($id);

        if (!$data) {
            return response()->json(['status' => 'error', 'message' => 'Data not found'], 404);
        }

        return response()->json($data);
    }

    public function delete(Request $request)
    {
        $id = $request->id;

        $success = $this->service->deleteLibrary($id);

        if (!$success) {
            return response()->json(['status' => 'error', 'message' => 'Data not found'], 404);
        }

        return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }
}
