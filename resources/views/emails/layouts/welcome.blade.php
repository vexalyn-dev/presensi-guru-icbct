<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #F1F5F9;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            background: rgba(250, 204, 21, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .header h1 {
            color: #FACC15;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
            color: #1E293B;
            line-height: 1.6;
        }
        .welcome-text {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #0F172A;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            color: white !important;
            padding: 14px 40px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .info-box {
            background: #F8FAFC;
            border-left: 4px solid #FACC15;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
        }
        .footer {
            background: #F1F5F9;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #64748B;
        }
        @media (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }
            .content {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">
                @if($appLogo)
                    <img src="{{ $appLogo }}" alt="Logo" style="width: 60px; height: 60px; object-fit: contain;">
                @endif
            </div>
            <h1>{{ $appName }}</h1>
        </div>
        
        <div class="content">
            <div class="welcome-text">
                Selamat Datang, {{ $user->name }}! 👋
            </div>
            
            <p>Terima kasih telah bergabung dengan <strong>{{ $appName }}</strong>. Akun Anda telah berhasil dibuat dan Anda sekarang dapat mengakses sistem presensi digital kami.</p>
            
            <div style="text-align: center;">
                <a href="{{ url('/dashboard') }}" class="button">Mulai Menggunakan</a>
            </div>
            
            <div class="info-box">
                <strong>Informasi Akun Anda:</strong><br>
                📧 Email: {{ $user->email }}<br>
                👤 Role: {{ ucfirst($user->role) }}<br>
                📅 Bergabung: {{ $user->created_at->format('d F Y') }}
            </div>
            
            <h3 style="margin-top: 30px; color: #0F172A;">Langkah Selanjutnya:</h3>
            <ol style="padding-left: 20px;">
                <li>Lengkapi profil Anda di menu <strong>Profil</strong></li>
                <li>Periksa <strong>Jadwal Mengajar</strong> Anda</li>
                <li>Mulai gunakan fitur <strong>Presensi Kelas</strong> dengan scan QR Code</li>
            </ol>
            
            <p style="margin-top: 30px;">Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi tim support kami.</p>
            
            <p style="margin-top: 20px;">Terima kasih,<br><strong>Tim {{ $appName }}</strong></p>
        </div>
        
        <div class="footer">
            © {{ date('Y') }} {{ $appName }}. All rights reserved.<br>
            Jika Anda tidak merasa mendaftar, abaikan email ini.
        </div>
    </div>
</body>
</html>