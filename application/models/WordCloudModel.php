<?php
class WordCloudModel extends CI_Model 
{
	
	private $table = 'wordcloud';
	private $id    = 'wc_id';

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
								left join member m on m.id=subscribe_id_member
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
	
	function check_date()
	{    
		return $this->db->query("select count(*) total from wordcloud where wc_date='".date('Y-m-d H:00:00')."'")->row();	 
	} 

	function emptyWordCloudTable()
	{
		$this->db->empty_table('wordcloud');
	}
	
	function get_rent()
	{    
		$today = date('Y-m-d'); // Today's date in 'Y-m-d' format
		$thirtyDaysAgo = date('Y-m-d', strtotime('-1 days')); // Date 30 days ago in 'Y-m-d' format
		$tenDaysAgo = date('Y-m-d', strtotime('-1 days')); // Date 30 days ago in 'Y-m-d' format
	
		$query = "SELECT sub.name title FROM rent r
				  LEFT JOIN knowledge_stock ks ON ks.id = r.knowledge_stock_id
				  LEFT JOIN knowledge_item ki ON ki.id = knowledge_item_id
				  LEFT JOIN knowledge_subject sub ON sub.id = ki.knowledge_subject_id
				  order by r.rent_date desc limit 1000";

				//    echo $query;
		return $this->db->query($query);	
	} 
	
	function get_access()
	{    
		$today = date('Y-m-d'); // Today's date in 'Y-m-d' format
		$thirtyDaysAgo = date('Y-m-d', strtotime('-1 days')); // Date 30 days ago in 'Y-m-d' format
		$tenDaysAgo = date('Y-m-d', strtotime('-1 days')); // Date 30 days ago in 'Y-m-d' format
	
		$query = "SELECT sub.name title FROM knowledge_item_view r
				  LEFT JOIN knowledge_item ki ON ki.id = kiv_id_item 
				  LEFT JOIN knowledge_subject sub ON sub.id = ki.knowledge_subject_id
				  order by r.kiv_date desc limit 3000";

				//    echo $query;
		return $this->db->query($query);	
	} 
	
	function insert_word($data)
	{    
		$this->db->insert_batch($this->table, $data['wordcloud']);
	} 
	
	function member($id)
	{   
		return $this->db->query("select * from member where id='$id'"); 
	} 
	
	function getTransactionNumber()
	{   
		return $this->db->query("select max(subscribe_transaction)max from member_subscribe where subscribe_transaction like '".date('ymd')."%'"); 
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
	
	function aktivasi($where, $item)
	{
		$this->db->where($where);
		return $this->db->update($this->table, $item);
	}
	
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

    function getcoursebytype($type)
    {
        return $this->db->query("SELECT * FROM ms_course WHERE course_type = '$type' and course_status='1' order by course_name");
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

	function check_booked_pin($email)
	{
		return $this->db->query("SELECT * FROM student WHERE student_type_pin='online' and student_status='N' and student_request='$email'"); 
	}  
	
	function get_pin()
	{
		return $this->db->query("SELECT * FROM student WHERE student_type_pin='online' and student_status='N' and (student_request is null or student_request='') limit 1"); 
	}   
	
	function getall_view() 
	{
		return $this->db->query("SELECT pr.*,participant.*,pin.*,discount_code,school_name,ms_kec.*,periode_name,periode_track_type,ref_username,(select kec_prov from ms_kec where kec_id=sch.school_id_kec)prov_school FROM 								participant 
								LEFT JOIN participant_registration pr ON par_id = sreg_id_participant
								LEFT JOIN pin ON sreg_id_pin= pin_id
								LEFT JOIN periode ON pin_id_periode = periode_id
								LEFT JOIN discount ON discount_id = pin_kodediskon
								LEFT JOIN referral ON ref_id = sreg_referral
								LEFT JOIN ms_school sch ON par_id_school = school_id
								LEFT JOIN ms_kec ON par_id_kec = kec_id  
								ORDER BY par_fullname asc");
	} 
	
	function getKuesioner($id) 
	{  
		return $this->db->query("SELECT * FROM ".$this->table."
								 LEFT JOIN questionnaire_response on qr_id_user=par_id
								 LEFT JOIN ms_kec ON par_id_kec = kec_id where par_id in ($id)");
	} 
	
	function getQuestions() 
	{  

		return $this->db->query("SELECT * FROM questionnaire_question");
	} 
	
	function getOptions() 
	{ 
	
		return $this->db->query("SELECT * FROM questionnaire_options");
	}

    function get_viewfull($id)
    {
        $q = "SELECT * 
              FROM participant 
		      LEFT JOIN ms_school on school_id = par_id_school 
		      LEFT JOIN ms_kec on kec_id = par_id_kec 
		      LEFT JOIN ms_kodepos on pos_code = par_postcode
		      WHERE par_id = '$id'";

        return $this->db->query($q);
    }

    function check($id)
    {
        $query = "SELECT * 
                  FROM ".$this->table."
                  LEFT JOIN questionnaire_response ON qr_id_user = par_id
                  WHERE par_id = '$id'";
        $dbs = $this->db->query($query)->row();

        $return = array();
        //check biodata
        if(!empty($dbs->par_fullname) and !empty($dbs->par_birthplace) and !empty($dbs->par_gender) and !empty($dbs->par_nik) and !empty($dbs->par_mobile) and !empty($dbs->par_id_kec) and !empty($dbs->par_id_school) and !empty($dbs->par_photo) and ($dbs->par_birthdate >= '1980-01-01') and file_exists($dbs->par_photo))
            $return['biodata'] = true;
        else
            $return['biodata'] = false;

        if(!empty($dbs->qr_response))
            $return['qr'] = true;
        else
            $return['qr'] = false;

        return $return;
    }

    function getViewPartcipantReg($iduser, $idsreg)
    {
        $q = "SELECT * 
              FROM participant 
              LEFT JOIN participant_registration on (par_id = sreg_id_participant and sreg_id = '$idsreg')
              LEFT JOIN pin on sreg_id_pin = pin_id
              LEFT JOIN periode on pin_id_periode = periode_id
		      LEFT JOIN ms_kec on kec_id = par_id_kec 
		      LEFT JOIN ms_kodepos on (pos_code = par_postcode and pos_id_kec=par_id_kec)
		      WHERE par_id = '$iduser'";

        return $this->db->query($q);
    }
	
		function checkParticipantNumber($number) 
		{ 
		
			return $this->db->query("select par_participantnumber from participant where par_participantnumber like '".$number."%' 
			order by par_participantnumber desc limit 1");
		}
}
?>