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
    <title>Make Admin Template &amp; Builder</title>
   


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
		<td colspan="3"><h3>Terimakasih telah berlangganan di Telkom University Open Library</h3></td>
	</tr>
	<tr>
		<td colspan="3"><img style="vertical-align:middle;" src="https://openlibrary.telkomuniversity.ac.id/olafa/tools/images/logo.png" width="90"><span style="font-weight:bold;font-size:24px;color:#c9302c">Telkom University Open Library</span></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">Pembayaran anda telah berhasil divalidasi. Berikut detail berlangganan Anda : </td>
	</tr>
	<tr>
		<td width="10%">No Transaksi</td>
		<td width="1%">:</td>
		<td><?php echo $subscribe_transaction ?></td>
	</tr>
	<tr>
		<td>Tanggal Pembayaran</td>
		<td>:</td>
		<td><?php echo convert_format_dates($subscribe_payment_date) ?></td>
	</tr>
	<tr>
		<td>Nominal Pembayaran</td>
		<td>:</td>
		<td><?php echo y_num_idr($subscribe_payment_code) ?></td>
	</tr>
	<tr>
		<td>Tanggal Berlangganan</td>
		<td>:</td>
		<td><?php echo convert_format_dates($subscribe_start_date).' - '.convert_format_dates($subscribe_end_date); ?></td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr> 
	<tr>
		<td colspan="3">Silahkan login untuk mengakses koleksi Open Library Telkom University<br>
        https://openlibrary.telkomuniversity.ac.id</td>
	</tr>
</table> 
</html>