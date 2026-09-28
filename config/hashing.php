<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    | bcrypt dipilih karena support cost factor yang dapat dikonfigurasi.
    */

    'driver' => 'bcrypt',

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options — rounds 12 (OWASP minimum recommendation)
    |--------------------------------------------------------------------------
    | Default Laravel adalah 10. Dinaikkan ke 12 untuk memperlambat brute-force.
    | Jangan terlalu tinggi (> 14) karena akan memperlambat login secara signifikan.
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
        'verify' => env('HASH_VERIFY', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Argon Options (tidak digunakan, tapi wajib ada agar config lengkap)
    |--------------------------------------------------------------------------
    */

    'argon' => [
        'memory' => env('ARGON_MEMORY', 65536),
        'threads' => env('ARGON_THREADS', 1),
        'time' => env('ARGON_TIME', 4),
        'verify' => env('HASH_VERIFY', true),
    ],

];
