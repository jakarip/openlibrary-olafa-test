<?php

namespace App\Models\Room;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TermconditionModel extends Model
{
    protected $connection = 'mysql';
    protected $table = 'room.term_conditions';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['information', 'term_sequence', 'information_en'];

    public function getTermCondition($roomId = null)
    {
      $query =  DB::connection('mysql')->table('room.term_conditions')
          ->select(
              'id',
              'information',
              'information_en',
              'term_sequence'
          );

          return $query->get();
    }
}
