<?php

class Katalogmk extends CI_Controller {
 
    function __construct() {
        parent::__construct(); 
		$this->load->model('KatalogMkModel');
		//$this->load->library('phpExcel/PHPExcel');   
		//$this->load->library('mpdf/mpdf');  
		if(!$this->session->userdata('login')) redirect(''); 
    }

    function index() {   
		// if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');  
		$data['tipe'] 	= $this->KatalogMkModel->get_type()->result();
		$data['menu'] 	= 'katalogmk/katalogmk'; 
		$this->load->view('theme', $data);
    }
	
	public function ajax_index()
	{ 
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$tipe = $this->input->post('tipe');
		$searchtype = $this->input->post('searchtype');
		$search = $this->input->post('searchs');
		
		$searchwhere = "";
		if ($searchtype=="title" and !EMPTY($search)){
			$searchwhere = " and lower(title) like '%".strtolower($search)."%'";
		}
		else if ($searchtype=="author" and !EMPTY($search)){
			$searchwhere = " and lower(author) like '%".strtolower($search)."%'";
		}
		else if ($searchtype=="subject" and !EMPTY($search)){
			$searchwhere = " and lower(ks.name) like '%".strtolower($search)."%'";
		}
		else if ($searchtype=="all" and !EMPTY($search)){ 
			$searchwhere = " and (lower(kp.name) like '%".strtolower($search)."%' or lower(kt.code) like '%".strtolower($search)."%' or lower(title) like '%".strtolower($search)."%' or lower(cc.name) like '%".strtolower($search)."%' or lower(ks.name) like '%".strtolower($search)."%' or lower(author) like '%".strtolower($search)."%' or lower(published_year) like '%".strtolower($search)."%')";
		}
		
		//echo $searchwhere;
		   
		
		$where = "and knowledge_type_id='$tipe'";
		
	 
// 		$table 		= "select kt.id, cc.name klasifikasi,ks.name subjek,kt.title,kt.published_year,kt.code,author,kp.name tipe,
// 		(
// select count(*) from knowledge_item_subject kis 
//  left join master_subject ms on ms.id=master_subject_id where curriculum_code='2016' and knowledge_item_id=kt.id limit 0,10) total 
// from knowledge_item kt
// 		left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
// 		left join classification_code cc on kt.classification_code_id=cc.id
// 		left join knowledge_type kp on kt.knowledge_type_id=kp.id where ks.active='1' and kp.active='1'
// 		$where $searchwhere ";
		
		 
// 			//echo $table;
		
// 		$table_count 		= "select kt.id, cc.name klasifikasi,ks.name subjek,kt.title,kt.published_year,kt.code,author,kp.name tipe
// from knowledge_item kt
// 		left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
// 		left join classification_code cc on kt.classification_code_id=cc.id
// 		left join knowledge_type kp on kt.knowledge_type_id=kp.id where ks.active='1' and kp.active='1'
// 		$where $searchwhere ";
		
// 		//echo $table_count;
// 		$colOrder 	= array(null,'tipe','code','title','klasifikasi','subjek','author','published_year','total',null); //set column field database for datatable orderable
// 		$colSearch 	= array('tipe','code','title','klasifikasi','subjek','author','published_year','total');  //set column field database for datatable
// 		$order 		= "order by tipe,published_year desc,title asc";  

		
		// $this->datatables->set_table($table);
		// $this->datatables->set_table_count($table_count);
		// $this->datatables->set_col_order($colOrder);
		// $this->datatables->set_col_search($colSearch);
		// $this->datatables->set_order($order);

		$columns = array( 
			array( 'db' => 'tipe', 'dt' => 1 ),
			array( 'db' => 'codes', 'dt' => 2 ),
			array( 'db' => 'title', 'dt' => 3 ),
			array( 'db' => 'klasifikasi', 'dt' => 4 ),
			array( 'db' => 'subjek', 'dt' => 5 ),
			array( 'db' => 'author', 'dt' => 6 ),
			array( 'db' => 'published_year', 'dt' => 7 )
		);

		$this->datatables_custom->set_cols($columns);
		$param	= $this->datatables_custom->query();		

		$param['where'] = $where.' '.$searchwhere; 
		$result = $this->KatalogMkModel->dtquery($param)->result();
		$filter = $this->KatalogMkModel->dtfiltered();
		$total	= $this->KatalogMkModel->dtcount();
		$output = $this->datatables_custom->output($total, $filter);
		 
		
		// $list = $this->datatables->get_datatables(); 
		// $data = array(); 
		 
		$no = $_POST['start'];
		foreach ($result as $dt) { 
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $dt->tipe;
			$row[] = $dt->codes;
			$row[] = $dt->title; 
			$row[] = $dt->klasifikasi;
			$row[] = $dt->subjek;
			$row[] = $dt->author; 
			$row[] = $dt->published_year; 
			$row[] = ($dt->total==10?'>= 10 MK' : $dt->total.' MK');
			$row[] = '<div class="mapping"><a class="btn btn-sm btn-primary " target="_blank" href="index.php/katalogmk/mapping/'.strtolower($dt->id).'" title="'.getLang('mapping').'"><i class="fa fa-file"></i></a></div>'; 
			$output['data'][] = $row;
			// $data[] = $row;
		}

		// $output = array(
		// 				"draw" => $_POST['draw'], 
		// 				"recordsTotal" => $this->datatables->count_all_count(),
		// 				"recordsFiltered" => $this->datatables->count_filtered_count(),
		// 				"data" => $data,
		// 		);
		echo json_encode($output);
	} 

