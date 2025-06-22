<?php

namespace App\Http\Controllers\OLAFA\EProceeding;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EProceedingModel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;

class EProceedingStatusController extends Controller
{
    public function index()
    {
        if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

        return view('olafa.eProceeding.status');
    }

    public function dt()
    {                   
        $data = EProceedingModel::getWorkflowDocuments();

        return datatables($data)->addColumn('action', function ($db) {
            $buttons = '';
            
            if ($db->state_id == '4') {
                $buttons .= '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Approved For Catalog & No Publish Proceedings ( Not Feasible )" onclick="status(\'' . $db->wddid . '\',\'3\',\'' . $db->latest_state_id . '\')">Approved For Catalog & No Publish Proceedings ( Not Feasible )</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )" onclick="status(\'' . $db->wddid . '\',\'52\',\'' . $db->latest_state_id . '\')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Approved For Catalog & Journal Publish Tel-U Proceedings" onclick="status(\'' . $db->wddid . '\',\'53\',\'' . $db->latest_state_id . '\')">Approved For Catalog & Journal Publish Tel-U Proceedings</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal - LoA Pending )" onclick="status(\'' . $db->wddid . '\',\'64\',\'' . $db->latest_state_id . '\')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal - LoA Pending )</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Metadata Approve for Catalog & Journal Publish External" onclick="status(\'' . $db->wddid . '\',\'91\',\'' . $db->latest_state_id . '\')">Metadata Approve for Catalog & Journal Publish External</button></div>';
            } elseif ($db->state_id == '3') {
                $buttons .= '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Document Not Feasible" onclick="status(\'' . $db->wddid . '\',\'4\',\'' . $db->latest_state_id . '\')">Document Not Feasible</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )" onclick="status(\'' . $db->wddid . '\',\'52\',\'' . $db->latest_state_id . '\')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Approved For Catalog & Journal Publish Tel-U Proceedings" onclick="status(\'' . $db->wddid . '\',\'53\',\'' . $db->latest_state_id . '\')">Approved For Catalog & Journal Publish Tel-U Proceedings</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal - LoA Pending )" onclick="status(\'' . $db->wddid . '\',\'64\',\'' . $db->latest_state_id . '\')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal - LoA Pending )</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Metadata Approve for Catalog & Journal Publish External" onclick="status(\'' . $db->wddid . '\',\'91\',\'' . $db->latest_state_id . '\')">Metadata Approve for Catalog & Journal Publish External</button></div>';
            } elseif ($db->state_id == '52') {
                $buttons .= '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Document Not Feasible" onclick="status(\'' . $db->wddid . '\',\'4\',\'' . $db->latest_state_id . '\')">Document Not Feasible</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Approved For Catalog & No Publish Proceedings ( Not Feasible )" onclick="status(\'' . $db->wddid . '\',\'3\',\'' . $db->latest_state_id . '\')">Approved For Catalog & No Publish Proceedings ( Not Feasible )</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Approved For Catalog & Journal Publish Tel-U Proceedings" onclick="status(\'' . $db->wddid . '\',\'53\',\'' . $db->latest_state_id . '\')">Approved For Catalog & Journal Publish Tel-U Proceedings</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal - LoA Pending )" onclick="status(\'' . $db->wddid . '\',\'64\',\'' . $db->latest_state_id . '\')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal - LoA Pending )</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Metadata Approve for Catalog & Journal Publish External" onclick="status(\'' . $db->wddid . '\',\'91\',\'' . $db->latest_state_id . '\')">Metadata Approve for Catalog & Journal Publish External</button></div>';
            } elseif ($db->state_id == '53') {
                $buttons .= '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Document Not Feasible" onclick="status(\'' . $db->wddid . '\',\'4\',\'' . $db->latest_state_id . '\')">Document Not Feasible</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Approved For Catalog & No Publish Proceedings ( Not Feasible )" onclick="status(\'' . $db->wddid . '\',\'3\',\'' . $db->latest_state_id . '\')">Approved For Catalog & No Publish Proceedings ( Not Feasible )</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )" onclick="status(\'' . $db->wddid . '\',\'52\',\'' . $db->latest_state_id . '\')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal - LoA Pending )" onclick="status(\'' . $db->wddid . '\',\'64\',\'' . $db->latest_state_id . '\')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal - LoA Pending )</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Metadata Approve for Catalog & Journal Publish External" onclick="status(\'' . $db->wddid . '\',\'91\',\'' . $db->latest_state_id . '\')">Metadata Approve for Catalog & Journal Publish External</button></div>';
            } elseif ($db->state_id == '64') {
                $buttons .= '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Document Not Feasible" onclick="status(\'' . $db->wddid . '\',\'4\',\'' . $db->latest_state_id . '\')">Document Not Feasible</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Approved For Catalog & No Publish Proceedings ( Not Feasible )" onclick="status(\'' . $db->wddid . '\',\'3\',\'' . $db->latest_state_id . '\')">Approved For Catalog & No Publish Proceedings ( Not Feasible )</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )" onclick="status(\'' . $db->wddid . '\',\'52\',\'' . $db->latest_state_id . '\')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal - LoA Pending )" onclick="status(\'' . $db->wddid . '\',\'53\',\'' . $db->latest_state_id . '\')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal - LoA Pending )</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Metadata Approve for Catalog & Journal Publish External" onclick="status(\'' . $db->wddid . '\',\'91\',\'' . $db->latest_state_id . '\')">Metadata Approve for Catalog & Journal Publish External</button></div>';
            } elseif ($db->state_id == '91') {
                $buttons .= '<div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Document Not Feasible" onclick="status(\'' . $db->wddid . '\',\'4\',\'' . $db->latest_state_id . '\')">Document Not Feasible</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Approved For Catalog & No Publish Proceedings ( Not Feasible )" onclick="status(\'' . $db->wddid . '\',\'3\',\'' . $db->latest_state_id . '\')">Approved For Catalog & No Publish Proceedings ( Not Feasible )</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )" onclick="status(\'' . $db->wddid . '\',\'52\',\'' . $db->latest_state_id . '\')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal - LoA Pending )" onclick="status(\'' . $db->wddid . '\',\'53\',\'' . $db->latest_state_id . '\')">Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal - LoA Pending )</button></div><br><br>
                    <div class="btn-group"><button type="button" class="btn btn-sm btn-success text-start" style="font-size: 10px;" title="Metadata Approve for Catalog & Journal Publish External" onclick="status(\'' . $db->wddid . '\',\'91\',\'' . $db->latest_state_id . '\')">Metadata Approve for Catalog & Journal Publish External</button></div>';
            }
            return $buttons;
        })
        ->rawColumns(['action'])->toJson();
    }

    public function changeStatus(Request $request)
    {
        $id = $request->id;
        $status = $request->status;

        $this->updateDocument($id, $status, $request->latest_state_id);

        return response()->json(['status' => 'success', 'message' => 'Status updated successfully']);

    }

    private function updateDocument($id, $status, $latest_state_id)
    {
        if ($latest_state_id != '5') {
            DB::table('workflow_document')
                ->where('id', $id)
                ->update([
                    'latest_state_id' => $status,
                    'created_at' => now(),
                    'created_by' => 'scheduler_change_status'
                ]);
        }

        return DB::table('workflow_document_state')
            ->where('document_id', $id)
            ->whereIn('id', function ($query) use ($id) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('workflow_document_state')
                    ->where('document_id', $id)
                    ->where('state_id', '!=', '5');
            })
            ->update(['state_id' => $status]);
    }
    
}
