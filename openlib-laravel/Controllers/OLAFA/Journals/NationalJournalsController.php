<?php

namespace App\Http\Controllers\OLAFA\Journals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;

class NationalJournalsController extends Controller
{
    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        return view('olafa.jurnal.jurnalnasional');
    }
    public function dt(Request $request)
    {
        $data = DB::table('knowledge_item')
        ->select(
            'knowledge_item.id as id', 
            'title', 
            'publisher_name', 
            'isbn', 
            'published_year', 
            DB::raw('COUNT(knowledge_item.id) as eks')
        )
        ->leftJoin('knowledge_stock', 'knowledge_item.id', '=', 'knowledge_stock.knowledge_item_id')
        ->where('knowledge_item.knowledge_type_id', '42')
        ->groupBy('knowledge_item.id', 'title', 'publisher_name', 'isbn', 'published_year')
        ->orderBy('published_year', 'desc')
        ->get();

        // dd(['data' => $data]);

        return datatables($data)
            ->rawColumns(['action'])->toJson();
    }
}
