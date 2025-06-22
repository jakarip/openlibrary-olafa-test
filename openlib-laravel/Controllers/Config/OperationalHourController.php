<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperationalHourController extends Controller
{
    public function index()
    {
        // Tidak menggunakan pengecekan otorisasi di sini, sesuai dengan pola di Holiday.php
        $locations = DB::connection('mysql')->table('item_location')->get();

        if (!auth()->can('config-operational-hour.view')) {
            return redirect('/home');
        }

        return view('config.operationalhour.index', [
            'locations' => $locations
        ]);
    }

    public function dt(Request $request)
    {
        try {
            $data = DB::table('item_location_hour')
                ->select('item_location_hour.*', 'item_location.name as location_name')
                ->join('item_location', 'item_location.id', '=', 'item_location_hour.ilh_item_location')
                ->get();

            return datatables($data)
                ->addColumn('action', function ($db) {
                    // Menggunakan format yang sama persis dengan Holiday.php
                    $btn = '<div class="btn-group my-btn-group">';
                    $btn .= '<button class="btn rounded-pill btn-icon btn-label-primary waves-effect my-dropdown-toggle" type="button" data-id="' . $db->ilh_id . '">';
                    $btn .= '<i class="ti ti-dots-vertical"></i>';
                    $btn .= '</button>';
                    $btn .= '<ul class="dropdown-menu" style="display:none;">';
                    $btn .= '<li><a class="dropdown-item d-flex align-items-center edit-btn" href="javascript:void(0);" data-id="' . $db->ilh_id . '">';
                    $btn .= '<i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>';
                    $btn .= '<li><a class="dropdown-item d-flex align-items-center text-danger delete-btn" href="javascript:void(0);" data-id="' . $db->ilh_id . '">';
                    $btn .= '<i class="ti ti-trash me-2"></i> Delete Data</a></li>';
                    $btn .= '</ul></div>';
                    return $btn;
                })
                ->addColumn('location_name', function ($db) {
                    return $db->location_name ?? 'Global';
                })
                ->rawColumns(['action'])
                ->toJson();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getById($id)
    {
        try {
            $data = DB::table('item_location_hour')->where('ilh_id', $id)->first();
            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get operational hour data'], 500);
        }
    }

    public function save(Request $request)
    {
        try {
            $inp = $request->inp;
            $ilh_id = $request->ilh_id;
            $location_id = $inp['ilh_item_location'];
            $date = $inp['ilh_date'];
            $hours = $inp['ilh_hour'];

            DB::beginTransaction();

            // 1. Cek apakah sudah ada record dengan kombinasi lokasi dan tanggal yang sama
            $existingRecord = DB::table('item_location_hour')
                ->where('ilh_item_location', $location_id)
                ->where('ilh_date', $date)
                ->first();

            // 2. Tentukan aksi berdasarkan hasil pengecekan
            if ($existingRecord) {
                // Ada data existing dengan tanggal yang sama
                if ($ilh_id && $ilh_id == $existingRecord->ilh_id) {
                    // Case: Edit data yang memang sedang diedit
                    DB::table('item_location_hour')
                        ->where('ilh_id', $ilh_id)
                        ->update([
                            'ilh_hour' => $hours
                        ]);
                    $actionTaken = 'updated';
                } else {
                    // Case: Data baru/edit tapi konflik dengan data lain
                    // Update data yang sudah ada
                    DB::table('item_location_hour')
                        ->where('ilh_id', $existingRecord->ilh_id)
                        ->update([
                            'ilh_hour' => $hours
                        ]);
                    $actionTaken = 'updated_existing';
                }
            } else {
                // Tidak ada data dengan tanggal yang sama, lakukan insert baru
                DB::table('item_location_hour')->insert([
                    'ilh_item_location' => $location_id,
                    'ilh_hour' => $hours,
                    'ilh_date' => $date
                ]);
                $actionTaken = 'created';
            }

            DB::commit();

            // 3. Return response yang sesuai
            $messages = [
                'created' => 'Jam operasional berhasil ditambahkan',
                'updated' => 'Jam operasional berhasil diperbarui',
                'updated_existing' => 'Jam operasional untuk tanggal tersebut sudah ada dan telah diperbarui'
            ];

            return response()->json([
                'status' => 'success',
                'message' => $messages[$actionTaken],
                'action' => $actionTaken
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan jam operasional',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $id = $request->id;

            DB::beginTransaction();

            DB::table('item_location_hour')->where('ilh_id', $id)->delete();

            DB::commit();

            return response()->json(['message' => 'Operational hour deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete operational hour',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
