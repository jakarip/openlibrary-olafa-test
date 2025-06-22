<?php

require APPPATH . '/libraries/REST_Controller.php';
 
class Api_theta_lecturer extends REST_Controller {
 
    function __construct($config = 'rest') {
       parent::__construct($config);
			$this->load->model("ApiThetaModel","atm");
			$this->load->model("RfidModel","rm");
    }
 
   
    function index_get() {
		
		$student 		= $this->input->get('student'); 
		$lecturer1 	= $this->input->get('lecturer1'); 
		$lecturer2 	= $this->input->get('lecturer2');  

		if(!$student or !$lecturer1) $this->response(array('status' => 'false','message'=>'Paramater Kosong'), 401);  

		$dts = $this->atm->CheckDataMappingLecturer($student)->row();
		
		if($dts) { 
			$data['C_KODE_DOSEN_PEMBIMBING_SATU'] = $lecturer1;
			$data['C_KODE_DOSEN_PEMBIMBING_DUA'] 	= $lecturer2;
			$data['U_USER'] 											= 'API_THETA_LECTURER';
			$data['U_DATE'] 											= date('Y-m-d H:i:s'); 
			if($this->atm->editStudentLecture($student,$data))	$this->response(array('status' => 'true'), 200); 
			else $this->response(array('status' => 'false'), 200); 
		}
		else { 
			$data['C_NPM'] 												= $student;
			$data['C_KODE_DOSEN_PEMBIMBING_SATU'] = $lecturer1;
			$data['C_KODE_DOSEN_PEMBIMBING_DUA'] 	= $lecturer2;
			$data['C_USER'] 											= 'API_THETA_LECTURER';
			$data['C_DATE'] 											= date('Y-m-d H:i:s'); 
			if($this->atm->addStudentLecture($data)) $this->response(array('status' => 'true'), 200); 
			else $this->response(array('status' => 'false'), 200); 
		}  
	}
}

?>