<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 520px; margin: 40px auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1e3a8a, #1d4ed8); padding: 32px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 24px; }
        .header p { color: rgba(255,255,255,0.7); margin: 8px 0 0; font-size: 14px; }
        .body { padding: 32px; }
        .body p { color: #4b5563; font-size: 15px; line-height: 1.6; }
        .btn { display: block; text-align: center; background: #1d4ed8; color: white !important; text-decoration: none; padding: 16px 32px; border-radius: 12px; font-weight: bold; font-size: 16px; margin: 24px 0; }
        .note { background: #fef9c3; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 16px; font-size: 13px; color: #92400e; }
        .footer { text-align: center; padding: 20px; color: #9ca3af; font-size: 12px; border-top: 1px solid #f3f4f6; }
    </style>
</head>
<body>
<div class="container">

    <div class="header">
        <h1>⚖️ Lapau Ancak</h1>
        <p>Platform Lelang Barang Rampasan Negara</p>
    </div>

    <div class="body">
        <p>Halo, <strong>{{ $namaPembeli }}</strong>!</p>
        <p>Anda telah meminta akses untuk mengikuti lelang. Klik tombol di bawah untuk verifikasi identitas Anda:</p>

        <a href="{{ $magicUrl }}" class="btn">
            🔑 Verifikasi & Masuk ke Lelang
        </a>

        <div class="note">
            ⚠️ Link ini hanya berlaku hingga <strong>23:59 hari ini</strong>. Setelah itu Anda perlu meminta link baru.
        </div>

        <p style="margin-top: 20px; font-size: 13px; color: #9ca3af;">
            Jika Anda tidak merasa meminta link ini, abaikan email ini.
        </p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} Lapau Ancak. All rights reserved.
    </div>

</div>
</body>
</html>