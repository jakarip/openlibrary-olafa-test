<?php

namespace App\Http\Controllers\OLAFA\Journals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\InternationalProdiModel;
use App\Models\InternationalModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;

class InternationalJournalsByProgramController extends Controller
{
    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        $journals = InternationalModel::all();
        return view('olafa.jurnal.jurnalinternasionalprodi', [
            'journals' => $journals
        ]);
    }

    public function dt()
    {
        $data = InternationalProdiModel::getProdiWithJournals();

        return datatables($data)
            ->addColumn('action', function ($db) {
                return '<div class="btn-group">
                    <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->c_kode_prodi . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>
                        
                    </ul>
                </div>';
            })
            ->rawColumns(['action','journal'])->toJson();
    }

    public function getById(Request $request)
    {
        $selectedJournals = InternationalProdiModel::where('prodi_code', $request->id)->pluck('io_id')->toArray();
        $journals = InternationalModel::all();
        
        return response()->json([
            'selectedJournals' => $selectedJournals,
            'journals' => $journals,
        ]);
    }

    public function save(Request $request)
    {
        try {
            
            $prodiCode = $request->id;
            $journalIds = $request->input('inp.io_id', '');

            // Delete existing records for the given prodi_code
            InternationalProdiModel::where('prodi_code', $prodiCode)->delete();

            // Insert new records
            if (!empty($journalIds)) {
                foreach ($journalIds as $journalId) {
                    InternationalProdiModel::create([
                        'prodi_code' => $prodiCode,
                        'io_id' => $journalId
                    ]);
                }
            }

            return response()->json(['status' => 'success', 'message' => 'Success to save data']);
        } catch (\Throwable $th) {
            throw $th;
        }

        // return response()->json(['status' => 'error', 'message' => 'Failed to save data']);
    }
}
