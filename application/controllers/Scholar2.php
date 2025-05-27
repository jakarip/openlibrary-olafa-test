<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require FCPATH.'/vendor/autoload.php'; 
require FCPATH.'/vendor/simple_html_dom.php'; 
class Scholar2 extends CI_Controller {
	
	function __construct() {
        parent::__construct();  
		$this->load->model('HomeModel');  
		$this->load->model('ApiModel'); 
		$this->load->model('MonitoringEproceedingModel'); 
		if (!$this->session->userdata('language')) $this->session->set_userdata(array('language' => 'ina')); 
		
	
    }

	
	public function index2($type="",$page="",$year="")
	{	  
		include('simple_html_dom.php');
		$scholar = array();
		$count = 0;

		$min = $page*10;
		$max = $min+15;
		
		// if($year!="") $year = '&as_ylo='.$year.'&as_yhi='.$year;
		for($i=$min;$i<=$min;$i++){  
			$url = 'https://scholar.google.com/scholar?start=10&q=site:openlibrarypublications.telkomuniversity.ac.id/index.php/engineering&hl=en&as_sdt=0,5';
			$html = file_get_html($url); 
			echo $html;
			// $ret = $html->find('div[class="gs_r gs_or gs_scl"]');  
			// foreach($html->find('div[class="gs_r gs_or gs_scl"]') as $element)  { 
				// $html2 	= file_get_html($element->find('.gs_rt a',0)->href); 
				// $vol 	= explode(" ",$html2->find('#breadcrumb',0)->plaintext);  
				
				// $cite = $element->find('.gs_fl a',3)->plaintext;
				// if(strpos($cite,'Cited by')=== false) $cites = 0;
				// else $cites = str_replace('Cited by ','',$cite); 
				
				// $scholar[$count]['url'] = $element->find('a',0)->href;
				// $scholar[$count]['title'] = $html2->find('#articleTitle',0)->plaintext;
				// $scholar[$count]['eproc'] = $html2->find('#headerTitle',0)->plaintext;
				// $scholar[$count]['author'] = $html2->find('#authorString',0)->plaintext; 
				// $scholar[$count]['volume'] = substr($vol[4],0,1);
				// $scholar[$count]['no'] = $vol[6];  
				// $scholar[$count]['year'] = substr($vol[7],1,4); 
				// $scholar[$count]['cited'] = $cites; 
				// $count++;
			// }
		}			
		echo "<pre>";
		print_r($scholar);
		echo "</pre>"; 
	}
	
	 
	
	
	
}
