<?php

class Membership extends CI_Controller {
 
    function __construct() {
        parent::__construct(); 
		$this->load->model('MembershipModel');
		// $this->load->library('PHPExcel');
		//$this->load->library('PHPExcel/IOFactory');

    }

    function index() {  
		

		$data['faculty']	= $this->MembershipModel->getallfakultas()->result(); 
		$data['menu'] 		= 'membership/index'; 
		$this->load->view('theme',$data);
    }
	 
	public function ajax_index()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$type 		= $this->input->post('type');
		$grow_year 		= $this->input->post('grow_year');
		$faculty 		= $this->input->post('faculty');
		$access 		= $this->input->post('access');

		if($faculty!="alls"){
			$where .=" and tmf.c_kode_fakultas='$faculty'";
		}

		if($type=="civitas"){
			$where .=" and member_type_id in (1,2,3,4,5,6,7,8,9,10,25)";
		}
		else if($type=="umum"){
			$where .=" and member_type_id in (19,20,21,22,23,24,26,27,28,29,30,31)";

		}

		

		if($access=='yes') $where .= "and master_data_uuid is not null"; 
		else if($access=='no')  $where .= "and master_data_uuid is null";
		
		if($grow_year!="alls") $where .=" and YEAR(m.created_at)='$grow_year'";

		$table 		= 
					"select master_data_fullname, master_data_number, nama_prodi,nama_fakultas,mt.name, master_data_institution,m.created_at,master_data_email,master_data_mobile_phone,master_data_uuid
					from member m
					left join member_type mt on mt.id=member_type_id
					left join t_mst_prodi tmp on c_kode_prodi=master_data_course
					left join t_mst_fakultas tmf on tmp.C_KODE_FAKULTAS=tmf.C_KODE_FAKULTAS where status='1' $where
					order by created_at desc,master_data_fullname asc
					";

