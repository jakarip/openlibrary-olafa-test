<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member\Member;
use App\Models\Member\MemberType;
use App\Models\Member\MemberClass;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\MemberApprovedMail;
use App\Mail\MemberRejectedMail;
use Illuminate\Support\Facades\Storage;
class MemberDataController extends Controller
{
    public function index()
    {
        $memberClasses = MemberClass::all();
        $memberTypes = MemberType::all();
        return view('member.data', compact('memberClasses', 'memberTypes'));
    }

    public function dt(Request $request)
    {
        try {
            $query = Member::select('member.*', 'member_type.name as member_type_name')
                ->leftJoin('member_type', 'member.member_type_id', '=', 'member_type.id');

            if ($request->has('member_type_id') && $request->member_type_id != '') {
                $query->where('member.member_type_id', $request->member_type_id);
            }

            if ($request->has('status') && $request->status != '') {
                $query->where('member.status', $request->status);
            }

            // Ambil kolom tambahan yang dipilih oleh user (pastikan dalam bentuk array)
            $selectedColumns = (array) $request->input('selected_columns', []);

            $dataTable = DataTables::eloquent($query)
                ->addColumn('action', function ($member) {
                    $actionButtons = '
                <div class="btn-group my-btn-group">
                  <button class="btn rounded-pill btn-icon btn-label-primary waves-effect my-dropdown-toggle"
                    type="button"
                    data-id="' . $member->id . '"
                  >
                    <i class="ti ti-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu" style="display:none;">';

                    if ($member->status == 2) {
                        $actionButtons .= '
                    <li>
                      <a class="dropdown-item d-flex align-items-center text-success approve-btn"
                         href="javascript:void(0);"
                         data-id="' . $member->id . '">
                        <i class="ti ti-check me-2"></i> Approve
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item d-flex align-items-center text-danger reject-btn"
                         href="javascript:void(0);"
                         data-id="' . $member->id . '">
                        <i class="ti ti-x me-2"></i> Reject
                      </a>
                    </li>
                    <li class="dropdown-divider"></li>';
                    }

                    $actionButtons .= '
                    <li>
                      <a class="dropdown-item d-flex align-items-center edit-btn"
                         href="javascript:void(0);"
                         data-id="' . $member->id . '">
                        <i class="ti ti-edit ti-sm me-2"></i> Edit Data
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item d-flex align-items-center text-danger delete-btn"
                         href="javascript:void(0);"
                         data-id="' . $member->id . '">
                        <i class="ti ti-trash me-2"></i> Delete Data
                      </a>
                    </li>
                  </ul>
                </div>';

                    return $actionButtons;
                })


                ->editColumn('member_type_id', function ($member) {
                    return $member->member_type_name ?? '-';
                })
                ->filterColumn('member_type_name', function ($query, $keyword) {
                    $query->whereHas('memberType', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    });
                });

            if (in_array('master_data_status', $selectedColumns)) {
                $dataTable->addColumn('master_data_status', function ($member) {
                    return $member->master_data_status ?? '-';
                });
            }

            if (in_array('master_data_ijasah', $selectedColumns)) {
                $dataTable->addColumn('master_data_ijasah', function ($member) {
                    return !empty($member->master_data_ijasah) ? $member->master_data_ijasah : '-';
                });
            }

            if (in_array('master_data_ktp', $selectedColumns)) {
                $dataTable->addColumn('master_data_ktp', function ($member) {
                    return !empty($member->master_data_ktp) ? $member->master_data_ktp : '-';
                });
            }

            if (in_array('master_data_idcard', $selectedColumns)) {
                $dataTable->addColumn('master_data_idcard', function ($member) {
                    return !empty($member->master_data_idcard) ? $member->master_data_idcard : '-';
                });
            }

            if (in_array('created_at', $selectedColumns)) {
                $dataTable->addColumn('created_at', function ($member) {
                    return (!empty($member->created_at) && $member->created_at != '0000-00-00 00:00:00')
                        ? \Carbon\Carbon::parse($member->created_at)->format('Y-m-d H:i:s')
                        : '-';
                });
            }

            if (in_array('updated_at', $selectedColumns)) {
                $dataTable->addColumn('updated_at', function ($member) {
                    return (!empty($member->updated_at) && $member->updated_at != '0000-00-00 00:00:00')
                        ? \Carbon\Carbon::parse($member->updated_at)->format('Y-m-d H:i:s')
                        : '-';
                });
            }


            return $dataTable->rawColumns(['action'])->toJson();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function insert(Request $request)
    {
        $request->validate([
            'member_type_id' => 'required|exists:member_type,id',
            'member_class_id' => 'nullable|exists:member_class,id',
            'master_data_fullname' => 'required|string',
            'master_data_email' => 'required|email|unique:member,master_data_email',
            'master_data_mobile_phone' => 'nullable|string',
            'status' => 'required|string',

            // Field opsional lainnya
            // 'master_data_user' => 'nullable|string',
            'master_data_status' => 'nullable|string',
            'master_data_ijasah' => 'nullable|string',
            'master_data_ktp' => 'nullable|string',
            'master_data_idcard' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();

            if (empty($data['created_by'])) {
                $data['created_by'] = 'admin';
            }

            if (empty($data['updated_by'])) {
                $data['updated_by'] = 'admin';
            }

            if (empty($data['created_at'])) {
                $data['created_at'] = now();
            }
            if (empty($data['updated_at'])) {
                $data['updated_at'] = now();
            }

            if (empty($data['member_class_id'])) {
                $data['member_class_id'] = 2;
            }

            $member = Member::create($data);
            DB::commit();

            return response()->json([
                'message' => 'Member berhasil ditambahkan',
                'data' => $member
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menambahkan member',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function edit($id)
    {
        $member = Member::find($id);
        if (!$member) {
            return response()->json(['message' => 'Member tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $member->id,
            'master_data_fullname' => $member->master_data_fullname,
            'master_data_email' => $member->master_data_email,
            'master_data_mobile_phone' => $member->master_data_mobile_phone,
            'status' => $member->status,
            'member_type_id' => $member->member_type_id,

            // Additional data
            'master_data_user' => $member->master_data_user,
            'master_data_status' => $member->master_data_status,
            'master_data_ijasah' => $member->master_data_ijasah,
            'master_data_ktp' => $member->master_data_ktp,
            'master_data_idcard' => $member->master_data_idcard
        ]);
    }


    public function update(Request $request, $id)
    {
        $member = Member::find($id);
        if (!$member) {
            return response()->json(['message' => 'Member tidak ditemukan'], 404);
        }

        $request->validate([
            'member_type_id' => 'required|exists:member_type,id',
            'master_data_fullname' => 'required',
            'master_data_email' => 'required|email|unique:member,master_data_email,' . $id,
            'master_data_mobile_phone' => 'nullable',
            'status' => 'required',

            // Additional fields
            'master_data_user' => 'nullable|string',
            'master_data_status' => 'nullable|string',
            'master_data_ijasah' => 'nullable|string',
            'master_data_ktp' => 'nullable|string',
            'master_data_idcard' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $member->update([
                'member_type_id' => $request->member_type_id,
                'master_data_fullname' => $request->master_data_fullname,
                'master_data_email' => $request->master_data_email,
                'master_data_mobile_phone' => $request->master_data_mobile_phone,
                'status' => $request->status,

                // Additional fields
                'master_data_user' => $request->master_data_user,
                'master_data_status' => $request->master_data_status,
                'master_data_ijasah' => $request->master_data_ijasah,
                'master_data_ktp' => $request->master_data_ktp,
                'master_data_idcard' => $request->master_data_idcard,
            ]);
            if (!$request->has('updated_by')) {
                $updateData['updated_by'] = 'admin';
            } else {
                $updateData['updated_by'] = $request->input('updated_by');
            }

            $updateData['updated_at'] = now();

            $member->update($updateData);

            DB::commit();
            return response()->json([
                'message' => 'Member berhasil diperbarui',
                'data' => $member
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal memperbarui data',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function delete($id)
    {
        $member = Member::find($id);
        if (!$member) {
            return response()->json(['message' => 'Member tidak ditemukan'], 404);
        }

        DB::beginTransaction();
        try {
            // Tentukan folder tempat file KTP/Ijasah disimpan
            $folderPath = 'public/' . $member->master_data_user;

            // Hapus folder beserta file-file di dalamnya (jika ada)
            if (Storage::exists($folderPath)) {
                Storage::deleteDirectory($folderPath);
            }

            // Hapus data member di database
            $member->delete();

            DB::commit();
            return response()->json(['message' => 'Member berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menghapus member', 'error' => $e->getMessage()], 500);
        }
    }

    public function approveMember($id)
    {
        $member = Member::find($id);
        if (!$member) {
            return response()->json(['message' => 'Member tidak ditemukan'], 404);
        }

        $member->update(['status' => 1]);
        $member->save();

        // Kirim Email Notifikasi
        Mail::to($member->master_data_email)->send(new MemberApprovedMail($member));

        return response()->json(['message' => 'Member berhasil diaktifkan dan email terkirim']);
    }

    public function rejectMember(Request $request, $id)
    {
        $member = Member::find($id);
        if (!$member) {
            return response()->json(['message' => 'Member tidak ditemukan'], 404);
        }

        $reason = $request->input('reason'); // Ambil alasan dari inputan admin

        if (!$reason) {
            return response()->json(['message' => 'Alasan penolakan harus diisi'], 422);
        }

        if (empty($member->master_data_email) || !filter_var($member->master_data_email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['message' => 'Email member tidak valid atau tidak tersedia'], 422);
        }

        try {
            $member->update(['status' => 3]); // Ubah status menjadi "Ditolak"

            Mail::to($member->master_data_email)->send(new MemberRejectedMail($member, $reason));

            return response()->json(['message' => 'Member berhasil ditolak dan email telah dikirim.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menolak member', 'error' => $e->getMessage()], 500);
        }
    }


}
