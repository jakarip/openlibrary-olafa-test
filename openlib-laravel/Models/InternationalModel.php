<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternationalModel extends Model
{
    protected $table = 'internationalonline'; 
    protected $primaryKey = 'io_id';
    public $timestamps = false; // Disable timestamps

    protected $fillable = [
        'io_name',
        'io_url'
    ];
}
