<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment_Model as Payment;
use App\Models\Member;
use App\Models\Penalty;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct()
    {
        ini_set('memory_limit', '-1');
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    /**
     * Display payment history
     */
    public function index(Request $request)
    {
        $userId = Auth::user()->id;
        if (!$userId) {
            return redirect()->route('login');
        }

        $penalty = (new Penalty())->getPenalty($userId);
        $payment = (new Payment())->getPayment($userId);

        return view('payment.payment', [
            'title' => 'History Pembayaran',
            'icon' => 'icon-book3',
            'penalty' => $penalty,
            'payment' => $payment,
        ]);
    }

    /**
     * Get payment data for datatables
     */
public function json(Request $request)
{
    if (!$request->ajax()) {
        return response()->json(['error' => 'Invalid request'], 400);
    }

    $userId = Auth::user()->id;

    // Total records
    $totalRecords = DB::table('rent_penalty_payment')->count();

    // Filtered records
    $filteredRecords = DB::table('rent_penalty_payment')
        ->where('member_id', $userId)
        ->where('amount', '>', 0)
        ->count();

    // Data query
    $query = DB::table('rent_penalty_payment as r')
    ->leftJoin('member as m', 'm.id', '=', 'r.member_id')
    ->select('m.master_data_user', 'm.master_data_fullname', 'r.payment_date', DB::raw("'tunai' AS status"), 'r.amount')
    ->where('r.member_id', $userId)
    ->where('r.amount', '>', 0);

    // Update payment date for online payments if needed
    DB::table('rent_penalty_payment_online as rp')
        ->where('rp.pay_id_member', $userId)
        ->where('rp.pay_status', 1)
        ->whereNull('rp.pay_payment_date')
        ->update(['rp.pay_payment_date' => now()]);

    // Prepare online payment query builder
    $onlineQuery = DB::table('rent_penalty_payment_online as rp')
        ->leftJoin('member as m', 'm.id', '=', 'rp.pay_id_member')
        ->select('m.master_data_user', 'm.master_data_fullname', 'rp.pay_payment_date AS payment_date', DB::raw("'transfer' AS status"), 'rp.pay_amount AS amount')
        ->where('rp.pay_id_member', $userId)
        ->where('rp.pay_status', 1)
        ->whereNotNull('rp.pay_payment_date');

    // Gabungkan kedua query
    $dataQuery = $query->union($onlineQuery)->get();

    // Format data for DataTables
    $data = $dataQuery->map(function ($row) {
        return [
            $row->master_data_user,
            $row->master_data_fullname,
            $row->payment_date,
            '<button class="btn btn-sm ' . ($row->status == 'transfer' ? 'btn-primary' : 'btn-success') . '">' . htmlspecialchars($row->status) . '</button>',
            "Rp " . number_format($row->amount, 0, ',', '.'),
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
     * Get member data for select2
     */
    public function getMember(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $searchTerm = $request->input('searchTerm');
        if (!$searchTerm) {
            return response()->json([]);
        }

        $members = Member::where('master_data_fullname', 'like', "%$searchTerm%")
            ->whereIn('member_type_id', [4, 7])
            ->where('status', 1)
            ->limit(20)
            ->get();

        return response()->json($members->map(function ($member) {
            return [
                'id' => $member->id,
                'text' => $member->master_data_fullname,
            ];
        }));
    }

    /**
     * Insert new payment record
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
     * Edit payment record
     */
    public function edit(Request $request)
    {
        $id = $request->input('id');
        $data = DB::table('books')->where('id', $id)->first();

        if ($data) {
            $data->book_date_prodi_submission = date('d-m-Y', strtotime($data->book_date_prodi_submission));
            return response()->json($data);
        }

        return response()->json(['error' => 'Data not found'], 404);
    }

    /**
     * Delete payment record
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