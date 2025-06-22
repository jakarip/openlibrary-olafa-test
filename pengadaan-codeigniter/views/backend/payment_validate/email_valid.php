<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
	<base href="<?= base_url() ?>" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="admin-themes-lab">
    <meta name="author" content="themes-lab">
    <link rel="shortcut icon" href="tools/assets/global/images/favicon.png" type="image/png">
    <title>Email Validasi</title>
   


<style>
h1, .h1 {
    font-size: 36px;
}h1, .h1, h2, .h2, h3, .h3 {
    margin-top: 20px;
    margin-bottom: 10px;
}
body {
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
    font-size: 14px;
    line-height: 1.42857143;
    color: #333;
}html {
    font-family: sans-serif;
    -webkit-text-size-adjust: 100%;
}

.btn-primary:hover, .btn-primary:focus, .btn-primary.focus, .btn-primary:active, .btn-primary.active, .open > .dropdown-toggle.btn-primary {
    color: #fff;
    background-color: #286090;
    border-color: #204d74;
}.btn-primary:hover, .btn-primary:focus, .btn-primary.focus, .btn-primary:active, .btn-primary.active, .open > .dropdown-toggle.btn-primary {
    color: #fff;
    background-color: #286090;
    border-color: #204d74;
}
.btn:hover, .btn:focus, .btn.focus {
    color: #333;
    text-decoration: none;
}
.btn:focus, .btn:active:focus, .btn.active:focus, .btn.focus, .btn.focus:active, .btn.active.focus {
    outline: thin dotted;
    outline: 5px auto -webkit-focus-ring-color;
    outline-offset: -2px;
}
.btn-primary {
    color: #fff;
    background-color: #1A82C3;
    border-color: #1A82C3;
}
.btn {
    display: inline-block;
    margin-bottom: 0;
    font-weight: normal;
    text-align: center;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    background-image: none;
    border: 1px solid transparent;
    white-space: nowrap;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    border-radius: 4px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
</style>
  </head>
<body>


<style>
h1, .h1 {
    font-size: 26px;
}h1, .h1, h2, .h2, h3, .h3 {
    margin-top: 20px;
    margin-bottom: 10px;
}
body {
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
    font-size: 14px;
    line-height: 1.42857143;
    color: #333;
}html {
    font-family: sans-serif;
    -webkit-text-size-adjust: 100%;
}
button { 
color: #fff;
    background-color: #1A82C3;
    border-color: #1A82C3;
    display: inline-block;
    margin-bottom: 0;
    font-weight: normal;
    text-align: center;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    background-image: none;
    border: 1px solid transparent;
    white-space: nowrap;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    border-radius: 4px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
</style>
<table width="100%"> 
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr> 
	<tr>
		<td colspan="2">
			<p>Dear, Calon Peserta <?= $setting['website_name'] ?>,</p>

            <p align="justify">Terima kasih sudah memilih <?= $setting['institution'] ?> sebagai calon kampus untuk melanjutkan studi Anda.  Bukti pembayaran token yang telah anda upload pada akun PMB ITTelkom Surabaya dinyatakan <strong>valid</strong> sesuai dengan nominal yang harus dibayarkan. Berikut ini adalah informasi akun anda.</p>

            Nomor Transaksi Anda adalah <strong><?php echo $pin->pin_transaction_number ?></strong><br>
			Nomor Validasi (Token) : <strong><?php echo $pin->pin_token ?></strong><br> 
            Masa Berlaku Token : <strong><?php echo y_date_text($pin->periode_start_date) ?></strong> Sampai <strong><?php echo y_date_text($pin->periode_end_date) ?></strong><br><br>
            Berikut ini tata cara Validasi Nomor Trasaksi <?= $setting['website_name'] ?> :<br>
		</td> 
	</tr> 
    
	<tr>
		<td  colspan="2" style="vertical-align:top;">   
            <ol>
                <li>Login menggunakan username dan password yang digunakan mendaftar PMB ITTelkom Surabaya</li>  
                <li>Pilih pada bagian Registrasi</li>  
                <li>Pilih Lanjutkan Pendaftaran</li>  
                <li>Pastikan mengisi menu Kuisioner, Biodata, dan seluruh tahapan seleksi PMB ITTelkom Surabaya sesuai dengan jalur yang dipilih pada akun PMB ITTelkom Surabaya</li>  
                <li>Apabila jalur pendaftaran telah dinyatakan berakhir, maka kamu tidak dapat memasukkan data ataupun mengikuti seleksi PMB karena token pendaftaran hanya berlaku selama periode tertentu </li>  
                <li>Jangan sampe kelewatan ya kawan dalam memasukkan data PMB.</li>  
            </ol>
		</td>
	</tr>
    <tr>
    <td colspan="2"><br><br>
			Kuy, kami tunggu ya kamu di Telkom Kampus Surabaya. <br> </td>
    </tr>
    <tr>
		<td colspan="2"><br><br><b>Salam,</b><br>
			<b>Tim <?= $setting['website_name'] ?></b></td>
	</tr>   
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr> 
</table>  
</html>
