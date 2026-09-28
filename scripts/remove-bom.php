<?php
// Remove UTF-8 BOM from PHP files

$files = [
    'routes/web.php',
    'resources/views/admin/activity-logs/index.blade.php',
    'resources/views/reports/attendance.blade.php',
];

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "SKIP (not found): $file\n";
        continue;
    }

    $content = file_get_contents($file);
    $bom = "\xEF\xBB\xBF";

    if (substr($content, 0, 3) === $bom) {
        file_put_contents($file, substr($content, 3));
        echo "FIXED: $file\n";
    } else {
        echo "OK:    $file (no BOM)\n";
    }
}