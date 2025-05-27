<?php

require APPPATH . '/libraries/REST_Controller.php';
 
class Api_loker extends REST_Controller {
 
    function __construct($config = 'rest') {
        parent::__construct($config); 
		$this->load->model('Usermodel', 'um', TRUE);
		$this->load->model('Lokermodel', 'lm', TRUE);
    }
  
   
   function index_post() { 
		$public_key = $this->input->post('public_key');
		$rfid = $this->input->post('rfid');
		 
		  
		if($public_key!='YPT14F28C25E25DF3E5A429CDDEFC638') $this->response(array('status' => "false",'message'=>'Public Key is wrong'), 200); 
		 
		if($rfid=='10496DD9') {
      $tmp['nomor'] 	 		= '0'; 
      $tmp['status']  		= 'wildcard';
      
      $this->response(array('status' => "true",'message'=>$tmp), 200); 
    }
		
		$user = $this->lm->checkRfid($rfid)->row(); 
		if(!$user) $this->response(array('status' => "false",'message'=>'RFID not registered'), 200); 
		
		$loker = $this->lm->checkLoker($user->id)->row();
		if(!$loker) {
			$nmr = $this->lm->getnmrloker()->result();
			
			if(!$nmr) $this->response(array('status' => "false",'message'=>'Loker is full'), 200); 
				 
			$arr = array();
			foreach($nmr as $row){
				$arr[] = $row->kunci_nomor;
			}

	
				
			$random_keys=array_rand($arr,1); 
			$dates = date('Y-m-d H:i:s');
			$attendance = array( 
				"history_date" => date('Y-m-d'),
				"history_id_member" => $user->id,
				"history_nomor" => $arr[$random_keys],
				"history_masuk" => $dates
			);
			$this->lm->add_loker($attendance);
			
			$kunci = array( 
				"kunci_waktu" => $dates,
				"kunci_id_member" => $user->id
			);
			$this->lm->edit_kunci($arr[$random_keys],$kunci); 
			
			$tmp['nomor'] 			= strval($arr[$random_keys]);
			$tmp['tanggal_masuk'] 	= $dates; 
			$tmp['tanggal_keluar']	= '-';
			$tmp['nama'] 			= $user->master_data_fullname;
			$tmp['nim_nip'] 		= $user->master_data_number;
			$tmp['status']  		= 'checkin';
			
			$this->response(array('status' => "true",'message'=>$tmp), 200); 
		}
		else {
			$lastdate   = date('Y-m-d H:i:s');
			$awal  		= date_create($loker->history_masuk);
			$akhir 		= date_create($lastdate); 
			$diff  		= date_diff($awal,$akhir);   
			 
			// if ($diff->d=='0' && $diff->h=='0' && $diff->i<'15' && $diff->invert=='0'){ 
			
				// $tmp['nomor'] 			= $loker->history_nomor;
				// $tmp['tanggal_masuk'] 	= $loker->history_masuk;
				// $tmp['tanggal_keluar']	= '-';
				// $tmp['nama'] 			= $user->master_data_fullname;
				// $tmp['nim_nip'] 		= $user->master_data_number;
				// $tmp['status']  		= 'checkin';
				
				// $this->response(array('status' => "true",'message'=>$tmp), 200); 
			// }
			// else { 
				$attendance = array(  
					"history_keluar" => $lastdate
				);
				$this->lm->edit_loker($loker->history_id,$attendance);
			
				$kunci = array( 
					"kunci_waktu" 	=> null,
					"kunci_id_member" => null
				);
				$this->lm->edit_kunci($loker->history_nomor,$kunci);  
			
				$tmp['nomor'] 	 		= $loker->history_nomor;
				$tmp['tanggal_masuk'] 	= $loker->history_masuk;
				$tmp['tanggal_keluar']	= $lastdate;
				$tmp['nama'] 			= $user->master_data_fullname;
				$tmp['nim_nip'] 		= $user->master_data_number;
				$tmp['status']  		= 'checkout';
				
				$this->response(array('status' => "true",'message'=>$tmp), 200); 
				
			// }
		}  
	}
	
	  
}

?>