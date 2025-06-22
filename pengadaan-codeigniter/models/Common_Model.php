<?php
class Common_Model extends CI_Model {
	
	
	
	
	function form_status()
	{
		$array[''] 						= 'Semua Status';
		$array['pengajuan'] 			= 'Pengajuan dari Prodi';
		$array['logistik'] 				= 'Pengajuan ke Logistik'; 
		$array['penerimaan'] 			= 'Penerimaan Buku';  
		$array['r_ketersediaan'] 		= 'Ketersediaan buku'; 
		$array['s_email'] 				= 'Konfirmasi Email';  
		
		return $array;
	} 
	
	function form_book_type()
	{
		$array[''] 						= 'Semua Jenis Buku';
		$array['cetak'] 				= 'Buku Cetak'; 
		$array['ebook'] 				= 'E-Book'; 
		
		return $array;
	} 
	
	function form_book_type_option()
	{
		$array[''] 						= 'Pilih Buku';
		$array['cetak'] 				= 'Buku Cetak'; 
		$array['ebook'] 				= 'E-Book'; 
		
		return $array;
	} 
}
?> 