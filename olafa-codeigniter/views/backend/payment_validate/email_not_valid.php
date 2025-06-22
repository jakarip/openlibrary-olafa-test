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

            <p align="justify">Terima kasih sudah memilih <?= $setting['institution'] ?> sebagai calon kampus untuk melanjutkan studi Anda. Bukti pembayaran token yang telah anda upload pada akun PMB ITTelkom Surabaya dinyatakan <strong>belum valid</strong> karena tidak sesuai dengan nominal yang harus dibayarkan. Berikut ini adalah informasi yang bisa Anda gunakan untuk melakukan pembayaran Nomor Transaksi (token) PMB ITTelkom Surabaya melalui Bank yang ada di Indonesia. 
            </p>

            Nomor Transaksi Anda adalah <strong><?php echo $pin->pin_transaction_number ?></strong><br>
			Kode Institusi : <strong><?= $setting['institution_code'] ?></strong><br>
			No. Virtual Account(VA) : <strong><?= $setting['institution_va'] ?><?php echo $pin->pin_transaction_number ?></strong><br>
            Berlaku Tanggal : <strong><?php echo y_date_text($pin->periode_start_date) ?></strong> Sampai <strong><?php echo y_date_text($pin->periode_end_date) ?></strong><br><br> 
            Berikut ini tata cara pembayaran Nomor Trasaksi <?= $setting['website_name'] ?> :<br>
		</td>
	</tr>
	<tr>
		<td width="2%" style="vertical-align:top;">1.</td>
		<td>
            Lakukan pembayaran biaya pendaftaran <?= $setting['website_name'] ?> sebesar <strong>Rp. <?php echo y_num_idr($pin->pin_price) ?></strong> melalui BANK MANDIRI : <br>
            <ol type="a">
                <li>
                    ATM
                    <ul>
                        <li>Pilih menu Pembayaran/Pembelian</li>
                        <li>Pilih Pendidikan</li>
                        <li>Masukkan kode pendidikan 89615 (ITTS) lalu tekan BENAR</li>
                        <li>Masukkan nomor virtual account (VA) lalu tekan tombol BENAR</li>
                        <li>Layar akan menampilkan informasi dan jumlah pembayaran</li>
                        <li>Tekan 1 jika data sesuai</li>
                        <li>Untuk melakukan eksekusi, tekan "YA", untuk pembatalan tekan "TIDAK"</li>
                        <li>Kode token dapat ditemukan pada struk pembayaran.</li>
                    </ul>
                </li>
                <li>
                    Teller
                    <ul>
                        <li>Isi blanko Multi Payment dengan mencantumkan nomor virtual account (VA) dan nama pendaftar dengan tujuan pembayaran Institut Teknologi Telkom Surabaya</li>
                        <li>Serahkan blanko ke teller untuk memproses pembayaran</li>
                        <li>Kode token berupa campuran Huruf dan Angka dapat ditemukan pada slip pembayaran. Apabila tidak menemukan pada slip pembayaran, silahkan hubungi petugas bank.</li>
                    </ul>
                </li>
                <li>
                    Internet Banking
                    <ul>
                        <li>Login dengan User ID dan Password</li>
                        <li>Pilih menu Pembayaran</li>
                        <li>Pilih Buat Pembayaran Baru</li>
                        <li>Pilih menu Pendidikan</li>
                        <li>Pilih rekening yang akan digunakan untuk membayar</li>
                        <li>Pilih Penyedia jasa: 89615 ITTS</li>
                        <li>Masukkan nomor virtual account (VA)</li>
                        <li>Klik "Lanjutkan", cek informasi yang muncul. Jika telah sesuai, masukan PIN yang degenerate oleh Token ke field yang tersedia. Pilih "Kirim"</li>
                        <li>Muncul bukti validasi dari sistem, print atau save untuk digunakan sebagai bukti.</li>
                        <li>Kode token dapat ditemukan pada bukti pembayaran.</li>
                    </ul>
                </li>

            </ol>
		</td>
	</tr>
    <tr>
        <td width="2%" style="vertical-align:top;">2.</td>
        <td>
            Lakukan pembayaran biaya pendaftaran <?= $setting['website_name'] ?> sebesar <strong>Rp. <?php echo y_num_idr($pin->pin_price) ?></strong> melalui <strong>SELAIN</strong> BANK MANDIRI : <br>
            <ol type="a">
                <li>
                    ATM
                    <ul>
                        <li>Pilih menu Transfer Antar Bank</li>
                        <li>Pilih Kode Bank Tujuan "008 – Mandiri"</li>
                        <li>Pilih Rekening Tujuan></li>
                        <li>Masukkan nomor virtual account (VA) lalu tekan tombol BENAR</li>
                        <li>Masukkan jumlah pembayaran sesuai tagihan yang harus dibayarkan</li>
                        <li>Klik "Lanjutkan", cek informasi yang muncul.</li>
                        <li>Jika telah sesuai, untuk melakukan eksekusi, tekan "YA", untuk pembatalan tekan "TIDAK"</li>
                        <li>Muncul bukti validasi dari sistem, simpan bukti pembayaran untuk digunakan sebagai bukti untuk upload bukti pembayaran.</li>
                    </ul>
                </li>
                <li>
                    Teller
                    <ul>
                        <li>Isi blanko Multi Payment dengan mencantumkan nomor virtual account (VA) dan nama pendaftar dengan tujuan pembayaran Institut Teknologi Telkom Surabaya</li>
                        <li>Serahkan blanko ke teller untuk memproses pembayaran</li>
                        <li>Muncul bukti validasi dari sistem, simpan bukti pembayaran untuk digunakan sebagai bukti untuk upload bukti pembayaran.</li>
                    </ul>
                </li>
                <li>
                    Internet Banking
                    <ul>
                        <li>Login dengan User ID dan Password</li>
                        <li>Pilih menu Transfer Antar Bank</li>
                        <li>Pilih Kode Bank Tujuan "008 – Mandiri"</li>
                        <li>Pilih Rekening Tujuan</li>
                        <li>Masukkan nomor virtual account (VA)</li>
                        <li>Klik "Lanjutkan", cek informasi yang muncul. Jika telah sesuai, masukan PIN yang degenerate oleh Token ke field yang tersedia. Pilih "Kirim"</li>
                        <li>Muncul bukti validasi dari sistem, print atau save untuk digunakan sebagai bukti.</li>
                    </ul>
                </li>

            </ol>
        </td>
    </tr>
	<tr>
		<td  style="vertical-align:top;">4.</td>
		<td>Setelah melakukan pembayaran, silahkan tunggu sampai pembayaran kamu dinyatakan valid ya.</td>
	</tr> 
	<tr>
		<td  style="vertical-align:top;">4.</td>
		<td>Jangan sampai kelupaan untuk bayar token ya, karena ada batas waktu pembayaran.</td>
	</tr> 
	<tr>
		<td colspan="2"><br><br>Kuy, kami tunggu ya kamu di Telkom Kampus Surabaya. <br> </td>
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
