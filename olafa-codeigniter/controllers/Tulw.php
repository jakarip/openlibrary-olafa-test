<?php

 
class Tulw extends CI_Controller {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct(); 
		
		$this->load->library('mpdf/mpdf'); 
		$this->load->model('TulwModel'); 
		//$this->load->library('paging');
		//$this->load->library('pagination');
		//$this->load->library('mpdf/mpdf');
		ini_set('MAX_EXECUTION_TIME', -1);
		ini_set('memory_limit','-1');
		
		
    }
	 function index() {  
		
		$this->load->view('tulw/index');
    }  
	
	function seemore() {  
		
		$this->load->view('tulw/single');
    } 
	
	function register() {  
		$data['event'] = $this->TulwModel->getEvent()->result();
		$this->load->view('tulw/reg',$data);
    } 
	
	function reg_process() { 
		$event	= $this->input->post('event');
		if (!empty($event)) {
			$name	= $this->StrToDb($this->input->post('name'));
			$email	= strtolower($this->StrToDb($this->input->post('email')));
			$phone	= $this->StrToDb($this->input->post('phone'));
			$type	= $this->StrToDb($this->input->post('phone')); 
			$cek 	= $this->TulwModel->checkEmailExist($email)->result();
			if (!$cek){
				foreach ($event as $ev){
					
					$data = array(
							"tulw_reg_name" 	=> $name,
							"tulw_reg_email" 	=> $email,
							"tulw_reg_phone" 	=> $phone,
							"tulw_reg_type" 	=> $type,
							"tulw_reg_event_id" => $ev
							);
					$this->TulwModel->insertReg($data);
				}
				echo "success";
			}
			else echo "error, email exist";
		}
		else echo "error, please checklist at least one event";
		
    } 
	
	function StrToDb($words, $style="") {
		$words = str_replace('\'','\'\'',$words);
		if($style=='ucwords') $words = ucwords($words);
		else if($style=='ucfirst') $words = ucfirst($words);
		else if($style=='upper') $words = strtoupper($words);
		else if($style=='lower') $words = strtolower($words);
		return trim($words);
	}
	 
}

?>