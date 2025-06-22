<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class TypeModel extends Authenticatable
{
    use HasFactory; 

    protected $table = 'knowledge_type';
    protected $primaryKey = 'id';

    public static function getTypeWithMemberTypes()
    {
        return DB::table('knowledge_type AS kt')
            ->select(
                'kt.id', 
                'kt.name', 
                DB::raw('IF(GROUP_CONCAT(mt.name ORDER BY mt.name SEPARATOR ", ") IS NULL OR GROUP_CONCAT(mt.name ORDER BY mt.name SEPARATOR ", ") = "", NULL, GROUP_CONCAT(mt.name ORDER BY mt.name SEPARATOR ", ")) AS member_types'),
                DB::raw('(SELECT COUNT(*) FROM knowledge_item WHERE knowledge_type_id = kt.id) AS item_count'),
                'kt.active', 
                'kt.type', 
                'kt.rentable'
            )
            ->leftJoin('member_type_permission AS mpp', 'kt.id', '=', 'mpp.knowledge_type_id')
            ->leftJoin('member_type AS mt', 'mt.id', '=', 'mpp.member_type_id')
            ->groupBy('kt.id', 'kt.name', 'kt.active', 'kt.type', 'kt.rentable')
            ->orderBy('kt.name', 'ASC')
            ->get();
    }

    // Method untuk mendapatkan member types tanpa menggunakan relasi
    public static function getMemberTypesByTypeId($typeId)
    {
        return DB::table('member_type AS mt')
            ->join('member_type_permission AS mpp', 'mt.id', '=', 'mpp.member_type_id')
            ->where('mpp.knowledge_type_id', $typeId)
            ->select('mt.id', 'mt.name')
            ->get();
    }


}
