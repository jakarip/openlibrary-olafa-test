<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppsLanguagesModel;
use Illuminate\Support\Facades\Storage;

use App\Models\AppsModulesGroupModel;

class AppsLanguages extends Controller
{
    public function index()
    {
        $groups = AppsModulesGroupModel::where('amg_type', 'primary')->get();
        $data['groups'] = $groups;

        return view('config.apps-languages', $data);
    }

    public function dt(Request $request)
    {
        $group = $request->group;

        $data = AppsLanguagesModel::query();

        if($group && $group != ''){
            $data = $data->where('al_group', $group);
        }

        return datatables($data)
            ->editColumn('al_key', function ($db) {
                $value = $db->al_group . '.' . $db->al_key;
                return $value.'<button type="button" class="btn btn-link" onclick="copy(\''.$value.'\')"><i class="ti ti-copy"></i></button>';
            })
            ->editColumn('updated_at', function ($db) {
                return date('d/m/Y H:i:s', strtotime($db->updated_at));
            })
            ->addColumn('action', function ($db) {
                return '<div class="btn-group">
                    <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->al_id . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>
                        <li><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->al_id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>
                    </ul>
                </div>';
            })
            ->editColumn('al_lang_id', function($db){
                return '
                    <div class="languages" id="lang-id-'.$db->al_id.'">'.$db->al_lang_id.'</div>
                    <div id="col-lang-id-'.$db->al_id.'" class="d-flex align-items-center d-none" style="gap:10px">
                        <input class="form-control form-control-sm flex-fill" value="'.$db->al_lang_id.'" id="input-lang-id-'.$db->al_id.'" />
                        <button class="btn btn-primary languages-save" id="save-lang-id-'.$db->al_id.'">
                            <i class="ti ti-device-floppy"></i>
                        </button>
                        <button class="btn btn-secondary languages-close" id="close-lang-id-'.$db->al_id.'" >
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                ';
            })
            ->editColumn('al_lang_en', function($db){
                return '
                    <div class="languages" id="lang-en-'.$db->al_id.'">'.$db->al_lang_en.'</div>
                    <div id="col-lang-en-'.$db->al_id.'" class="d-flex align-items-center d-none" style="gap:10px">
                        <input class="form-control form-control-sm flex-fill" value="'.$db->al_lang_en.'" id="input-lang-en-'.$db->al_id.'" />
                        <button class="btn btn-primary languages-save" id="save-lang-en-'.$db->al_id.'">
                            <i class="ti ti-device-floppy"></i>
                        </button>
                        <button class="btn btn-secondary languages-close" id="close-lang-en-'.$db->al_id.'">
                            <i class="ti ti-x"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['action', 'al_key', 'al_lang_id', 'al_lang_en'])->toJson();
    }

    public function getbyid(Request $request)
    {
        return AppsLanguagesModel::find($request->id)->toJson();
    }

    public function save(Request $request)
    {
        try {
            $inp = $request->inp;
            $dbs = AppsLanguagesModel::find($request->id);
            if(!$dbs){
                $data = AppsLanguagesModel::where('al_group', $inp['al_group'])->where('al_key', $inp['al_key'])->first();
                if(!$data) $dbs = new AppsLanguagesModel();
                else {
                    return response()->json(['status' => 'error', 'message' => "{{ __('config.message_key_cant_duplicate') }}"]);
                }
            }

            foreach ($inp as $key => $value) {
                $dbs->$key = $value;
            }

            $dbs->save();

            return response()->json(['status' => 'success', 'message' => 'Success to save data']);
        } catch (\Throwable $th) {
            throw $th;
        }

        return response()->json(['status' => 'error', 'message' => 'Failed to save data']);
    }

    public function delete(Request $request)
    {
        $id = $request->id;

        $data = AppsLanguagesModel::find($id);
        $data->delete();

        return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
    }

    public function generate(Request $request)
    {
        $data = AppsLanguagesModel::where('al_group', $request->group)->get();

        $data_en = [
            // 'save' => 'Save',
            // 'cancel' => 'Cancel',
            // 'update' => 'Update',
            // 'message_success_delete' => 'Data Has Been Deleted'
        ];

        $data_id = [
            // 'save' => 'Simpan',
            // 'cancel' => 'Batal',
            // 'update' => 'Ubah',
            // 'message_success_delete' => 'Data Berhasil Dihapus'
        ];

        foreach($data as $key => $item){
            $data_en[$item->al_key] = $item->al_lang_en;
            $data_id[$item->al_key] = $item->al_lang_id;
        }

        Storage::disk('lang')->put('en/'.$request->group.'.php', "<?php\n\nreturn " . var_export($data_en, true) . ';');
        Storage::disk('lang')->put('id/'.$request->group.'.php', "<?php\n\nreturn " . var_export($data_id, true) . ';');

        return response()->json(['status' => 'success', 'message' => 'Success to generate data']);
    }
}
