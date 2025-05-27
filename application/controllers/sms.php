	<?php

class Sms extends CI_Controller {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct(); 
		$this->load->model('SmsModel');  
    }

    function grup($page = "") { 
		if (!$this->session->userdata('login')) redirect('');
		 
		$data['sms'] 	= $this->SmsModel->getgrup()->result();
		$data['total'] 	= $this->SmsModel->getgrup()->num_rows(); 
		$data['view'] 	= 'sms/grup'; 
		$data['site'] 	= 'setting grup sms';
		$this->load->view('main',$data); 
    }   
	
	function addgrup() { 
		if (!$this->session->userdata('login')) redirect('');
		 
		$data['view'] 	= 'sms/addgrup'; 
		$data['site'] 	= 'tambah grup';
		$this->load->view('main',$data); 
    }  
	
	function delGrup() { 
		if (!$this->session->userdata('login')) redirect('');
		$this->SmsModel->delGrup($_POST['id']);
    }  
	
	public function addGrupDb(){
		if (!$this->session->userdata('login')) redirect('');
		$data = array(
					"sg_name"		=> ucwords(strtolower($_POST['grup']))
		); 
		$id = $this->SmsModel->addGrup($data);
		 $member = $this->input->post('member');
		$mem = json_decode($member);
		print_r($mem);
		
		foreach($mem as $row){
			$name = explode(" - ",$row);
			$member = $this->SmsModel->getAnggota($name[1])->row();
			$data = array(
				"sg_id"		=> $id,
				"username"	=> $name[1],
				"fullname"	=> $member->fullname,
				"hp"		=> $member->hp
			); 
			$this->SmsModel->addGrupDetail($data);
		} 
		
		redirect('sms/grup');
	} 
	
	public function editGrup(){  
		if (!$this->session->userdata('login')) redirect('');
		$id = $_POST['id'];  
		$data['grup']	= $this->SmsModel->getgrupbyid($id)->row(); 
		$datas	= $this->SmsModel->getgrupdetailbysg($id)->result();
		 
		foreach($datas as $row){
			$data['member'][] = $row->fullname." - ".$row->username;
		}
		echo json_encode($data);
	} 
	
	public function editGrupDb(){
		if (!$this->session->userdata('login')) redirect('');
		$id			= $_POST['id'];
		
		$data = array(
					"pj_nomor"		=> ucwords(strtolower($_POST['nomor'])),
					"pj_dosen"		=> ucwords(strtolower($_POST['dosen'])),
					"pj_nik"		=> ucwords(strtolower($_POST['nik'])),
					"pj_jabatan"	=> ucwords(strtolower($_POST['jabatan'])),
					"pj_kaprodi"	=> ucwords(strtolower($_POST['kaprodi']))
		); 
		$this->SmsModel->editGrup($id,$data);
		redirect('sms/grup');
	} 
	
	//============
	function grupdetail($id) { 
		if (!$this->session->userdata('login')) redirect('');
		
		if ($this->SmsModel->getgrupbyid($id)->num_rows()==0) redirect('sms/grup');
		
		$row 	= $this->SmsModel->getgrupdetailbysg($id)->result(); 
		$detail = array();
		foreach ($row as $r){ 
			$member = $this->SmsModel->getAnggota($r->username)->row();
			$detail[] = array("id"=>$r->username,"nama"=>$member->fullname,"hp"=>$member->hp);
		}
		
		$data['detail'] = $detail;
		
		$data['total'] 		= $this->SmsModel->getgrupdetailbysg($id)->num_rows(); 
		$data['grup'] 	= $this->SmsModel->getgrupbyid($id)->row();
		$data['view'] 		= 'sms/grupdetail'; 
		$data['site'] 		= 'grup detail';
		$this->load->view('main',$data); 
    }    
	
	function delGrupDetail() { 
		if (!$this->session->userdata('login')) redirect('');
		$this->SmsModel->delGrupDetail($_POST['id']);
    }  
	
	public function addGrupDetailDb(){
		if (!$this->session->userdata('login')) redirect('');
		
		$data = array(
					"pd_mk"			=> ucwords(strtolower($_POST['mk'])),
					"pd_semester"	=> ucwords(strtolower($_POST['smt'])),
					"pd_judul"		=> ucwords(strtolower($_POST['judul'])),
					"pd_pengarang"	=> ucwords(strtolower($_POST['pengarang'])),
					"pd_penerbit"	=> ucwords(strtolower($_POST['penerbit'])),
					"pd_tahun"		=> ucwords(strtolower($_POST['tahun'])),
					"pd_tipe"		=> ucwords(strtolower($_POST['tipe'])),
					"pd_eks_awal"	=> '0',
					"pd_eks_akhir"	=> '0',
					"pd_status"		=> ucwords(strtolower('Diajukan Dosen')),
					"pd_pj_id"		=> ucwords(strtolower($_POST['id'])),
					"pd_bs_id"		=> '',
					"pd_nd_id"		=> ''
		); 
		$this->SmsModel->addGrupDetail($data);
		
		redirect('sms/grupdetail/'.$_POST['id']);
	} 
	
	public function editGrupDetail(){ 
		if (!$this->session->userdata('login')) redirect(''); 
		$id = $_POST['id'];  
		$data	= $this->SmsModel->getgrupdetailbyid($id)->row(); 
		echo json_encode($data);
	} 
	
	public function editGrupDetailDb(){
		if (!$this->session->userdata('login')) redirect('');
		$id			= $_POST['id'];
		
		$data = array(
					"pd_mk"			=> ucwords(strtolower($_POST['mk'])),
					"pd_semester"	=> ucwords(strtolower($_POST['smt'])),
					"pd_judul"		=> ucwords(strtolower($_POST['judul'])),
					"pd_pengarang"	=> ucwords(strtolower($_POST['pengarang'])),
					"pd_penerbit"	=> ucwords(strtolower($_POST['penerbit'])),
					"pd_tahun"		=> ucwords(strtolower($_POST['tahun'])),
					"pd_tipe"		=> ucwords(strtolower($_POST['tipe']))
		); 
		$this->SmsModel->editGrupDetail($id,$data);
		redirect('sms/grupdetail/'.$_POST['idparent']);
	} 
	
	function addmember() {  
		if (!$this->session->userdata('login')) redirect('');
		echo '<div class="form-group" id="minbook'.$_POST['id'].'">
				<label  class=" col-md-1 col-sm-1 col-xs-12" for="first-name">Nama<span class="required">*</span></label> 
				<div class="col-md-11 col-sm-11 col-xs-12">
					<input type="text" id="nama"name="nama['.$_POST['id'].']" required="required" class="form-control col-md-7 col-xs-12" placeholder="Nama">
				</div> 
			</div> ';
    }  
	
	function memberjson() {
		$search = $_REQUEST['q'];
		if (strlen($search)>=3){
				$data = $this->SmsModel->searchAnggota($search)->result();
				$member = array();
				foreach($data as $row){
					
					$member[] = $row->fullname." - ".$row->c_username;
				}
				echo json_encode($member); 
		}
		else echo json_encode("");
		
    }  
	
	//===========================================================================================================
	
	function nodin($page = "") { 
		if (!$this->session->userdata('login')) redirect('');
		 
		$data['sms'] 	= $this->SmsModel->getnodin()->result();
		$data['total'] 	= $this->SmsModel->getnodin()->num_rows(); 
		$data['view'] 	= 'sms/nodin'; 
		$data['site'] 	= 'nota dinas ke logistik';
		$this->load->view('main',$data); 
    }   
	
	function addnodins() { 
		if (!$this->session->userdata('login')) redirect('');
		
		$data['detail'] 	= $this->SmsModel->getallgrupnd()->result(); 
		$data['view'] 	= 'sms/addnodin'; 
		$data['site'] 	= 'tambah nota dinas'; 
		$this->load->view('main',$data); 
    }  
	
	function nodindtcontent() {
		if (!$this->session->userdata('login')) redirect(''); 
		
		
		$myarray = array_filter($_POST['ids']); 
		$ids = join(',',$myarray);
		
		if($ids=="") $where = "";
		else $where= "and pd_id not in (".$ids.")";
		
		$detail 	= $this->SmsModel->getallgrupnd($where)->result();
		$style 		= 'even pointer'; 
		
		print_r($ids);
		$data = "";
		$no=1; foreach ($detail as $row)  { 
		$data.='<tr class="'.$style.'" style="cursor:pointer!important;">  
			<td class="">'.$no.'</td>
			<td class="">'.ucwords(strtolower($row->pd_id)).'</td> 
			<td class="">'.ucwords(strtolower($row->pj_nomor)).'</td> 
			<td class="">'.ucwords(strtolower($row->pj_dosen)).'</td>
			<td class="">'.ucwords(strtolower($row->pd_mk)).'</td> 
			<td class="">'.ucwords(strtolower($row->pd_semester)).'</td>
			<td class="">'.$row->pd_judul.'</td>
			<td class="">'.$row->pd_pengarang.'</td>  
			<td class="">'.$row->pd_tipe.'</td>
		 </tr> ';	
			$no++;
			if($style 	= 'even pointer') $style 	= 'odd pointer'; 
			else $style 	= 'even pointer'; 
		} 
		
		echo $data;
    } 
	
	function delNodin() { 
		if (!$this->session->userdata('login')) redirect('');
		$this->SmsModel->delNodin($_POST['id']);
    }  
	 
	
	public function addNodinDb(){
		if (!$this->session->userdata('login')) redirect('');
		$data = array(
					"nd_nomor"		=> ucwords(strtolower($_POST['nomor'])),
					"nd_tanggal"	=> date('Y-m-d')
		); 
		$id = $this->SmsModel->addNodin($data);
		 
		
		for ($i=0;$i<count($_POST['eks']);$i++){
			$data = array(
						"pd_eks_awal"		=> $_POST['eks'][$i],
						"pd_status"			=> 'Diajukan ke Logistik',
						"pd_nd_id"			=> $id
			); 
			$this->SmsModel->editGrupDetail($_POST['ids'][$i],$data);
		}
		
		redirect('sms/nodin');
	} 
	
	public function editNodin(){  
		if (!$this->session->userdata('login')) redirect('');
		$id = $_POST['id'];  
		$data	= $this->SmsModel->getnodinbyid($id)->row(); 
		echo json_encode($data);
	} 
	
	public function editNodinDb(){
		if (!$this->session->userdata('login')) redirect('');
		$id			= $_POST['id'];
		
		$data = array(
					"nd_nomor"		=> ucwords(strtolower($_POST['nomor']))
		); 
		$this->SmsModel->editNodin($id,$data);
		redirect('sms/nodin');
	} 
	
	function addnodinlist() {  
		if (!$this->session->userdata('login')) redirect('');
		echo '<div class="form-group" id="minbook'.$_POST['id'].'">
				<div class="col-md-10 col-sm-10 col-xs-12">
					<input type="text" readonly="readonly" id="judul" name="judul['.$_POST['id'].']" required="required" class="form-control col-md-7 col-xs-12 judul" placeholder="Nomor Grup-Judul">
					<input type="hidden" id="ids" name="ids['.$_POST['id'].']">
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
					<input type="text" id="eks" name="eks['.$_POST['id'].']" required="required" class="form-control col-md-7 col-xs-12" placeholder="Eksemplar">
				</div> 
			</div> ';
    } 
	
	//==============
	
	function nodindetail($id) { 
		if (!$this->session->userdata('login')) redirect('');
		
		if ($this->SmsModel->getnodinbyid($id)->num_rows()==0) redirect('sms/nodin');
		
		$data['detail'] 	= $this->SmsModel->getgrupdetailbynd($id)->result();
		$data['total'] 		= $this->SmsModel->getgrupdetailbynd($id)->num_rows(); 
		$data['grup'] 	= $this->SmsModel->getnodinbyid($id)->row();
		$data['view'] 		= 'sms/nodindetail'; 
		$data['site'] 		= 'nota dinas detail';
		$this->load->view('main',$data); 
    }    
	
	function delNodinDetail() { 
		if (!$this->session->userdata('login')) redirect('');
		$data = array(
					"pd_eks_awal"		=> '0',
					"pd_status"			=> 'Diajukan Dosen',
					"pd_nd_id"			=> '0'
		); 
		$this->SmsModel->editGrupDetail($_POST['id'],$data);
    }  
	
	public function addNodinDetailDb(){
		if (!$this->session->userdata('login')) redirect('');
		
		for ($i=0;$i<count($_POST['eks']);$i++){
			$data = array(
						"pd_eks_awal"		=> $_POST['eks'][$i],
						"pd_status"			=> 'Diajukan ke Logistik',
						"pd_nd_id"			=> $_POST['id']
			); 
			$this->SmsModel->editGrupDetail($_POST['ids'][$i],$data);
		}
		
		redirect('sms/nodindetail/'.$_POST['id']);
	} 
	
	public function editNodinDetail(){ 
		if (!$this->session->userdata('login')) redirect(''); 
		$id = $_POST['id'];  
		$data	= $this->SmsModel->getgrupdetailbyid($id)->row(); 
		echo json_encode($data);
	} 
	
	public function editNodinDetailDb(){
		if (!$this->session->userdata('login')) redirect('');
		$id			= $_POST['id'];
		
		$data = array(
					"pd_eks_awal"			=> ucwords(strtolower($_POST['eks']))
		); 
		$this->SmsModel->editGrupDetail($id,$data);
		redirect('sms/nodindetail/'.$_POST['idparent']);
	} 	

	function addnodindetail($id) { 
		
		if (!$this->session->userdata('login')) redirect('');
		if ($this->SmsModel->getnodinbyid($id)->num_rows()==0) redirect('sms/nodin');
		$data['grup'] 	= $this->SmsModel->getnodinbyid($id)->row();
		$data['detail'] 	= $this->SmsModel->getallgrupnd()->result(); 
		$data['view'] 	= 'sms/addnodindetail'; 
		$data['site'] 	= 'tambah list nota dinas'; 
		$this->load->view('main',$data); 
    }  
	
	//===========================================================================================================
	
	function bast($page = "") { 
		 
		if (!$this->session->userdata('login')) redirect('');
		$data['sms'] 	= $this->SmsModel->getbast()->result();
		$data['total'] 	= $this->SmsModel->getbast()->num_rows(); 
		$data['view'] 	= 'sms/bast'; 
		$data['site'] 	= 'bast dari logistik';
		$this->load->view('main',$data); 
    }   
	
	function addbasts() { 
		
		if (!$this->session->userdata('login')) redirect('');
		$data['detail'] 	= $this->SmsModel->getallgrupbs()->result(); 
		$data['view'] 	= 'sms/addbast'; 
		$data['site'] 	= 'tambah bast'; 
		$this->load->view('main',$data); 
    }  
	
	function bastdtcontent() {  
		if (!$this->session->userdata('login')) redirect('');
		
		$myarray = array_filter($_POST['ids']); 
		$ids = join(',',$myarray);
		
		if($ids=="") $where = "";
		else $where= "and pd_id not in (".$ids.")";
		
		$detail 	= $this->SmsModel->getallgrupbs($where)->result();
		$style 		= 'even pointer'; 
		
		print_r($ids);
		$data = "";
		$no=1; foreach ($detail as $row)  { 
		$data.='<tr class="'.$style.'" style="cursor:pointer!important;">  
			<td class="">'.$no.'</td>
			<td class="">'.ucwords(strtolower($row->pd_id)).'</td> 
			<td class="">'.ucwords(strtolower($row->pj_nomor)).'</td> 
			<td class="">'.ucwords(strtolower($row->pj_dosen)).'</td>
			<td class="">'.ucwords(strtolower($row->pd_mk)).'</td> 
			<td class="">'.ucwords(strtolower($row->pd_semester)).'</td>
			<td class="">'.$row->pd_judul.'</td>
			<td class="">'.$row->pd_pengarang.'</td>  
			<td class="">'.$row->pd_tipe.'</td>
		 </tr> ';	
			$no++;
			if($style 	= 'even pointer') $style 	= 'odd pointer'; 
			else $style 	= 'even pointer'; 
		} 
		
		echo $data;
    } 
	
	function delBast() { 
		if (!$this->session->userdata('login')) redirect('');
		$this->SmsModel->delBast($_POST['id']);
    }  
	 
	
	public function addBastDb(){
		if (!$this->session->userdata('login')) redirect('');
		$data = array(
					"bs_nomor"		=> ucwords(strtolower($_POST['nomor'])),
					"bs_tanggal"	=> date('Y-m-d')
		); 
		$id = $this->SmsModel->addBast($data);
		 
		
		for ($i=0;$i<count($_POST['eks']);$i++){
			$data = array(
						"pd_eks_akhir"		=> $_POST['eks'][$i],
						"pd_status"			=> 'Diterima',
						"pd_bs_id"			=> $id
			); 
			$this->SmsModel->editGrupDetail($_POST['ids'][$i],$data);
		}
		
		redirect('sms/bast');
	} 
	
	public function editBast(){  
		if (!$this->session->userdata('login')) redirect('');
		$id = $_POST['id'];  
		$data	= $this->SmsModel->getbastbyid($id)->row(); 
		echo json_encode($data);
	} 
	
	public function editBastDb(){
		if (!$this->session->userdata('login')) redirect('');
		$id			= $_POST['id'];
		
		$data = array(
					"bs_nomor"		=> ucwords(strtolower($_POST['nomor']))
		); 
		$this->SmsModel->editBast($id,$data);
		redirect('sms/bast');
	} 
	
	function addbastlist() {  
		if (!$this->session->userdata('login')) redirect('');
		echo '<div class="form-group" id="minbook'.$_POST['id'].'">
				<div class="col-md-10 col-sm-10 col-xs-12">
					<input type="text" readonly="readonly" id="judul" name="judul['.$_POST['id'].']" required="required" class="form-control col-md-7 col-xs-12 judul" placeholder="Nomor Grup-Judul">
					<input type="hidden" id="ids" name="ids['.$_POST['id'].']">
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
					<input type="text" id="eks" name="eks['.$_POST['id'].']" required="required" class="form-control col-md-7 col-xs-12" placeholder="Eksemplar">
				</div> 
			</div> ';
    } 
	
	//==============
	
	function bastdetail($id) { 
		
		if (!$this->session->userdata('login')) redirect('');
		
		if ($this->SmsModel->getbastbyid($id)->num_rows()==0) redirect('sms/bast');
		
		$data['detail'] 	= $this->SmsModel->getgrupdetailbybs($id)->result();
		$data['total'] 		= $this->SmsModel->getgrupdetailbybs($id)->num_rows(); 
		$data['grup'] 	= $this->SmsModel->getbastbyid($id)->row();
		$data['view'] 		= 'sms/bastdetail'; 
		$data['site'] 		= 'bast detail';
		$this->load->view('main',$data); 
    }    
	
	function delBastDetail() { 
		if (!$this->session->userdata('login')) redirect('');
		
		$data = array(
					"pd_eks_akhir"		=> '0',
					"pd_status"			=> 'Diajukan ke Logistik',
					"pd_bs_id"			=> '0'
		); 
		$this->SmsModel->editGrupDetail($_POST['id'],$data);
    }  
	
	public function addBastDetailDb(){
		if (!$this->session->userdata('login')) redirect('');
		for ($i=0;$i<count($_POST['eks']);$i++){
			$data = array(
						"pd_eks_akhir"		=> $_POST['eks'][$i],
						"pd_status"			=> 'Diterima',
						"pd_bs_id"			=> $_POST['id']
			); 
			$this->SmsModel->editGrupDetail($_POST['ids'][$i],$data);
		}
		
		redirect('sms/bastdetail/'.$_POST['id']);
	} 
	
	public function editBastDetail(){ 
		if (!$this->session->userdata('login')) redirect('');
		$id = $_POST['id'];  
		$data	= $this->SmsModel->getgrupdetailbyid($id)->row(); 
		echo json_encode($data);
	} 
	
	public function editBastDetailDb(){
		if (!$this->session->userdata('login')) redirect('');
		$id			= $_POST['id'];
		
		$data = array(
					"pd_eks_akhir"			=> ucwords(strtolower($_POST['eks']))
		); 
		$this->SmsModel->editGrupDetail($id,$data);
		redirect('sms/bastdetail/'.$_POST['idparent']);
	} 	

	function addbastdetail($id) { 
		if (!$this->session->userdata('login')) redirect('');
		
		if ($this->SmsModel->getbastbyid($id)->num_rows()==0) redirect('sms/bast');
		$data['grup'] 	= $this->SmsModel->getbastbyid($id)->row();
		$data['detail'] 	= $this->SmsModel->getallgrupbs()->result(); 
		$data['view'] 	= 'sms/addbastdetail'; 
		$data['site'] 	= 'tambah list bast'; 
		$this->load->view('main',$data); 
    }  
	
	//==================================================================================================================
	function lists() { 
		$data['total'] 	= $this->SmsModel->getall()->num_rows();
		$data['detail'] 	= $this->SmsModel->getall()->result(); 
		$data['view'] 	= 'sms/list'; 
		$data['site'] 	= 'list sms'; 
		$this->load->view('main',$data); 
    } 

	public function editList(){  
		if (!$this->session->userdata('login')) redirect('');
		$id = $_POST['id'];  
		$data	= $this->SmsModel->getgrupdetailbyid($id)->row(); 
		echo json_encode($data);
	} 
	
	public function editListDb(){
		if (!$this->session->userdata('login')) redirect('');
		$id			= $_POST['id'];
		$status = 'Ditolak - '.$_POST['alasan'];
		$data = array(
					"pd_status"			=> ucwords(strtolower($status))
		); 
		$this->SmsModel->editGrupDetail($id,$data);
		redirect('sms/lists');
	} 		
}

?>