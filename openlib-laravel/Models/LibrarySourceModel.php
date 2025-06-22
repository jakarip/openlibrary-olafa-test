<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibrarySourceModel extends Model
{
    use HasFactory;
    protected $table = 'library_sources';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
