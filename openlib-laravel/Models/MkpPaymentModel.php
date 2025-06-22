<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MkpPaymentModel extends Model
{
    protected $table = 'rent_penalty_payment_online';
    protected $primaryKey = 'pay_id';
    public $timestamps = false;

    protected $fillable = [
        'pay_id_rent_penalty_payment',
        'pay_id_member',
        'pay_status',
        'pay_no_ref',
        'pay_payment_date',
        'pay_amount',
        'pay_request_date',
        'pay_expired_date',
        'pay_link',
        'pay_link_status',
        'pay_link_status_code'
    ];
    /**
     * Query for new datatables purpose
     */
    public function dtquery($param)
    {
        return DB::select("
            SELECT SQL_CALC_FOUND_ROWS * 
            FROM {$this->table}  
            LEFT JOIN member m ON m.id = subscribe_id_member
            {$param['where']} {$param['order']} {$param['limit']}
        ");
    }

    public function dtfiltered()
    {
        $result = DB::select('SELECT FOUND_ROWS() as jumlah');
        return $result[0]->jumlah ?? 0;
    }

    public function dtcount()
    {
        return DB::table($this->table)->count();
    }

    public function getAll()
    {
        return DB::table($this->table)->get();
    }

    public function checkDate()
    {
        return DB::table('wordcloud')
            ->where('wc_date', now()->format('Y-m-d H:00:00'))
            ->count();
    }

    public function emptyWordCloudTable()
    {
        DB::table('wordcloud')->truncate();
    }

    public function getRent()
    {
        return DB::select("
            SELECT sub.name AS title 
            FROM rent r
            LEFT JOIN knowledge_stock ks ON ks.id = r.knowledge_stock_id
            LEFT JOIN knowledge_item ki ON ki.id = ks.knowledge_item_id
            LEFT JOIN knowledge_subject sub ON sub.id = ki.knowledge_subject_id
            ORDER BY r.rent_date DESC 
            LIMIT 1000
        ");
    }

    public function getAccess()
    {
        return DB::select("
            SELECT sub.name AS title 
            FROM knowledge_item_view r
            LEFT JOIN knowledge_item ki ON ki.id = r.kiv_id_item 
            LEFT JOIN knowledge_subject sub ON sub.id = ki.knowledge_subject_id
            ORDER BY r.kiv_date DESC 
            LIMIT 3000
        ");
    }

    public function insertWord($data)
    {
        DB::table('wordcloud')->insert($data['wordcloud']);
    }

    public function member($id)
    {
        return DB::table('member')->where('id', $id)->get();
    }

    public function getTransactionNumber()
    {
        return DB::table('member_subscribe')
            ->where('subscribe_transaction', 'like', now()->format('ymd') . '%')
            ->max('subscribe_transaction');
    }

    public function getByQuery($param)
    {
        return DB::select("
            SELECT * 
            FROM {$this->table} 
            {$param['where']} {$param['order']} {$param['limit']}
        ");
    }

    public function countByQuery($param)
    {
        $result = DB::select("
            SELECT COUNT({$this->primaryKey}) AS jumlah 
            FROM {$this->table} 
            {$param['where']}
        ");
        return $result[0]->jumlah ?? 0;
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
        return DB::table($this->table)->where($this->primaryKey, $id)->get();
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

    public function aktivasi($where, $item)
    {
        return DB::table($this->table)
            ->where($where)
            ->update($item);
    }

    public function getLogin($username, $password)
    {
        return DB::table($this->table)
            ->where('student_username', $username)
            ->where('student_password', $password)
            ->get();
    }

    public function getProfile($id)
    {
        return DB::table('student_registration')
            ->where('sreg_id_student', $id)
            ->get();
    }

    public function getSchool()
    {
        return DB::table('ms_school')->orderBy('school_name')->get();
    }

    public function getCourse()
    {
        return DB::table('ms_course')->orderBy('course_name')->get();
    }

    public function getCourseByType($type)
    {
        return DB::table('ms_course')
            ->where('course_type', $type)
            ->where('course_status', 1)
            ->orderBy('course_name')
            ->get();
    }

    public function getCourseActive()
    {
        return DB::table('ms_course')
            ->where('course_status', 1)
            ->orderBy('course_name')
            ->get();
    }

    public function getKec($id)
    {
        return DB::table('ms_kec')
            ->where('kec_kab_id', $id)
            ->orderBy('kec_name')
            ->get();
    }

    public function getTrack()
    {
        return DB::table('ms_track')
            ->where('track_status', 1)
            ->where('track_id', '!=', 3)
            ->orderBy('track_name')
            ->get();
    }

    public function getTrackNotEast()
    {
        return DB::table('ms_track')
            ->where('track_status', 1)
            ->where('track_id', '!=', 1)
            ->orderBy('track_name')
            ->get();
    }
}