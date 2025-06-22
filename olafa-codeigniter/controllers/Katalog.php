<?php

class Katalog extends CI_Controller {
 
    function __construct() {
        parent::__construct(); 
		$this->load->model('Katalogmodel');
		$this->load->model('KatalogMkModel');
		// $this->load->library('PHPExcel/PHPExcel');   
		// $this->load->library('Mpdf/mpdf');   
		// if(!$this->session->userdata('login')) redirect('');
		
		ini_set('memory_limit','-1'); 
		ini_set('max_execution_time','3600'); 
    }

    function index() {  
		$data['reservation'] = (ISSET($_POST['reservation'])?$_POST['reservation']:''); 
		$data['location'] 		= $this->KatalogMkModel->get_location()->result();
		$data['location_choose'] = array();
		$where				 = "";
		if(!empty($_POST['reservation'])){ 
			$tgl 	= explode(' to ',$_POST['reservation']);
			$start 	= explode('-',$tgl[0]);
			$end 	= explode('-',$tgl[1]);
			$start 	= $start[2]."-".$start[1]."-".$start[0];
			$end  	= $end[2]."-".$end[1]."-".$end[0]; 
			
				$where ="and kk.entrance_date BETWEEN '".$start." 00:00:00' AND '".$end."  23:59:59' ";  


			// echo $where;
		} 
	
		// if (count($_POST['location'])!=0){ 
		// 	$data['location_choose'] = $_POST['location'];
		// 	$where .= "and kk.item_location_id in (".implode(",",$_POST['location']).") ";   
		// }  

		$data['jurusan'] = $this->Katalogmodel->getallknowledgetype($where)->result(); 
		// echo "<pre>";
		// print_r($data['jurusan']);
		// echo "</pre>";
		$data['menu'] 	= 'katalog/katalog'; 
		$this->load->view('theme', $data);
    } 
	
	
	 
	public function ajax_statistik()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$id 		= $this->input->post('id');
		$type 		= $this->input->post('type');
		
