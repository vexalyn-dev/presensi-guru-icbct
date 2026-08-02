<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="id">
<head>
<title>{{ config('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
/* Reset */
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    background-color: #F0F4F8;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    color: #1E293B;
    width: 100% !important;
    height: 100%;
    margin: 0;
    padding: 0;
}

.email-wrapper {
    background-color: #F0F4F8;
    padding: 40px 16px;
    width: 100%;
}

.email-container {
    max-width: 580px;
    margin: 0 auto;
    background: #FFFFFF;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(15,23,42,0.10),
                0 1px 4px rgba(15,23,42,0.06);
}

/* Header */
.email-header {
    background: linear-gradient(150deg, #080F1E 0%, #0F172A 60%, #162035 100%);
    padding: 36px 40px 32px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.email-header::before {
    content: '';
    position: absolute; top: -60px; right: -60px;
    width: 220px; height: 220px; border-radius: 50%;
    background: radial-gradient(circle, rgba(250,204,21,0.10) 0%, transparent 65%);
}
.email-header::after {
    content: '';
    position: absolute; bottom: -50px; left: -50px;
    width: 180px; height: 180px; border-radius: 50%;
    background: radial-gradient(circle, rgba(99,102,241,0.07) 0%, transparent 65%);
}
.header-logo-box {
    width: 80px; height: 80px;
    margin: 0 auto 16px;
    border-radius: 20px;
    background: rgba(255,255,255,0.05);
    border: 1.5px solid rgba(250,204,21,0.28);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    position: relative; z-index: 1;
}
.header-logo-box img {
    width: 76%; height: 76%;
    object-fit: contain;
}
.header-app-name {
    font-size: 15px;
    font-weight: 700;
    color: #FFFFFF;
    margin-bottom: 4px;
    position: relative; z-index: 1;
    letter-spacing: -0.1px;
}
.header-tagline {
    font-size: 12px;
    color: rgba(255,255,255,0.42);
    position: relative; z-index: 1;
}

/* Body */
.email-body {
    padding: 36px 40px 32px;
}

.greeting {
    font-size: 22px;
    font-weight: 800;
    color: #0F172A;
    margin-bottom: 12px;
    letter-spacing: -0.4px;
}

.body-text {
    font-size: 15px;
    line-height: 1.7;
    color: #475569;
    margin-bottom: 16px;
}

/* Button */
.btn-wrapper {
    text-align: center;
    margin: 28px 0;
}
.btn-reset {
    display: inline-block;
    background-color: #0F172A;
    color: #FFFFFF !important;
    text-decoration: none;
    font-size: 15px;
    font-weight: 700;
    padding: 14px 36px;
    border-radius: 12px;
    letter-spacing: 0.02em;
    mso-padding-alt: 14px 36px;
    box-shadow: 0 4px 16px rgba(15,23,42,0.28);
}

/* Divider */
.divider {
    height: 1px;
    background: #F1F5F9;
    margin: 24px 0;
}

/* Info box */
.info-box {
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-left: 3px solid #FACC15;
    border-radius: 10px;
    padding: 14px 16px;
    font-size: 13px;
    color: #64748B;
    line-height: 1.6;
    margin-bottom: 20px;
}
.info-box strong {
    color: #0F172A;
    font-weight: 600;
}

/* Subcopy / fallback URL */
.subcopy {
    background: #F8FAFC;
    border-top: 1px solid #F1F5F9;
    padding: 20px 40px;
}
.subcopy p {
    font-size: 12px;
    color: #94A3B8;
    line-height: 1.65;
    margin-bottom: 6px;
}
.subcopy a {
    color: #0F172A;
    word-break: break-all;
    font-size: 11.5px;
}

/* Footer */
.email-footer {
    background: #F8FAFC;
    border-top: 1px solid #F1F5F9;
    padding: 20px 40px;
    text-align: center;
}
.footer-text {
    font-size: 12px;
    color: #94A3B8;
    line-height: 1.6;
}
.footer-text a {
    color: #64748B;
    text-decoration: none;
}

@media only screen and (max-width: 600px) {
    .email-container { border-radius: 16px; }
    .email-header { padding: 28px 24px 24px; }
    .header-logo-box { width: 68px; height: 68px; }
    .email-body { padding: 28px 24px 24px; }
    .greeting { font-size: 20px; }
    .body-text { font-size: 14px; }
    .btn-reset { padding: 13px 28px; font-size: 14px; }
    .subcopy { padding: 16px 24px; }
    .email-footer { padding: 16px 24px; }
}
</style>
{!! $head ?? '' !!}
</head>
<body>
<div class="email-wrapper">
    <div class="email-container">

        {!! $header ?? '' !!}

        <div class="email-body">
            {!! Illuminate\Mail\Markdown::parse($slot) !!}
            {!! $subcopy ?? '' !!}
        </div>

        {!! $footer ?? '' !!}

    </div>
</div>
</body>
</html>
