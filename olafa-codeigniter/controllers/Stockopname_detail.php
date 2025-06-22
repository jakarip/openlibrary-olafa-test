<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stockopname_detail extends CI_Controller {

    public function __construct(){ 
        parent::__construct();
		 
		if(!$this->session->userdata('login_room')) redirect(url_admin(), 'refresh'); 
		if(!$this->session->userdata('memberid')) redirect(url_admin(), 'refresh'); 
        $this->load->model('Stockopname_Model','sm');  
    }

    public function id($id){ 
		if(checkCurrentMenubyUserGroup()=="no") redirect(url_admin(), 'refresh');  
        $data['menu']	= 'stockopname_detail'; 
		
        $data['dt'] 	= $this->sm->getbyid($id)->row();
		$data['id'] 	= $id;
        $data['admin'] 	= $this->sm->getadmin()->result();
        $data['location'] 	= $this->sm->getlocation()->result();
        $data['type'] 	= $this->sm->getknowledge_type_id($id)->result();
 
        $this->load->view('theme',$data);
    }
 
    public function ajax_data(){
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

		$session = $this->session->all_userdata();
		$status = $this->input->post("status");
		$status_openlibrary = $this->input->post("status_openlibrary");
		$location_openlibrary = $this->input->post("location_openlibrary");
		$member = $this->input->post("member");
		$type = $this->input->post("type");
		$location = $this->input->post("location");
		$kondisi = $this->input->post("kondisi");
		$id = $this->input->post("id");
		$where = "";
		if($status_openlibrary!="") $where .= "and ks.status in (".implode(",",$status_openlibrary).")"; 
		if($status!="") $where .= "and sos_status in (".implode(",",$status).")"; 
		if($member!="Semua") $where .= "and m.id='$member'";
		if($type!="Semua") $where .= "and kit.knowledge_type_id='$type'";
		if($location_openlibrary!="") $where .= "and ks.item_location_id in (".implode(",",$location_openlibrary).")";
		if($location!="") $where .= "and sos_id_location in (".implode(",",$location).")";
		if($kondisi=="status") $where .= "and ks.status!=sos_status";
		if($kondisi=="lokasi") $where .= "and ks.item_location_id!=sos_id_location";
		 
		// echo $where;
		// GROUP_CONCAT(concat(substr(title,1,50),' ...') SEPARATOR '<br><br> ') item_title,
		$person = ""; 
			$table 		= "select master_data_fullname,master_data_user,title,sos.*,ks.code kscode,kit.code kitcode,ks.status,kt.name,cc.code cccode,il.name lokasi_openlibrary, il2.name lokasi from so_stock sos 
			left join so_edition on so_id=sos_id_so
			left join knowledge_stock ks on ks.id=sos_id_stock
			left join knowledge_item kit on kit.id=knowledge_item_id
			left join classification_code cc on cc.id=classification_code_id
			left join item_location il on il.id=ks.item_location_id
			left join item_location il2 on il2.id=sos_id_location
			left join knowledge_type kt on kt.id=kit.knowledge_type_id
			left join member m on m.id=sos_id_user where sos_id_so='$id' $where "; // default order    
		$order 		= "order by kscode desc"; // default order   

		echo $person;
		//			left join telu8381_openlibrarys.member_type on member_type_id = member_type.id 
		$colOrder 	= array(null,'sos_date','master_data_fullname','name','title','cccode','kscode','kitcode','lokasi_openlibrary','lokasi','status','sos_status','sos_filename',null); //set column field database for datatable orderable
		$colSearch 	= array('sos_date','master_data_fullname','name','title','cccode','kscode','kitcode','lokasi_openlibrary','lokasi','status','sos_status','sos_filename'); //set column field database for datatable
			 
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder); 
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order); 
		
		$list = $this->datatables->get_datatables();
		
        $data = array();
        $no = $_POST['start'];
		$status_option = array(
			'0'=> '',
			'1'=>'Tersedia',
			'2'=>'Dipinjam',
			'3'=>'Rusak',
			'4'=>'Hilang',
			'5'=>'Expired',  
			'6'=>'Hilang Diganti',
			'7'=>'Sedang Diproses',
			'8'=>'Cadangan',
			'9'=>'Weeding',
		);
        foreach ($list as $dt){
            $no++;
            $row = array();
            $row[] = '<div class="text-center">'.$no.'</div>';
            $row[] = convert_format_date($dt->sos_date);
            $row[] = ucwords(strtolower($dt->master_data_user." - ".$dt->master_data_fullname));  
            $row[] = $dt->name; 
            $row[] = $dt->title;
            $row[] = $dt->cccode;  
            $row[] = $dt->kitcode;  
            $row[] = $dt->kscode;  
            $row[] = $dt->lokasi_openlibrary; 
            $row[] = $dt->lokasi; 
            $row[] = $status_option[$dt->status];   
            $row[] = $status_option[$dt->sos_status];   
            $row[] = $dt->sos_filename; 
            $row[] = (($session['memberid']==$dt->sos_id_user and $dt->sos_status=='1')?'<div class="btn-group"><button type="button" class="btn btn-sm btn-danger" title="'.getLang("delete").'" onclick="del(\''.$dt->sos_id.'\')"><i class="fa fa-trash-o"></i></button></div>':'');
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

	public function ajax_duplicate(){
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

		$session = $this->session->all_userdata();
		
		$id = $this->input->post("id");  
		$type_duplicate = $this->input->post("type_duplicate");
		$status_openlibrary_duplicate = $this->input->post("status_openlibrary_duplicate");
		
		$where = "";
		if($type_duplicate!="Semua") $where .= "and kit.knowledge_type_id='$type_duplicate'";
		if($status_openlibrary_duplicate!="") $where .= "and ks.status in (".implode(",",$status_openlibrary_duplicate).")"; 

		
		// GROUP_CONCAT(concat(substr(title,1,50),' ...') SEPARATOR '<br><br> ') item_title,
		$person = ""; 
			$table 		= "select * from( select GROUP_CONCAT(DISTINCT master_data_user
			ORDER BY  master_data_user ASC SEPARATOR ', ') fullname,title,ks.code kscode,kit.code kitcode,kt.name,cc.code cccode, GROUP_CONCAT(DISTINCT sos_filename
			ORDER BY  sos_filename ASC SEPARATOR ', ') filename,count(*) total from so_stock sos 
			left join knowledge_stock ks on ks.id=sos_id_stock
			left join knowledge_item kit on kit.id=knowledge_item_id
			left join classification_code cc on cc.id=classification_code_id
			left join knowledge_type kt on kt.id=kit.knowledge_type_id
			left join member m on m.id=sos_id_user where sos_id_so='$id' $where group by sos_id_stock) a where total > 1"; // default order    
		$order 		= "order by kscode desc"; // default order   
		// echo $table;
		//			left join telu8381_openlibrarys.member_type on member_type_id = member_type.id 
		$colOrder 	= array(null,'total','fullname','name','title','cccode','kscode','kitcode','filename',null); //set column field database for datatable orderable
		$colSearch 	= array('total','fullname','name','title','cccode','kscode','kitcode','filename'); //set column field database for datatable
			 
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder); 
		$this->datatables->set_col_search($colSearch); 
		$this->datatables->set_order($order); 
		
		$list = $this->datatables->get_datatables();
		
        $data = array();
        $no = $_POST['start'];
		$status_option = array(
			'1'=>'Tersedia',
			'2'=>'Dipinjam',
			'3'=>'Rusak',
			'4'=>'Hilang',
			'5'=>'Expired',
			'6'=>'Hilang Diganti',
			'7'=>'Sedang Diproses',
			'8'=>'Cadangan',
			'9'=>'Weeding',
		);
        foreach ($list as $dt){
            $no++;
            $row = array();
            $row[] = '<div class="text-center">'.$no.'</div>'; 
            $row[] = $dt->total; 
            $row[] = ucwords(strtolower($dt->fullname));   
            $row[] = $dt->name; 
            $row[] = $dt->title;
            $row[] = $dt->cccode;
            $row[] = $dt->kitcode;   
            $row[] = $dt->kscode;  
            $row[] = $dt->filename; 
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

	public function ajax_statistik(){
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

		$session = $this->session->all_userdata();
		
		$id = $this->input->post("id"); 
		$statistik = $this->input->post("statistik"); 
		$rangedate = $this->input->post("rangedate"); 
		$show_status = $this->input->post("show_status"); 

		$temp = explode(" - ",$rangedate);
		$tempstart = explode("-",$temp[0]);
		$tempend = explode("-",$temp[1]);
		
		$start = $tempstart[2]."-".$tempstart[1]."-".$tempstart[0];
		$end = $tempend[2]."-".$tempend[1]."-".$tempend[0];

		$where = "";
		$location = $this->input->post("location");
		if($location!="") $where .= "and ks.item_location_id in (".implode(",",$location).")";
		if($rangedate!="") $where .="and ks.entrance_date BETWEEN '".$start." 00:00:00' AND '".$end."  23:59:59' ";  

		if($statistik=='so'){ 

			if($show_status=='openlib'){
				$table = "select
				(
					select count(distinct kit.id) total from knowledge_item kit  
					left join knowledge_stock ks on kit.id=knowledge_item_id
					 join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
					where  kit.knowledge_type_id=aa.ktid $where
				)'judul',
				(
					select count(distinct kit.id) total from knowledge_item kit  
					left join knowledge_stock ks on kit.id=knowledge_item_id
					 join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
					where  status='1' and kit.knowledge_type_id=aa.ktid $where
				)'judul1',
				(
					select count(distinct kit.id) total from knowledge_item kit  
					left join knowledge_stock ks on kit.id=knowledge_item_id
					 join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
					where  status='2' and kit.knowledge_type_id=aa.ktid $where
				)'judul2',
				(
					select count(distinct kit.id) total from knowledge_item kit  
					left join knowledge_stock ks on kit.id=knowledge_item_id
					 join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
					where  status='3' and kit.knowledge_type_id=aa.ktid $where
				)'judul3',
				(
					select count(distinct kit.id) total from knowledge_item kit  
					left join knowledge_stock ks on kit.id=knowledge_item_id
					 join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
					where  status='4' and kit.knowledge_type_id=aa.ktid $where
				)'judul4',
				(
					select count(distinct kit.id) total from knowledge_item kit  
					left join knowledge_stock ks on kit.id=knowledge_item_id
					 join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
					where  status='5' and kit.knowledge_type_id=aa.ktid $where
				)'judul5',
				(
					select count(distinct kit.id) total from knowledge_item kit  
					left join knowledge_stock ks on kit.id=knowledge_item_id
					 join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
					where  status='6' and kit.knowledge_type_id=aa.ktid $where
				)'judul6',
				(
					select count(distinct kit.id) total from knowledge_item kit  
					left join knowledge_stock ks on kit.id=knowledge_item_id
					 join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
					where  status='7' and kit.knowledge_type_id=aa.ktid $where
				)'judul7',
				(
					select count(distinct kit.id) total from knowledge_item kit  
					left join knowledge_stock ks on kit.id=knowledge_item_id
					 join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
					where  status='8' and kit.knowledge_type_id=aa.ktid $where
				)'judul8',
				(
					select count(distinct kit.id) total from knowledge_item kit  
					left join knowledge_stock ks on kit.id=knowledge_item_id
					 join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
					where  status='9' and kit.knowledge_type_id=aa.ktid $where
				)'judul9',
				aa.* 
				from (select kt.name, kt.id ktid,
					SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) AS status1,
					SUM(CASE WHEN status = '2' THEN 1 ELSE 0 END) AS status2,
					SUM(CASE WHEN status = '3' THEN 1 ELSE 0 END) AS status3,
					SUM(CASE WHEN status = '4' THEN 1 ELSE 0 END) AS status4,
					SUM(CASE WHEN status = '5' THEN 1 ELSE 0 END) AS status5,
					SUM(CASE WHEN status = '6' THEN 1 ELSE 0 END) AS status6,
					SUM(CASE WHEN status = '7' THEN 1 ELSE 0 END) AS status7,
					SUM(CASE WHEN status = '8' THEN 1 ELSE 0 END) AS status8,
					SUM(CASE WHEN status = '9' THEN 1 ELSE 0 END) AS status9
					from knowledge_type kt
					left join knowledge_item kit on kit.knowledge_type_id=kt.id
					left join knowledge_stock ks on kit.id=knowledge_item_id $where
					join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id'
					LEFT JOIN knowledge_subject kss ON kit.knowledge_subject_id = kss.id 
					where kt.id not in (4,5,6,21,24,25,47,49,51,52,55,62,70,73,75,79) and kss.active='1'
					group by kt.id)aa";
			}
			else {
				$table = "select
				(
					select count(distinct kit.id) total from knowledge_item kit  
					left join knowledge_stock ks on kit.id=knowledge_item_id
					join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
					where kit.knowledge_type_id=aa.ktid $where
				)'judul',
				(
					select count(distinct kit.id) total from knowledge_item kit  
					left join knowledge_stock ks on kit.id=knowledge_item_id
					left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
					where  sos_status='1' and kit.knowledge_type_id=aa.ktid $where
				)'judul1',
				(
					select count(distinct kit.id) total from knowledge_item kit  
					left join knowledge_stock ks on kit.id=knowledge_item_id
					left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
					where  sos_status='2' and kit.knowledge_type_id=aa.ktid $where
				)'judul2',
				(
					select count(distinct kit.id) total from knowledge_item kit  
					left join knowledge_stock ks on kit.id=knowledge_item_id
					left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
					where  sos_status='3' and kit.knowledge_type_id=aa.ktid $where
				)'judul3',
				(
					select count(distinct kit.id) total from knowledge_item kit  
					left join knowledge_stock ks on kit.id=knowledge_item_id
					left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
					where  sos_status='4' and kit.knowledge_type_id=aa.ktid $where
				)'judul4',
				(
					select count(distinct kit.id) total from knowledge_item kit  
					left join knowledge_stock ks on kit.id=knowledge_item_id
					left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
					where  sos_status='5' and kit.knowledge_type_id=aa.ktid $where
				)'judul5',
				(
					select count(distinct kit.id) total from knowledge_item kit  
					left join knowledge_stock ks on kit.id=knowledge_item_id
					left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
					where  sos_status='6' and kit.knowledge_type_id=aa.ktid $where
				)'judul6',
				(
					select count(distinct kit.id) total from knowledge_item kit  
					left join knowledge_stock ks on kit.id=knowledge_item_id
					left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
					where  sos_status='7' and kit.knowledge_type_id=aa.ktid $where
				)'judul7',
				(
					select count(distinct kit.id) total from knowledge_item kit  
					left join knowledge_stock ks on kit.id=knowledge_item_id
					left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
					where  sos_status='8' and kit.knowledge_type_id=aa.ktid $where
				)'judul8',
				(
					select count(distinct kit.id) total from knowledge_item kit  
					left join knowledge_stock ks on kit.id=knowledge_item_id
					left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
					where  sos_status='9' and kit.knowledge_type_id=aa.ktid $where
				)'judul9',
				aa.* 
				from (select kt.name, kt.id ktid,
					SUM(CASE WHEN sos_status = '1' THEN 1 ELSE 0 END) AS status1,
					SUM(CASE WHEN sos_status = '2' THEN 1 ELSE 0 END) AS status2,
					SUM(CASE WHEN sos_status = '3' THEN 1 ELSE 0 END) AS status3,
					SUM(CASE WHEN sos_status = '4' THEN 1 ELSE 0 END) AS status4,
					SUM(CASE WHEN sos_status = '5' THEN 1 ELSE 0 END) AS status5,
					SUM(CASE WHEN sos_status = '6' THEN 1 ELSE 0 END) AS status6,
					SUM(CASE WHEN sos_status = '7' THEN 1 ELSE 0 END) AS status7,
					SUM(CASE WHEN sos_status = '8' THEN 1 ELSE 0 END) AS status8,
					SUM(CASE WHEN sos_status = '9' THEN 1 ELSE 0 END) AS status9
					from knowledge_type kt
					left join knowledge_item kit on kit.knowledge_type_id=kt.id
					left join knowledge_stock ks on kit.id=knowledge_item_id $where
					left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id'
					LEFT JOIN knowledge_subject kss ON kit.knowledge_subject_id = kss.id 
					where kt.id not in (4,5,6,21,24,25,47,49,51,52,55,62,70,73,75,79) and kss.active='1'
					group by kt.id)aa";
				}

			

		}
		else { 
			$person = "";
			$table = "select
			(
				select count(distinct kit.id) total from knowledge_item kit  
				left join knowledge_stock ks on kit.id=knowledge_item_id
				left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
				where sos_id_stock is null and kit.knowledge_type_id=aa.ktid $where
			)'judul',
			(
				select count(distinct kit.id) total from knowledge_item kit  
				left join knowledge_stock ks on kit.id=knowledge_item_id
				left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
				where  ks.status='1' and sos_id_stock is null and kit.knowledge_type_id=aa.ktid $where
			)'judul1',
			(
				select count(distinct kit.id) total from knowledge_item kit  
				left join knowledge_stock ks on kit.id=knowledge_item_id
				left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
				where  ks.status='2' and sos_id_stock is null and kit.knowledge_type_id=aa.ktid $where
			)'judul2',
			(
				select count(distinct kit.id) total from knowledge_item kit  
				left join knowledge_stock ks on kit.id=knowledge_item_id
				left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
				where  ks.status='3' and sos_id_stock is null and kit.knowledge_type_id=aa.ktid $where
			)'judul3',
			(
				select count(distinct kit.id) total from knowledge_item kit  
				left join knowledge_stock ks on kit.id=knowledge_item_id
				left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
				where  ks.status='4' and sos_id_stock is null and kit.knowledge_type_id=aa.ktid $where
			)'judul4',
			(
				select count(distinct kit.id) total from knowledge_item kit  
				left join knowledge_stock ks on kit.id=knowledge_item_id
				left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
				where  ks.status='5' and sos_id_stock is null and kit.knowledge_type_id=aa.ktid $where
			)'judul5',
			(
				select count(distinct kit.id) total from knowledge_item kit  
				left join knowledge_stock ks on kit.id=knowledge_item_id
				left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
				where  ks.status='6' and sos_id_stock is null and kit.knowledge_type_id=aa.ktid $where
			)'judul6',
			(
				select count(distinct kit.id) total from knowledge_item kit  
				left join knowledge_stock ks on kit.id=knowledge_item_id
				left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
				where  ks.status='7' and sos_id_stock is null and kit.knowledge_type_id=aa.ktid $where
			)'judul7',
			(
				select count(distinct kit.id) total from knowledge_item kit  
				left join knowledge_stock ks on kit.id=knowledge_item_id
				left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
				where  ks.status='8' and sos_id_stock is null and kit.knowledge_type_id=aa.ktid $where
			)'judul8',
			(
				select count(distinct kit.id) total from knowledge_item kit  
				left join knowledge_stock ks on kit.id=knowledge_item_id
				left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id' 
				where  ks.status='9' and sos_id_stock is null and kit.knowledge_type_id=aa.ktid $where
			)'judul9',
			aa.* 
			from (select kt.name, kt.id ktid,
			SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) AS status1,
			SUM(CASE WHEN status = '2' THEN 1 ELSE 0 END) AS status2,
			SUM(CASE WHEN status = '3' THEN 1 ELSE 0 END) AS status3,
			SUM(CASE WHEN status = '4' THEN 1 ELSE 0 END) AS status4,
			SUM(CASE WHEN status = '5' THEN 1 ELSE 0 END) AS status5, 
			SUM(CASE WHEN status = '6' THEN 1 ELSE 0 END) AS status6,
			SUM(CASE WHEN status = '7' THEN 1 ELSE 0 END) AS status7,
			SUM(CASE WHEN status = '8' THEN 1 ELSE 0 END) AS status8,
			SUM(CASE WHEN status = '9' THEN 1 ELSE 0 END) AS status9
			 from knowledge_type kt
			left join knowledge_item kit on kit.knowledge_type_id=kt.id
			left join knowledge_stock ks on kit.id=knowledge_item_id $where
			left join so_stock sos on sos_id_stock=ks.id and sos_id_so='$id'
			LEFT JOIN knowledge_subject kss ON kit.knowledge_subject_id = kss.id 
			where kt.id not in (4,5,6,21,24,25,47,49,51,52,55,62,70,73,75,79) and kss.active='1' and sos_id_stock is null 
			group by kt.id)aa";
		}
 
		// echo $table;

		
		// GROUP_CONCAT(concat(substr(title,1,50),' ...') SEPARATOR '<br><br> ') item_title,
		$order 		= "order by name asc"; // default order   
	//  echo $table; 
		//			left join telu8381_openlibrarys.member_type on member_type_id = member_type.id 
		$colOrder 	= array(null,'name','judul','judul1','status1','judul2','status2','judul3','status3','judul4','status4','judul5','status5','judul6','status6','judul7','status7','judul8','status8','judul9','status9'); //set column field database for datatable orderable
		$colSearch 	= array('total','judul','judul1','status1','judul2','status2','judul3','status3','judul4','status4','judul5','status5','judul6','status6','judul7','status7','judul8','status8','judul9','status9'); //set column field database for datatable
			 
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder); 
		$this->datatables->set_col_search($colSearch); 
		$this->datatables->set_order($order); 
		
		$list = $this->datatables->get_datatables();
		
        $data = array();
        $no = $_POST['start'];
		$status_option = array(
			'1'=>'Tersedia',
			'2'=>'Dipinjam',
			'3'=>'Rusak',
			'4'=>'Hilang',
			'5'=>'Expired',
			'6'=>'Hilang Diganti',
			'7'=>'Sedang Diproses',
			'8'=>'Cadangan',
			'9'=>'Weeding',
		);
        foreach ($list as $dt){
            $no++;
            $row = array();
            $row[] = '<div class="text-center">'.$no.'</div>'; 
            $row[] = $dt->name;  
            $row[] = $dt->judul; 
            $row[] = $dt->judul1; 
            $row[] = $dt->status1; 
            $row[] = $dt->judul2; 
            $row[] = $dt->status2;
            $row[] = $dt->judul3; 
            $row[] = $dt->status3;
            $row[] = $dt->judul4; 
            $row[] = $dt->status4;
            $row[] = $dt->judul5; 
            $row[] = $dt->status5;
            $row[] = $dt->judul6; 
            $row[] = $dt->status6;
            $row[] = $dt->judul7; 
            $row[] = $dt->status7; 
            $row[] = $dt->judul8;   
            $row[] = $dt->status8; 
            $row[] = $dt->judul9; 
            $row[] = $dt->status9;    
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

	public function ajax_not_so(){
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

		$session = $this->session->all_userdata();
		
		$id = $this->input->post("id"); 
		$classification_start = $this->input->post("classification_start"); 
		$classification_end = $this->input->post("classification_end"); 
		$where = "";
		$type = $this->input->post("type_not_so");
		if($type!="Semua") $where .= "and kit.knowledge_type_id='$type'";

		
		$status_openlibrary = $this->input->post("status_openlibrary2");
		if($status_openlibrary!="") $where .= "and ks.status in (".implode(",",$status_openlibrary).")"; 

		$location = $this->input->post("location");
		if($location!="") $where .= "and il.id in (".implode(",",$location).")";

		
		if($classification_start!="") $where .= "and cc.code>='$classification_start'";	
		else $where .= "and cc.code>='000'";

		if($classification_end!="") $where .= "and cc.code<='$classification_end'";	
		else $where .= "and cc.code<='9999999'";

		// GROUP_CONCAT(concat(substr(title,1,50),' ...') SEPARATOR '<br><br> ') item_title,
		$person = ""; 
			$table 		= "select distinct title,author,ks.code kscode,kit.code kitcode,kt.name,cc.code cccode,ks.status, il.name lokasi
			from knowledge_stock ks
			left join knowledge_item kit on kit.id=knowledge_item_id
			left join classification_code cc on cc.id=classification_code_id
			left join knowledge_type kt on kt.id=kit.knowledge_type_id
			left join item_location il on il.id=ks.item_location_id
			left join so_stock sos on sos.sos_id_stock=ks.id and sos_id_so='$id'
			where  kt.id not in (4,5,6,21,24,25,47,49,51,52,55,62,70,73,75,79) and sos_id_stock is null $where"; 
		$order 		= "order by kscode desc"; // default order   
 
		//			left join telu8381_openlibrarys.member_type on member_type_id = member_type.id 
		$colOrder 	= array(null,'lokasi','name','title','author','cccode','kscode','kitcode','status',null); //set column field database for datatable orderable
		$colSearch 	= array('lokasi','name','title','author','cccode','kscode','kitcode','status'); //set column field database for datatable
			//  echo $table;
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder); 
		$this->datatables->set_col_search($colSearch); 
		$this->datatables->set_order($order); 
		
		$list = $this->datatables->get_datatables();
		
        $data = array();
        $no = $_POST['start'];
		$status_option = array(
			'1'=>'Tersedia',
			'2'=>'Dipinjam',
			'3'=>'Rusak',
			'4'=>'Hilang',
			'5'=>'Expired',
			'6'=>'Hilang Diganti',
			'7'=>'Sedang Diproses',
			'8'=>'Cadangan',
			'9'=>'Weeding',
		);
        foreach ($list as $dt){
            $no++;
            $row = array();
            $row[] = '<div class="text-center">'.$no.'</div>';  
            $row[] = $dt->lokasi;
            $row[] = $dt->name;
            $row[] = $dt->title;
            $row[] = $dt->author;
            $row[] = $dt->cccode;
            $row[] = $dt->kitcode;   
            $row[] = $dt->kscode;  
            $row[] = $status_option[$dt->status]; 
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

	function deletes(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id     = $this->input->post('id');
		//echo $id;
		$this->sm->deleteTable('so_stock','sos_id',$id);
		echo json_encode(array("status" => TRUE));
	} 

	function delete_all(){
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id     = $this->input->post('id');
		//echo $id;
		$session = $this->session->all_userdata();
		$this->sm->deleteAllSOStock($session['memberid'],$id);
		echo json_encode(array("status" => TRUE));
	} 

	public function export_excel(){
		
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

		$status = $this->input->post("status");
		$where = "";
		if($status!="") $where = "where bds_status='$status'";
		 
		$data = $this->bm->getDataExport($where)->result();
 
		
		$no = 0;
		$row[$no] = array('Tanggal','No Pesanan','Username','NIK/NIM','Nama','Penerima','Alamat','Telp','Total Buku','No. Katalog','Barcode','Status','Alasan Ditolak');
        // $no = $_POST['start'];
        foreach ($data as $key=>$dt){
			$no++;
			$row[$no] = array(convert_format_date($dt->tanggal),$dt->bds_number,$dt->master_data_user, ucwords(strtolower($dt->master_data_number)),ucwords(strtolower($dt->master_data_fullname)),$dt->bds_receiver,$dt->bds_address,$dt->bds_phone,$dt->total_buku,$dt->item_code,$dt->stock_code,$dt->bds_status,$dt->bds_reason); 
			
		}
		echo json_encode($row);  
    } 
	
	public function save_image()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		if(!$this->input->post('inp')) return false;
		
		$id     = $this->input->post('id');
		$item     = $this->input->post('inp'); 

		$session = $this->session->all_userdata();

		// print_r($_FILES);

		if ($_FILES) {
			//Checking if file is selected or not
			if ($_FILES['file']['name'] != "") {
		  
			  //Checking if the file is plain text or not
			  if (isset($_FILES) && $_FILES['file']['type'] != 'text/plain') {
				//   echo "<span>File could not be accepted ! Please upload any '*.txt' file.</span>";
				  
				  echo json_encode(array("status" => FALSE,"message"=>"Bukan file .txt"));
				  exit();
			  }  
			  $item['sos_id_so'] = $id; 
			  $item['sos_id_user'] = $session['memberid'];
			  $item['sos_filename'] = $_FILES['file']['name'];
		 
			  $fileName = $_FILES['file']['tmp_name'];
			
			  //Throw an error message if the file could not be open
			  $file = fopen($fileName,"r") or exit("Unable to open file!");
			 
			  // Reading a .txt file line by line
			  $temp = array(); 
			  while(!feof($file)) {
				
				$temp[] = trim(strtolower(str_replace(array("\r", "\n", "'"),"",fgets($file))));
				// echo fgets($file). "";
			  }
			 
			  //Reading a .txt file character by character
			//   while(!feof($file)) {
			// 	echo fgetc($file);
			//   }
			  fclose($file);

			$temps = array_unique($temp);  
			$temps = "'".implode("','",$temps)."'"; 
			// $temps =  str_replace(array("\r", "\n"), '', $temps);
			$dts =  $this->sm->getstock($temps)->result();
 
		 
			$dts_temp = array();
			foreach($dts as $row){
				$dts_temp[] = strtolower($row->code);
			}

			// echo "<pre>";
			// print_r($dts_temp);
			// echo "</pre>";

			// echo "<pre>";
			// print_r($temp);
			// echo "</pre>";
			
			$ada = "";
			$tidakada = "";
			foreach($temp as $key => $row){
				if(in_array($row,$dts_temp)){
					$ada.= "<span style='color:green'>".($key+1).". ".$row."<br></span>";
				}
				else {
					if($row!="") $tidakada.= "<span style='color:red'>".($key+1).". ".$row."<br></span>";
				}
			}

			$counts = array_count_values($temp); 
			$duplicate = "";
			// Iterate through the counts to display duplicate values and their counts 
			foreach ($counts as $value => $count) { 
				if ($count > 1 and $value!="") { 
					$duplicate .= "$value &nbsp; &nbsp; &nbsp; &nbsp; Total Duplikat : $count<br>"; 
				} 
			}  
 
			$dt 	= $this->sm->getbystock($temps,$item);
			echo "<b>Duplikat : </b><br>".$duplicate."<br><br><b>List Tidak Ada di Database : </b><br>".$tidakada."<br><br><b>List Ada di Database : </b><br>".$ada;
		  }
		   
		} 
		 

		// if ($this->bm->edit($id, $item)) echo json_encode(array("status" => TRUE));
	} 

	public function save_manual()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		if(!$this->input->post('inp')) return false;
		
		$id     = $this->input->post('id');
		$item     = $this->input->post('inp');
		$barcode     = strtolower($this->input->post('barcode')); 

		$session = $this->session->all_userdata();

		 
		$dt 	= $this->sm->CheckBarcode($barcode)->row();

		if($dt){
			$temp[] = $barcode; 
			$item['sos_id_so'] = $id; 
			$item['sos_id_user'] = $session['memberid'];
			
			$dt 	= $this->sm->getbystockOne($temp,$item);
			echo $barcode." Ada\n"; 
		}
		else echo $barcode." Tidak Ada\n"; 
 
	} 
}
