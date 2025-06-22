<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reports\PracticalWorkModel;
use Illuminate\Validation\Rule;

class PracticalWork extends Controller
{
    protected $practicalWorkModel;

    public function __construct(PracticalWorkModel $practicalWorkModel)
    {
        $this->practicalWorkModel = $practicalWorkModel;
    }

    public function index()
    {
        $prodi = DB::table('t_mst_prodi')
            ->select('C_KODE_PRODI', 'C_KODE_FAKULTAS', 'NAMA_PRODI')
            ->get();

        return view('reports.practicalwork.data', compact('prodi'));
    }

//     public function dt(Request $request)
// {
//     $data = DB::table('ereport as e')
//         ->join('member as m', 'e.ereport_id_member', '=', 'm.id')
//         ->join('t_mst_prodi as p', 'm.master_data_course', '=', 'p.C_KODE_PRODI')
//         ->leftJoin('member as d', 'e.ereport_id_lecturer', '=', 'd.id')
//         ->select([
//             'e.ereport_id',
//             'e.ereport_type',
//             'm.master_data_number',
//             'm.master_data_fullname',
//             'p.NAMA_PRODI as nama_prodi',
//             'e.ereport_title',
//             'd.master_data_fullname as dosen_pembimbing',
//             'e.ereport_mentor_company_name',
//             'e.ereport_industry_type',
//             'e.ereport_mentor_company_address',
//             DB::raw("CONCAT(e.ereport_mentor_front_name, ' ', e.ereport_mentor_last_name) AS mentor_name"),
//             'e.ereport_mentor_academic',
//             DB::raw("CONCAT(e.ereport_mentor_phone, ' / ', e.ereport_mentor_email) AS mentor_contact"),
//             'e.ereport_status'
//             ])
//             ->orderBy('e.ereport_id', 'desc'); // Mengurutkan berdasarkan ID (atau 'created_at' jika ada)
    
//         // Filtering berdasarkan input pengguna
//         if ($request->has('jenis_laporan') && !empty($request->jenis_laporan)) {
//             $data->where('e.ereport_type', $request->jenis_laporan);
//         }
    
//         if ($request->has('status') && !empty($request->status)) {
//             $data->where('e.ereport_status', $request->status);
//         }
    
//         if ($request->has('prodi') && !empty($request->prodi)) {
//             $data->where('p.NAMA_PRODI', $request->prodi);
//         }
    
//         if ($request->has('onlyforme') && $request->onlyforme == 1) {
//             $data->where('e.ereport_id_lecturer', auth()->user()->id);
//         }

//         $user = auth()->user();
//         $memberClassId = $user->member_class_id ?? null;
//         $memberId = $user->id ?? null;

//         $data = (new PracticalWorkModel())->getEreportDetails($memberClassId, $memberId);

//         return datatables($data)
//             ->addColumn('action', function ($db) {
//                 return '<div class="btn-group">
//                     <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
//                         <i class="ti ti-dots-vertical"></i>
//                     </button>
//                     <ul class="dropdown-menu dropdown-menu-end">
//                         <li><a class="dropdown-item d-flex align-items-center edit-btn" data-id="' . $db->ereport_id . '" href="' . url('report/practical-work/edit/' . $db->ereport_id) . '">
//                             <i class="ti ti-edit ti-sm me-2"></i> Detail Data</a></li>
//                     </ul>
//                 </div>';
//             })
//             ->rawColumns(['action'])
//             ->toJson();
//     }


public function dt(Request $request)
{
    $user = auth()->user();
    $memberClassId = $user->member_class_id ?? null;
    $memberId = $user->id ?? null;

    $query = (new PracticalWorkModel())->getEreportDetailsBuilder($memberClassId, $memberId);

    // Filtering DataTables (langsung di query builder)
    if ($request->filled('jenis_laporan')) {
        $query->where('e.ereport_type', $request->jenis_laporan);
    }
    if ($request->filled('status')) {
        $query->where('e.ereport_status', $request->status);
    }
    if ($request->filled('prodi')) {
        $query->where('p.NAMA_PRODI', $request->prodi);
    }
    if ($request->has('onlyforme') && $request->onlyforme == 1) {
        $query->where('d.master_data_fullname', $user->master_data_fullname);
    }

    return datatables($query)
        ->addColumn('action', function ($db) {
            return '<div class="btn-group">
                <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item d-flex align-items-center edit-btn" data-id="' . $db->ereport_id . '" href="' . url('report/practical-work/edit/' . $db->ereport_id) . '">
                        <i class="ti ti-edit ti-sm me-2"></i> Detail Data</a></li>
                </ul>
            </div>';
        })
        ->rawColumns(['action'])
        ->toJson();
}

