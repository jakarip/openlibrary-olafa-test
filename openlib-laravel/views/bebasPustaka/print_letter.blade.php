<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Open Library - Universitas Telkom</title>
    <style type="text/css">
        #sf_admin_container {
            width: 600px;
            margin: 5px auto;
            font-family: serif;
            font-size: 12pt;
        }

        #sf_admin_container .report_header {
            font-weight: bold;
            text-align: center;
            margin-bottom: 10pt;
        }

        #sf_admin_container .report_header_separator {
            border-top: 1px solid #000;
            margin-bottom: 10pt;
        }

        #sf_admin_container .report_content_center {
            text-align: center;
            margin-top: 5pt;
            margin-bottom: 10pt;
        }

        #sf_admin_container .report_content {
            text-align: left;
            margin-top: 5pt;
            margin-bottom: 10pt;
        }

        #sf_admin_container .report_content table td {
            text-align: left;
            vertical-align: top;
        }
    </style>
</head>

<body style="margin: 0;">
    <div id="report_content">
        <div id="sf_admin_container">
            <div class="report_header">
                OPEN LIBRARY - TELKOM UNIVERSITY
            </div>
            <div class="report_content_center">
                Bandung Technoplex<br />
                Jl. Telekomunikasi 1 - Terusan Buah Batu Bandung<br />
                Website : openlibrary.telkomuniversity.ac.id<br />
                E-mail: library@telkomuniversity.ac.id
            </div>
            <div class="report_header_separator"></div>
            <div class="report_content_center">
                <b>KETERANGAN BEBAS KEWAJIBAN</b>
                <br />NOMOR: {{ $dt->letter_number }}
            </div>
            <div class="report_content">
                Yang bertanda tangan di bawah ini, Kepala Bagian Perpustakaan Telkom University :
            </div>
            <div class="report_content" style="padding-left: 20px;">
                <table>
                    <tr>
                        <td width="150">Nama</td>
                        <td width="5">:</td>
                        <td>{{ $dt->name }}</td>
                    </tr>
                    <tr>
                        <td>Nomor Telp</td>
                        <td>:</td>
                        <td>{{ $dt->master_data_mobile_phone }}</td>
                    </tr>
                    <tr>
                        <td>Nomor Registrasi</td>
                        <td>:</td>
                        <td>{{ $dt->registration_number }}</td>
                    </tr>
                    <tr>
                        <td>Jurusan/Departemen</td>
                        <td>:</td>
                        <td>{{ $dt->NAMA_PRODI }}</td>
                    </tr>
                    <tr>
                        <td>Username SSO</td>
                        <td>:</td>
                        <td>{{ $dt->member_number }}</td>
                    </tr>
                </table>
            </div>
            <div class="report_content">
                Mahasiswa yang namanya tersebut di atas telah bebas dari peminjaman bahan pustaka dan telah menyerahkan
                Buku Sumbangan dengan tahun terbit sesuai ketentuan yang berlaku ke Perpustakaan Telkom University. Buku
                yang disumbangkan:
            </div>
            <div style="padding-left: 20px;">
                {{ $dt->donated_item_title }}, {{ $dt->donated_item_author }}
            </div>
            <div class="report_content">&nbsp;</div>
            <div class="report_content">
                Bandung, {{ date('d') }} {{ date('F') }}, {{ date('Y') }}<br />
                AN. Kepala Bagian Perpustakaan Telkom University<br /><br />
                <img src="" width="30%"><br>
                ________________________
            </div>
        </div>
    </div>
    <script>
        window.onload = function () {
            window.print();
        };
    </script>
</body>

</html>