    public function mapping($id="") {  
		$data['katalog'] = $this->KatalogMkModel->getbyid($id)->row();
		if (!$data['katalog']) redirect('index.php/katalogmk');
		
		$data['curriculum_year'] 	= $this->KatalogMkModel->getcurriculumyear()->result();
		$data['study_program'] 		= $this->KatalogMkModel->getstudyprogram()->result();
		$data['menu'] 	= 'katalogmk/katalogmk_mapping'; 
		$this->load->view('theme', $data);
    }  
	
	public function ajax_list()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id 				= $this->input->post("id");
		$curriculum_year 	= $this->input->post("curriculum_year");
		$study_program 		= $this->input->post("study_program");
		
		if ($study_program!="all") $where = "and course_code ='$study_program'";
		else $where = "";
		$table 		= "select * from knowledge_item_subject kis left join master_subject ms on ms.id=kis.master_subject_id left join t_mst_prodi on course_code=c_kode_prodi where knowledge_item_id='$id' and curriculum_code='$curriculum_year' $where";  
		$colOrder 	= array(null,'code','name'); //set column field database for datatable orderable
		$colSearch 	= array('code','name'); //set column field database for datatable
		$order 		= "order by name asc"; // default order 
		
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
			$row[] = '<div class="text-center"><input type="checkbox" value="'.$dt->master_subject_id.'" name="inp[id][]"></div>';
			$row[] = $dt->code;
			$row[] = '<div class="text-left"><span style="color:green">'.$dt->NAMA_PRODI.'</span><br>'.$dt->name.'</div>';
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
	
	public function ajax_not_list()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id 				= $this->input->post("id");
		$curriculum_year 	= $this->input->post("curriculum_year");
		$study_program 		= $this->input->post("study_program");
		
		if ($study_program!="all") $where = "and course_code ='$study_program'";
		else $where = "";
		
		$table 		= "select * from master_subject left join t_mst_prodi on course_code=c_kode_prodi where curriculum_code='$curriculum_year' and id not in (select master_subject_id from knowledge_item_subject where knowledge_item_id='$id') $where"; 
		$colOrder 	= array(null,'code','name'); //set column field database for datatable orderable
		$colSearch 	= array('code','name'); //set column field database for datatable
		$order 		= "order by name asc"; // default order 
		
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
			$row[] = '<div class="text-center"><input type="checkbox" value="'.$dt->id.'" name="inp[id][]"></div>';
			$row[] = $dt->code;
			$row[] = '<div class="text-left"><span style="color:green">'.$dt->NAMA_PRODI.'</span><br>'.$dt->name.'</div>';
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
	
	public function insert_course()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id = $this->input->post('id'); 
		$inp = $this->input->post('inp'); 
		foreach($inp['id'] as $row){
			$item['master_subject_id']		= $row;
			$item['knowledge_item_id']		= $id;
			if(!$this->KatalogMkModel->checkExisting($row,$id)->row()) $this->KatalogMkModel->add($item);
		}
		echo json_encode(array("status" => TRUE));
	}
	 
	public function delete_course()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id = $this->input->post('id');
		$inp = $this->input->post('inp');
		
		foreach($inp['id'] as $row){
			$this->KatalogMkModel->delete($row,$id);
		}
		echo json_encode(array("status" => TRUE));
	}
	
	
}

?>