        public function create()
        {
            $user = auth()->user();
            // Cek jika mahasiswa sudah pernah membuat laporan Magang/KP
        if ($user->member_class_id == 2) {
            $sudahMagang = PracticalWorkModel::where('ereport_id_member', $user->id)
                 ->where('ereport_type', 'Magang')
                 ->exists();
            $sudahKP = PracticalWorkModel::where('ereport_id_member', $user->id)
                 ->where('ereport_type', 'KP')
                 ->exists();

        if ($sudahMagang && $sudahKP) {
            return redirect()->route('practicalwork.data')->with('error', 'Anda sudah pernah membuat laporan Magang dan KP. Tidak dapat menambah lagi.');
        } elseif ($sudahMagang) {
            return redirect()->route('practicalwork.data')->with('error', 'Anda sudah pernah membuat laporan Magang. Tidak dapat menambah Magang lagi.');
        } elseif ($sudahKP) {
            return redirect()->route('practicalwork.data')->with('error', 'Anda sudah pernah membuat laporan KP. Tidak dapat menambah KP lagi.');
        }
    }
            $prodi = DB::table('t_mst_prodi')
                ->select('C_KODE_PRODI', 'C_KODE_FAKULTAS', 'NAMA_PRODI')
                ->get();
        
                $dosen_pembimbing = DB::table('member')
                ->where('member_type_id', 2) // kode dosen, sesuaikan jika perlu
                ->where('status', 1)
                ->orderBy('master_data_fullname')
                ->select('id', 'master_data_fullname AS dosen_pembimbing')
                ->get();
        
            return view('reports.practicalwork.add', compact('prodi', 'dosen_pembimbing'));
        }

