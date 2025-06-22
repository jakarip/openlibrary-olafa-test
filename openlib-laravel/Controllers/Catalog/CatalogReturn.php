<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\CarbonPeriod;

class CatalogReturn extends Controller
{
    public function index()
    {
        return view('catalog.return.return');
    }

    public function autoData(Request $request)
    {
        $query = strtolower($request->input('q'));
        if (!$query) {
            return response()->json([
                'single_match' => false,
                'items' => []
            ]);
        }

        // Try exact match first
        $exactMatch = DB::select("
            SELECT m.id, m.master_data_user, m.master_data_number, m.master_data_fullname,
                mt.name as member_type, mt.rent_quantity as loan_limit
            FROM member m
            JOIN member_type mt ON m.member_type_id = mt.id
            WHERE LOWER(m.master_data_user) = ?
            OR LOWER(m.master_data_number) = ?
            LIMIT 1
        ", [$query, $query]);

        // If exact match found, return it
        if (count($exactMatch) === 1) {
            $member = $exactMatch[0];

            // Count active loans
            $activeLoans = DB::table('rent')
                ->where('member_id', $member->id)
                ->whereNull('return_date')
                ->count();

            $formatted = [
                'id' => $member->id,
                'username' => $member->master_data_user,
                'fullname' => $member->master_data_fullname,
                'nim' => $member->master_data_number,
                'member_type' => $member->member_type,
                'loan_limit' => $member->loan_limit,
                'active_loans' => $activeLoans,
                'name' => "{$member->master_data_user} - {$member->master_data_number} - {$member->master_data_fullname}",
            ];

            return response()->json([
                'single_match' => true,
                'exact_match' => true,
                'member' => $formatted,
                'items' => [$formatted]
            ]);
        }

        // If no exact match, do a partial search
        $results = DB::select("
            SELECT m.id, m.master_data_user, m.master_data_number, m.master_data_fullname,
                mt.name as member_type, mt.rent_quantity as loan_limit
            FROM member m
            JOIN member_type mt ON m.member_type_id = mt.id
            WHERE LOWER(m.master_data_user) LIKE ?
            OR LOWER(m.master_data_number) LIKE ?
            OR LOWER(m.master_data_fullname) LIKE ?
            LIMIT 10
        ", ["%$query%", "%$query%", "%$query%"]);

        $formatted = [];
        foreach ($results as $item) {
            // Count active loans for each member
            $activeLoans = DB::table('rent')
                ->where('member_id', $item->id)
                ->whereNull('return_date')
                ->count();

            $formatted[] = [
                'id' => $item->id,
                'username' => $item->master_data_user,
                'fullname' => $item->master_data_fullname,
                'nim' => $item->master_data_number,
                'member_type' => $item->member_type,
                'loan_limit' => $item->loan_limit,
                'active_loans' => $activeLoans,
                'name' => "{$item->master_data_user} - {$item->master_data_number} - {$item->master_data_fullname}",
            ];
        }

        // Check if there's a single match
        $singleMatch = count($formatted) === 1;
        $member = $singleMatch ? $formatted[0] : null;

        return response()->json([
            'single_match' => $singleMatch,
            'exact_match' => false,
            'member' => $member,
            'items' => $formatted
        ]);
    }

    public function getMemberInfo(Request $request)
    {
        $memberId = $request->input('member_id');
        if (!$memberId) {
            return response()->json(['success' => false, 'message' => 'ID member diperlukan']);
        }

        $member = DB::table('member as m')
            ->join('member_type as mt', 'm.member_type_id', '=', 'mt.id')
            ->select(
                'm.*',
                'mt.name as member_type',
                'mt.rent_quantity',
                'mt.rent_period',
                'mt.rent_extension_day',
                'mt.rent_extension_count'
            )
            ->where('m.id', $memberId)
            ->first();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Member tidak ditemukan']);
        }

        // Count active loans
        $activeLoans = DB::table('rent')
            ->where('member_id', $memberId)
            ->whereNull('return_date')
            ->count();

        // MODIFIED: Get total penalty from ALL loans (not just active ones)
        $totalPenalty = DB::table('rent')
            ->where('member_id', $memberId)
            // Remove the whereNull('return_date') filter to include all loans
            ->sum('penalty_total');

        // Get total payments
        $totalPaid = DB::table('rent_penalty_payment')
            ->where('member_id', $memberId)
            ->sum('amount');

        // Calculate outstanding balance
        $outstandingPenalty = max($totalPenalty - $totalPaid, 0);

        $result = [
            'success' => true,
            'member' => [
                'id' => $member->id,
                'name' => $member->master_data_fullname,
                'number' => $member->master_data_number,
                'type' => $member->member_type,
                'loan_limit' => $member->rent_quantity,
                'loan_period' => $member->rent_period,
                'extension_days' => $member->rent_extension_day,
                'extension_limit' => $member->rent_extension_count,
                'active_loans' => $activeLoans,
                'total_penalty' => $totalPenalty,
                'total_paid' => $totalPaid,
                'outstanding_penalty' => $outstandingPenalty,
                'username' => $member->master_data_user,
            ]
        ];

        return response()->json($result);
    }

    public function getHistory(Request $request)
    {
        $memberId = $request->input('member_id');
        $status = $request->input('status', 'active');  // active|returned|all
        $draw = (int) $request->input('draw');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $skipPenaltyUpdate = $request->input('skip_penalty_update', false);

        if (!$memberId) {
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'footer' => [
                    'grand_penalty' => 0,
                    'total_paid' => 0,
                    'outstanding' => 0,
                ],
            ]);
        }

