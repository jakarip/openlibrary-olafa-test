<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require FCPATH.'/vendor/autoload.php'; 
class Dashboard extends CI_Controller {
	
	function __construct() {
        parent::__construct();  
		$this->load->model('HomeModel');  
		$this->load->model('ApiModel'); 
		$this->load->model('MonitoringEproceedingModel'); 
		if (!$this->session->userdata('language')) $this->session->set_userdata(array('language' => 'ina'));
		if(!$this->session->userdata('login')) redirect('');
		
	
    }

	
	public function analytics()
	{	
		 
		//start google analytics
		$analytics = $this->initializeAnalytics();  
		$service = new Google_Service_AnalyticsReporting($analytics);  
		
		$start = array();
		$end = array();
		for($i=1;$i<=12;$i++){
			// $year = date('y')-1;
			// $month = new google_service_analyticsreporting_daterange();
			// $date = $year."-".sprintf("%02d", $i)."-01";
			// $month->setstartdate($date);
			// $month->setenddate(date("y-m-t", strtotime($date)));
			
			// $users = new google_service_analyticsreporting_metric();
			// $users->setexpression("ga:users");
			// $users->setalias("users");

			// //create the dimensions object.
			// $pageviews = new google_service_analyticsreporting_metric();
			// $pageviews->setexpression("ga:pageviews");
			// $pageviews->setalias("pageviews"); 

			// // create the reportrequest object.
			// $request = new google_service_analyticsreporting_reportrequest();
			// $request->setviewid("110460126"); 
			// $request->setdateranges(array($month));
			// $request->setmetrics(array($users,$pageviews));

			// $body = new google_service_analyticsreporting_getreportsrequest();
			// $body->setreportrequests( array( $request) );
			// $datas = $service->reports->batchget( $body );
			// $dt = $this->printresults($datas);
		 
			// $start['users'][$i-0] = $dt[0];
			// $start['pageviews'][$i-0] = $dt[1];
			
			 
			$year = date('Y');
			$month = new Google_Service_AnalyticsReporting_DateRange();
			$date = $year."-".sprintf("%02d", $i)."-01";
			$month->setStartDate($date);
			$month->setEndDate(date("Y-m-t", strtotime($date)));
	 
			$users = new Google_Service_AnalyticsReporting_Metric();
			$users->setExpression("ga:users");
			$users->setAlias("users");

			//Create the Dimensions object.
			$pageviews = new Google_Service_AnalyticsReporting_Metric();
			$pageviews->setExpression("ga:pageviews");
			$pageviews->setAlias("pageviews"); 

			// Create the ReportRequest object.
			$request = new Google_Service_AnalyticsReporting_ReportRequest();
			$request->setViewId("110460126"); 
			$request->setDateRanges(array($month));
			$request->setMetrics(array($users,$pageviews));

			$body = new Google_Service_AnalyticsReporting_GetReportsRequest();
			$body->setReportRequests( array( $request) );
			$datas = $service->reports->batchGet( $body );
			$dt = $this->printResults($datas);
			$end['users'][$i-0] = $dt[0];
			$end['pageviews'][$i-0] = $dt[1]; 
		} 
		
		//end google analytics
		
	}
	
	function initializeAnalytics()
	{ 
	  $KEY_FILE_LOCATION = FCPATH.'analytics-api-268518-6054b820f64c.json';

	  // Create and configure a new client object.
	  $client = new Google_Client();
	  $client->setApplicationName("Hello Analytics Reporting");
	  $client->setAuthConfig($KEY_FILE_LOCATION);
	  $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
	  $analytics = new Google_Service_Analytics($client);

	  return $client;
	}
	 
