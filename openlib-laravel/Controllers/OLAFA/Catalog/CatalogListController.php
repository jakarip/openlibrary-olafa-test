<?php

namespace App\Http\Controllers\OLAFA\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KatalogModel;
use App\Services\OLAFA\Catalog\CatalogListService;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CatalogListController extends Controller
{
    protected $catalogListService;

    public function __construct(CatalogListService $catalogListService)
    {
        $this->catalogListService = $catalogListService;
    }

    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        
        $koleksi = $this->catalogListService->getKnowledgeType();
        return view('olafa.katalog.list-katalog', ['koleksi' => $koleksi]);
    }

    public function dt(Request $request)
    {
        $filters = $request->only(['startDate', 'endDate', 'klasifikasi', 'type']);
        $data = $this->catalogListService->getCatalogList($filters);
        return datatables($data)->toJson();
    }
}
