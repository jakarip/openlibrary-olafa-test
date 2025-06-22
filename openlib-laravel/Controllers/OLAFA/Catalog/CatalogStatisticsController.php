<?php

namespace App\Http\Controllers\OLAFA\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OLAFA\Catalog\CatalogStatisticsService;
use App\Models\KatalogModel;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CatalogStatisticsController extends Controller
{
    protected $catalogStatisticsService;

    public function __construct(CatalogStatisticsService $catalogStatisticsService)
    {
        $this->catalogStatisticsService = $catalogStatisticsService;
    }

    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        
        $locations = $this->catalogStatisticsService->getLocations();
        return view('olafa.katalog.statistik', ['locations' => $locations]);
    }

    public function dt(Request $request)
    {
        $filters = $request->only(['startDate', 'endDate', 'location']);
        $data = $this->catalogStatisticsService->getStatistics($filters);
        return datatables($data)->toJson();
    }

    public function dt_detail(Request $request)
    {
        $id = $request->input('id');
        $type = $request->input('type');
        $data = $this->catalogStatisticsService->getDetailStatistics($id, $type);
        return datatables($data)->toJson();
    }

}
