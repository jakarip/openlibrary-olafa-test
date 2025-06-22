<?php

namespace App\Http\Controllers\OLAFA\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DashboardOlafaModel;
use App\Models\EproceedingModel;
use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;

class DashboardOLAFAController extends Controller
{
    public function index (){
		if(!auth()->can('config-catalog-location.view')){
            return redirect('/home');
        }

		
        return view('olafa.dashboard.index');
    }

    public function ajax(Request $request, DashboardOlafaModel $DashboardModel){
        $year = $request->input('year');
        $year = '2020';
        // dd($year);
        $digital = '4,5,6,21,24,25,47,49,51,52,55,62,70,73,75,79';
        
        $data['koleksi']['judul']				= $DashboardModel->getTotalJudulFisik($year,$digital)[0]->total ;
		$data['koleksi']['judul_all']			= $DashboardModel->getTotalJudulFisikAll($year,$digital)[0]->total ;
		$data['koleksi']['judul_digital']				= $DashboardModel->getTotalJudulDigital($year,$digital)[0]->total ;
		$data['koleksi']['judul_digital_all']			= $DashboardModel->getTotalJudulDigitalAll($year,$digital)[0]->total ;
		$data['koleksi']['koleksi']				= $DashboardModel->getTotalKoleksiFisik($year,$digital)[0]->total ;
		$data['koleksi']['koleksi_all']			= $DashboardModel->getTotalKoleksiFisikAll($year,$digital)[0]->total ;
		// $data['koleksi']['koleksi_digital']				= $DashboardModel->getTotalKoleksiDigital($year,$digital)[0]->total ;
		// $data['koleksi']['koleksi_digital_all']			= $DashboardModel->getTotalKoleksiDigitalAll($year,$digital)[0]->total ;
		$data['pengunjung']['fisik'] 			= $DashboardModel->getTotalPengunjungFisik($year)[0]->total ;
		$data['pengunjung']['journal'] 			= $DashboardModel->getTotalPengunjungJournal()[0]->total ;
		$data['pengunjung']['online_user'] 		= $DashboardModel->getTotalPengunjungOnline($year)[0]->total ;
		$data['pengunjung']['online_pageview'] 	= $DashboardModel->getTotalPageView($year)[0]->total ;
		$data['pengunjung']['online_user_eproc'] 		= $DashboardModel->getTotalPengunjungOnlineEproc($year)[0]->total ;
		$data['pengunjung']['online_pageview_eproc'] 	= $DashboardModel->getTotalPageViewEproc($year)[0]->total ;
        
		$data['anggota']['civitas_webmobile']		= $DashboardModel->getTotalCivitasWebMobile($year)[0]->total ;
		$data['anggota']['civitas_web']				= $DashboardModel->getTotalCivitasWeb($year)[0]->total ;
		$data['anggota']['umum_webmobile']			= $DashboardModel->getTotalUmumWebMobile($year)[0]->total ;
		$data['anggota']['umum_web']				= $DashboardModel->getTotalUmumWeb($year)[0]->total ;
		$data['anggota']['civitas_all_webmobile']	= $DashboardModel->getTotalCivitasAllWebMobile()[0]->total ;
		$data['anggota']['civitas_all_web']			= $DashboardModel->getTotalCivitasAllWeb()[0]->total ;
		$data['anggota']['umum_all_webmobile']		= $DashboardModel->getTotalUmumAllWebMobile()[0]->total ;
		$data['anggota']['umum_all_web']			= $DashboardModel->getTotalUmumAllWeb()[0]->total ;
		// $data['pengunjung']['online_user'] 		= array_sum($end['users']);
		// $data['pengunjung']['online_pageview'] 	= array_sum($end['pageviews']);
		
		$data['sirkulasi']['peminjaman']		= $DashboardModel->getTotalPeminjaman($year)[0]->total ;
		$data['sirkulasi']['pengembalian'] 		= $DashboardModel->getTotalPengembalian($year)[0]->total ; 
		$data['sirkulasi']['ruangan'] 			= $DashboardModel->getTotalRoom($year)[0]->total ;
		$data['sirkulasi']['bds'] 				= $DashboardModel->getTotalBds($year)[0]->total ;
		$data['sirkulasi']['usulan'] 			= $DashboardModel->getTotalUsulan($year)[0]->total ;
		$data['sirkulasi']['sbkp'] 				= $DashboardModel->getTotalSBKP($year)[0]->total ;
		$data['sirkulasi']['ebook'] 			= $DashboardModel->getTotalAccessEbook($year)[0]->total ;
		$data['sirkulasi']['karyailmiah'] 		= $DashboardModel->getTotalAccessKaryaIlmiah($year)[0]->total ;
        
        
        // dd($data);
        $lastyear 	= $year-1;

        $judul 				= $DashboardModel->getTotalJudulFisikPerBulan($year,$digital); 
		$koleksi 			= $DashboardModel->getTotalKoleksiFisikPerBulan($year,$digital);  
		$judul_digital 		= $DashboardModel->getTotalJudulDigitalPerBulan($year,$digital); 
		// $koleksi_digital	= $DashboardModel->getTotalKoleksiDigitalPerBulan($year,$digital); 
		$civitas_webmobile	= $DashboardModel->getTotalCivitasWebMobilePerBulan($year);
		$civitas_web		= $DashboardModel->getTotalCivitasWebPerBulan($year);
		$umum_webmobile		= $DashboardModel->getTotalUmumWebMobilePerBulan($year);
		$umum_web			= $DashboardModel->getTotalUmumWebPerBulan($year);
		$pengunjung 		= $DashboardModel->getTotalPengunjungFisikPerBulan($year);   
		$peminjaman 		= $DashboardModel->getTotalPeminjamanPerBulan($year); 
		$pengembalian		= $DashboardModel->getTotalPengembalianPerBulan($year);
		$ruangan				= $DashboardModel->getTotalRoomPerBulan($year);
		$bds				= $DashboardModel->getTotalBdsPerBulan($year);
		$usulan				= $DashboardModel->getTotalUsulanPerBulan($year);
		$sbkp				= $DashboardModel->getTotalSBKPPerBulan($year);
		$journalvisitor		= $DashboardModel->getTotalJournalVisitorPerBulan($year);
		
		$judullast 			= $DashboardModel->getTotalJudulFisikPerBulan($lastyear,$digital); 
		$koleksilast 		= $DashboardModel->getTotalKoleksiFisikPerBulan($lastyear,$digital);  
		$judul_digital_last 	= $DashboardModel->getTotalJudulDigitalPerBulan($lastyear,$digital); 
		// $koleksi_digital_last	= $DashboardModel->getTotalKoleksiDigitalPerBulan($lastyear,$digital); 
		$civitas_webmobilelast		= $DashboardModel->getTotalCivitasWebMobilePerBulan($lastyear);
		$civitas_weblast		= $DashboardModel->getTotalCivitasWebPerBulan($lastyear);
		$umum_webmobilelast			= $DashboardModel->getTotalUmumWebMobilePerBulan($lastyear);
		$umum_weblast			= $DashboardModel->getTotalUmumWebPerBulan($lastyear);
		$pengunjunglast 	= $DashboardModel->getTotalPengunjungFisikPerBulan($lastyear);  
		$peminjamanlast 	= $DashboardModel->getTotalPeminjamanPerBulan($lastyear); 
		$pengembalianlast	= $DashboardModel->getTotalPengembalianPerBulan($lastyear);
		$ruanganlast		= $DashboardModel->getTotalRoomPerBulan($lastyear);
		$bdslast			= $DashboardModel->getTotalBdsPerBulan($lastyear);
		$usulanlast			= $DashboardModel->getTotalUsulanPerBulan($lastyear);
		$sbkplast			= $DashboardModel->getTotalSBKPPerBulan($lastyear);
		$journalvisitorlast	= $DashboardModel->getTotalJournalVisitorPerBulan($lastyear); 

		// $online['lastyear'] 			= $DashboardModel->getTotalOnlineVisitorPerBulan($lastyear);
		// $online['year'] 					= $DashboardModel->getTotalOnlineVisitorPerBulan($year); 
		// $pageviews['lastyear'] 		= $DashboardModel->getTotalPageViewsPerBulan($lastyear);
		// $pageviews['year'] 				= $DashboardModel->getTotalPageViewsPerBulan($year);
        
		// $onlineeproc['lastyear'] 			= $DashboardModel->getTotalOnlineVisitorPerBulanEproc($lastyear);
		// $onlineeproc['year'] 					= $DashboardModel->getTotalOnlineVisitorPerBulanEproc($year);
		// $pageviewseproc['lastyear'] 		= $DashboardModel->getTotalPageViewsPerBulanEproc($lastyear);
		// $pageviewseproc['year'] 				= $DashboardModel->getTotalPageViewsPerBulanEproc($year); 
        
		
		// $ebook['lastyear'] 		= $DashboardModel->getTotalAccessEbookPerBulan($lastyear);
		// $ebook['year'] 				= $DashboardModel->getTotalAccessEbookPerBulan($year); 
        
		
		// $karyailmiah['lastyear'] 		= $DashboardModel->getTotalAccessKaryaIlmiahPerBulan($lastyear);
		// $karyailmiah['year'] 				= $DashboardModel->getTotalAccessKaryaIlmiahPerBulan($year); 

		// $data['online']['lastyear'][] =  $online['lastyear'][0]->januari;
		// $data['online']['lastyear'][] =  $online['lastyear'][0]->februari;
		// $data['online']['lastyear'][] =  $online['lastyear'][0]->maret;
		// $data['online']['lastyear'][] =  $online['lastyear'][0]->april;
		// $data['online']['lastyear'][] =  $online['lastyear'][0]->mei;
		// $data['online']['lastyear'][] =  $online['lastyear'][0]->juni;
		// $data['online']['lastyear'][] =  $online['lastyear'][0]->juli;
		// $data['online']['lastyear'][] =  $online['lastyear'][0]->agustus;
		// $data['online']['lastyear'][] =  $online['lastyear'][0]->september;
		// $data['online']['lastyear'][] =  $online['lastyear'][0]->oktober;
		// $data['online']['lastyear'][] =  $online['lastyear'][0]->november;
		// $data['online']['lastyear'][] =  $online['lastyear'][0]->desember;
		
		// $data['online']['year'][] =  $online['year'][0]->januari;
		// $data['online']['year'][] =  $online['year'][0]->februari;
		// $data['online']['year'][] =  $online['year'][0]->maret;
		// $data['online']['year'][] =  $online['year'][0]->april;
		// $data['online']['year'][] =  $online['year'][0]->mei;
		// $data['online']['year'][] =  $online['year'][0]->juni;
		// $data['online']['year'][] =  $online['year'][0]->juli;
		// $data['online']['year'][] =  $online['year'][0]->agustus;
		// $data['online']['year'][] =  $online['year'][0]->september;
		// $data['online']['year'][] =  $online['year'][0]->oktober;
		// $data['online']['year'][] =  $online['year'][0]->november;
		// $data['online']['year'][] =  $online['year'][0]->desember;
        
		// $data['pageviews']['lastyear'][] =  $pageviews['lastyear'][0]->januari;
		// $data['pageviews']['lastyear'][] =  $pageviews['lastyear'][0]->februari;
		// $data['pageviews']['lastyear'][] =  $pageviews['lastyear'][0]->maret;
		// $data['pageviews']['lastyear'][] =  $pageviews['lastyear'][0]->april;
		// $data['pageviews']['lastyear'][] =  $pageviews['lastyear'][0]->mei;
		// $data['pageviews']['lastyear'][] =  $pageviews['lastyear'][0]->juni;
		// $data['pageviews']['lastyear'][] =  $pageviews['lastyear'][0]->juli;
		// $data['pageviews']['lastyear'][] =  $pageviews['lastyear'][0]->agustus;
		// $data['pageviews']['lastyear'][] =  $pageviews['lastyear'][0]->september;
		// $data['pageviews']['lastyear'][] =  $pageviews['lastyear'][0]->oktober;
		// $data['pageviews']['lastyear'][] =  $pageviews['lastyear'][0]->november;
		// $data['pageviews']['lastyear'][] =  $pageviews['lastyear'][0]->desember;
		
		// $data['pageviews']['year'][] =  $pageviews['year'][0]->januari;
		// $data['pageviews']['year'][] =  $pageviews['year'][0]->februari;
		// $data['pageviews']['year'][] =  $pageviews['year'][0]->maret;
		// $data['pageviews']['year'][] =  $pageviews['year'][0]->april;
		// $data['pageviews']['year'][] =  $pageviews['year'][0]->mei;
		// $data['pageviews']['year'][] =  $pageviews['year'][0]->juni;
		// $data['pageviews']['year'][] =  $pageviews['year'][0]->juli;
		// $data['pageviews']['year'][] =  $pageviews['year'][0]->agustus;
		// $data['pageviews']['year'][] =  $pageviews['year'][0]->september;
		// $data['pageviews']['year'][] =  $pageviews['year'][0]->oktober;
		// $data['pageviews']['year'][] =  $pageviews['year'][0]->november;
		// $data['pageviews']['year'][] =  $pageviews['year'][0]->desember; 

		

		// $data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear'][0]->januari;
		// $data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear'][0]->februari;
		// $data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear'][0]->maret;
		// $data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear'][0]->april;
		// $data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear'][0]->mei;
		// $data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear'][0]->juni;
		// $data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear'][0]->juli;
		// $data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear'][0]->agustus;
		// $data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear'][0]->september;
		// $data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear'][0]->oktober;
		// $data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear'][0]->november;
		// $data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear'][0]->desember;
		
		// $data['onlineeproc']['year'][] =  $onlineeproc['year'][0]->januari;
		// $data['onlineeproc']['year'][] =  $onlineeproc['year'][0]->februari;
		// $data['onlineeproc']['year'][] =  $onlineeproc['year'][0]->maret;
		// $data['onlineeproc']['year'][] =  $onlineeproc['year'][0]->april;
		// $data['onlineeproc']['year'][] =  $onlineeproc['year'][0]->mei;
		// $data['onlineeproc']['year'][] =  $onlineeproc['year'][0]->juni;
		// $data['onlineeproc']['year'][] =  $onlineeproc['year'][0]->juli;
		// $data['onlineeproc']['year'][] =  $onlineeproc['year'][0]->agustus;
		// $data['onlineeproc']['year'][] =  $onlineeproc['year'][0]->september;
		// $data['onlineeproc']['year'][] =  $onlineeproc['year'][0]->oktober;
		// $data['onlineeproc']['year'][] =  $onlineeproc['year'][0]->november;
		// $data['onlineeproc']['year'][] =  $onlineeproc['year'][0]->desember;

		// $data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear'][0]->januari;
		// $data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear'][0]->februari;
		// $data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear'][0]->maret;
		// $data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear'][0]->april;
		// $data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear'][0]->mei;
		// $data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear'][0]->juni;
		// $data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear'][0]->juli;
		// $data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear'][0]->agustus;
		// $data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear'][0]->september;
		// $data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear'][0]->oktober;
		// $data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear'][0]->november;
		// $data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear'][0]->desember;
		
		// $data['pageviewseproc']['year'][] =  $pageviewseproc['year'][0]->januari;
		// $data['pageviewseproc']['year'][] =  $pageviewseproc['year'][0]->februari;
		// $data['pageviewseproc']['year'][] =  $pageviewseproc['year'][0]->maret;
		// $data['pageviewseproc']['year'][] =  $pageviewseproc['year'][0]->april;
		// $data['pageviewseproc']['year'][] =  $pageviewseproc['year'][0]->mei;
		// $data['pageviewseproc']['year'][] =  $pageviewseproc['year'][0]->juni;
		// $data['pageviewseproc']['year'][] =  $pageviewseproc['year'][0]->juli;
		// $data['pageviewseproc']['year'][] =  $pageviewseproc['year'][0]->agustus;
		// $data['pageviewseproc']['year'][] =  $pageviewseproc['year'][0]->september;
		// $data['pageviewseproc']['year'][] =  $pageviewseproc['year'][0]->oktober;
		// $data['pageviewseproc']['year'][] =  $pageviewseproc['year'][0]->november;
		// $data['pageviewseproc']['year'][] =  $pageviewseproc['year'][0]->desember; 


		
        
		// $months = ['januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember'];

        // foreach ($months as $month) {
        //     if (!empty($ebook['lastyear']) && isset($ebook['lastyear'][0]->$month)) {
        //         $data['ebook']['lastyear'][] = $ebook['lastyear'][0]->$month;
        //     } else {
        //         // Handle the case where 'lastyear' is empty or the month is not set
        //         $data['ebook']['lastyear'][] = 0; // or any default value
        //     }
        // }
		
		// $data['ebook']['year'][] =  $ebook['year'][0]->januari;
		// $data['ebook']['year'][] =  $ebook['year'][0]->februari;
		// $data['ebook']['year'][] =  $ebook['year'][0]->maret;
		// $data['ebook']['year'][] =  $ebook['year'][0]->april;
		// $data['ebook']['year'][] =  $ebook['year'][0]->mei;
		// $data['ebook']['year'][] =  $ebook['year'][0]->juni;
		// $data['ebook']['year'][] =  $ebook['year'][0]->juli;
		// $data['ebook']['year'][] =  $ebook['year'][0]->agustus;
		// $data['ebook']['year'][] =  $ebook['year'][0]->september;
		// $data['ebook']['year'][] =  $ebook['year'][0]->oktober;
		// $data['ebook']['year'][] =  $ebook['year'][0]->november;
		// $data['ebook']['year'][] =  $ebook['year'][0]->desember;  
        

        // foreach ($months as $month) {
        //     if (!empty($karyailmiah['lastyear']) && isset($karyailmiah['lastyear'][0]->$month)) {
        //         $data['karyailmiah']['lastyear'][] =  $karyailmiah['lastyear'][0]->$month;
        //     } else {
        //         // Handle the case where 'lastyear' is empty or the month is not set
        //         $data['karyailmiah']['lastyear'][] =  0;
        //     }
        // }

		// $data['karyailmiah']['year'][] =  $karyailmiah['year'][0]->januari;
		// $data['karyailmiah']['year'][] =  $karyailmiah['year'][0]->februari;
		// $data['karyailmiah']['year'][] =  $karyailmiah['year'][0]->maret;
		// $data['karyailmiah']['year'][] =  $karyailmiah['year'][0]->april;
		// $data['karyailmiah']['year'][] =  $karyailmiah['year'][0]->mei;
		// $data['karyailmiah']['year'][] =  $karyailmiah['year'][0]->juni;
		// $data['karyailmiah']['year'][] =  $karyailmiah['year'][0]->juli;
		// $data['karyailmiah']['year'][] =  $karyailmiah['year'][0]->agustus;
		// $data['karyailmiah']['year'][] =  $karyailmiah['year'][0]->september;
		// $data['karyailmiah']['year'][] =  $karyailmiah['year'][0]->oktober;
		// $data['karyailmiah']['year'][] =  $karyailmiah['year'][0]->november;
		// $data['karyailmiah']['year'][] =  $karyailmiah['year'][0]->desember; 
        
        foreach ($judullast as $key => $row){
			$last = 0;
			$now  = 0;
			$keys = $key+1;
			if(strlen($keys)=='1'){
				$keys = "0".$keys;
			}
			for($i=0;$i<12;$i++){
				if(!empty($journalvisitor[$i]->total)){
					$bln = substr($journalvisitor[$i]->tgl,5,2);
					if($keys==$bln){
						$now = $journalvisitor[$i]->total;
					}
				}
				
				if(!empty($journalvisitorlast[$i]->total)){
					$bln = substr($journalvisitorlast[$i]->tgl,5,2);
					if($keys==$bln){
						$last = $journalvisitorlast[$i]->total;
					}
				}
			}
			
			$data['grafik']['judul']['lastyear'][] 			= $row->total;
			$data['grafik']['koleksi']['lastyear'][] 		= $koleksilast[$key]->total; 
			$data['grafik']['judul_digital']['lastyear'][] 	= $judul_digital_last[$key]->total;
			// $data['grafik']['koleksi_digital']['lastyear'][]= $koleksi_digitallast[$key]->total;  
			$data['grafik']['civitas_webmobile']['lastyear'][] 	= $civitas_webmobilelast[$key]->total;
			$data['grafik']['civitas_web']['lastyear'][] 		= $civitas_weblast[$key]->total;
			$data['grafik']['umum_webmobile']['lastyear'][] 	= $umum_webmobilelast[$key]->total; 
			$data['grafik']['umum_web']['lastyear'][] 			= $umum_weblast[$key]->total; 
			$data['grafik']['pengunjung']['lastyear'][] 	= $pengunjunglast[$key]->total; 
			$data['grafik']['peminjaman']['lastyear'][] 	= $peminjamanlast[$key]->total; 
			$data['grafik']['pengembalian']['lastyear'][] 	= $pengembalianlast[$key]->total; 
			$data['grafik']['ruangan']['lastyear'][] 		= $ruanganlast[$key]->total; 
			$data['grafik']['bds']['lastyear'][] 			= $bdslast[$key]->total; 
			$data['grafik']['usulan']['lastyear'][] 		= $usulanlast[$key]->total; 
			$data['grafik']['sbkp']['lastyear'][] 			= $sbkplast[$key]->total; 
			$data['grafik']['journal']['lastyear'][] 		= $last; 

			$data['grafik']['judul']['year'][] 				= (empty($judul[$key]->total)?0:$judul[$key]->total); 
			$data['grafik']['koleksi']['year'][] 			= (empty($koleksi[$key]->total)?0:$koleksi[$key]->total); 
			$data['grafik']['judul_digital']['year'][] 		= (empty($judul_digital[$key]->total)?0:$judul_digital[$key]->total); 
			// $data['grafik']['koleksi_digital']['year'][] 	= (empty($koleksi_digital[$key]->total)?0:$koleksi_digital[$key]->total); 
			// $data['grafik']['digital']['year'][] 			= (empty($digitals[$key]->total)?0:$digitals[$key]->total); 
			$data['grafik']['civitas_webmobile']['year'][] 	= (empty($civitas_webmobile[$key]->total)?0:$civitas_webmobile[$key]->total); 
			$data['grafik']['civitas_web']['year'][] 		= (empty($civitas_web[$key]->total)?0:$civitas_web[$key]->total); 
			$data['grafik']['umum_webmobile']['year'][] 	= (empty($umum_webmobile[$key]->total)?0:$umum_webmobile[$key]->total); 
			$data['grafik']['umum_web']['year'][] 			= (empty($umum_web[$key]->total)?0:$umum_web[$key]->total); 
			$data['grafik']['pengunjung']['year'][] 		= (empty($pengunjung[$key]->total)?0:$pengunjung[$key]->total); 
			$data['grafik']['peminjaman']['year'][] 		= (empty($peminjaman[$key]->total)?0:$peminjaman[$key]->total);  
			$data['grafik']['pengembalian']['year'][] 		= (empty($pengembalian[$key]->total)?0:$pengembalian[$key]->total); 
			$data['grafik']['ruangan']['year'][] 			= (empty($ruangan[$key]->total)?0:$ruangan[$key]->total);
			$data['grafik']['bds']['year'][] 			= (empty($bds[$key]->total)?0:$bds[$key]->total);
			$data['grafik']['usulan']['year'][] 			= (empty($usulan[$key]->total)?0:$usulan[$key]->total);
			$data['grafik']['sbkp']['year'][] 			= (empty($sbkp[$key]->total)?0:$sbkp[$key]->total);
			$data['grafik']['journal']['year'][] 			= $now; 
		} 

        return response()->json($data);

        // $rasio = $DashboardModel->getRasioMKperProdi2(); 
		// $total = 0;
		// foreach($rasio as $row){ 
		// 	$total = $total + $row->judul; 
		// } 

		// // print_r($data['grafik']['judul_digital']);
		
		// foreach($rasio as $row){ 
		// 		if($row->mk==0) $rat = 0;
		// 		else $rat = round($row->mkadabuku / $row->mk *100,2);
		// 		$data['rasio']['prodi'][] = "'".$row->nama_prodi." (".$row->judul.")'";
		// 		$data['rasio']['rasio'][] = (isset($row->judul)?$rat:0);
		// 		$data['rasio']['total'][] = (isset($row->judul)?$row->judul:0);
		// 		$data['rasio']['mkadabuku'][] = (isset($row->mkadabuku)?$row->mkadabuku:0);
		// 		$data['rasio']['mk'][] = (isset($row->mk)?$row->mk:0);
		// 		$total = $total + $row->judul;
				
		// } 

		// // echo array_sum($data['rasio']['rasio']);
		// $data['rasio']['totals'] = round(array_sum($data['rasio']['rasio'])/count($rasio),2);

        // $file = $DashboardModel->getFileTotal();
		
		// foreach($file as $row){
		// 	$data['file']['name'][] = "'".$row->nama_file."'";
		// 	$data['file']['total'][] = $row->total_file;
			
		// } 

        // $eprocModel = new EproceedingModel;
        // $edition		 		= $eprocModel->getLastEprocEdition(); 
        // $edition = $edition[0];
        // $data['eproceeding']['edition'] 	= $edition->nama;
		// $data['eproceeding']['name']		= array('0' => '"TA/PA/Thesis Masuk"',
        //                                             '1' => '"Jurnal Masuk"',
        //                                             '2' => '"On Draft"',
        //                                             '3' => '"Need Revision"',
        //                                             '4' => '"Ready for Review"',
        //                                             '5' => '"Archieved"',
        //                                             '6' => '"Not Feasible"',
        //                                             '7' => '"Publish Eksternal"',
        //                                             '8' => '"Publish Tel-U Proceeding"',
        //                                             '9' => '"Metadata Approve for Catalog"');
		// $data['eproceeding']['total'][]		= $eprocModel->totaltamasukbykodejur('',$edition->datestart,$edition->datefinish);  
		// $data['eproceeding']['total'][]		= $eprocModel->totaljurnalmasukbykodejur('',$edition->datestart,$edition->datefinish);
		// $data['eproceeding']['total'][]		= $eprocModel->totaldocbykodejurandstate('','22',$edition->datestart,$edition->datefinish);   
		// $data['eproceeding']['total'][]		= $eprocModel->totaldocbykodejurandstate('','2',$edition->datestart,$edition->datefinish); 
		// $data['eproceeding']['total'][]		= $eprocModel->totaldocbykodejurandstate('','1',$edition->datestart,$edition->datefinish); 
		// $data['eproceeding']['total'][]		= $eprocModel->totaldocbykodejurandstate('','5',$edition->datestart,$edition->datefinish); 
		// $data['eproceeding']['total'][]		= $eprocModel->totaldocbykodejurandstate('','3',$edition->datestart,$edition->datefinish); 
		// $data['eproceeding']['total'][]		= $eprocModel->totaldocbykodejurandstate('','52',$edition->datestart,$edition->datefinish); 
		// $data['eproceeding']['total'][]		= $eprocModel->totaldocbykodejurandstate('','53',$edition->datestart,$edition->datefinish);  
		// $data['eproceeding']['total'][]		= $eprocModel->totaldocbykodejurandstate('','91',$edition->datestart,$edition->datefinish); 

    }
}
