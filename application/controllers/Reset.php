<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reset extends CI_Controller {

    public function __construct(){
        parent::__construct();
		
		if(!$this->session->userdata('login_room')) redirect(url_admin(), 'refresh'); 
        $this->load->model('BlacklistModel','bm');
    }

    public function index(){ 

		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh'); 
        $data['menu']		= 'reset'; 
        $this->load->view('theme',$data);
    }

    function update(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $member 	= $this->input->post('member'); 
		$data2 = explode(",",$member);
		foreach($data2 as $row){ 
			$this->bm->edit($row);
		}  
		echo json_encode(array("status" => TRUE));
    } 
 
	 
}
