<?php
class Student_model extends CI_Model 
{
	
	private $table = 'student';
	private $id    = 'student_id';
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	// Query for new datatables purpose ;
	//--------------------------------------------------------------------------------------------------------------------------
	function dtquery($param)
	{
		return $this->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM ".$this->table."
								 LEFT JOIN student_registration ON student_id = sreg_id_student
								 $param[where] $param[order] $param[limit]");
	}
	
	function dtfiltered()
	{
		$result = $this->db->query('SELECT FOUND_ROWS() as jumlah')->row();
		
		return $result->jumlah;
	}
	
	function dtcount()
	{
		return $this->db->count_all($this->table);
	}
	//--------------------------------------------------------------------------------------------------------------------------
	
	
	function getall()
	{
		return $this->db->get($this->table);
	}
	
	function getbyquery($param)
	{
		return $this->db->query("SELECT * FROM ".$this->table." $param[where] $param[order] $param[limit]");
	}
	
	function countbyquery($param)
	{
		$result = $this->db->query("SELECT COUNT(".$this->id.") as jumlah FROM ".$this->view." $param[where]")->row();
		
		if(!empty($result))
			return $result->jumlah;
		else
			return 0;
	}
	
	function countall()
	{
		return $this->db->count_all($this->table);
	}
	
	function getby($item)
	{
		$this->db->where($item);
		return $this->db->get($this->table);
	}
	
	function getbyid($id)
	{
		$this->db->where($this->id, $id);
		return $this->db->get($this->table);
	}
	
	function add($item)
	{
		$this->db->insert($this->table, $item);
		return $this->db->insert_id();
	}
	
	function edit($id, $item)
	{
		$this->db->where($this->id, $id);
		return $this->db->update($this->table, $item);
	}
	
	function delete($id)
	{
		$this->db->where($this->id, $id);
		return $this->db->delete($this->table);
	}
	
	/**		FOR ADDITONAL FUNCTION
			Untuk Menambah function baru silahkan letakkan di bawah ini.
	**/
	
	function getlogin($username, $password)
	{
		return $this->db->query("SELECT * FROM ".$this->table." WHERE student_username = '$username' AND student_password = '$password'");
	}
	
	function getprofile($id)
	{ 
		return $this->db->query("SELECT * FROM student_registration WHERE sreg_id_student = '$id'");
	}
	
	function getschool()
	{
		return $this->db->query("SELECT * FROM ms_school order by school_name");
	}
	
	function getcourse()
	{
		return $this->db->query("SELECT * FROM ms_course order by course_name");
	}
	
	function getcourseactive()
	{
		return $this->db->query("SELECT * FROM ms_course where course_status='1' order by course_name");
	} 
	
	function getkec($id)
	{
		return $this->db->query("SELECT * FROM ms_kec where kec_kab_id='$id' order by kec_name");
	} 
	
	function gettrack()
	{
		return $this->db->query("SELECT * FROM ms_track where track_status='1' and track_id!='3' order by track_name");
	}  
	function gettracknoteast()
	{
		return $this->db->query("SELECT * FROM ms_track where track_status='1' and track_id!='1' order by track_name");
	}  
	
	function editreg($id, $item)
	{
		$this->db->where('sreg_id_student', $id);
		return $this->db->update('student_registration', $item);
	}
	
