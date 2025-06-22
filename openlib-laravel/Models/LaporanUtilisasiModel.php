<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LaporanUtilisasiModel extends Model
{
    public function prodi()
    {
        return DB::table('t_mst_fakultas as tmf')
            ->select('tmp.c_kode_fakultas', 'tmp.c_kode_prodi', 'tmf.nama_fakultas', 'tmp.nama_prodi')
            ->leftJoin('t_mst_prodi as tmp', 'tmp.c_kode_fakultas', '=', 'tmf.c_kode_fakultas')
            ->where('tmf.nama_fakultas', '!=', '')
            ->where(function($query) {
                $query->where('tmp.nama_prodi', 'not like', '%Pindahan%')
                      ->where('tmp.nama_prodi', 'not like', '%International%')
                      ->where('tmp.nama_prodi', 'not like', '%Internasional%');
            })
            ->orderBy('tmf.nama_fakultas')
            ->orderBy('tmp.nama_prodi')
            ->get();
    }

    public function pengunjung($awal, $akhir, $prodi = "")
    {
        $query = DB::table('member_attendance as wd')
            ->select(DB::raw('count(member_id) as total'))
            ->whereBetween('wd.attended_at', [$awal, $akhir]);

        if ($prodi != "") {
            $temp = explode("-", $prodi);
            if (count($temp) > 1 && $temp[1] != "") {
                $query->where('master_data_course', $temp[1]);
            } else {
                $query->whereIn('master_data_course', function($subQuery) use ($temp) {
                    $subQuery->select('c_kode_prodi')
                                ->from('t_mst_prodi')
                                ->where('c_kode_fakultas', $temp[0]);
                });
            }
        }
        // dd($query->first());
        return $query->first();
    }

    public function peminjaman($awal, $akhir, $prodi = "")
    {
        $query = DB::table('rent as wd')
            ->leftJoin('member as m', 'm.id', '=', 'wd.member_id')
            ->select(DB::raw('count(wd.member_id) as total'))
            ->whereBetween('wd.rent_date', [$awal, $akhir]);

        if ($prodi != "") {
            $temp = explode("-", $prodi);
            if (count($temp) > 1 && $temp[1] != "") {
                $query->where('m.master_data_course', $temp[1]);
            } else {
                $query->whereIn('m.master_data_course', function($subQuery) use ($temp) {
                    $subQuery->select('c_kode_prodi')
                             ->from('t_mst_prodi')
                             ->where('c_kode_fakultas', $temp[0]);
                });
            }
        }

        return $query->first();
    }

    public function pengembalian($awal, $akhir, $prodi = "")
    {
        $query = DB::table('rent as wd')
            ->leftJoin('member as m', 'm.id', '=', 'wd.member_id')
            ->select(DB::raw('count(wd.member_id) as total'))
            ->whereBetween('wd.return_date', [$awal, $akhir]);

        if ($prodi != "") {
            $temp = explode("-", $prodi);
            if (count($temp) > 1 && $temp[1] != "") {
                $query->where('m.master_data_course', $temp[1]);
            } else {
                $query->whereIn('m.master_data_course', function($subQuery) use ($temp) {
                    $subQuery->select('c_kode_prodi')
                             ->from('t_mst_prodi')
                             ->where('c_kode_fakultas', $temp[0]);
                });
            }
        }

        return $query->first();
    }

    public function bebaspustaka($awal, $akhir, $prodi = "")
    {
        $query = DB::table('free_letter as wd')
            ->select(DB::raw('count(distinct wd.member_number) as total'))
            ->whereBetween('wd.created_at', [$awal, $akhir])
            ->where('wd.is_member', '1');

        if ($prodi != "") {
            $temp = explode("-", $prodi);
            if (count($temp) > 1 && $temp[1] != "") {
                $query->where('wd.course_code', $temp[1]);
            } else {
                $query->whereIn('wd.course_code', function($subQuery) use ($temp) {
                    $subQuery->select('c_kode_prodi')
                             ->from('t_mst_prodi')
                             ->where('c_kode_fakultas', $temp[0]);
                });
            }
        }

        return $query->first();
    }

    public function tapa_based_on_bebaspustaka_date($awal, $akhir, $status, $prodi = "")
    {
        $query = DB::table('free_letter as fl')
            ->leftJoin('workflow_document as wdd', 'wdd.member_id', '=', 'fl.registration_number')
            ->leftJoin('workflow_document_state as wds', 'wds.document_id', '=', 'wdd.id')
            ->select(DB::raw('count(*) as total'))
            ->whereBetween('fl.created_at', [$awal, $akhir])
            ->where('fl.is_member', '1')
            ->where('wds.state_id', $status)
            ->where('wds.id', function($subQuery) {
                $subQuery->select(DB::raw('max(id)'))
                         ->from('workflow_document_state')
                         ->whereColumn('document_id', 'wdd.id')
                         ->where('state_id', '!=', '5');
            })
            ->groupBy('wdd.member_id');

        if ($prodi != "") {
            $temp = explode("-", $prodi);
            if (count($temp) > 1 && $temp[1] != "") {
                $query->where('fl.course_code', $temp[1]);
            } else {
                $query->whereIn('fl.course_code', function($subQuery) use ($temp) {
                    $subQuery->select('c_kode_prodi')
                             ->from('t_mst_prodi')
                             ->where('c_kode_fakultas', $temp[0]);
                });
            }
        }

        return DB::query()->from(DB::raw("({$query->toSql()}) as sub"))->mergeBindings($query)->select(DB::raw('count(*) as total'))->first();
    }

    public function tapa_transaksi_readonly($akhir)
    {
        $temp = explode("-", $akhir);
        return DB::table('online_access')
            ->where('year', $temp[0])
            ->where('type', 'karyailmiah')
            ->get();
    }

    public function ebook_transaksi_readonly($akhir)
    {
        $temp = explode("-", $akhir);
        return DB::table('online_access')
            ->where('year', $temp[0])
            ->where('type', 'ebook')
            ->get();
    }

    public function visitor_openlib($akhir)
    {
        $temp = explode("-", $akhir);
        return DB::table('online_visitor')
            ->where('year', $temp[0])
            ->orderBy('type', 'desc')
            ->get();
    }

    public function visitor_eproc($akhir)
    {
        $temp = explode("-", $akhir);
        return DB::table('online_visitor_eproc')
            ->where('year', $temp[0])
            ->orderBy('type', 'desc')
            ->get();
    }
}
