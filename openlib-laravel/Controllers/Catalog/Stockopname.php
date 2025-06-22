<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Catalog\StockopnameKatalogModel;
use App\Models\Catalog\StockopnameModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use DateTime;

class Stockopname extends Controller
{
    public function index()
    {
        if(!auth()->can('catalog-stockopname.view')){
            return redirect('/home');
        }

        return view('config.catalog.stockopname.stockopname');
    }

    public function dt(Request $request)
    {
        $data = (new StockopnameModel())->getSOEdition();

        $counter = 1;

        return datatables($data)
            ->addColumn('action', function ($db) {
                return '<div class="btn-group">
                    <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="d-flex" ><a class="dropdown-item d-flex align-items-center" href="' . url('catalog/stockopname/detail/' . $db->so_id) . '"><i class="ti ti-file me-2"></i> Detail</a></li>
                    </ul>
                </div>';
            })
            ->addColumn('so_id', function ($db) use (&$counter) {
                return $counter++;
            })
            ->addColumn('so_date', function ($db) {
                $date = new DateTime($db->so_date);
                return $date->format('d-m-Y');
            })
            ->addColumn('so_status', function ($db) {
                $status = $db->so_status == 1 ? __('common.active') : __('common.not_active');
                $class = $db->so_status == 1 ? 'bg-success' : 'bg-danger';
                return '<span class="badge ' . $class . '">' . $status . '</span>';
            })
            ->addColumn('row_class', function ($db) {
                return $db->so_status == 1 ? '' : 'inactive-row'; // Tambahkan kelas jika status tidak aktif
            })
            ->rawColumns(['action', 'so_status', 'so_date', 'so_id', 'row_class'])
            ->toJson();
    }