		if($type=='judul'){
			$table 		= "select cc.code klasifikasi,kk.code barcode,ks.name subjek,kk.status,kt.* from knowledge_item kt 
						left join knowledge_stock kk on kt.id=kk.knowledge_item_id
						left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
						left join classification_code cc on kt.classification_code_id=cc.id
						left join knowledge_type kp on kt.knowledge_type_id=kp.id where ks.active='1' and kp.active='1' and kt.knowledge_type_id=$id group by kt.id
					";
		}
		else if($type=='eksemplar'){
			$table 		= "select cc.code klasifikasi,kk.code barcode,ks.name subjek,kk.status,kt.* from knowledge_item kt 
						left join knowledge_stock kk on kt.id=kk.knowledge_item_id
						left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
						left join classification_code cc on kt.classification_code_id=cc.id
						left join knowledge_type kp on kt.knowledge_type_id=kp.id where ks.active='1' and kp.active='1' and kt.knowledge_type_id=$id
					";
 
					 
		}
		else {
			$table 		= "select cc.code klasifikasi,kk.code barcode,ks.name subjek,kk.status,kt.* from knowledge_item kt 
				left join knowledge_stock kk on kt.id=kk.knowledge_item_id
				left join knowledge_subject ks on kt.knowledge_subject_id=ks.id
				left join classification_code cc on kt.classification_code_id=cc.id
				left join knowledge_type kp on kt.knowledge_type_id=kp.id where ks.active='1' and kp.active='1' and kt.knowledge_type_id=$id and status='$type'
			";
			
		}
		$colOrder 	= array(null,'code','barcode','title','subjek','klasifikasi','author','publisher_name','status'); //set column field database for datatable orderable
		$colSearch 	= array('code','barcode','title','subjek','klasifikasi','author','publisher_name','status'); //set column field database for datatable
		$order 		= "order by title,author asc"; // default order 
		
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order);
		
		$list = $this->datatables->get_datatables();
		//print_r($list);
		$data = array();
		$no = $_POST['start'];
		$temp = array();
		$temp[1] = 'Tersedia';
		$temp[2] = 'Dipinjam';
		$temp[3] = 'Rusak';
		$temp[4] = 'Hilang';
		$temp[5] = 'Expired';
		$temp[6] = 'Hilang diganti';
		$temp[7] = 'Diolah';
		$temp[8] = 'Cadangan';
		$temp[9] = 'Weeding';
		foreach ($list as $dt) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $dt->code;
			$row[] = ($type=='judul'?'':$dt->barcode);   
			$row[] = $dt->title; 
			$row[] = $dt->subjek; 
			$row[] = $dt->klasifikasi; 
			$row[] = $dt->author; 
			$row[] = $dt->publisher_name; 
			$row[] = ($type=='judul'?'':$temp[$dt->status]);  
		
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
	
	function ecatalog($edisi="") {  
		
		$submit			 = $this->input->post('submit'); 
		$data['month']	 = $this->input->post('month'); 
		if (!empty($submit)){ 
			
			$class[0] = "General Collection";
			$class[1] = "Philosophy and phychology";
			$class[2] = "Religion";
			$class[3] = "Social sciences";
			$class[4] = "Language";
			$class[5] = "Science";
			$class[6] = "Technology (Applied Science)";
			$class[7] = "Arts & Recreation";
			$class[8] = "Literature";
			$class[9] = "History & Geography";
			
			$data['class'] = $class;
			foreach ($class as $i => $val){
				$code_awal 	= $i."00";
				$code_akhir = $i."99"; 
				$data['data'][$i]		= $this->Katalogmodel->getEcatalog($data['month'],$code_awal,$code_akhir)->result(); 
			}  
		
		} 
		
		
		$data['menu'] 	= 'katalog/ecatalog';  
		$this->load->view('theme', $data);
    }  
	
	public function ecatalog_pdf($month)
	{  
		ini_set('memory_limit','-1'); 
		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		error_reporting(E_ALL);
		 
		$class[0] = "General Collection";
		$class[1] = "Philosophy and phychology";
		$class[2] = "Religion";
		$class[3] = "Social sciences";
		$class[4] = "Language";
		$class[5] = "Science";
		$class[6] = "Technology (Applied Science)";
		$class[7] = "Arts & Recreation";
		$class[8] = "Literature";
		$class[9] = "History & Geography";
		$html = "<img src='../../../../data/batik/www/uploads/banner/ecataloguebanner.jpg'><br><br>";
		//  $html = "";
		foreach ($class as $i => $val){
			 
			$code_awal 	= $i."00";
			$code_akhir = $i."99"; 
			$data[$i]		= $this->Katalogmodel->getEcatalog($month,$code_awal,$code_akhir)->result(); 
			//echo count($data[$i])."<br>"; 
			//print_r($html);				
			if (count($data[$i])!=0) {
			 
				$html .=" 
				<div width='80%' style='font-size:18px;font-weight:bold;font-family:calibri;margin-left:50px;'>".$i."00-".$i."99 ".$val."</div>
					<table width='100%'>  
				<tbody>"; 	
							
				$no		= 1; 
				$style 	= 'even pointer'; 
				$count = 0;
				foreach ($data[$i] as $row) 	{
				$count++;	
				if ($row->cover_path!="" and file_exists('../../../../data/batik/www/uploads/book/cover/'.$row->cover_path)) $file = $row->cover_path; 
					else $file="default.jpg";
					if ($count==1) $html.="<tr><td colspan='3'><hr style='height:1px; border:none; color:#c2c2a3; background-color:#000;'></td></tr>";
					$title = clean(strtolower($row->title)); 
					$url   = "https://openlibrary.telkomuniversity.ac.id/pustaka/".$row->cat_id."/".$title.".html";
				 
					$html .= "<tr class='".$style."'>";
					
					$html .= '<td style="font-size:14px;font-family:calibri; margin:5px;" width="10%" align="center"  valign="top"><img src="../../../../data/batik/www/uploads/book/cover/'.$file.'" width="90px"><br>'.$row->cat_code.'</td>';
				$html .= "	<td  style='font-size:14px;font-family:calibri; margin:5px;' width='50%'  valign='top'> 
						<a href='".$url."'>".$row->title."</a><br>";
						$html .=  $row->author."<br>";
						$html .= $row->publisher_name.", ".$row->published_year."<br>";
						$html .= "Klasifikasi ".$row->class_name."<br>";
						$html .= $row->tipe." (Sirkulasi)<br>";
						$html .= "<a href='https://openlibrary.telkomuniversity.ac.id/knowledgeitem/".$row->cat_id."/available.html'>total ".$row->eks." Koleksi</a><br>"; 
						 $html .= "<a  href='".$url."' style=''>".wordwrap($url, 20, "\n", true)."</a>";
					 $html .= "</td>
						<td style='font-size:14px;font-family:calibri; margin:5px;'  width='40%' valign='top'>";
						 $html .= '<b>'.$row->subject.'</b><br>';
						$html .= ($row->alternate_subject!=""?$row->alternate_subject."<br>":"");
						$html .= limit_text($row->abstract_content,30)."<a href='".$url."'>selengkapnya..</a><br>";
					$html.="</td> 
					</tr><tr><td colspan='3'><hr style='height:0.5px; border:none; color:#c2c2a3; background-color:#000;'></td></tr>";


					$no++; 
					if($style 	= 'even pointer') $style 	= 'odd pointer'; 
					else $style 	= 'even pointer'; 
					 
				}
			   
				$html .="</tbody>";

				$html .="</table>";
				
			}
		}    	
		 
		
		$mo = array ("01" => "January","02" => "February","03" => "March","04" => "April","05" => "May","06" => "June","07" => "July","08" => "August","09" => "September","10" => "October","11" => "November","12" => "December",
					);
		
		$tgl = explode("-",$month);
		
		 
										//urutan margin mpdf -> left-right-top-bottom
		$pdf  			= new mPDF('c','A4','','','10','10','10','15');
		$pdfFilePath 	= 'E - Catalogue of Book Collection '.$mo[$tgl[0]].' '.$tgl[1].'.pdf';  
		// $pdf->AddPage('', '', 159);
		$pdf->SetHTMLFooter('
			<table width="100%" style="vertical-align: bottom; font-family: calibri; font-size: 11px; color: #000000; font-weight: bold;"><tr>
			<td width="66%" style="text-align: left; ">E - Catalogue of Book Collection '.$mo[$tgl[0]].' '.$tgl[1].'</td>
			<td width="33%" style="text-align: right; ">Page {PAGENO}</td>
			</tr></table> 
			');  
			
		$pdf->WriteHTML($html); 
		$pdf->debug = true;
       // echo $html;
		 $pdf->Output($pdfFilePath, 'D');
	}
	
	function lists() { 
		$data['type'] = $this->Katalogmodel->getknowledgetype()->result();  
		$data['menu'] 	= 'katalog/lists'; 
		$this->load->view('theme', $data);
    }

	
	public function ajax_katalog()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$month 			= $this->input->post('month');
		$klasifikasi	= $this->input->post('klasifikasi');
		$type 			= $this->input->post('type');
		$where = "";
		$where2 = "";
		if (!empty($month)) {
			$tgl 	= explode(' to ',$month);
			$start 	= explode('-',$tgl[0]);
			$end 	= explode('-',$tgl[1]);
			$where .="and kt.entrance_date BETWEEN '".$start[2]."-".$start[1]."-".$start[0]." 00:00:00' AND '".$end[2]."-".$end[1]."-".$end[0]." 23:59:59' ";
			
		}	 
		
		if(is_array($type)){
			$where .= "and kt.knowledge_type_id in (".implode(',',$type).")";
		}  
		
		if ($klasifikasi!="") {  
			if($klasifikasi=='0'){
				$where2="where (codes2='0' or (codes2>0 and codes2<100))";
			}
			else {
				$where2="where codes2>=".$klasifikasi."00  and codes2<".$klasifikasi."99 ";
				
			}
		}	
		
		$table 		= "select * from (select kt.code catalog,count(ks.code)eksemplar, cc. CODE klasifikasi, title,nama_prodi,
						author,  SUBSTRING_INDEX(SUBSTRING_INDEX( author, ',', 1 ),' ',-1) author_code, publisher_name,kl.name tipe,published_year,sum(ks.price) harga,il.name lokasi,
						replace(ltrim(replace(cc.code,'0',' ')),' ','0') codes2
						
						from knowledge_stock ks  
						left join knowledge_item kt on kt.id=ks.knowledge_item_id  
						LEFT JOIN classification_code cc ON cc.id = kt.classification_code_id 
						LEFT JOIN knowledge_type kl ON kl.id = kt.knowledge_type_id 
						left join item_location il on il.id=kt.item_location_id  
						left join t_mst_prodi tmp on c_kode_prodi=kt.course_code  
						where 1=1 $where group by kt.id,ks.course_code) b $where2";
						
						
						// cast(replace(ltrim(replace(cc.code,'0',' ')),' ','0') as int) codes2
				 
		$colOrder 	= array(null,'tipe','catalog','barcode','status','klasifikasi','title','author','publisher_name','published_year','lokasi','eksemplar','harga',null); //set column field database for datatable orderable
		$colSearch 	= array('tipe','catalog','barcode','status','klasifikasi','title','author','publisher_name','published_year','lokasi','eksemplar','harga'); //set column field database for datatable
		// $order 		= "order by klasifikasi,author_code,title asc"; // default order 
		 
		$this->datatables->set_table($table);
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order);
		
		$list = $this->datatables->get_datatables(); 
		$data = array(); 
		foreach ($list as $dt) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $dt->tipe;  
			$row[] = $dt->catalog; 
			$row[] = $dt->klasifikasi;
			$row[] = $dt->title;
			$row[] = $dt->author;
			$row[] = $dt->publisher_name;
			$row[] = $dt->published_year; 
			$row[] = $dt->eksemplar; 
		
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
	
	function collection() { 
		$data['type'] = $this->Katalogmodel->getknowledgetype()->result();  
		$data['menu'] 	= 'katalog/collection';  
		$data['location'] 		= $this->KatalogMkModel->get_location()->result();
		$this->load->view('theme', $data);
    }
	
	public function ajax_index()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$month 			= $this->input->post('month');
		$status 		= $this->input->post('status');
		$type 			= $this->input->post('type');
		$klasifikasi	= $this->input->post('klasifikasi');
		$origination	= $this->input->post('origination');
		$location		= $this->input->post('location');

		$where = "";
		if (!empty($month)) {
			$tgl 	= explode(' to ',$month);
			$start 	= explode('-',$tgl[0]);
			$end 	= explode('-',$tgl[1]);
			$where .="and ks.entrance_date BETWEEN '".$start[2]."-".$start[1]."-".$start[0]." 00:00:00' AND '".$end[2]."-".$end[1]."-".$end[0]." 23:59:59' ";
			
		}	 
		
		if (is_array($location)){  
			$where .= "and ks.item_location_id in (".implode(",",$location).") ";   
		} 
		
		if(is_array($status)){
			$where .= "and ks.status in (".implode(',',$status).")";
		} 
		
		if(is_array($type)){
			 
			$where .= "and kt.knowledge_type_id in (".implode(',',$type).")";
		} 
		
		
		if ($klasifikasi!="") {  
			if($klasifikasi=='0'){
				$where2="where (codes2='0' or (codes2>0 and codes2<100))";
			}
			else {
				$where2="where codes2>=".$klasifikasi."00  and codes2<".$klasifikasi."99 ";
				
			}
		}	 
		
		if ($origination!="") {   
			$where.=" and ks.origination='".$origination."'"; 
		}
		
		$table 		= "select * from (SELECT il.name location_name, ks.id id, kt. CODE catalog, ks.status, ks. CODE barcode, cc. CODE klasifikasi, title,ks.origination,
						author, publisher_name,kl.name tipe,published_year,replace(ltrim(replace(cc.code,'0',' ')),' ','0') codes2 FROM knowledge_item kt
						LEFT JOIN knowledge_stock ks ON kt.id = knowledge_item_id
						LEFT JOIN knowledge_subject kss ON kss.id = kt.knowledge_subject_id
						LEFT JOIN classification_code cc ON cc.id = kt.classification_code_id
						LEFT JOIN item_location il ON il.id = ks.item_location_id
						LEFT JOIN knowledge_type kl ON kl.id = kt.knowledge_type_id 
						where 1=1 and kss.active='1' $where order by published_year desc,status,kl.id,kt.id asc)a $where2";
						// echo $table;
		$colOrder 	= array(null,'tipe','catalog','barcode','status','klasifikasi','title','author','publisher_name','published_year','location_name',null); //set column field database for datatable orderable
		$colSearch 	= array('tipe','catalog','barcode','status','klasifikasi','title','author','publisher_name','published_year','location_name'); //set column field database for datatable
		$order 		= "order by published_year desc"; // default order 
		
		$this->datatables->set_table($table); 
		$this->datatables->set_col_order($colOrder);
		$this->datatables->set_col_search($colSearch);
		$this->datatables->set_order($order);
		
		$list = $this->datatables->get_datatables();
		//print_r($list);
		$data = array();
		$no = $_POST['start'];
		$dts[1] = "Tersedia";
		$dts[2] = "Dipinjam";
		$dts[3] = "Rusak";
		$dts[4] = "Hilang";
		$dts[5] = "Expired";
		$dts[6] = "Hilang Diganti";
		$dts[7] = "Sedang Diproses";
		$dts[8] = "Cadangan";
		$dts[9] = "Weeding";
		foreach ($list as $dt) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $dt->tipe;  
			$row[] = $dt->catalog;
			$row[] = $dt->barcode;
			$row[] = $dts[$dt->status];
			$row[] = ($dt->origination=='1'?'Beli':'Sumbangan');
			$row[] = $dt->klasifikasi;
			$row[] = $dt->title;
			$row[] = $dt->author;
			$row[] = $dt->publisher_name;
			$row[] = $dt->published_year;
			$row[] = $dt->location_name;
		
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
	
	
	
	public function excel()
	{ 		    
		
		$tipe 			= $_POST['tipe'];
		$origination 	= $_POST['status']; 
		$status_book 	= $_POST['status_book']; 

		
				
		$tgl 	= explode(' to ',$_POST['reservation']);
		$start 	= explode('-',$tgl[0]);
		$end 	= explode('-',$tgl[1]);
		$klasifikasi	= $this->input->post('klasifikasi');

		
		$where ="and 
		ks.created_at BETWEEN '".$start[2]."-".$start[1]."-".$start[0]." 00:00:00' AND '".$end[2]."-".$end[1]."-".$end[0]."  23:59:59' ".($tipe!='all'?"and ks.knowledge_type_id='$tipe' ":'').($origination!='all'?"and ks.origination='$origination'":'');

		$where .=" and status='".$status_book."'";
		
		if (count($_POST['location'])!=0){ 
			$data['location_choose'] = $_POST['location'];
			$where .= "and ks.item_location_id in (".implode(",",$_POST['location']).") ";   
		}  

		
		if ($klasifikasi!="") {  
			if($klasifikasi=='0'){
				$where2="where (codes2='0' or (codes2>0 and codes2<100))";
			}
			else {
				$where2="where codes2>=".$klasifikasi."00  and codes2<".$klasifikasi."99 ";
				
			}
		}
 	
	 
	
		$book = $this->Katalogmodel->getbookonprocess($where,$where2)->result(); 
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setTitle("Pengolahan")
						 ->setDescription("description");			 
		  
		
		$objPHPExcel->getActiveSheet()->setCellValue('A2', 'JENIS');
		$objPHPExcel->getActiveSheet()->setCellValue('B2', 'NO KATALOG');
		$objPHPExcel->getActiveSheet()->setCellValue('C2', 'BARCODE');
		$objPHPExcel->getActiveSheet()->setCellValue('D2', 'NO KLASIFIKASI');
		$objPHPExcel->getActiveSheet()->setCellValue('E2', 'JUDUL');
		$objPHPExcel->getActiveSheet()->setCellValue('F2', 'PENGARANG');
		$objPHPExcel->getActiveSheet()->setCellValue('G2', 'PENERBIT');
		$objPHPExcel->getActiveSheet()->setCellValue('H2', 'LOKASI');
		$objPHPExcel->getActiveSheet()->setCellValue('I2', 'STATUS');
		$i 		= 3;
		$keys 	= ""; 

		// print_r($book);
		foreach ($book as $row) { 
			  
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,ucwords(strtolower($row->tipe))); 
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$i, ucwords(strtolower($row->catalog)), PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$i, ucwords(strtolower($row->barcode)), PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.$i, ucwords(strtolower($row->klasifikasi)), PHPExcel_Cell_DataType::TYPE_STRING); 
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, ucwords(strtolower($row->title)));
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, ucwords(strtolower($row->author)));
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, ucwords(strtolower($row->publisher_name)));
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, ucwords(strtolower($row->location_name)));
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, ucwords(strtolower($row->origination=='1'?'beli':'sumbangan')));
			$i++; 
		
		} 
		$objPHPExcel->setActiveSheetIndex(0);

		// Save it as an excel 2003 file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		// $objWriter->save('php://output');str_replace("world","Peter","Hello world!");
		$filePath = 'downloads/pengolahan_'.strtotime("now").'.xlsx';
		$objWriter->save($filePath);
		echo $filePath;
	}
	
	function pengolahan() {  
		$data['tipe'] 		= $this->KatalogMkModel->get_type()->result(); 
		$data['location'] 		= $this->KatalogMkModel->get_location()->result();
		$data['klasifikasi'] 	= $_POST['klasifikasi'];
		$klasifikasi			= $_POST['klasifikasi'];
		$data['location_choose'] = array();
		if (ISSET($_POST['olah'])){
			//print_r($_POST);
			$item = array("status" => '1',
						"updated_by" => $this->session->userdata("username"),
						"updated_at" => date ('Y-m-d H:i:s')); 
			foreach ($_POST['olah'] as $row){
				$this->Katalogmodel->ubahStatus($row,$item);
			}
		} 
		$where = "";
		$where2 = "";

		// print_r($_POST);
		
		if ($_POST['reservation']!="" ){
				$data['reservation'] 	= $_POST['reservation'];  
				$tgl 	= explode(' to ',$_POST['reservation']);
				$start 	= explode('-',$tgl[0]);
				$end 	= explode('-',$tgl[1]);
				$where .="and 
				ks.created_at BETWEEN '".$start[2]."-".$start[1]."-".$start[0]." 00:00:00' AND '".$end[2]."-".$end[1]."-".$end[0]."  23:59:59'";   
		}  
		
		if (($_POST['tipe']!="all")){
				$data['choose'] = $_POST['tipe'];
				$where .= "and ks.knowledge_type_id='".$_POST['tipe']."' ";   
		}  
		
		if (($_POST['barcode']!="")){
				$data['barcode'] = $_POST['barcode'];
				$where .= "and ks.code='".$_POST['barcode']."' ";   
		} 
		
		if (($_POST['status']!="all")){
				$data['status'] = $_POST['status'];
				$where .= "and ks.origination='".$_POST['status']."' ";   
		} 
		
		if (($_POST['status_book']!="all")){
				$data['status_book'] = $_POST['status_book'];
				$where .= "and ks.status='".$_POST['status_book']."' ";   
		} 
		
		// if (count($_POST['location'])!=0){ 
		// 		$data['location_choose'] = $_POST['location'];
		// 		$where .= "and ks.item_location_id in (".implode(",",$_POST['location']).") ";   
		// } 

		if ($klasifikasi!="") {  
			if($klasifikasi=='0'){
				$where2="where (codes2='0' or (codes2>0 and codes2<100))";
			}
			else {
				$where2="where codes2>=".$klasifikasi."00  and codes2<".$klasifikasi."99 ";
				
			}
		}	 
		
		$data['book'] = $this->Katalogmodel->getbookonprocess($where,$where2)->result();
		
		$data['menu'] 	= 'katalog/pengolahan'; 
		$this->load->view('theme', $data);
    } 
	
	function content() { 
		if ($_POST['id']=='0'){
			echo '<div class="form-group">
										 <span class="col-md-1 col-sm-1 col-xs-12">     <button   type="submit" value="submit" name="submit" class="btn btn-success">Report</button></span> 
										</div>';
		}
		else {
			echo 					' <div class="form-group">
											<label class="control-label col-md-1 col-sm-1 col-xs-12" style="padding-top:8px;">Tanggal</label>
											<div class="col-md-11 col-sm-11 col-xs-12">
												<div class="controls">
													<div class="input-prepend input-group">
														<span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
														<input type="text" style="width: 200px" name="reservation" id="reservation" class="form-control" value="" />
													</div>
												</div>
											</div>   
										</div> 
										<div class="form-group">
											&nbsp;
										</div>
										<div class="form-group">
											<span class="col-md-1 col-sm-1 col-xs-12">     <button   type="submit" value="submit" name="submit" class="btn btn-success">Report</button></span> 
										</div>';
		}
    }
	 
	
	
	public function download($file)
	{
		$this->load->helper('download');
		$file = urldecode($file);
		$data = file_get_contents(FCPATH."downloads/".$file); // Read the file's contents
		$name = $file;

		force_download($name, $data); 
	}
	
	public function lists_excel()
	{  
		if(!$this->input->is_ajax_request()) return false; 
		
		$month 			= $this->input->post('date');
		$status 		= $this->input->post('status');
		$type 			= $this->input->post('type');
		$klasifikasi	= $this->input->post('klasifikasi');
		$origination	= $this->input->post('origination');
		$location	= $this->input->post('location');

		
		if (!empty($month)) {
			$tgl 	= explode(' to ',$month);
			$start 	= explode('-',$tgl[0]);
			$end 	= explode('-',$tgl[1]);
			$where .="and ks.entrance_date BETWEEN '".$start[2]."-".$start[1]."-".$start[0]." 00:00:00' AND '".$end[2]."-".$end[1]."-".$end[0]." 23:59:59' ";
			
		}	 

		 
		
		if (is_array($location)){  
			$where .= "and ks.item_location_id in (".implode(",",$location).") ";   
		} 
		
		if(is_array($status)){
			$where .= "and ks.status in (".implode(',',$status).")";
		} 
		
		if(is_array($type)){
			$where .= "and kt.knowledge_type_id in (".implode(',',$type).")";
		} 
		
		
		
		if($origination!=""){
			$where .= "and ks.origination ='$origination'";
		} 
		
		if ($klasifikasi!="") {  
			if($klasifikasi=='0'){
				$where2="where (codes2='0' or (codes2>0 and codes2<100))";
			}
			else {
				$where2="where codes2>=".$klasifikasi."00  and codes2<".$klasifikasi."99 ";
				
			}
		}	
		
		$book = $this->Katalogmodel->getbook($where,$where2)->result();
		$objPHPExcel = new PHPExcel();
	
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Buku Sirkulasi")
									 ->setTitle("Buku Sirkulasi")
									 ->setSubject("Buku Sirkulasi")
									 ->setDescription("Buku Sirkulasi")
									 ->setKeywords("Buku Sirkulasi")
									 ->setCategory("Buku Sirkulasia");


		$sheet = $objPHPExcel->getActiveSheet();
		
		
		
		$sheet->getStyle("A1:H3")->getFont()->setBold(true)
				->setSize(12);									
		
		$sheet->getStyle('A3:H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	  
		
		$styleArray = array(
		  'borders' => array(
			'allborders' => array(
			  'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		  )
		);

		// $objPHPExcel->getActiveSheet()->getStyle('A4:Y500')->applyFromArray($styleArray);
		// unset($styleArray);
		
		$objPHPExcel->setActiveSheetIndex(0)
					 
					->setCellValue('A1', "Buku Sirkulasi ".$reservation) 
					->setCellValue('A3', "No") 
					->setCellValue('C3', "Tipe")
					->setCellValue('D3', "Katalog")
					->setCellValue('E3', "Barcode")
					->setCellValue('F3', "Status")
					->setCellValue('G3', "Asal penerimaan")
					->setCellValue('F3', "Klasifikasi")
					->setCellValue('H3', "Judul")
					->setCellValue('I3', "Pengarang")
					->setCellValue('J3', "Penerbit")
					->setCellValue('K3', "Tahun"); 
		
		$no = 1;
		$i  = 4;
		foreach ($book as $row){
			
			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValueExplicit('A'.$i, $no, PHPExcel_Cell_DataType::TYPE_STRING) 
					->setCellValueExplicit('B'.$i, $row->tipe, PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('C'.$i, $row->catalog, PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('D'.$i, $row->barcode, PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('E'.$i, $row->status, PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('F'.$i, ($row->origination?'Beli':'Sumbangan'), PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('G'.$i, $row->klasifikasi, PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('H'.$i, $row->title, PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('I'.$i, $row->author, PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('J'.$i, $row->publisher_name, PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('K'.$i, $row->published_year, PHPExcel_Cell_DataType::TYPE_STRING); 
			
			$no++;
			$i++;
		}
					
					 
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Buku Sirkulasi');


		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);


		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="BukuSirkulasi".xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		//echo $objWriter->save('php://output');
		
		$filePath = FCPATH.'downloads/BukuSirkulasi.xlsx';
		//echo $filePath;
		$objWriter->save($filePath);
		echo json_encode(urlencode('BukuSirkulasi.xlsx'));
		exit();
	}
	
	public function lists_catalog()
	{  
		ini_set('memory_limit','-1'); 
		ini_set('display_errors', 'On');
		
		$month 			= $this->input->post('date');
		$klasifikasi	= $this->input->post('klasifikasi');
		$type 			= $this->input->post('type'); 

		
		
		$where  = "";
		$where2 = "";
		if (!empty($month)) {
			$tgl 	= explode(' to ',$month);
			$start 	= explode('-',$tgl[0]);
			$end 	= explode('-',$tgl[1]);
			$where .="and ks.entrance_date BETWEEN '".$start[2]."-".$start[1]."-".$start[0]." 00:00:00' AND '".$end[2]."-".$end[1]."-".$end[0]." 23:59:59' ";
			
		}	  
		
		if(is_array($type)){
			$where .= "and kt.knowledge_type_id in (".implode(',',$type).")";
		}    
		
		if ($klasifikasi!="") {  
			if($klasifikasi=='0'){
				$where2="where (codes2='0' or (codes2>0 and codes2<100))";
			}
			else {
				$where2="where codes2>=".$klasifikasi."00  and codes2<".$klasifikasi."99 ";
				
			}
		}	  
		
		
		$data['book'] = $this->Katalogmodel->getcatalog_book($where,$where2)->result(); 
		
        $html = $this->load->view('katalog/print_catalog', $data, true);   
		// print_r($data);
		
										//urutan margin mpdf -> left-right-top-bottom
		$pdf  			= new mPDF('c','A4','','','10','10','10','15');
		// echo FCPATH.'downloads/print_catalog.pdf';
		$pdfFilePath = FCPATH.'downloads/print_catalog.pdf'; 
		$pdf->WriteHTML($html); 
		$pdf->Output($pdfFilePath, 'F');
		
		echo json_encode(urlencode('print_catalog.pdf'));
	}
} 

?>