	function printResults(&$reports) {
		$temp = array();
		for ( $reportIndex = 0; $reportIndex < count( $reports ); $reportIndex++ ) {
			$report = $reports[ $reportIndex ];
			$header = $report->getColumnHeader();
			$dimensionHeaders = $header->getDimensions();
			$metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
			$rows = $report->getData()->getRows();
			
			
			if(count($rows)!=0){
				for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
				  $row = $rows[ $rowIndex ];
				  // $dimensions = $row->getDimensions();
				  $metrics = $row->getMetrics();
				  // for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
					// print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "\n");
				  // }
				  for ($j = 0; $j < count($metrics); $j++) {
					$values = $metrics[$j]->getValues();
					for ($k = 0; $k < count($values); $k++) {
						$entry = $metricHeaders[$k];
						$temp[$k] = $values[$k]; 
						// print($entry->getName() . ": " . $values[$k] . "\n");
					}
				  }
				}
			}
			else {
				$temp[0] = 0;
				$temp[1] = 0;
			}
		}
		return $temp;
	}
	
	public function index($year="")
	{	   
		// ini_set('display_errors', '1');
		// ini_set('display_startup_errors', '1');
		// error_reporting(E_ALL);
		$year = date('2020');
		if($year=="") $year = date('Y');
		$digital = '4,5,6,21,24,25,47,49,51,52,55,62,70,73,75,79';
		//end google analytics
		$data['koleksi']['judul']				= $this->HomeModel->getTotalJudulFisik($year,$digital)->row();
		$data['koleksi']['koleksi']				= $this->HomeModel->getTotalKoleksiFisik($year,$digital)->row();
		$data['koleksi']['judul_digital']				= $this->HomeModel->getTotalJudulDigital($year,$digital)->row();
		// $data['koleksi']['koleksi_digital']				= $this->HomeModel->getTotalKoleksiDigital($year,$digital)->row();
		$data['koleksi']['judul_all']			= $this->HomeModel->getTotalJudulFisikAll($year,$digital)->row();
		$data['koleksi']['koleksi_all']			= $this->HomeModel->getTotalKoleksiFisikAll($year,$digital)->row();
		$data['koleksi']['judul_digital_all']			= $this->HomeModel->getTotalJudulDigitalAll($year,$digital)->row();
		// $data['koleksi']['koleksi_digital_all']			= $this->HomeModel->getTotalKoleksiDigitalAll($year,$digital)->row();
		$data['pengunjung']['fisik'] 			= $this->HomeModel->getTotalPengunjungFisik($year)->row();
		$data['pengunjung']['journal'] 			= $this->HomeModel->getTotalPengunjungJournal()->row();
		$data['pengunjung']['online_user'] 		= $this->HomeModel->getTotalPengunjungOnline($year)->row();
		$data['pengunjung']['online_pageview'] 	= $this->HomeModel->getTotalPageView($year)->row();
		$data['pengunjung']['online_user_eproc'] 		= $this->HomeModel->getTotalPengunjungOnlineEproc($year)->row();
		$data['pengunjung']['online_pageview_eproc'] 	= $this->HomeModel->getTotalPageViewEproc($year)->row();
 
		$data['anggota']['civitas_webmobile']		= $this->HomeModel->getTotalCivitasWebMobile($year)->row();
		$data['anggota']['civitas_web']				= $this->HomeModel->getTotalCivitasWeb($year)->row();
		$data['anggota']['umum_webmobile']			= $this->HomeModel->getTotalUmumWebMobile($year)->row();
		$data['anggota']['umum_web']				= $this->HomeModel->getTotalUmumWeb($year)->row();
		$data['anggota']['civitas_all_webmobile']	= $this->HomeModel->getTotalCivitasAllWebMobile()->row();
		$data['anggota']['civitas_all_web']			= $this->HomeModel->getTotalCivitasAllWeb()->row();
		$data['anggota']['umum_all_webmobile']		= $this->HomeModel->getTotalUmumAllWebMobile()->row();
		$data['anggota']['umum_all_web']			= $this->HomeModel->getTotalUmumAllWeb()->row();
		// $data['pengunjung']['online_user'] 		= array_sum($end['users']);
		// $data['pengunjung']['online_pageview'] 	= array_sum($end['pageviews']);
		
		$data['sirkulasi']['peminjaman']		= $this->HomeModel->getTotalPeminjaman($year)->row();
		$data['sirkulasi']['pengembalian'] 		= $this->HomeModel->getTotalPengembalian($year)->row(); 
		$data['sirkulasi']['ruangan'] 			= $this->HomeModel->getTotalRoom($year)->row();
		$data['sirkulasi']['bds'] 				= $this->HomeModel->getTotalBds($year)->row();
		$data['sirkulasi']['usulan'] 			= $this->HomeModel->getTotalUsulan($year)->row();
		$data['sirkulasi']['sbkp'] 				= $this->HomeModel->getTotalSBKP($year)->row();
		$data['sirkulasi']['ebook'] 			= $this->HomeModel->getTotalAccessEbook($year)->row();
		$data['sirkulasi']['karyailmiah'] 		= $this->HomeModel->getTotalAccessKaryaIlmiah($year)->row();
		
		
		
		//statistk 
		// $year 		= date('Y');
		$lastyear 	= $year-1;
		$data['year'] = $year;

		// print_r($year);
	
		$judul 				= $this->HomeModel->getTotalJudulFisikPerBulan($year,$digital)->result(); 
		$koleksi 			= $this->HomeModel->getTotalKoleksiFisikPerBulan($year,$digital)->result();  
		$judul_digital 		= $this->HomeModel->getTotalJudulDigitalPerBulan($year,$digital)->result(); 
		$koleksi_digital	= $this->HomeModel->getTotalKoleksiDigitalPerBulan($year,$digital)->result(); 
		$civitas_webmobile	= $this->HomeModel->getTotalCivitasWebMobilePerBulan($year)->result();
		$civitas_web		= $this->HomeModel->getTotalCivitasWebPerBulan($year)->result();
		$umum_webmobile		= $this->HomeModel->getTotalUmumWebMobilePerBulan($year)->result();
		$umum_web			= $this->HomeModel->getTotalUmumWebPerBulan($year)->result();
		$pengunjung 		= $this->HomeModel->getTotalPengunjungFisikPerBulan($year)->result();   
		$peminjaman 		= $this->HomeModel->getTotalPeminjamanPerBulan($year)->result(); 
		$pengembalian		= $this->HomeModel->getTotalPengembalianPerBulan($year)->result();
		$ruangan				= $this->HomeModel->getTotalRoomPerBulan($year)->result();
		$bds				= $this->HomeModel->getTotalBdsPerBulan($year)->result();
		$usulan				= $this->HomeModel->getTotalUsulanPerBulan($year)->result();
		$sbkp				= $this->HomeModel->getTotalSBKPPerBulan($year)->result();
		$journalvisitor		= $this->HomeModel->getTotalJournalVisitorPerBulan($year)->result();
		
		$judullast 			= $this->HomeModel->getTotalJudulFisikPerBulan($lastyear,$digital)->result(); 
		$koleksilast 		= $this->HomeModel->getTotalKoleksiFisikPerBulan($lastyear,$digital)->result();  
		$judul_digital_last 	= $this->HomeModel->getTotalJudulDigitalPerBulan($lastyear,$digital)->result(); 
		$koleksi_digital_last	= $this->HomeModel->getTotalKoleksiDigitalPerBulan($lastyear,$digital)->result(); 
		$civitas_webmobilelast		= $this->HomeModel->getTotalCivitasWebMobilePerBulan($lastyear)->result();
		$civitas_weblast		= $this->HomeModel->getTotalCivitasWebPerBulan($lastyear)->result();
		$umum_webmobilelast			= $this->HomeModel->getTotalUmumWebMobilePerBulan($lastyear)->result();
		$umum_weblast			= $this->HomeModel->getTotalUmumWebPerBulan($lastyear)->result();
		$pengunjunglast 	= $this->HomeModel->getTotalPengunjungFisikPerBulan($lastyear)->result();  
		$peminjamanlast 	= $this->HomeModel->getTotalPeminjamanPerBulan($lastyear)->result(); 
		$pengembalianlast	= $this->HomeModel->getTotalPengembalianPerBulan($lastyear)->result();
		$ruanganlast		= $this->HomeModel->getTotalRoomPerBulan($lastyear)->result();
		$bdslast			= $this->HomeModel->getTotalBdsPerBulan($lastyear)->result();
		$usulanlast			= $this->HomeModel->getTotalUsulanPerBulan($lastyear)->result();
		$sbkplast			= $this->HomeModel->getTotalSBKPPerBulan($lastyear)->result();
		$journalvisitorlast	= $this->HomeModel->getTotalJournalVisitorPerBulan($lastyear)->result(); 

		$online['lastyear'] 			= $this->HomeModel->getTotalOnlineVisitorPerBulan($lastyear)->row();
		$online['year'] 					= $this->HomeModel->getTotalOnlineVisitorPerBulan($year)->row();
		$pageviews['lastyear'] 		= $this->HomeModel->getTotalPageViewsPerBulan($lastyear)->row();
		$pageviews['year'] 				= $this->HomeModel->getTotalPageViewsPerBulan($year)->row();

		$onlineeproc['lastyear'] 			= $this->HomeModel->getTotalOnlineVisitorPerBulanEproc($lastyear)->row();
		$onlineeproc['year'] 					= $this->HomeModel->getTotalOnlineVisitorPerBulanEproc($year)->row();
		$pageviewseproc['lastyear'] 		= $this->HomeModel->getTotalPageViewsPerBulanEproc($lastyear)->row();
		$pageviewseproc['year'] 				= $this->HomeModel->getTotalPageViewsPerBulanEproc($year)->row(); 

		
		$ebook['lastyear'] 		= $this->HomeModel->getTotalAccessEbookPerBulan($lastyear)->row();
		$ebook['year'] 				= $this->HomeModel->getTotalAccessEbookPerBulan($year)->row(); 

		
		$karyailmiah['lastyear'] 		= $this->HomeModel->getTotalAccessKaryaIlmiahPerBulan($lastyear)->row();
		$karyailmiah['year'] 				= $this->HomeModel->getTotalAccessKaryaIlmiahPerBulan($year)->row(); 

		$data['online']['lastyear'][] =  $online['lastyear']->januari;
		$data['online']['lastyear'][] =  $online['lastyear']->februari;
		$data['online']['lastyear'][] =  $online['lastyear']->maret;
		$data['online']['lastyear'][] =  $online['lastyear']->april;
		$data['online']['lastyear'][] =  $online['lastyear']->mei;
		$data['online']['lastyear'][] =  $online['lastyear']->juni;
		$data['online']['lastyear'][] =  $online['lastyear']->juli;
		$data['online']['lastyear'][] =  $online['lastyear']->agustus;
		$data['online']['lastyear'][] =  $online['lastyear']->september;
		$data['online']['lastyear'][] =  $online['lastyear']->oktober;
		$data['online']['lastyear'][] =  $online['lastyear']->november;
		$data['online']['lastyear'][] =  $online['lastyear']->desember;
		
		$data['online']['year'][] =  $online['year']->januari;
		$data['online']['year'][] =  $online['year']->februari;
		$data['online']['year'][] =  $online['year']->maret;
		$data['online']['year'][] =  $online['year']->april;
		$data['online']['year'][] =  $online['year']->mei;
		$data['online']['year'][] =  $online['year']->juni;
		$data['online']['year'][] =  $online['year']->juli;
		$data['online']['year'][] =  $online['year']->agustus;
		$data['online']['year'][] =  $online['year']->september;
		$data['online']['year'][] =  $online['year']->oktober;
		$data['online']['year'][] =  $online['year']->november;
		$data['online']['year'][] =  $online['year']->desember;

		$data['pageviews']['lastyear'][] =  $pageviews['lastyear']->januari;
		$data['pageviews']['lastyear'][] =  $pageviews['lastyear']->februari;
		$data['pageviews']['lastyear'][] =  $pageviews['lastyear']->maret;
		$data['pageviews']['lastyear'][] =  $pageviews['lastyear']->april;
		$data['pageviews']['lastyear'][] =  $pageviews['lastyear']->mei;
		$data['pageviews']['lastyear'][] =  $pageviews['lastyear']->juni;
		$data['pageviews']['lastyear'][] =  $pageviews['lastyear']->juli;
		$data['pageviews']['lastyear'][] =  $pageviews['lastyear']->agustus;
		$data['pageviews']['lastyear'][] =  $pageviews['lastyear']->september;
		$data['pageviews']['lastyear'][] =  $pageviews['lastyear']->oktober;
		$data['pageviews']['lastyear'][] =  $pageviews['lastyear']->november;
		$data['pageviews']['lastyear'][] =  $pageviews['lastyear']->desember;
		
		$data['pageviews']['year'][] =  $pageviews['year']->januari;
		$data['pageviews']['year'][] =  $pageviews['year']->februari;
		$data['pageviews']['year'][] =  $pageviews['year']->maret;
		$data['pageviews']['year'][] =  $pageviews['year']->april;
		$data['pageviews']['year'][] =  $pageviews['year']->mei;
		$data['pageviews']['year'][] =  $pageviews['year']->juni;
		$data['pageviews']['year'][] =  $pageviews['year']->juli;
		$data['pageviews']['year'][] =  $pageviews['year']->agustus;
		$data['pageviews']['year'][] =  $pageviews['year']->september;
		$data['pageviews']['year'][] =  $pageviews['year']->oktober;
		$data['pageviews']['year'][] =  $pageviews['year']->november;
		$data['pageviews']['year'][] =  $pageviews['year']->desember; 

		

		$data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear']->januari;
		$data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear']->februari;
		$data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear']->maret;
		$data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear']->april;
		$data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear']->mei;
		$data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear']->juni;
		$data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear']->juli;
		$data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear']->agustus;
		$data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear']->september;
		$data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear']->oktober;
		$data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear']->november;
		$data['onlineeproc']['lastyear'][] =  $onlineeproc['lastyear']->desember;
		
		$data['onlineeproc']['year'][] =  $onlineeproc['year']->januari;
		$data['onlineeproc']['year'][] =  $onlineeproc['year']->februari;
		$data['onlineeproc']['year'][] =  $onlineeproc['year']->maret;
		$data['onlineeproc']['year'][] =  $onlineeproc['year']->april;
		$data['onlineeproc']['year'][] =  $onlineeproc['year']->mei;
		$data['onlineeproc']['year'][] =  $onlineeproc['year']->juni;
		$data['onlineeproc']['year'][] =  $onlineeproc['year']->juli;
		$data['onlineeproc']['year'][] =  $onlineeproc['year']->agustus;
		$data['onlineeproc']['year'][] =  $onlineeproc['year']->september;
		$data['onlineeproc']['year'][] =  $onlineeproc['year']->oktober;
		$data['onlineeproc']['year'][] =  $onlineeproc['year']->november;
		$data['onlineeproc']['year'][] =  $onlineeproc['year']->desember;

		$data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear']->januari;
		$data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear']->februari;
		$data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear']->maret;
		$data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear']->april;
		$data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear']->mei;
		$data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear']->juni;
		$data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear']->juli;
		$data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear']->agustus;
		$data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear']->september;
		$data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear']->oktober;
		$data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear']->november;
		$data['pageviewseproc']['lastyear'][] =  $pageviewseproc['lastyear']->desember;
		
		$data['pageviewseproc']['year'][] =  $pageviewseproc['year']->januari;
		$data['pageviewseproc']['year'][] =  $pageviewseproc['year']->februari;
		$data['pageviewseproc']['year'][] =  $pageviewseproc['year']->maret;
		$data['pageviewseproc']['year'][] =  $pageviewseproc['year']->april;
		$data['pageviewseproc']['year'][] =  $pageviewseproc['year']->mei;
		$data['pageviewseproc']['year'][] =  $pageviewseproc['year']->juni;
		$data['pageviewseproc']['year'][] =  $pageviewseproc['year']->juli;
		$data['pageviewseproc']['year'][] =  $pageviewseproc['year']->agustus;
		$data['pageviewseproc']['year'][] =  $pageviewseproc['year']->september;
		$data['pageviewseproc']['year'][] =  $pageviewseproc['year']->oktober;
		$data['pageviewseproc']['year'][] =  $pageviewseproc['year']->november;
		$data['pageviewseproc']['year'][] =  $pageviewseproc['year']->desember; 


		

		$data['ebook']['lastyear'][] =  $ebook['lastyear']->januari;
		$data['ebook']['lastyear'][] =  $ebook['lastyear']->februari;
		$data['ebook']['lastyear'][] =  $ebook['lastyear']->maret;
		$data['ebook']['lastyear'][] =  $ebook['lastyear']->april;
		$data['ebook']['lastyear'][] =  $ebook['lastyear']->mei;
		$data['ebook']['lastyear'][] =  $ebook['lastyear']->juni;
		$data['ebook']['lastyear'][] =  $ebook['lastyear']->juli;
		$data['ebook']['lastyear'][] =  $ebook['lastyear']->agustus;
		$data['ebook']['lastyear'][] =  $ebook['lastyear']->september;
		$data['ebook']['lastyear'][] =  $ebook['lastyear']->oktober;
		$data['ebook']['lastyear'][] =  $ebook['lastyear']->november;
		$data['ebook']['lastyear'][] =  $ebook['lastyear']->desember;
		
		$data['ebook']['year'][] =  $ebook['year']->januari;
		$data['ebook']['year'][] =  $ebook['year']->februari;
		$data['ebook']['year'][] =  $ebook['year']->maret;
		$data['ebook']['year'][] =  $ebook['year']->april;
		$data['ebook']['year'][] =  $ebook['year']->mei;
		$data['ebook']['year'][] =  $ebook['year']->juni;
		$data['ebook']['year'][] =  $ebook['year']->juli;
		$data['ebook']['year'][] =  $ebook['year']->agustus;
		$data['ebook']['year'][] =  $ebook['year']->september;
		$data['ebook']['year'][] =  $ebook['year']->oktober;
		$data['ebook']['year'][] =  $ebook['year']->november;
		$data['ebook']['year'][] =  $ebook['year']->desember;  

		$data['karyailmiah']['lastyear'][] =  $karyailmiah['lastyear']->januari;
		$data['karyailmiah']['lastyear'][] =  $karyailmiah['lastyear']->februari;
		$data['karyailmiah']['lastyear'][] =  $karyailmiah['lastyear']->maret;
		$data['karyailmiah']['lastyear'][] =  $karyailmiah['lastyear']->april;
		$data['karyailmiah']['lastyear'][] =  $karyailmiah['lastyear']->mei;
		$data['karyailmiah']['lastyear'][] =  $karyailmiah['lastyear']->juni;
		$data['karyailmiah']['lastyear'][] =  $karyailmiah['lastyear']->juli;
		$data['karyailmiah']['lastyear'][] =  $karyailmiah['lastyear']->agustus;
		$data['karyailmiah']['lastyear'][] =  $karyailmiah['lastyear']->september;
		$data['karyailmiah']['lastyear'][] =  $karyailmiah['lastyear']->oktober;
		$data['karyailmiah']['lastyear'][] =  $karyailmiah['lastyear']->november;
		$data['karyailmiah']['lastyear'][] =  $karyailmiah['lastyear']->desember;
		
		$data['karyailmiah']['year'][] =  $karyailmiah['year']->januari;
		$data['karyailmiah']['year'][] =  $karyailmiah['year']->februari;
		$data['karyailmiah']['year'][] =  $karyailmiah['year']->maret;
		$data['karyailmiah']['year'][] =  $karyailmiah['year']->april;
		$data['karyailmiah']['year'][] =  $karyailmiah['year']->mei;
		$data['karyailmiah']['year'][] =  $karyailmiah['year']->juni;
		$data['karyailmiah']['year'][] =  $karyailmiah['year']->juli;
		$data['karyailmiah']['year'][] =  $karyailmiah['year']->agustus;
		$data['karyailmiah']['year'][] =  $karyailmiah['year']->september;
		$data['karyailmiah']['year'][] =  $karyailmiah['year']->oktober;
		$data['karyailmiah']['year'][] =  $karyailmiah['year']->november;
		$data['karyailmiah']['year'][] =  $karyailmiah['year']->desember; 
		 
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
			$data['grafik']['digital']['year'][] 			= (empty($digitals[$key]->total)?0:$digitals[$key]->total); 
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
	 
		$rasio = $this->HomeModel->getRasioMKperProdi2()->result(); 
		$total = 0;
		foreach($rasio as $row){ 
			$total = $total + $row->judul; 
		} 

		// print_r($data['grafik']['judul_digital']);
		
		foreach($rasio as $row){ 
				if($row->mk==0) $rat = 0;
				else $rat = round($row->mkadabuku / $row->mk *100,2);
				$data['rasio']['prodi'][] = "'".$row->nama_prodi." (".$row->judul.")'";
				$data['rasio']['rasio'][] = (isset($row->judul)?$rat:0);
				$data['rasio']['total'][] = (isset($row->judul)?$row->judul:0);
				$data['rasio']['mkadabuku'][] = (isset($row->mkadabuku)?$row->mkadabuku:0);
				$data['rasio']['mk'][] = (isset($row->mk)?$row->mk:0);
				$total = $total + $row->judul;
				
		} 

		// echo array_sum($data['rasio']['rasio']);
		$data['rasio']['totals'] = round(array_sum($data['rasio']['rasio'])/count($rasio),2);
		  
		
		//list file  
		$file = $this->HomeModel->getFileTotal()->result();
		
		foreach($file as $row){
			$data['file']['name'][] = "'".$row->nama_file."'";
			$data['file']['total'][] = $row->total_file;
			
		} 
		//end list file
		
		//eproceeding 
		$edition		 		= $this->MonitoringEproceedingModel->getLastEprocEdition()->row(); 
		$data['eproceeding']['name']		= array('0' => '"TA/PA/Thesis Masuk"',
													'1' => '"Jurnal Masuk"',
													'2' => '"On Draft"',
													'3' => '"Need Revision"',
													'4' => '"Ready for Review"',
													'5' => '"Archieved"',
													'6' => '"Not Feasible"',
													'7' => '"Publish Eksternal"',
													'8' => '"Publish Tel-U Proceeding"',
													'9' => '"Metadata Approve for Catalog"');
		$data['eproceeding']['edition'] 	= $edition->nama;
		$data['eproceeding']['total'][]		= $this->MonitoringEproceedingModel->totaltamasukbykodejur('',$edition->datestart,$edition->datefinish)->row()->total; 
		$data['eproceeding']['total'][]		= $this->MonitoringEproceedingModel->totaljurnalmasukbykodejur('',$edition->datestart,$edition->datefinish)->row()->total;
		$data['eproceeding']['total'][]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate('','22',$edition->datestart,$edition->datefinish)->row()->total;   
		$data['eproceeding']['total'][]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate('','2',$edition->datestart,$edition->datefinish)->row()->total; 
		$data['eproceeding']['total'][]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate('','1',$edition->datestart,$edition->datefinish)->row()->total; 
		$data['eproceeding']['total'][]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate('','5',$edition->datestart,$edition->datefinish)->row()->total; 
		$data['eproceeding']['total'][]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate('','3',$edition->datestart,$edition->datefinish)->row()->total; 
		$data['eproceeding']['total'][]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate('','52',$edition->datestart,$edition->datefinish)->row()->total; 
		$data['eproceeding']['total'][]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate('','53',$edition->datestart,$edition->datefinish)->row()->total;  
		$data['eproceeding']['total'][]		= $this->MonitoringEproceedingModel->totaldocbykodejurandstate('','91',$edition->datestart,$edition->datefinish)->row()->total;  		
		//end eproceeding
		
		$data['menu'] = "dashboard"; 
		$this->load->view('theme',$data);
	}
	
	
	
}
