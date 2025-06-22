<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rfidreg extends CI_Controller {

    public function __construct(){
        parent::__construct();
		
		if(!$this->session->userdata('login')) redirect(url_admin(), 'refresh'); 
        $this->load->model('RfidRegModel','rrm');
		$this->load->model("RfidModel","rm");
    }

    public function index(){ 

		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh'); 
        $data['menu']		= 'rfidreg/rfidreg'; 
        $this->load->view('theme',$data);
    }

    public function ajax_data(){
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$table 		= "select rf.*,master_data_fullname from rfid_not_same_with_igracias rf join member on username_id=member.id";
		$colOrder 	= array(null,'username','master_data_fullname','rfid',null); //set column field database for datatable orderable
		$colSearch 	= array('username','master_data_fullname','rfid'); //set column field database for datatable
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
            $row[] = $dt->username;
            $row[] = $dt->master_data_fullname;
            $row[] = $dt->rfid; 
			
            $row[] = '<div class="btn-group"> <button type="button" class="btn btn-sm btn-success" title="'.getLang("edit").'" onclick="edit('."'".$dt->id."'".')"><i class="fa fa-pencil-square-o"></i></button><button type="button" class="btn btn-sm btn-danger" title="'.getLang("delete_data").'" onclick="del('."'".$dt->id."'".','."'".$dt->username."'".')"><i class="fa fa-trash-o"></i></button></div>';
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
			$tab['id'] 	= $row['master_data_user'];
			$tab['name'] 	= $row['master_data_user']." - ".$row['master_data_fullname'];
			$arr[] = $tab; 
		}
		echo json_encode($arr);
    } 
	
	public function ajax_image()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed'); 
		$table 		= "select * from rfid_not_in_db"; 
		$colOrder 	= array(null,'rfid','description',null); //set column field database for datatable orderable
		$colSearch 	= array('rfid','description'); //set column field database for datatable
		$order 		= "order by description asc"; // default order 
		
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
			$row[] = $dt->rfid;
			$row[] = $dt->description; 
			if ($dt->id<10 or $dt->id>229){
				$row[] = '<div class="btn-group"> <button type="button" class="btn btn-sm btn-danger" title="'.getLang("delete_data").'" onclick="del_image('."'".$dt->id."'".','."'".$dt->rfid."'".')"><i class="fa fa-trash-o"></i></button></div>';
			}
			else $row[] = '';
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
		if ($this->rrm->checkInsert($item)->result())echo json_encode(array("status" => "False"));
		else {
			$status	= "success";
			$dt 	= $this->rm->GetRfid('',$item['username'])->row();
			$member = $this->rm->GetMember($item['username'])->row();
			if (!$member){
				if ($dt->C_KODE_JENIS_USER=='mahasiswa'){
					$user = $this->rm->GetUser('mahasiswa',$item['username'])->row();
					if ($user) {
						$master_data_course = $user->c_kode_prodi;
						$member_type_api = $this->rm->GetMemberTypeApi('MAHASISWA',$user->c_kode_prodi)->row(); 
					}
					else $status = "failed";
				}
				else {
					$user = $this->rm->GetUser('pegawai',$dt->c_username)->row();
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
						"master_data_user" 		=> $dt->c_username,
						"master_data_email" 	=> $dt->email,
						"master_data_course" 	=> $master_data_course,
						"master_data_fullname" 	=> $dt->fullname,
						"status" 				=> "1",
						"created_at" 			=> date("Y-m-d")
					);
				
					$item['username_id'] = $this->rm->add($data);
					if ($this->rrm->add($item)) echo json_encode(array("status" => TRUE));
				}
				else {
					echo json_encode(array("status" => FALSE));
				} 
			} 
			else {
				$item['username_id'] = $member->id;
				if ($this->rrm->add($item)) echo json_encode(array("status" => TRUE));
			} 
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
