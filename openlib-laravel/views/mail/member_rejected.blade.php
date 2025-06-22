<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keanggotaan Anda Ditolak</title>
</head>

<body style="background-color: #f8f9fa; padding: 20px; font-family: Arial, sans-serif;">
    <div
        style="max-width: 600px; margin: auto; background: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); text-align: center; border: 1px solid #ddd;">

        <!-- Header dengan Logo -->
        <div style="background-color: #b30000; padding: 15px; border-radius: 10px 10px 0 0;">
            <img src="https://openlibrary.telkomuniversity.ac.id/images/logo_openlibrary.png" alt="OpenLibrary"
                style="height: 50px; vertical-align: middle;">
            <img src="https://openlibrary.telkomuniversity.ac.id/images/telkom-university.png" alt="Telkom University"
                style="height: 50px; vertical-align: middle;">
        </div>

        <!-- Konten -->
        <div style="padding: 20px;">
            <h3 style="color: #b30000;">❌ Keanggotaan Anda Ditolak</h3>
            <p style="color: #555; font-size: 16px;">
                Halo, <strong>{{ $nama_member }}</strong>,<br>
                Mohon maaf, permohonan keanggotaan OpenLibrary Telkom University Anda tidak dapat disetujui.
            </p>

            <p style="font-size: 14px; font-weight: bold; color: #333;">Alasan Penolakan:</p>
            <div
                style="background-color: #f8d7da; padding: 10px; border-radius: 5px; font-size: 14px; color: #721c24; border-left: 5px solid #b30000; font-style: italic;">
                <p style="margin: 0;">{{ $reason }}</p>
            </div>

            <p style="color: #555; font-size: 14px; margin-top: 20px;">
                Jika Anda memiliki pertanyaan lebih lanjut, silakan hubungi tim dukungan kami.
            </p>
        </div>

        <!-- Footer -->
        <div style="background: #f8f9fa; padding: 10px; font-size: 12px; color: #777; border-radius: 0 0 10px 10px;">
            &copy; 2024 OpenLibrary - Telkom University. All Rights Reserved.
        </div>

    </div>
</body>

</html>