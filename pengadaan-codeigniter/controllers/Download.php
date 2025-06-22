<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Download extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Common_Model', 'cm', TRUE);
		$this->load->model('Member_Model', 'dm', TRUE);
		$this->load->model('Referral_Model', '', TRUE);
		$this->load->model('Usermodel', 'um', TRUE);
		$this->load->model('ApiModel', '', TRUE);  
		$this->load->library("Encrypt");
		$this->load->helper('form');
		
		
	}
	 
	
	public function files($var="")
	{   
	 
        if(empty($var))
        {
			header("Location: /");
			die();
        }
         else
        { 
			
			$var = base64_decode($var);
			$var = json_decode($var, true);  
			
			
			$notsecurefile = $this->um->checkstatusuploadtypeisnotsecure($var['file_type'],$var['extension'])->row();
		 
			if(!array_key_exists('code', $var) and $notsecurefile){
				$datas['login'] = true;
				$datas['user']['id'] = '';
				$datas['user']['username'] 		= '';
				$datas['user']['fullname'] 		= 'Visitor';
				$datas['user']['membertype']	= '';  
				$datas['user']['classtype'] 	= '';
				$this->session->set_userdata($datas); 
				
				
				$kt = $this->um->getKnowledgeItem($var['item_id'])->row();
				
				$link = $var['path'].'/'.$kt->softcopy_path.'/'.$kt->code.'_'.$var['file_type'].'.'.$var['extension']; 
				
				$data['download'] = '1';
				$data['dwn']['knowledge_item_id'] = $var['item_id'];
				$data['dwn']['member_id'] = $datas['user']['id'];
				$data['dwn']['name'] = $kt->code.'_'.$var['file_type'].'.'.$var['extension']; 
				 
				$data['readonly'] = '1'; 
				$data['read']['knowledge_item_id'] = $var['item_id'];
				$data['read']['member_id'] = $datas['user']['id'];
				$data['read']['name'] = $kt->code.'_'.$var['file_type'].'.'.$var['extension'];  
				 
				$data['name'] = $kt->code.'_'.$var['file_type'].'.'.$var['extension'];  
				$data['link'] = $link; 
				
				$data = json_encode($data);  
				$data = base64_encode($data);
				
				return redirect('download/flippingbook/'.$data); 
			}
			else {
				
				$temp = $this->um->checkstatus($var['code'])->row();  
				if($temp){
					$datas['login'] = true;
					$datas['user']['id'] = $temp->id;
					$datas['user']['username'] 		= $temp->master_data_user;
					$datas['user']['fullname'] 		= $temp->master_data_fullname;
					$datas['user']['membertype']	= $temp->member_type_id;  
					$datas['user']['classtype'] 	= $temp->member_class_id;  
					$this->session->set_userdata($datas); 
				}
				else { 
					header("location: /");
					die();
				} 

				$kt = $this->um->getKnowledgeItem($var['item_id'])->row();
 
				//allow file 3 tahun kebelakang
				$buku_ta = array('bab2','bab3','bab4','bab5','bab6','bab7','bab8','bab9'); 
				$yearnow = date('Y')-3;   
			 
				if($temp->member_type_id==19 and in_array($kt->knowledge_type_id,array(4,5,6)) and $kt->published_year>$yearnow and in_array($var['file_type'],$buku_ta)){
					echo "<script> alert('jenis keanggotaan anda tidak diperbolehkan mengakses / men-download dokumen ini'); window.location.href='/';</script>";
					die();
				}  
				//=================================
				$iuser = $this->session->userdata('user');
				 
				$temp = $this->um->checkstatusdownloadreadonly($iuser['membertype'],$var['file_type'],$var['extension'])->row();
			 
				 
				$link = $var['path'].'/'.$kt->softcopy_path.'/'.$kt->code.'_'.$var['file_type'].'.'.$var['extension'];  
			  
				if($temp->downloads=='1'){
					$data['download'] = '1';
					$data['dwn']['knowledge_item_id'] = $var['item_id'];
					$data['dwn']['member_id'] = $datas['user']['id'];
					$data['dwn']['name'] = $kt->code.'_'.$var['file_type'].'.'.$var['extension']; 
					 
					$data['read']['knowledge_item_id'] = $var['item_id'];
					$data['read']['member_id'] = $datas['user']['id'];
					$data['read']['name'] = $kt->code.'_'.$var['file_type'].'.'.$var['extension'];  
					
				}
				else $data['download'] = '0';  
				
				if($temp->readonly=='1'){
					$data['readonly'] = '1';  
					
					$data['read']['knowledge_item_id'] = $var['item_id'];
					$data['read']['member_id'] = $datas['user']['id'];
					$data['read']['name'] = $kt->code.'_'.$var['file_type'].'.'.$var['extension'];  
				}
				else $data['readonly'] = '0'; 
				
				$data['name'] = $kt->code.'_'.$var['file_type'].'.'.$var['extension'];  
				$data['link'] = $link; 
				
				$data = json_encode($data);  
				$data = base64_encode($data);
				
				return redirect('download/flippingbook/'.$data); 
			}
        }
	} 
	
	
	
	public function flippingbook($var){
		
		if (!$this->session->userdata('login')) {
			header("Location: /");
			die();
		}
		else{ 
			$data['var']	= $var;  
			$var = base64_decode($var);
			$var = json_decode($var, true);  
		  
			$data['download'] =  $var['download'];
			$data['readonly'] =  $var['readonly'];
			$data['view'] 	= 'frontend/download/index';
			
			$data['title']	= 'Files';		
			$data['icon']	= 'icon-files';  
			if($var['download']==0 and $var['readonly']==0){
				echo "<script> alert('jenis keanggotaan anda tidak diperbolehkan men-download dokumen ini'); window.location.href='/';</script>";
				die();
			} 
			
			
	
			$this->load->view('frontend/tpl_flippingbook', $data);
		}
	}
	
	
	function flippingbook_url_download($var="")
	{    
		 
		if (!$this->session->userdata('login')) {
			header("location: /");
			die();
		} 
         else
        {   
			$var = base64_decode($var);
			$var = json_decode($var, true);  
			
			if($var['download']!='1'){
				header("location: /");
				die();
			}
			else {  
				if($_SERVER['HTTP_SEC_FETCH_MODE']=='navigate') { 
					$var['dwn']['created_at'] = date('Y-m-d H:i:s');  			
					$this->um->addtable('batik.knowledge_item_file_download',$var['dwn']);  
				} 
				else {
					$var['read']['created_at'] = date('Y-m-d H:i:s');  			
					$this->um->addtable('batik.knowledge_item_file_readonly',$var['read']);
				}
				 
				header('content-type: application/pdf');
				header("content-transfer-encoding: binary");
				header("content-disposition: inline; filename=".$var['name']);
				readfile($var['link']);
			}
        }
	}
	
	
	
	
	function flippingbook_url_download_bypass($var="")
	{      
			$var = base64_decode($var);
			$var = json_decode($var, true);  
	 
			if($var['download']==0 and $var['readonly']==0){
				// header("location: /"); 
				// die();
			}  
			else {   
				// if($_SERVER['HTTP_SEC_FETCH_MODE']=='navigate') { 
					// $var['dwn']['created_at'] = date('Y-m-d H:i:s');  			
					// $this->um->addtable('batik.knowledge_item_file_download',$var['dwn']);  
				// } 
				// else { 
					// $var['read']['created_at'] = date('Y-m-d H:i:s');  			
					// $this->um->addtable('batik.knowledge_item_file_readonly',$var['read']);
				// }
				
				// header("Content-Disposition: attachment; filename=" . urlencode($file));   
				// header("Content-Type: application/download");
				// header("Content-Description: File Transfer");            
				// header("Content-Length: " . filesize($file));
				$temp = substr($var['name'],-4); 

				print_r($var);
				if($temp=='docx')
					header('Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document'); 
				else 
				header('Content-type: application/pdf');
				// header("Content-Transfer-Encodingg: binary");
				header("Content-Disposition: attachment; filename=".$var['name']); 
				header("Content-Length: " . filesize($var['link']));  
				readfile($var['link']);
			}
        // }
	} 
	
	
	
	
	
	
	function flippingbook_url_download_bypass2($var="")
	{    
		 
		// if (!$this->session->userdata('login')) {
			// header("location: /");
			// die();
		// } 
         // else
        // {   
			$var = base64_decode($var);
			$var = json_decode($var, true);  
			
			if($var['download']!='1'){
				header("location: /");
				die();
			}
			else {  
				// if($_SERVER['HTTP_SEC_FETCH_MODE']=='navigate') { 
					// $var['dwn']['created_at'] = date('Y-m-d H:i:s');  			
					// $this->um->addtable('batik.knowledge_item_file_download',$var['dwn']);  
				// } 
				// else {
					// $var['read']['created_at'] = date('Y-m-d H:i:s');  			
					// $this->um->addtable('batik.knowledge_item_file_readonly',$var['read']);
				// }
				
				header("Content-Disposition: attachment; filename=" . urlencode($var['name']));   
				header("Content-Type: application/download");
				header("Content-Description: File Transfer");      
				header("Content-Length: " . filesize($var['link']));
				       
 
			}
        // }
	} 
	 
	 
	function flippingbook_url($var="")
	{    
		 
		if (!$this->session->userdata('login') || $_SERVER['HTTP_SEC_FETCH_MODE']=='navigate' || $_SERVER['HTTP_SEC_FETCH_SITE']!='same-origin') {
			 
			header("location: /");
			die();
		} 
         else
        {   
			$var = base64_decode($var);
			$var = json_decode($var, true);

			$var['read']['created_at'] = date('y-m-d h:i:s');  			
			$this->um->addtable('batik.knowledge_item_file_readonly',$var['read']);
			
			header('content-type: application/pdf');
			header("content-transfer-encoding: binary");
			header("content-disposition: inline; filename=".$var['name']);
			readfile($var['link']);
        }
	}
	
	// function files2($var){
	 
		// $file_url = "/data/batik/symfony_projects/book/21.13.056/21.13.056_electronic_book_1.pdf";
		// header('Content-Type: application/pdf');
		// header("Content-Transfer-Encoding: Binary");
		// header("Content-disposition: inline; filename=21.13.056_electronic_book_1.pdf");
		// readfile($file_url);
	// }
 
	 
	
	function logout()
	{
		$data = array('login' => NULL, 'user' => NULL);
		$this->session->set_flashdata($data);
        $this->session->sess_destroy();
        
        $this->session->unset_userdata('user_login'); 

		header("Location: /");
		die();
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/

	 
}

?>