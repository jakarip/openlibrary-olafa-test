<?php

class Usereducation2 extends CI_Controller {
 
    function __construct() {
        parent::__construct(); 
		$this->load->model('UserEducationManualModel');
		$this->load->library('PHPExcel');
		//$this->load->library('PHPExcel/IOFactory');

    }

    function index() {  
	
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');  
		$data['prodi']		= $this->UserEducationManualModel->getProdi()->result();
		$data['menu'] 		= 'usereducation/usereducation_manual2'; 
		$this->load->view('theme',$data);
    }
	 
	public function ajax_index()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$year 		= $this->input->post('year');
		$table 		= "select * from useredu_manual";
		$colOrder 	= array(null,'useredu_name','useredu_nim','useredu_prodi','useredu_date',null); //set column field database for datatable orderable
		$colSearch 	= array('useredu_name','useredu_nim','useredu_prodi','useredu_date'); //set column field database for datatable
		$order 		= "order by useredu_id desc"; // default order 
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order);
		
		$list = $this->datatables->get_datatables();
		//print_r($list);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $dt) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $dt->useredu_name;
			$row[] = $dt->useredu_nim; 
			$row[] = $dt->useredu_prodi; 
			$row[] = $dt->useredu_date; 
			$row[] = '<div><a class="btn btn-sm btn-danger btn-embossed" target="_blank" href="index.php/usereducation/students/'.strtolower($dt->useredu_id).'/'.$year.'" title="'.getLang('student').'"><i class="fa fa-users"></i></a></div>';
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->datatables->count_all(),
						"recordsFiltered" => $this->datatables->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	} 
	
	public function add()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');  
		$item['useredu_date'] 	= date("Y-m-d H:i:s");  
		 
		if ($this->UserEducationManualModel->add($item)) echo json_encode(array("status" => TRUE)); 
	}
	
}

?>