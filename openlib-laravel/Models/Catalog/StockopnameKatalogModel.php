<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockopnameKatalogModel extends Model
{
    protected $connection = 'mysql';
    protected $table = 'so_edition';
    protected $primaryKey = 'so_id';
    public $timestamps = false;

    public function getKatalogWeedingQuery($lokasiopenlib = null, $status = null, $classification_start = null, $classification_end = null, $year_start = null, $year_end = null, $terakhirpinjam = null)
    {
        $query = DB::table('knowledge_item')
            ->leftJoin('knowledge_stock', 'knowledge_stock.knowledge_item_id', '=', 'knowledge_item.id')
            ->leftJoin('knowledge_subject', 'knowledge_item.knowledge_subject_id', '=', 'knowledge_subject.id')
            ->leftJoin('classification_code', 'classification_code.id', '=', 'knowledge_item.classification_code_id')
            ->leftJoin('item_location', 'item_location.id', '=', 'knowledge_stock.item_location_id')
            ->leftJoin('knowledge_type', 'knowledge_stock.knowledge_type_id', '=', 'knowledge_type.id')
            ->select(
                'knowledge_type.name AS jenis_katalog',
                'knowledge_item.title',
                'classification_code.code AS no_klasifikasi',
                'knowledge_item.author',
                'knowledge_item.published_year',
                'knowledge_item.code AS no_katalog',
                'knowledge_stock.code AS barcode',
                'item_location.name AS lokasi_openlibrary',
                'knowledge_stock.status AS status_openlib'
            )
            ->where('knowledge_subject.active', '1')
            ->where('knowledge_type.active', '1')
            ->whereNotIn('knowledge_stock.status', [4, 5])
            ->where('knowledge_type.rentable', '1');

        if (!empty($lokasiopenlib)) {
            $query->where('knowledge_item.item_location_id', $lokasiopenlib);
        }

        if (!empty($status)) {
            $query->where('knowledge_stock.status', $status);
        }

        if (!empty($classification_start)) {
            $query->where('classification_code.code', '>=', $classification_start);
        } else {
            $query->where('classification_code.code', '>=', '000');
        }

        if (!empty($classification_end)) {
            $query->where('classification_code.code', '<=', $classification_end);
        } else {
            $query->where('classification_code.code', '<=', '9999999');
        }

        if (!empty($year_start)) {
            $query->where('knowledge_item.published_year', '>=', $year_start);
        } else {
            $query->where('knowledge_item.published_year', '>=', 1700);
        }

        if (!empty($year_end)) {
            $query->where('knowledge_item.published_year', '<=', $year_end);
        } else {
            $query->where('knowledge_item.published_year', '<=', date('Y'));
        }

        // Filter terakhir pinjam (NOT EXISTS rent dalam X tahun terakhir)
        if (!empty($terakhirpinjam)) {
            $tahunSekarang = date("Y-m-d");
            $tahunAwal = date("Y-m-d", strtotime('-' . $terakhirpinjam . ' year'));

            $query->whereNotExists(function ($q) use ($tahunAwal, $tahunSekarang) {
                $q->select(DB::raw(1))
                    ->from('rent')
                    ->leftJoin('knowledge_stock as stock', 'stock.id', '=', 'rent.knowledge_stock_id')
                    ->whereRaw('stock.knowledge_item_id = knowledge_item.id')
                    ->whereBetween('rent.rent_date', [$tahunAwal, $tahunSekarang]);
            });
        }

        $query->orderByDesc('knowledge_stock.code');

        return $query;
    }
}
