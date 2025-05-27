<?php

class Digitalaccess extends CI_Controller {
 
    function __construct() {
        parent::__construct(); 
		$this->load->model('DigitalaccessModel');
		$this->load->library('PHPExcel');
		//$this->load->library('PHPExcel/IOFactory');

    }

    function index() {  
	
		$data['faculty']	= $this->DigitalaccessModel->getallfakultas()->result(); 
		$data['menu'] 		= 'digitalaccess/index'; 
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

		

		if($access=='mobile') $where .= "and wd.type is not null"; 
		else if($access=='web')  $where .= "and wd.type is null";

		
		if($grow_year!="alls") $where .=" and YEAR(wd.created_at)='$grow_year'";

		$table 		= " 
		select kt.name jeniskatalog,kit.title,author,wd.name nama_file,master_data_institution,wd.created_at,wd.type jenis_akses
		from knowledge_item_file_readonly wd
		left join member m on m.id=wd.member_id
		left join member_type mt on mt.id=member_type_id
		left join t_mst_prodi tmp on c_kode_prodi=master_data_course
		left join t_mst_fakultas tmf on tmp.C_KODE_FAKULTAS=tmf.C_KODE_FAKULTAS
		left join knowledge_item kit on kit.id=knowledge_item_id
		left join knowledge_type kt on kt.id=kit.knowledge_type_id
		where 1=1 $where
		order by wd.created_at desc";

					// echo $table;
		$colOrder 	= array(null,'master_data_fullname','mtname','master_data_institution','jeniskatalog','title','author','nama_file','created_at','jenis_akses',null); //set column field database for datatable orderable
		$colSearch 	= array('master_data_fullname','mtname','master_data_institution','jeniskatalog','title','author','nama_file','created_at','jenis_akses'); //set column field database for datatable
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
			$row[] = $dt->master_data_fullname;
			$row[] = $dt->mtname;
			$row[] = $dt->master_data_institution;
			$row[] = $dt->jeniskatalog; 
			$row[] = $dt->title; 
			$row[] = $dt->author; 
			$row[] = $dt->nama_file; 
			$row[] = $dt->created_at; 
			if($dt->jenis_akses==""){
				$row[] = 'web'; 
			}
			else $row[] = $dt->type; 
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
		$data['member']		= $this->DigitalaccessModel->getmember($id)->row();
		$data['menu'] 		= 'cumlaude/cumlaude_'.$type; 
		$this->load->view('theme',$data);
    }


	 
	
	public function totalcollection()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed'); 
		echo json_encode($this->DigitalaccessModel->totalcollection($this->input->post('year'),$this->input->post('grow_year'),$this->input->post('faculty'))->row());
	}
	
	function mk($jurusan='',$tahun="") { 
	 	$jurusan			= strtoupper($jurusan);
		$data['jurusan']	= $this->DigitalaccessModel->getjurbykodejur($jurusan)->row();
		$data['mk'] 		= $this->DigitalaccessModel->totalsubject($jurusan,$tahun)->row(); 
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
		 
		$data['bukuref']		= $this->DigitalaccessModel->getbukuref($data['mk']['id'], '', '',$type)->result(); 
		
        $this->load->view('cumlaude/bahanpustaka_view_buku', $data); 
		 
	}
	
}

?>