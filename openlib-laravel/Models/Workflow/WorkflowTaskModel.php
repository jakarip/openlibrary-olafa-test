<?php

namespace App\Models\Workflow;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WorkflowTaskModel extends Model
{
    protected $table = 'workflow_task';

    public function saveTask($data)
    {
        return DB::transaction(function () use ($data) {
            // Insert atau update workflow_task
            $taskId = $data['id'] ?? null;
            if ($taskId) {
                // Update jika id ada
                DB::table('workflow_task')->where('id', $taskId)->update([
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'duration' => $data['duration'],
                    'display_order' => $data['display_order'],
                    'workflow_id' => $data['workflow_id'],
                    'next_state_id' => $data['next_state_id'],
                    'updated_by' => $data['updated_by'],
                ]);
            } else {
                // Insert jika id tidak ada
                $taskId = DB::table('workflow_task')->insertGetId([
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'duration' => $data['duration'],
                    'display_order' => $data['display_order'],
                    'workflow_id' => $data['workflow_id'],
                    'next_state_id' => $data['next_state_id'],
                    'created_by' => $data['created_by'],
                    'updated_by' => $data['updated_by'],
                ]);
            }

            // Hapus state lama sebelum disimpan
            DB::table('workflow_transition')->where('task_id', $taskId)->delete();

            // Insert workflow_transition dengan state_id sebagai array
            if (!empty($data['state_id'])) {
                foreach ($data['state_id'] as $stateId) {
                    DB::table('workflow_transition')->insert([
                        'task_id' => $taskId,
                        'state_id' => $stateId,
                    ]);
                }
            }

            return $taskId;
        });
    }

    public function getStatesByTaskId($taskId)
    {
        return DB::table('workflow_transition')
            ->where('task_id', $taskId)
            ->pluck('state_id')
            ->toArray();
    }

    public function getById($id)
    {
        $task = DB::table('workflow_task')
            ->join('workflow_transition', 'workflow_task.id', '=', 'workflow_transition.task_id')
            ->select('workflow_task.*', 'workflow_transition.state_id')
            ->where('workflow_task.id', $id)
            ->get();

        // Ambil state_id yang terkait
        $stateId = (new WorkflowTaskModel())->getStatesByTaskId($id);

        return response()->json([
            'task' => $task,
            'stateIds' => $stateId // Kirim stateIds untuk digunakan di blade
        ]);
    }


    public function deleteTask($id)
    {
        return DB::transaction(function () use ($id) {
            DB::table('workflow_transition')->where('task_id', $id)->delete();
            return DB::table('workflow_task')->where('id', $id)->delete();
        });
    }

    public function getDataTable()
    {
        return DB::table('workflow_task')
            ->join('workflow', 'workflow.id', '=', 'workflow_task.workflow_id')
            ->select('workflow_task.*', 'workflow.name as workflow_name')
            ->get();
    }
}