	function getregistration($id)
	{
		return $this->db->query("SELECT *,
		(select raport_url from student_raport where raport_id_student=sreg_id_student and raport_semester='1')smstr_1,
		(select raport_url from student_raport where raport_id_student=sreg_id_student and raport_semester='2')smstr_2,
		(select raport_url from student_raport where raport_id_student=sreg_id_student and raport_semester='3')smstr_3,
		(select raport_url from student_raport where raport_id_student=sreg_id_student and raport_semester='4')smstr_4,
		(select raport_url from student_raport where raport_id_student=sreg_id_student and raport_semester='5')smstr_5
		FROM student_registration 
		left join student on student_id=sreg_id_student 
		left join ms_school on school_id=sreg_id_school 
		left join ms_kec on kec_id=sreg_id_kec 
		left join ms_track on track_id=sreg_id_track
		left join ms_kodepos on pos_code=sreg_postcode
		left join ms_prodi on prodi_id=sreg_choice_prodi
		where sreg_id_student='$id' group by student_id");
	}
	
	function getinfo($id="")
	{
		if ($id!="") return $this->db->query("select * from ms_info where info_id='$id' order by info_prodi");
		else return $this->db->query("select * from ms_info order by info_prodi");
	}
	
	function getschoolbyid($id)
	{
		return $this->db->query("SELECT * FROM ms_school left join ms_kec on kec_id=school_id_kec  where school_id='$id'");
	}
	
	function getsetting($option)
	{
		return $this->db->query("SELECT * FROM settings where setting_option='$option'");
	}
	
	function addSchool($item)
	{
		$this->db->insert('ms_school', $item);
		return $this->db->insert_id(); 
	} 
	
	function getschoolbyname($name)
	{
		return $this->db->query("SELECT * FROM ms_school left join ms_kec on kec_id=school_id_kec where school_name like'%$name%' or school_npsn like'%$name%' order by school_name limit 10");
	} 
	
	function getfeebyprodi($prodi,$track)
	{
		return $this->db->query("select * from ms_fee
		join ms_component on fee_id_component=component_id 
		join ms_track on fee_id_track=track_id 
		where component_status='1' and track_status='1' and fee_id_track='$track' and fee_id_prodi='$prodi' order by component_id");
	}
	
	function getfeebyid($id)
	{
		return $this->db->query("select * from ms_fee join ms_component on fee_id_component=component_id  where fee_id='$id'");
	} 
	
	function getkecbyname($name)
	{
		return $this->db->query("SELECT * FROM ms_kec  where kec_name like'%$name%' or kec_kab like'%$name%' or kec_prov like'%$name%' order by kec_prov,kec_kab,kec_name limit 20");
	}  
	
	function getkabbyname($name)
	{
		return $this->db->query("SELECT * FROM ms_kab left join ms_prov on kab_id_prov=prov_id where kab_name like'%$name%' or prov_name like'%$name%' order by prov_name,kab_name limit 15");
	} 
	
	function getkodepos($id)
	{
		return $this->db->query("SELECT * FROM ms_kodepos where pos_id_kec='$id' order by pos_code");
	} 
	
	function getkodekec($id)
	{
		return $this->db->query("SELECT * FROM ms_kec where kec_kab_id='$id' order by kec_name");
	} 
	
	function getkab()
	{
		return $this->db->query("SELECT * FROM ms_kab left join ms_prov on kab_id_prov=prov_id order by prov_name,kab_name");
	} 
	
	
	
	function add_raport($item)
	{
		$this->db->insert('student_raport', $item);
		return $this->db->insert_id();
	}
	
	function getraport($smstr,$id)
	{
		return $this->db->query("SELECT * FROM student_raport where raport_id_student='$id' and raport_semester='$smstr'");
	}  
	
	function edit_raport($where, $item)
	{
		$this->db->where($where); 
		return $this->db->update('student_raport', $item);
	}
	
	
	/* Dashboard */ 
	 
	function get_dashboard_token()
	{
		return $this->db->query("SELECT
									SUM( CASE WHEN pin_status = 'Y' THEN 1 ELSE 0 END ) AS ya,
									SUM( CASE WHEN pin_status = 'N' THEN 1 ELSE 0 END ) AS tidak 
								 FROM pin"); 
	}
	
	function get_dashboard_used()
	{
		return $this->db->query("SELECT
									SUM( CASE WHEN sreg_status_Print = 'Y' THEN 1 ELSE 0 END ) AS ya,
									SUM( CASE WHEN sreg_status_Print = 'N' THEN 1 ELSE 0 END ) AS tidak 
								 FROM participant_registration 
								 LEFT JOIN participant ON sreg_id_participant = par_id
								 LEFT JOIN pin ON sreg_id_pin = pin_id
								 WHERE pin_status = 'Y'"); 
	}  
	
	function get_dashboard_lulus()
	{
		return $this->db->query("SELECT
									SUM( CASE WHEN sreg_status_pass = 'Y' and sreg_status_Print = 'Y' THEN 1 ELSE 0 END ) AS ya,
									SUM( CASE WHEN sreg_status_pass = 'N' and sreg_status_Print = 'Y' THEN 1 ELSE 0 END ) AS tidak 
								 FROM participant_registration"); 
	}  
	
	function get_dashboard_ke()
	{
		return $this->db->query("SELECT sreg_choice, COUNT(*) as jml
								 FROM participant_registration
								 GROUP BY sreg_choice"); 
	}  
	
	function get_dashboard_prodi()
	{
		return $this->db->query("SELECT prodi_name, COUNT(*) as jml
								 FROM participant_registration LEFT JOIN ms_prodi ON sreg_choice_prodi = prodi_id
								 WHERE prodi_name != ''
								 GROUP BY sreg_choice_prodi"); 
	} 
	
	function check_booked_pin($email)
	{
		return $this->db->query("SELECT * FROM student WHERE student_type_pin='online' and student_status='N' and student_request='$email'"); 
	}  
	
	function get_pin()
	{
		return $this->db->query("SELECT * FROM student WHERE student_type_pin='online' and student_status='N' and (student_request is null or student_request='') limit 1"); 
	}   
	
	function getprodi()
	{
		return $this->db->query("SELECT * FROM ms_prodi order by prodi_name");
	} 
	
	function getprodiactive()
	{
		return $this->db->query("SELECT * FROM ms_prodi where prodi_status='1' order by prodi_name");
	}   
}
?>