        // Only update penalties if explicitly requested or processing a return
        if (!$skipPenaltyUpdate) {
            $this->updateRentPenalty($memberId);
        }

        // Query loans with filtering based on status
        $base = DB::table('rent as r')
            ->join('rent_cart as rc', 'rc.id', '=', 'r.rent_cart_id')
            ->join('knowledge_stock as ks', 'ks.id', '=', 'r.knowledge_stock_id')
            ->join('knowledge_item as ki', 'ki.id', '=', 'ks.knowledge_item_id')
            ->select('r.*', 'rc.rental_code', 'ks.code as barcode', 'ki.title')
            ->where('r.member_id', $memberId);

        // Apply filter for the displayed data rows
        if ($status === 'active')
            $base->whereNull('r.return_date');
        else if ($status === 'returned')
            $base->whereNotNull('r.return_date');

        $recordsTotal = $base->count();

        $rows = $base
            ->orderByDesc('r.rent_date')
            ->skip($start)->take($length)
            ->get();

        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                'id' => $row->id,
                'title' => $row->title,
                'barcode' => $row->barcode,
                'rental_code' => $row->rental_code,
                'rent_date' => $row->rent_date,
                'due_date' => $row->extended_to_date ?: $row->return_date_expected,
                'extended_from' => $row->extended_from_date,
                'extended_to' => $row->extended_to_date,
                'extended_count' => $row->extended_count ?? 0,
                'return_date' => $row->return_date,
                'holiday_day' => $row->penalty_holiday ?? 0,
                'penalty_day' => $row->penalty_day ?? 0,
                'penalty_total' => $row->penalty_total ?? 0,
            ];
        }

        // MODIFIED: Always get total penalty from ALL loans (no status filter)
        $grandPenalty = DB::table('rent')
            ->where('member_id', $memberId)
            ->sum('penalty_total');

        // For payment, we always calculate the total paid regardless of status
        $totalPaid = DB::table('rent_penalty_payment')
            ->where('member_id', $memberId)
            ->sum('amount');

        $outstanding = max($grandPenalty - $totalPaid, 0);

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data,
            'footer' => [
                'grand_penalty' => $grandPenalty,
                'total_paid' => $totalPaid,
                'outstanding' => $outstanding,
            ],
        ]);
    }

    // Added a lightweight version of getHistory that always skips penalty calculations
    public function getLightHistory(Request $request)
    {
        $memberId = $request->input('member_id');
        $status = $request->input('status', 'active');  // active|returned|all
        $draw = (int) $request->input('draw');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        if (!$memberId) {
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'footer' => [
                    'grand_penalty' => 0,
                    'total_paid' => 0,
                    'outstanding' => 0,
                ],
            ]);
        }

        // Query loans - NO penalty updates
        $base = DB::table('rent as r')
            ->join('rent_cart as rc', 'rc.id', '=', 'r.rent_cart_id')
            ->join('knowledge_stock as ks', 'ks.id', '=', 'r.knowledge_stock_id')
            ->join('knowledge_item as ki', 'ki.id', '=', 'ks.knowledge_item_id')
            ->select('r.*', 'rc.rental_code', 'ks.code as barcode', 'ki.title')
            ->where('r.member_id', $memberId);

        if ($status === 'active')
            $base->whereNull('r.return_date');
        else if ($status === 'returned')
            $base->whereNotNull('r.return_date');

        $recordsTotal = $base->count();

        $rows = $base
            ->orderByDesc('r.rent_date')
            ->skip($start)->take($length)
            ->get();

        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                'id' => $row->id,
                'title' => $row->title,
                'barcode' => $row->barcode,
                'rental_code' => $row->rental_code,
                'rent_date' => $row->rent_date,
                'due_date' => $row->extended_to_date ?: $row->return_date_expected,
                'extended_from' => $row->extended_from_date,
                'extended_to' => $row->extended_to_date,
                'extended_count' => $row->extended_count ?? 0,
                'return_date' => $row->return_date,
                'holiday_day' => $row->penalty_holiday ?? 0,
                'penalty_day' => $row->penalty_day ?? 0,
                'penalty_total' => $row->penalty_total ?? 0,
            ];
        }

        // Simple summary calculations without updating
        $grandPenalty = DB::table('rent')->where('member_id', $memberId)->sum('penalty_total');
        $totalPaid = DB::table('rent_penalty_payment')->where('member_id', $memberId)->sum('amount');
        $outstanding = max($grandPenalty - $totalPaid, 0);

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data,
            'footer' => [
                'grand_penalty' => $grandPenalty,
                'total_paid' => $totalPaid,
                'outstanding' => $outstanding,
            ],
        ]);
    }

    public function checkBarcode(Request $request)
    {
        $barcode = $request->input('barcode');
        $memberId = $request->input('member_id');

        if (!$barcode) {
            return response()->json(['success' => false, 'message' => 'Barcode diperlukan']);
        }

        try {
            // Find the stock by barcode with a single query that includes all needed data
            $rent = DB::table('rent as r')
                ->join('knowledge_stock as ks', 'ks.id', '=', 'r.knowledge_stock_id')
                ->join('knowledge_item as ki', 'ki.id', '=', 'ks.knowledge_item_id')
                ->select(
                    'r.*',
                    'ks.code as barcode',
                    'ki.title',
                    'ks.id as stock_id'
                )
                ->where('ks.code', $barcode)
                ->where('r.member_id', $memberId)
                ->whereNull('r.return_date')
                ->first();

            if (!$rent) {
                return response()->json(['success' => false, 'message' => 'Koleksi ini tidak dipinjam oleh anggota ini atau barcode tidak ditemukan']);
            }

            // Calculate penalties directly without using updateRentPenalty
            // This is much faster for a single rental
            $today = Carbon::today();
            $limitDate = $rent->extended_to_date ? Carbon::parse($rent->extended_to_date) : Carbon::parse($rent->return_date_expected);

            $penaltyDay = 0;
            $holidayPenalty = 0;

            if ($today->gt($limitDate)) {
                $from = $limitDate->copy()->addDay();

                // Only count weekdays
                $period = CarbonPeriod::create($from, $today);
                $allWeekdayDates = [];

                foreach ($period as $d) {
                    if ($d->isWeekday()) {
                        $allWeekdayDates[] = $d->format('Y-m-d');
                    }
                }

                if (count($allWeekdayDates) > 0) {
                    // Get holidays in a single query
                    $holidayDates = DB::table('holiday')
                        ->whereIn('holiday_date', $allWeekdayDates)
                        ->pluck('holiday_date')
                        ->toArray();

                    $holidayPenalty = count($holidayDates);
                    $penaltyDay = count($allWeekdayDates) - $holidayPenalty;
                }
            }

            $penaltyTotal = $penaltyDay * $rent->penalty_per_day;

            // Determine due date and days overdue
            $dueDate = $rent->extended_to_date ? Carbon::parse($rent->extended_to_date) : Carbon::parse($rent->return_date_expected);
            $isOverdue = $dueDate->lt($today);
            $daysOverdue = $isOverdue ? $dueDate->diffInDays($today) : 0;

            return response()->json([
                'success' => true,
                'rent' => [
                    'id' => $rent->id,
                    'stock_id' => $rent->stock_id,
                    'title' => $rent->title,
                    'barcode' => $rent->barcode,
                    'rent_date' => $rent->rent_date,
                    'due_date' => $dueDate->format('Y-m-d'),
                    'is_overdue' => $isOverdue,
                    'days_overdue' => $daysOverdue,
                    'holiday_day' => $holidayPenalty,
                    'penalty_day' => $penaltyDay,
                    'penalty_total' => $penaltyTotal,
                    'penalty_per_day' => $rent->penalty_per_day
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking barcode: ' . $e->getMessage()
            ], 500);
        }
    }

    public function processReturn(Request $request)
    {
        $request->validate([
            'rent_id' => 'required|integer|exists:rent,id',
            'status' => 'required|integer|in:2,3,4', // 2=returned, 3=damaged, 4=lost
        ]);

        $rentId = $request->rent_id;
        $status = $request->status;

        // Check if rent exists and is active
        $rent = DB::table('rent')
            ->where('id', $rentId)
            ->whereNull('return_date')
            ->first();

        if (!$rent) {
            return response()->json(['success' => false, 'message' => 'Peminjaman tidak ditemukan atau sudah dikembalikan']);
        }

        $today = Carbon::today();
        $limitDate = $rent->extended_to_date ? Carbon::parse($rent->extended_to_date) : Carbon::parse($rent->return_date_expected);

        DB::beginTransaction();
        try {
            // Update penalty information if return date is after due date
            $allDates = [];
            $holidayDates = [];
            $penaltyDay = 0;
            $holidayPenalty = 0;

            if ($today->gt($limitDate)) {
                $from = $limitDate->copy()->addDay();

                // Collect weekdays in range
                $period = CarbonPeriod::create($from, $today);
                foreach ($period as $d) {
                    if ($d->isWeekday()) {
                        $allDates[] = $d->format('Y-m-d');
                    }
                }

                if ($allDates) {
                    // Find holidays in that date range
                    $holidayDates = DB::table('holiday')
                        ->whereIn('holiday_date', $allDates)
                        ->pluck('holiday_date')
                        ->toArray();

                    $holidayPenalty = count($holidayDates);
                    $penaltyDay = count($allDates) - $holidayPenalty;

                    // Create penalty records for each day
                    $daily = [];
                    foreach ($allDates as $d) {
                        if (in_array($d, $holidayDates)) {
                            continue;
                        }
                        $daily[] = [
                            'member_id' => $rent->member_id,
                            'rent_id' => $rent->id,
                            'penalty_date' => $d,
                            'amount' => $rent->penalty_per_day,
                        ];
                    }

                    if (count($daily) > 0) {
                        DB::table('rent_penalty')->insert($daily);
                    }
                }
            }

            $penaltyTotal = $penaltyDay * $rent->penalty_per_day;

            // Add additional penalty for damaged or lost status
            $additionalPenalty = 0;
            if ($status == 3) { // Damaged
                // Add 50% of the book price as additional penalty
                $bookPrice = DB::table('knowledge_stock')
                    ->where('id', $rent->knowledge_stock_id)
                    ->value('price');

                $additionalPenalty = $bookPrice ? round($bookPrice * 0.5) : 0;
            } elseif ($status == 4) { // Lost
                // Add 100% of the book price as additional penalty
                $bookPrice = DB::table('knowledge_stock')
                    ->where('id', $rent->knowledge_stock_id)
                    ->value('price');

                $additionalPenalty = $bookPrice ?: 0;
            }

            $totalPenalty = $penaltyTotal + $additionalPenalty;

            // Update rent record
            DB::table('rent')->where('id', $rentId)->update([
                'return_date' => $today->format('Y-m-d'),
                'status' => $status,
                'penalty_holiday' => $holidayPenalty,
                'penalty_day' => $penaltyDay,
                'penalty_total' => $totalPenalty,
                'updated_by' => Auth::user()->username ?? 'system',
                'updated_at' => Carbon::now(),
            ]);

            // Update stock status
            $stockStatus = 1; // Default: Available
            if ($status == 3) {
                $stockStatus = 3; // Damaged
            } elseif ($status == 4) {
                $stockStatus = 4; // Lost
            }

            DB::table('knowledge_stock')
                ->where('id', $rent->knowledge_stock_id)
                ->update([
                    'status' => $stockStatus,
                    'updated_by' => Auth::user()->username ?? 'system',
                    'updated_at' => Carbon::now()
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Koleksi berhasil diproses',
                'status_text' => $status == 2 ? 'dikembalikan' : ($status == 3 ? 'rusak' : 'hilang'),
                'penalty_day' => $penaltyDay,
                'penalty_total' => $totalPenalty,
                'additional_penalty' => $additionalPenalty
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'member_id' => 'required|integer|exists:member,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $memberId = $request->member_id;
        $amount = $request->amount;
        $rentId = $request->rent_id; // Optional, can be null for general payment

        // Check if there are any outstanding penalties
        $totalPenalty = DB::table('rent')
            ->where('member_id', $memberId)
            ->sum('penalty_total');

        $totalPaid = DB::table('rent_penalty_payment')
            ->where('member_id', $memberId)
            ->sum('amount');

        $outstanding = max($totalPenalty - $totalPaid, 0);

        if ($outstanding <= 0) {
            return response()->json(['success' => false, 'message' => 'Anggota ini tidak memiliki denda yang belum dibayar']);
        }

        if ($amount > $outstanding) {
            return response()->json(['success' => false, 'message' => "Jumlah pembayaran (Rp. " . number_format($amount) . ") melebihi sisa denda (Rp. " . number_format($outstanding) . ")"]);
        }

        DB::beginTransaction();
        try {
            // Record the payment
            DB::table('rent_penalty_payment')->insert([
                'member_id' => $memberId,
                'rent_id' => $rentId, // Can be null
                'payment_date' => Carbon::today(),
                'amount' => $amount
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Pembayaran denda sebesar Rp. " . number_format($amount) . " berhasil diproses",
                'amount' => $amount,
                'outstanding' => $outstanding - $amount
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Modified to optionally update only a specific rent
    public function updateRentPenalty($memberId, $specificRentId = null)
    {
        // Start DB transaction for atomicity
        DB::beginTransaction();
        try {
            // Get rentals for this member, optionally just one specific rent
            $rentsQuery = DB::table('rent as r')
                ->join('knowledge_stock as ks', 'ks.id', '=', 'r.knowledge_stock_id')
                ->join('knowledge_item as ki', 'ki.id', '=', 'ks.knowledge_item_id')
                ->select('r.*', 'ks.code as barcode', 'ki.title')
                ->where('r.member_id', $memberId)
                ->whereNull('r.return_date');

            if ($specificRentId) {
                $rentsQuery->where('r.id', $specificRentId);
            }

            $activeRents = $rentsQuery->get();

            $today = Carbon::today();
            $totalPenalty = 0;

            foreach ($activeRents as $rent) {
                // Determine limit date: extended_to_date or return_date_expected
                $limitDate = $rent->extended_to_date ? Carbon::parse($rent->extended_to_date) : Carbon::parse($rent->return_date_expected);
                $from = Carbon::parse($limitDate)->addDay();
                $to = $today;

                // Initialize calculations
                $allDates = [];
                $holidayDates = [];
                $penaltyDay = 0;
                $holidayPenalty = 0;

                if ($from->lte($to)) {
                    // Collect weekdays in range
                    $period = CarbonPeriod::create($from, $to);
                    foreach ($period as $d) {
                        if ($d->isWeekday()) {
                            $allDates[] = $d->format('Y-m-d');
                        }
                    }

                    if ($allDates) {
                        // Find holidays in that date range
                        $holidayDates = DB::table('holiday')
                            ->whereIn('holiday_date', $allDates)
                            ->pluck('holiday_date')
                            ->toArray();

                        $holidayPenalty = count($holidayDates);
                        $penaltyDay = count($allDates) - $holidayPenalty;

                        // Upsert daily penalty records
                        $daily = [];
                        foreach ($allDates as $d) {
                            if (in_array($d, $holidayDates)) {
                                continue;
                            }
                            $daily[] = [
                                'member_id' => $rent->member_id,
                                'rent_id' => $rent->id,
                                'penalty_date' => $d,
                                'amount' => $rent->penalty_per_day,
                            ];
                        }

                        if (count($daily) > 0) {
                            // Use updateOrInsert for each record
                            foreach ($daily as $record) {
                                DB::table('rent_penalty')->updateOrInsert(
                                    [
                                        'rent_id' => $record['rent_id'],
                                        'penalty_date' => $record['penalty_date']
                                    ],
                                    [
                                        'member_id' => $record['member_id'],
                                        'amount' => $record['amount']
                                    ]
                                );
                            }
                        }
                    }
                }

                $penaltyTotal = $penaltyDay * $rent->penalty_per_day;
                $totalPenalty += $penaltyTotal;

                // Update penalty columns in rent table
                DB::table('rent')
                    ->where('id', $rent->id)
                    ->update([
                        'penalty_holiday' => $holidayPenalty,
                        'penalty_day' => $penaltyDay,
                        'penalty_total' => $penaltyTotal,
                        'updated_at' => now(),
                    ]);
            }

            // Commit all changes
            DB::commit();

            return [
                'success' => true,
                'total_penalty' => $totalPenalty,
                'message' => 'Berhasil memperbarui data denda'
            ];
        } catch (\Exception $e) {
            // Rollback on error
            DB::rollback();
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    public function dt(Request $request)
    {
        // Always use the light version of the history method for datatable requests
        return $this->getLightHistory($request);
    }
}