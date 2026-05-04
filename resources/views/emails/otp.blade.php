<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Reset Password</title>
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind -->
    <style>
        /* Reset & Base Styles */
        body {
            font-family: 'Inter', Arial, sans-serif !important;
            background-color: #f8fafc;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Header Styles */
        .header {
            background: linear-gradient(135deg, #033e87 0%, #0056b3 100%);
            padding: 40px 20px;
            text-align: center;
        }

        .header img {
            width: 80px;
            height: 80px;
            margin-bottom: 16px;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .header p {
            color: #e0e7ff;
            margin: 8px 0 0 0;
            font-size: 14px;
        }

        /* Content Styles */
        .content {
            padding: 40px 30px;
        }

        .content p {
            color: #475569;
            font-size: 15px;
            margin: 0 0 20px 0;
        }

        /* OTP Box */
        .otp-box {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #0ea5e9;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }

        .otp-label {
            color: #0369a1;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        .otp-code {
            font-size: 48px;
            font-weight: 800;
            color: #033e87;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 0;
            word-break: break-all;
        }

        .otp-expiry {
            color: #64748b;
            font-size: 13px;
            margin-top: 12px;
        }

        /* Warning Box */
        .warning {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 16px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .warning p {
            color: #92400e;
            font-size: 13px;
            margin: 0;
        }

        /* Footer */
        .footer {
            background-color: #f8fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .footer p {
            color: #94a3b8;
            font-size: 12px;
            margin: 0;
            line-height: 1.6;
        }

        .footer a {
            color: #033e87;
            text-decoration: none;
            font-weight: 600;
        }

        /* ========================================
           RESPONSIVE BREAKPOINTS - 4 DEVICES
           ======================================== */

        /* 📱 Mobile: < 480px */
        @media screen and (max-width: 480px) {
            body {
                padding: 10px;
            }

            .container {
                border-radius: 12px;
            }

            .header {
                padding: 30px 15px;
            }

            .header h1 {
                font-size: 20px;
            }

            .header p {
                font-size: 13px;
            }

            .content {
                padding: 25px 20px;
            }

            .content p {
                font-size: 14px;
            }

            .otp-box {
                padding: 20px 15px;
                margin: 20px 0;
            }

            .otp-label {
                font-size: 12px;
            }

            .otp-code {
                font-size: 36px;
                letter-spacing: 4px;
            }

            .otp-expiry {
                font-size: 12px;
            }

            .warning {
                padding: 12px;
            }

            .warning p {
                font-size: 12px;
            }

            .footer {
                padding: 20px 15px;
            }

            .footer p {
                font-size: 11px;
            }
        }

        /* 📱 Tablet Portrait: 481px - 768px */
        @media screen and (min-width: 481px) and (max-width: 768px) {
            body {
                padding: 15px;
            }

            .header {
                padding: 35px 20px;
            }

            .header h1 {
                font-size: 22px;
            }

            .content {
                padding: 30px 25px;
            }

            .otp-box {
                padding: 25px 20px;
            }

            .otp-code {
                font-size: 42px;
                letter-spacing: 6px;
            }

            .footer {
                padding: 25px 20px;
            }
        }

        /* 💻 Desktop: 769px - 1024px */
        @media screen and (min-width: 769px) and (max-width: 1024px) {
            .container {
                max-width: 580px;
            }

            .content {
                padding: 35px 28px;
            }

            .otp-code {
                font-size: 46px;
            }
        }

        /* 🖥️ Large Desktop: > 1024px */
        @media screen and (min-width: 1025px) {
            .container {
                max-width: 600px;
            }

            /* Styles default sudah optimal untuk large desktop */
        }

        /* 🔧 Email Client Fixes */
        @media screen and (max-width: 600px) {
            .otp-code {
                font-size: 32px !important;
                letter-spacing: 2px !important;
            }
        }

        /* Dark Mode Support (Optional) */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #0f172a;
            }

            .container {
                background-color: #1e293b;
            }

            .content p,
            .otp-expiry,
            .footer p {
                color: #cbd5e1;
            }

            .otp-label {
                color: #7dd3fc;
            }

            .otp-code {
                color: #93c5fd;
            }

            .warning {
                background-color: #78350f;
                border-left-color: #fbbf24;
            }

            .warning p {
                color: #fde68a;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-lock"></i> Reset Password</h1>
            <p>BPJS Keliling</p>
        </div>


        <div class="content">
            <p>Halo,</p>
            <p>Anda telah meminta untuk mereset password akun BPJS Keliling Anda. Gunakan kode OTP di bawah ini untuk melanjutkan proses reset password:</p>

            <div class="otp-box">
                <div class="otp-label">Kode OTP Anda</div>
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-expiry">⏱️ Berlaku selama 10 menit</div>
            </div>

            <div class="warning">
                <p>
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Perhatian:</strong>
                    Jangan bagikan kode OTP ini kepada siapapun, termasuk petugas BPJS.
                    Kami tidak akan pernah meminta kode OTP Anda melalui telepon, SMS, atau email.
                </p>
            </div>


            <p>Jika Anda tidak meminta reset password, abaikan email ini. Akun Anda tetap aman.</p>
        </div>

        <div class="footer">
            <p>
                Email ini dikirim secara otomatis oleh sistem BPJS Keliling.<br>
                Jika ada pertanyaan, hubungi admin di <a href="https://wa.me/6282137993903">WhatsApp</a>
            </p>
            <p style="margin-top: 16px;">
                &copy; {{ date('Y') }} BPJS Keliling. Sistem Informasi Pelayanan Terpadu.
            </p>

        </div>
    </div>
</body>

</html>