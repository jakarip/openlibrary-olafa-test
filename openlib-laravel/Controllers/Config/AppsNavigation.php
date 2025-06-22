<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppsModulesNavModel; 
use App\Models\AppsModulesModel;
use App\Models\AppsModulesGroupModel;

class AppsNavigation extends Controller
{
    public function index()
    {
        \Menu::make('MyNavBar', function ($menu) {
            $items = AppsModulesNavModel::ViewQuery()->whereNull('deleted_at');

            foreach ($items as $item) 
            {
                $title = "item_" . $item->amn_item_type . '_id';
                $url = $item->amn_item_type == 'group' ? '[Group]' : '[Module] &nbsp; '.$item->item_module_url;

                if ($item->amn_parent == 0 or empty($item->amn_parent) or is_null($item->amn_parent)) {
                    $menu->add($item->$title, $url)->id($item->amn_id)
                        ->append('</span><small class="url">' . $url . '</small>')
                        ->prepend('<span>');
                } else {
                    if ($menu->find($item->amn_parent)) {
                        $menu->find($item->amn_parent)->add($item->$title, $url)->id($item->amn_id)
                            ->append('</span><small class="url">' . $url . '</small>')
                            ->prepend('<span>');
                    }
                }
            }
        });

        $data['modules'] = AppsModulesModel::all();
        $data['groups'] = AppsModulesGroupModel::all();
        
        return view('config.apps-navigation', $data);
    }

    public function order(Request $request)
    {
        $menuItemOrder = json_decode($request->order);

        $this->orderMenu($menuItemOrder, 0);

        return response()->json(['status' => 'success', 'message' => '']);
    }

    private function orderMenu(array $menuItems, $parentId)
    {
        foreach ($menuItems as $index => $menuItem) {
            $item = AppsModulesNavModel::findOrFail($menuItem->id);
            $item->amn_order = $index + 1;
            $item->amn_parent = $parentId;
            $item->save();

            if (isset($menuItem->children)) {
                $this->orderMenu($menuItem->children, $menuItem->id);
            }
        }
    }

    public function getbyid(Request $request)
    {
        return AppsModulesNavModel::find($request->id)->toJson();
    }

    public function save(Request $request)
    {
        try {
            $inp = $request->inp;
            $dbs = AppsModulesNavModel::find($request->id) ?? new AppsModulesNavModel();

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

        $data = AppsModulesNavModel::find($id);
        $data->delete();

        return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
    }

    public function addModule(Request $request){
        $module_id = $request->module_id;
        try {
            $module = AppsModulesModel::find($module_id);
            
            if($module){
                $data = new AppsModulesNavModel();
                $data->amn_item_id = $module_id;
                $data->amn_item_type = 'module';
                $data->amn_order = 0;
                $data->amn_parent = 0;
                $data->save();
            }
            
            return response()->json(['status' => 'success', 'message' => 'Success to save data']);

        } catch (\Throwable $th) {
            throw $th;
        }
        return response()->json(['status' => 'error', 'message' => 'Failed to save data']);
    }

    public function addGroup(Request $request){
        $module_id = $request->module_id;
        try {
            $module = AppsModulesGroupModel::find($module_id);
            
            if($module){
                $data = new AppsModulesNavModel();
                $data->amn_item_id = $module_id;
                $data->amn_item_type = 'group';
                $data->amn_order = 0;
                $data->amn_parent = 0;
                $data->save();
            }
            
            return response()->json(['status' => 'success', 'message' => 'Success to save data']);

        } catch (\Throwable $th) {
            throw $th;
        }
        return response()->json(['status' => 'error', 'message' => 'Failed to save data']);
    }
}
