<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Catalog\LocationModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class MsCatalogLocation extends Controller
{
    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        return view('config.catalog.location');
    }

    public function dt(Request $request)
    {
        $data = LocationModel::with('collection');

        return datatables($data)
            ->addColumn('action', function ($db) {

                if(auth()->canAtLeast(array('config-catalog-location.edit','config-catalog-location.delete'))){
                    $btn = '<div class="btn-group">
                        <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                    ';
                    if(auth()->can('config-catalog-location.update')){
                        $btn .='<li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->id . '\')"><i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>';
                    }
                    if(auth()->can('config-catalog-location.delete')){
                        $btn .='<li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->id . '\')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>';
                    }
                    $btn.=' </ul>
                    </div>';
                }
                return $btn;
            })
            ->addColumn('collection', function($db){
                return count($db->collection);
            })
           ->rawColumns(['action'])->toJson();
    }

    public function getbyid(Request $request)
    {
        return LocationModel::find($request->id)->toJson();
    }

    public function save(Request $request)
    {
        try {
            $inp = $request->inp;
            $dbs = LocationModel::find($request->id) ?? new LocationModel();

            foreach ($inp as $key => $value) {
                $dbs->$key = $value;
            }

            if(!$dbs->created_by)
            $dbs->created_by = session()->get('userData')->id;
            $dbs->updated_by = session()->get('userData')->id;
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

        $data = LocationModel::find($id);
        $data->delete();

        return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
    }
}
