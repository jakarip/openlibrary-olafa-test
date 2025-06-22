<?php

namespace App\Http\Controllers\OLAFA\LibraryMaterials;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MappingKatalogModel;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class LibraryCatalogMappingController extends Controller
{
    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        $mappingKatalogModel = new MappingKatalogModel();
        $tipe = $mappingKatalogModel->get_type();
        return view('olafa.bahanPustaka.mapping',['tipe' => $tipe]);
    }

    public function dt(Request $request)
    {
        $mappingKatalogModel = new MappingKatalogModel();
        $tipe = $request->input('tipe');
        $searchtype = $request->input('searchtype');
        
        $searchwhere = "";
        if ($searchtype == "title") {
            $searchwhere = " and lower(title)";
        } else if ($searchtype == "author") {
            $searchwhere = " and lower(author)";
        } else if ($searchtype == "subject") {
            $searchwhere = " and lower(ks.name)";
        } else if ($searchtype == "all") {
            $searchwhere = " and (lower(kp.name) or lower(kt.code) or lower(title) or lower(cc.name) or lower(ks.name) or lower(author) or lower(published_year))";
        }
        
        $where = "and knowledge_type_id='$tipe' $searchwhere";
        // $limit = "LIMIT 20";
        $param = [
            'where' => $where,
            // 'limit' => $limit,
        ];

        $data = $mappingKatalogModel->dtquery($param);
        // dd( $data);
        return datatables($data)
            ->addColumn('action', function ($db) {
                return '<div class="btn-group">
                    <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="d-flex" ><a class="dropdown-item d-flex block  align-items-center" href="' . url('olafa/bahan-pustaka-mapping/detail/' . $db->id) . '"><i class="ti ti-edit ti-sm me-2"></i> Detail Data</a></li>
                    </ul>
                </div>';
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function detail($id)
    {
        $mappingKatalogModel = new MappingKatalogModel();
        $katalog = $mappingKatalogModel->getbyid($id);
        $curriculum_year = $mappingKatalogModel->getcurriculumyear();
        $study_program = $mappingKatalogModel->getstudyprogram();

        return view('olafa.bahanPustaka.mappingdetail',
        [
            'id' => $id,
            'katalog' => $katalog,
            'curriculum_year' => $curriculum_year,
            'study_program' => $study_program
        ]);
    }

    public function dt_not_list(Request $request)
    {
        $id = $request->input('id');
        $curriculum_year = $request->input('kurikulum');
        $study_program = $request->input('prodi');

        $data = DB::table('master_subject')
            ->leftJoin('t_mst_prodi', 'course_code', '=', 'c_kode_prodi')
            ->where('curriculum_code', $curriculum_year)
            ->whereNotIn('master_subject.id', function($query) use ($id) {
                $query->select('master_subject_id')
                    ->from('knowledge_item_subject')
                    ->where('knowledge_item_id', $id);
            })->get();

        if ($study_program != "all") {
            $data->where('course_code', $study_program);
        }

        return datatables($data)
        ->addColumn('action', function($data) {
            return '<input type="checkbox" value="'.$data->id.'" name="inp[id][]" class="dt-checkboxes form-check-input">';
        })
        ->editColumn('name', function($data) {
            return '<div class="text-left"><span style="color:green">'.$data->NAMA_PRODI.'</span><br>'.$data->name.'</div>';
        })
        ->rawColumns(['action','name'])
        ->toJson();
    }
    public function dt_list(Request $request)
    {
        $id = $request->input('id');
        $curriculum_year = $request->input('kurikulum');
        $study_program = $request->input('prodi');

        $data = DB::table('knowledge_item_subject as kis')
            ->leftJoin('master_subject as ms', 'ms.id', '=', 'kis.master_subject_id')
            ->leftJoin('t_mst_prodi as tp', 'tp.c_kode_prodi', '=', 'ms.course_code')
            ->where('kis.knowledge_item_id', $id)
            ->where('ms.curriculum_code', $curriculum_year)->get();

        if ($study_program != "all") {
            $data->where('ms.course_code', $study_program);
        }

        return datatables($data)
        ->addColumn('action', function($data) {
            return '<input type="checkbox" value="'.$data->id.'" name="inp[id][]" class="dt-checkboxes form-check-input">';
        })
        ->editColumn('name', function($data) {
            return '<div class="text-left"><span style="color:green">'.$data->NAMA_PRODI.'</span><br>'.$data->name.'</div>';
        })
        ->rawColumns(['action','name'])
        ->toJson();
    }

    public function insert(Request $request)
    {
        $knowledge_item_id = $request->input('id');
        $ids = $request->input('ids');

        $mappingKatalogModel = new MappingKatalogModel();

        foreach ($ids as $master_subject_id) {
            $item = [
                'master_subject_id' => $master_subject_id,
                'knowledge_item_id' => $knowledge_item_id
            ];
            if (!$mappingKatalogModel->checkExisting($master_subject_id, $knowledge_item_id)->isNotEmpty()) {
                $mappingKatalogModel->add($item);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Success to save data']);
    }

    public function delete(Request $request)
    {
        $knowledge_item_id = $request->input('id');
        $ids = $request->input('ids');

        $mappingKatalogModel = new MappingKatalogModel();

        foreach ($ids as $master_subject_id) {
            $mappingKatalogModel->deleteItem($master_subject_id, $knowledge_item_id);
        }

        return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
    }
}
