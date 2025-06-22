<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\CarbonPeriod;

class CatalogBorrowing extends Controller
{
    public function index()
    {
        return view('catalog.borrowing.borrowing');
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

        // Coba exact match dulu
        $exactMatch = DB::select("
        SELECT m.id, m.master_data_user, m.master_data_number, m.master_data_fullname,
               mt.name as member_type, mt.rent_quantity as loan_limit
        FROM member m
        JOIN member_type mt ON m.member_type_id = mt.id
        WHERE LOWER(m.master_data_user) = ?
           OR LOWER(m.master_data_number) = ?
        LIMIT 1
    ", [$query, $query]);

        // Jika ditemukan exact match, langsung return itu
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

        // Jika tidak ada exact match, lakukan pencarian partial seperti biasa
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

        // Jika hanya ada 1 hasil, tandai sebagai single_match
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

        // Get total penalty
        $totalPenalty = DB::table('rent')
            ->where('member_id', $memberId)
            ->whereNull('return_date')
            ->sum('penalty_total');

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
                'total_penalty' => $totalPenalty
            ]
        ];

        return response()->json($result);
    }

    public function checkBarcode(Request $request)
    {
        $barcode = $request->input('barcode');
        $memberId = $request->input('member_id');

        if (!$barcode) {
            return response()->json(['success' => false, 'message' => 'Barcode diperlukan']);
        }

        // Check if stock exists and is available
        $stock = DB::table('knowledge_stock as ks')
            ->join('knowledge_item as ki', 'ki.id', '=', 'ks.knowledge_item_id')
            ->join('knowledge_type as kt', 'kt.id', '=', 'ki.knowledge_type_id')
            ->select(
                'ks.*',
                'ki.title',
                'ki.author',
                'ki.publisher_name',
                'ki.published_year',
                'ki.rent_cost',
                'ki.penalty_cost',
                'kt.rentable'
            )
            ->where('ks.code', $barcode)
            ->first();

        if (!$stock) {
            return response()->json(['success' => false, 'message' => 'Koleksi tidak ditemukan']);
        }

        // Check stock status - only status 1 (Tersedia) can be borrowed
        if ($stock->status != 1) {
            $statusLabels = [
                1 => 'Tersedia',
                2 => 'Dipinjam',
                3 => 'Rusak',
                4 => 'Hilang',
                5 => 'Expired',
                6 => 'Hilang diganti',
                7 => 'Diolah',
                8 => 'Cadangan',
                9 => 'Weeding'
            ];

            $statusText = $statusLabels[$stock->status] ?? 'Tidak diketahui';
            return response()->json(['success' => false, 'message' => "Koleksi tidak tersedia. Status: $statusText"]);
        }

        if (!$stock->rentable) {
            return response()->json(['success' => false, 'message' => 'Koleksi tidak dapat dipinjam']);
        }

        // Get member type information
        $member = DB::table('member as m')
            ->join('member_type as mt', 'm.member_type_id', '=', 'mt.id')
            ->select('m.*', 'mt.rent_period', 'mt.rent_period_unit')
            ->where('m.id', $memberId)
            ->first();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Data anggota tidak ditemukan']);
        }

        // Calculate due date
        $startDate = Carbon::now();
        $dueDate = $this->calculateDueDate($startDate, $member->rent_period);

        return response()->json([
            'success' => true,
            'item' => [
                'stock_id' => $stock->id,
                'item_id' => $stock->knowledge_item_id,
                'barcode' => $stock->code,
                'title' => $stock->title,
                'author' => $stock->author,
                'publisher' => $stock->publisher_name,
                'year' => $stock->published_year,
                'rent_cost' => $stock->rent_cost,
                'penalty_cost' => $stock->penalty_cost,
                'due_date' => $dueDate->format('Y-m-d')
            ]
        ]);
    }

    public function searchCatalog(Request $request)
    {
        $query = $request->input('q');
        $statusFilter = $request->input('status');
        $draw = $request->input('draw', 1);
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $baseQuery = DB::table('knowledge_item as ki')
            ->join('knowledge_stock as ks', 'ki.id', '=', 'ks.knowledge_item_id')
            ->join('knowledge_type as kt', 'kt.id', '=', 'ki.knowledge_type_id')
            ->select(
                'ks.id as stock_id',
                'ks.code as barcode',
                'ki.title',
                'ki.author',
                'ki.publisher_name',
                'ki.published_year',
                'ks.status',
                'kt.rentable'
            );

        // Apply status filter
        if ($statusFilter && $statusFilter != '0') {
            $baseQuery->where('ks.status', $statusFilter);
        }

        if ($query) {
            $baseQuery->where(function ($q) use ($query) {
                $q->where('ki.title', 'like', "%{$query}%")
                    ->orWhere('ki.author', 'like', "%{$query}%")
                    ->orWhere('ks.code', 'like', "%{$query}%");
            });
        }

        $totalRecords = $baseQuery->count();

        $items = $baseQuery->skip($start)->take($length)->get();

        $data = [];
        foreach ($items as $item) {
            $statusText = '';
            $statusClass = '';

            switch ($item->status) {
                case 1:
                    $statusText = 'Tersedia';
                    $statusClass = 'success';
                    break;
                case 2:
                    $statusText = 'Dipinjam';
                    $statusClass = 'warning';
                    break;
                case 3:
                    $statusText = 'Rusak';
                    $statusClass = 'danger';
                    break;
                case 4:
                    $statusText = 'Hilang';
                    $statusClass = 'danger';
                    break;
                case 5:
                    $statusText = 'Expired';
                    $statusClass = 'secondary';
                    break;
                case 6:
                    $statusText = 'Hilang diganti';
                    $statusClass = 'info';
                    break;
                case 7:
                    $statusText = 'Diolah';
                    $statusClass = 'primary';
                    break;
                case 8:
                    $statusText = 'Cadangan';
                    $statusClass = 'info';
                    break;
                case 9:
                    $statusText = 'Weeding';
                    $statusClass = 'secondary';
                    break;
                default:
                    $statusText = 'Tidak Tersedia';
                    $statusClass = 'danger';
            }

            $data[] = [
                'stock_id' => $item->stock_id,
                'barcode' => $item->barcode,
                'title' => $item->title,
                'author' => $item->author,
                'publisher' => $item->publisher_name,
                'year' => $item->published_year,
                'status' => $statusText,
                'status_class' => $statusClass,
                'rentable' => $item->rentable,
                'is_available' => $item->status == 1 && $item->rentable
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }

    public function storeBorrowing(Request $request)
    {
        $request->validate([
            'member_id' => 'required|integer|exists:member,id',
            'items' => 'required|array|min:1',
            'items.*.stock_id' => 'required|integer|exists:knowledge_stock,id',
            'items.*.rent_cost' => 'required|numeric',
            'items.*.rent_period' => 'required|integer|min:1',
        ]);

        $memberId = $request->member_id;
        $items = $request->items;

        // Check member exists and get member type
        $member = DB::table('member')
            ->join('member_type', 'member.member_type_id', '=', 'member_type.id')
            ->select('member.*', 'member_type.rent_quantity', 'member_type.rent_period')
            ->where('member.id', $memberId)
            ->first();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Member tidak ditemukan']);
        }

        // Check if member has reached borrowing limit
        $activeLoans = DB::table('rent')
            ->where('member_id', $memberId)
            ->whereNull('return_date')
            ->count();

        $newLoansCount = count($items);

        if ($activeLoans + $newLoansCount > $member->rent_quantity) {
            return response()->json([
                'success' => false,
                'message' => "Anggota telah mencapai batas peminjaman ({$member->rent_quantity} buku)"
            ]);
        }

        DB::beginTransaction();
        try {
            // Create rent cart
            $rentalCode = 'RC' . time();
            $rentCartId = DB::table('rent_cart')->insertGetId([
                'member_id' => $memberId,
                'rental_code' => $rentalCode,
                'created_by' => Auth::user()->username ?? 'system',
                'created_at' => Carbon::now(),
                'updated_by' => Auth::user()->username ?? 'system',
                'updated_at' => Carbon::now()
            ]);

            $today = Carbon::today();
            $createdRents = [];

            foreach ($items as $item) {
                // Check if stock is available
                $stock = DB::table('knowledge_stock')
                    ->where('id', $item['stock_id'])
                    ->where('status', 1) // Status 1 = Available
                    ->first();

                if (!$stock) {
                    throw new \Exception("Koleksi dengan ID {$item['stock_id']} tidak tersedia");
                }

                // Calculate due date based on rent period
                $dueDate = $this->calculateDueDate($today, $item['rent_period']);

                // Create rent record
                $rentId = DB::table('rent')->insertGetId([
                    'rent_cart_id' => $rentCartId,
                    'member_id' => $memberId,
                    'knowledge_stock_id' => $item['stock_id'],
                    'rent_date' => $today->format('Y-m-d'),
                    'return_date_expected' => $dueDate->format('Y-m-d'),
                    'status' => 1, // Status 1 = Dipinjam
                    'rent_period' => $item['rent_period'],
                    'rent_period_unit' => 1, // Days
                    'rent_period_day' => $item['rent_period'],
                    'rent_cost_per_day' => $item['rent_cost'],
                    'rent_cost_total' => $item['rent_cost'] * $item['rent_period'],
                    'penalty_per_day' => $item['penalty_cost'] ?? 0,
                    'created_by' => Auth::user()->username ?? 'system',
                    'created_at' => Carbon::now(),
                    'updated_by' => Auth::user()->username ?? 'system',
                    'updated_at' => Carbon::now(),
                ]);

                // Update stock status to borrowed
                DB::table('knowledge_stock')
                    ->where('id', $item['stock_id'])
                    ->update([
                        'status' => 2, // Status 2 = Dipinjam
                        'updated_by' => Auth::user()->username ?? 'system',
                        'updated_at' => Carbon::now()
                    ]);

                $createdRents[] = $rentId;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil dibuat',
                'rental_code' => $rentalCode,
                'rent_ids' => $createdRents
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extend a borrowed item's due date
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function extendBorrowing(Request $request)
    {
        $request->validate([
            'rent_id' => 'required|integer|exists:rent,id',
            'days' => 'required|integer|min:1',
        ]);

        $rentId = $request->rent_id;

        // Check if rent exists and is active
        $rent = DB::table('rent')
            ->where('id', $rentId)
            ->whereNull('return_date')
            ->first();

        if (!$rent) {
            return response()->json(['success' => false, 'message' => 'Peminjaman tidak ditemukan atau sudah dikembalikan']);
        }

        // Get member type extension rules
        $memberType = DB::table('member')
            ->join('member_type', 'member.member_type_id', '=', 'member_type.id')
            ->select('member_type.rent_extension_day', 'member_type.rent_extension_count')
            ->where('member.id', $rent->member_id)
            ->first();

        if (!$memberType) {
            return response()->json(['success' => false, 'message' => 'Tipe anggota tidak ditemukan']);
        }

        // Check if requested days exceed the allowed extension days
        if ($request->days > $memberType->rent_extension_day) {
            return response()->json([
                'success' => false,
                'message' => "Perpanjangan maksimal adalah {$memberType->rent_extension_day} hari"
            ]);
        }

        // Check if loan has been extended the maximum number of times
        if ($rent->extended_count >= $memberType->rent_extension_count) {
            return response()->json([
                'success' => false,
                'message' => "Batas maksimal perpanjangan ({$memberType->rent_extension_count}x) telah tercapai"
            ]);
        }

        // CHANGED LOGIC HERE: Start extension from today instead of last due date
        $today = Carbon::today();

        // For extended_from_date, we can reference either the previous due date or today
        $fromDate = $rent->extended_to_date ? $rent->extended_to_date : $rent->return_date_expected;

        // Calculate new due date starting from today
        $newDueDate = $this->calculateDueDate($today, $request->days);

        try {
            DB::table('rent')->where('id', $rentId)->update([
                'extended_count' => DB::raw('extended_count + 1'),
                'extended_from_date' => $fromDate,
                'extended_to_date' => $newDueDate->format('Y-m-d'),
                'return_date_expected' => $newDueDate->format('Y-m-d'), // Also update expected return date
                'updated_by' => Auth::user()->username ?? 'system',
                'updated_at' => Carbon::now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil diperpanjang',
                'new_due_date' => $newDueDate->format('Y-m-d')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function returnBook(Request $request)
    {
        $request->validate([
            'rent_id' => 'required|integer|exists:rent,id',
        ]);

        $rentId = $request->rent_id;

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
            // Update penalty information
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

            // Update rent record
            DB::table('rent')->where('id', $rentId)->update([
                'return_date' => $today->format('Y-m-d'),
                'status' => 2, // Status 2 = Dikembalikan
                'penalty_holiday' => $holidayPenalty,
                'penalty_day' => $penaltyDay,
                'penalty_total' => $penaltyTotal,
                'updated_by' => Auth::user()->username ?? 'system',
                'updated_at' => Carbon::now(),
            ]);

            // Update stock status to available
            DB::table('knowledge_stock')
                ->where('id', $rent->knowledge_stock_id)
                ->update([
                    'status' => 1, // Status 1 = Tersedia
                    'updated_by' => Auth::user()->username ?? 'system',
                    'updated_at' => Carbon::now()
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Koleksi berhasil dikembalikan',
                'penalty_day' => $penaltyDay,
                'penalty_total' => $penaltyTotal
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghitung due date peminjaman dengan hanya menghitung hari kerja (Senin-Jumat).
     *
     * @param string|Carbon $startDate Tanggal awal peminjaman.
     * @param int $workDays Jumlah hari kerja.
     * @return Carbon
     */
    protected function calculateDueDate($startDate, $workDays)
    {
        $dueDate = Carbon::parse($startDate);
        while ($workDays > 0) {
            $dueDate->addDay();
            if ($dueDate->isWeekday()) {
                // Check if it's not a holiday
                $isHoliday = DB::table('holiday')
                    ->where('holiday_date', $dueDate->format('Y-m-d'))
                    ->exists();

                if (!$isHoliday) {
                    $workDays--;
                }
            }
        }
        return $dueDate;
    }

    public function getHistory(Request $request)
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

        // Update penalties first
        $this->updateRentPenalty($memberId);

        // Get member extension info
        $member = DB::table('member')
            ->join('member_type', 'member.member_type_id', '=', 'member_type.id')
            ->select(
                'member_type.rent_extension_day',
                'member_type.rent_extension_count'
            )
            ->where('member.id', $memberId)
            ->first();

        // Query loans
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

        // Get total penalty and payment info based on the filter
        $penaltyQuery = DB::table('rent')
            ->where('member_id', $memberId);

        if ($status === 'active')
            $penaltyQuery->whereNull('return_date');
        else if ($status === 'returned')
            $penaltyQuery->whereNotNull('return_date');

        $grandPenalty = $penaltyQuery->sum('penalty_total');

        // For payment, we always calculate the total paid regardless of status
        // since payments are applied to the member's total debt
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

    public function updateRentPenalty($memberId)
    {
        // Start DB transaction for atomicity
        DB::beginTransaction();
        try {
            // Get all active rentals for this member
            $activeRents = DB::table('rent as r')
                ->join('knowledge_stock as ks', 'ks.id', '=', 'r.knowledge_stock_id')
                ->join('knowledge_item as ki', 'ki.id', '=', 'ks.knowledge_item_id')
                ->select('r.*', 'ks.code as barcode', 'ki.title')
                ->where('r.member_id', $memberId)
                ->whereNull('r.return_date')
                ->get();

            $today = Carbon::today();
            $totalPenalty = 0;

            foreach ($activeRents as $rent) {
                // Determine limit date: extended_to_date or return_date_expected
                $limitDate = $rent->extended_to_date ?: $rent->return_date_expected;
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
                            DB::table('rent_penalty')->upsert(
                                $daily,
                                ['rent_id', 'penalty_date'],
                                ['amount']
                            );
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

    public function checkItem(Request $request)
    {
        $stockId = $request->input('stock_id');
        $memberId = $request->input('member_id');

        if (!$stockId) {
            return response()->json(['success' => false, 'message' => 'ID koleksi diperlukan']);
        }

        // Check if stock exists and is available
        $stock = DB::table('knowledge_stock as ks')
            ->join('knowledge_item as ki', 'ki.id', '=', 'ks.knowledge_item_id')
            ->join('knowledge_type as kt', 'kt.id', '=', 'ki.knowledge_type_id')
            ->select(
                'ks.*',
                'ki.title',
                'ki.author',
                'ki.publisher_name',
                'ki.published_year',
                'ki.rent_cost',
                'ki.penalty_cost',
                'kt.rentable'
            )
            ->where('ks.id', $stockId)
            ->first();

        if (!$stock) {
            return response()->json(['success' => false, 'message' => 'Koleksi tidak ditemukan']);
        }

        // Check stock status - only status 1 (Tersedia) can be borrowed
        if ($stock->status != 1) {
            $statusLabels = [
                1 => 'Tersedia',
                2 => 'Dipinjam',
                3 => 'Rusak',
                4 => 'Hilang',
                5 => 'Expired',
                6 => 'Hilang diganti',
                7 => 'Diolah',
                8 => 'Cadangan',
                9 => 'Weeding'
            ];

            $statusText = $statusLabels[$stock->status] ?? 'Tidak diketahui';
            return response()->json(['success' => false, 'message' => "Koleksi tidak tersedia. Status: $statusText"]);
        }

        if (!$stock->rentable) {
            return response()->json(['success' => false, 'message' => 'Koleksi tidak dapat dipinjam']);
        }

        // Get member type information
        $member = DB::table('member as m')
            ->join('member_type as mt', 'm.member_type_id', '=', 'mt.id')
            ->select('m.*', 'mt.rent_period', 'mt.rent_period_unit')
            ->where('m.id', $memberId)
            ->first();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Data anggota tidak ditemukan']);
        }

        // Calculate due date
        $startDate = Carbon::now();
        $dueDate = $this->calculateDueDate($startDate, $member->rent_period);

        return response()->json([
            'success' => true,
            'item' => [
                'stock_id' => $stock->id,
                'item_id' => $stock->knowledge_item_id,
                'barcode' => $stock->code,
                'title' => $stock->title,
                'author' => $stock->author,
                'publisher' => $stock->publisher_name,
                'year' => $stock->published_year,
                'rent_cost' => $stock->rent_cost,
                'penalty_cost' => $stock->penalty_cost,
                'due_date' => $dueDate->format('Y-m-d')
            ]
        ]);
    }
}