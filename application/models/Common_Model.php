<?php
class Common_Model extends CI_Model {
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	function form_jenis_anggota()
	{
		$array[''] 					= 'Pilih Jenis Anggota / Choose member type';
		$array['umum'] 					= 'Umum';
		$array['alumni'] 				= 'Alumni Telkom University'; 
		$array['lemdikti'] 			= 'Lemdikti YPT'; 
		$array['ptasuh'] 				= 'Perguruan Tinggi Asuh';
		$array['internasional'] 					= 'International Student';
		
		return $array;
	}
	
	function form_member_type()
	{ 
		$array['alumni'] 			= '20'; 
		$array['lemdikti'] 		= '21'; 
		$array['ptasuh'] 			= '22';
		$array['umum'] 				= '23';
		$array['internasional'] 				= '24';
		
		return $array;
	}
	
	function form_ptasuh()
	{
		$array['stkipsetiabudhi'] 	= 'STKIP Setia Budhi Rangkasbitung'; 
		$array['stt-wastukancana'] 	= 'STT Wastukancana Purwakarta'; 
		$array['unismabekasi'] 			= 'Unisma Bekasi'; 
		$array['unibi'] 						= 'Universitas Informatika dan Bisnis Indonesia';
		$array['uicm'] 							= 'Universitas Insan Cendekia Mandiri';
		$array['umu']								= 'Universitas Muhammadiyah Cirebon';
		$array['unucirebon'] 				= 'Universitas Nadhlatul Ulama Cirebon'; 
		$array['unsub'] 						= 'Universitas Subang'; 
		return $array;
	}
	
	function form_ptasuh_email()
	{
		$array['stkipsetiabudhi'] 	= 'stkipsetiabudhi.ac.id'; 
		$array['stt-wastukancana'] 	= 'stt-wastukancana.ac.id'; 
		$array['unismabekasi'] 			= 'unismabekasi.ac.id'; 
		$array['unibi'] 						= 'unibi.ac.id';
		$array['uicm'] 							= 'uicm.ac.id';
		$array['umu']								= 'umu.ac.id';
		$array['unucirebon'] 				= 'unucirebon.ac.id'; 
		$array['unsub'] 						= 'unsub.ac.id'; 
		return $array;
	}
	
	function form_lemdikti()
	{
		$array['itts']		= 'Institut Teknologi Telkom Surabaya';
		$array['ittp'] 		= 'Institut Teknologi Telkom Purwokerto'; 
		$array['ittj'] 		= 'Institut Teknologi Telkom Jakarta'; 
		return $array;
	}
	
	function form_lemdikti_email()
	{
		$array['itts']		= 'ittelkom-sby.ac.id';
		$array['ittp'] 		= 'ittelkom-pwt.ac.id'; 
		$array['ittj'] 		= 'ittelkom-jkt.ac.id'; 
		return $array;
	}
	
	function form_pin()
	{
		$array['offline'] 	        = 'Cash and Carry (Offline)';
		$array['online'] 	        = 'BANK (Online)';
		$array['affiliate'] 	    = 'Affiliate';
		$array['affiliate_online'] 	= 'Affiliate Online';
		
		return $array;
	}
	
	function form_type_track()
	{
		$array['rapor'] 	= 'Rapor';
		$array['seleksi'] 	= 'Seleksi';
        $array['utbk'] 	    = 'UTBK';
		
		return $array;
    }
	
	function form_pil_prodi()
	{
		$array['1'] 	= '1';
		$array['2'] 	= '2';
		$array['3'] 	= '3';
		$array['4'] 	= '4';
		$array['5'] 	= '5';
		$array['6'] 	= '6';
		$array['7'] 	= '7';
		$array['8'] 	= '8';
		$array['9'] 	= '9';
		
		return $array;
	}
	
	function form_school_status()
	{
		$array['Negeri'] 	= 'Negeri';
		$array['Swasta'] 	= 'Swasta'; 
		
		return $array;
	}
	
	function form_step($jalur)
	{
		if($jalur == 'rapor')
        {
            $array['1'] 	= 'Input Biodata';
            $array['2'] 	= 'Input Nilai Rapor';
            $array['3'] 	= 'Upload Rapor';
            $array['4'] 	= 'Input dan Upload Nilai UN';
            $array['5'] 	= 'Input Prestasi';
            $array['6'] 	= 'Pilih Program Studi';
            $array['7'] 	= 'Cetak Berita Acara';
        }
        else if($jalur == 'utbk')
        {
            $array['1'] 	= 'Input Biodata';
            $array['2'] 	= 'Input dan Upload Nilai UTBK SBMPTN';
            $array['3'] 	= 'Input Prestasi';
            $array['4'] 	= 'Pilih Program Studi';
            $array['5'] 	= 'Cetak Kartu Peserta';
        }
        else
        {
            $array['1'] 	= 'Input Biodata';
            $array['2'] 	= 'Pilih Lokasi Seleksi';
            $array['3'] 	= 'Pilih Program Studi';
            $array['4'] 	= 'Cetak Berita Acara';
        }


		return $array;
	}

