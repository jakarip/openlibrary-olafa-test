<?php

namespace App\Http\Controllers\OLAFA\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KatalogModel;
use App\Services\OLAFA\Catalog\CollectionListService;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CollectionListController extends Controller
{
    protected $collectionListService;

    public function __construct(CollectionListService $collectionListService)
    {
        $this->collectionListService = $collectionListService;
    }

    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        $koleksi = $this->collectionListService->getKnowledgeType();
        $locations = $this->collectionListService->getLocations();
        return view('olafa.katalog.list-koleksi', ['koleksi' => $koleksi, 'locations' => $locations]);
    }

    public function dt(Request $request)
    {
        $filters = $request->only(['startDate', 'endDate', 'status', 'type', 'klasifikasi', 'origination', 'location']);
        $data = $this->collectionListService->getCollectionList($filters);
        return datatables($data)->toJson();
    }
}