       public function edit($id)
    {
        if (!auth()->can('report-practical-work.update')) {
            return redirect()->route('practicalwork.data')->with('error', 'You do not have permission to edit the report.');
        }

        $user = auth()->user();

        // Ambil data laporan berdasarkan ID
        $report = DB::table('ereport as e')
            ->leftJoin('member as d', 'e.ereport_id_lecturer', '=', 'd.id')
            ->where('e.ereport_id', $id)
            ->select('e.*', 'd.master_data_fullname as dosen_pembimbing')
            ->first();

        if (!$report) {
            return redirect()->route('practicalwork.data')->with('error', 'Data tidak ditemukan.');
        }

        // CEK: Jika mahasiswa, hanya boleh akses miliknya sendiri
        if ($user->member_class_id == 2 && $report->ereport_id_member != $user->id) {
            return redirect()->route('practicalwork.data')->with('error', 'Anda tidak dapat mengakses data ini.');
        }
        
        // Dosen hanya boleh akses mahasiswa bimbingannya
    if ($user->member_class_id == 9 && $report->ereport_id_lecturer != $user->id) {
        return redirect()->route('practicalwork.data')->with('error', 'Anda hanya dapat mengedit data mahasiswa bimbingan Anda.');
    }

        // Tentukan status yang bisa dipilih berdasarkan peran pengguna
        if ($user->member_class_id == 2) {
            $status = ['ON DRAFT', 'READY FOR REVIEW'];
            if (!in_array($report->ereport_status, $status)) {
                $status[] = $report->ereport_status;
            }
        } elseif ($user->member_class_id == 1 || $user->member_class_id == 9) {
            $status = ['READY FOR REVIEW', 'APPROVED', 'NEED FOR REVISION'];
        } else {
            $status = ['ON DRAFT', 'READY FOR REVIEW'];
        }

        // Ambil data dosen pembimbing
        $dosen_pembimbing = DB::table('member')
            ->where('member_type_id', 2)
            ->where('status', 1)
            ->orderBy('master_data_fullname')
            ->select('id', 'master_data_fullname AS dosen_pembimbing')
            ->get();

        // Ambil status laporan history (semua status)
$ereport_status = DB::table('ereport_status as e')
    ->join('member as m', 'e.es_id_member', '=', 'm.id')
    ->select([
        'e.es_status as ereport_status',
        'm.master_data_number',
        'm.master_data_fullname',
        DB::raw("DATE_FORMAT(e.es_date, '%Y-%m-%d %H:%i') as created_at"), // Format tanggal tanpa detik
    ])
    ->where('e.es_id_report', $id) // Pastikan ini sesuai dengan ID laporan
    ->orderByRaw("CASE WHEN e.es_status = 'ON DRAFT' THEN 0 ELSE 1 END, e.es_date DESC") // Urutkan ON DRAFT di atas
    ->get();

        return view('reports.practicalwork.edit', compact(
            'report', 'status', 'dosen_pembimbing', 'ereport_status'
        ))->with('member_class_id', $user->member_class_id);
    }



public function getById($id)
{
    // Cari data berdasarkan ID
    $report = PracticalWorkModel::find($id);

    // dd($report);

    if (!$report) {
        return response()->json(['error' => 'Data tidak ditemukan.'], 404);  // Jika data tidak ditemukan
    }

    return response()->json(['data' => $report], 200);  // Jika data ditemukan, kirim data dalam format JSON
}

public function update(Request $request, $id)
    {
            try {
        $user = auth()->user();
        $member_class_id = $user->member_class_id;

        // Validasi berbeda untuk dosen/admin dan mahasiswa
        if (in_array($member_class_id, [1,9])) {
            // Dosen/admin hanya validasi status & revisi
            $request->validate([
                'ereport_status' => ['required', Rule::in(['ON DRAFT', 'NEED FOR REVISION', 'READY FOR REVIEW', 'APPROVED', 'SUCCESS'])],
                'ereport_revision' => 'nullable|string',
            ]);
        } else {
            // Mahasiswa: semua field wajib
            $request->validate([
                'ereport_title' => 'required|string|max:255',
                'ereport_status' => ['required', Rule::in(['ON DRAFT', 'NEED FOR REVISION', 'READY FOR REVIEW', 'APPROVED', 'SUCCESS'])],
                'ereport_mentor_company_name' => 'required|string|max:255',
                'ereport_mentor_company_address' => 'required|string|max:255',
                'ereport_mentor_front_name' => 'required|string|max:255',
                'ereport_mentor_last_name' => 'required|string|max:255',
                'ereport_mentor_academic' => 'required|string|max:255',
                'ereport_mentor_phone' => 'required|string|max:15',
                'ereport_mentor_email' => 'required|email|max:255',
            ]);
        }

        $report = PracticalWorkModel::find($id);
        if (!$report) {
            return redirect()->route('practicalwork.data')->with('error', 'Laporan tidak ditemukan.');
        }

        // CEK: Jika mahasiswa, hanya boleh update miliknya sendiri
        if ($member_class_id == 2 && $report->ereport_id_member != $user->id) {
            return redirect()->route('practicalwork.data')->with('error', 'Anda tidak dapat mengedit data ini.');
        }

        // Dosen hanya boleh update mahasiswa bimbingannya
if ($member_class_id == 9 && $report->ereport_id_lecturer != $user->id) {
    return redirect()->route('practicalwork.data')->with('error', 'Anda hanya dapat mengedit data mahasiswa bimbingan Anda.');
}

        // Mahasiswa tidak boleh update jika status READY FOR REVIEW
        if ($member_class_id == 2 && $report->ereport_status == 'READY FOR REVIEW') {
            return redirect()->back()->with('error', 'Anda tidak dapat mengedit data ini.');
        }

        $old_status = $report->ereport_status;

        // Update data laporan
        if (in_array($member_class_id, [1,9])) {
            // Dosen/admin hanya update status & revisi
            $report->ereport_status = $request->ereport_status;
            $report->ereport_revision = $request->ereport_revision;
        } else {
            // Mahasiswa update semua field
            $report->ereport_title = $request->ereport_title;
            $report->ereport_status = $request->ereport_status;
            $report->ereport_mentor_company_name = $request->ereport_mentor_company_name;
            $report->ereport_mentor_company_address = $request->ereport_mentor_company_address;
            $report->ereport_mentor_front_name = $request->ereport_mentor_front_name;
            $report->ereport_mentor_last_name = $request->ereport_mentor_last_name;
            $report->ereport_mentor_academic = $request->ereport_mentor_academic;
            $report->ereport_mentor_phone = $request->ereport_mentor_phone;
            $report->ereport_mentor_email = $request->ereport_mentor_email;
            $report->ereport_revision = $request->ereport_revision;
            $report->ereport_id_lecturer = $request->ereport_id_lecturer;
            $report->ereport_industry_type = $request->ereport_industry_type;
            $report->ereport_mentor_position = $request->ereport_mentor_position;
        }

        // File upload logic (boleh tetap dijalankan untuk mahasiswa)
        if ($member_class_id == 2) {
    $username = preg_replace('/\s+/', '', strtolower($user->master_data_user)); // username tanpa spasi
    $timestamp = time();

    $fileLabels = [
    'ereport_file' => 'ereport_file',
    'ereport_file_similarity' => 'file_similarity',
    'ereport_file_approval' => 'file_approval',
    'ereport_file_finish' => 'file_finish',
    'ereport_file_implementation' => 'file_implementation'
    ];

    foreach ($fileLabels as $fileKey => $label) {
        if ($request->hasFile($fileKey)) {
            $file = $request->file($fileKey);
            $ext = $file->getClientOriginalExtension();
            $filename = "{$username}_{$timestamp}_{$label}.{$ext}";
            $folder = "ereport_file/{$username}";
            $filePath = $file->storeAs($folder, $filename, 'public');
            $report->$fileKey = $filePath;
        }
    }
}

        $report->save();

       // Simpan perubahan status ke dalam tabel history jika berubah
if ($old_status !== $report->ereport_status) {
    DB::table('ereport_status')->insert([
        'es_id_report' => $report->ereport_id,
        'es_status' => $request->ereport_status,
        'es_date' => now(),
        'es_id_member' => $user->id,
    ]);
}

        return redirect()->route('practicalwork.data')->with('success', 'Data berhasil diperbarui.');
    } catch (\Exception $e) {
        return redirect()->route('practicalwork.data')->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
    }
}


public function updateReport($id, array $data)
{
    $updated = $this->where($this->primaryKey, $id)->update($data);
    if ($updated) {
        return true; // Mengembalikan true jika pembaruan berhasil
    }
    return false; // Mengembalikan false jika tidak ada yang diperbarui
}
       

public function save(Request $request)
{
    try {
        // Validate the request
        $request->validate([
            'jenis_laporan' => 'required|string',
            'judul_laporan' => 'required|string|max:255',
            'ereport_file' => 'nullable|file|mimes:pdf|max:2048', // Add this line
            'ereport_file_similarity' => 'nullable|file|mimes:pdf|max:2048', // Add this line
            'ereport_file_approval' => 'nullable|file|mimes:pdf|max:2048', // Add this line
            'ereport_file_finish' => 'nullable|file|mimes:pdf|max:2048', // Add this line
            'ereport_file_implementation' => 'nullable|file|mimes:pdf|max:2048', // Add this line
        ], [
            'ereport_file.mimes' => 'Error format file harus PDF.',
            'ereport_file_similarity.mimes' => 'Error format file harus PDF.',
            'ereport_file_approval.mimes' => 'Error format file harus PDF.',
            'ereport_file_finish.mimes' => 'Error format file harus PDF.',
            'ereport_file_implementation.mimes' => 'Error format file harus PDF.',
        ]);
        $ereport = new PracticalWorkModel();

        // Isi data dari form input
        $ereport->ereport_type = $request->jenis_laporan;
        $ereport->ereport_status = $request->status;
        $ereport->ereport_title = $request->judul_laporan;
        $ereport->ereport_mentor_company_name = $request->ereport_mentor_company_name;
        $ereport->ereport_mentor_company_address = $request->ereport_mentor_company_address;
        $ereport->ereport_industry_type = $request->ereport_industry_type;
        $ereport->ereport_mentor_front_name = $request->ereport_mentor_front_name;
        $ereport->ereport_mentor_last_name = $request->ereport_mentor_last_name;
        $ereport->ereport_mentor_academic = $request->ereport_mentor_academic;
        $ereport->ereport_mentor_phone = $request->ereport_mentor_phone;
        $ereport->ereport_mentor_email = $request->ereport_mentor_email;

        $user = auth()->user();
        $username = preg_replace('/\s+/', '', strtolower($user->master_data_user)); // username tanpa spasi
        $timestamp = time();

        $fileLabels = [
            'ereport_file' => 'ereport_file',
            'ereport_file_similarity' => 'file_similarity',
            'ereport_file_approval' => 'file_approval',
            'ereport_file_finish' => 'file_finish',
            'ereport_file_implementation' => 'file_implementation'
        ];

        foreach ($fileLabels as $fileKey => $label) {
            if ($request->hasFile($fileKey)) {
                $file = $request->file($fileKey);
                $ext = $file->getClientOriginalExtension();
                $filename = "{$username}_{$timestamp}_{$label}.{$ext}";
                $folder = "ereport_file/{$username}";
                $filePath = $file->storeAs($folder, $filename, 'public');
                $ereport->$fileKey = $filePath;
            }
        }
        

        // Menambahkan ID member (pengguna yang sedang login) ke kolom ereport_id_member
        $ereport->ereport_id_member = auth()->user()->id; // Menggunakan ID mahasiswa yang sedang login

        // ID dosen pembimbing yang dipilih
        $ereport->ereport_id_lecturer = $request->ereport_id_lecturer;

        // Simpan data ke database
        $ereport->save();

        return redirect()->route('practicalwork.data')->with('success', 'Data berhasil disimpan');
    } catch (\Exception $e) {
        return redirect()->route('practicalwork.data')->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
    }
}

public function store(Request $request)
{
    $user = auth()->user();

    // Cek jika mahasiswa sudah pernah membuat laporan Magang/KP
    if ($user->member_class_id == 2) {
        $jenis = $request->jenis_laporan;
        $sudahAda = PracticalWorkModel::where('ereport_id_member', $user->id)
            ->where('ereport_type', $jenis)
            ->exists();

        if ($sudahAda) {
            return redirect()->route('practicalwork.data')->with('error', 'Anda sudah pernah membuat laporan ' . $jenis . '. Tidak dapat menambah lagi.');
        }
    }
    try {
        // Validasi input
        $request->validate([
            'jenis_laporan' => 'required|string',
            'judul_laporan' => 'required|string|max:255',
            'ereport_file' => 'nullable|file|mimes:pdf|max:2048', // Add this line
            'ereport_file_similarity' => 'nullable|file|mimes:pdf|max:2048', // Add this line
            'ereport_file_approval' => 'nullable|file|mimes:pdf|max:2048', // Add this line
            'ereport_file_finish' => 'nullable|file|mimes:pdf|max:2048', // Add this line
            'ereport_file_implementation' => 'nullable|file|mimes:pdf|max:2048', // Add this line
        ], [
            'ereport_file.mimes' => 'Error format file harus PDF.',
            'ereport_file_similarity.mimes' => 'Error format file harus PDF.',
            'ereport_file_approval.mimes' => 'Error format file harus PDF.',
            'ereport_file_finish.mimes' => 'Error format file harus PDF.',
            'ereport_file_implementation.mimes' => 'Error format file harus PDF.',
        ]);

        // Simpan data tanpa status
        $ereport = new PracticalWorkModel();
        $ereport->ereport_type = $request->jenis_laporan;
        $ereport->ereport_title = $request->judul_laporan;
        $ereport->ereport_mentor_company_name = $request->ereport_mentor_company_name;
        $ereport->ereport_mentor_company_address = $request->ereport_mentor_company_address;
        $ereport->ereport_industry_type = $request->ereport_industry_type;
        $ereport->ereport_mentor_front_name = $request->ereport_mentor_front_name;
        $ereport->ereport_mentor_last_name = $request->ereport_mentor_last_name;
        $ereport->ereport_mentor_position = $request->ereport_mentor_position;
        $ereport->ereport_mentor_academic = $request->ereport_mentor_academic;
        $ereport->ereport_mentor_phone = $request->ereport_mentor_phone;
        $ereport->ereport_mentor_email = $request->ereport_mentor_email;
        $ereport->ereport_status = 'ON DRAFT';
        $ereport->ereport_id_member = auth()->user()->id;
        $ereport->ereport_id_lecturer = $request->ereport_id_lecturer;

        // File upload logic
            $user = auth()->user();
$username = preg_replace('/\s+/', '', strtolower($user->master_data_user)); // gunakan username
$timestamp = time();

$fileLabels = [
    'ereport_file' => 'ereport_file',
    'ereport_file_similarity' => 'file_similarity',
    'ereport_file_approval' => 'file_approval',
    'ereport_file_finish' => 'file_finish',
    'ereport_file_implementation' => 'file_implementation'
];

foreach ($fileLabels as $fileKey => $label) {
    if ($request->hasFile($fileKey)) {
        $file = $request->file($fileKey);
        $ext = $file->getClientOriginalExtension();
        $filename = "{$username}_{$timestamp}_{$label}.{$ext}";
        $folder = "ereport_file/{$username}";
        $filePath = $file->storeAs($folder, $filename, 'public');
        $ereport->$fileKey = $filePath; // atau $report->$fileKey jika di update
    }
}

        // dd($ereport);

        $ereport->save();

        // Simpan status ke tabel history
DB::table('ereport_status')->insert([
    'es_id_report' => $ereport->ereport_id, // ID laporan yang baru dibuat
    'es_status' => 'ON DRAFT', // Status awal
    'es_date' => now(), // Tanggal saat status disimpan
    'es_id_member' => auth()->user()->id,
]);

        // Redirect ke halaman edit untuk memilih status
        return redirect()->route('practicalwork.edit', ['id' => $ereport->ereport_id])
        ->with('success', 'Data berhasil disimpan.');
    } catch (\Exception $e) {
        return redirect()->route('practicalwork.add')->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
    }
}

public function searchMembers(Request $request)
{
    $search = $request->input('search', '');
    $data = DB::connection('mysql')
        ->table('member')
        ->select('id', 'master_data_user', 'master_data_fullname', 'master_data_number')
        ->where(function($query) use ($search) {
            $query->where('master_data_user', 'LIKE', "{$search}%")
                ->orWhere('master_data_fullname', 'LIKE', "{$search}%")
                ->orWhere('master_data_number', 'LIKE', "{$search}%");
        })
        ->orderBy('master_data_fullname')
        ->limit(20)
        ->get();
    return response()->json($data);
}
// public function delete(Request $request)
// {
//     $ereport = $this->practicalWorkModel->find($request->ereport_id);
//     $user = auth()->user();

//     if (!$ereport) {
//         return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
//     }

//     if ($user->role !== 'admin' && $ereport->created_by !== $user->id) {
//         return response()->json(['status' => 'error', 'message' => 'Anda tidak memiliki izin untuk menghapus data ini.'], 403);
//     }

//     $ereport->delete();
//     return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
// }
}