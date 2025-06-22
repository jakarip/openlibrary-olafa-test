<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaymentOnline extends Model
{
    protected $table = 'rent_penalty_payment_online';
    protected $primaryKey = 'id';
    public $timestamps = false;

    /**
     * Query for new datatables purpose
     */
    public function dtquery($param)
    {
        return DB::select("
            SELECT 
                m.master_data_fullname,
                m.master_data_user,
                r.*
            FROM rent_penalty_payment_online r
            LEFT JOIN member m ON m.id = r.pay_id_member
            {$param['where']} {$param['order']} {$param['limit']}
        ");
    }

    public function dtfiltered($where = "")
    {
        return DB::table('rent_penalty_payment_online')
            ->whereRaw($where)
            ->count();
    }

    public function getPenalty($member_id)
    {
        return DB::table('rent')
        ->where('member_id', $member_id)
        ->sum('penalty_total');
    }

    public function getPayment($member_id)
{
    $rentPayment = DB::table('rent_penalty_payment')
        ->where('member_id', $member_id)
        ->sum('amount');

    $onlinePayment = DB::table('rent_penalty_payment_online')
        ->where('pay_id_member', $member_id)
        ->where('pay_status', 1) // Hanya pembayaran yang berhasil
        ->sum('pay_amount');

    return $rentPayment + $onlinePayment;
}

    public function dtcount()
    {
        return DB::table($this->table)->count();
    }

    public function getByNoRef($ref)
    {
        return DB::table('rent_penalty_payment_online as r')
            ->leftJoin('member as m', 'm.id', '=', 'r.pay_id_member')
            ->where('r.pay_no_ref', $ref)
            ->first();
    }

    public function getAll()
    {
        return DB::table($this->table)->get();
    }

    public function checkPaymentCode($code)
    {
        return DB::table('member_subscribe')
            ->whereIn('subscribe_status', [0, 2])
            ->where('subscribe_payment_code', $code)
            ->get();
    }

    public function member($id)
{
    return DB::table('member')->where('id', $id)->first();
}

    public function getTransactionNumber()
    {
        return DB::table('member_subscribe')
            ->where('subscribe_transaction', 'like', date('ymd') . '%')
            ->max('subscribe_transaction');
    }

    public function getByQuery($param)
    {
        return DB::select("SELECT * FROM {$this->table} {$param['where']} {$param['order']} {$param['limit']}");
    }

    public function countByQuery($param)
    {
        return DB::table($this->table)
            ->whereRaw($param['where'])
            ->count();
    }

    public function countAll()
    {
        return DB::table($this->table)->count();
    }

    public function getBy($item)
    {
        return DB::table($this->table)->where($item)->get();
    }

    public function getById($id)
    {
        return DB::table($this->table)->where('book_id', $id)->get();
    }

    public function getBook($id)
    {
        return DB::table($this->table)
            ->select('book_id', 'book_title', 'book_member', 'book_date_logistic_submission', 'book_memo_logistic_number')
            ->whereIn('book_id', explode(',', $id))
            ->get();
    }

    public function getMember($id)
    {
        return DB::table('member')->where('id', $id)->get();
    }

    public function getEmailConfirmedAndAvailable($id)
    {
        return DB::table($this->table)
            ->select('book_status', 'book_id', 'book_title', 'book_date_email_confirmed', 'book_date_available', 'book_catalog_number')
            ->where('book_id', $id)
            ->get();
    }

    public function add($item)
    {
        return DB::table($this->table)->insertGetId($item);
    }

    public function edit($id, $item)
    {
        return DB::table($this->table)
            ->where($this->primaryKey, $id)
            ->update($item);
    }

    public function deleteById($id)
    {
        return DB::table($this->table)
            ->where($this->primaryKey, $id)
            ->delete();
    }

    public function addBatch($items)
    {
        return DB::table($this->table)->insert($items);
    }

    public function getProdi()
    {
        return DB::select("
            SELECT *
            FROM t_mst_fakultas tmf
            LEFT JOIN t_mst_prodi tmp ON tmp.c_kode_fakultas = tmf.c_kode_fakultas
            WHERE nama_prodi NOT LIKE '%Pindahan%'
              AND nama_prodi NOT LIKE '%International%'
              AND nama_prodi NOT LIKE '%Internasional%'
            ORDER BY nama_fakultas, nama_prodi
        ");
    }

    public function getMemberByName($name)
    {
        return DB::table('member')
            ->whereIn('member_type_id', [4, 7])
            ->where('master_data_fullname', 'like', "%$name%")
            ->where('status', 1)
            ->orderBy('master_data_fullname')
            ->limit(20)
            ->get();
    }

    public function editLogistic($ids, $item)
    {
        return DB::table($this->table)
            ->whereIn($this->primaryKey, $ids)
            ->update($item);
    }

    public function updateByNoRef($ref, $item)
    {
        return DB::table($this->table)
            ->where('pay_no_ref', $ref)
            ->update($item);
    }

    public function insertData($item)
    {
        return DB::table('rent_penalty_payment')->insertGetId($item);
    }
}