					//  echo $table;
		$colOrder 	= array(null,'nama_fakultas','nama_prodi','master_data_number','master_data_fullname','name','master_data_institution','master_data_mobile_phone','master_data_email','created_at','master_data_uuid',null); //set column field database for datatable orderable
		$colSearch 	= array('nama_fakultas','nama_prodi','master_data_number','master_data_fullname','name','master_data_institution','master_data_mobile_phone','master_data_email','created_at','master_data_uuid'); //set column field database for datatable
		$order 		= "order by created_at desc,master_data_fullname asc"; // default order 
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order);
		
		$list = $this->datatables->get_datatables();
		//print_r($list);
		$data = array(); 
		foreach ($list as $dt) {
			 
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $dt->nama_fakultas;
			$row[] = $dt->nama_prodi;
			$row[] = $dt->master_data_number;
			$row[] = $dt->master_data_fullname;
			$row[] = $dt->name;
			$row[] = $dt->master_data_institution;
			$row[] = $dt->master_data_email;
			$row[] = $dt->master_data_mobile_phone;
			$row[] = $dt->created_at;

			if($dt->master_data_uuid!="") $row[] = '<div><button class="btn btn-sm btn-success btn-embossed" ><i class="fa fa-check"></i></button></div>'; 
			else  $row[] = '';

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

    function total($type,$id) {
		$data['id'] 		= $id;
		$data['member']		= $this->MembershipModel->getmember($id)->row();
		$data['menu'] 		= 'cumlaude/cumlaude_'.$type; 
		$this->load->view('theme',$data);
    }


	public function ajax_visitor()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id 		= $this->input->post('id');

		$table 		= "select name,attended_at from member_attendance 
		left join item_location il on il.id=item_location_id
		where member_id='$id'
		order by attended_at desc";

					// echo $table;
		$colOrder 	= array(null,'name','attended_at',null); //set column field database for datatable orderable
		$colSearch 	= array('name','attended_at'); //set column field database for datatable
		$order 		= "order by attended_at desc"; // default order 
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order);
		
		$list = $this->datatables->get_datatables();
		//print_r($list);
		$data = array(); 
		foreach ($list as $dt) {
			 
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $dt->name;
			
			$temp = explode(" ",$dt->attended_at);
			$begintime = new DateTime($temp[1]);
			$endtime = new DateTime('08:00');

			if($begintime < $endtime) {
				$time = date('Y-m-d H:i',strtotime('+7 hours',strtotime($dt->attended_at)));
			}
			else $time = $dt->attended_at;
			
			$row[] = $time;
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

	public function ajax_rent()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id 		= $this->input->post('id');

		$table 		= "select kit.title,author,ks.code,rent_date,return_date from rent  
		left join knowledge_item kit on kit.id=ks.knowledge_item_id
		where member_id='$id'
		order by rent_date desc";

					// echo $table;
		$colOrder 	= array(null,'title','author','code','rent_date','return_date',null); //set column field database for datatable orderable
		$colSearch 	= array('title','author','code','rent_date','return_date'); //set column field database for datatable
		$order 		= "order by rent_date desc"; // default order 
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order);
		
		$list = $this->datatables->get_datatables();
		//print_r($list);
		$data = array(); 
		foreach ($list as $dt) {
			 
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $dt->title;
			$row[] = $dt->author;
			$row[] = $dt->code;
			$row[] = $dt->rent_date;
			$row[] = $dt->return_date;
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

	public function ajax_room()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id 		= $this->input->post('id');

		$table 		= "
		select room_name, bk_reason, bk_startdate, bk_enddate from room.booking
		left join room.room r on room_id=bk_room_id
		where bk_memberid='$id'
		order by bk_startdate desc";

					// echo $table;
		$colOrder 	= array(null,'room_name','bk_reason','bk_startdate','bk_enddate',null); //set column field database for datatable orderable
		$colSearch 	= array('room_name','bk_reason','bk_startdate','bk_enddate'); //set column field database for datatable
		$order 		= "order by bk_startdate desc"; // default order 
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order);
		
		$list = $this->datatables->get_datatables();
		//print_r($list);
		$data = array(); 
		foreach ($list as $dt) {
			 
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $dt->room_name; 
			$row[] = $dt->bk_reason; 
			$row[] = $dt->bk_startdate; 
			$row[] = $dt->bk_enddate; 
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

	public function ajax_digital()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id 		= $this->input->post('id');

		$table 		= " 
		select kt.name,kit.title,author,wd.name nama_file,wd.created_at
		from knowledge_item_file_readonly wd
		left join knowledge_item kit on kit.id=knowledge_item_id
		left join knowledge_type kt on kt.id=kit.knowledge_type_id
		where member_id='$id'
		order by wd.created_at desc";

					// echo $table;
		$colOrder 	= array(null,'name','title','author','nama_file','created_at',null); //set column field database for datatable orderable
		$colSearch 	= array('name','title','author','nama_file','created_at'); //set column field database for datatable
		$order 		= "order by created_at desc"; // default order 
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order);
		
		$list = $this->datatables->get_datatables();
		//print_r($list);
		$data = array(); 
		foreach ($list as $dt) {
			 
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $dt->name; 
			$row[] = $dt->title; 
			$row[] = $dt->author; 
			$row[] = $dt->nama_file; 
			$row[] = $dt->created_at; 
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
	  
	
	public function totalcollection()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed'); 
		echo json_encode($this->MembershipModel->totalcollection($this->input->post('year'),$this->input->post('grow_year'),$this->input->post('faculty'))->row());
	}
	
	function mk($jurusan='',$tahun="") { 
	 	$jurusan			= strtoupper($jurusan);
		$data['jurusan']	= $this->MembershipModel->getjurbykodejur($jurusan)->row();
		$data['mk'] 		= $this->MembershipModel->totalsubject($jurusan,$tahun)->row(); 
		if(empty($data['jurusan'])) return false;
		 
		$data['tahun'] 			= $tahun; 
		$data['menu'] 			= 'cumlaude/bahanpustaka_mk'; 
		$this->load->view('theme', $data);
	}
	
	
	public function ajax_mk()
	{
		$jurusan 	= $this->input->post('jurusan'); 
		$tahun 		= $this->input->post('tahun');
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$year 		= $this->input->post('year');
		$table 		= "select *,SUBSTR(msu.code,-1) sks,(select count(*) from knowledge_item_subject kis 
					left join knowledge_item kt on knowledge_item_id=kt.id 
					left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
					left join knowledge_type kp on kt.knowledge_type_id=kp.id
					where ks.active='1' and kp.active='1' and knowledge_type_id=21 and kis.master_subject_id=msu.id) jmljudul,
					(select count(*) from knowledge_item_subject kis 
					left join knowledge_item kt on knowledge_item_id=kt.id 
					left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
					left join knowledge_type kp on kt.knowledge_type_id=kp.id
					where ks.active='1' and kp.active='1' and knowledge_type_id  in (1, 2, 3, 33, 40, 41, 59, 65) and kis.master_subject_id=msu.id) jmljudul_fisik
					from master_subject msu where course_code ='$jurusan' AND msu.curriculum_code = '$tahun'";
					
		$colOrder 	= array(null,'code','semester','name','sks','jmljudul_fisik','jmljudul',null); //set column field database for datatable orderable
		$colSearch 	= array('code','semester','name','sks','jmljudul_fisik','jmljudul'); //set column field database for datatable
		$order 		= "order by semester"; // default order 
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order);
		
		$list = $this->datatables->get_datatables();
		//print_r($list);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $dt) {
			$id 				= array('id' => $dt->id, 'kodemk' => $dt->code, 'namamk' => $dt->name);
			$ids				= urlencode(base64_encode(serialize($id)));
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $dt->code;
			$row[] = $dt->semester;
			$row[] = $dt->name;
			$row[] = '<div class="td_right">'.$dt->sks.' '.getLang('sks').'</div>';
			$row[] = '<div class="td_right">'.$dt->jmljudul_fisik.' '.getLang('title').'</div>';
			$row[] = '<div class="td_right">'.$dt->jmljudul.' '.getLang('title').'</div>';
			$row[] = '<div><a class="btn btn-sm btn-danger btn-embossed" href="javascript:;"  onclick="viewBuku('."'".$ids."'".','."'book'".')"  title="'.getLang('Detail Buku Tercetak').'"><i class="fa fa-file"></i></a><a class="btn btn-sm btn-danger btn-embossed" href="javascript:;"  onclick="viewBuku('."'".$ids."'".','."'ebook'".')"  title="'.getLang('Detail E-Book').'"><i class="fa fa-file"></i></a></div>';
		
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
	
	function viewbuku() {
		$data['mk'] 	 		= unserialize(base64_decode(urldecode($this->input->post('id')))); 
		$type 	 		= $this->input->post('type');
		
		$data['type'] 	 		= ($type=='book'?'Buku Tercetak':'E-Book');
		
		if(!is_array($data['mk'])) return false;
		 
		$data['bukuref']		= $this->MembershipModel->getbukuref($data['mk']['id'], '', '',$type)->result(); 
		
        $this->load->view('cumlaude/bahanpustaka_view_buku', $data); 
		 
	}
	
}

?>