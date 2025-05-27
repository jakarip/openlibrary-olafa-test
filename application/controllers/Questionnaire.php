<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Questionnaire extends CI_Controller {

    public function __construct(){
        parent::__construct();
		
		if(!$this->session->userdata('login_room')) redirect(url_admin(), 'refresh'); 
        $this->load->model('QuitionerModel','rm');
    }

    public function index(){ 
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh'); 
		if ($this->rm->checkQuitioner()->num_rows()!=0) redirect(url_admin(), 'refresh'); 
        $data['menu']		= 'quitioner';
		$data['quitioner'] 	= $this->rm->quitioner()->result();
        $this->load->view('theme',$data);
    } 

    function insert(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed'); 
		$question = $this->input->post('quitioner');
		$datas['id'] = $this->session->userdata('memberid');
		foreach($question as $key => $row){
			$datas['no'.$key] = $row;
		}
		$datas['tanggal'] = date('Y-m-d H:i:s');
		//print_r($datas);
		$this->rm->add($datas);
		echo json_encode(array("status" => TRUE));
    } 
}
