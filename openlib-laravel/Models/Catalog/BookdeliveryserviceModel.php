<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BookdeliveryserviceModel extends Model
{
    protected $connection = 'mysql';
    protected $table = 'batik.book_delivery_service';
    protected $primaryKey = 'bds_id';
    public $timestamps = false;

    public function getBookDeliveryService($status = null)
    {
        $query = DB::connection('mysql')->table('book_delivery_service')
            ->leftJoin('batik.member', 'book_delivery_service.bds_idmember', '=', 'member.id')
            // ->leftJoin('batik.book_delivery_service_book', 'book_delivery_service.bds_id', '=', 'book_delivery_service_book.bdsb_idbds')
            ->leftJoin('batik.book_delivery_service_status', 'book_delivery_service.bds_id', '=', 'book_delivery_service_status.bdss_idbds')
            ->select(
                'book_delivery_service.bds_id',
                'book_delivery_service.bds_createdate',
                'book_delivery_service.bds_number',
                'member.master_data_user',
                'member.master_data_fullname',
                'book_delivery_service.bds_receiver',
                'book_delivery_service.bds_address',
                'book_delivery_service.bds_phone',
                'book_delivery_service.bds_status',
                'book_delivery_service.bds_photo_courier',
                DB::raw('GROUP_CONCAT(book_delivery_service_status.bdss_status) AS concatenated_bdss_status'),
                DB::raw('GROUP_CONCAT(book_delivery_service_status.bdss_date) AS concatenated_bdss_date'),
                'book_delivery_service.bds_reason'
            )
            ->groupBy(
                'book_delivery_service.bds_id',
                'book_delivery_service.bds_createdate',
                'book_delivery_service.bds_number',
                'member.master_data_user',
                'member.master_data_fullname',
                'book_delivery_service.bds_receiver',
                'book_delivery_service.bds_address',
                'book_delivery_service.bds_phone',
                'book_delivery_service.bds_status',
                'book_delivery_service.bds_photo_courier',
                'book_delivery_service.bds_reason'
            );

        if ($status) {
            $query->where('book_delivery_service.bds_status', $status);
        }

        return $query->get();
    }

    public function getBookDeliveryServiceBooks($bdsId)
    {
        return DB::connection('mysql')->table('batik.book_delivery_service_book')
            ->where('bdsb_idbds', $bdsId) // Filter berdasarkan ID book_delivery_service
            ->select('bdsb_item_code', 'bdsb_stock_code')
            ->get();
    }

    public function checkEksemplar($item, $barcode, $memberid)
    {
        $query = DB::connection('mysql')->table('batik.rent as r')
            ->leftJoin('batik.knowledge_stock as ks', 'ks.id', '=', 'r.knowledge_stock_id')
            ->leftJoin('batik.knowledge_item2 as kit', 'kit.id', '=', 'ks.knowledge_item_id')
            ->where('ks.code', $barcode)
            ->where('kit.code', $item)
            ->where('r.member_id', $memberid)
            ->where('ks.status', '2')
            ->select('ks.id');

        return $query->get();
    }
}
