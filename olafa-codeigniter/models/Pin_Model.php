<?php
class Pin_Model extends CI_Model 
{
	
	private $table = 'pin';
	private $id    = 'pin_id';
	
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
								left join periode on periode_id=pin_id_periode
								left join participant_registration on sreg_id_pin=pin_id
								left join participant on sreg_id_participant=par_id
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

    function getPinActiveByUser($idp)
    {
        return $this->db->query("SELECT *
                                 FROM participant_registration
                                 LEFT JOIN ".$this->table." ON sreg_id_pin = pin_id
                                 LEFT JOIN periode ON pin_id_periode = periode_id
                                 WHERE sreg_id_participant = '$idp' AND sreg_active = 'Y'
                                 ORDER BY sreg_input_date DESC
                                 LIMIT 0, 1");
    }

    function getPinByUser($idp)
    {
        return $this->db->query("SELECT * 
                                 FROM ".$this->table." 
                                 WHERE pin_booking_by = '$idp'");
    }

	function getPinByPeriode($periode)
    {
        return $this->db->query("SELECT pin_price, pin_max_prodi, COUNT(pin_price) as jml 
                                 FROM ".$this->table." 
                                 WHERE pin_id_periode = '$periode' AND pin_status = 'N' AND pin_type = 'online' AND (pin_booking_by = '' OR pin_booking_by = '0' OR pin_booking_by is NULL)
                                 GROUP BY pin_price, pin_max_prodi 
                                 ORDER BY pin_price ASC");
    }

	function getPinByTypePeriode($periode, $type)
    {
        return $this->db->query("SELECT pin_price, pin_max_prodi, pin_kodediskon, COUNT(pin_price) as jml 
                                 FROM ".$this->table." 
                                 WHERE pin_id_periode = '$periode' 
                                    AND pin_status = 'N' 
                                    AND lower(pin_type) = '".strtolower($type)."' 
                                    AND (pin_booking_by = '' OR pin_booking_by = '0' OR pin_booking_by is NULL)
                                 GROUP BY pin_kodediskon, pin_max_prodi, pin_price
                                 ORDER BY pin_price ASC");
    }

    function getPinAffByPeriode($periode)
    {
        return $this->db->query("SELECT pin_price, pin_max_prodi, COUNT(pin_price) as jml 
                                 FROM ".$this->table." 
                                 LEFT JOIN periode ON pin_id_periode = periode_id
                                 WHERE pin_id_periode = '$periode' AND pin_status = 'N' AND pin_type = 'affiliate_online' AND (pin_booking_by = '' OR pin_booking_by = '0' OR pin_booking_by is NULL)
                                 GROUP BY pin_price, pin_max_prodi 
                                 ORDER BY pin_price ASC");
    }

    function getPinToken($periode, $pin, $token)
    {
        return $this->db->query("SELECT *
                                 FROM ".$this->table." 
                                 LEFT JOIN participant ON par_id = pin_booking_by
                                 WHERE pin_id_periode = '$periode' AND pin_status = 'N' AND pin_transaction_number = '$pin' AND pin_token = '$token' AND (pin_booking_by != '' OR pin_booking_by != '0' OR pin_booking_by is not NULL)");
    }

    function getPinTokenOffline($periode, $pin, $token)
    {
        return $this->db->query("SELECT *
                                 FROM ".$this->table." 
                                 LEFT JOIN periode ON pin_id_periode = periode_id
                                 WHERE pin_id_periode = '$periode' AND pin_status = 'N' AND pin_type = 'offline' AND pin_transaction_number = '$pin' AND pin_token = '$token'");
    }

    function getPinTokenAfiliasi($periode, $pin, $token)
    {
        return $this->db->query("SELECT *
                                 FROM ".$this->table." 
                                 LEFT JOIN periode ON pin_id_periode = periode_id
                                 WHERE pin_id_periode = '$periode' AND pin_status = 'N' AND pin_type = 'affiliate' AND pin_transaction_number = '$pin' AND pin_token = '$token' AND (pin_booking_by = '' OR pin_booking_by = '0' OR pin_booking_by is NULL)");
    }

    function getPinTokenOnline($id, $periode, $pin, $token)
    {
        return $this->db->query("SELECT *
                                 FROM ".$this->table." 
                                 LEFT JOIN periode ON pin_id_periode = periode_id
                                 WHERE pin_id_periode = '$periode' AND pin_status = 'N' AND pin_type = 'online' 
                                 AND pin_transaction_number = '$pin' AND pin_token = '$token' AND pin_booking_by = '$id'");
    }

    function getPinTokenAffiliateOnline($id, $periode, $pin, $token)
    {
        return $this->db->query("SELECT *
                                 FROM ".$this->table." 
                                 WHERE pin_id_periode = '$periode' AND pin_status = 'N' AND pin_type = 'affiliate_online' 
                                 AND pin_transaction_number = '$pin' AND pin_token = '$token' AND pin_booking_by = '$id'");
    }

    function getFreePinByPeriode($periode, $price, $max_prodi)
    {
        return $this->db->query("SELECT *
                                 FROM ".$this->table."
                                 LEFT JOIN periode ON pin_id_periode = periode_id
                                 WHERE pin_id_periode = '$periode' AND pin_price = '$price' AND pin_max_prodi = '$max_prodi' AND pin_status = 'N' AND pin_type = 'online' AND (pin_booking_by = '' OR pin_booking_by = '0' OR pin_booking_by is NULL)                                 
                                 ORDER BY pin_id ASC
                                 LIMIT 0, 1");
    }

    function getFreePinByTypePeriode($type, $periode, $price, $max_prodi, $discount)
    {
        $d = "";
        if(!empty($discount) and $discount != 'null')
            $d = "AND discount_id = '$discount'";

        return $this->db->query("SELECT *
                                 FROM ".$this->table."
                                 LEFT JOIN periode ON pin_id_periode = periode_id
                                 LEFT JOIN discount ON pin_kodediskon = discount_id
                                 WHERE pin_id_periode = '$periode' AND pin_price = '$price' AND pin_max_prodi = '$max_prodi' AND pin_status = 'N' AND pin_type = '$type' AND (pin_booking_by = '' OR pin_booking_by = '0' OR pin_booking_by is NULL) $d                            
                                 ORDER BY pin_id ASC
                                 LIMIT 0, 1");
    }

    function getFreePinAffiliateByPeriode($periode, $price, $max_prodi)
    {
        return $this->db->query("SELECT *
                                 FROM ".$this->table."
                                 LEFT JOIN periode ON pin_id_periode = periode_id
                                 WHERE pin_id_periode = '$periode' AND pin_price = '$price' AND pin_max_prodi = '$max_prodi' AND pin_status = 'N' AND pin_type = 'affiliate_online' AND (pin_booking_by = '' OR pin_booking_by = '0' OR pin_booking_by is NULL)
                                 ORDER BY pin_id ASC
                                 LIMIT 0, 1");
    }

    function getbyin($id)
	{
		return $this->db->query("SELECT * FROM pin where pin_id in ($id)");
	} 
	
	function cekTotalPin($pin)
	{
		return $this->db->query("SELECT count(*)total FROM pin where pin_transaction_number like '$pin%'");
	} 
	
	function cekPin($pin)
	{
		return $this->db->query("SELECT pin_transaction_number FROM pin where pin_transaction_number like '$pin%'");
	} 
}
?>