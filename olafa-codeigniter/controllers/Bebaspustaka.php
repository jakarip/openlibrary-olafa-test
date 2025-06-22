<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BebasPustaka extends CI_Controller {

    public function __construct(){
        parent::__construct();
		
		if(!$this->session->userdata('login')) redirect(url_admin(), 'refresh'); 
        $this->load->model('BebasPustakaModel','bpm');  
    }

    public function delete_file(){ 
		
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh'); 
        $data['menu']		= 'bebaspustaka/delete'; 
        $this->load->view('theme',$data);
    }  

    public function lecturer(){ 
		
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh'); 
        $data['menu']		= 'bebaspustaka/lecturer'; 
        $this->load->view('theme',$data);
    }  

    function ajax_data(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		if (empty($_POST['search'])) echo json_encode(array("status"=>"empty"));
		else {
			$dt = $this->bpm->dokumen($_POST['search'])->result_array();
			$data['status'] = "success";
			$data['data'] 	= $dt; 
			echo json_encode($data);
		}
	} 
	
	function auto_data(){
		$dt = $this->bpm->member(strtolower($_GET['q']))->result_array();
		$arr = array();
		foreach ($dt as $row){
			$tab['id'] 	= $row['memberid'];
			$tab['name'] 	= $row['master_data_user']." - ".$row['master_data_number']." - ".$row['master_data_fullname']." (".$row['NAMA_PRODI'].")";
			$arr[] = $tab; 
		}
		echo json_encode($arr);
    } 
	
	function auto_data_lecturer(){
		$dt = $this->bpm->member_lecturer(strtolower($_GET['q']))->result_array();
		$arr = array();
		foreach ($dt as $row){
			$tab['id'] 	= $row['memberid'];
			$tab['name'] 	= $row['master_data_user']." - ".$row['master_data_number']." - ".$row['master_data_fullname']." (".$row['NAMA_PRODI'].")";
			$arr[] = $tab; 
		}
		echo json_encode($arr);
    } 

	public function delete_detail($id){ 
		
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh'); 
		$dt = $this->bpm->getDocument($id)->result_array();
		if(!$dt) redirect("index.php/bebaspustaka/delete_file");
        $data['menu']	= 'bebaspustaka/delete_detail';  
		$data['dt'] 	= $dt;
        $this->load->view('theme',$data);
    }  
	
	function ajax_delete_document(){
		
		$dt = $this->bpm->getDocument($_POST['id'])->result_array();
		foreach ($dt as $row){
		
			$folder = "../../../../data/batik/symfony_projects/book/".$row['master_data_user']; 
			$file = $folder.'/'.$row['location']; 
			if (file_exists($file)) {
				unlink($file);
			}
		}
		
		$this->bpm->delete_document($_POST['id']);
		echo json_encode(array('status'=>"success"));
	} 
 
	

    function ajax_delete_file(){
		$dt = $this->bpm->get_file($_POST['id'])->row_array();  
		$folder = "../../../../data/batik/symfony_projects/book/".$dt['master_data_user']; 
		$file = $folder.'/'.$dt['location'];
		$this->bpm->delete_file($_POST['id']);
		if (file_exists($file)) {
			unlink($file);
		}
		echo json_encode(array('status'=>"success"));
	} 
 
}
