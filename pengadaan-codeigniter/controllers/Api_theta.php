<?php

require APPPATH . '/libraries/REST_Controller.php';
 
class Api_theta extends REST_Controller {
 
    function __construct($config = 'rest') {
        parent::__construct($config);
		$this->load->model("ApiThetaModel","atm");
		$this->load->model("RfidModel","rm");
    }
 
   
     function index_get() {
		
		$username 	= $this->input->get('username'); 
		$row 		= $this->atm->amnesty()->result();
		$amnesty	= array();
		foreach($row as $rw){
			$amnesty[] = strtolower($rw->master_data_user);
		}
		
		$row 		= $this->atm->revision()->result();
		$revision	= array();
		foreach($row as $rw){
			$revision[] = strtolower($rw->master_data_user);
		}
		
		$status	= "success";
		$dts 	= $this->rm->GetRfid('',$username)->row();
		$member = $this->rm->GetMember($username)->row();
		if (!$member){
			if($dts){
				if ($dts->C_KODE_JENIS_USER=='mahasiswa'){
					$user = $this->rm->GetUser('mahasiswa',$username)->row();
					if ($user) {
						$master_data_course = $user->c_kode_prodi;
						$member_type_api = $this->rm->GetMemberTypeApi('MAHASISWA',$user->c_kode_prodi)->row(); 
					}
					else $status = "failed";
				}
				else {
					$user = $this->rm->GetUser('pegawai',$dts->c_username)->row();
					if ($user) {
						$master_data_course = NULL;
						$member_type_api = $this->rm->GetMemberTypeApi('PEGAWAI',$user->c_kode_status_pegawai)->row();
					}
					else $status = "failed";
				}
				
				if ($status=="success"){
					$data = array( 
						"member_type_id" 		=> $member_type_api->member_type_id,
						"member_class_id" 		=> $member_type_api->member_class_id,
						"master_data_user" 		=> $dts->c_username,
						"master_data_email" 	=> $dts->email,
						"master_data_course" 	=> $master_data_course,
						"master_data_fullname" 	=> $dts->fullname,
						"status" 				=> "1",
						"created_at" 			=> date("Y-m-d")
					);
				
					$item['username_id'] = $this->rm->add($data); 
				}  
			}
		}  
		
		
		$status = $this->atm->CheckStatus($username)->row();
		$dt['buku'] = 0;
		$dt['dokumen'] = 0;
		$dt['peminjaman'] = 0;
		$dt['lunas'] = 0;
		if ($status){
			
			if ($status->buku==1) $dt['buku'] = 1;
			if ($status->dokumen==1) $dt['dokumen'] = 1;
			if ($status->peminjaman==1) $dt['peminjaman'] = 1;
			if ($status->lunas==1) $dt['lunas'] = 1;
			
			
			if (in_array(strtolower($username),$amnesty)) $dt['lunas'] = 1;
			
			if($dt['dokumen']==0){
				$dt['revisi'] = 0;
			}
			else {
				if (in_array(strtolower($username),$revision)) $dt['revisi'] = 0;
				else $dt['revisi'] = 1;
			}
				
			
			$this->response(array('status' => $dt), 200);    
		} else  $this->response(array('status' => 'failed'), 200);  
  }
}

?>