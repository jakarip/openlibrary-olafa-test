<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct() {
        parent::__construct(); 
		$this->load->model('HomeModel');  
		$this->load->model('ApiModel');  
		$this->load->model('MonitoringEproceedingModel');  
		if (!$this->session->userdata('language')) $this->session->set_userdata(array('language' => 'ina'));
		// if(!$this->session->userdata('login')) redirect('');	
    }
	public function index()
	{   
		$data['menu'] = "home"; 
		$this->scheduler(); 
		$date = date('Y-m-d', strtotime('-3 month'));
		$dt = $this->HomeModel->getdoc_noloa($date)->result();
		if($dt){
			foreach($dt as $row){  
				$this->MonitoringEproceedingModel->updateDocument($row->id,'53',$row->latest_state_id);
			}
		}
		$this->load->view('theme',$data); 
	}
	
	
		
	
function SSO() { 
	$post = [
		"username" => "ahmadadhityanurhadi",
		"password" => "Nurhadi1+"
	]; 
	
		// "username" => "gilang@openlib",
		// "password" => "gilang"
	$config['useragent'] = $_SERVER['HTTP_USER_AGENT'];  
	echo urlencode('Nurhadi1%2B')." ".urldecode('Nurhadi1%252B');
	$params = '';
    foreach($post as $key=>$value)
		$params .= $key.'='.$value.'&';
         
	$params = trim($params, '&');  

	$ch = curl_init('https://gateway.telkomuniversity.ac.id/issueauth');
	// $ch = curl_init('http://cuti.ypt.or.id/cldap.php');
	curl_setopt($ch, CURLOPT_USERAGENT, $config['useragent']);
	curl_setopt($ch, CURLOPT_POST, 3);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, count($post)); //number of parameters sent
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params); 
	$response = curl_exec($ch);

	curl_close($ch);
	$dt = json_decode($response,true); 
		// print_r($dt['token']); 
	if(array_key_exists('token',$dt)){
			$token = $dt['token'];
			//setup the request, you can also use CURLOPT_URL
			$ch = curl_init('https://gateway.telkomuniversity.ac.id/5ff0b2a422bc020c4f2f46f24d0375f0');

			curl_setopt($ch, CURLOPT_USERAGENT, $config['useragent']);
			// Returns the data/output as a string instead of raw data
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			//Set your auth headers
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			   'Content-Type: application/json',
			   'Authorization: Bearer ' . $token
			   ));

			// get stringified data/output. See CURLOPT_RETURNTRANSFER
			$data = curl_exec($ch);

			// get info about the request
			$info = curl_getinfo($ch);
			// close curl resource to free up system resources
			curl_close($ch);
				
				$result = json_decode($data,true);
				if(is_array($result)){
					return $data;
				}	 
				else echo 'false';   
	}
	else echo 'false';  
} 

	
	function scheduler() { 
		ini_set('memory_limit', '-1'); 
	
		$time = $this->ApiModel->schedulerLog()->num_rows();
		if ($time<1){
			$data = array ('sch_log_date' => date('Y-m-d'));
			$this->ApiModel->InsertSchedulerLog($data);
			//update file total
			$listFile=array();
			$hitFile =array();
			$upload	 = $this->ApiModel->getUploadType(); 
			foreach($upload as $up){
				if($up->extension=='pdf'){
					$listFile[] = $up->name.'.'.$up->extension;
					$ids[] = $up->id;
					$secure[] = $up->is_secure;
					$hitFile[]  = 0;
				}
			}
			$dir = $_SERVER["DOCUMENT_ROOT"]."/../symfony_projects/book";
			$files1 = scandir($dir);

			foreach ($files1 as $row){
				if ($row!='.' && $row!='..'){ 
					for ($i=0;$i<count($listFile);$i++){
						$file    = $_SERVER["DOCUMENT_ROOT"].'/../symfony_projects/book/'.$row.'/'.$row.'_'.$listFile[$i];
						if (file_exists($file)) $hitFile[$i]++;
					} 
				}
			} 
			
			$this->ApiModel->DeleteFileTotal(); 
			for ($i=0;$i<count($listFile);$i++){
				$data = array ('nama_file' => $listFile[$i], 'total_file' => $hitFile[$i], 'file_id' => $ids[$i], 'keterangan' => $secure[$i]);
				$this->ApiModel->InsertFileTotal($data);
			} 
			//===============================================================================";
		} 
    }
	
	public function ceklogin()
	{
		$user = mysql_real_escape_string($_POST['username']);	
		$pass = md5(mysql_real_escape_string($_POST['password']));	
		
		$a = $this->LoginModel->login($user,$pass)->num_rows();
		if($a!=0) {
			$b = $this->LoginModel->checkUser($user)->num_rows();
			if($b!=0) {
				$data = array ("username" => $user,"login"=>TRUE); 
				$this->session->set_userdata($data);
				echo "success";
			}
			else echo "failed";
		} 
		else echo "failed";
	}
	
	function logout()
	{
		$this->session->sess_destroy();
		redirect("home");	
	}
}


?>