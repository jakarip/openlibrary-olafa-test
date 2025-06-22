<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockopnameModel extends Model
{
    protected $connection = 'mysql';
    protected $table = 'batik.so_edition';
    protected $primaryKey = 'so_id';
    public $timestamps = false;

    public function getSOEdition()
    {
        return DB::table('batik.so_edition')
            ->select('so_id', 'so_name', 'so_status', 'so_date')
            ->orderBy('so_status', 'desc')
            ->get();
    }

    public function getStockDetails($id, $jeniskatalog = null, $lokasiopenlib = null, $lokasiso = null, $statusopenlib = null, $statusso = null, $condition = null, $userId = null)
    {
        $query = DB::table('batik.so_stock')
            ->leftJoin('batik.member', 'so_stock.sos_id_user', '=', 'member.id')
            ->leftJoin('batik.knowledge_stock', 'so_stock.sos_id_stock', '=', 'knowledge_stock.id')
            ->leftJoin('batik.knowledge_item', 'knowledge_stock.knowledge_item_id', '=', 'knowledge_item.id')
            ->leftJoin('batik.classification_code', 'knowledge_item.classification_code_id', '=', 'classification_code.id')
            ->leftJoin('batik.knowledge_type', 'knowledge_item.knowledge_type_id', '=', 'knowledge_type.id')
            ->leftJoin('batik.item_location AS loc_openlibrary', 'knowledge_item.item_location_id', '=', 'loc_openlibrary.id')
            ->leftJoin('batik.item_location AS loc_so', 'so_stock.sos_id_location', '=', 'loc_so.id')
            ->select(
                'so_stock.sos_id_user',
                'so_stock.sos_id',
                'so_stock.sos_date',
                'member.master_data_fullname',
                'member.master_data_user',
                'knowledge_type.name AS jenis_katalog',
                'knowledge_item.title',
                'knowledge_item.code AS no_katalog',
                'knowledge_stock.code AS barcode',
                'loc_openlibrary.name AS lokasi_openlibrary',
                'loc_so.name AS lokasi_so',
                'so_stock.sos_filename',
                'knowledge_stock.status AS status_openlib',
                'so_stock.sos_status',
                'classification_code.code'
            )
            ->where('sos_id_so', $id);

        if (!empty($condition === 'status_diff')) {
            $query->whereColumn('knowledge_stock.status', '!=', 'so_stock.sos_status');
        }

        if (!empty($condition === 'location_diff')) {
            $query->whereColumn('loc_openlibrary.name', '!=', 'loc_so.name');
        }

        if (!empty($jeniskatalog)) {
            $query->where('knowledge_item.knowledge_type_id', $jeniskatalog);
        }

        if (!empty($lokasiopenlib)) {
            $query->where('knowledge_item.item_location_id', $lokasiopenlib);
        }

        if (!empty($lokasiso)) {
            $query->where('so_stock.sos_id_location', $lokasiso);
        }

        if (!empty($statusopenlib)) {
            $query->where('knowledge_stock.status', $statusopenlib);
        }

        if (!empty($statusso)) {
            $query->where('so_stock.sos_status', $statusso);
        }

        if (!empty($userId)) {
            $query->where('so_stock.sos_id_user', $userId);
        }

        $query->orderBy('so_stock.sos_date', 'desc');

        return $query;
    }

    public function getBarcodeDuplicate($so_id, $jeniskatalogduplicate = null, $statusopenlibduplicate = null)
    {
        // Subquery sesuai SQL native
        $subquery = DB::table('batik.so_stock as sos')
            ->leftJoin('batik.knowledge_stock as ks', 'ks.id', '=', 'sos.sos_id_stock')
            ->leftJoin('batik.knowledge_item as kit', 'kit.id', '=', 'ks.knowledge_item_id')
            ->leftJoin('batik.classification_code as cc', 'cc.id', '=', 'kit.classification_code_id')
            ->leftJoin('batik.knowledge_type as kt', 'kt.id', '=', 'kit.knowledge_type_id')
            ->leftJoin('batik.member as m', 'm.id', '=', 'sos.sos_id_user')
            ->selectRaw("
                GROUP_CONCAT(DISTINCT m.master_data_user ORDER BY m.master_data_user ASC SEPARATOR ', ') as fullname,
                kit.title,
                ks.code as kscode,
                kit.code as kitcode,
                kt.name,
                cc.code as cccode,
                GROUP_CONCAT(DISTINCT sos.sos_filename ORDER BY sos.sos_filename ASC SEPARATOR ', ') as filename,
                COUNT(*) as total_member
            ")
            ->where('sos.sos_id_so', $so_id);

        // Filter: Jenis Katalog
        if (!empty($jeniskatalogduplicate) && $jeniskatalogduplicate !== 'Semua') {
            $subquery->where('kit.knowledge_type_id', $jeniskatalogduplicate);
        }

        // Filter: Status
        if (!empty($statusopenlibduplicate)) {
            $subquery->whereIn('ks.status', (array) $statusopenlibduplicate);
        }

        // Grouping agar sesuai dengan SQL native
        $subquery->groupBy([
            'sos.sos_id_stock', // penting agar COUNT(*) per barcode
            'kit.title',
            'ks.code',
            'kit.code',
            'kt.name',
            'cc.code'
        ]);

        // Bungkus dalam query luar (WHERE total > 1 dan ORDER BY)
        $query = DB::table(DB::raw("({$subquery->toSql()}) as a"))
            ->mergeBindings($subquery) // membawa parameter binding dari subquery
            ->where('total_member', '>', 1)
            ->orderBy('kscode', 'desc');

        return $query;
    }

    public function getBarcodeBelumSo($so_id, $jeniskatalogbelumso = null, $lokasiopenlibbelumso = null, $statusopenlibbelumso = null, $classification_start = null, $classification_end = null)
    {
        $query = DB::table('batik.knowledge_stock as ks')
            ->leftJoin('batik.knowledge_item as kit', 'kit.id', '=', 'ks.knowledge_item_id')
            ->leftJoin('batik.classification_code as cc', 'cc.id', '=', 'kit.classification_code_id')
            ->leftJoin('batik.knowledge_type as kt', 'kt.id', '=', 'kit.knowledge_type_id')
            ->leftJoin('batik.item_location as il', 'il.id', '=', 'ks.item_location_id')
            ->leftJoin('batik.so_stock as sos', function($join) use ($so_id) {
                $join->on('sos.sos_id_stock', '=', 'ks.id')
                    ->where('sos.sos_id_so', '=', $so_id);
            })
            ->select(
                'kit.title',
                'kit.author',
                'ks.code as barcode',
                'kit.code as no_katalog',
                'kt.name as jenis_katalog',
                'cc.code as no_klasifikasi',
                'ks.status as status_openlib',
                'il.name as lokasi_openlibrary'
            )
            ->whereNotIn('kt.id', [4,5,6,21,24,25,47,49,51,52,55,62,70,73,75,79])
            ->whereNull('sos.sos_id_stock')
            ->where('cc.code', '>=', '000')
            ->where('cc.code', '<=', '9999999')
            ->distinct()
            ->orderBy('barcode', 'desc');

        if (!empty($lokasiopenlibbelumso)) {
            $query->whereIn('il.id', (array)$lokasiopenlibbelumso);
        }

        if (!empty($jeniskatalogbelumso) && $jeniskatalogbelumso != 'Semua') {
            $query->where('kit.knowledge_type_id', $jeniskatalogbelumso);
        }

        if (!empty($statusopenlibbelumso)) {
            $query->whereIn('ks.status', (array)$statusopenlibbelumso);
        }

        if (!empty($classification_start)) {
            $query->where('cc.code', '>=', $classification_start);
        } else {
            $query->where('cc.code', '>=', '000');
        }

        if (!empty($classification_end)) {
            $query->where('cc.code', '<=', $classification_end);
        } else {
            $query->where('cc.code', '<=', '9999999');
        }

        return $query;
        // return $query->orderBy('ks.code', 'desc');
    }

    public function getStockDataTable($id)
    {
        return DB::table('batik.so_stock')
            ->where('sos_id_so', $id)
            ->select('sos_id', 'sos_date', 'sos_id_user', 'master_data_fullname')
            ->get();
    }

    public static function CheckBarcode($barcode)
    {
        return DB::table('batik.knowledge_stock')
            ->select('id')
            ->where('code', $barcode)
            ->first();
    }

    public static function getbystockOne($temp, $item)
    {
        DB::table('batik.so_stock')
            ->where('sos_id_so', $item['sos_id_so'])
            ->where('sos_id_user', $item['sos_id_user'])
            ->whereIn('sos_id_stock', function ($query) use ($temp) {
                $query->select('id')
                    ->from('knowledge_stock')
                    ->whereIn('code', $temp);
            })
            ->delete();

        // Insert data baru
        $insertData = DB::table('batik.knowledge_stock')
            ->select(DB::raw("'' AS sos_id, '{$item['sos_id_so']}' AS sos_id_so, id AS sos_id_stock, '{$item['sos_id_user']}' AS sos_id_user, CURRENT_DATE AS sos_date, '{$item['sos_status']}' AS sos_status, '{$item['sos_filename']}' AS sos_filename, '{$item['sos_id_location']}' AS sos_id_location"))
            ->whereIn('code', $temp);

        return DB::table('so_stock')->insert($insertData->get()->toArray());
    }

    public function getStock(array $codes)
    {
        return DB::table('batik.knowledge_stock')
            ->select('code')
            ->whereIn(DB::raw('lower(code)'), $codes)
            ->get();
    }

    public function getByStock(array $codes, array $item)
    {
        DB::table('batik.so_stock')
            ->where('sos_id_so', $item['sos_id_so'])
            ->where('sos_id_user', $item['sos_id_user'])
            ->whereIn('sos_id_stock', function ($query) use ($codes) {
                $query->select('id')->from('knowledge_stock')->whereIn(DB::raw('lower(code)'), $codes);
            })
            ->delete();

        DB::table('batik.so_stock')->insertUsing(
            ['sos_id', 'sos_id_so', 'sos_id_stock', 'sos_id_user', 'sos_date', 'sos_status', 'sos_filename', 'sos_id_location'],
            DB::table('knowledge_stock')->selectRaw(
                "'' as sos_id, ? as sos_id_so, id as sos_id_stock, ? as sos_id_user, ? as sos_date, ? as sos_status, ? as sos_filename, ? as sos_id_location",
                [$item['sos_id_so'], $item['sos_id_user'], now()->format('Y-m-d'), $item['sos_status'], $item['sos_filename'], $item['sos_id_location']]
            )->whereIn(DB::raw('lower(code)'), $codes)
        );

        return true;
    }
}
