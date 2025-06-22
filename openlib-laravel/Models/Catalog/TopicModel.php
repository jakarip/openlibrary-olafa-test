<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class TopicModel extends Authenticatable
{
    protected $table = 'knowledge_subject';
    protected $primaryKey = 'id';

    public static function getSubjectWithItemCount()
    {
        return DB::table('knowledge_subject AS ks')
            ->select(
                'ks.id',
                'ks.name', 
                'ks.active', 
                DB::raw('COUNT(ki.id) AS jumlah_katalog'),
                'ks.updated_by', 
                'ks.updated_at'
            )
            ->leftJoin('knowledge_item AS ki', 'ki.knowledge_subject_id', '=', 'ks.id')
            ->groupBy('ks.id','ks.name', 'ks.active', 'ks.updated_by', 'ks.updated_at')
            ->orderBy('ks.name', 'ASC')
            // ->orderBy('ks.id', 'DESC')
            // ->limit(10) 
            ->get();
    }

}
