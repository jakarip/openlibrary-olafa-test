<?php

class Pengadaan extends CI_Controller {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct(); 
		$this->load->model('PengadaanModel');  
    }

    function pengajuan($page = "") { 
		if (!$this->session->userdata('login')) redirect('');
		 
		$data['pengadaan'] 	= $this->PengadaanModel->getpengajuan()->result();
		$data['total'] 	= $this->PengadaanModel->getpengajuan()->num_rows(); 
		$data['view'] 	= 'pengadaan/pengajuan'; 
		$data['site'] 	= 'pengajuan';
		$this->load->view('main',$data); 
    }   
	
	function addpengajuan() { 
		if (!$this->session->userdata('login')) redirect('');
		 
		$data['view'] 	= 'pengadaan/addpengajuan'; 
		$data['site'] 	= 'tambah pengajuan';
		$this->load->view('main',$data); 
    }  
	
	function delPengajuan() { 
		if (!$this->session->userdata('login')) redirect('');
		$this->PengadaanModel->delPengajuan($_POST['id']);
    }  
	 
	
	public function addPengajuanDb(){
		if (!$this->session->userdata('login')) redirect('');
		$data = array(
					"pj_nomor"		=> ucwords(strtolower($_POST['nomor'])),
					"pj_dosen"		=> ucwords(strtolower($_POST['dosen'])),
					"pj_nik"		=> ucwords(strtolower($_POST['nik'])),
					"pj_jabatan"	=> ucwords(strtolower($_POST['jabatan'])),
					"pj_kaprodi"	=> ucwords(strtolower($_POST['kaprodi'])),
					"pj_tanggal"	=> date('Y-m-d')
		); 
		$id = $this->PengadaanModel->addPengajuan($data);
		
		$mk 		= $_POST['mk'];
		$smt 		= $_POST['smt'];
		$judul 		= $_POST['judul'];
		$pengarang 	= $_POST['pengarang'];
		$penerbit 	= $_POST['penerbit'];
		$tahun 		= $_POST['tahun'];
		$tipe 		= $_POST['tipe'];
		for ($i=0;$i<count($_POST['mk']);$i++){
			$data = array(
						"pd_mk"			=> ucwords(strtolower($mk[$i])),
						"pd_semester"	=> ucwords(strtolower($smt[$i])),
						"pd_judul"		=> ucwords(strtolower($judul[$i])),
						"pd_pengarang"	=> ucwords(strtolower($pengarang[$i])),
						"pd_penerbit"	=> ucwords(strtolower($penerbit[$i])),
						"pd_tahun"		=> ucwords(strtolower($tahun[$i])),
						"pd_tipe"		=> ucwords(strtolower($tipe[$i])),
						"pd_eks_awal"	=> '0',
						"pd_eks_akhir"	=> '0',
						"pd_status"		=> ucwords(strtolower('Diajukan Dosen')),
						"pd_pj_id"		=> $id,
						"pd_bs_id"		=> '',
						"pd_nd_id"		=> ''
			); 
			$this->PengadaanModel->addPengajuanDetail($data);
		}
		redirect('pengadaan/pengajuan');
	} 
	
	public function editPengajuan(){  
		if (!$this->session->userdata('login')) redirect('');
		$id = $_POST['id'];  
		$data	= $this->PengadaanModel->getpengajuanbyid($id)->row(); 
		echo json_encode($data);
	} 
	
	public function editPengajuanDb(){
		if (!$this->session->userdata('login')) redirect('');
		$id			= $_POST['id'];
		
		$data = array(
					"pj_nomor"		=> ucwords(strtolower($_POST['nomor'])),
					"pj_dosen"		=> ucwords(strtolower($_POST['dosen'])),
					"pj_nik"		=> ucwords(strtolower($_POST['nik'])),
					"pj_jabatan"	=> ucwords(strtolower($_POST['jabatan'])),
					"pj_kaprodi"	=> ucwords(strtolower($_POST['kaprodi']))
		); 
		$this->PengadaanModel->editPengajuan($id,$data);
		redirect('pengadaan/pengajuan');
	} 
	
	//============
	function pengajuandetail($id) { 
		if (!$this->session->userdata('login')) redirect('');
		
		if ($this->PengadaanModel->getpengajuanbyid($id)->num_rows()==0) redirect('pengadaan/pengajuan');
		
		$data['detail'] 	= $this->PengadaanModel->getpengajuandetailbypj($id)->result();
		$data['total'] 		= $this->PengadaanModel->getpengajuandetailbypj($id)->num_rows(); 
		$data['pengajuan'] 	= $this->PengadaanModel->getpengajuanbyid($id)->row();
		$data['view'] 		= 'pengadaan/pengajuandetail'; 
		$data['site'] 		= 'pengajuan detail';
		$this->load->view('main',$data); 
    }    
	
	function delPengajuanDetail() { 
		if (!$this->session->userdata('login')) redirect('');
		$this->PengadaanModel->delPengajuanDetail($_POST['id']);
    }  
	
	public function addPengajuanDetailDb(){
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
		$this->PengadaanModel->addPengajuanDetail($data);
		
		redirect('pengadaan/pengajuandetail/'.$_POST['id']);
	} 
	
	public function editPengajuanDetail(){ 
		if (!$this->session->userdata('login')) redirect(''); 
		$id = $_POST['id'];  
		$data	= $this->PengadaanModel->getpengajuandetailbyid($id)->row(); 
		echo json_encode($data);
	} 
	
	public function editPengajuanDetailDb(){
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
		$this->PengadaanModel->editPengajuanDetail($id,$data);
		redirect('pengadaan/pengajuandetail/'.$_POST['idparent']);
	} 
	
	function addbook() {  
		if (!$this->session->userdata('login')) redirect('');
		echo '<div class="form-group" id="minbook'.$_POST['id'].'">
				<div class="col-md-2 col-sm-2 col-xs-12">
					<input type="text" id="mk"name="mk['.$_POST['id'].']" required="required" class="form-control col-md-7 col-xs-12" placeholder="Mata Kuliah">
				</div>
				<div class="col-md-1 col-sm-1 col-xs-12">
					<input type="text" id="smt"name="smt['.$_POST['id'].']" required="required" class="form-control col-md-7 col-xs-12" placeholder="Smt">
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
					<input type="text" id="judul"name="judul['.$_POST['id'].']" required="required" class="form-control col-md-7 col-xs-12" placeholder="Judul">
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
					<input type="text" id="pengarang"name="pengarang['.$_POST['id'].']"class="form-control col-md-7 col-xs-12" placeholder="Pengarang">
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
					<input type="text" id="penerbit"name="penerbit['.$_POST['id'].']" class="form-control col-md-7 col-xs-12" placeholder="Penerbit">
				</div>
				<div class="col-md-1 col-sm-1 col-xs-12">
					<input type="text" id="tahun"name="tahun['.$_POST['id'].']" class="form-control col-md-7 col-xs-12" placeholder="Tahun">
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12">
					<select required="required" class="form-control col-md-7 col-xs-12" name="tipe['.$_POST['id'].']" id="tipe">
						<option value="">Tipe</option>
						<option value="Utama">Utama</option>
						<option value="Penunjang">Penunjang</option>
					</select>
				</div> 
			</div> ';
    }  
	
	//===========================================================================================================
	
	function nodin($page = "") { 
		if (!$this->session->userdata('login')) redirect('');
		 
		$data['pengadaan'] 	= $this->PengadaanModel->getnodin()->result();
		$data['total'] 	= $this->PengadaanModel->getnodin()->num_rows(); 
		$data['view'] 	= 'pengadaan/nodin'; 
		$data['site'] 	= 'nota dinas ke logistik';
		$this->load->view('main',$data); 
    }   
	
	function addnodins() { 
		if (!$this->session->userdata('login')) redirect('');
		
		$data['detail'] 	= $this->PengadaanModel->getallpengajuannd()->result(); 
		$data['view'] 	= 'pengadaan/addnodin'; 
		$data['site'] 	= 'tambah nota dinas'; 
		$this->load->view('main',$data); 
    }  
	
	function nodindtcontent() {
		if (!$this->session->userdata('login')) redirect(''); 
		
		
		$myarray = array_filter($_POST['ids']); 
		$ids = join(',',$myarray);
		
		if($ids=="") $where = "";
		else $where= "and pd_id not in (".$ids.")";
		
		$detail 	= $this->PengadaanModel->getallpengajuannd($where)->result();
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
		$this->PengadaanModel->delNodin($_POST['id']);
    }  
	 
	
	public function addNodinDb(){
		if (!$this->session->userdata('login')) redirect('');
		$data = array(
					"nd_nomor"		=> ucwords(strtolower($_POST['nomor'])),
					"nd_tanggal"	=> date('Y-m-d')
		); 
		$id = $this->PengadaanModel->addNodin($data);
		 
		
		for ($i=0;$i<count($_POST['eks']);$i++){
			$data = array(
						"pd_eks_awal"		=> $_POST['eks'][$i],
						"pd_status"			=> 'Diajukan ke Logistik',
						"pd_nd_id"			=> $id
			); 
			$this->PengadaanModel->editPengajuanDetail($_POST['ids'][$i],$data);
		}
		
		redirect('pengadaan/nodin');
	} 
	
	public function editNodin(){  
		if (!$this->session->userdata('login')) redirect('');
		$id = $_POST['id'];  
		$data	= $this->PengadaanModel->getnodinbyid($id)->row(); 
		echo json_encode($data);
	} 
	
	public function editNodinDb(){
		if (!$this->session->userdata('login')) redirect('');
		$id			= $_POST['id'];
		
		$data = array(
					"nd_nomor"		=> ucwords(strtolower($_POST['nomor']))
		); 
		$this->PengadaanModel->editNodin($id,$data);
		redirect('pengadaan/nodin');
	} 
	
	function addnodinlist() {  
		if (!$this->session->userdata('login')) redirect('');
		echo '<div class="form-group" id="minbook'.$_POST['id'].'">
				<div class="col-md-10 col-sm-10 col-xs-12">
					<input type="text" readonly="readonly" id="judul" name="judul['.$_POST['id'].']" required="required" class="form-control col-md-7 col-xs-12 judul" placeholder="Nomor Pengajuan-Judul">
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
		
		if ($this->PengadaanModel->getnodinbyid($id)->num_rows()==0) redirect('pengadaan/nodin');
		
		$data['detail'] 	= $this->PengadaanModel->getpengajuandetailbynd($id)->result();
		$data['total'] 		= $this->PengadaanModel->getpengajuandetailbynd($id)->num_rows(); 
		$data['pengajuan'] 	= $this->PengadaanModel->getnodinbyid($id)->row();
		$data['view'] 		= 'pengadaan/nodindetail'; 
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
		$this->PengadaanModel->editPengajuanDetail($_POST['id'],$data);
    }  
	
	public function addNodinDetailDb(){
		if (!$this->session->userdata('login')) redirect('');
		
		for ($i=0;$i<count($_POST['eks']);$i++){
			$data = array(
						"pd_eks_awal"		=> $_POST['eks'][$i],
						"pd_status"			=> 'Diajukan ke Logistik',
						"pd_nd_id"			=> $_POST['id']
			); 
			$this->PengadaanModel->editPengajuanDetail($_POST['ids'][$i],$data);
		}
		
		redirect('pengadaan/nodindetail/'.$_POST['id']);
	} 
	
	public function editNodinDetail(){ 
		if (!$this->session->userdata('login')) redirect(''); 
		$id = $_POST['id'];  
		$data	= $this->PengadaanModel->getpengajuandetailbyid($id)->row(); 
		echo json_encode($data);
	} 
	
	public function editNodinDetailDb(){
		if (!$this->session->userdata('login')) redirect('');
		$id			= $_POST['id'];
		
		$data = array(
					"pd_eks_awal"			=> ucwords(strtolower($_POST['eks']))
		); 
		$this->PengadaanModel->editPengajuanDetail($id,$data);
		redirect('pengadaan/nodindetail/'.$_POST['idparent']);
	} 	

	function addnodindetail($id) { 
		
		if (!$this->session->userdata('login')) redirect('');
		if ($this->PengadaanModel->getnodinbyid($id)->num_rows()==0) redirect('pengadaan/nodin');
		$data['pengajuan'] 	= $this->PengadaanModel->getnodinbyid($id)->row();
		$data['detail'] 	= $this->PengadaanModel->getallpengajuannd()->result(); 
		$data['view'] 	= 'pengadaan/addnodindetail'; 
		$data['site'] 	= 'tambah list nota dinas'; 
		$this->load->view('main',$data); 
    }  
	
	//===========================================================================================================
	
	function bast($page = "") { 
		 
		if (!$this->session->userdata('login')) redirect('');
		$data['pengadaan'] 	= $this->PengadaanModel->getbast()->result();
		$data['total'] 	= $this->PengadaanModel->getbast()->num_rows(); 
		$data['view'] 	= 'pengadaan/bast'; 
		$data['site'] 	= 'bast dari logistik';
		$this->load->view('main',$data); 
    }   
	
	function addbasts() { 
		
		if (!$this->session->userdata('login')) redirect('');
		$data['detail'] 	= $this->PengadaanModel->getallpengajuanbs()->result(); 
		$data['view'] 	= 'pengadaan/addbast'; 
		$data['site'] 	= 'tambah bast'; 
		$this->load->view('main',$data); 
    }  
	
	function bastdtcontent() {  
		if (!$this->session->userdata('login')) redirect('');
		
		$myarray = array_filter($_POST['ids']); 
		$ids = join(',',$myarray);
		
		if($ids=="") $where = "";
		else $where= "and pd_id not in (".$ids.")";
		
		$detail 	= $this->PengadaanModel->getallpengajuanbs($where)->result();
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
		$this->PengadaanModel->delBast($_POST['id']);
    }  
	 
	
	public function addBastDb(){
		if (!$this->session->userdata('login')) redirect('');
		$data = array(
					"bs_nomor"		=> ucwords(strtolower($_POST['nomor'])),
					"bs_tanggal"	=> date('Y-m-d')
		); 
		$id = $this->PengadaanModel->addBast($data);
		 
		
		for ($i=0;$i<count($_POST['eks']);$i++){
			$data = array(
						"pd_eks_akhir"		=> $_POST['eks'][$i],
						"pd_status"			=> 'Diterima',
						"pd_bs_id"			=> $id
			); 
			$this->PengadaanModel->editPengajuanDetail($_POST['ids'][$i],$data);
		}
		
		redirect('pengadaan/bast');
	} 
	
	public function editBast(){  
		if (!$this->session->userdata('login')) redirect('');
		$id = $_POST['id'];  
		$data	= $this->PengadaanModel->getbastbyid($id)->row(); 
		echo json_encode($data);
	} 
	
	public function editBastDb(){
		if (!$this->session->userdata('login')) redirect('');
		$id			= $_POST['id'];
		
		$data = array(
					"bs_nomor"		=> ucwords(strtolower($_POST['nomor']))
		); 
		$this->PengadaanModel->editBast($id,$data);
		redirect('pengadaan/bast');
	} 
	
	function addbastlist() {  
		if (!$this->session->userdata('login')) redirect('');
		echo '<div class="form-group" id="minbook'.$_POST['id'].'">
				<div class="col-md-10 col-sm-10 col-xs-12">
					<input type="text" readonly="readonly" id="judul" name="judul['.$_POST['id'].']" required="required" class="form-control col-md-7 col-xs-12 judul" placeholder="Nomor Pengajuan-Judul">
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
		
		if ($this->PengadaanModel->getbastbyid($id)->num_rows()==0) redirect('pengadaan/bast');
		
		$data['detail'] 	= $this->PengadaanModel->getpengajuandetailbybs($id)->result();
		$data['total'] 		= $this->PengadaanModel->getpengajuandetailbybs($id)->num_rows(); 
		$data['pengajuan'] 	= $this->PengadaanModel->getbastbyid($id)->row();
		$data['view'] 		= 'pengadaan/bastdetail'; 
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
		$this->PengadaanModel->editPengajuanDetail($_POST['id'],$data);
    }  
	
	public function addBastDetailDb(){
		if (!$this->session->userdata('login')) redirect('');
		for ($i=0;$i<count($_POST['eks']);$i++){
			$data = array(
						"pd_eks_akhir"		=> $_POST['eks'][$i],
						"pd_status"			=> 'Diterima',
						"pd_bs_id"			=> $_POST['id']
			); 
			$this->PengadaanModel->editPengajuanDetail($_POST['ids'][$i],$data);
		}
		
		redirect('pengadaan/bastdetail/'.$_POST['id']);
	} 
	
	public function editBastDetail(){ 
		if (!$this->session->userdata('login')) redirect('');
		$id = $_POST['id'];  
		$data	= $this->PengadaanModel->getpengajuandetailbyid($id)->row(); 
		echo json_encode($data);
	} 
	
	public function editBastDetailDb(){
		if (!$this->session->userdata('login')) redirect('');
		$id			= $_POST['id'];
		
		$data = array(
					"pd_eks_akhir"			=> ucwords(strtolower($_POST['eks']))
		); 
		$this->PengadaanModel->editPengajuanDetail($id,$data);
		redirect('pengadaan/bastdetail/'.$_POST['idparent']);
	} 	

	function addbastdetail($id) { 
		if (!$this->session->userdata('login')) redirect('');
		
		if ($this->PengadaanModel->getbastbyid($id)->num_rows()==0) redirect('pengadaan/bast');
		$data['pengajuan'] 	= $this->PengadaanModel->getbastbyid($id)->row();
		$data['detail'] 	= $this->PengadaanModel->getallpengajuanbs()->result(); 
		$data['view'] 	= 'pengadaan/addbastdetail'; 
		$data['site'] 	= 'tambah list bast'; 
		$this->load->view('main',$data); 
    }  
	
	//==================================================================================================================
	function lists() { 
		$data['total'] 	= $this->PengadaanModel->getall()->num_rows();
		$data['detail'] 	= $this->PengadaanModel->getall()->result(); 
		$data['view'] 	= 'pengadaan/list'; 
		$data['site'] 	= 'list pengadaan'; 
		$this->load->view('main',$data); 
    } 

	public function editList(){  
		if (!$this->session->userdata('login')) redirect('');
		$id = $_POST['id'];  
		$data	= $this->PengadaanModel->getpengajuandetailbyid($id)->row(); 
		echo json_encode($data);
	} 
	
	public function editListDb(){
		if (!$this->session->userdata('login')) redirect('');
		$id			= $_POST['id'];
		$status = 'Ditolak - '.$_POST['alasan'];
		$data = array(
					"pd_status"			=> ucwords(strtolower($status))
		); 
		$this->PengadaanModel->editPengajuanDetail($id,$data);
		redirect('pengadaan/lists');
	} 		
}

?>