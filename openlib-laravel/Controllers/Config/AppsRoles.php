<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppsRolesModel;
use App\Models\AppsModulesGroupModel;
use App\Models\AppsModulesNavModel;
use App\Models\AppsPermissionRoleModel;
use App\Models\MemberModel;

class AppsRoles extends Controller
{
    public function index()
    {
        return view('config.apps-roles');
    } 

    public function dt(Request $request)
    {
        $data = AppsRolesModel::query();

        return datatables($data)
            ->addColumn('action', function ($db) {
                return '<div class="btn-group">
                        <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->ar_id . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Instrumen</a></li>
                            <li><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->ar_id . '\')"><i class="ti ti-trash me-2"></i> Delete Instrumen</a></li>
                        </ul>
                    </div>';
            })
            ->editColumn('ar_name', function($db){
                return '<a href="'.url('config/apps/roles/mapping/'.$db->ar_id).'">'.$db->ar_name.'</a>';
            })
           ->rawColumns(['action', 'ar_name'])->toJson();
    }

    public function getbyid(Request $request)
    {
        return AppsRolesModel::find($request->id)->toJson();
    }

    public function save(Request $request)
    {
        try {
            $inp = $request->inp;
            $dbs = AppsRolesModel::find($request->id) ?? new AppsRolesModel();

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

        $data = AppsRolesModel::find($id);
        $data->delete();

        return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
    }

    public function mapping(Request $request, $id)
    {
        $navs = AppsModulesNavModel::with('module.permission', 'group')->get();

        $groups = $navs->where('amn_item_type', 'group');
        foreach($groups as $group){
            $group->childrens = $navs->where('amn_item_type', 'module')->where('amn_parent', $group->amn_id);
        }

        $data['groups'] = $groups;

        $permission = AppsPermissionRoleModel::where('apr_id_role', $id)->pluck('apr_id_permission', 'apr_id_permission');
        $data['permissions'] = $permission->toArray();

        $roles = AppsRolesModel::all();
        $data['roles'] = $roles;
        $role = AppsRolesModel::find($id);
        $data['role'] = $role; 
        $data['id'] = $id;
        
        return view('config.apps-roles-mapping', $data);
    }

    public function mappingSave(Request $request, $id){
        $permissions = array_keys($request->permission); 

        $role_permission = AppsPermissionRoleModel::where('apr_id_role', $id)->get(); 
        foreach($role_permission->whereIn('apr_id_permission', $permissions) as $item){
            $item->restore();
        }
        
        foreach($role_permission->whereNotIn('apr_id_permission', $permissions) as $item){
            $item->delete();
        }

        foreach($permissions as $ap_id){
            $data = AppsPermissionRoleModel::firstOrCreate([
                'apr_id_permission' => intval($ap_id),
                'apr_id_role' => intval($id)
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Success to save data']);
    }

    public function mappingUser(Request $request, $id)
    { 

        $roles = AppsRolesModel::all();
        $data['roles'] = $roles;
        $role = AppsRolesModel::find($id);
        $data['role'] = $role; 
        $data['id'] = $id;

        return view('config.apps-roles-mapping-user', $data);
    }

    public function users(Request $request){
        $data = MemberModel::where('master_data_fullname', 'ILIKE', '%'.$request->value.'%')->get();

        if($data && count($data) > 0)
            return json_encode(array('status' => '201', 'data' => $data));
        else return json_encode(array('status' => '400'));
    }

    public function mappingDt(Request $request)
    {
        $data = AppsRolesModel::query();

        return datatables($data)
            ->addColumn('action', function ($db) {
                return '<div class="btn-group">
                        <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->ar_id . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Instrumen</a></li>
                            <li><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->ar_id . '\')"><i class="ti ti-trash me-2"></i> Delete Instrumen</a></li>
                        </ul>
                    </div>';
            })
            ->editColumn('ar_name', function($db){
                return '<a href="'.url('config/apps/roles/mapping/'.$db->ar_id).'">'.$db->ar_name.'</a>';
            })
           ->rawColumns(['action', 'ar_name'])->toJson();
    }

    public function mappingUserSave(Request $request)
    {
        try {
            $inp = $request->inp;
            $dbs = AppsRolesModel::find($request->id) ?? new AppsRolesModel();

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

    public function user_list($id)
    {
        $role_id = $id;
        $data = MemberModel::where('member_type_id', $role_id);

        return datatables($data)
            ->addColumn('action', function ($db) {
                return '<div class="btn-group">
                        <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>
                        </ul>
                    </div>';
            }) 
           ->rawColumns(['action'])->toJson();
    }

    public function user_assign(Request $request, $id){
        $user_id = $request->user_id;
        $data = MemberModel::find($user_id);
        $data->member_type_id = $id;
        $data->save();
        return response()->json(['status' => 'success', 'message' => 'Success to save data']);
    }
    
    public function del_assign(Request $request){
        $user_id = $request->user_id;
        $data = MemberModel::find($user_id);
        $data->member_type_id = null;
        $data->save();
        return response()->json(['status' => 'success', 'message' => 'Success to save data']);
    }
}