    public function dt_barcodeduplicate(Request $request, $id)
    {
        $jeniskatalogduplicate = $request->input('knowledge_type_id');
        $statusopenlibduplicate = $request->input('status');

        $start = $request->input('start');
        $length = $request->input('length');
        $draw = $request->input('draw');

        $query = (new StockopnameModel())->getBarcodeDuplicate($id, $jeniskatalogduplicate, $statusopenlibduplicate);

        $allData = $query->get();

        $totalRecords = $allData->count();
        $recordsFiltered = $totalRecords;

        // Pagination manual
        $data = $allData->slice($start, $length)->values();

        $data->transform(function ($item, $key) use ($start) {
            $item->row_number = $start + $key + 1;
            $item->total_member = $item->total_member;
            $item->master_data_fullname = $item->fullname;
            $item->jenis_katalog = $item->name;
            $item->title = $item->title;
            $item->no_klasifikasi = $item->cccode;
            $item->no_katalog = $item->kitcode;
            $item->barcode = $item->kscode;
            $item->sos_filename = $item->filename;
            return $item;
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function dt_barcodebelumso(Request $request, $so_id)
    {
        $jeniskatalogbelumso = $request->input('knowledge_type_id');
        $lokasiopenlibbelumso = $request->input('item_location_id');
        $statusopenlibbelumso = $request->input('status');
        $classification_start = $request->input('classification_start');
        $classification_end = $request->input('classification_end');

        // Ambil parameter pagination dari DataTables
        $start = $request->input('start');
        $length = $request->input('length');
        $draw = $request->input('draw');

        // Query data dengan filter, parameter pertama adalah $so_id
        $query = (new StockopnameModel())->getBarcodeBelumSo(
            $so_id,
            $jeniskatalogbelumso,
            $lokasiopenlibbelumso,
            $statusopenlibbelumso,
            $classification_start,
            $classification_end
        );

        $allData = $query->get();

        $totalRecords = $allData->count();
        $recordsFiltered = $totalRecords;

        // Pagination manual
        $data = $allData->slice($start, $length)->values();

        // Tambahkan nomor urut dan proses kolom status_openlib
        $data->transform(function ($item, $key) use ($start) {
            $item->row_number = $start + $key + 1;
            $item->formatted_status_openlibbelumso = match ($item->status_openlib) {
                1 => 'Tersedia',
                2 => 'Dipinjam',
                3 => 'Rusak',
                4 => 'Hilang',
                5 => 'Expired',
                6 => 'Hilang Diganti',
                7 => 'Sedang Diproses',
                8 => 'Cadangan',
                9 => 'Weeding',
                default => '',
            };
            return $item;
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function dtStatistikBarcodeBelumSo(Request $request, $id)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'No direct script access allowed'], 403);
        }

        // Ambil data dari request
        $itemLocationId = $request->input('item_location_id');
        $entranceDate = $request->input('entrance_date');

        // Query untuk Statistik Belum SO
        $query = DB::table('knowledge_type as kt')
            ->select(
                'kt.name',
                'kt.id as ktid',
                DB::raw('COUNT(DISTINCT kit.id) as judul'),
                DB::raw('COUNT(DISTINCT CASE WHEN ks.status = "1" THEN kit.id END) as judul1'),
                DB::raw('COUNT(DISTINCT CASE WHEN ks.status = "2" THEN kit.id END) as judul2'),
                DB::raw('COUNT(DISTINCT CASE WHEN ks.status = "3" THEN kit.id END) as judul3'),
                DB::raw('COUNT(DISTINCT CASE WHEN ks.status = "4" THEN kit.id END) as judul4'),
                DB::raw('COUNT(DISTINCT CASE WHEN ks.status = "5" THEN kit.id END) as judul5'),
                DB::raw('COUNT(DISTINCT CASE WHEN ks.status = "6" THEN kit.id END) as judul6'),
                DB::raw('COUNT(DISTINCT CASE WHEN ks.status = "7" THEN kit.id END) as judul7'),
                DB::raw('COUNT(DISTINCT CASE WHEN ks.status = "8" THEN kit.id END) as judul8'),
                DB::raw('COUNT(DISTINCT CASE WHEN ks.status = "9" THEN kit.id END) as judul9'),
                DB::raw('SUM(CASE WHEN ks.status = "1" THEN 1 ELSE 0 END) as eksemplar1'),
                DB::raw('SUM(CASE WHEN ks.status = "2" THEN 1 ELSE 0 END) as eksemplar2'),
                DB::raw('SUM(CASE WHEN ks.status = "3" THEN 1 ELSE 0 END) as eksemplar3'),
                DB::raw('SUM(CASE WHEN ks.status = "4" THEN 1 ELSE 0 END) as eksemplar4'),
                DB::raw('SUM(CASE WHEN ks.status = "5" THEN 1 ELSE 0 END) as eksemplar5'),
                DB::raw('SUM(CASE WHEN ks.status = "6" THEN 1 ELSE 0 END) as eksemplar6'),
                DB::raw('SUM(CASE WHEN ks.status = "7" THEN 1 ELSE 0 END) as eksemplar7'),
                DB::raw('SUM(CASE WHEN ks.status = "8" THEN 1 ELSE 0 END) as eksemplar8'),
                DB::raw('SUM(CASE WHEN ks.status = "9" THEN 1 ELSE 0 END) as eksemplar9')
            )
            ->leftJoin('knowledge_item as kit', 'kit.knowledge_type_id', '=', 'kt.id')
            ->leftJoin('knowledge_stock as ks', 'kit.id', '=', 'ks.knowledge_item_id')
            ->leftJoin('so_stock as sos', function ($join) use ($id) {
                $join->on('sos.sos_id_stock', '=', 'ks.id')
                    ->where('sos.sos_id_so', '=', $id);
            })
            ->leftJoin('knowledge_subject as kss', 'kit.knowledge_subject_id', '=', 'kss.id')
            ->whereNotIn('kt.id', [4, 5, 6, 21, 24, 25, 47, 49, 51, 52, 55, 62, 70, 73, 75, 79])
            ->where('kss.active', '1')
            ->whereNull('sos.sos_id_stock'); // Filter untuk Belum SO

        // Filter lokasi
        if ($itemLocationId) {
            $query->where('kit.item_location_id', $itemLocationId);
        }

        // Filter tanggal
        if ($entranceDate) {
            $dates = explode(' - ', $entranceDate);
            $startDate = $dates[0];
            $endDate = $dates[1];
            $query->whereBetween('ks.entrance_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        // Group by dan order by
        $query->groupBy('kt.id', 'kt.name')
            ->orderBy('kt.name', 'asc');

        // Ambil data
        $data = $query->get();

        // Format response
        $output = [
            "draw" => $request->input('draw'),
            "recordsTotal" => $data->count(),
            "recordsFiltered" => $data->count(),
            "data" => $data,
        ];

        return response()->json($output);
    }

    public function dtStatistikBarcodeSudahSo(Request $request, $id)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'No direct script access allowed'], 403);
        }

        // Ambil data dari request
        $itemLocationId = $request->input('item_location_id');
        $entranceDate = $request->input('entrance_date');
        $showStatus = $request->input('show_status');

        // Query untuk Statistik Sudah SO
        $query = DB::table('knowledge_type as kt')
            ->select(
                'kt.name',
                'kt.id as ktid',
                DB::raw('COUNT(DISTINCT kit.id) as judul'),
                DB::raw('COUNT(DISTINCT CASE WHEN ks.status = "1" THEN kit.id END) as judul1'),
                DB::raw('COUNT(DISTINCT CASE WHEN ks.status = "2" THEN kit.id END) as judul2'),
                DB::raw('COUNT(DISTINCT CASE WHEN ks.status = "3" THEN kit.id END) as judul3'),
                DB::raw('COUNT(DISTINCT CASE WHEN ks.status = "4" THEN kit.id END) as judul4'),
                DB::raw('COUNT(DISTINCT CASE WHEN ks.status = "5" THEN kit.id END) as judul5'),
                DB::raw('COUNT(DISTINCT CASE WHEN ks.status = "6" THEN kit.id END) as judul6'),
                DB::raw('COUNT(DISTINCT CASE WHEN ks.status = "7" THEN kit.id END) as judul7'),
                DB::raw('COUNT(DISTINCT CASE WHEN ks.status = "8" THEN kit.id END) as judul8'),
                DB::raw('COUNT(DISTINCT CASE WHEN ks.status = "9" THEN kit.id END) as judul9'),
                DB::raw('SUM(CASE WHEN ks.status = "1" THEN 1 ELSE 0 END) as eksemplar1'),
                DB::raw('SUM(CASE WHEN ks.status = "2" THEN 1 ELSE 0 END) as eksemplar2'),
                DB::raw('SUM(CASE WHEN ks.status = "3" THEN 1 ELSE 0 END) as eksemplar3'),
                DB::raw('SUM(CASE WHEN ks.status = "4" THEN 1 ELSE 0 END) as eksemplar4'),
                DB::raw('SUM(CASE WHEN ks.status = "5" THEN 1 ELSE 0 END) as eksemplar5'),
                DB::raw('SUM(CASE WHEN ks.status = "6" THEN 1 ELSE 0 END) as eksemplar6'),
                DB::raw('SUM(CASE WHEN ks.status = "7" THEN 1 ELSE 0 END) as eksemplar7'),
                DB::raw('SUM(CASE WHEN ks.status = "8" THEN 1 ELSE 0 END) as eksemplar8'),
                DB::raw('SUM(CASE WHEN ks.status = "9" THEN 1 ELSE 0 END) as eksemplar9')
            )
            ->leftJoin('knowledge_item as kit', 'kit.knowledge_type_id', '=', 'kt.id')
            ->leftJoin('knowledge_stock as ks', 'kit.id', '=', 'ks.knowledge_item_id')
            ->leftJoin('so_stock as sos', function ($join) use ($id) {
                $join->on('sos.sos_id_stock', '=', 'ks.id')
                    ->where('sos.sos_id_so', '=', $id);
            })
            ->leftJoin('knowledge_subject as kss', 'kit.knowledge_subject_id', '=', 'kss.id')
            ->whereNotIn('kt.id', [4, 5, 6, 21, 24, 25, 47, 49, 51, 52, 55, 62, 70, 73, 75, 79])
            ->where('kss.active', '1')
            ->whereNotNull('sos.sos_id_stock'); // Filter untuk Sudah SO

        // Filter lokasi
        if ($itemLocationId) {
            $query->where('kit.item_location_id', $itemLocationId);
        }

        // Filter tanggal
        if ($entranceDate) {
            $dates = explode(' - ', $entranceDate);
            $startDate = $dates[0];
            $endDate = $dates[1];
            $query->whereBetween('ks.entrance_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        // Tambahkan kolom dinamis berdasarkan show_status
        if ($showStatus === 'so') {
            // Berdasarkan status SO
            for ($i = 1; $i <= 9; $i++) {
                $query->addSelect([
                    DB::raw("COUNT(DISTINCT CASE WHEN sos.sos_status = '{$i}' THEN kit.id END) as judul{$i}"),
                    DB::raw("SUM(CASE WHEN sos.sos_status = '{$i}' THEN 1 ELSE 0 END) as eksemplar{$i}")
                ]);
            }
        } else {
            // Default atau openlib
            for ($i = 1; $i <= 9; $i++) {
                $query->addSelect([
                    DB::raw("COUNT(DISTINCT CASE WHEN ks.status = '{$i}' THEN kit.id END) as judul{$i}"),
                    DB::raw("SUM(CASE WHEN ks.status = '{$i}' THEN 1 ELSE 0 END) as eksemplar{$i}")
                ]);
            }
        }

        // Group by dan order by
        $query->groupBy('kt.id', 'kt.name')
            ->orderBy('kt.name', 'asc');

        // Ambil data
        $data = $query->get();

        // Format response
        $output = [
            "draw" => $request->input('draw'),
            "recordsTotal" => $data->count(),
            "recordsFiltered" => $data->count(),
            "data" => $data,
        ];

        return response()->json($output);
    }

    public function detail(Request $request, $id)
    {
        $stockopname = StockopnameModel::find($id);
        $locations = DB::table('item_location')->select('id', 'name')->get();
        $jeniskatalog = DB::connection('mysql')->table('knowledge_type')->select('id', 'name')->get();
        $users = DB::table('member')
        ->select('id', 'master_data_user', 'master_data_fullname')
        ->where('member_type_id', '1')
        ->whereIn('member_class_id', [1, 7])
        ->where('status', '1')
        ->orderBy('master_data_fullname')
        ->get();

        $currentUser = auth()->user();

        return view('config.catalog.stockopname.detail', [
            'locations' => $locations,
            'stockopname' => $stockopname,
            'jeniskatalog' => $jeniskatalog,
            'users' => $users,
            'id' => $id,
            'currentUser' => $currentUser
        ]);
    }

    public function detail_dt(Request $request, $id)
    {
        $jeniskatalog = $request->input('knowledge_type_id');
        $lokasiopenlib = $request->input('item_location_id');
        $lokasiso = $request->input('sos_id_location');
        $statusopenlib = $request->input('status');
        $statusso = $request->input('sos_status');
        // $condition = $request->input('status');
        $condition = $request->input('condition');
        $userId = $request->input('user_id');

        // Ambil parameter pagination dari DataTables
        $start = $request->input('start');
        $length = $request->input('length');
        $draw = $request->input('draw');

        // Query data dengan filter
        $query = (new StockopnameModel())->getStockDetails($id, $jeniskatalog, $lokasiopenlib, $lokasiso, $statusopenlib, $statusso, $condition, $userId);

        // Hitung total records tanpa filter
        $totalRecords = $query->count();

        // Hitung total records setelah filter (jika ada filter)
        $recordsFiltered = $query->count();

        // Ambil data dengan pagination
        $data = $query->skip($start)->take($length)->get();

        // Tambahkan nomor urut dan proses kolom status_openlib
        $data->transform(function ($item, $key) use ($start) {
            $item->row_number = $start + $key + 1;
            $item->formatted_date = (new DateTime($item->sos_date))->format('d-m-Y');
            $item->member = $item->master_data_user . ' - ' . $item->master_data_fullname;
            $item->formatted_status_openlib = match ($item->status_openlib) {
                1 => 'Tersedia',
                2 => 'Dipinjam',
                3 => 'Rusak',
                4 => 'Hilang',
                5 => 'Expired',
                6 => 'Hilang Diganti',
                7 => 'Sedang Diproses',
                8 => 'Cadangan',
                9 => 'Weeding',
                default => '',
            };
            $item->formatted_sos_status = match ($item->sos_status) {
                1 => 'Tersedia',
                2 => 'Dipinjam',
                3 => 'Rusak',
                4 => 'Hilang',
                5 => 'Expired',
                6 => 'Hilang Diganti',
                7 => 'Sedang Diproses',
                8 => 'Cadangan',
                9 => 'Weeding',
                default => '',
            };
            $item->action = ($item->sos_id_user == auth()->id())
                ? '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $item->sos_id . ')">
                    <i class="ti ti-trash" style="font-size: 1rem;"></i></button>'
                : '';

            return $item;
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function katalog(Request $request)
    {
        $locations = DB::connection('mysql')->table('item_location')->get();

        return view('config.catalog.stockopname.stockopnameKatalog', [
            'locations' => $locations,
            'selectedLocationId' => $request->input('item_location_id', ''),
        ]);
    }

    public function katalog_dt(Request $request)
    {
        $lokasiopenlib = $request->input('item_location_id');
        $status = $request->input('status');
        $classification_start = $request->input('classification_start');
        $classification_end = $request->input('classification_end');
        $year_start = $request->input('year_start');
        $year_end = $request->input('year_end');
        $terakhirpinjam = $request->input('terakhirpinjam');

        // Ambil parameter pagination dari DataTables
        $start = $request->input('start');
        $length = $request->input('length');
        $draw = $request->input('draw');

        // Query data dengan filter
        $query = (new StockopnameKatalogModel())->getKatalogWeedingQuery($lokasiopenlib, $status, $classification_start, $classification_end, $year_start, $year_end, $terakhirpinjam);

        // Hitung total records tanpa filter
        $totalRecords = $query->count();

        // Hitung total records setelah filter (jika ada filter)
        $recordsFiltered = $query->count();

        // Ambil data dengan pagination
        $data = $query->skip($start)->take($length)->get();

        // Tambahkan nomor urut dan proses kolom status_openlib
        $data->transform(function ($item, $key) use ($start) {
            $item->row_number = $start + $key + 1;

            // Proses kolom status_openlib
            $statusText = '';
            switch ($item->status_openlib) {
                case 1: $statusText = 'Tersedia'; break;
                case 2: $statusText = 'Dipinjam'; break;
                case 3: $statusText = 'Rusak'; break;
                case 4: $statusText = 'Hilang'; break;
                case 5: $statusText = 'Expired'; break;
                case 6: $statusText = 'Hilang Diganti'; break;
                case 7: $statusText = 'Sedang Diproses'; break;
                case 8: $statusText = 'Cadangan'; break;
                case 9: $statusText = 'Weeding'; break;
            }
            $item->status_openlib = '<span>' . $statusText . '</span>';

            return $item;
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function getbyid(Request $request)
    {
        return StockopnameModel::find($request->id)->toJson();
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $currentUserId = auth()->id(); // Mendapatkan ID user yang sedang login

        // Validasi apakah data ini milik user yang sedang login
        $record = DB::table('so_stock')->where('sos_id', $id)->first();

        if (!$record) {
            return response()->json(['status' => 'error', 'message' => 'Data not found'], 404);
        }

        if ($record->sos_id_user != $currentUserId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized action'], 403);
        }

        // Hapus data jika validasi lolos
        $deleted = DB::delete('DELETE FROM so_stock WHERE sos_id = ?', [$id]);

        if ($deleted) {
            return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Failed to delete data'], 500);
        }
    }

    public function delete_all(Request $request)
    {
        $currentUserId = auth()->id(); // Mendapatkan ID user yang sedang login
        $sos_id_so = $request->input('sos_id_so'); // ID stock opname yang sedang diproses

        // Hanya hapus data yang memiliki sos_id_user sesuai dengan user yang login DAN terkait dengan stock opname yang sedang diproses
        $deleted = DB::table('so_stock')
            ->where('sos_id_user', $currentUserId)
            ->where('sos_id_so', $sos_id_so) // Pastikan hanya menghapus data untuk stock opname yang sedang diproses
            ->delete();

        if ($deleted) {
            return response()->json(['status' => 'success', 'message' => 'All data successfully deleted']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'No data found to delete'], 404);
        }
    }

    public function getHistory($id)
    {
        // Ganti 'history_table' dengan nama tabel yang sesuai
        $history = DB::table('history_table')->where('sos_id', $id)->get(); // Ambil data history
        return response()->json($history);
    }

    public function check_barcode(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'No direct script access allowed'], 403);
        }

        $barcode = strtolower($request->input('barcode'));
        \Log::info('Checking barcode: ' . $barcode);

        $dt = StockopnameModel::CheckBarcode($barcode);

        if ($dt) {
            return response()->json(['success' => true, 'message' => "$barcode ada."]);
        } else {
            return response()->json(['success' => false, 'message' => "$barcode tidak ada."]);
        }
    }

    public function save_manual(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'barcode' => 'required|string',
            'sos_status' => 'required|integer', // Validasi untuk status SO
            'sos_filename' => 'required|string', // Validasi untuk filename
            'sos_id_location' => 'required|integer', // Validasi untuk lokasi
        ]);

        Log::info('Stock opname data: ', $request->all());

        try {
            $id = $request->input('id'); // ID stock opname
            $barcode = strtolower($request->input('barcode'));
            $sos_status = $request->input('sos_status');
            $sos_filename = $request->input('sos_filename');
            $sos_id_location = $request->input('sos_id_location');

            // Ambil data user dari session
            $userData = Session::get('userData');
            if (!$userData) {
                return response()->json(['status' => 'error', 'message' => 'Session user tidak ditemukan. Silakan login ulang.'], 401);
            }

            // Validasi barcode
            $barcodeData = StockopnameModel::CheckBarcode($barcode);
            if (!$barcodeData) {
                return response()->json(['status' => 'error', 'message' => 'Barcode tidak ditemukan.'], 400);
            }

            // Cek apakah ada data dengan kode yang sama untuk user yang sama DAN stock opname yang sama
            $existingData = DB::table('so_stock')
                ->join('knowledge_stock', 'so_stock.sos_id_stock', '=', 'knowledge_stock.id')
                ->join('knowledge_item', 'knowledge_stock.knowledge_item_id', '=', 'knowledge_item.id')
                ->where('so_stock.sos_id_user', $userData->id)
                ->where('so_stock.sos_id_so', $id) // Pastikan hanya menghapus data untuk stock opname yang sedang diproses
                ->where('knowledge_stock.code', $barcode) // Gunakan kolom 'code' dari 'knowledge_stock'
                ->select('so_stock.sos_id_stock')
                ->first();

            // Jika ada data lama dengan kode yang sama, hapus data tersebut
            if ($existingData) {
                DB::table('so_stock')
                    ->where('sos_id_user', $userData->id)
                    ->where('sos_id_so', $id) // Pastikan hanya menghapus data untuk stock opname yang sedang diproses
                    ->where('sos_id_stock', $existingData->sos_id_stock)
                    ->delete();
            }

            // Siapkan data untuk penyimpanan
            $data = [
                'sos_id_so' => $id,
                'sos_id_user' => $userData->id, // ID member
                'sos_id_stock' => $barcodeData->id,
                'sos_date' => now(),
                'sos_filename' => $sos_filename,
                'sos_status' => $sos_status,
                'sos_id_location' => $sos_id_location,
            ];

            // Insert ke tabel so_stock
            DB::table('so_stock')->insert($data);

            return response()->json(['status' => 'success']);
        } catch (\Throwable $th) {
            Log::error('Error saving stock opname: ' . $th->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat memproses data: ' . $th->getMessage()], 500);
        }
    }

    public function saveImage(Request $request)
    {
        $request->validate([
            'id' => 'required|integer', // ID stock opname
            'sos_status' => 'required|integer',
            'sos_id_location' => 'required|integer',
            'file' => 'required|file|mimes:txt',
        ]);

        try {
            $id = $request->input('id'); // ID stock opname
            $sos_status = $request->input('sos_status');
            $sos_id_location = $request->input('sos_id_location');

            // Ambil data user dari session
            $userData = Session::get('userData');
            if (!$userData) {
                return response()->json(['status' => 'error', 'message' => 'Session user tidak ditemukan. Silakan login ulang.'], 401);
            }

            // Proses file yang diupload
            $file = $request->file('file');
            $sos_filename = $file->getClientOriginalName();
            $fileContent = file($file->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            // Simpan nomor urut dan isi baris
            $codesWithIndex = [];
            foreach ($fileContent as $index => $code) {
                $codesWithIndex[] = [
                    'index' => $index + 1,
                    'code' => strtolower(trim($code)), // Normalisasi kode (lowercase dan trim)
                ];
            }

            if (empty($codesWithIndex)) {
                return response()->json(['status' => 'error', 'message' => 'File kosong atau format tidak sesuai.'], 400);
            }

            // Ambil semua kode unik dari file
            $codesFromFile = array_column($codesWithIndex, 'code');

            // Cek apakah ada data dengan kode yang sama untuk user yang sama DAN stock opname yang sama
            $existingData = DB::table('so_stock')
                ->join('knowledge_stock', 'so_stock.sos_id_stock', '=', 'knowledge_stock.id')
                ->join('knowledge_item', 'knowledge_stock.knowledge_item_id', '=', 'knowledge_item.id')
                ->where('so_stock.sos_id_user', $userData->id)
                ->where('so_stock.sos_id_so', $id) // Pastikan hanya menghapus data untuk stock opname yang sedang diproses
                ->whereIn('knowledge_stock.code', $codesFromFile) // Gunakan kolom 'code' dari 'knowledge_stock'
                ->select('so_stock.sos_id_stock')
                ->get();

            // Jika ada data lama dengan kode yang sama, hapus data tersebut
            if ($existingData->isNotEmpty()) {
                DB::table('so_stock')
                    ->where('sos_id_user', $userData->id)
                    ->where('sos_id_so', $id) // Pastikan hanya menghapus data untuk stock opname yang sedang diproses
                    ->whereIn('sos_id_stock', $existingData->pluck('sos_id_stock')->toArray())
                    ->delete();
            }

            // Cek setiap barcode
            $ada = [];
            $tidakAda = [];
            $uniqueCodes = [];

            foreach ($codesWithIndex as $item) {
                $code = $item['code'];
                $index = $item['index'];

                $dt = StockopnameModel::CheckBarcode($code);
                if ($dt) {
                    $ada[] = [
                        'index' => $index,
                        'code' => $code,
                    ];
                } else {
                    $tidakAda[] = [
                        'index' => $index,
                        'code' => $code,
                    ];
                }
            }

            // Jika tidak ada data yang ditemukan di database
            if (empty($ada) && !empty($tidakAda)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Tidak ada data yang ditemukan di database. Data tidak disimpan.',
                    'dataAda' => [],
                    'dataTidakAda' => $tidakAda,
                ]);
            }

            // Siapkan data untuk disimpan (hanya simpan data unik)
            $insertData = [];
            $uniqueCodes = []; // Untuk menyimpan kode unik yang sudah disimpan
            foreach ($ada as $item) {
                $code = $item['code'];
                if (!in_array($code, $uniqueCodes)) {
                    $stock = StockopnameModel::CheckBarcode($code);
                    if ($stock) {
                        $insertData[] = [
                            'sos_id_so' => $id,
                            'sos_id_user' => $userData->id,
                            'sos_id_stock' => $stock->id,
                            'sos_date' => now()->format('Y-m-d'),
                            'sos_filename' => $sos_filename,
                            'sos_status' => $sos_status,
                            'sos_id_location' => $sos_id_location,
                        ];
                        $uniqueCodes[] = $code; // Tandai kode ini sudah diproses
                    }
                }
            }

            // Simpan ke database jika ada data yang sesuai
            if (!empty($insertData)) {
                DB::table('so_stock')->insert($insertData);
            }

            // Hitung jumlah duplikat
            $duplicateCounts = array_count_values(array_column($codesWithIndex, 'code'));
            $duplicateInfo = [];
            foreach ($duplicateCounts as $code => $count) {
                if ($count > 1) {
                    $duplicateInfo[] = "$code total duplikat : $count";
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil diproses.',
                'dataAda' => $ada, // Kirim semua data yang ada di database (termasuk duplikat)
                'dataTidakAda' => $tidakAda,
                'duplicateInfo' => $duplicateInfo, // Informasi duplikat
            ]);

        } catch (\Throwable $th) {
            Log::error('Error saving stock opname: ' . $th->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat memproses data: ' . $th->getMessage()], 500);
        }
    }
}