    function form_step_url($jalur)
    {
        if($jalur == 'rapor')
        {
            $array['1'] 	= 'biodata';
            $array['2'] 	= 'input_rapor';
            $array['3'] 	= 'upload_rapor';
            $array['4'] 	= 'input_un';
            $array['5'] 	= 'input_prestasi';
            $array['6'] 	= 'choose_prodi';
            $array['7'] 	= 'print_report';
        }
        else if($jalur == 'utbk')
        {
            $array['1'] 	= 'biodata';
            $array['2'] 	= 'input_utbk';
            $array['3'] 	= 'input_prestasi_utbk';
            $array['4'] 	= 'choose_prodi';
            $array['5'] 	= 'print_report';
        }
        else
        {
            $array['1'] 	= 'biodata';
            $array['2'] 	= 'choose_selection_place';
            $array['3'] 	= 'choose_prodi';
            $array['4'] 	= 'print_report';
        }


        return $array;
    }

	function get_form_step($mode = '', $jalur, $id)
    {
        if(empty($mode))
            $array = $this->form_step($jalur);
        else
            $array = $this->form_step_url($jalur);

        if(isset($array[$id]) && !empty($id)) return $array[$id];
        else return '';
    }
	
	function form_departement() 
	{
		$array['IPA'] 		    = 'IPA';
		$array['IPS'] 		    = 'IPS';
        $array['BAHASA'] 		= 'BAHASA';
        $array['AGAMA'] 		= 'AGAMA';
        $array['TEKNIK'] 		= 'TEKNIK';
        $array['NON TEKNIK'] 	= 'NON TEKNIK';

		return $array;
	}
	
	function get($fn, $id)
	{
		$array = $this->$fn();
		
		if(isset($array[$id]) && !empty($id)) return $array[$id];
		else return '';
	}
	
	function add_logs($mod, $act, $id)
	{
		$ci = get_instance();
		$ci->load->helper('ycode');
		
		$user = y_info_login();
		
		$this->db->query("INSERT INTO logs VALUES ('', NOW(), '$mod', '$act', '$id', '".$user->user_id."')");
    } 
    
    function form_promotion_setting()
	{
        $array['token'] 	= 'Sudah Bayar Token';
		$array['up3'] 	    = 'Sudah Bayar UP3'; 
		
		return $array;
	}
    
    function form_transfer()
	{
        $array['Teller atau ATM'] 	= 'Teller atau ATM';
		$array['M-Banking'] 	    = 'M-Banking'; 
		$array['Internet Banking']  = 'Internet Banking'; 
		
		return $array;
	}
    
    function form_type_km()
	{
        $array['register'] 	        = 'Register Akun';
		$array['request_token'] 	= 'Request Token'; 
		$array['token']             = 'Token';  
		$array['up3']               = 'UP3 & Beasiswa'; 
		
		return $array;
	}
	
	function sdgs()
	{
		$array['1'] 	= 'Pilar pembangunan sosial - Menghapus kemiskinan';  
		$array['2'] 	= 'Pilar pembangunan sosial - Mengakhiri kelaparan';  
		$array['3'] 	= 'Pilar pembangunan sosial - Kesehatan yang baik dan kesejahteraan';  
		$array['4'] 	= 'Pilar pembangunan sosial - Pendidikan Bermutu';  
		$array['5'] 	= 'Pilar pembangunan sosial - Kesetaraan gender';  
		$array['7'] 	= 'Pilar pembangunan ekonomi - Energi bersih dan terjangkau';  
		$array['8'] 	= 'Pilar pembangunan ekonomi - Pekerjaan layak dan pertumbuhan ekonomi';  
		$array['9'] 	= 'Pilar pembangunan ekonomi - Infrastruktur, industri, dan inovasi';  
		$array['10'] 	= 'Pilar pembangunan ekonomi - Mengurangi ketimpangan';  
		$array['17'] 	= 'Pilar pembangunan ekonomi - Kemitraan untuk mencapai tujuan';  
		$array['6'] 	= 'Pilar pembangunan lingkungan - Akses air bersih dan sanitasi';  
		$array['11'] 	= 'Pilar pembangunan lingkungan - Kota dan komunitas yang berkelanjutan';  
		$array['12'] 	= 'Pilar pembangunan lingkungan - Konsumsi dan produksi yang bertanggungjawab';  
		$array['13'] 	= 'Pilar pembangunan lingkungan - Penanganan perubahan iklim';  
		$array['14'] 	= 'Pilar pembangunan lingkungan - Menjaga ekosistem laut';  
		$array['15'] 	= 'Pilar pembangunan lingkungan - Menjaga ekosistem darat';  
		$array['16'] 	= 'Pilar pembangunan  hukum dan tata kelola - Perdamaian, keadilan, dan kelembagaan yang kuat';  
		return $array;
	}
}
?>