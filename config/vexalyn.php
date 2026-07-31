<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Vexalyn Dev Center Configuration
    |--------------------------------------------------------------------------
    | Konfigurasi API untuk Pusat Bantuan (Support Center)
    | Set nilai-nilai ini di file .env
    */

    'api_url'        => env('VEXALYN_API_URL',        'https://api.vexalyn.dev/v1'),
    'api_key'        => env('VEXALYN_API_KEY',         ''),
    'project_id'     => env('VEXALYN_PROJECT_ID',      'icb-ct-absensi-guru'),
    'project_name'   => env('VEXALYN_PROJECT_NAME',    'ICB CT - Absensi Guru'),
    'webhook_secret' => env('VEXALYN_WEBHOOK_SECRET',  ''),

    /*
    |--------------------------------------------------------------------------
    | Feature Toggle
    |--------------------------------------------------------------------------
    | true  = fully enabled (laporan dikirim ke Vexalyn)
    | false = UI tampil, tapi tombol kirim menampilkan modal "dalam pengembangan"
    */
    'enabled' => env('SUPPORT_CENTER_ENABLED', false),
];
