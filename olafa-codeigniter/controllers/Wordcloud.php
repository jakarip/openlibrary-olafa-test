<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require FCPATH.'/vendor/autoload.php'; 
class Wordcloud extends CI_Controller {
	
	function __construct() {
        parent::__construct();  
		$this->load->model('Dashboard_Model');  
		$this->load->model('ApiModel'); 
		$this->load->model('MonitoringEproceedingModel'); 
		$this->load->model('WordCloudModel'); 
		if (!$this->session->userdata('language')) $this->session->set_userdata(array('language' => 'ina')); 
		
	
    }

	 
	public function index()
	{	 
    
		  
    if($this->WordCloudModel->check_date()->total==0){
		  $word = $this->count_wc(); 
      foreach($word as $key=>$words){
        $datas['wordcloud'][] = ['wc_word'=>$key,'wc_weight'=>$words,'wc_date'=>date('Y-m-d H:00:00')];
      }  
      $this->WordCloudModel->emptyWordCloudTable();
      $this->WordCloudModel->insert_word($datas);  
    } 
		$data['word'] = $this->WordCloudModel->getall()->result();
   
		$this->load->view('wordcloud',$data);
	}

	 
	private function count_wc()
	{	  
    $rent = $this->WordCloudModel->get_rent()->result();
    $access = $this->WordCloudModel->get_access()->result();
    // echo "<pre>";
    // print_r($book);
    // echo "</pre>"; 
    $wordCounts = [];
    foreach($rent as $row){
      $title = $this->stopword(strtolower($row->title));
      // echo $title."<br>";
      // $title = $this->stemmer($title);
      // echo $title."<br>";
      $words = $this->preprocessTitle($title);
      
      // print_r($words);
      // echo $title."<br>";
      foreach ($words as $word) {
        if (!isset($wordCounts[$word])) {
            $wordCounts[$word] = 1;
        } else {
            $wordCounts[$word]++;
        }
      } 
    }
    foreach($access as $row){
      $title = $this->stopword(strtolower($row->title));
      // echo $title."<br>";
      // $title = $this->stemmer($title);
      // echo $title."<br>";
      $words = $this->preprocessTitle($title);
      
      // print_r($words);
      // echo $title."<br>";
      foreach ($words as $word) {
        if (!isset($wordCounts[$word])) {
            $wordCounts[$word] = 1;
        } else {
            $wordCounts[$word]++;
        }
      } 
    }
    // echo "<pre>";
    // print_r($wordCounts);
    // echo "</pre>"; 
    
    // Sort the word counts in descending order
    arsort($wordCounts);

    // Get the top 30 words
    return array_slice($wordCounts, 0, 150, true); 
	}

  private function preprocessTitle($title)
  {
 
      // Tokenize: split into words
      $words = preg_split('/\s+/', $title);
 

      return $words;
  }

  private function stemmer($sentence)

  {

    // echo $sentence."<br>";
    $stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();

    $stemmer = $stemmerFactory->createStemmer();

    $output = $stemmer->stem($sentence);

    
    // echo $output."<br>";
    return $output;
  

  }

  private function stopword($sentence){

    //stopword
    
    // $sentence = 'Perekonomian Indonesia sedang dalam pertumbuhan yang membanggakan';
    
    $stopWordRemoverFactory = new \Sastrawi\StopWordRemover\StopWordRemoverFactory();
    
    $stopword = $stopWordRemoverFactory->createStopWordRemover();
    
    $outputstopword = $stopword->remove($sentence);
    
    return $outputstopword;
    // echo $outputstopword;
    
    //Perekonomian Indonesia sedang pertumbuhan membanggakan
    
    }

	

	 
	public function report()
	{	  
		 
		
		// //end google analytics
		$pengunjung['year']			= $this->Dashboard_Model->get_year_visitor()->row();
		$pengunjung['month']		= $this->Dashboard_Model->get_month_visitor()->row();
		$pengunjung['day'] 			= $this->Dashboard_Model->get_day_visitor()->row(); 
		$pengunjung['checkout'] 	= $this->Dashboard_Model->get_day_visitor_checkout()->row(); 
  
		
		echo '<div class="col-md-3 col-sm-12">
		<table class="table info_dashboard" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<td class="desc_dashboard" align="center" style="font-size:50px;height:100px;">'.date("Y").'</td> 
				</tr> 
				<tr>
					<td align="center" style="font-size:50px;height:100px;">'.number_format($pengunjung['year']->total,0,'','.').'</td> 
				</tr>   
			</thead>
		</table>
	</div> 
	<div class="col-md-3 col-sm-12">
		<table class="table info_dashboard" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<td class="desc_dashboard" align="center" style="font-size:50px;height:100px;">'.date("F Y").'</td> 
				</tr> 
				<tr>
					<td align="center" style="font-size:50px;height:100px;">'.number_format($pengunjung['month']->total,0,'','.').'</td> 
				</tr>   
			</thead>
		</table>
	</div> 
	 
	<div class="col-md-3 col-sm-12">
		<table class="table info_dashboard" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<td class="desc_dashboard" align="center" style="font-size:50px;height:100px;">Check In Hari Ini</td> 
				</tr> 
				<tr>
					<td align="center" style="font-size:50px;height:100px;">'.number_format($pengunjung['day']->total,0,'','.').'</td> 
				</tr>   
			</thead>
		</table>
	</div>  
	 
	<div class="col-md-3 col-sm-12">
		<table class="table info_dashboard" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<td class="desc_dashboard" align="center" style="font-size:50px;height:100px;">Check Out Hari Ini</td> 
				</tr> 
				<tr>
					<td align="center" style="font-size:50px;height:100px;">'.number_format($pengunjung['checkout']->total,0,'','.').'</td> 
				</tr>   
			</thead>
		</table>
	</div>  
	
	';
	}
	
	
	
}
