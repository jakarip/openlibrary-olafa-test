<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member\MemberPresence;

class MemberPresenceController extends Controller
{
    // Menampilkan semua data kehadiran member
    public function index()
    {
        return response()->json(MemberPresence::all());
    }

    // API untuk DataTables
    public function dt()
    {
        return response()->json(MemberPresence::select(['id', 'member_id', 'master_data_course', 'item_location_id', 'attended_at'])->get());
    }

    // Tambah data kehadiran member
    public function insert(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:member,id',
            'master_data_course' => 'nullable',
            'item_location_id' => 'required',
            'attended_at' => 'required|date'
        ]);

        $presence = MemberPresence::create($request->all());
        return response()->json(['message' => 'Kehadiran berhasil dicatat', 'data' => $presence]);
    }

    // Edit (Tampilkan detail kehadiran)
    public function edit($id)
    {
        $presence = MemberPresence::find($id);
        if (!$presence) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
        return response()->json($presence);
    }

    // Update data kehadiran
    public function update(Request $request, $id)
    {
        $presence = MemberPresence::find($id);
        if (!$presence) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $request->validate([
            'item_location_id' => 'required',
            'attended_at' => 'required|date'
        ]);

        $presence->update($request->all());
        return response()->json(['message' => 'Data berhasil diperbarui']);
    }

    // Hapus data kehadiran
    public function delete($id)
    {
        MemberPresence::destroy($id);
        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}
