<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Member_Payment extends Model
{
    protected $table = 'member';
    protected $primaryKey = 'id';
    public $timestamps = false;

    /**
     * Get filtered data for datatables
     */
    public function dtquery($param)
    {
        return DB::select("
            SELECT * 
            FROM {$this->table} 
            LEFT JOIN member_subscribe ON subscribe_id_member = id AND subscribe_status = '1'
            {$param['where']} {$param['order']} {$param['limit']}
        ");
    }

    /**
     * Get filtered count
     */
    public function dtfiltered()
    {
        return DB::selectOne('SELECT FOUND_ROWS() AS jumlah')->jumlah ?? 0;
    }

    /**
     * Get total count
     */
    public function dtcount()
    {
        return DB::table($this->table)->count();
    }

    /**
     * Get all records
     */
    public function getAll()
    {
        return $this->all();
    }

    /**
     * Get records by query
     */
    public function getByQuery($param)
    {
        return DB::select("
            SELECT * 
            FROM {$this->table} 
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
        return $this->find($id);
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
     * Edit user login record
     */
    public function editUserLogin($username, $item)
    {
        return DB::table('masterdata.t_mst_user_login')
            ->where('C_USERNAME', $username)
            ->update($item);
    }

    /**
     * Delete a record
     */
    public function deleteRecord($id)
    {
        return $this->where($this->primaryKey, $id)->delete();
    }

    /**
     * Delete user login record
     */
    public function deleteUserLogin($username)
    {
        return DB::table('masterdata.t_mst_user_login')
            ->where('c_username', $username)
            ->delete();
    }

    /**
     * Delete employee record
     */
    public function deleteEmployee($nip)
    {
        return DB::table('masterdata.t_mst_pegawai')
            ->where('c_nip', $nip)
            ->delete();
    }

    /**
     * Delete VFS user record
     */
    public function deleteVfsUser($usr)
    {
        return DB::table('masterdata.vfs_users')
            ->where('usr', $usr)
            ->delete();
    }

    /**
     * Activate a member
     */
    public function activate($where, $item)
    {
        return $this->where($where)->update($item);
    }

    /**
     * Get member details
     */
    public function getMember($id)
    {
        return DB::select("
            SELECT * 
            FROM {$this->table} m 
            LEFT JOIN masterdata.t_mst_user_login ON master_data_user = c_username 
            WHERE m.id = '$id'
        ");
    }
}