<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class UploadTypeModel extends Authenticatable
{
    protected $table = 'upload_type';
    protected $primaryKey = 'id';


    public static function getMemberTypes($uploadTypeId)
    {
        return DB::table('member_type AS mt')
            ->join('member_type_upload_type AS mtut', 'mt.id', '=', 'mtut.member_type_id')
            ->where('mtut.upload_type_id', $uploadTypeId)
            ->select('mt.*') // Select all columns from member_type
            ->get();
    }

    public static function getUploadTypeWithMemberAccess()
{
    return DB::table('upload_type AS ut')
        ->select(
            'ut.id',
            'ut.name',
            'ut.extension',
            'ut.title',
            'ut.is_secure',
            DB::raw('GROUP_CONCAT(DISTINCT mtd.name ORDER BY mtd.name SEPARATOR ", ") AS member_download'),
            DB::raw('GROUP_CONCAT(DISTINCT mtr.name ORDER BY mtr.name SEPARATOR ", ") AS member_readonly')
        )
        ->leftJoin('member_type_upload_type AS mtut', 'ut.id', '=', 'mtut.upload_type_id')
        ->leftJoin('member_type AS mtd', 'mtut.member_type_id', '=', 'mtd.id') // Member Download

        ->leftJoin('member_type_upload_type_readonly AS mtutro', 'ut.id', '=', 'mtutro.upload_type_id')
        ->leftJoin('member_type AS mtr', 'mtutro.member_type_id', '=', 'mtr.id') // Member Read-only

        ->groupBy('ut.id', 'ut.name', 'ut.extension', 'ut.title', 'ut.is_secure')
        ->get();
}

}
