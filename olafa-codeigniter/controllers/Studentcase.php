<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Studentcase extends CI_Controller {

    public function __construct(){
        parent::__construct();
		
		if(!$this->session->userdata('login')) redirect(url_admin(), 'refresh'); 
        $this->load->model('RoomModel','rm');
		if(!$this->session->userdata('login')) redirect('');
    }

    public function index(){ 

		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh'); 
        $data['menu']		= 'room';
		$data['room'] 		= $this->rm->getbyactiveid()->result();
        $this->load->view('theme',$data);
    }

    public function ajax_data(){
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$table 		= "select * from room.room";
		$colOrder 	= array(null,'room_name','room_capacity','room_description','room_activate',null); //set column field database for datatable orderable
		$colSearch 	= array('room_name','room_capacity','room_description','room_activate'); //set column field database for datatable
			$order 		= "order by room_active,room_name asc"; // default order  
		
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
            $row[] = $dt->room_name;
            $row[] = $dt->room_capacity;
            $row[] = $dt->room_description; 
			
            $row[] = '<div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="'.getLang("edit").'" onclick="edit('."'".$dt->room_id."'".')"><i class="fa fa-pencil-square-o"></i></button> '.(($dt->room_active==0)?'<button type="button" class="btn btn-sm btn-danger" title="'.getLang("deactivate_data").'" onclick="del('."'1'".','."'".$dt->room_id."'".','."'".$dt->room_name."'".')"><i class="fa fa-unlink"></i></button>':'<button type="button" class="btn btn-sm btn-default" title="'.getLang("activate_data").'" onclick="del('."'0'".','."'".$dt->room_id."'".','."'".$dt->room_name."'".')"><i class="fa fa-link"></i></button>').'</div>';
            $row[] = $dt->room_active;
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
	
	public function ajax_image()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id 		= $this->input->post('id');
		$table 		= "select * from room.room_gallery left join room.room on room_id=rg_room_id"; 
		$colOrder 	= array(null,'room_name','rg_image',null); //set column field database for datatable orderable
		$colSearch 	= array('room_name','rg_image'); //set column field database for datatable
		$order 		= "order by room_name asc"; // default order 
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order);
		
		$list = $this->datatables->get_datatables();
		$data = array();
		$no = $_POST['start']; 
		foreach ($list as $dt) {
			$no++;
			$row = array();
			$row[] = '<div class="text-center">'.$no.'</div>';
			$row[] = $dt->room_name;
			$row[] = (ISSET($dt->rg_image)?'<div class="nailthumb-container" style="cursor:pointer" onclick="detail_image('."'".$dt->rg_image."'".')"><img src="tools/images/'.$dt->rg_image.'" width="0px"> </img></div>':'<div class="nailthumb-container"><img src="tools/images/default.png" width="150px"> </img></div>'); 
			$row[] = '<div class="btn-group"><button type="button" class="btn btn-sm btn-danger" title="'.getLang("delete").'" onclick="del_image('."'".$dt->rg_id."'".','."'".$dt->rg_id."'".')"><i class="fa fa-trash-o"></i></button></div>'; 
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

    function insert(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $item 							= $_POST['inp'];  
		if ($this->rm->add($item)) echo json_encode(array("status" => TRUE));
    }

    function update(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
        $item   = $_POST['inp'];    
		if ($this->rm->edit($id, $item)) echo json_encode(array("status" => TRUE));
    }

    function edit(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
        $data 	= $this->rm->getbyid($id)->row();
        echo json_encode($data);
    }

    function deletes(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
        $item	= $_POST['inp'];   
        $this->rm->edit($id, $item);
        echo json_encode(array("status" => TRUE));
    } 
	
	public function insert_image()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		if(!$this->input->post('inp')) return false;
		
		$item 			= $this->input->post('inp');  
		
		$uploaddir 		= 'tools/images/';
		$file 			= explode(".", $_FILES['image']['name']); 
		$ext 			= end($file);
		$newFile 		= round(microtime(true)).'.'.$ext;
		$uploadfile 	= $uploaddir . basename($newFile);
		move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile); 
		
		$item['rg_image'] 	= $newFile; 
		
		$this->rm->addImage($item);
		echo json_encode(array("status" => TRUE));
	} 
	
	function delete_image(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $id     = $this->input->post('id');
		$data = $this->rm->getbyimage($id)->row();
		if (file_exists('tools/images/'.$data->rg_image)) unlink('tools/images/'.$data->rg_image);
        $this->rm->deleteImage($id);
        echo json_encode(array("status" => TRUE));
    }  
}
