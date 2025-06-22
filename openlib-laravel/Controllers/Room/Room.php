<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use App\Models\Room\RoomGalleryModel;
use App\Models\Room\RoomModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Room extends Controller
{
    public function index()
    {
        if(!auth()->can('room-roomdata.view')){
            return redirect('/home');
        }

        $rooms = DB::connection('mysql')->table('room.room')->get();
        $locations = DB::connection('mysql')->table('item_location')->get();

        return view('room.room', [
            'rooms' => $rooms,
            'locations' => $locations
        ]);
    }

    public function getRuanganData(Request $request)
    {
        $locationId = $request->input('location');
        $data = (new RoomModel())->getRoomsWithGallery($locationId);

        return datatables($data)
            ->addColumn('action', function ($db) {
                $actionButton = '';

                if(auth()->canAtLeast(['room-roomdata-room.edit', 'room-roomdata-room.delete', 'room-roomdata-room.activate', 'room-roomdata-room.disable'])) {
                    if ($db->room_active == 1 && auth()->can('room-roomdata-room.activate')) {
                        $actionButton = '<li class="d-flex"><a class="dropdown-item d-flex align-items-center text-success" href="javascript:activate(\'' . $db->room_id . '\')"><i class="ti ti-check me-2"></i> ' . __('common.activate') . ' </a></li>';
                    } elseif ($db->room_active == 0 && auth()->can('room-roomdata-room.disable')) {
                        $actionButton = '<li class="d-flex"><a class="dropdown-item d-flex align-items-center text-warning" href="javascript:disable(\'' . $db->room_id . '\')"><i class="ti ti-ban me-2"></i> ' . __('common.deactivate') . ' </a></li>';
                    }

                    $btn = '<div class="btn-group">
                                <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">';

                    if(auth()->can('room-roomdata-room.edit')) {
                        $btn .= '<li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:edit(\'' . $db->room_id . '\')"><i class="ti ti-edit ti-sm me-2"></i> ' . __('common.edit_data') . ' </a></li>';
                    }

                    if(auth()->can('room-roomdata-room.delete')) {
                        $btn .= '<li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:del(\'' . $db->room_id . '\')"><i class="ti ti-trash me-2"></i> ' . __('common.delete_data') . ' </a></li>';
                    }

                    $btn .= $actionButton;

                    $btn .= '</ul>
                            </div>';

                    return $btn;
                }

                return '';
            })
            ->addColumn('room_name_formatted', function ($db) {
                if(auth()->can('room-roomdata.view')) {
                    $price = $db->room_price;
                    $hour = $db->room_hour;
                    $displayText = $db->room_name;

                    $formatRupiah = function ($angka) {
                        if ($angka) {
                            return 'Rp ' . number_format($angka, 0, ',', '.');
                        }
                        return '';
                    };

                    if ($price && $hour) {
                        $displayText .= ' (' . $formatRupiah($price) . ' / ' . $hour . ' jam)';
                    } else if ($price) {
                        $displayText .= ' (' . $formatRupiah($price) . ')';
                    } else if ($hour) {
                        $displayText .= ' (' . $hour . ' jam)';
                    }

                    return $displayText;
                }

                return '<span class="text-muted">Unauthorized</span>';
            })
            ->addColumn('room_active_formatted', function ($db) {
                if(auth()->can('room-roomdata.view')) {
                    $badgeClass = '';
                    $statusText = '';

                    if ($db->room_active === 0) {
                        $badgeClass = 'success';
                        $statusText = __('common.active');
                    } else if ($db->room_active === 1) {
                        $badgeClass = 'danger';
                        $statusText = __('common.not_active');
                    }

                    return '<span class="badge text-bg-' . $badgeClass . '">' . $statusText . '</span>';
                }

                return '<span class="text-muted">Unauthorized</span>';
            })
            ->rawColumns(['action', 'room_active_formatted', 'room_name_formatted'])
            ->toJson();
    }

    public function getGaleriData(Request $request)
    {
        $data = (new RoomGalleryModel())->getGalleryData();

        return datatables($data)
            ->addColumn('action', function ($db) {
                if(auth()->canAtLeast(['room-roomdata-gallery.view', 'room-roomdata-gallery.edit', 'room-roomdata-gallery.delete'])) {
                    $btn = '<div class="btn-group">
                                <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">';

                    if(auth()->can('room-roomdata-gallery.view')) {
                        $btn .= '<li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:showGallery(' . $db->rg_id . ')"><i class="ti ti-eye ti-sm me-2"></i> ' . __('common.detail_gallery') . ' </a></li>';
                    }

                    if(auth()->can('room-roomdata-gallery.edit')) {
                        $btn .= '<li class="d-flex"><a class="dropdown-item d-flex align-items-center" href="javascript:editGallery(\'' . $db->rg_id . '\')"><i class="ti ti-edit ti-sm me-2"></i> ' . __('common.edit_data') . ' </a></li>';
                    }

                    if(auth()->can('room-roomdata-gallery.delete')) {
                        $btn .= '<li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" href="javascript:delGallery(\'' . $db->rg_id . '\')"><i class="ti ti-trash me-2"></i> ' . __('common.delete_data') . ' </a></li>';
                    }

                    $btn .= '</ul>
                            </div>';

                    return $btn;
                }

                return '';
            })
            ->addColumn('rg_image', function ($db) {
                if(auth()->can('room-roomdata-gallery.view')) {
                    $images = explode(',', $db->rg_image);
                    $img = ltrim($images[0]);
                    if (!str_starts_with($img, 'storage/')) {
                        $img = 'storage/' . ltrim($img, '/');
                    }
                    $imageUrl = asset($img);
                    return '<img src="' . $imageUrl . '" alt="Room Image" style="width: 100px; height: auto; margin-right: 5px;">';
                }

                return '<span class="text-muted">Unauthorized</span>';
            })
            ->rawColumns(['action', 'rg_image'])
            ->toJson();
    }

    public function getbyid(Request $request)
    {
        return RoomModel::find($request->id)->toJson();
    }

    public function saveRuangan(Request $request)
    {
        try {
            $request->validate([
                'inp.room_name' => 'required|string|max:255',
                'inp.room_min' => 'required|integer',
                'inp.room_max' => 'required|integer',
                'inp.room_capacity' => 'required|integer',
                'inp.room_description' => 'nullable|string',
                'inp.room_price' => 'nullable|numeric|min:0',
                'inp.room_hour' => 'nullable|integer',
            ]);

            $inp = $request->inp;
            $dbs = RoomModel::find($request->id) ?? new RoomModel();

            foreach ($inp as $key => $value) {
                $dbs->$key = $value;
            }

            $dbs->save();

            return response()->json(['status' => 'success', 'message' => 'Success to save data']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Failed to save data', 'error' => $th->getMessage()]);
        }
    }

    public function delete(Request $request)
    {
        try {
            $id = $request->id;
            $data = RoomModel::find($id);

            if ($data) {
                $data->delete();
                return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data not found']);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Failed to delete data', 'error' => $th->getMessage()]);
        }
    }

    public function deleteGallery(Request $request)
    {
        try {
            $id = $request->id;
            $data = RoomGalleryModel::find($id);

            if ($data) {
                $data->delete();
                return response()->json(['status' => 'success', 'message' => 'Success to delete data']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Data not found']);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Failed to delete data', 'error' => $th->getMessage()]);
        }
    }

    public function toggleRoomStatus(Request $request)
    {
        $roomId = $request->input('room_id');
        $room = RoomModel::find($roomId);

        if ($room) {
            $room->room_active = $room->room_active == 0 ? 1 : 0;
            $room->save();

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'Room not found']);
    }

    public function getGalleryDetail($rg_id)
    {
        $gallery = RoomGalleryModel::find($rg_id);

        if ($gallery) {
            $room = RoomModel::find($gallery->rg_room_id);
            $roomName = $room ? $room->room_name : 'Unknown Room';

            $images = explode(',', $gallery->rg_image);
            $imageUrls = array_map(function($image) {
                $img = ltrim($image);
                if (!str_starts_with($img, 'storage/')) {
                    $img = 'storage/' . ltrim($img, '/');
                }
                return asset($img);
            }, $images);

            return response()->json([
                'images' => $imageUrls,
                'room_name' => $roomName,
            ]);
        }

        return response()->json(['message' => 'Gallery not found'], 404);
    }

    public function getGalleryById(Request $request)
    {
        $gallery = RoomGalleryModel::find($request->id);

        if ($gallery) {
            $images = explode(',', $gallery->rg_image);
            $imageUrls = array_map(function($image) {
                $img = ltrim($image);
                if (!str_starts_with($img, 'storage/')) {
                    $img = 'storage/' . ltrim($img, '/');
                }
                return asset($img);
            }, $images);

            return response()->json([
                'rg_id' => $gallery->rg_id,
                'rg_room_id' => $gallery->rg_room_id,
                'rg_image' => $imageUrls,
            ]);
        } else {
            return response()->json(['error' => 'Gallery not found'], 404);
        }
    }

    public function saveGallery(Request $request)
    {
        try {
            $request->validate([
                'inp.rg_image'   => 'required',
                'inp.rg_image.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            ], [
                'inp.rg_image.required' => 'Gambar wajib diupload.',
                'inp.rg_image.*.required' => 'Gambar wajib diupload.',
                'inp.rg_image.*.image' => 'Hanya file gambar yang diperbolehkan.',
                'inp.rg_image.*.mimes' => 'Hanya format jpeg, png, jpg, atau gif yang diperbolehkan.',
                'inp.rg_image.*.max'   => 'Ukuran file maksimal 5MB.',
            ]);

            $inp = $request->inp;
            $roomId = $inp['rg_room_id'] ?? null;
            $galleryId = $request->id ?? null;
            $deletedImages = $request->deleted_images ? explode(',', $request->deleted_images) : [];

            $existingGallery = RoomGalleryModel::where('rg_room_id', $roomId)->first();

            // Jika create dan sudah ada galeri untuk ruangan ini
            if (!$galleryId && $existingGallery) {
                return response()->json([
                    'status' =>  __('common.message_error_title'),
                    'message' =>  __('rooms.message_room_alreadyimage')
                ]);
            }

            if ($galleryId) {
                $dbs = RoomGalleryModel::find($galleryId);
                if (!$dbs) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Data tidak ditemukan.'
                    ]);
                }
                if ($existingGallery && $existingGallery->rg_id != $galleryId) {
                    return response()->json([
                        'status' => __('common.message_error_title'),
                        'message' => __('rooms.message_room_alreadyimage')
                    ]);
                }
            } else {
                $dbs = new RoomGalleryModel();
            }

            // Ambil gambar lama dari database
            $currentImages = [];
            if ($galleryId && $dbs->rg_image) {
                $currentImages = explode(',', $dbs->rg_image);
            }

            // Hapus gambar lama yang dihapus user
            if (!empty($deletedImages)) {
                foreach ($deletedImages as $index) {
                    if (isset($currentImages[$index])) {
                        $oldImagePath = public_path($currentImages[$index]);
                        if (file_exists($oldImagePath)) {
                            @unlink($oldImagePath);
                        }
                        unset($currentImages[$index]);
                    }
                }
                $currentImages = array_values($currentImages); // reindex
            }

            // Handle upload gambar baru
            if ($request->hasFile('inp.rg_image')) {
                foreach ($request->file('inp.rg_image') as $image) {
                    if ($image && $image->isValid()) {
                        $imagePath = $image->store('room/gallery', 'public');
                        $newImagePath = 'storage/' . $imagePath;
                        if (!in_array($newImagePath, $currentImages)) {
                            $currentImages[] = $newImagePath;
                        }
                    }
                }
            }

            $currentImages = array_unique($currentImages);
            $dbs->rg_image = implode(',', $currentImages);

            foreach ($inp as $key => $value) {
                if ($key !== 'rg_image') {
                    $dbs->$key = $value;
                }
            }

            $dbs->save();

            return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data', 'error' => $th->getMessage()]);
        }
    }

    public function getRoomList()
    {
        $rooms = \DB::connection('mysql')->table('room.room')->get(['room_id', 'room_name']);
        return response()->json($rooms);
    }
}
