<?php

namespace App\Http\Controllers\Bebaspustaka;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class KelengkapanController extends Controller
{
    public function index()
    {
        // dd(session('activeRole'));
        // dd(auth()->user());
        $arId = session('activeRole')->ar_id ?? null;


        return view('bebaspustaka.kelengkapan', [
            'arId' => $arId
        ]);
    }



    /**
     * 1) Auto-complete (Typeahead) untuk pencarian member
     */
    public function autoData(Request $request)
    {
        $query = strtolower($request->input('q'));
        if (!$query) {
            return response()->json([]);
        }

        $results = DB::select("
        SELECT m.id, m.master_data_user, m.master_data_number, m.master_data_fullname
        FROM member m
        WHERE m.master_data_user LIKE ?
           OR m.master_data_number LIKE ?
           OR m.master_data_fullname LIKE ?
        LIMIT 10
    ", ["%$query%", "%$query%", "%$query%"]);

        $formatted = [];
        foreach ($results as $item) {
            $formatted[] = [
                'id' => $item->id,
                'username' => $item->master_data_user,
                'fullname' => $item->master_data_fullname,
                'nim' => $item->master_data_number,
                'name' => "{$item->master_data_user} - {$item->master_data_number} - {$item->master_data_fullname}",
            ];
        }

        return response()->json($formatted);
    }


    /**
     * 2) Mendapatkan status kelengkapan SBKP
     *    - Termasuk logika amnesty, revision, dan undur_diri (sesuai contoh CI)
     */
    public function getStatus(Request $request)
    {
        $username = $request->input('username');
        if (!$username) {
            return response()->json(['error' => 'Username is required'], 400);
        }

        $result = DB::select("
            SELECT buku, dokumen, peminjaman, approval,
                   IF(ISNULL(lunas) OR lunas=0, TRUE, FALSE) AS lunas
            FROM (
                SELECT
                    (denda - IF(ISNULL(bayar), 0, bayar)) AS lunas,
                    IF(ISNULL(buku), FALSE, TRUE)       AS buku,
                    IF(ISNULL(dokumen), FALSE, TRUE)    AS dokumen,
                    IF(ISNULL(approval), FALSE, TRUE)   AS approval,
                    IF(ISNULL(peminjaman), TRUE, FALSE) AS peminjaman
                FROM (
                    SELECT
                        (SELECT id
                        FROM free_letter
                        WHERE member_number = mm.master_data_number
                        LIMIT 1
                        ) AS buku,


                        (SELECT id FROM workflow_document
                         WHERE member_id = mm.id
                           AND latest_state_id IN (3,4,52,53,64,5,91)
                         LIMIT 1
                        ) AS dokumen,

                        (SELECT id FROM workflow_document
                         WHERE member_id = mm.id
                           AND latest_state_id IN (64,5,91)
                         LIMIT 1
                        ) AS approval,

                        (SELECT id FROM rent
                         WHERE member_id = mm.id
                           AND return_date IS NULL
                         LIMIT 1
                        ) AS peminjaman,

                        (SELECT SUM(amount) FROM rent_penalty
                         WHERE member_id = mm.id
                        ) AS denda,

                        (SELECT SUM(amount) FROM rent_penalty_payment
                         WHERE member_id = mm.id
                        ) AS bayar

                    FROM member mm
                    WHERE mm.master_data_user = ?
                      AND mm.member_type_id != '19'
                ) a
            ) b
        ", [$username]);

        if (empty($result)) {
            $result = [
                [
                    'buku' => 0,
                    'dokumen' => 0,
                    'approval' => 0,
                    'peminjaman' => 0,
                    'lunas' => true
                ]
            ];
        }

        $data = json_decode(json_encode($result), true);

        $amnestyRows = DB::select("
            SELECT m.master_data_user
            FROM amnesty_denda ad
            JOIN member m ON m.id = ad.username_id
        ");
        $amnesty = array_map(fn($row) => strtolower($row->master_data_user), $amnestyRows);

        if (in_array(strtolower($username), $amnesty)) {
            $data[0]['lunas'] = true;
        }

        $revisionRows = DB::select("
            SELECT m.master_data_user
            FROM bebaspustaka_revision br
            JOIN member m ON m.id = br.username_id
        ");
        $revision = array_map(fn($row) => strtolower($row->master_data_user), $revisionRows);

        if (!empty($data[0]['dokumen']) && $data[0]['dokumen'] == 1) {
            if (in_array(strtolower($username), $revision)) {
                $data[0]['revisi'] = 0;
            } else {
                $data[0]['revisi'] = 1;
            }
        } else {
            $data[0]['revisi'] = 0;
        }

        $undur_diri = ['erikosaputra'];
        if (in_array(strtolower($username), $undur_diri)) {
            $data[0]['buku'] = 1;
            $data[0]['dokumen'] = 1;
            $data[0]['approval'] = 1;
            $data[0]['peminjaman'] = 1;
            $data[0]['lunas'] = true;
            $data[0]['revisi'] = 1;
        }

        return response()->json($data);
    }


    private function getStatusInternally($username)
    {
        $result = DB::select("
        SELECT buku, dokumen, peminjaman, approval,
               IF(ISNULL(lunas) OR lunas=0, TRUE, FALSE) AS lunas
        FROM (
            SELECT
                (denda - IF(ISNULL(bayar), 0, bayar)) AS lunas,
                IF(ISNULL(buku), FALSE, TRUE)       AS buku,
                IF(ISNULL(dokumen), FALSE, TRUE)    AS dokumen,
                IF(ISNULL(approval), FALSE, TRUE)   AS approval,
                IF(ISNULL(peminjaman), TRUE, FALSE) AS peminjaman
            FROM (
                SELECT
                    (SELECT id
                     FROM free_letter
                     WHERE member_number = mm.master_data_number
                     LIMIT 1
                    ) AS buku,

                    (SELECT id FROM workflow_document
                     WHERE member_id = mm.id
                       AND latest_state_id IN (3,4,52,53,64,5,91)
                     LIMIT 1
                    ) AS dokumen,

                    (SELECT id FROM workflow_document
                     WHERE member_id = mm.id
                       AND latest_state_id IN (64,5,91)
                     LIMIT 1
                    ) AS approval,

                    (SELECT id FROM rent
                     WHERE member_id = mm.id
                       AND return_date IS NULL
                     LIMIT 1
                    ) AS peminjaman,

                    (SELECT SUM(amount) FROM rent_penalty
                     WHERE member_id = mm.id
                    ) AS denda,

                    (SELECT SUM(amount) FROM rent_penalty_payment
                     WHERE member_id = mm.id
                    ) AS bayar

                FROM member mm
                WHERE mm.master_data_user = ?
                  AND mm.member_type_id != '19'
            ) a
        ) b
    ", [$username]);

        if (empty($result)) {
            $result = [
                [
                    'buku' => 0,
                    'dokumen' => 0,
                    'approval' => 0,
                    'peminjaman' => 0,
                    'lunas' => true
                ]
            ];
        }

        $data = json_decode(json_encode($result), true);

        $amnestyRows = DB::select("
        SELECT m.master_data_user
        FROM amnesty_denda ad
        JOIN member m ON m.id = ad.username_id
    ");
        $amnesty = array_map(fn($row) => strtolower($row->master_data_user), $amnestyRows);

        if (in_array(strtolower($username), $amnesty)) {
            $data[0]['lunas'] = true;
        }

        $revisionRows = DB::select("
        SELECT m.master_data_user
        FROM bebaspustaka_revision br
        JOIN member m ON m.id = br.username_id
    ");
        $revision = array_map(fn($row) => strtolower($row->master_data_user), $revisionRows);

        if (!empty($data[0]['dokumen']) && $data[0]['dokumen'] == 1) {
            if (in_array(strtolower($username), $revision)) {
                $data[0]['revisi'] = 0;
            } else {
                $data[0]['revisi'] = 1;
            }
        } else {
            $data[0]['revisi'] = 0;
        }

        $undur_diri = ['erikosaputra'];
        if (in_array(strtolower($username), $undur_diri)) {
            $data[0]['buku'] = 1;
            $data[0]['dokumen'] = 1;
            $data[0]['approval'] = 1;
            $data[0]['peminjaman'] = 1;
            $data[0]['lunas'] = true;
            $data[0]['revisi'] = 1;
        }

        return response()->json($data);
    }

    /**
     * 3) Cetak PDF (mirip create_pdf di CI)
     *    - Pastikan sudah jalankan `composer require mpdf/mpdf`
     */
    public function createPDF($id)
    {
        $rows = DB::select("
            SELECT fl.*, m.master_data_user, m.master_data_fullname,
                   tmp.NAMA_PRODI, tmf.NAMA_FAKULTAS
            FROM free_letter fl
            LEFT JOIN member m ON m.id = fl.registration_number
            LEFT JOIN t_mst_prodi tmp ON tmp.c_kode_prodi = m.master_data_course
            LEFT JOIN t_mst_fakultas tmf ON tmf.c_kode_fakultas = tmp.c_kode_fakultas
            WHERE fl.id = ?
        ", [$id]);

        if (empty($rows)) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
        $data = $rows[0];

        $mpdf = new Mpdf([
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 5,
            'margin_bottom' => 1,
        ]);
        $mpdf->ignore_invalid_utf8 = true;

        $html = view('bebaspustaka.sbkpprint_print', compact('data'))->render();

        $mpdf->WriteHTML($html);

        return $mpdf->Output('SBKP.pdf', 'D');
    }
}
