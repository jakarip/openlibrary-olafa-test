<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdiModel extends Model
{
    protected $table = 't_mst_prodi';
    protected $primaryKey = 'C_KODE_PRODI';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'C_KODE_PRODI',
        'C_KODE_FAKULTAS',
        'NAMA_PRODI',
        'NAMA_PRODI_ENGLISH'
    ];

    public function fakultas()
    {
        return $this->belongsTo(FakultasModel::class, 'C_KODE_FAKULTAS', 'C_KODE_FAKULTAS');
    }

    public function scopeActive($query)
    {
        return $query->where('F_AKTIF', 1);
    }
}