<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member\MemberNotification;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class MemberNotificationController extends Controller
{
    public function index()
    {
        $memberNotifications = MemberNotification::with('member')->get();
        return view('member.notification', compact('memberNotifications'));
    }

    // public function index()
    // {
    //     $memberNotifications = MemberNotification::all();
    //     return view('member.notification', compact('memberNotifications'));
    // }

    public function dt(Request $request)
    {
        try {
            // Pastikan relasi sudah ada dan benar
            $query = MemberNotification::with('member');

            return \Yajra\DataTables\Facades\DataTables::eloquent($query)
                ->editColumn('member_id', function ($notification) {
                    // Tampilkan nama member (dari relasi)
                    return $notification->member ? $notification->member->master_data_fullname : '-';
                })
                ->addColumn('action', function ($notification) {
                    // Tombol aksi (edit/delete) di sini
                    return '
                        <div class="btn-group my-btn-group">
                          <button class="btn rounded-pill btn-icon btn-label-primary waves-effect my-dropdown-toggle"
                            type="button"
                            data-id="' . $notification->id . '"
                          >
                            <i class="ti ti-dots-vertical"></i>
                          </button>
                          <ul class="dropdown-menu" style="display:none;">
                            <li>
                              <a class="dropdown-item d-flex align-items-center edit-btn"
                                 href="javascript:void(0);"
                                 data-id="' . $notification->id . '">
                                <i class="ti ti-edit ti-sm me-2"></i> Edit Data
                              </a>
                            </li>
                            <li>
                              <a class="dropdown-item d-flex align-items-center text-danger delete-btn"
                                 href="javascript:void(0);"
                                 data-id="' . $notification->id . '">
                                <i class="ti ti-trash me-2"></i> Delete Data
                              </a>
                            </li>
                          </ul>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true); // <-- Pastikan ada ini
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function insert(Request $request)
    {
        $request->validate([
            'member_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();

            // Set nilai default jika tidak disediakan
            $data['sent'] = $data['sent'] ?? 0;
            $data['error_message'] = $data['error_message'] ?? null;
            $data['created_by'] = $data['created_by'] ?? 'WorkflowDocumentState:NotifyMember';
            $data['updated_by'] = $data['updated_by'] ?? 'WorkflowDocumentState:NotifyMember';
            $data['created_at'] = $data['created_at'] ?? now();
            $data['updated_at'] = $data['updated_at'] ?? now();

            $notification = MemberNotification::create($data);

            DB::commit();

            return response()->json([
                'message' => 'Notification berhasil ditambahkan',
                'data' => $notification
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menambahkan notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $notification = MemberNotification::find($id);
        if (!$notification) {
            return response()->json(['message' => 'Notification tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $notification->id,
            'member_id' => $notification->member_id,
            'title' => $notification->title,
            'content' => $notification->content,
            'sent' => $notification->sent,
            'error_message' => $notification->error_message,
            'created_by' => $notification->created_by,
            'updated_by' => $notification->updated_by,
            'created_at' => $notification->created_at,
            'updated_at' => $notification->updated_at,
        ]);
    }

    public function update(Request $request, $id)
    {
        $notification = MemberNotification::find($id);
        if (!$notification) {
            return response()->json(['message' => 'Notification tidak ditemukan'], 404);
        }

        $request->validate([
            'member_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['updated_by'] = 'WorkflowDocumentState:NotifyMember';
            $data['updated_at'] = now();

            $notification->update($data);

            DB::commit();
            return response()->json([
                'message' => 'Notification berhasil diperbarui',
                'data' => $notification
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal memperbarui notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        $notification = MemberNotification::find($id);
        if (!$notification) {
            return response()->json(['message' => 'Notification tidak ditemukan'], 404);
        }

        DB::beginTransaction();
        try {
            $notification->delete();
            DB::commit();
            return response()->json(['message' => 'Notification berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menghapus notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function searchMembers(Request $request)
    {
        $searchTerm = $request->input('search', '');
        $members = \App\Models\Member\Member::select('master_data_user', 'master_data_fullname', 'master_data_number')
            ->where(function ($query) use ($searchTerm) {
                $query->where('master_data_user', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('master_data_fullname', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('master_data_number', 'LIKE', "%{$searchTerm}%");
            })
            ->get();

        return response()->json(['data' => $members]);
    }
}
