<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'libraries/simple_html_dom.php');
class Ms_Regional extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Ms_Regional_Model', 'dm', TRUE);
		$this->load->model('Common_Model', 'cm', TRUE);
		
		if (!$this->session->userdata('login')) redirect(y_url_admin().'/login');	
	}
	
	public function index()
	{
		$data['view'] 	= 'backend/ms_regional/index';
		$data['title']	= 'Master Data Wilayah';		
		$data['icon']	= 'icon-location4';
		
		$this->load->helper('form');
		
		$kabs = $this->dm->getkab()->result();
		$data['prov'] = array('' => 'Pilih Provinsi', 'new' => '+ Tambah Baru');
		$kabupaten = array();
		foreach($kabs as $kab)
		{
			$data['prov'][$kab->reg_prov_code.'||'.$kab->reg_prov] = $kab->reg_prov;
			$kabupaten[$kab->reg_prov_code.'||'.$kab->reg_prov][$kab->reg_kab_code.'||'.$kab->reg_kab] = $kab->reg_kab;
		}
		
		$data['kab'] = json_encode($kabupaten);
		
		$this->load->view('backend/tpl', $data);
	}
	
	public function json()
	{
		if(!$this->input->is_ajax_request()) return false;
		
		$columns = array(
			array( 'db' => 'reg_prov', 'dt' => 0 ),
			array( 'db' => 'reg_kab', 'dt' => 1 ),
			array( 'db' => 'reg_kec', 'dt' => 2 )
		);
		
		$this->datatables->set_cols($columns);
		$param	= $this->datatables->query();		
		$result = $this->dm->dtquery($param)->result();
		$filter = $this->dm->dtfiltered();
		$total	= $this->dm->dtcount();
		$output = $this->datatables->output($total, $filter);
		
		foreach($result as $row)
		{
			$rows = array (
				$row->reg_prov_code.' - '.$row->reg_prov,
				$row->reg_kab_code.' - '.$row->reg_kab,
				$row->reg_kec_code.' - '.$row->reg_kec,
				'<a href="javascript:edit('.$row->reg_id.')" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a>
				<a href="javascript:del('.$row->reg_id.', \''.$row->reg_kec.'\')" title="Delete Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-trash"></i></a>'
			);
			
			$output['data'][] = $rows;
		}
		
		echo json_encode( $output );
	}
	
	public function insert()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		
		if( $this->dm->add($item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function edit()
	{
		if(!$this->input->post('id')) return false;
		
		echo json_encode($this->dm->getbyid($this->input->post('id'))->row());
	}
	
	public function update()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('inp')) return false;
		
		$item = $this->input->post('inp');
		$id   = $this->input->post('id');
		
		if( $this->dm->edit($id, $item) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menyimpan Data'));
	}
	
	public function delete()
	{
		if(!$this->input->is_ajax_request()) return false;
		if(!$this->input->post('id')) return false;
		
		$id = $this->input->post('id');
		
		if( $this->dm->delete($id) )
			echo json_encode(array('status' => 'ok;', 'text' => ''));
		else
			echo json_encode(array('status' => 'error;', 'text' => 'Gagal Menghapus Data'));
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
	
	public function x()
	{
		$text = file_get_contents('http://dapo.dikdasmen.kemdikbud.go.id/sp'); echo $text;
		
		$dom = new simple_html_dom();
		$html = $dom->load($text);
		
		$tds = $html->find('.table');
		
		if(!empty($tds))
		{
			foreach($tds as $td)
			{
				echo $td->innertext;
			}
		}
	}
	
	public function kab()
	{
		$prov = array('020000','050000','030000','070000','190000','240000','280000','120000','110000','130000','080000','090000','060000','230000','010000','180000','150000','140000','250000','200000','100000','170000','220000','160000','210000','040000','270000','260000','330000','310000','320000','300000','290000','340000','350000');
		
		foreach($prov as $p)
		{
			$json = file_get_contents('http://dapo.dikdasmen.kemdikbud.go.id/rekap/dataSekolah?id_level_wilayah=1&kode_wilayah='.$p);
			$kabs = json_decode($json, true);
			
			$idprov = (int) substr($p, 0, 2);
			
			foreach($kabs as $kab)
			{
				echo "INSERT ms_kab VALUES ('', '".$idprov."', '".$kab['kode_wilayah']."', '".trim(str_replace('Prov. ', '', $kab['nama']))."');<br>";
				//echo "'".trim($prov['kode_wilayah'])."',";
			}	
		}
	}
	
	public function kec()
	{
		$kab = $this->db->query("SELECT * FROM ms_kab LEFT JOIN ms_prov ON kab_id_prov = prov_id WHERE prov_id BETWEEN 31 AND 35 ORDER BY prov_id ASC")->result();
		
		foreach($kab as $k)
		{
			$json = file_get_contents('http://dapo.dikdasmen.kemdikbud.go.id/rekap/dataSekolah?id_level_wilayah=2&kode_wilayah='.$k->kab_code);
			$data = json_decode($json, true);
			
			$insert = array();
			
			foreach($data as $row)
			{
				$insert[] = array('kec_prov_id' 	=> $k->prov_id,
								  'kec_prov_code' 	=> $k->prov_code,
								  'kec_prov' 		=> $k->prov_name,
								  'kec_kab_id' 		=> $k->kab_id,
								  'kec_kab_code'	=> $k->kab_code,
								  'kec_kab' 		=> $k->kab_name,
								  'kec_code' 		=> trim($row['kode_wilayah']),
								  'kec_name' 		=> trim(str_replace('Kec. ', '', $row['nama'])) );
			}
			
			$this->db->insert_batch('ms_kec', $insert);
		}
	}
	
	public function sma()
	{
		for($i=1; $i<=7000; $i+=500)
		{
			$kab = $this->db->query("SELECT * FROM ms_kec WHERE kec_id BETWEEN ".$i." AND ".($i+499)." ORDER BY kec_id ASC")->result();
		
			foreach($kab as $k)
			{
				$json = file_get_contents('http://dapo.dikdasmen.kemdikbud.go.id/rekap/progresSP?id_level_wilayah=3&kode_wilayah='.$k->kec_code);
				$data = json_decode($json, true);
				
				foreach($data as $row)
				{
					$bentuk = trim($row['bentuk_pendidikan']);
					
					if($bentuk == 'SMA' or $bentuk == 'SMK')
					$insert[] = array('school_id_kec' 	=> $k->kec_id,
									  'school_name' 	=> trim($row['nama']),
									  'school_status' 	=> trim($row['status_sekolah']),
									  'school_npsn' 	=> trim($row['npsn']) );
				}
				
				$this->db->insert_batch('ms_school', $insert);
				
				echo $k->kec_id.'<br>';
			}
			
			sleep(10);
		}
	}
	
	public function prov()
	{
		$json = '[{"nama":"Prov. Jawa Barat","kode_wilayah":"020000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":503,"sma_s":1115,"sma":1618,"smk_n":285,"smk_s":2625,"smk":2910,"slb_n":40,"slb_s":340,"slb":380,"sd_n":17743,"sd_s":1877,"sd":19620,"smp_n":1994,"smp_s":3254,"smp":5248,"sekolah_n":20565,"sekolah_s":9211,"sekolah":29776},{"nama":"Prov. Jawa Timur","kode_wilayah":"050000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":423,"sma_s":1122,"sma":1545,"smk_n":295,"smk_s":1746,"smk":2041,"slb_n":70,"slb_s":403,"slb":473,"sd_n":17604,"sd_s":1758,"sd":19362,"smp_n":1725,"smp_s":2943,"smp":4668,"sekolah_n":20117,"sekolah_s":7972,"sekolah":28089},{"nama":"Prov. Jawa Tengah","kode_wilayah":"030000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":363,"sma_s":505,"sma":868,"smk_n":237,"smk_s":1349,"smk":1586,"slb_n":39,"slb_s":146,"slb":185,"sd_n":17918,"sd_s":1130,"sd":19048,"smp_n":1771,"smp_s":1545,"smp":3316,"sekolah_n":20328,"sekolah_s":4675,"sekolah":25003},{"nama":"Prov. Sumatera Utara","kode_wilayah":"070000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":424,"sma_s":649,"sma":1073,"smk_n":266,"smk_s":724,"smk":990,"slb_n":27,"slb_s":25,"slb":52,"sd_n":8294,"sd_s":1377,"sd":9671,"smp_n":1310,"smp_s":1246,"smp":2556,"sekolah_n":10321,"sekolah_s":4021,"sekolah":14342},{"nama":"Prov. Sulawesi Selatan","kode_wilayah":"190000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":336,"sma_s":252,"sma":588,"smk_n":167,"smk_s":277,"smk":444,"slb_n":24,"slb_s":63,"slb":87,"sd_n":6157,"sd_s":284,"sd":6441,"smp_n":1249,"smp_s":408,"smp":1657,"sekolah_n":7933,"sekolah_s":1284,"sekolah":9217},{"nama":"Prov. Nusa Tenggara Timur","kode_wilayah":"240000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":335,"sma_s":196,"sma":531,"smk_n":148,"smk_s":138,"smk":286,"slb_n":31,"slb_s":13,"slb":44,"sd_n":3290,"sd_s":1790,"sd":5080,"smp_n":1261,"smp_s":403,"smp":1664,"sekolah_n":5065,"sekolah_s":2540,"sekolah":7605},{"nama":"Prov. Banten","kode_wilayah":"280000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":148,"sma_s":398,"sma":546,"smk_n":75,"smk_s":622,"smk":697,"slb_n":7,"slb_s":87,"slb":94,"sd_n":3956,"sd_s":637,"sd":4593,"smp_n":549,"smp_s":898,"smp":1447,"sekolah_n":4735,"sekolah_s":2642,"sekolah":7377},{"nama":"Prov. Lampung","kode_wilayah":"120000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":233,"sma_s":260,"sma":493,"smk_n":107,"smk_s":378,"smk":485,"slb_n":11,"slb_s":14,"slb":25,"sd_n":4369,"sd_s":316,"sd":4685,"smp_n":690,"smp_s":666,"smp":1356,"sekolah_n":5410,"sekolah_s":1634,"sekolah":7044},{"nama":"Prov. Sumatera Selatan","kode_wilayah":"110000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":325,"sma_s":272,"sma":597,"smk_n":113,"smk_s":189,"smk":302,"slb_n":13,"slb_s":19,"slb":32,"sd_n":4301,"sd_s":367,"sd":4668,"smp_n":877,"smp_s":455,"smp":1332,"sekolah_n":5629,"sekolah_s":1302,"sekolah":6931},{"nama":"Prov. Kalimantan Barat","kode_wilayah":"130000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":260,"sma_s":167,"sma":427,"smk_n":103,"smk_s":102,"smk":205,"slb_n":13,"slb_s":8,"slb":21,"sd_n":4142,"sd_s":257,"sd":4399,"smp_n":995,"smp_s":319,"smp":1314,"sekolah_n":5513,"sekolah_s":853,"sekolah":6366},{"nama":"Prov. Sumatera Barat","kode_wilayah":"080000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":231,"sma_s":95,"sma":326,"smk_n":111,"smk_s":101,"smk":212,"slb_n":31,"slb_s":117,"slb":148,"sd_n":3968,"sd_s":202,"sd":4170,"smp_n":672,"smp_s":128,"smp":800,"sekolah_n":5013,"sekolah_s":643,"sekolah":5656},{"nama":"Prov. Riau","kode_wilayah":"090000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":296,"sma_s":147,"sma":443,"smk_n":121,"smk_s":174,"smk":295,"slb_n":17,"slb_s":29,"slb":46,"sd_n":3209,"sd_s":473,"sd":3682,"smp_n":837,"smp_s":321,"smp":1158,"sekolah_n":4480,"sekolah_s":1144,"sekolah":5624},{"nama":"Prov. Aceh","kode_wilayah":"060000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":396,"sma_s":127,"sma":523,"smk_n":148,"smk_s":65,"smk":213,"slb_n":28,"slb_s":44,"slb":72,"sd_n":3333,"sd_s":132,"sd":3465,"smp_n":888,"smp_s":246,"smp":1134,"sekolah_n":4793,"sekolah_s":614,"sekolah":5407},{"nama":"Prov. Nusa Tenggara Barat","kode_wilayah":"230000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":156,"sma_s":159,"sma":315,"smk_n":89,"smk_s":213,"smk":302,"slb_n":15,"slb_s":27,"slb":42,"sd_n":3005,"sd_s":179,"sd":3184,"smp_n":604,"smp_s":307,"smp":911,"sekolah_n":3869,"sekolah_s":885,"sekolah":4754},{"nama":"Prov. D.K.I. Jakarta","kode_wilayah":"010000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":117,"sma_s":373,"sma":490,"smk_n":63,"smk_s":520,"smk":583,"slb_n":8,"slb_s":79,"slb":87,"sd_n":1537,"sd_s":913,"sd":2450,"smp_n":292,"smp_s":782,"smp":1074,"sekolah_n":2017,"sekolah_s":2667,"sekolah":4684},{"nama":"Prov. Sulawesi Tengah","kode_wilayah":"180000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":170,"sma_s":44,"sma":214,"smk_n":99,"smk_s":83,"smk":182,"slb_n":18,"slb_s":6,"slb":24,"sd_n":2660,"sd_s":230,"sd":2890,"smp_n":724,"smp_s":115,"smp":839,"sekolah_n":3671,"sekolah_s":478,"sekolah":4149},{"nama":"Prov. Kalimantan Selatan","kode_wilayah":"150000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":135,"sma_s":54,"sma":189,"smk_n":61,"smk_s":62,"smk":123,"slb_n":17,"slb_s":21,"slb":38,"sd_n":2774,"sd_s":140,"sd":2914,"smp_n":519,"smp_s":81,"smp":600,"sekolah_n":3506,"sekolah_s":358,"sekolah":3864},{"nama":"Prov. Kalimantan Tengah","kode_wilayah":"140000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":181,"sma_s":58,"sma":239,"smk_n":90,"smk_s":44,"smk":134,"slb_n":19,"slb_s":4,"slb":23,"sd_n":2433,"sd_s":199,"sd":2632,"smp_n":694,"smp_s":132,"smp":826,"sekolah_n":3417,"sekolah_s":437,"sekolah":3854},{"nama":"Prov. Papua","kode_wilayah":"250000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":138,"sma_s":96,"sma":234,"smk_n":79,"smk_s":58,"smk":137,"slb_n":9,"slb_s":4,"slb":13,"sd_n":1607,"sd_s":936,"sd":2543,"smp_n":482,"smp_s":190,"smp":672,"sekolah_n":2315,"sekolah_s":1284,"sekolah":3599},{"nama":"Prov. Sulawesi Tenggara","kode_wilayah":"200000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":233,"sma_s":58,"sma":291,"smk_n":99,"smk_s":59,"smk":158,"slb_n":18,"slb_s":50,"slb":68,"sd_n":2255,"sd_s":64,"sd":2319,"smp_n":683,"smp_s":70,"smp":753,"sekolah_n":3288,"sekolah_s":301,"sekolah":3589},{"nama":"Prov. Jambi","kode_wilayah":"100000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":156,"sma_s":69,"sma":225,"smk_n":102,"smk_s":74,"smk":176,"slb_n":12,"slb_s":6,"slb":18,"sd_n":2335,"sd_s":122,"sd":2457,"smp_n":559,"smp_s":114,"smp":673,"sekolah_n":3164,"sekolah_s":385,"sekolah":3549},{"nama":"Prov. Sulawesi Utara","kode_wilayah":"170000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":119,"sma_s":108,"sma":227,"smk_n":89,"smk_s":101,"smk":190,"slb_n":6,"slb_s":20,"slb":26,"sd_n":1375,"sd_s":853,"sd":2228,"smp_n":468,"smp_s":249,"smp":717,"sekolah_n":2057,"sekolah_s":1331,"sekolah":3388},{"nama":"Prov. Bali","kode_wilayah":"220000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":78,"sma_s":81,"sma":159,"smk_n":51,"smk_s":125,"smk":176,"slb_n":11,"slb_s":3,"slb":14,"sd_n":2324,"sd_s":123,"sd":2447,"smp_n":255,"smp_s":149,"smp":404,"sekolah_n":2719,"sekolah_s":481,"sekolah":3200},{"nama":"Prov. Kalimantan Timur","kode_wilayah":"160000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":135,"sma_s":80,"sma":215,"smk_n":86,"smk_s":133,"smk":219,"slb_n":9,"slb_s":26,"slb":35,"sd_n":1655,"sd_s":221,"sd":1876,"smp_n":431,"smp_s":204,"smp":635,"sekolah_n":2316,"sekolah_s":664,"sekolah":2980},{"nama":"Prov. Maluku","kode_wilayah":"210000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":205,"sma_s":73,"sma":278,"smk_n":80,"smk_s":33,"smk":113,"slb_n":8,"slb_s":5,"slb":13,"sd_n":1241,"sd_s":540,"sd":1781,"smp_n":499,"smp_s":144,"smp":643,"sekolah_n":2033,"sekolah_s":795,"sekolah":2828},{"nama":"Prov. D.I. Yogyakarta","kode_wilayah":"040000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":69,"sma_s":96,"sma":165,"smk_n":50,"smk_s":167,"smk":217,"slb_n":9,"slb_s":70,"slb":79,"sd_n":1438,"sd_s":407,"sd":1845,"smp_n":214,"smp_s":228,"smp":442,"sekolah_n":1780,"sekolah_s":968,"sekolah":2748},{"nama":"Prov. Maluku Utara","kode_wilayah":"270000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":132,"sma_s":67,"sma":199,"smk_n":63,"smk_s":62,"smk":125,"slb_n":16,"slb_s":5,"slb":21,"sd_n":1105,"sd_s":205,"sd":1310,"smp_n":349,"smp_s":132,"smp":481,"sekolah_n":1665,"sekolah_s":471,"sekolah":2136},{"nama":"Prov. Bengkulu","kode_wilayah":"260000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":108,"sma_s":31,"sma":139,"smk_n":64,"smk_s":38,"smk":102,"slb_n":13,"slb_s":4,"slb":17,"sd_n":1307,"sd_s":75,"sd":1382,"smp_n":381,"smp_s":42,"smp":423,"sekolah_n":1873,"sekolah_s":190,"sekolah":2063},{"nama":"Prov. Sulawesi Barat","kode_wilayah":"330000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":74,"sma_s":14,"sma":88,"smk_n":58,"smk_s":79,"smk":137,"slb_n":12,"slb_s":12,"slb":24,"sd_n":1301,"sd_s":24,"sd":1325,"smp_n":308,"smp_s":56,"smp":364,"sekolah_n":1753,"sekolah_s":185,"sekolah":1938},{"nama":"Prov. Kepulauan Riau","kode_wilayah":"310000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":84,"sma_s":51,"sma":135,"smk_n":33,"smk_s":69,"smk":102,"slb_n":7,"slb_s":9,"slb":16,"sd_n":686,"sd_s":250,"sd":936,"smp_n":222,"smp_s":131,"smp":353,"sekolah_n":1032,"sekolah_s":510,"sekolah":1542},{"nama":"Prov. Papua Barat","kode_wilayah":"320000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":76,"sma_s":45,"sma":121,"smk_n":31,"smk_s":21,"smk":52,"slb_n":4,"slb_s":1,"slb":5,"sd_n":634,"sd_s":401,"sd":1035,"smp_n":216,"smp_s":80,"smp":296,"sekolah_n":961,"sekolah_s":548,"sekolah":1509},{"nama":"Prov. Gorontalo","kode_wilayah":"300000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":55,"sma_s":6,"sma":61,"smk_n":40,"smk_s":16,"smk":56,"slb_n":8,"slb_s":0,"slb":8,"sd_n":919,"sd_s":21,"sd":940,"smp_n":314,"smp_s":19,"smp":333,"sekolah_n":1336,"sekolah_s":62,"sekolah":1398},{"nama":"Prov. Bangka Belitung","kode_wilayah":"290000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":43,"sma_s":24,"sma":67,"smk_n":34,"smk_s":20,"smk":54,"slb_n":7,"slb_s":2,"slb":9,"sd_n":754,"sd_s":57,"sd":811,"smp_n":159,"smp_s":51,"smp":210,"sekolah_n":997,"sekolah_s":154,"sekolah":1151},{"nama":"Prov. Kalimantan Utara","kode_wilayah":"340000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":41,"sma_s":18,"sma":59,"smk_n":18,"smk_s":11,"smk":29,"slb_n":4,"slb_s":0,"slb":4,"sd_n":431,"sd_s":36,"sd":467,"smp_n":144,"smp_s":30,"smp":174,"sekolah_n":638,"sekolah_s":95,"sekolah":733},{"nama":"Luar Negeri","kode_wilayah":"350000  ","id_level_wilayah":1,"mst_kode_wilayah":"000000  ","induk_provinsi":null,"kode_wilayah_induk_provinsi":null,"induk_kabupaten":null,"kode_wilayah_induk_kabupaten":null,"sma_n":8,"sma_s":5,"sma":13,"smk_n":0,"smk_s":1,"smk":1,"slb_n":0,"slb_s":0,"slb":0,"sd_n":13,"sd_s":109,"sd":122,"smp_n":9,"smp_s":51,"smp":60,"sekolah_n":30,"sekolah_s":166,"sekolah":196}]';
		
		$provs = json_decode($json, true);
		
		foreach($provs as $prov)
		{
			//$id = (int) substr($prov['kode_wilayah'], 0, 2);
			//echo "INSERT ms_prov VALUES ('".$id."', '".$prov['kode_wilayah']."', '".trim(str_replace('Prov. ', '', $prov['nama']))."');<br>";
			echo "'".trim($prov['kode_wilayah'])."',";
		}	
	}
	
	public function s()
	{
		$sekolah = '52900;52500;51500;50500;52200;50100;52400;50400;51300;50700;52100;50800;51000;51800;50300;51400;50900;51200;52600;51900;51100;52000;52700;50200;52300;52800;51700;50600;51600;56800;56500;56300;56200;56100;56400;56600;56700;56000';
		
		$jenjang = array('SMA', 'SMK');
		
		$ss = explode(';', $sekolah);
		//$ss = array('52900');
		
		foreach($ss as $s)
		{
			for($page=1; $page<=10; $page++)
			{
			$fields = array(
				'page' => $page,
				'kode_kabupaten' => '0'.$s,
				'bentuk' => 'SMK',
				'status' => 'N',
				'akreditasi' => 'semua'
			);
			
			$url = 'http://sekolah.data.kemdikbud.go.id/chome/pagingpencarian';
			
			//url-ify the data for the POST
			$fields_string = '';
			foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			rtrim($fields_string, '&');
			
			//open connection
			$ch = curl_init();
			
			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10); # timeout after 10 seconds, you can increase it
		   //curl_setopt($ch,CURLOPT_HEADER,false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  # Set curl to return the data instead of printing it to the browser.
			curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)"); # Some server may refuse your request if you dont pass
			
			curl_setopt($ch,CURLOPT_POST, count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
			
			//execute post
			$result = curl_exec($ch);
			
			//close connection
			curl_close($ch);
			//echo $result;
			$dom = new simple_html_dom();
			$html = $dom->load($result);
			
			$school = $html->find('ul.list-group');
			
			if(!empty($school))
			{
				foreach($school as $sch)
				{
					$schoolname = $sch->find('a.text-info', 0)->innertext;		
					$schoolname = trim(preg_replace("/\([^)]+\)/","",$schoolname));
					
					$alamat = '';
					foreach($sch->find('li.text-muted') as $schalamat)
					{
						$alamat .= trim(str_replace(array('<i class="glyphicon glyphicon-road text-info"></i>', '<i class="glyphicon glyphicon-globe text-info">', '&nbsp;'), '', $schalamat->innertext)).' ';
					}
					
					echo "INSERT INTO ms_school VALUES ('', '$schoolname', '".trim($alamat)."', '');<br>";
				}
			}
			
			}
		}
	}
}

?>