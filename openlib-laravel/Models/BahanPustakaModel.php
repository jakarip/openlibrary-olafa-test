<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BahanPustakaModel extends Model
{
    use HasFactory;

    // protected $table = 'bahan_pustaka'; // Adjust the table name if necessary

    /**
     * Get all jurusan.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllJurusan()
    {
        return DB::table('t_mst_prodi')
            ->leftJoin('t_mst_fakultas', 't_mst_prodi.c_kode_fakultas', '=', 't_mst_fakultas.c_kode_fakultas')
            ->select('c_kode_prodi', 'nama_prodi', 'nama_fakultas')
            ->orderBy('nama_fakultas')
            ->orderBy('nama_prodi')
            ->get();
    }

    /**
     * Get all fakultas.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllFakultas()
    {
        return DB::table('t_mst_fakultas')
            ->orderBy('nama_fakultas')
            ->get();
    }

    /**
     * Get curriculum.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCurriculum()
    {
        return DB::table('master_subject')
            ->select('curriculum_code')
            ->groupBy('curriculum_code')
            ->orderBy('curriculum_code', 'desc')
            ->get();
    }

    /**
     * Get Bahan Pustaka.
     *
     * @param string $year
     * @param string $growYear
     * @param string $faculty
     * @return \Illuminate\Support\Collection
     */
    public static function getBahanPustaka($year, $growYear, $faculty)
    {
        return DB::select("
            SELECT 
                tmp.c_kode_prodi, 
                tmf.nama_fakultas, 
                tmp.nama_prodi,

                -- Judul (Knowledge Items)
                (
                    SELECT COUNT(kt.id) 
                    FROM master_subject msu
                    LEFT JOIN knowledge_item_subject kis ON kis.master_subject_id = msu.id
                    LEFT JOIN knowledge_item kt ON kis.knowledge_item_id = kt.id
                    LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                    LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                    WHERE ks.active = '1' 
                    AND kp.active = '1'
                    AND kt.knowledge_type_id  IN (21) 
                    AND msu.curriculum_code = ?
                    AND msu.course_code = tmp.c_kode_prodi
                    AND kt.entrance_date BETWEEN '1900-01-01' AND ?
                ) AS judul,

                -- Eks (Knowledge Stock)
                (
                    SELECT COUNT(kk.id) 
                    FROM master_subject msu
                    LEFT JOIN knowledge_item_subject kis ON kis.master_subject_id = msu.id
                    LEFT JOIN knowledge_item kt ON kis.knowledge_item_id = kt.id
                    LEFT JOIN knowledge_stock kk ON kk.knowledge_item_id = kt.id
                    LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                    LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                    WHERE ks.active = '1' 
                    AND kp.active = '1'
                    AND kk.status NOT IN (4, 5)
                    AND kk.knowledge_type_id IN (21) 
                    AND msu.curriculum_code = ?
                    AND msu.course_code = tmp.c_kode_prodi
                    AND kk.entrance_date BETWEEN '1900-01-01' AND ?
                ) AS eks,

                -- Judul Fisik (Physical Knowledge Items)
                (
                    SELECT COUNT(kt.id) 
                    FROM master_subject msu
                    LEFT JOIN knowledge_item_subject kis ON kis.master_subject_id = msu.id
                    LEFT JOIN knowledge_item kt ON kis.knowledge_item_id = kt.id
                    LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                    LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                    WHERE ks.active = '1' 
                    AND kp.active = '1'
                    AND kt.knowledge_type_id IN (1, 2, 3, 33, 40, 41, 59, 65)
                    AND msu.curriculum_code = ?
                    AND msu.course_code = tmp.c_kode_prodi
                    AND kt.entrance_date BETWEEN '1900-01-01' AND ?
                ) AS judul_fisik,

                -- Eks Fisik (Physical Knowledge Stock)
                (
                    SELECT COUNT(kk.id) 
                    FROM master_subject msu
                    LEFT JOIN knowledge_item_subject kis ON kis.master_subject_id = msu.id
                    LEFT JOIN knowledge_item kt ON kis.knowledge_item_id = kt.id
                    LEFT JOIN knowledge_stock kk ON kk.knowledge_item_id = kt.id
                    LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                    LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                    WHERE ks.active = '1' 
                    AND kp.active = '1'
                    AND kk.status NOT IN (4, 5)
                    AND kk.knowledge_type_id IN (1, 2, 3, 33, 40, 41, 59, 65)
                    AND msu.curriculum_code = ?
                    AND msu.course_code = tmp.c_kode_prodi
                    AND kk.entrance_date BETWEEN '1900-01-01' AND ?
                ) AS eks_fisik,

                -- Total Mata Kuliah (Courses)
                (
                    SELECT COUNT(*) 
                    FROM master_subject 
                    WHERE curriculum_code = ? 
                    AND course_code = tmp.c_kode_prodi
                ) AS mk,

                -- Mata Kuliah dengan Buku (Courses with Books)
                (
                    SELECT COUNT(*) 
                    FROM master_subject 
                    WHERE curriculum_code = ? 
                    AND course_code = tmp.c_kode_prodi 
                    AND id IN (
                        SELECT master_subject_id 
                        FROM knowledge_item_subject
                    )
                ) AS mkadabuku

            FROM 
                t_mst_fakultas tmf
            LEFT JOIN 
                t_mst_prodi tmp ON tmp.c_kode_fakultas = tmf.c_kode_fakultas
            WHERE 
                tmp.nama_prodi NOT LIKE '%Pindahan%'
                AND tmp.nama_prodi NOT LIKE '%International%'
                AND tmp.nama_prodi NOT LIKE '%Internasional%'
                AND tmf.c_kode_fakultas = ?
        ", [
            $year,
            $growYear . '-12-31 23:59:59',
            $year,
            $growYear . '-12-31 23:59:59',
            $year,
            $growYear . '-12-31 23:59:59',
            $year,
            $growYear . '-12-31 23:59:59',
            $year,
            $year,
            $faculty
        ]);
    }

    /**
     * Get study program by ID.
     *
     * @param string $id
     * @return \Illuminate\Support\Collection
     */
    public function getStudyProgram($id)
    {
        return DB::table('t_mst_fakultas')
            ->leftJoin('t_mst_prodi', 't_mst_prodi.c_kode_fakultas', '=', 't_mst_fakultas.c_kode_fakultas')
            ->select('c_kode_prodi', 'nama_fakultas', 'nama_prodi')
            ->where('c_kode_prodi', $id)
            ->get();
    }

    /**
     * Get MK by ID and year.
     *
     * @param string $id
     * @param string $year
     * @return \Illuminate\Support\Collection
     */
    public function getMK($id, $year)
    {
        return DB::table('master_subject')
            ->select('*', DB::raw('SUBSTR(code, -1) AS sks'))
            ->where('course_code', $id)
            ->where('curriculum_code', $year)
            ->get();
    }

    /**
     * Get total collection.
     *
     * @param string $year
     * @param string $growYear
     * @param string $faculty
     * @return \Illuminate\Support\Collection
     */
    public function totalCollection($year, $growYear, $faculty)
    {
        return DB::select("
            SELECT 
                SUM(judul) AS judul,
                SUM(eks) AS eks,
                SUM(judul_fisik) AS judul_fisik,
                SUM(eks_fisik) AS eks_fisik,
                SUM(mk) AS mk,
                SUM(mkadabuku) AS mkadabuku
            FROM (
                SELECT 
                    tmp.c_kode_prodi,
                    tmf.nama_fakultas,
                    tmp.nama_prodi,

                    -- Judul (Knowledge Items)
                    (
                        SELECT COUNT(kt.id) 
                        FROM master_subject msu
                        LEFT JOIN knowledge_item_subject kis ON kis.master_subject_id = msu.id
                        LEFT JOIN knowledge_item kt ON kis.knowledge_item_id = kt.id
                        LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                        LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                        WHERE ks.active = '1' 
                        AND kp.active = '1'
                        AND kt.knowledge_type_id = 21 
                        AND msu.curriculum_code = ?
                        AND msu.course_code = tmp.c_kode_prodi
                        AND kt.entrance_date BETWEEN '1900-01-01' AND ?
                    ) AS judul,

                    -- Eks (Knowledge Stock)
                    (
                        SELECT COUNT(kk.id) 
                        FROM master_subject msu
                        LEFT JOIN knowledge_item_subject kis ON kis.master_subject_id = msu.id
                        LEFT JOIN knowledge_item kt ON kis.knowledge_item_id = kt.id
                        LEFT JOIN knowledge_stock kk ON kk.knowledge_item_id = kt.id
                        LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                        LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                        WHERE ks.active = '1' 
                        AND kp.active = '1'
                        AND kk.status NOT IN (4, 5)
                        AND kk.knowledge_type_id = 21 
                        AND msu.curriculum_code = ?
                        AND msu.course_code = tmp.c_kode_prodi
                        AND kk.entrance_date BETWEEN '1900-01-01' AND ?
                    ) AS eks,

                    -- Judul Fisik (Physical Knowledge Items)
                    (
                        SELECT COUNT(kt.id) 
                        FROM master_subject msu
                        LEFT JOIN knowledge_item_subject kis ON kis.master_subject_id = msu.id
                        LEFT JOIN knowledge_item kt ON kis.knowledge_item_id = kt.id
                        LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                        LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                        WHERE ks.active = '1' 
                        AND kp.active = '1'
                        AND kt.knowledge_type_id IN (1, 2, 3, 33, 40, 41, 59, 65)
                        AND msu.curriculum_code = ?
                        AND msu.course_code = tmp.c_kode_prodi
                        AND kt.entrance_date BETWEEN '1900-01-01' AND ?
                    ) AS judul_fisik,

                    -- Eks Fisik (Physical Knowledge Stock)
                    (
                        SELECT COUNT(kk.id) 
                        FROM master_subject msu
                        LEFT JOIN knowledge_item_subject kis ON kis.master_subject_id = msu.id
                        LEFT JOIN knowledge_item kt ON kis.knowledge_item_id = kt.id
                        LEFT JOIN knowledge_stock kk ON kk.knowledge_item_id = kt.id
                        LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                        LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                        WHERE ks.active = '1' 
                        AND kp.active = '1'
                        AND kk.status NOT IN (4, 5)
                        AND kk.knowledge_type_id IN (1, 2, 3, 33, 40, 41, 59, 65)
                        AND msu.curriculum_code = ?
                        AND msu.course_code = tmp.c_kode_prodi
                        AND kk.entrance_date BETWEEN '1900-01-01' AND ?
                    ) AS eks_fisik,

                    -- Total Mata Kuliah (Courses)
                    (
                        SELECT COUNT(*) 
                        FROM master_subject 
                        WHERE curriculum_code = ? 
                        AND course_code = tmp.c_kode_prodi
                    ) AS mk,

                    -- Mata Kuliah dengan Buku (Courses with Books)
                    (
                        SELECT COUNT(*) 
                        FROM master_subject 
                        WHERE curriculum_code = ? 
                        AND course_code = tmp.c_kode_prodi 
                        AND id IN (
                            SELECT master_subject_id 
                            FROM knowledge_item_subject
                        )
                    ) AS mkadabuku

                FROM 
                    t_mst_fakultas tmf
                LEFT JOIN 
                    t_mst_prodi tmp ON tmp.c_kode_fakultas = tmf.c_kode_fakultas
                WHERE 
                    tmp.nama_prodi NOT LIKE '%Pindahan%'
                    AND tmp.nama_prodi NOT LIKE '%International%'
                    AND tmp.nama_prodi NOT LIKE '%Internasional%'
                    AND tmf.c_kode_fakultas = ?
            ) a
        ", [
            $year,
            $growYear . '-12-31 23:59:59',
            $year,
            $growYear . '-12-31 23:59:59',
            $year,
            $growYear . '-12-31 23:59:59',
            $year,
            $growYear . '-12-31 23:59:59',
            $year,
            $year,
            $faculty
        ]);
    }

    /**
     * Get total subjects.
     *
     * @param string $jurusan
     * @param string $tahun
     * @return \Illuminate\Support\Collection
     */
    public function totalSubjects($jurusan, $tahun)
    {
        return DB::select("
            SELECT COUNT(*) AS totalmk, SUM(book) AS mk, SUM(buku) AS judul, SUM(buku_fisik) AS judul_fisik
            FROM (
                SELECT *, CASE WHEN buku = '0' THEN '0' ELSE '1' END AS book, CASE WHEN buku_fisik = '0' THEN '0' ELSE '1' END AS book_fisik
                FROM (
                    SELECT *,
                        (SELECT COUNT(*) FROM knowledge_item_subject
                        LEFT JOIN knowledge_item kt ON knowledge_item_id = kt.id
                        LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                        LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                        WHERE ks.active = '1' AND kp.active = '1' AND knowledge_type_id = 21 AND master_subject_id = msu.id) AS buku,
                        (SELECT COUNT(*) FROM knowledge_item_subject
                        LEFT JOIN knowledge_item kt ON knowledge_item_id = kt.id
                        LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                        LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                        WHERE ks.active = '1' AND kp.active = '1' AND knowledge_type_id IN (1, 2, 3, 33, 40, 41, 59, 65) AND master_subject_id = msu.id) AS buku_fisik
                    FROM master_subject msu
                    WHERE course_code = ? AND msu.curriculum_code = ?
                ) a
            ) b
        ", [$jurusan, $tahun]);
    }

    /**
     * Get Bahan Pustaka by kode jurusan.
     *
     * @param string $jurusan
     * @param string $tahun
     * @return \Illuminate\Support\Collection
     */
    public function getBahanPustakaByKodeJur($jurusan, $tahun)
    {
        return DB::select("
            SELECT COUNT(judul) AS judul, SUM(eks) AS eks
            FROM (
                SELECT knowledge_item_id,
                    (SELECT COUNT(id) FROM knowledge_item WHERE id = kis.knowledge_item_id) AS judul,
                    (SELECT COUNT(ks.id) FROM knowledge_item ki
                    LEFT JOIN knowledge_stock ks ON ks.knowledge_item_id = ki.id
                    WHERE ki.id = kis.knowledge_item_id GROUP BY ki.id) AS eks
                FROM knowledge_item_subject kis
                LEFT JOIN master_subject ms ON kis.master_subject_id = ms.id
                WHERE ms.course_code = ? AND ms.curriculum_code = ?
                ORDER BY knowledge_item_id
            ) a
        ", [$jurusan, $tahun]);
    }

    /**
     * Get additional Bahan Pustaka by kode jurusan.
     *
     * @param string $jurusan
     * @return \Illuminate\Support\Collection
     */
    public function getAdditionalBahanPustakaByKodeJur($jurusan)
    {
        return DB::select("
            SELECT SUM(b.NJMLTOTAL) AS eks, COUNT(*) AS judul
            FROM tbbpkuliahtambahan a
            LEFT JOIN tbkoleksi b ON a.TNOINDUK = SUBSTR(b.TNOINDUK, 1, 7)
            LEFT JOIN tbmatakuliah c ON a.KD_KULIAH = c.ID_KULIAH
            WHERE c.KD_JURUSAN = ? AND a.TH_KURIKULUM = 2008
        ", [$jurusan]);
    }

    /**
     * Get jurusan by kode jurusan.
     *
     * @param string $jurusan
     * @return \Illuminate\Support\Collection
     */
    public function getJurusanByKodeJur($jurusan)
    {
        return DB::table('t_mst_prodi')
            ->where('c_kode_prodi', $jurusan)
            ->value('nama_prodi');
    }

    /**
     * Get MK by kode jurusan and tahun.
     *
     * @param string $jurusan
     * @param string $tahun
     * @return \Illuminate\Support\Collection
     */
    public function getMKByKodeJur($jurusan, $tahun)
    {
        return DB::select("
            SELECT msu.*, SUBSTR(msu.code, -1) AS sks,
                (SELECT COUNT(*) 
                FROM knowledge_item_subject kis 
                LEFT JOIN knowledge_item kt ON kis.knowledge_item_id = kt.id 
                LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                WHERE ks.active = '1' 
                AND kp.active = '1' 
                AND knowledge_type_id = 21 
                AND kis.master_subject_id = msu.id) AS jmljudul,
                (SELECT COUNT(*) 
                FROM knowledge_item_subject kis 
                LEFT JOIN knowledge_item kt ON kis.knowledge_item_id = kt.id 
                LEFT JOIN knowledge_subject ks ON kt.knowledge_subject_id = ks.id
                LEFT JOIN knowledge_type kp ON kt.knowledge_type_id = kp.id
                WHERE ks.active = '1' 
                AND kp.active = '1' 
                AND knowledge_type_id IN (1, 2, 3, 33, 40, 41, 59, 65) 
                AND kis.master_subject_id = msu.id) AS jmljudul_fisik
            FROM master_subject msu 
            WHERE msu.course_code = ? 
            AND msu.curriculum_code = ?
        ", [$jurusan, $tahun]);
    }

    /**
     * Delete Bahan Pustaka reference by ID.
     *
     * @param string $id
     * @return bool
     */
    public function deleteBahanPustakaRef($id)
    {
        return DB::table('tbbpkuliah')->where('NO', $id)->delete();
    }

    /**
     * Check NO induk.
     *
     * @param string $NOinduk
     * @return \Illuminate\Support\Collection
     */
    public function checkNOInduk($NOinduk)
    {
        return DB::table('TBKOLEKSI')
            ->select('TNOINDUK', 'TJUDUL', 'TPENGARANG')
            ->where('TNOINDUK', 'LIKE', $NOinduk . '.%')
            ->get();
    }

    /**
     * Check duplicate.
     *
     * @param string $NOinduk
     * @param string $mk
     * @return \Illuminate\Support\Collection
     */
    public function checkDuplicate($NOinduk, $mk)
    {
        return DB::select("
            SELECT NO
            FROM (
                SELECT NO FROM TBBPKULIAH WHERE TNOINDUK = ? AND KD_KULIAH = ?
                UNION
                SELECT NO FROM TBBPKULIAHTAMBAHAN WHERE TNOINDUK = ? AND KD_KULIAH = ?
            ) a
        ", [$NOinduk, $mk, $NOinduk, $mk]);
    }

    /**
     * Add Bahan Pustaka reference plus.
     *
     * @param string $query
     * @return bool
     */
    public function addBahanPustakaRefPlus($query)
    {
        return DB::statement($query);
    }

    public function getBukuRef($kode, $type)
    {
        $temp = "ki.knowledge_type_id IN (1, 2, 3, 33, 40, 41, 59, 65, 21)";
        if ($type == 'book') {
            $temp = "ki.knowledge_type_id IN (1, 2, 3, 33, 40, 41, 59, 65)";
        } else if ($type == 'ebook') {
            $temp = "ki.knowledge_type_id = '21'";
        }

        return DB::select("
            SELECT ki.id AS kiid, kis.master_subject_id, ki.code AS kode_buku, ki.knowledge_type_id, cc.code AS klasifikasi, ki.title, ki.author, eks,
                ki.published_year,
                ki.isbn, (SELECT COUNT(*) FROM knowledge_stock ks WHERE status = '1' AND knowledge_item_id = ki.id) AS tersedia 
            FROM knowledge_item_subject kis 
            JOIN (
                SELECT ki.id, ki.code, ki.title,
                    ki.published_year,
                    ki.isbn, ki.classification_code_id, ki.knowledge_subject_id, ki.knowledge_type_id, ki.author, COUNT(ki.id) AS eks
                FROM knowledge_item ki
                JOIN knowledge_stock ks ON ki.id = ks.knowledge_item_id 
                GROUP BY ki.id, ki.code, ki.title, ki.published_year, ki.isbn, ki.classification_code_id, ki.knowledge_subject_id, ki.knowledge_type_id, ki.author
            ) ki ON kis.knowledge_item_id = ki.id 
            LEFT JOIN classification_code cc ON cc.id = ki.classification_code_id
            LEFT JOIN master_subject ms ON kis.master_subject_id = ms.id
            LEFT JOIN knowledge_subject ks ON ki.knowledge_subject_id = ks.id
            LEFT JOIN knowledge_type kp ON ki.knowledge_type_id = kp.id
            WHERE ks.active = '1' AND kp.active = '1' AND $temp
            AND kis.master_subject_id = ?
            ORDER BY ki.published_year DESC, ki.title
        ", [$kode]);
    }
}
