<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('login')) redirect(url_admin(), 'refresh');
		
		$this->load->model('Menumodel','mm',true);
		$this->load->model('Usergroupmodel','um',true);
		$this->load->model('Usergroupmappingmodel','umm',true);
		$this->load->model('Languagemodel','lm',true);
		$this->load->model('Usermodel','user',true);
	}
	
	public function user()
	{
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');
		$data['usergroup']= $this->um->getall()->result();
		$data['menu']= 'backend/setting/user';
		$this->load->view('theme',$data);
	}

	public function ajax_user()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$table 		= "select *,(SELECT GROUP_CONCAT(ug_name order by ug_name SEPARATOR ', ' )FROM md_uguser left join
					md_usergroup ug on ug_id=uu_ug_id where uu_user_id=user_id) list_usergroup 
					from md_user  left join md_usergroup on ug_id=user_default_ug";
		$colOrder 	= array(null,'user_name','user_username','user_plain_pass','user_email','ug_name','list_usergroup',null); //set column field database for datatable orderable
		$colSearch 	= array('user_name','user_username','user_plain_pass','user_email','ug_name','list_usergroup'); //set column field database for datatable
		$order 		= "order by user_name asc"; // default order 
		
		$code = $this->input->post('code');
		
		$this->datatables->set_db('menu');
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
			$row[] = $dt->user_name;
			$row[] = $dt->user_username;
			if($code=='encrypt') $row[] = str_repeat("*", 10);
			else if($code=='decrypt') $row[] = $dt->user_plain_pass;
			$row[] = $dt->user_email;
			$row[] = $dt->ug_name;
			$row[] = $dt->list_usergroup;
			$row[] = '<div class="btn-group">'.(($dt->user_username!="superadmin")?'<button class="btn btn-sm btn-success" onclick="edit('."'".$dt->user_id."'".')" title="'.getLang("edit").'"><i class="fa fa-edit"></i></button><button class="btn btn-sm btn-danger" onclick="del('."'".$dt->user_id."'".','."'".$dt->user_username."'".')" title="'.getLang("delete").'"><i class="fa fa-trash-o"></i></button>':'').'<button class="btn btn-sm btn-primary"onclick="detail('."'".$dt->user_id."'".')" title="'.getLang("user_mapping").'"><i class="fa fa-plus-square"></i></button></div>';
		
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
	
	function checkUsername() { 
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$user = strtolower($_POST['inp']['user_username']);
		$data = $this->user->checkUsername($user)->row();
		if ($data) echo "false";
		else echo "true";
	}  
	
	public function insert_user()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		if(!$this->input->post('inp')) return false;
		
		$user = strtolower($_POST['inp']['user_username']);
		$data = $this->user->checkUsername($user)->row();
		if ($data) echo json_encode(array("status" => FALSE));
		else {
			$item = $this->input->post('inp');
			$item['user_pass'] 	 	 = md5($item['user_plain_pass']);
			$item['user_input_date'] = $this->session->userdata('user_id');
			$item['user_input_date'] = date("Y-m-d H:i:s"); 
			
			$this->user->add($item);
			echo json_encode(array("status" => TRUE));
		}
	}   
	
	function check_plain_pass() { 
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id 	= $this->input->post("id");
		$pass 	= $this->input->post("password");
		$pass 	= md5($pass);
		$data	= $this->user->getby($this->session->userdata('username'),$pass)->row(); 
		if ($data) echo json_encode(array("status" => TRUE));
		else echo json_encode(array("status" => FALSE));
	}  
	
	function get_usergroup_user() { 
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id 	= $this->input->post("id");
		
		$user	= $this->user->getbyid($id)->row(); 
		$data	= $this->user->getMappingUserGroup($id)->result(); 
		
		$dt[] 	= $user;
		$dt[] 	= $data;
		echo json_encode($dt);
	} 
	
	function set_usergroup_user() { 
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id 	= $this->input->post("id");
		$inp 	= $this->input->post("inp");
		
		$user = $this->user->getbyid($id)->row();
		$this->user->delete_mappingusergroup($id);
		foreach($inp['uu_ug_id'] as $row){
			$item['uu_user_id']		= $id;
			$item['uu_ug_id']		= $row;
			$item['uu_input_id'] 	= $this->session->userdata('user_id');
			$item['uu_input_date']	= date("Y-m-d H:i:s");
			$this->umm->add($item);
		}
		if (!in_array($user->user_default_ug,$inp['uu_ug_id'])) {
			$item2['user_default_ug'] = $inp['uu_ug_id'][0];
			$this->user->edit($id,$item2);
		}
		echo json_encode(array("status" => TRUE));
	}
	
	public function edit_user()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$data = $this->user->getbyid($this->input->post('id'))->row();
		$ug = $this->user->getMappingUserGroup($this->input->post('id'))->result();
		$html = "";
		if ($ug) {
			foreach ($ug as $row){
				$html.='<option value="'.$row->ug_id.'"'.(($row->ug_id==$data->user_default_ug)?"selected":"").'>'.$row->ug_name.'</option>';
			}
		}
		$arr = array();
		$arr[] = $data;
		$arr[] = $html;
		echo json_encode($arr);
	}
	
	public function update_user()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$id   = $this->input->post('id');
		$pass = $this->input->post('password');
		
		if ($pass!="") {
			$item['user_plain_pass'] = $pass;
			$item['user_pass'] 	 	 = md5($pass);
		}
		
		$item['user_edit_id'] 	= $this->session->userdata('user_id');
		$item['user_edit_date'] = date("Y-m-d H:i:s");
		
		$this->user->edit($id, $item);
		echo json_encode(array("status" => TRUE));
	}
	
	public function delete_user()
	{
		if(!$this->input->is_ajax_request()) return false;
		$this->user->delete($this->input->post('id'));
	}
	
	//menu
	
	public function menu()
	{
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');
		$data['menu']= 'backend/setting/menu';
		$this->load->view('theme',$data);
	}

	public function ajax_menu()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$table 		= "select * from md_menu where menu_id>10";
		$colOrder 	= array(null,'menu_name','menu_name_eng','menu_name_ina','menu_icon','menu_url','menu_display',null); //set column field database for datatable orderable
		$colSearch 	= array('menu_name','menu_name_eng','menu_name_ina','menu_icon','menu_url','menu_display'); //set column field database for datatable
		$order 		= "order by menu_name_eng asc"; // default order 
		
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
			$row[] = $dt->menu_name;
			$row[] = $dt->menu_name_eng;
			$row[] = $dt->menu_name_ina;
			$row[] = $dt->menu_icon;
			$row[] = $dt->menu_url;
			$row[] = $dt->menu_display;
			$row[] = '<div class="btn-group"><button class="btn btn-sm btn-success" onclick="edit('."'".$dt->menu_id."'".')" title="'.getLang("edit").'"><i class="fa fa-edit"></i></button><button class="btn btn-sm btn-danger" onclick="del('."'".$dt->menu_id."'".','."'".$dt->menu_name_eng."'".')" title="'.getLang("delete").'"><i class="fa fa-trash-o"></i></button></div>';
		
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
	
	public function insert_menu()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$item['menu_name'] 	 	 = ucwords(strtolower($item['menu_name']));
		$item['menu_name_eng'] 	 = ucwords(strtolower($item['menu_name_eng']));
		$item['menu_name_ina']   = ucwords(strtolower($item['menu_name_ina']));
		$item['menu_icon'] 	 	 = strtolower($item['menu_icon']);
		$item['menu_url'] 	 	 = strtolower($item['menu_url']);
		$item['menu_input_id'] 	 = $this->session->userdata('user_id');
		$item['menu_input_date'] = date("Y-m-d H:i:s"); 
		
		$this->mm->add($item);
		echo json_encode(array("status" => TRUE));
	}
	
	public function edit_menu()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		echo json_encode($this->mm->getbyid($this->input->post('id'))->row());
	}
	
	public function update_menu()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$id   = $this->input->post('id');
		
		$item['menu_name_eng'] 	 = ucwords(strtolower($item['menu_name_eng']));
		$item['menu_name_ina']   = ucwords(strtolower($item['menu_name_ina']));
		$item['menu_icon'] 	 	 = strtolower($item['menu_icon']);
		$item['menu_url'] 	 	 = strtolower($item['menu_url']);
		$item['menu_edit_id'] 	= $this->session->userdata('user_id');
		$item['menu_edit_date'] = date("Y-m-d H:i:s");
		
		$this->mm->edit($id, $item);
		echo json_encode(array("status" => TRUE));
	}
	
	public function delete_menu()
	{
		if(!$this->input->is_ajax_request()) return false;
		$this->mm->delete($this->input->post('id'));
	}
	
	//menu display order
	
	public function menudisplayorder()
	{
		$data['usergroup']= $this->um->getall()->result();
		$data['menu']= 'backend/setting/menudisplayorder';
		$this->load->view('theme',$data);
	} 

	public function ajax_menudisplayorder()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$table 		= "select Concat(If(isnull(m2.menu_name_ina),'',Concat('/',m2.menu_display_order,'/',m2.menu_name_ina)),'/',
		m1.menu_display_order,'/',m1.menu_name_ina) as generated_path,
		m2.menu_name_ina as parent,m1.* from md_menu m1 left JOIN
		md_menu m2 on m2.menu_id=m1.menu_parent_id where m1.menu_id > 15";
		$colOrder 	= array(null,'menu_display_order','menu_name_ina','menu_parent_id',null); //set column field database for datatable orderable
		$colSearch 	= array('menu_display_order','menu_name_ina','menu_parent_id'); //set column field database for datatable
		$order 		= "ORDER BY generated_path"; // default order 
		$this->datatables->set_db('menu');
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
			$row[] = '<input type="hidden" name="id[]" value="'.$dt->menu_id.'"><input type="text" name="menu_display_order[]" class="form-control" value="'.$dt->menu_display_order.'">';
			$row[] = (!empty($dt->menu_parent_id)?' -- '.$dt->menu_name_ina : $dt->menu_name_ina);
			$row[] = getParentMenuExceptCoreMenu($dt->menu_parent_id,$dt->menu_id);
			$row[] = '<div class="btn-group"><a href="javascript:;" class="btn btn-sm btn-primary" onclick="detail('."'".$dt->menu_id."'".')" title="'.getLang("menu_mapping").'"><i class="fa fa-eye"></i></a></div>';
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
	
	public function update_menudisplayorder()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id   					= $this->input->post('id');
		$menu_display_order 	= $this->input->post('menu_display_order');
		$menu_parent_id   		= $this->input->post('menu_parent_id');
		$status 				= "TRUE";
		foreach($id as $key=>$val){
			if(!empty($menu_parent_id[$key])) {
				$cek = $this->mm->checkMenuHasChildMenu($val)->result();
				if ($cek) $status = "FALSE";
			}
		}
		if ($status=="TRUE"){
			foreach($id as $key=>$val){
				$item['menu_display_order'] = $menu_display_order[$key];
				$item['menu_parent_id']   	= (!empty($menu_parent_id[$key])?$menu_parent_id[$key]:null);
				$item['menu_edit_id'] 		= $this->session->userdata('user_id');
				$item['menu_edit_date'] 	= date("Y-m-d H:i:s");
				$this->mm->edit($val, $item);
			}
			echo json_encode(array("status" => TRUE));
		}
		else echo json_encode(array("status" => FALSE));
	} 
	
	function get_usergroup_menu() { 
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id 	= $this->input->post("id");
		$user	= $this->mm->getbyid($id)->row(); 
		$data	= $this->mm->getMappingUserGroup($id)->result(); 
		
		$dt[] = $user;
		$dt[] = $data;
		echo json_encode($dt);
	} 
	
	function set_usergroup_menu() { 
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id 	= $this->input->post("id");
		$inp 	= $this->input->post("inp");
		
		$this->mm->delete_mappingusergroup($id);
		foreach($inp['um_ug_id'] as $row){
			$item['um_menu_id']		= $id;
			$item['um_ug_id']		= $row;
			$item['um_input_id'] 	= $this->session->userdata('user_id');
			$item['um_input_date']	= date("Y-m-d H:i:s");
			$this->mm->addMenuMapping($item);
		} 
		echo json_encode(array("status" => TRUE));
	}
	
	//usergroup
	
	public function usergroup()
	{
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');
		$data['menu']= 'backend/setting/usergroup';
		$this->load->view('theme',$data);
	}
	
	public function ajax_usergroup()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$table 		= "select *,(select count(*)from md_uguser where uu_ug_id=ug.ug_id)total_user from md_usergroup ug";
		$colOrder 	= array(null,'ug_name','ug_desc','total_user',null); //set column field database for datatable orderable
		$colSearch 	= array('ug_name','total_user'); //set column field database for datatable
		$order 		= "order by ug_name asc"; // default order 
		
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
			$row[] = $dt->ug_name;
			$row[] = $dt->ug_desc;
			$row[] = $dt->total_user; 
			$row[] = '<div class="btn-group">'.(($dt->ug_id!="1")?'<button class="btn btn-sm btn-success"onclick="edit('."'".$dt->ug_id."'".')" title="'.getLang("edit").'"><i class="fa fa-edit"></i></button><button class="btn btn-sm btn-danger" onclick="del('."'".$dt->ug_id."'".','."'".$dt->ug_name."'".')" title="'.getLang("delete").'"><i class="fa fa-trash-o"></i></button>':'').'<a class="btn btn-sm btn-primary" href="backend/setting/usergroupmapping/'.$dt->ug_id.'" title="'.getLang("usergroup_mapping").'"><i class="fa fa-plus-square"></i></a></div>';
		
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
	
	public function insert_usergroup()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$item['ug_input_id'] 	= $this->session->userdata('user_id');
		$item['ug_input_date']	= date("Y-m-d H:i:s");
		
		$this->um->add($item);
		echo json_encode(array("status" => TRUE));
	}
	
	public function edit_usergroup()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		echo json_encode($this->um->getbyid($this->input->post('id'))->row());
	}
	
	public function update_usergroup()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$id   = $this->input->post('id');
		$item['ug_edit_id']		= $this->session->userdata('user_id');
		$item['ug_edit_date'] 	= date("Y-m-d H:i:s");
		
		$this->um->edit($id, $item);
		echo json_encode(array("status" => TRUE));
	}
	
	public function delete_usergroup()
	{
		if(!$this->input->is_ajax_request()) return false;
		$this->um->delete($this->input->post('id'));
	}
	
	//usergroupmapping
	
	public function usergroupmapping($id)
	{
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');
		$data['usergroup'] = $this->um->getbyid($id)->row();
		if (!$data['usergroup']) redirect('backend/setting/usergroup');
		
		$data['menu']= 'backend/setting/usergroupmapping';
		$this->load->view('theme',$data);
	}
	
	public function ajax_ugmapping_all_user_list()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id 		= $this->input->post("id");
		$table 		= "select * from md_user where user_id not in(select uu_user_id from md_uguser where uu_ug_id='$id')";
		$colOrder 	= array(null,'user_username','user_name'); //set column field database for datatable orderable
		$colSearch 	= array('user_username','user_name'); //set column field database for datatable
		$order 		= "order by user_username asc"; // default order 
		
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
			$row[] = '<div class="text-center"><input type="checkbox" value="'.$dt->user_id.'" name="inp[id][]"></div>';
			$row[] = $dt->user_username;
			$row[] = $dt->user_name;
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
	
	public function ajax_ugmapping_registered_user_list()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id 		= $this->input->post("id");
		$table 		= "select *,(select uu_id from md_uguser where uu_user_id=user_id and uu_ug_id='$id')uu_id,(select ug_name from md_usergroup where ug_id='$id')ug_name from md_user where user_id in(select uu_user_id from md_uguser where uu_ug_id='$id')";
		$colOrder 	= array(null,'user_username','user_name'); //set column field database for datatable orderable
		$colSearch 	= array('user_username','user_name'); //set column field database for datatable
		$order 		= "order by user_username asc"; // default order 
		
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
			if ($dt->ug_name=='superadmin' and $dt->user_username=='superadmin') $row[] = "";
			else $row[] = '<div class="text-center"><input type="checkbox" value="'.$dt->uu_id.'" name="inp[id][]"></div>';
			$row[] = $dt->user_username;
			$row[] = $dt->user_name;
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
	
	public function register_user()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id = $this->input->post('id');
		$inp = $this->input->post('inp');
		
		foreach($inp['id'] as $row){
			$item['uu_user_id']		= $row;
			$item['uu_ug_id']		= $id;
			$item['uu_input_id'] 	= $this->session->userdata('user_id');
			$item['uu_input_date']	= date("Y-m-d H:i:s");
			$this->umm->add($item);
			$user = $this->user->getbyid($row)->row();
			if ($user->user_default_ug=="" or $user->user_default_ug==null){
				$item2['user_default_ug'] = $id;
				$this->user->edit($row,$item2);
			}
		}
		echo json_encode(array("status" => TRUE));
	}
	 
	public function delete_registered_user()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id = $this->input->post('id');
		$inp = $this->input->post('inp');
		
		foreach($inp['id'] as $row){
			$uguser = $this->umm->getbyid($row)->row();
			$user = $this->user->getbyid($uguser->uu_user_id)->row();
			if ($user->user_default_ug==$id){
				$dt = $this->umm->getMappingUserGroup($id,$uguser->uu_user_id)->row();
				if ($dt){
					$item2['user_default_ug'] = $dt->uu_ug_id;
					$this->user->edit($uguser->uu_user_id,$item2);
				}
				else {
					$item2['user_default_ug'] = null;
					$this->user->edit($uguser->uu_user_id,$item2);
				}
				$this->umm->delete($row);
			}
		}
		echo json_encode(array("status" => TRUE));
	}
	
	//language
	
	public function language()
	{
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');
		$data['menu']= 'backend/setting/language';
		$this->load->view('theme',$data);
	}
	
	public function ajax_language()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$table 		= "select * from md_language";
		$colOrder 	= array(null,'lang_var','lang_eng','lang_ina',null); //set column field database for datatable orderable
		$colSearch 	= array('lang_var','lang_eng','lang_ina'); //set column field database for datatable
		$order 		= "order by lang_var asc"; // default order 
		
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
			$row[] = '<input type="text" id="lang_var'.$dt->lang_id.'" class="form-control" value="'.$dt->lang_var.'">';
			$row[] = '<input type="text" id="lang_eng'.$dt->lang_id.'" class="form-control" value="'.$dt->lang_eng.'">';
			$row[] = '<input type="text" id="lang_ina'.$dt->lang_id.'" class="form-control" value="'.$dt->lang_ina.'">';
			$row[] = '<div class="btn-group"><button class="btn btn-sm btn-success"onclick="edit('."'".$dt->lang_id."'".')" title="'.getLang("save").'"><i class="fa fa-save"></i></button><button class="btn btn-sm btn-danger" onclick="del('."'".$dt->lang_id."'".','."'".$dt->lang_eng."'".')" title="'.getLang("delete").'"><i class="fa fa-trash-o"></i></button></div>';
		
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
	
	function checkLanguage() { 
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$lang = strtolower($_POST['inp']['lang_var']);
		$data = $this->lm->checkLanguage($lang)->row();
		if ($data) echo "false";
		else echo "true";
	}  
	
	public function insert_language()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		if(!$this->input->post('inp')) return false;
		
		$item 						= $this->input->post('inp');
		$item['lang_input_id'] 		= $this->session->userdata('user_id');
		$item['lang_input_date']	= date("Y-m-d H:i:s");
		
		$this->lm->add($item);
		echo json_encode(array("status" => TRUE));
	}
	
	public function update_language()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		if(!$this->input->post('inp')) return false;
		
		$item 		= $this->input->post('inp');
		$id   		= $this->input->post('id');
		
		$search 	= array(" ", "'", '"');
		$replace 	= array('_', '', '');
		$item['lang_var']			= str_replace($search,$replace,strtolower($item['lang_var']));
		$item['lang_edit_id']		= $this->session->userdata('user_id');
		$item['lang_edit_date'] 	= date("Y-m-d H:i:s");
		
		$this->lm->edit($id, $item);
		echo json_encode(array("status" => TRUE));
	}
	
	public function delete_language()
	{
		if(!$this->input->is_ajax_request()) return false;
		$this->lm->delete($this->input->post('id'));
	}
	

}
