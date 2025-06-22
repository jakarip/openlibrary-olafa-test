<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Payment_Model extends Model
{
    protected $table = 'rent_penalty_payment';
    protected $primaryKey = 'id';
    public $timestamps = false;

    /**
     * Get filtered data for datatables
     */
    public function dtquery($param)
    {
        return DB::select("
            SELECT 
                master_data_fullname, 
                master_data_user,
                payment_date,
                amount,
                CASE 
                    WHEN rp.pay_id_rent_penalty_payment != 0 THEN 'transfer'
                    ELSE 'tunai'
                END AS status
            FROM rent_penalty_payment r  
            LEFT JOIN rent_penalty_payment_online rp ON r.id = rp.pay_id_rent_penalty_payment
            LEFT JOIN member m ON m.id = member_id
            {$param['where']} {$param['order']} {$param['limit']}
        ");
    }

    /**
     * Get filtered count
     */
    public function dtfiltered($where = "")
    {
        return DB::table('rent_penalty_payment')
            ->whereRaw($where)
            ->count();
    }

    /**
     * Get penalty sum for a member
     */
    public function getPenalty($member_id)
    {
        return DB::table('rent_penalty')
            ->where('member_id', $member_id)
            ->sum('amount');
    }

    /**
     * Get payment sum for a member
     */
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

    /**
     * Get all records
     */
    public function getAll()
    {
        return $this->all();
    }

    /**
     * Check payment code
     */
    public function checkPaymentCode($code)
    {
        return DB::table('member_subscribe')
            ->whereIn('subscribe_status', [0, 2])
            ->where('subscribe_payment_code', $code)
            ->get();
    }

    /**
     * Get member by ID
     */
    public function member($id)
    {
        return DB::table('member')
            ->where('id', $id)
            ->get();
    }

    /**
     * Get transaction number
     */
    public function getTransactionNumber()
    {
        return DB::table('member_subscribe')
            ->where('subscribe_transaction', 'like', date('ymd') . '%')
            ->max('subscribe_transaction');
    }

    /**
     * Get records by query
     */
    public function getByQuery($param)
    {
        return DB::select("
            SELECT * FROM {$this->table} 
            {$param['where']} {$param['order']} {$param['limit']}
        ");
    }

    /**
     * Count records by query
     */
    public function countByQuery($param)
    {
        return DB::selectOne("
            SELECT COUNT({$this->primaryKey}) AS jumlah 
            FROM {$this->table} 
            {$param['where']}
        ")->jumlah ?? 0;
    }

    /**
     * Count all records
     */
    public function countAll()
    {
        return $this->count();
    }

    /**
     * Get records by condition
     */
    public function getBy($item)
    {
        return $this->where($item)->get();
    }

    /**
     * Get record by ID
     */
    public function getById($id)
    {
        return $this->where('book_id', $id)->get();
    }

    /**
     * Get book details
     */
    public function getBook($id)
    {
        return DB::select("
            SELECT book_id, book_title, book_member, book_date_logistic_submission, book_memo_logistic_number 
            FROM {$this->table} 
            WHERE book_id IN ($id)
        ");
    }

    /**
     * Get email confirmed and available books
     */
    public function getEmailConfirmedAndAvailable($id)
    {
        return DB::select("
            SELECT book_status, book_id, book_title, book_date_email_confirmed, book_date_available, book_catalog_number 
            FROM {$this->table} 
            WHERE book_id = '$id'
        ");
    }

    /**
     * Add a new record
     */
    public function add($item)
    {
        return $this->create($item);
    }

    /**
     * Edit a record
     */
    public function edit($id, $item)
    {
        return $this->where($this->primaryKey, $id)->update($item);
    }

    /**
     * Delete a record
     */
    public function deleteRecord($id)
    {
        return $this->where($this->primaryKey, $id)->delete();
    }

    /**
     * Add multiple records
     */
    public function addBatch($items)
    {
        return DB::table($this->table)->insert($items);
    }

    /**
     * Get faculties and programs
     */
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

    /**
     * Get members by name
     */
    public function getMemberByName($name)
    {
        return DB::table('member')
            ->whereIn('member_type_id', [4, 7])
            ->where('master_data_fullname', 'like', "%$name%")
            ->where('status', '1')
            ->orderBy('master_data_fullname')
            ->limit(20)
            ->get();
    }

    /**
     * Edit logistic records
     */
    public function editLogistic($id, $item)
    {
        return $this->whereIn($this->primaryKey, $id)->update($item);
    }
}