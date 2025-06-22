<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password Open Library</title>
</head>

<body style="background-color: #f8f9fa; padding: 20px; font-family: Arial, sans-serif;">
    <div style="max-width: 600px; margin: auto; background: #ffffff; padding: 20px; border-radius: 10px;
              box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); text-align: center; border: 1px solid #ddd;">

        <div style="background-color: #b30000; padding: 15px; border-radius: 10px 10px 0 0;">
            <img src="https://openlibrary.telkomuniversity.ac.id/images/logo_openlibrary.png" alt="OpenLibrary"
                style="height: 50px; vertical-align: middle;">
            <img src="https://openlibrary.telkomuniversity.ac.id/images/telkom-university.png" alt="Telkom University"
                style="height: 50px; vertical-align: middle;">
        </div>

        <div style="padding: 20px;">
            <h3 style="color: #333;">🔑 Reset Password</h3>
            <p style="color: #555; font-size: 16px;">
                Halo, <strong>{{ $nama_member }}</strong>!<br>
                Kami menerima permintaan untuk mereset password akun Anda. Silakan klik tombol di bawah ini
                untuk mengatur ulang password Anda.
            </p>

            <a href="{{ $resetLink }}" style="display: inline-block; padding: 12px 25px; font-size: 16px; color: white;
                background: #b30000; text-decoration: none; border-radius: 5px; font-weight: bold;
                box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.15);">
                Reset Password
            </a>

            <p style="color: #555; font-size: 14px; margin-top: 20px;">
                Jika Anda tidak meminta reset password, abaikan email ini.
            </p>
        </div>

        <div style="background: #f8f9fa; padding: 10px; font-size: 12px; color: #777; border-radius: 0 0 10px 10px;">
            &copy; 2025 OpenLibrary - Telkom University. All Rights Reserved.
        </div>

    </div>
</body>

</html>