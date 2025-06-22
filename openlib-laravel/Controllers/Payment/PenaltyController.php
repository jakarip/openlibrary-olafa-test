<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Penalty;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenaltyController extends Controller
{
    public function __construct()
    {
        ini_set('memory_limit', '-1');
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    /**
     * Display penalty history
     */
    public function index(Request $request)
    {
        $userId = Auth::user()->id;

        if (!$userId) {
            return redirect()->route('login');
        }

        $penalty = (new Penalty())->getPenalty($userId);
        $payment = (new Penalty())->getPayment($userId);

        return view('payment.penalty', [
            'title' => 'History Denda',
            'icon' => 'icon-book3',
            'penalty' => $penalty,
            'payment' => $payment,
        ]);
    }

    /**
     * Get penalty data for datatables
     */
    public function json(Request $request)
{
    if (!$request->ajax()) {
        return response()->json(['error' => 'Invalid request'], 400);
    }

    $userId = Auth::user()->id;

    // Total records
    $totalRecords = DB::table('rent')->count();

    // Filtered records
    $filteredRecords = DB::table('rent')
        ->where('member_id', $userId)
        ->where('penalty_day', '>', 0)
        ->count();

    // Data query
    $query = DB::table('rent as r')
        ->leftJoin('knowledge_stock as ks', 'ks.id', '=', 'r.knowledge_stock_id')
        ->leftJoin('knowledge_item as kit', 'kit.id', '=', 'ks.knowledge_item_id')
        ->leftJoin('member as m', 'm.id', '=', 'r.member_id')
        ->select(
            'r.status as status',
            'm.master_data_user as master_data_user',
            'm.master_data_fullname as master_data_fullname',
            'kit.title as title',
            'kit.code as code',
            'ks.code as barcode',
            'r.rent_date as rent_date',
            'r.return_date as return_date',
            'r.return_date_expected as return_date_expected',
            'r.penalty_total as penalty_total'
        )
        ->where('r.member_id', $userId)
        ->where('penalty_day', '>', 0)
        ->get();
    
    // Format data for DataTables
    $data = $query->map(function ($row) {
    $status = match ($row->status) {
        2 => '<button class="btn btn-success btn-xs">dikembalikan</button>',
        1 => '<button class="btn btn-warning btn-xs">dipinjam</button>',
        3 => '<button class="btn btn-danger btn-xs">rusak</button>',
        4 => '<button class="btn btn-danger btn-xs">hilang</button>',
        default => '<button class="btn btn-secondary btn-xs">unknown</button>',
    };
 logger()->info('Status:', ['status' => $row->status, 'mapped_status' => $status]);
    return [
        'status' => $status,
        'username' => $row->master_data_user,
        'full_name' => $row->master_data_fullname,
        'title' => $row->title,
        'catalog_number' => $row->code,
        'barcode' => $row->barcode,
        'borrow_date' => $row->rent_date,
        'return_date' => $row->return_date,
        'due_date' => $row->return_date_expected,
        'penalty' => "Rp " . number_format($row->penalty_total, 0, ',', '.'),
    ];
});

    return response()->json([
        'draw' => intval($request->input('draw')),
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'data' => $data,
    ]);
}

    /**
     * Insert new penalty record
     */
    public function insert(Request $request)
    {
        $data = $request->input('inp');
        $data['book_date_prodi_submission'] = date('Y-m-d', strtotime($data['book_date_prodi_submission']));
        $data['book_status'] = 'pengajuan';

        if (DB::table('books')->insert($data)) {
            return response()->json(['status' => 'ok', 'text' => 'Data saved successfully']);
        }

        return response()->json(['status' => 'error', 'text' => 'Failed to save data'], 500);
    }

    /**
     * Delete penalty record
     */
    public function delete(Request $request)
    {
        $id = $request->input('id');
        if (DB::table('books')->where('id', $id)->delete()) {
            return response()->json(['status' => 'ok', 'text' => 'Data deleted successfully']);
        }

        return response()->json(['status' => 'error', 'text' => 'Failed to delete data'], 500);
    }
}