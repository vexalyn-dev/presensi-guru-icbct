{{--
    Fallback: redirect ke halaman utama presensi kelas.
    Shared space sekarang ditangani sebagai bottom sheet di index.blade.php.
--}}
@php
    $classroomId = request('classroom_id');
    $mode        = request('mode', 'in');
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redirect...</title>
    <script>
        // Redirect ke halaman utama — shared space sekarang di bottom sheet
        window.location.replace('{{ route("teacher.class-attendance") }}');
    </script>
</head>
<body></body>
</html>
