<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Catalog\BookdeliveryserviceModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use DateTime;

class Bookdeliveryservice extends Controller
{
    public function index()
    {
        if(!auth()->can('catalog-bds.view')){
            return redirect('/home');
        }
        return view('config.catalog.bookdeliveryservice');
    }

    public function dt(Request $request)
    {
        $status = $request->input('bds_status');
        $data = (new BookdeliveryserviceModel())->getBookDeliveryService($status);

        return datatables($data)
            ->addColumn('action', function ($db) {
                if (!auth()->canAtLeast([
                    'catalog-bds.approve', 'catalog-bds.notApprove', 'catalog-bds.process',
                    'catalog-bds.send', 'catalog-bds.received', 'catalog-bds.completed'
                ])) return '';

                $buttons = '';
                $statuses = [
                    'Request' => ['approve' => 'primary', 'notApprove' => 'warning'],
                    'Approved' => ['process' => 'info'],
                    'Process' => ['send' => 'success'],
                    'Send' => ['received' => 'warning'],
                    'Received' => ['completed' => 'success'],
                ];

                if (isset($statuses[$db->bds_status])) {
                    foreach ($statuses[$db->bds_status] as $action => $btnClass) {
                        if (auth()->can("catalog-bds.$action")) {
                            $buttons .= '<button type="button" class="btn btn-sm btn-' . $btnClass . ' text-start mb-1" style="font-size: 10px;" title="' . $action . '" onclick="status(\'' . $db->bds_status . '\', \'' . $action . '\', ' . $db->bds_id . ')">' . ucfirst($action) . '</button>';
                        }
                    }
                }

                return $buttons ? '<div class="btn-group-vertical">' . $buttons . '</div>' : '';
            })
            ->addColumn('history', function ($db) {
                return auth()->can('catalog-bds.history')
                    ? '<div class="btn-group-vertical">
                            <button type="button" class="btn btn-sm btn-secondary text-start mb-1" style="font-size: 10px;" title="history" onclick="history(\'' . addslashes($db->concatenated_bdss_date) . '\', \'' . addslashes($db->concatenated_bdss_status) . '\')">History</button>
                    </div>'
                    : '';
            })
            ->addColumn('bds_photo_courier', function ($db) {
                if (!auth()->can('catalog-bds.view')) return '<span class="text-muted">Unauthorized</span>';
                if (empty($db->bds_photo_courier)) return '';

                $images = explode(',', $db->bds_photo_courier);
                $urls = array_map(function ($img) {
                    $img = ltrim($img);
                    if (!str_starts_with($img, 'storage/')) {
                        $img = 'storage/' . ltrim($img, '/');
                    }
                    return asset($img);
                }, $images);
                $jsonUrls = htmlspecialchars(json_encode($urls), ENT_QUOTES, 'UTF-8');

                return '<img src="' . $urls[0] . '" alt="Foto Kurir" style="width: 150px; height: auto; margin-right: 5px; cursor:pointer;" class="courier-photo-gallery" data-images=\'' . $jsonUrls . '\'>';
            })
            ->addColumn('item_codes', function ($db) {
                if (!auth()->can('catalog-bds.view')) return '<span class="text-muted">Unauthorized</span>';
                $books = (new BookdeliveryserviceModel())->getBookDeliveryServiceBooks($db->bds_id);
                return collect($books)->map(fn($b) => '<div>' . $b->bdsb_item_code . '</div>')->implode('');
            })
            ->addColumn('stock_codes', function ($db) {
                if (!auth()->can('catalog-bds.view')) return '<span class="text-muted">Unauthorized</span>';
                $books = (new BookdeliveryserviceModel())->getBookDeliveryServiceBooks($db->bds_id);
                return collect($books)->map(fn($b) => '<div>' . $b->bdsb_stock_code . '</div>')->implode('');
            })
            ->addColumn('bds_createdate', function ($db) {
                return auth()->can('catalog-bds.view')
                    ? (new DateTime($db->bds_createdate))->format('d-m-Y')
                    : '<span class="text-muted">Unauthorized</span>';
            })
            ->addColumn('bds_status', function ($db) {
                if (!auth()->can('catalog-bds.view')) return '<span class="text-muted">Unauthorized</span>';
                $badgeMap = [
                    'Request' => 'warning', 'Approved' => 'primary', 'Not Approved' => 'danger',
                    'Process' => 'info', 'Completed' => 'success', 'Send' => 'light', 'Received' => 'dark'
                ];
                $class = $badgeMap[$db->bds_status] ?? 'secondary';
                return '<span class="badge text-bg-' . $class . '">' . $db->bds_status . '</span>';
            })
            ->rawColumns([
                'action', 'history', 'bds_photo_courier',
                'item_codes', 'stock_codes', 'bds_createdate', 'bds_status'
            ])
            ->toJson();
    }

    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'bds_status' => 'required|string',
            'bds_reason' => 'nullable|string',
            'barcodes' => 'nullable|array',
            'inp.bds_photo_courier.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:512000',
        ]);

        $booking = BookdeliveryserviceModel::find($id);
        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $status = $request->bds_status;
        $booking->bds_status = $status;
        $booking->bds_reason = ($status === 'Not Approved') ? $request->bds_reason : null;
        $booking->bds_createdate = now();
        $booking->save();

        DB::table('book_delivery_service_status')->insert([
            'bdss_idbds' => $booking->bds_id,
            'bdss_status' => $status,
            'bdss_date' => now(),
        ]);

        // Jika upload foto saat status 'Received', langsung alihkan ke saveGallery
        if ($status === 'Received' && $request->hasFile('inp.bds_photo_courier')) {
            return $this->saveGallery($request);
        }

        $date = now();
        $messages = match($status) {
            'Approved'     => "Permintaan peminjaman buku dengan no pesanan {$booking->bds_number} pada tanggal $date telah disetujui.",
            'Send'         => "Permintaan peminjaman buku dengan no pesanan {$booking->bds_number} pada tanggal $date sedang dikirim ke alamat yang tertera.",
            'Completed'    => "Proses pengiriman buku dengan no pesanan {$booking->bds_number} pada tanggal $date sudah selesai.",
            'Process'      => "Permintaan peminjaman buku dengan no pesanan {$booking->bds_number} pada tanggal $date sedang diproses.",
            'Not Approved' => "Mohon maaf, permintaan peminjaman buku dengan no pesanan {$booking->bds_number} pada tanggal $date tidak disetujui, karena: {$request->bds_reason}",
            default        => "Status permintaan peminjaman buku no {$booking->bds_number} pada tanggal $date telah diperbarui menjadi $status.",
        };

        DB::table('batik.notification_mobile')->insert([
            'notif_id_member' => session('userData')->master_data_user,
            'notif_type' => 'bookdelivery',
            'notif_content' => $messages,
            'notif_date' => now(),
            'notif_status' => 'unread',
            'notif_id_detail' => $id,
        ]);

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function saveGallery(Request $request)
    {
        try {
            $request->validate([
                'inp.bds_photo_courier' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            ], [
                'inp.bds_photo_courier.required' => 'Gambar wajib diupload.',
                'inp.bds_photo_courier.image' => 'Hanya file gambar yang diperbolehkan.',
                'inp.bds_photo_courier.mimes' => 'Format hanya jpeg, png, jpg, gif.',
                'inp.bds_photo_courier.max' => 'Ukuran maksimal 5MB.',
            ]);

            $inp = $request->inp;
            $bdsId = $inp['bds_id'] ?? null;
            $deletedImages = explode(',', $request->deleted_images ?? '');

            $dbs = BookdeliveryserviceModel::where('bds_id', $bdsId)->first();
            if (!$dbs) return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan.']);

            $currentImages = $dbs->bds_photo_courier ? explode(',', $dbs->bds_photo_courier) : [];

            foreach ($deletedImages as $index) {
                if (isset($currentImages[$index])) {
                    $path = public_path($currentImages[$index]);
                    if (file_exists($path)) @unlink($path);
                    unset($currentImages[$index]);
                }
            }

            if ($request->hasFile('inp.bds_photo_courier')) {
                $image = $request->file('inp.bds_photo_courier');
                if ($image->isValid()) {
                    $imagePath = $image->store('catalog/fotokurirbds', 'public');
                    $currentImages = ['storage/' . $imagePath]; // replace all
                }
            }

            $dbs->bds_photo_courier = implode(',', array_values(array_unique($currentImages)));
            $dbs->bds_status = 'Received';
            $dbs->bds_createdate = now();

            foreach ($inp as $key => $value) {
                if ($key !== 'bds_photo_courier') $dbs->$key = $value;
            }

            $dbs->save();

            DB::table('book_delivery_service_status')->insert([
                'bdss_idbds' => $dbs->bds_id,
                'bdss_status' => 'Received',
                'bdss_date' => now(),
            ]);

            DB::table('batik.notification_mobile')->insert([
                'notif_id_member' => session('userData')->master_data_user,
                'notif_type' => 'bookdelivery',
                'notif_content' => "Permintaan peminjaman buku dengan no pesanan {$dbs->bds_number} pada tanggal " . now() . " sudah diterima.",
                'notif_date' => now(),
                'notif_status' => 'unread',
                'notif_id_detail' => $dbs->bds_id,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data', 'error' => $th->getMessage()]);
        }
    }

    public function getbyid(Request $request)
    {
        return BookdeliveryserviceModel::find($request->id)->toJson();
    }

    public function checkEksemplar(Request $request)
    {
        $request->validate([
            'item' => 'required|string',
            'barcode' => 'required|string',
            'memberid' => 'required|integer',
        ]);

        $model = new BookdeliveryserviceModel();
        $result = $model->checkEksemplar($request->item, $request->barcode, $request->memberid);

        return response()->json(['exists' => $result->isNotEmpty()]);
    }

    public function getBookDeliveryServiceBooks($id)
    {
        $model = new BookdeliveryserviceModel();
        $books = $model->getBookDeliveryServiceBooks($id);
        // Ambil memberid dari booking
        $booking = BookdeliveryserviceModel::find($id);
        $memberid = $booking ? $booking->bds_idmember : null;

        return response()->json([
            'books' => $books,
            'memberid' => $memberid
        ]);
    }
}
