<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LecturerBookModel extends Model
{
    public $timestamps = false;
    protected $table = 'telu_press';
    protected $primaryKey = 'press_id';

    protected $fillable = [
        'press_barcode', 'press_type', 'press_title', 'press_author', 
        'press_publisher', 'press_published_year', 'press_faculty_unit', 'press_isbn'
    ];
}
