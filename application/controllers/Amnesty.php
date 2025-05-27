<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Amnesty extends CI_Controller {

    public function __construct(){
        parent::__construct();
		
		if(!$this->session->userdata('login')) redirect(url_admin(), 'refresh'); 
        $this->load->model('AmnestyModel','rrm'); 
    }

    public function index(){ 

		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh'); 
        $data['menu']		= 'amnesty/amnesty'; 
        $this->load->view('theme',$data);
    }

    public function ajax_data(){
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$table 		= "select ad.*,master_data_user,master_data_fullname from amnesty_denda ad join member m on m.id=username_id";
		$colOrder 	= array(null,'master_data_user','master_data_fullname',null); //set column field database for datatable orderable
		$colSearch 	= array('master_data_user','master_data_fullname'); //set column field database for datatable
			$order 		= "order by master_data_fullname asc"; // default order  
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order); 
		
		$list = $this->datatables->get_datatables();
		
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $dt){
            $no++;
            $row = array();
            $row[] = '<div class="text-center">'.$no.'</div>';
            $row[] = $dt->master_data_user;
            $row[] = $dt->master_data_fullname; 
			
            $row[] = '<div class="btn-group"><button type="button" class="btn btn-sm btn-danger" title="'.getLang("delete_data").'" onclick="del('."'".$dt->id."'".','."'".$dt->master_data_user."'".')"><i class="fa fa-trash-o"></i></button></div>';
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
	
	function auto_data(){
		$dt = $this->rrm->member(strtolower($_GET['q']))->result_array();
		$arr = array();
		foreach ($dt as $row){
			$tab['id'] 	= $row['id'];
			$tab['name'] 	= $row['master_data_user']." - ".$row['master_data_fullname']." (".$row['NAMA_PRODI'].")";
			$arr[] = $tab; 
		}
		echo json_encode($arr);
    } 
	
	 
    function insert(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $item 							= $_POST['inp']; 
		if ($this->rrm->checkInsert($item)->result())echo json_encode(array("status" => "False"));
		else {
			if ($this->rrm->add($item)) echo json_encode(array("status" => TRUE)); 
		}
    }

    function update(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
        $item   = $_POST['inp'];    
		if ($this->rrm->checkEdit($item,$id)->result()) echo json_encode(array("status" => "False"));
		else {
			if ($this->rrm->edit($id, $item)) echo json_encode(array("status" => TRUE));
		}
    }

    function edit(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
        $data 	= $this->rrm->getbyid($id)->row();
        echo json_encode($data);
    }

    function deletes(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id'); 
        $this->rrm->deletes($id);
        echo json_encode(array("status" => TRUE));
    } 
	
	public function insert_image()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		if(!$this->input->post('inp')) return false;
		
		$item 							= $_POST['inp']; 
		if ($this->rrm->checkInsertNotDb($item)->result())echo json_encode(array("status" => "False"));
		else {
			if ($this->rrm->addNotDb($item)) echo json_encode(array("status" => TRUE));
		}
	} 
	
	function delete_image(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id'); 
       if ($this->rrm->deleteNotDb($id)) echo json_encode(array("status" => TRUE));
	   else echo json_encode(array("status" => "False"));
    }  
}
