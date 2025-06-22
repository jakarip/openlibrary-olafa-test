<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Catalog\UsulanbahanpustakaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;

class Usulanbahanpustaka extends Controller
{
    public function index()
    {
        if(!auth()->can('catalog-usulanbahanpustaka.view')){
            return redirect('/home');
        }

        return view('config.catalog.usulanbahanpustaka');
    }

    public function dt(Request $request)
    {
        $status = $request->input('bp_status');
        $data = (new UsulanbahanpustakaModel())->getUsulanBahanPustaka($status);

        return datatables($data)
            ->addColumn('action', function ($db) {
                if (!auth()->canAtLeast([
                    'catalog-usulanbahanpustaka.approve',
                    'catalog-usulanbahanpustaka.notApprove',
                    'catalog-usulanbahanpustaka.completed',
                    'catalog-usulanbahanpustaka.process'
                ])) return '';

                $buttons = '';

                if ($db->bp_status === 'Request' && auth()->can('catalog-usulanbahanpustaka.approve')) {
                    $buttons .= '<button type="button" class="btn btn-sm btn-primary text-start mb-1" style="font-size: 10px;" title="approve" onclick="status(\'' . $db->bp_status . '\', \'approve\', ' . $db->bp_id . ')">Approve</button>';
                    $buttons .= '<button type="button" class="btn btn-sm btn-warning text-start mb-1" style="font-size: 10px;" title="notApprove" onclick="status(\'' . $db->bp_status . '\', \'notApprove\', ' . $db->bp_id . ')">Not Approve</button>';
                }

                if ($db->bp_status === 'Approved' && auth()->can('catalog-usulanbahanpustaka.process')) {
                    $buttons .= '<button type="button" class="btn btn-sm btn-info text-start mb-1" style="font-size: 10px;" title="process" onclick="status(\'' . $db->bp_status . '\', \'process\', ' . $db->bp_id . ')">Process</button>';
                }

                if ($db->bp_status === 'Process' && auth()->can('catalog-usulanbahanpustaka.completed')) {
                    $buttons .= '<button type="button" class="btn btn-sm btn-success text-start mb-1" style="font-size: 10px;" title="completed" onclick="status(\'' . $db->bp_status . '\', \'completed\', ' . $db->bp_id . ')">Completed</button>';
                }

                return '<div class="btn-group-vertical">' . $buttons . '</div>';
            })
            ->addColumn('bp_createdate', function ($db) {
                return auth()->can('catalog-usulanbahanpustaka.view')
                    ? date('d-m-Y', strtotime($db->bp_createdate))
                    : '<span class="text-muted">Unauthorized</span>';
            })
            ->addColumn('bp_status', function ($db) {
                if (!auth()->can('catalog-usulanbahanpustaka.view')) {
                    return '<span class="text-muted">Unauthorized</span>';
                }

                $statusMap = [
                    'Request'      => 'warning',
                    'Approved'     => 'primary',
                    'Not Approved' => 'danger',
                    'Process'      => 'info',
                    'Completed'    => 'success',
                ];

                $badgeClass = $statusMap[$db->bp_status] ?? 'secondary';
                return '<span class="badge text-bg-' . $badgeClass . '">' . $db->bp_status . '</span>';
            })
            ->addColumn('history', function ($db) {
                if (!auth()->can('catalog-usulanbahanpustaka.history')) return '';
                return '
                    <div class="btn-group-vertical">
                        <button type="button" class="btn btn-sm btn-secondary text-start mb-1" style="font-size: 10px;" title="history"
                            onclick="history(\'' . addslashes($db->concatenated_bps_date) . '\', \'' . addslashes($db->concatenated_bps_status) . '\')">'
                            . __('catalogs.bahanpustaka_table_history') .
                        '</button>
                    </div>';
            })
            ->rawColumns(['action', 'history', 'bp_createdate', 'bp_status'])
            ->toJson();
    }

    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'bp_status' => 'required|string',
            'bp_reason' => 'nullable|string',
        ]);

        $booking = UsulanbahanpustakaModel::find($id);
        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $booking->bp_status = $request->bp_status;

        if ($request->bp_status === 'Not Approved' && $request->bp_reason) {
            $booking->bp_reason = $request->bp_reason;
        }

        $booking->save();

        DB::table('usulan_bahanpustaka_status')->insert([
            'bps_idbp'    => $booking->bp_id,
            'bps_status'  => $request->bp_status,
            'bps_date'    => now(),
        ]);

        $status = $request->bp_status;
        $judul  = $booking->bp_title;
        $tanggal = now();

        $messages = match ($status) {
            'Approved'     => "Permintaan usulan bahan pustaka dengan judul $judul pada tanggal $tanggal telah disetujui.",
            'Process'      => "Permintaan usulan bahan pustaka dengan judul $judul pada tanggal $tanggal sedang diproses.",
            'Completed'    => "Permintaan usulan bahan pustaka dengan judul $judul pada tanggal $tanggal telah tersedia di Tel-U Open Library.",
            'Not Approved' => "Permintaan usulan bahan pustaka dengan judul $judul pada tanggal $tanggal tidak disetujui. Alasan: {$request->bp_reason}",
            'Cancel'       => "Permintaan usulan bahan pustaka dengan judul $judul pada tanggal $tanggal telah dibatalkan.",
            default        => "Permintaan usulan bahan pustaka dengan judul $judul pada tanggal $tanggal telah diubah statusnya menjadi $status.",
        };

        DB::table('batik.notification_mobile')->insert([
            'notif_id_member'  => session()->get('userData')->master_data_user,
            'notif_type'       => 'bahanpustaka',
            'notif_content'    => $messages,
            'notif_date'       => now(),
            'notif_status'     => 'unread',
            'notif_id_detail'  => $id,
        ]);

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function completed(Request $request)
    {
        if (!$request->ajax()) {
            return response('No direct script access allowed', 403);
        }

        $id = $request->input('id');

        // Ambil data dari tabel usulan_bahanpustaka menggunakan bp_id
        $model = new UsulanbahanpustakaModel();
        $data = $model->getUsulanBahanPustaka()->where('bp_id', $id)->first();

        if (!$data) {
            return response()->json(["status" => false, "message" => "Data usulan bahan pustaka tidak ditemukan"], 404);
        }

        // Generate kode unik
        $book = DB::table('batik.knowledge_item2')
            ->where('code', 'like', date("y") . '.' . sprintf("%02d", 1) . '%')
            ->orderBy('code', 'desc')
            ->first();

        if ($book) {
            $temp = explode(".", $book->code);
            $code = date("y") . '.' . sprintf("%02d", 1) . '.' . sprintf("%03d", ($temp[2] + 1));
        } else {
            $code = date("y") . '.' . sprintf("%02d", 1) . '.001';
        }

        // Data untuk knowledge_item
        $inp2 = [
            'knowledge_type_id' => 1,
            'classification_code_id' => 1,
            'item_location_id' => 9,
            'faculty_code' => $data->C_KODE_FAKULTAS ?? '0',
            'course_code' => $data->C_KODE_PRODI ?? '0',
            'title' => $data->bp_title ?? '0',
            'author' => $data->bp_author ?? '0',
            'knowledge_subject_id' => 6942,
            'author_type' => 1,
            'editor' => null,
            'publisher_name' => $data->bp_publisher ?? '',
            'publisher_city' => null,
            'published_year' => $data->bp_publishedyear ?? '0',
            'origination' => '1',
            'supplier' => 'Universitas Telkom, ' . ucwords(strtolower($data->nama_fakultas)),
            'price' => 0,
            'entrance_date' => now(),
            'abstract_content' => '',
            'penalty_cost' => 0,
            'rent_cost' => 0,
            'created_by' => session()->get('userData')->master_data_user,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_by' => session()->get('userData')->master_data_user,
            'updated_at' => date('Y-m-d H:i:s'),
            'softcopy_path' => $code,
            'code' => $code,
            'collation' => '',
        ];

        // Masukkan data ke knowledge_item
        $ids = DB::table('batik.knowledge_item2')->insertGetId($inp2);

        // Data untuk knowledge_stock
        $koleksi = [
            'knowledge_item_id' => $ids,
            'knowledge_type_id' => 1,
            'item_location_id' => 9,
            'code' => $code . '-1',
            'faculty_code' => $data->C_KODE_FAKULTAS ?? '0',
            'course_code' => $data->C_KODE_PRODI ?? '0',
            'origination' => '1',
            'supplier' => 'Universitas Telkom, ' . ucwords(strtolower($data->nama_fakultas)),
            'price' => '0',
            'entrance_date' => date('Y-m-d'),
            'status' => '1',
            'created_by' => session()->get('userData')->master_data_user,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_by' => session()->get('userData')->master_data_user,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Masukkan data ke knowledge_stock
        $ids2 = DB::table('batik.knowledge_stock')->insertGetId($koleksi);

        // Update status di usulan_bahanpustaka menggunakan bp_id
        DB::table('batik.usulan_bahanpustaka')
            ->where('bp_id', $id)
            ->update(['bp_status' => 'Completed']);

        // Kirim response ke frontend
        return response()->json(["status" => true, "message" => "Data berhasil ditransfer ke katalog", "id" => $ids]);
    }
}
