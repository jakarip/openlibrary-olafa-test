<?php
namespace App\Http\Controllers\Landingpage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\SelectTrait;
use App\Models\KnowledgeItemModel;
use App\Models\KnowledgeStockModel;
use App\Models\RentModel;
use Illuminate\Support\Facades\DB;
use App\Models\ItemLocation;
use App\Models\ItemLocationHour;
use App\Models\Holiday;
use Carbon\Carbon;

class Home extends Controller
{
    use SelectTrait;

    public function index(Request $request)
    {
        // Your existing code stays first
        $latest = KnowledgeStockModel::select('knowledge_item_id')
            ->whereIn('knowledge_type_id', [1, 29, 33, 40, 49, 59])
            ->groupBy('knowledge_item_id')
            ->orderBy(DB::raw('max(entrance_date)'), 'desc')
            ->limit('12')
            ->pluck('knowledge_item_id')->toArray();

        $year = date('Y');
        $popular = RentModel::selectRaw('knowledge_item_id, max(title) as title, max(stock_total) as total, max(stock_available) as available, max(stock_entrance_date) as tgl, max(name) as cat, max(cover_path) as cover')
            ->leftJoin('knowledge_stock', 'knowledge_stock_id', 'knowledge_stock.id')
            ->leftJoin('knowledge_item', 'knowledge_item_id', 'knowledge_item.id')
            ->leftJoin('knowledge_type', 'knowledge_item.knowledge_type_id', 'knowledge_type.id')
            ->whereIn('knowledge_item.knowledge_type_id', [1, 29, 33, 40, 49, 59])
            ->where('cover_path', '!=', '')
            ->where('stock_available', '>', '0')
            ->whereBetween(DB::raw('YEAR(rent_date)'), [$year - 1, $year])
            ->groupBy('knowledge_item_id')
            ->orderBy(DB::raw('count(*)'), 'desc')
            ->limit('6')
            ->get();

        /*$dbs = KnowledgeItemModel::select('knowledge_item.id', 'title', 'stock_total', 'stock_available', 'stock_entrance_date', 'name', 'cover_path')
            ->leftJoin('knowledge_type', 'knowledge_type_id', 'knowledge_type.id')
            ->whereIn('knowledge_item.id', array_merge($latest, $popular))
            ->get();

        $items = [];
        foreach($dbs as $db) {
            $items[$db->id] = $db;
        }*/
        //$order = $this->order();
        $knowledge_type = $this->select_knowledge_type_rentable();
        $knowledge_location = $this->select_knowledge_location();

        $pys = KnowledgeItemModel::select('published_year')->whereNotNull('published_year')->where('published_year', '!=', '0')->groupBy('published_year')->orderBy('published_year')->get();
        $published_year = ['' => 'Semua Tahun Terbit'];
        foreach ($pys as $py) {
            $published_year[$py->published_year] = $py->published_year;
        }

        $data = KnowledgeItemModel::with('classification', 'knowledgeType', 'stocks')->orderBy('entrance_date', 'desc')->paginate(30);

        // Now add the library hours code
        $today = Carbon::today()->format('Y-m-d');
        $dayName = strtolower(Carbon::today()->format('D'));

        $dayMap = [
            'sun' => 'open_sun',
            'mon' => 'open_mon',
            'tue' => 'open_tue',
            'wed' => 'open_wed',
            'thu' => 'open_thu',
            'fri' => 'open_fri',
            'sat' => 'open_sat',
        ];

        $col = $dayMap[$dayName];

        $locations = ItemLocation::active()->ordered()->get();
        $libraries = [];

        foreach ($locations as $loc) {
            $isHoliday = $loc->holidays()->where('holiday_date', $today)->exists();

            if ($isHoliday) {
                $hours = 'Tutup';
            } else {
                $special = $loc->specialHours()->where('ilh_date', $today)->first();

                if ($special) {
                    $hours = $special->ilh_hour;
                } else {
                    $hours = $loc->$col ?: 'Tutup';
                }
            }

            if ($hours === '-' || $hours === null || empty($hours)) {
                $hours = 'Tutup';
            }

            $libraries[] = [
                'name' => $loc->name,
                'hours' => $hours
            ];
        }

        return view('landingpage/index', compact('popular', 'latest', 'data', 'request', 'knowledge_type', 'knowledge_location', 'published_year', 'libraries'));
    }

    public function detail()
    {
        return view('landingpage/detail');
    }

    public function catalog(Request $request)
    {
        $knowledge_type = $this->select_knowledge_type_rentable();
        $knowledge_location = $this->select_knowledge_location();

        $pys = KnowledgeItemModel::select('published_year')->whereNotNull('published_year')->where('published_year', '!=', '0')->groupBy('published_year')->orderBy('published_year')->get();
        $published_year = ['' => 'Semua Tahun Terbit'];
        foreach ($pys as $py) {
            $published_year[$py->published_year] = $py->published_year;
        }

        $data = KnowledgeItemModel::with('classification', 'type', 'stock')->orderBy('entrance_date', 'desc')->paginate(30);
        return view('landingpage/catalog', compact('data', 'request', 'knowledge_type', 'knowledge_location', 'published_year'));
    }
}