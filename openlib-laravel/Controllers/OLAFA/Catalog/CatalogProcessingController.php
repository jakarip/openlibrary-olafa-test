<?php

namespace App\Http\Controllers\OLAFA\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KatalogModel;
use App\Services\OLAFA\Catalog\CatalogProcessingService;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CatalogProcessingController extends Controller
{
    protected $catalogProcessingService;

    public function __construct(CatalogProcessingService $catalogProcessingService)
    {
        $this->catalogProcessingService = $catalogProcessingService;
    }

    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        
        $tipe =  $this->catalogProcessingService->getType();
        $locations =  $this->catalogProcessingService->getLocations();
        return view('olafa.katalog.pengolahan', ['tipe' => $tipe, 'locations' => $locations]);
    }

    public function dt(Request $request)
    {
        $filters = $request->only(['startDate', 'endDate', 'type', 'origination', 'status', 'klasifikasi', 'barcode', 'location']);
        $data = $this->catalogProcessingService->getBooksOnProcess($filters);

        foreach ($data as $dt) {
            $dt->origination = $dt->origination == '1' ? 'Beli' : 'Sumbangan';
        }

        return datatables($data)->toJson();
    }

    public function ubah(Request $request, KatalogModel $katalog)
    {

        if ($request->has('ids')) {
            $this->catalogProcessingService->updateStatus($request->input('ids'), session()->get('userData')->id);
            return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'No IDs provided.']);
    }
}
