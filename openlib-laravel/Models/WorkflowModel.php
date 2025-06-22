<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowModel extends Model
{
    protected $table = 'workflow';
    protected $primaryKey = 'id';

    // Relationship with WorkflowStateModel for start_state
    public function startState()
    {
        return $this->belongsTo(WorkflowStateModel::class, 'start_state_id');
    }

    // Relationship with WorkflowStateModel for final_state
    public function finalState()
    {
        return $this->belongsTo(WorkflowStateModel::class, 'final_state_id');
    }

    // Relationship with WorkflowStateModel
    public function workflowStates()
    {
        return $this->hasMany(WorkflowStateModel::class, 'workflow_id');
    }
}
