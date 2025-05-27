<?php

 
class Creamer extends CI_Controller {

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct(); 
		
		//$this->load->library('mpdf/mpdf'); 
		$this->load->model('CreamerModel'); 
		//$this->load->library('paging');
		//$this->load->library('pagination');
		//$this->load->library('mpdf/mpdf');
		ini_set('MAX_EXECUTION_TIME', -1);
		ini_set('memory_limit','-1');
		
		
    }
	
	function hp() { 
		//$message = '[OPENLIBRARY]\Diberitahukan bahwa besok, tanggal 07-04-2016 adalah batas akhir peminjaman buku dengan kode 8.791-E*.\nTerimakasih';
		//$url = "http://10.13.14.171/sendsms.php?appname=OPENLIBRARY&number=08112137203&text=".urlencode($message);
		//echo	file_get_contents($url);
		$member = $this->CreamerModel->getMember();
		foreach($member as $row){
			$md = $this->CreamerModel->getMasterData($row->master_data_user)->row();
			echo $row->master_data_user." ".$md->NO_HP." ";
			if($md->NO_HP!="") {
				$this->CreamerModel->updateMembers($row->master_data_user,$md->NO_HP);
			}
		 }
    }    
	
	private function matakuliah() { 
		$subject = $this->CreamerModel->GetSubject()->result();
		foreach($subject as $row){
			$semester = 0;
			if ($row->tipe=='TINGKAT 1'){
				if($row->semester=='Ganjil') $semester = 1;
				else $semester = 2;
			}
			else if ($row->tipe=='TINGKAT 2'){
				if($row->semester=='Ganjil') $semester = 3;
				else $semester = 4;
			}
			else if ($row->tipe=='TINGKAT 3'){
				if($row->semester=='Ganjil') $semester = 5;
				else $semester = 6;
			}
			else if ($row->tipe=='TINGKAT 4'){
				if($row->semester=='Ganjil') $semester = 7;
				else $semester = 8;
			}
			
			$data = array ( 
				'curriculum_code'	=>'2016',
				'course_code'		=>$row->prodi,
				'code'				=>$row->kode_mk,
				'name'				=>$row->nama_mk,
				'semester'			=>$semester
			);
			
			$this->CreamerModel->InsertMK($data);
		}
    } 
	
	function mapping() { 
		$kode = $this->CreamerModel->GetNamaMK()->result();
		
					$i=0;
		foreach($kode as $row){
			//echo $row->code;
			$mapping = $this->CreamerModel->GetMappingMasterSubject($row->name,$row->course_code)->result();
			foreach($mapping as $map){
				$data = $this->CreamerModel->GetMasterSubject($row->name,$row->course_code)->result();
				foreach($data as $dt){
					$check = $this->CreamerModel->CheckMapping($map->knowledge_item_id,$dt->id)->num_rows();
					//echo $check;
					if ($check==0){
						$datas = array ( 
							'knowledge_item_id'	=>$map->knowledge_item_id,
							'master_subject_id'	=>$dt->id
						);
						
						echo "<br>".$i++;
						$this->CreamerModel->InsertMapping($datas);
					}
					
				}
			}
			
		}
    } 
	
	
	function smsRent($stock="",$member="") { 
		$url = "http://10.13.14.171/sendsms.php?appname=OPENLIBRARY&number=08112137203&text=".urlencode("hello world");
		file_get_contents($url);
		//echo $content;
    } 
	
	
	function smsReturn($stock,$member) { 
		//echo $stock." ".$member;
		$username 	= $this->CreamerModel->GetUsername($member)->row();
		$member 	= $this->CreamerModel->GetPhone($username->user)->row();
		
		$stok = explode("_",$stock);
		print_r($stok);
		 
		$kode = array();
		foreach ($stok as $row){
			$cat 	= $this->CreamerModel->GetCatalogue($row)->row();
			$kode[] = $cat->kode;
		} 
		$kode = implode(" ",$kode);
		 
		
		//$a = file_get_contents('http://10.14.203.4:9333/ozeki?action=sendMessage&ozmsUserInfo=admin%3Aabc123&recepient='.$member->hp.'&messageData='.$kode);
		
		echo 'http://10.14.203.4:9333/ozeki?action=sendMessage&ozmsUserInfo=admin%3Aabc123&recepient='.$member->hp.'&messageData='.$kode;
    }
	
	function scheduler() { 
	
		//update file total
		$listFile=array();
		$hitFile =array();
		$upload	 = $this->CreamerModel->getUploadType(); 
		foreach($upload as $up){
			if($up->extension=='pdf'){
				$listFile[] = $up->name.'.'.$up->extension;
				$ids[] = $up->id;
				$secure[] = $up->is_secure;
				$hitFile[]  = 0;
			}
		}
	
		$dir    = '../../../data/batik/symfony_projects/book';
		$files1 = scandir($dir);

		foreach ($files1 as $row){
			if ($row!='.' && $row!='..'){ 
				for ($i=0;$i<count($listFile);$i++){
					$file    = '../../../data/batik/symfony_projects/book/'.$row.'/'.$row.'_'.$listFile[$i];
					if (file_exists($file)) $hitFile[$i]++;
				} 
			}
		} 
		
		$this->CreamerModel->DeleteFileTotal(); 
		for ($i=0;$i<count($listFile);$i++){
			$data = array ('nama_file' => $listFile[$i], 'total_file' => $hitFile[$i], 'file_id' => $ids[$i], 'keterangan' => $secure[$i]);
			$this->CreamerModel->InsertFileTotal($data);
		} 
		//===============================================================================
		
		echo "success   ";
		
		
    }
	
	function rename_jurnals() { 
	
		//update file total
		$listFile=array();
		$hitFile =array();
		$upload	 = $this->CreamerModel->getFileRename(); 
		foreach($upload as $up){
			$file    = '../../../data/batik/symfony_projects/book/'.$up->codes.'/'.$up->codes.'_jurnal.pdf';
			$files    = '../../../data/batik/symfony_projects/book/'.$up->codes.'/'.$up->codes.'_jurnals.pdf';
			if (file_exists($file)) {
				rename($file, $files);
			}
		} 
    }
	
	function total_file_per_bulan() { 
	
		//update file total
		$listFile=array();
		$hitFile =array();
		$upload	 = $this->CreamerModel->getUploadType(); 
		foreach($upload as $up){
			if($up->extension=='pdf'){
				$listFile[] = $up->name.'.'.$up->extension;
				$hitFile[]  = 0;
			}
		}
	
		$dir    = '../../../data/batik/symfony_projects/book';
		$files1 = scandir($dir);

		foreach ($files1 as $row){
			if ($row!='.' && $row!='..'){ 
				for ($i=0;$i<count($listFile);$i++){
					$file    = '../../../data/batik/symfony_projects/book/'.$row.'/'.$row.'_'.$listFile[$i];
					if (file_exists($file)) {
						
						
						$dateBook = date ("Y-m-d H:i:s", filemtime($file)); 
						//echo $dateBook."<br>";
						$date = explode ("-",$dateBook);
						if ($date[0]<2016) {
							$hitFile[$i]++;
						}
						else if ($date[0]==2016 AND intval($date[1])<=6){
							 $hitFile[$i]++;
						}
						//echo $file." ".date ("Y-m-d H:i:s", filemtime($file))."<br>";
						
						
					}
				} 
			}
		} 
		
		// $this->CreamerModel->DeleteFileTotal(); 
		for ($i=0;$i<count($listFile);$i++){
			$data = array ('nama_file' => $listFile[$i], 'total_file' => $hitFile[$i]);
			echo $hitFile[$i]."<br>";
			//$this->CreamerModel->InsertFileTotal($data);
		} 
		//===============================================================================
		
		
		
    }
	
	// function audioVisual() {
		// $audiovisual = $this->CreamerModel->koleksiAudioVisual();
		
		
		
		// foreach ($audiovisual as $row){
			// $lastCode = $this->CreamerModel->lastKodeKatalog();
			// $code = explode(".",$lastCode->code);
			// $code = ltrim($code[2], '0');
			// $code++;
			// $code = sprintf("%03s", $code);
			
			// $data = array ( 
				// 'knowledge_type_id'			=>'15',
				// 'classification_code_id'	=>'1',
				// 'item_location_id'			=>'8',
				// 'code'						=>'16.15.'.$code,
				// 'collation'					=>'',
				// 'faculty_code'				=>null,
				// 'course_code'				=>null,
				// 'title'						=>$row->av_judul,
				// 'author'					=>$row->av_pengarang,
				// 'knowledge_subject_id'		=>'1',
				// 'alternate_subject'			=>null,
				// 'isbn'						=>'',
				// 'author_type'				=>'1',
				// 'translator'				=>null,
				// 'editor'					=>null,
				// 'publisher_name'			=>$row->av_penerbit,
				// 'publisher_city'			=>null,
				// 'published_year'			=>'',
				// 'language'					=>null,
				// 'origination'				=>'2',
				// 'supplier'					=>'mahasiswa',
				// 'price'						=>'0',
				// 'entrance_date'				=>$row->av_tgl_masuk,
				// 'abstract_content'			=>$row->av_abstrak,
				// 'cover_path'				=>null,
				// 'softcopy_path'				=>null,
				// 'penalty_cost'				=>null,
				// 'rent_cost'					=>null,
				// 'created_by'				=>'',
				// 'created_at'				=>$row->av_tgl_masuk,
				// 'updated_by'				=>'',
				// 'updated_at'				=>''
			// );
			
			// $id = $this->CreamerModel->InsertKatalog($data);
			
			
			// $audiovisualeks = $this->CreamerModel->koleksiAudioVisualEks($row->av_id);
			
			// foreach ($audiovisualeks as $eks){
				
				// $lasteksCode = $this->CreamerModel->lastKodeEksKatalog($id);
				// if ($lasteksCode->num_rows()!=0){
					// $lasteksCode = $lasteksCode->row();
					// $ekscode = explode("-",$lasteksCode->code); 
					// $ekscode = $ekscode[1]+1; 
					// //echo $ekscode;
				// }
				// else {
					// $ekscode = 1;
				// }
				
				// $data = array ( 			 
					// 'knowledge_item_id'	=>$id,
					// 'knowledge_type_id'	=>'15',
					// 'item_location_id'	=>'8',
					// 'code'				=>'16.15.'.$code.'-'.$ekscode,
					// 'faculty_code'		=>null,
					// 'course_code'		=>'',
					// 'origination'		=>'2',
					// 'supplier'			=>$eks->aveks_pemasok,
					// 'price'				=>'0',
					// 'entrance_date'		=>$row->av_tgl_masuk,
					// 'status'			=>'1',
					// 'created_by'		=>'',
					// 'created_at'		=>$row->av_tgl_masuk,
					// 'updated_by'		=>'',
					// 'updated_at'		=>''
				// );
				
				// $this->CreamerModel->InsertEksKatalog($data);
				
			// }
			
			

		// }
		// // echo "<pre>";
		// // print_r($koleksi);
		// // echo "</pre>";
	// }
	
	// function knowledgeitem() {
		// ini_set('MAX_EXECUTION_TIME', -1);
		// ini_set('memory_limit','-1');
		// $audiovisual = $this->CreamerModel->knowledge_item();
		
		
		
		// foreach ($audiovisual as $row){
		 
			// $data = array ( 
				// 'id'						=>$row->id,
				// 'knowledge_type_id'			=>$row->knowledge_type_id,
				// 'classification_code_id'	=>$row->classification_code_id,
				// 'item_location_id'			=>$row->item_location_id,
				// 'code'						=>$row->code,
				// 'collation'					=>$row->collation,
				// 'faculty_code'				=>$row->faculty_code,
				// 'course_code'				=>$row->course_code,
				// 'title'						=>$row->title,
				// 'author'					=>$row->author,
				// 'knowledge_subject_id'		=>$row->knowledge_subject_id,
				// 'alternate_subject'			=>$row->alternate_subject,
				// 'isbn'						=>$row->isbn,
				// 'author_type'				=>$row->author_type,
				// 'translator'				=>$row->translator,
				// 'editor'					=>$row->editor,
				// 'publisher_name'			=>$row->publisher_name,
				// 'publisher_city'			=>$row->publisher_city,
				// 'published_year'			=>$row->published_year,
				// 'language'					=>$row->language,
				// 'origination'				=>$row->origination,
				// 'supplier'					=>$row->supplier,
				// 'price'						=>$row->price,
				// 'entrance_date'				=>$row->entrance_date,
				// 'abstract_content'			=>$row->abstract_content,
				// 'cover_path'				=>$row->cover_path,
				// 'softcopy_path'				=>$row->softcopy_path,
				// 'penalty_cost'				=>$row->penalty_cost,
				// 'rent_cost'					=>$row->rent_cost,
				// 'created_by'				=>$row->created_by,
				// 'created_at'				=>$row->created_at,
				// 'updated_by'				=>$row->updated_by,
				// 'updated_at'				=>$row->updated_at
			// );
			
			// $this->CreamerModel->InsertKatalog($data);
			 
			// $ekss = $this->CreamerModel->knowledge_stock($row->id); 
			
			// foreach ($ekss as $eks){ 
				
				// $data = array ( 
					// 'id'				=>$eks->id,
					// 'knowledge_item_id'	=>$eks->knowledge_item_id,
					// 'knowledge_type_id'	=>$eks->knowledge_type_id,
					// 'item_location_id'	=>$eks->item_location_id,
					// 'code'				=>$eks->code,
					// 'faculty_code'		=>$eks->faculty_code,
					// 'course_code'		=>$eks->course_code,
					// 'origination'		=>$eks->origination,
					// 'supplier'			=>$eks->supplier,
					// 'price'				=>$eks->price,
					// 'entrance_date'		=>$eks->entrance_date,
					// 'status'			=>$eks->status,
					// 'created_by'		=>$eks->created_by,
					// 'created_at'		=>$eks->created_at,
					// 'updated_by'		=>$eks->updated_by,
					// 'updated_at'		=>$eks->updated_at
				// );
				
				// $this->CreamerModel->InsertEksKatalog($data); 
			// }  
		// } 
	// }
	
	function totFile() {  
		$listFile=array();
		$hitFile =array();
		$upload	 = $this->CreamerModel->getUploadType(); 
		foreach($upload as $up){
			$listFile[] = $up->name.'.'.$up->extension;
			$hitFile[]  = 0;
		}
	
		$dir    = '../../../data/batik/symfony_projects/book';
		$files1 = scandir($dir);

		foreach ($files1 as $row){
			if ($row!='.' && $row!='..'){ 
				//$dirs    = '../../../data/batik/symfony_projects/book/'.$row;
				//$files2 = scandir($dirs);
				// echo "<pre>";
				// print_r($files2);
				// echo "</pre>";
				//foreach ($files2 as $row2){
				//	if ($row2!='.' && $row2!='..'){ 
						for ($i=0;$i<count($listFile);$i++){
							$file    = '../../../data/batik/symfony_projects/book/'.$row.'/'.$row.'_'.$listFile[$i];
							if (file_exists($file)) $hitFile[$i]++;
						}
					//}
				//}
			}
		}
		
		for ($i=0;$i<count($listFile);$i++){
			echo $listFile[$i]."<br>";
		}
		
		for ($i=0;$i<count($listFile);$i++){
			echo $hitFile[$i]."<br>";
		}
	}
	
	function totFile2() {  
		$listFile=array();
		$hitFile =array();
		$knowledgeType	 = $this->CreamerModel->getKnowledgeType();
		
		$upload	 = $this->CreamerModel->getUploadType(); 
		foreach($upload as $up){ 
			$totFile[]  = 0;
			$listFile[] = $up->name.'.'.$up->extension;
		}
		
		foreach ($knowledgeType as $kt) { 
			echo $kt->name."<br><br>";
			$katalog	 = $this->CreamerModel->getKatalogByType($kt->id);
			
			$upload	 = $this->CreamerModel->getUploadType();
			 
			for ($i=0;$i<count($listFile);$i++){
				$hitFile[$i] = 0;
			} 
			
			foreach ($katalog as $row){
				for ($i=0;$i<count($listFile);$i++){
					$file    = '../../../data/batik/symfony_projects/book/'.$row->code.'/'.$row->code.'_'.$listFile[$i];
					// if ($listFile[$i]=="flipbook.html"){
						 // $dir = "https://flippingbookopenlibrary.telkomuniversity.ac.id/".$row->code;
						// $file_headers = @get_headers($dir);
						// if($file_headers[0] != 'HTTP/1.1 404 Not Found') $hitFile[$i]++;
					// }
					// else {
						if (file_exists($file)) {	
							$hitFile[$i]++;
							//echo $row->code."-".$listFile[$i]."<br>";
						}
					//}
				}
			}
			
			 
			for ($i=0;$i<count($listFile);$i++){
				//if ($listFile[$i]=="flipbook.html")
					echo $hitFile[$i]."<br>";
			}
			
			echo "<br><br>";
			
			for ($i=0;$i<count($listFile);$i++){
				$totFile[$i]=$totFile[$i]+$hitFile[$i];
			}
			
			//break;
			
		}
		
		echo "<br><br><br><br><br> Total File<br>";
		
		for ($i=0;$i<count($listFile);$i++){
			//if ($listFile[$i]=="flipbook.html") 
			echo $totFile[$i]."<br>";
		} 
	}
	
	function sortTAFile() {
		$cat = $this->CreamerModel->GetCatalogTA()->result();
		
			$size = array();
			$list = array();
			$i=0;
		foreach ($cat as $row){
			 $file    = '../../../data/batik/symfony_projects/book/'.$row->code.'/'.$row->code.'_resume.pdf';
			if (file_exists($file)) {
				//echo "[".$i."] ".$row->code.'_resume.pdf'. ': ' . round(filesize($file)/1024,2) . ' kb'."<br>";
				$i++;
				$size[] = round(filesize($file)/1024,2);
				$list[] = $row->code.";".$row->fakultas.";".$row->prodi.";".$row->author.";".$row->editor.";".$row->klasifikasi;
				
			}
			//sort($size);
		}
		asort($size);
		foreach ($size as $key => $rs){
			echo $list[$key].";".$rs."<br>";
		}
			// echo "<pre>";
			// print_r($size);
			// echo "</pre>";
	}
	
	function eproc_generate() { 
	
		$html=""; 
			$html = '<table border="5" cellspacing="1" cellpadding="1">
						<tbody>
						<tr width="100%" background="/uploads/left.png">
						<td rowspan="60" width="3%">&nbsp;</td>
						</tr>'; 
						
				$html .='
						<tr>
						<td><img src="/uploads/banner/banner_ejurnal_rupa_2015.png" alt="" width="100%" /></td>
						</tr>'; 
			
			$html.='<tr bgcolor="eeeeee">
					<td style="text-align: right;"><strong></strong></td>
					</tr>';
			 
				$html .='
						<tr style="background-color: #dd2821;">
						<td style="color: #ffffff;"></td>
						</tr>
						<tr>
						<td><ol>'; 
			
				$supplier = 'Jurnal Rupa FIK';
				$dt		 = $this->CreamerModel->getKnowledgeItem($supplier); 
			
				foreach ($dt as $row){
					
					$html.='<li>';
					
					
					$upload	 = $this->CreamerModel->getUploadType(); 
					foreach($upload as $up){
						$eproc  = '../../../data/batik/symfony_projects/book/'.$row->code.'/'.$row->code.'_'.$up->name.'.'.$up->extension;
						$name = "";
						$ext  = "";
						if (file_exists($eproc)){
							
							$name = $up->name;
							$ext  = $up->extension;
							break;
							
						}
						
					} 
					
					if ($name!=""){ 
						$html.='<a href="/pustaka/files/'.$row->id.'/'.$name.'/'.$name.'.'.$ext.'">'.ucwords(strtolower($row->title)).'</a>';
					}
					else $html.=ucwords(strtolower($row->title)); 
					
					$html.='<br>'.ucwords(strtolower($row->author)).'</li>';
				}
				$html.='</ol>
						</td>
						</tr>';
		 
			$html.='
					<tr>
					<td>
					<p><img src="/uploads/footer_eproc.jpg" alt="" width="100%" /></p>
					<p>&nbsp;</p>
					</td>
					</tr>
					</tbody>
					</table>'; 
					
		//$data['html'] = $html;
		echo '<pre>';
		echo htmlspecialchars($html);
		echo '</pre>';
		
		//echo $html;
		
		$data['site'] 	= 'generate e-proceeding'; 
		$data['view'] 	= 'monitoringeproceeding/monitoringeproceeding_generate'; 
		//$this->load->view('main', $data);
    }
	
	// public function getNIM()
	// {	
		// $db = $this->CreamerModel->getUsenameByWorkflow();
		
		// foreach ($db as $row){ 
			// $dta = explode('_',$row->loc);
			
			// $resume   	 = '../../../data/batik/symfony_projects/book/'.$dta[0];
			
			// $files1 	= scandir($resume);
			// foreach($files1) {
				// //rename("/tmp/tmp_file.txt", "/home/user/login/docs/my_file.txt");
			// }
			// // $jurnal    = '../../../data/batik/symfony_projects/book/'.$row->code.'/'.$row->code.'_jurnal.pdf';
			// // //if (file_exists($resume)) echo $row->code.'_resume.pdf <br>';
			// // if (file_exists($jurnal)) echo $row->code."_jurnal.pdf <br>"; 
			// // else echo "<br>";
		// }
		 
		
	// }
	public function contents()
	{
		ini_set('MAX_EXECUTION_TIME', -1);
		ini_set('memory_limit','-1');
		ini_set('default_socket_timeout', 900); 
		//$homepage = file_get_contents('http://10.14.203.4:9333/ozeki?action=sendMessage&ozmsUserInfo=adminOpenLib&recepient=085294741000&messageData=kerenbanget[NO-REPLY]');
		//$homepage = file_get_contents('http://10.13.14.171/sendsms.php?appname=TESTING&number=08112137203&text=Isi%20Pesan');
		//$contents = curl($homepage);
		
		// if (!$data = $this->http_get_contents("https://openlibrary.telkomuniversity.ac.id")) {
			  // $error = error_get_last();
			  // echo "HTTP request failed. Error was: " . $error['message'];
			   // echo $this->http_get_contents("https://www.facebook.com/yudhiaddict");
		// } else {

			  // echo $this->http_get_contents("https://www.facebook.com/yudhiaddict");
		// }
		///echo $homepage;
		echo $this->SendSmsTelkomuniv('08112137203','');
	}
	
	public function content()
	{
		// http://10.14.203.4:9333/ozeki?action=sendMessage&ozmsUserInfo=admin%3Aabc123&recepient=".$destinationNumber."&messageData=".$sms_message."[NO-REPLY]
// OpenLib";
		$a =  file_get_contents('http://10.14.203.4:9333/ozeki?action=sendMessage&ozmsUserInfo=admin%3Aabc123&recepient=08112137203&messageData=Halo Salam Super[NO-REPLY]OpenLib');
		print_r($a);	
	}
	
	function SendSmsTelkomuniv($destinationNumber, $sms_message) {
            $content = false;
            $destinationNumber = str_replace("(", "", $destinationNumber);
            $destinationNumber = str_replace(")", "", $destinationNumber);
            $destinationNumber = str_replace("-", "", $destinationNumber);
            $destinationNumber = str_replace("/", "", $destinationNumber);
            $destinationNumber = str_replace(" ", "", $destinationNumber);
            $destinationNumber = str_replace("+", "", $destinationNumber);
            $prefix = '0';
            if (substr($destinationNumber, 0, strlen($prefix)) == $prefix) {
                $destinationNumber = "62" . substr($destinationNumber, strlen($prefix));
            }
            //echo $destinationNumber; die();
            if (substr($destinationNumber, 0, 2) == '62') {
                $sms_message = urlencode($sms_message);
                $sms_message = substr($sms_message, 0, 160);

                /* Telkomsel Official */
                $user = "telkomuniv";
                $password = "Telkom12345";
                $url = "http://10.13.14.171/sendsms.php?appname=TESTING&number=08112137203&text=isipesan&=showstatus=true";
				
				
				$content = file_get_contents($url);
				
               
            }
            return $content;
    }
	
	function http_get_contents($url)
	{
	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_TIMEOUT, 1);
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	  if(FALSE === ($retval = curl_exec($ch))) {
		error_log(curl_error($ch));
	  } else {
		return $retval;
	  }
	}
	
	public function content2()
	{
		$db = $this->CreamerModel->getMember();
		echo "<pre>";
		print_r($db);
		echo "</pre>";
	}
	 


	public function renames()
	{	
		$db = $this->CreamerModel->getMember();
		
		foreach ($db as $row){
			if($this->CreamerModel->getSSO($row->master_data_user)->num_rows()!=0){
				$dt = $this->CreamerModel->getSSO($row->master_data_user)->row();
				if ($row->master_data_course!=$dt->c_kode_prodi){
					$this->CreamerModel->updateMember($dt->c_kode_prodi,$row->master_data_user);
					echo $row->master_data_user."<br>";
				}
			}
		}
		
	}
	
	// private function renames()
	// {	
		// $db = $this->CreamerModel->getUsenameByWorkflow();
		
		// $old   	 = '../../../data/batik/symfony_projects/book/1103138473';
		// $new   	 = '../../../data/batik/symfony_projects/book/nandawidy';
		// rename($old, $new);	
			// $files1 	= scandir($resume);
			// print_r($files1);
			
			
			// // foreach($files1) {
				// // rename("/tmp/tmp_file.txt", "/home/user/login/docs/my_file.txt");
			// // }
			// // $jurnal    = '../../../data/batik/symfony_projects/book/'.$row->code.'/'.$row->code.'_jurnal.pdf';
			// // //if (file_exists($resume)) echo $row->code.'_resume.pdf <br>';
			// // if (file_exists($jurnal)) echo $row->code."_jurnal.pdf <br>"; 
			// // else echo "<br>";
		 
		
	// }
	 
	
	private function renameFolderWorkflow()
	{	
		$db = $this->CreamerModel->getUsenameByWorkflow();
		
		foreach ($db as $row){ 
			$dta = explode('_',$row->loc);
			
			$resume   	 = '../../../data/batik/symfony_projects/book/'.$dta[0];
			
			
			//$files1 	= scandir($resume);
			//foreach($files1) {
				//rename("/tmp/tmp_file.txt", "/home/user/login/docs/my_file.txt");
			//}
			// $jurnal    = '../../../data/batik/symfony_projects/book/'.$row->code.'/'.$row->code.'_jurnal.pdf';
			// //if (file_exists($resume)) echo $row->code.'_resume.pdf <br>';
			// if (file_exists($jurnal)) echo $row->code."_jurnal.pdf <br>"; 
			// else echo "<br>";
		}
		 
		
	}
	
	public function checkjournalPDF()
	{	
		
		$db = $this->CreamerModel->getDocument();
		
		foreach ($db as $row){ 
			if ($row->lokasi!="") {
				if (file_exists('../../../data/batik/symfony_projects/book/'.$row->created_by.'/'.$row->lokasi)){
					$fp = fopen('../../../data/batik/symfony_projects/book/'.$row->created_by.'/'.$row->lokasi, 'r');
	 
					fseek($fp, 0);
					$data = fread($fp, 5);  
					if(strcmp($data,"%PDF-")==0)
					{
					  echo "ada";
					}
					else
					{
						echo "tidak ada";
					} 
					fclose($fp);
				}
				else echo "tidak ada";
				
			}
			else echo "tidak ada";
			echo "<br>";
			
 
		}
		 
		
	}
	public function checkKnowledgeItem()
	{	
	
		foreach ($db as $row){ 
			//echo $row->code." ";
			//$resume    = '../../../data/batik/symfony_projects/book/'.$row->code.'/'.$row->code.'_resume.pdf';
			$jurnal    = '../../../data/batik/symfony_projects/book/'.$row->code.'/'.$row->code.'_jurnal.pdf';
			//if (file_exists($resume)) echo $row->code.'_resume.pdf <br>';
			if (file_exists($jurnal)) echo $row->code."_jurnal.pdf <br>"; 
			else echo "<br>";
		}
		 
		
	}
	
	public function merge()
	{	
		
		$this->load->library('pdfmerger/PDFMerger');  
		$dir    = 'resumes/1103104160';
		$files1 = scandir($dir);
		$files2 = scandir($dir, 1);

		foreach ($files1 as $row){
			if ($row!='.' && $row!='..'){ 
				if (strpos($row,'bab') !== false) {
					$last = $row;
				}
			} 
		}

		$pdf = new PDFMerger;

		$pdf->addPDF('resumes/1103104160/1103104160_resume.pdf', 'all')
			->addPDF('resumes/1103104160/1103104160_bab1.pdf', 'all') 
			->addPDF('resumes/1103104160/'.$last, 'all')
			->addPDF('resumes/1103104160/1103104160_daftar_pustaka.pdf', 'all')
			->merge('file', 'resumes/1103104160/1103104160.pdf');
 
	}
	
	public function rrmdir($dir) {
	   if (is_dir($dir)) {
		 $objects = scandir($dir);
		 foreach ($objects as $object) {
		   if ($object != "." && $object != "..") {
			 if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object);
		   }
		 }
		 reset($objects);
			rmdir($dir);
	   } 
	 
	} 
	
	public function totalfile()
	{
		$dirs    = '../../../data/batik/symfony_projects/book';
		$files1 = scandir($dirs);
		$cover2 		= 0;
		$abstraksi2		= 0;
		$abstract2 		= 0;
		$persembahan2	= 0;
		$disclaimer2 	= 0;
		$resume2		= 0;
		$bab12 			= 0;
		$jurnal2		= 0; 
		
		foreach($files1 as $row){ 
			$cover    		= '../../../data/batik/symfony_projects/book/'.$row.'/'.$row.'_cover.pdf';
			$abstraksi    	= '../../../data/batik/symfony_projects/book/'.$row.'/'.$row.'_abstraksi.pdf';
			$abstract    	= '../../../data/batik/symfony_projects/book/'.$row.'/'.$row.'_abstract.pdf';
			$persembahan    = '../../../data/batik/symfony_projects/book/'.$row.'/'.$row.'_persembahan.pdf';
			$disclaimer    	= '../../../data/batik/symfony_projects/book/'.$row.'/'.$row.'_disclaimer.pdf';
			$resume   	 	= '../../../data/batik/symfony_projects/book/'.$row.'/'.$row.'_resume.pdf';
			$bab1    		= '../../../data/batik/symfony_projects/book/'.$row.'/'.$row.'_bab1.pdf'; 
			$jurnal    		= '../../../data/batik/symfony_projects/book/'.$row.'/'.$row.'_jurnal_eproc.pdf';
			if (file_exists($cover)) $cover2++; 
			if (file_exists($abstraksi)) $abstraksi2++;
			if (file_exists($abstract)) $abstract2++; 
			if (file_exists($persembahan)) $persembahan2++;
			if (file_exists($disclaimer)) $disclaimer2++; 
			if (file_exists($resume)) $resume2++;
			if (file_exists($bab1)) $bab12++; 
			if (file_exists($jurnal)) $jurnal2++;
		}
		echo "cover       $cover2 <br>";
		echo "abstraksi       $abstraksi2 <br>";
		echo "abstract       $abstract2 <br>";
		echo "persembahan       $persembahan2 <br>";
		echo "disclaimer       $disclaimer2 <br>";
		echo "resume       $resume2 <br>";
		echo "bab1       $bab12 <br>";
		echo "jurnal       $jurnal2 <br>";
	}
	
	public function aa()
	{
		$dir    = 'resume';
		$this->rrmdir($dir); 
	}
	
	public function index()
	{	   
		// $dir    = 'resumes';
		// $files1 = scandir($dir);
		// $i		= 0; 
		// foreach($files1 as $row){
			 
			// if ($row!='.' && $row!='..'){ 
				// $i++;
				
				// $dirs    = '../../../data/batik/symfony_projects/book/'.$row;
				// if (is_dir($dirs)){ 
					// $folder = scandir($dirs);
					// $first			= "";
					// $last			= ""; 
					// $dp1 			= $row."_dp.pdf";
					// $dp2 			= $row."_DAFTARPUSTAKA.pdf";
					// $dp3 			= $row."_ DAFTARPUSTAKA.pdf";
					// $dp4 			= $row."_DAFTAR PUSTAKA.pdf";
					// $dp5 			= $row."_DaftarPustaka.pdf";
					// $dp6			= $row."_daftar_pustaka.pdf";
					// $dp7			= $row."_daftarpustaka.pdf";
					// $dp8 			= $row."_ dp.pdf";
					// $cek = 0;
					
					 
					// foreach ($folder as $rows){
					 
						
						// if ($rows!='.' && $rows!='..'){  
							 
							// if ((stripos($rows,'bab') !== false)&&(stripos($rows,'.pdf') !== false)) {
								// $last=$rows;
								// if($cek==0) $first = $rows;
								// $cek++;
							// }  
							
							// if ($rows==$dp1) copy("../../../data/batik/symfony_projects/book/".$row."/".$dp1,"resume/".$i."_3.pdf");
							// else if ($rows==$dp2) copy("../../../data/batik/symfony_projects/book/".$row."/".$dp2,"resume/".$i."_3.pdf"); 
							// else if ($rows==$dp3) copy("../../../data/batik/symfony_projects/book/".$row."/".$dp3,"resume/".$i."_3.pdf"); 
							// else if ($rows==$dp4) copy("../../../data/batik/symfony_projects/book/".$row."/".$dp4,"resume/".$i."_3.pdf"); 
							// else if ($rows==$dp5) copy("../../../data/batik/symfony_projects/book/".$row."/".$dp5,"resume/".$i."_3.pdf"); 
							// else if ($rows==$dp6) copy("../../../data/batik/symfony_projects/book/".$row."/".$dp6,"resume/".$i."_3.pdf"); 
							// else if ($rows==$dp7) copy("../../../data/batik/symfony_projects/book/".$row."/".$dp7,"resume/".$i."_3.pdf"); 
							// else if ($rows==$dp8) copy("../../../data/batik/symfony_projects/book/".$row."/".$dp8,"resume/".$i."_3.pdf"); 
							
						// } 
					// } 
					// if ($first!="") copy("../../../data/batik/symfony_projects/book/".$row."/".$first,"resume/".$i."_1.pdf");
					// if ($last!="") copy("../../../data/batik/symfony_projects/book/".$row."/".$last,"resume/".$i."_2.pdf");
					
					
					 // echo $row."<br/>";
				// }
				
			// } 
			 
		// }
		 
	}
	
	public function watermark()
	{	   
		 
		$ta	= $this->CreamerModel->getSomeData(); 
		foreach ($ta as $row){
			if($row->tipe=='4') $tipe='S1';
			else if($row->tipe=='5') $tipe='S2'; 
			else $tipe='D3';
			  
			if (($row->softcopy_path!="")) {
				if(!is_dir("resumes_custom/".$row->softcopy_path)) mkdir("resumes_custom/".$row->softcopy_path);
				$list = $row->softcopy_path; 
			}
			else {
				if(!is_dir("resumes_custom/".$row->code)) mkdir("resumes_custom/".$row->code); 
				$list = $row->code; 
			}
			
			
			echo $list."<br>";
			
			$files 	= "resumes/".$list."/".$list."_resume.pdf";
			$new 	= "resumes_custom/".$list."/0s.pdf";
			$header = "<table style='font-size:10px;font-weight:bold;width:100%;' ><tr><td><img style='' src='".base_url()."/themes/images/tu.png' width='50px'></td><td align='right'>Tugas Akhir - ".$row->published_year."</td></table>";
			$footer = "<table style='font-size:10px;font-weight:bold;width:100%;' ><tr><td>".$row->nama_fakultas."</td><td align='right'>Program Studi ".$tipe." ".$row->nama_prodi."</td></table>";
			$this->addDocIdentification($files,$new,$header,$footer);
			
			$header = "<table style='font-size:10px;font-weight:bold;width:100%;' ><tr><td><img style='' src='".base_url()."/themes/images/tu.png' width='50px'></td><td align='right'>Tugas Akhir - ".$row->published_year."</td></table>";
			$footer = "<table style='font-size:10px;font-weight:bold;width:100%;' ><tr><td>".$row->nama_fakultas."</td><td align='right'>Program Studi ".$tipe." ".$row->nama_prodi."</td></table>";
			
			
			// $dir = "resumes/".$list;
			// $file = scandir($file);
			
			// foreach ($file as $r){
				// if (stripos($r,'resume')){
					// $new 	= "resumes_custom/".$list."/1s.pdf";
					// $this->addDocIdentification($files,$new,$header,$footer); 
				// }
			// }
			
			$files  = "resumes/".$list."/1.pdf";
			$new 	= "resumes_custom/".$list."/1s.pdf";
			if(file_exists($files)) $this->addDocIdentification($files,$new,$header,$footer); 
			else echo $files;
			
			$files  = "resumes/".$list."/2.pdf";
			$new 	= "resumes_custom/".$list."/2s.pdf";
			if(file_exists($files)) $this->addDocIdentification($files,$new,$header,$footer); 
			
			$files  = "resumes/".$list."/3.pdf";
			$new 	= "resumes_custom/".$list."/3s.pdf";
			if(file_exists($files)) $this->addDocIdentification($files,$new,$header,$footer);  
			 
		} 
		
	 
		// $this->load->view('template',$data);
	} 
	
	public function watermark_tes()
	{	   
		  
		$dirs    = '../../../data/batik/symfony_projects/book/';
		$dir = $this->scan_dir($dirs);
		 
		$key =  array_search('24.12.009',$dir);
		$key++;
		$dir = array_slice($dir, $key); 
		$max = $key+10;
		//print_r($dir); 
		foreach ($dir as $row){  
			$dir2    = '../../../data/batik/symfony_projects/book/'.$row;
			//echo $row."<br>";
			//echo  $row.' '.date ("d-m-Y h:i:s", filemtime($dir2)).'<br>';
			foreach(scandir($dir2) as $rows){
				if ($rows!='.' && $rows!='..' && ((strpos($rows, '.pdf') !== false)||(strpos($rows, '.PDF') !== false))){  
					$files = '../../../data/batik/symfony_projects/book/'.$row.'/'.$rows; 
					// $new 	= "downloads/bab1_wm.pdf";
					echo $files."<br>";
					//if ($files!='../../../data/batik/symfony_projects/book/24.12.002/24.12.002.pdf')
					$this->addDocIdentification($files,$files); 
				}
			} 
			echo "<br>";  
			if ($key==$max) break;    
			$key++;
		} 
		
		//$files 	= "downloads/C050081007_abstraksi.pdf";
		//$new 	= "downloads/C050081007_abstraksi_wm.pdf";
		//$this->addDocIdentification($files,$new);
	} 
	
	

	function scan_dir($dir) {
		$ignored = array('.', '..', '.svn', '.htaccess');

		$files = array();   
		foreach (scandir($dir) as $file) {
			if(in_array($file, $ignored)) continue;
			// else if(strpos($file, '.zip') !== false) continue;
			// else if(strpos($file, '.rar') !== false) continue;
			// else if(strpos($file, '.pdf') !== false) continue;
			if (!is_dir('../../../data/batik/symfony_projects/book/'.$file))continue;
			$files[$file] = filemtime($dir . '/' . $file); 
			//echo  filectime($dir . '/' . $file).'<br>';
		}

		//arsort($files);
		asort($files);
		$files = array_keys($files);
	
		return ($files) ? $files : false;
	}
	public function addDocIdentification($files,$new,$header="",$footer=""){
		
		$mpdf = new mPDF(); 
		$mpdf->showWatermarkImage = true; 
		$mpdf->watermarkImageAlpha = 0.15;
		$mpdf->SetImportUse();
		$pagecount = $mpdf->SetSourceFile($files);
			 
		for($i = 1; $i <= $pagecount; $i++){
			 $tplId = $mpdf->ImportPage($i);
			$size = $mpdf->getTemplateSize($tplId); 
			$orientation = 'P';
			$mpdf->_setPageSize(array($size['w'], $size['h']), $orientation);
			$mpdf->AddPage();  
			$mpdf->UseTemplate($tplId);
			$mpdf->WriteHTML("<watermarkimage src='http://www.icls2016.org/images/Telkom.png'  size='120,140'  />");
		} 
		$mpdf->Output($new,'F');
	}
 
    private function create_pdf($html,$file,$header="",$footer="",$paper='A4')
	{  
		$mpdf = new mPDF('utf-8', $paper, 0, '', 10, 10, 5, 1, 1, 1, '');
		$mpdf->ignore_invalid_utf8 = true;
		$mpdf->SetHTMLHeader('<div style="text-align: right; font-weight: bold;">My document</div>');

		$mpdf->WriteHTML($html);	
		$mpdf->SetHTMLFooter($footer);
		$mpdf->Output($file,'F');
	}
	
	public function doprint($pdf=false)
	{
		$ta	= $this->CreamerModel->getAllData();
		
		foreach ($ta as $row){
			$abstracts 		= "";
			$abstraks 		= "";
			$abstractIndex 	= "";
			$abstract  		= "";
			$keyword 		= "";
			$abstrakIndex	= "";
			$abstrak 		= "";
			$katakunci	 	= "";
			
			if ((strpos($row->abstrak,'ABSTRACT:') !== false) || (strpos($row->abstrak,'ABSTRAKSI:') !== false)) {
				 $abstracts 	= explode("ABSTRACT:",$row->abstrak);
				 $abstraks 		= explode("ABSTRAKSI:",$abstracts[0]);
				   
				 $abstractIndex = explode("Keyword:",$abstracts[1]);
				 $abstract 		= $abstractIndex[0];
				 $keyword		= $abstractIndex[1];
				 
				 $abstrakIndex = explode("Kata kunci:",$abstraks[1]);
				 $abstrak 		= $abstrakIndex[0];
				 $katakunci		= $abstrakIndex[1];
				  
				 if($katakunci=="") { 
					$abstrakIndex = explode("KATA KUNCI:",$abstraks[1]);
					$abstrak 		= $abstrakIndex[0];
					$katakunci		= $abstrakIndex[1];
				 }
				 
				 if($katakunci=="") {
					$abstrakIndex = explode("Kata Kunci :",$abstraks[1]);
					$abstrak 		= $abstrakIndex[0];
					$katakunci		= $abstrakIndex[1];
				 }
				 
				 
				 if($keyword=="") { 
					$abstractIndex = explode("KEYWORD:",$abstracts[1]);
					$abstract 		= $abstractIndex[0];
					$keyword		= $abstractIndex[1]; 
				 }   
				 
				 if($katakunci!="") { 
					$abstrak 		= str_replace('Kata Kunci :','',$abstrak);
					$katakunci		= str_replace('Kata Kunci :','',$katakunci);
				 } 
			}
			else {
				$abstrak = $row->abstrak;  
			}
			 
			
			$pembimbing1 = $this->CreamerModel->getPembimbing($row->pembimbing1);
			$pembimbing2 = $this->CreamerModel->getPembimbing($row->pembimbing2);
			  
			$html = '
			<html>
			<head>
			<style>
			@page {
				margin : 2.5cm 2.5cm 2.5cm 2.5cm; 
			} 
			</style>
			</head>
			<body> 
			<table width="100%" style="font-style:Times New Roman">
				<tr>
					<td style="font-weight:bold;font-size:11pt;text-transform: uppercase;" align=center>'.$row->title.'</td>
				<tr>
				<tr>
					<td style="font-weight:bold;font-size:11pt;text-transform: uppercase;" align=center>&nbsp;</td>
				</tr>
				<tr>
					<td style="font-weight:bold;font-size:9pt;text-transform: capitalize;" align=center>'.(($row->author!="")? $row->author.'&#185;':'').(($row->pembimbing1!="")? ', '.$row->pembimbing1.'&#178;':'').(($row->pembimbing2!="")? ', '.$row->pembimbing2.'&#179;':'').'
					</td>
				</tr>
				<tr>
					<td style="font-weight:bold;font-size:9pt;text-transform: uppercase;" align=center>&nbsp;</td>
				</tr>
				<tr>
					<td style="font-size:9pt;text-transform: capitalize;" align=center>'.(($row->nama_prodi!="")? '&#185;'.$row->nama_prodi:'').(($row->nama_fakultas!="")? ', '.$row->nama_fakultas:'').((($row->nama_prodi!="")||($row->nama_fakultas!=""))?', Universitas Telkom' : 'Universitas Telkom' ).(($pembimbing1->prodi!="")? '<br>&#178;'.$pembimbing1->prodi.', '.$pembimbing1->fakultas.', Universitas Telkom':'').(($pembimbing2->prodi!="")? '<br>&#179;'.$pembimbing2->prodi.', '.$pembimbing2->fakultas.', Universitas Telkom':'').'
					</td>
				</tr> 
				<tr>
					<td style="font-size:9pt;" align=center>'.(($row->email!="")? '&#185;<span style="font-weight:bold;text-decoration:underline;">'.$row->email.'</span>':'').(($pembimbing1->master_data_email!="")? ', &#178;<span style="font-weight:bold;text-decoration:underline;">'.$pembimbing1->master_data_email.'</span>':'').(($pembimbing2->master_data_email!="")? ', &#179;<span style="font-weight:bold;text-decoration:underline;">'.$pembimbing2->master_data_email.'</span>':'').'
					</td>
				</tr>  
				<tr>
					<td style="font-weight:bold;font-size:9pt;">'.(($row->abstrak!="")? '<hr>Abstrak <br/><p>'.$abstrak."</p><br/>":'').(($katakunci!="")? 'Kata Kunci : '.$katakunci."</p><br/><hr>":'<hr>').'
					</td>
				</tr> 
				<tr>
					<td style="font-weight:bold;font-size:9pt;">'.(($abstracts[1]!="")? 'Abstract <br/><p>'.$abstract."</p><br/>":'').(($keyword!="")? 'Keywords : '.$keyword."</p><br/><hr>":'').'
					</td>
				</tr> 
				
			 
			</table></body></html>';
			  
			if (($row->softcopy_path!="")) {
				if ($pembimbing1->master_data_email!="") echo "Email ".$row->softcopy_path."<br>";
				if(!is_dir("resumes/".$row->softcopy_path)) mkdir("resumes/".$row->softcopy_path);
				$file = 'resumes/'.$row->softcopy_path.'/'.$row->softcopy_path."_resume.pdf";
				echo $row->softcopy_path."<br>";
			}
			else {
				if ($pembimbing1->master_data_email!="") echo "Email ".$row->code."<br>";
				if(!is_dir("resumes/".$row->code)) mkdir("resumes/".$row->code); 
				$file = 'resumes/'.$row->code.'/'.$row->code."_resume.pdf";
				echo $row->code."<br>";
			}
			 
			//$header = "<table style='font-size:10px;font-weight:bold;width:100%;' ><tr><td><img style='' src='".base_url()."/themes/images/tu.png' width='50px'></td><td align='right'>Tugas Akhir - ".$row->published_year."</td></table>";
			// $footer = "<table style='font-size:10px;font-weight:bold;width:100%;' ><tr><td>".$row->nama_fakultas."</td><td align='right'>".$row->nama_prodi."</td></table>";
			$this->create_pdf($html,$file,$header,$footer);
			//echo $header."<br>";
			//echo $footer."<br>";
		} 
		//$data['view'] = 'page_prints';
		//$this->load->view('template',$data);
	} 
	
	function removeWorkflowDocFile() {  
		$listFile=array();
		$hitFile =array();
		$upload	 = $this->CreamerModel->getListWorkflowDoc(); 
		foreach($upload as $up){ 
			$dirs    = '../../../data/batik/symfony_projects/book/'.$up->master_data_user;
			if (is_dir($dirs)) echo $dirs;
		}
		
		// $dir    = '../../../data/batik/symfony_projects/book';
		// $files1 = scandir($dir);

		// foreach ($files1 as $row){
			// if ($row!='.' && $row!='..'){ 
				// //$dirs    = '../../../data/batik/symfony_projects/book/'.$row;
				// //$files2 = scandir($dirs);
				// // echo "<pre>";
				// // print_r($files2);
				// // echo "</pre>";
				// //foreach ($files2 as $row2){
				// //	if ($row2!='.' && $row2!='..'){ 
						// for ($i=0;$i<count($listFile);$i++){
							// $file    = '../../../data/batik/symfony_projects/book/'.$row.'/'.$row.'_'.$listFile[$i];
							// if (file_exists($file)) $hitFile[$i]++;
						// }
					// //}
				// //}
			// }
		// }
		
		// for ($i=0;$i<count($listFile);$i++){
			// echo $listFile[$i]."<br>";
		// }
		
		// for ($i=0;$i<count($listFile);$i++){
			// echo $hitFile[$i]."<br>";
		// }
	}
}

?>