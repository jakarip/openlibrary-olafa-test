<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\KnowledgeItemModel;

class WorkflowStateModel extends Model
{
    protected $table = 'workflow_state';
    protected $primaryKey = 'id';

    // Relationship with Workflow
    public function workflow()
    {
        return $this->belongsTo(WorkflowModel::class, 'workflow_id');
    }

    // Method to get workflow states with workflow names
    public static function getWorkflowStatesWithWorkflow()
    {
        return self::select(
            'workflow_state.id',
            'workflow_state.name as state_name', 
            'workflow_state.description', 
            'workflow.name as workflow_name',
            'workflow_state.updated_by',
            'workflow_state.updated_at',
        )
        ->join('workflow', 'workflow.id', '=', 'workflow_state.workflow_id')
        ->groupBy('workflow_state.id', 'workflow_state.name', 'workflow_state.description', 'workflow.name','workflow_state.updated_by','workflow_state.updated_at')
        ->get();
    }
}
