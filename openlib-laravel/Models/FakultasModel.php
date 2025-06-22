<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FakultasModel extends Model
{
    protected $table = 't_mst_fakultas';
    protected $primaryKey = ['C_KODE_FAKULTAS', 'C_KODE_PT'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'C_KODE_FAKULTAS',
        'C_KODE_PT',
        'NAMA_FAKULTAS',
        'NAMA_FAKULTAS_ENGLISH',
        'NO_SK_PENDIRIAN',
        'TGL_SK_PENDIRIAN',
        'TELEPHONE_FAKULTAS',
        'FAKSIMIL_FAKULTAS',
        'VISI_FAKULTAS',
        'MISI_FAKULTAS',
        'TUJUAN_FAKULTAS',
        'F_AKTIF',
        'JENIS_EPROC',
        'SINGKATAN'
    ];

    // Scope untuk fakultas aktif
    public function scopeActive($query)
    {
        return $query->where('F_AKTIF', 1);
    }

    // Relationship ke knowledge_item
    public function knowledgeItems()
    {
        return $this->hasMany(KnowledgeItemModel::class, 'faculty_code', 'C_KODE_FAKULTAS');
    }
}