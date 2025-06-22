<?php

class Katalog extends CI_Controller {
 
    function __construct() {
        parent::__construct(); 
		$this->load->model('KatalogModel');
		$this->load->library('phpExcel/PHPExcel');   
		$this->load->library('mpdf/mpdf');   
    }

    function index() { 
		$data['jurusan'] = $this->KatalogModel->getallknowledgetype()->result(); 
		$data['site'] 	= 'katalog'; 
		$data['view'] 	= 'katalog/katalog'; 
		$this->load->view('main', $data);
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
				$data['data'][$i]		= $this->KatalogModel->getEcatalog($data['month'],$code_awal,$code_akhir)->result(); 
			}  
		
		}
		// echo "<pre>";
		// print_r($data['data']);
		// echo "</pre>";
		
		// echo "<pre>";
		// print_r($data['data']);
		// echo "</pre>";
		
		$data['site'] 	= 'e-catalog'; 
		$data['view'] 	= 'katalog/ecatalog'; 
		$this->load->view('main', $data);
    } 
	
	public function ecatalog_pdf($month)
	{  
		ini_set('memory_limit','-1'); 
		
		 
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
		$html = "<img src='../../../data/batik/www/uploads/banner/ecataloguebanner.jpg'><br><br>";
		
		foreach ($class as $i => $val){
			 
			$code_awal 	= $i."00";
			$code_akhir = $i."99"; 
			$data[$i]		= $this->KatalogModel->getEcatalog($month,$code_awal,$code_akhir)->result(); 
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
				if ($row->cover_path!="") $file = $row->cover_path; 
					else $file="default.jpg";
					if ($count==1) $html.="<tr><td colspan='3'><hr style='height:1px; border:none; color:#c2c2a3; background-color:#000;'></td></tr>";
					$title = $this->mainapi->clean(strtolower($row->title)); 
					$url   = "https://openlibrary.telkomuniversity.ac.id/pustaka/".$row->cat_id."/".$title.".html";
				 
					$html .= "<tr class='".$style."'>";
					$html .= "<td style='font-size:14px;font-family:calibri; margin:5px;' width='10%' align='center'  valign='top'><img src='../../../data/batik/www/uploads/book/cover/".$file."' width='90px'><br>".$row->cat_code."</td>";
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
						$html .= $this->mainapi->limit_text($row->abstract_content,30)."<a href='".$url."'>selengkapnya..</a><br>";
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
		 
		
		$mo = array ("01" => "Januari","02" => "Februari","03" => "Maret","04" => "April","05" => "Mei","06" => "Juni","07" => "Juli","08" => "Agustus","09" => "September","10" => "Oktober","11" => "November","12" => "Desember",
					);
		
		$tgl = explode("-",$month);
										//urutan margin mpdf -> left-right-top-bottom
		$pdf  			= new mPDF('c','A4','','','10','10','10','15');
		$pdfFilePath 	= 'catalogue.pdf';  
		$pdf->SetHTMLFooter('
			<table width="100%" style="vertical-align: bottom; font-family: calibri; font-size: 11px; color: #000000; font-weight: bold;"><tr>
			<td width="99%" style="text-align: right; ">Page {PAGENO}</td>
			</tr></table>
			');  
		$pdf->WriteHTML($html); 
		$pdf->Output($pdfFilePath, 'D');
	}
	
	function lists() {
		if (ISSET($_POST['submit']) and $_POST['reservation']!=""){
				$data['reservation'] 	= $_POST['reservation']; 
				
				$tgl 	= explode(' - ',$_POST['reservation']);
				$start 	= explode('/',$tgl[0]);
				$end 	= explode('/',$tgl[1]);
				$where ="and 
				ks.created_at BETWEEN '".$start[2]."-".$start[0]."-".$start[1]." 00:00:00' AND '".$end[2]."-".$end[0]."-".$end[1]."  23:59:59' ";  
			$data['book'] = $this->KatalogModel->getbook($where)->result(); 
		}
		
		$data['site'] 	= 'list katalog'; 
		$data['view'] 	= 'katalog/lists'; 
		$this->load->view('main', $data);
    }
	
	function pengolahan() {  
		if (ISSET($_POST['submit'])){
			$item = array("status" => '1',
						"updated_by" => $this->session->userdata("username"),
						"updated_at" => date ('Y-m-d H:i:s')); 
			foreach ($_POST['olah'] as $row){
				$this->KatalogModel->ubahStatus($row,$item);
			}
		}
		
		if (ISSET($_POST['report']) and $_POST['reservation']!=""){
				$data['reservation'] 	= $_POST['reservation']; 
				
				$tgl 	= explode(' - ',$_POST['reservation']);
				$start 	= explode('/',$tgl[0]);
				$end 	= explode('/',$tgl[1]);
				$where ="and 
				ks.created_at BETWEEN '".$start[2]."-".$start[0]."-".$start[1]." 00:00:00' AND '".$end[2]."-".$end[0]."-".$end[1]."  23:59:59' ";  
			$data['book'] = $this->KatalogModel->getbookonprocess($where)->result(); 
		} 
		else $data['book'] = $this->KatalogModel->getbookonprocess()->result();
		
		$data['site'] 	= 'pengolahan'; 
		$data['view'] 	= 'katalog/pengolahan'; 
		$this->load->view('main', $data);
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
		$file = urldecode($file);
		$data = file_get_contents(FCPATH."download/".$file); // Read the file's contents
		$name = $file;

		force_download($name, $data); 
	}
	
	public function lists_excel()
	{  
		if(!$this->input->is_ajax_request()) return false;
		$reservation 	= $this->input->post('date');
		$tgl 			= explode(' - ',$reservation);
		$start 			= explode('/',$tgl[0]);
		$end 			= explode('/',$tgl[1]);
		$where ="and 
				ks.created_at BETWEEN '".$start[2]."-".$start[0]."-".$start[1]." 00:00:00' AND '".$end[2]."-".$end[0]."-".$end[1]."  23:59:59' ";  
		$book = $this->KatalogModel->getbook($where)->result();
		
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
					->setCellValue('B3', "Tipe")
					->setCellValue('C3', "Katalog")
					->setCellValue('D3', "Barcode")
					->setCellValue('E3', "Klasifikasi")
					->setCellValue('F3', "Judul")
					->setCellValue('G3', "Pengarang")
					->setCellValue('H3', "Penerbit"); 
		
		$no = 1;
		$i  = 4;
		foreach ($book as $row){
			
			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValueExplicit('A'.$i, $no, PHPExcel_Cell_DataType::TYPE_STRING) 
					->setCellValueExplicit('B'.$i, $row->tipe, PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('C'.$i, $row->catalog, PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('D'.$i, $row->barcode, PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('E'.$i, $row->klasifikasi, PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('F'.$i, $row->title, PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('G'.$i, $row->author, PHPExcel_Cell_DataType::TYPE_STRING)
					->setCellValueExplicit('H'.$i, $row->publisher_name, PHPExcel_Cell_DataType::TYPE_STRING); 
			
			$no++;
			$i++;
		}
					
					 
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Buku Sirkulasi');


		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);


		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Buku Sirkulasi".xlsx"');
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
		
		$filePath = FCPATH.'download/Buku Sirkulasi.xlsx';
		$objWriter->save($filePath);
		echo json_encode(urlencode('Buku Sirkulasi.xlsx'));
		exit();
	}
}

?>