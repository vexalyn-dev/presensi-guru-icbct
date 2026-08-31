<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseBackupController extends Controller
{
    private string $backupDir;
    private array $excludeTables = ['jobs', 'failed_jobs', 'cache', 'sessions'];

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0775, true);
        }
    }

    public function index(Request $request)
    {
        $backups = $this->listBackups();
        $stats = [
            'total'      => count($backups),
            'total_size' => $this->totalSize($backups),
            'latest'     => $backups[0] ?? null,
        ];

        return view('admin.database-backup.index', compact('backups', 'stats'));
    }

    public function create(Request $request)
    {
        $includeAll = (bool) $request->input('full', 0);
        $backups    = $this->listBackups();

        // Cegah double click — backup terakhir < 60 detik
        $lastBackup = $backups[0] ?? null;
        if ($lastBackup && ($lastBackup['age_seconds'] ?? 99999) < 60) {
            return back()->with('warning', 'Backup terakhir dibuat kurang dari 1 menit yang lalu. Tunggu sebentar.');
        }

        $filename = 'backup_' . now()->format('Ymd_His') . '.sql';
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        try {
            if ($includeAll) {
                $tables = DB::select('SHOW TABLES');
                $dbName = config('database.default');
                $tableList = array_column($tables, "Tables_in_{$dbName}");
            } else {
                $tableList = $this->getTablesWithoutExcludes();
            }

            $dbConfig = config('database.connections.' . config('database.default'));
            $host     = $dbConfig['host'] ?? '127.0.0.1';
            $user     = $dbConfig['username'];
            $pass     = $dbConfig['password'] ?? '';
            $dbname   = $dbConfig['database'];

            $escapedPass = $pass !== '' ? "-p" . escapeshellarg($pass) : '';

            $cmd = sprintf(
                'mysqldump --no-tablespaces -h%s -u%s %s %s --single-transaction --quick --lock-tables=false %s > "%s"',
                escapeshellarg($host),
                escapeshellarg($user),
                $escapedPass,
                escapeshellarg($dbname),
                implode(' ', array_map('escapeshellarg', $tableList)),
                escapeshellarg($filepath)
            );

            $output  = [];
            $exitCode = null;
            exec($cmd . ' 2>&1', $output, $exitCode);

            if ($exitCode !== 0) {
                Log::error('Backup DB gagal: ' . implode("\n", $output));
                @unlink($filepath);
                return back()->with('error', 'Gagal membuat backup: ' . implode("\n", array_slice($output, -3)));
            }

            $fileSize = filesize($filepath);
            if ($fileSize === false || $fileSize < 100) {
                @unlink($filepath);
                return back()->with('error', 'File backup kosong atau tidak valid.');
            }

            // Kompres ke .sql.gz
            $gzFile = $filepath . '.gz';
            $gz = gzopen($gzFile, 'wb9');
            gzwrite($gz, file_get_contents($filepath));
            gzclose($gz);
            @unlink($filepath);

            $this->purgeOldBackups(5);

            return back()->with('success', 'Backup database berhasil dibuat!');
        } catch (\Throwable $e) {
            Log::error('Backup exception: ' . $e->getMessage());
            @unlink($filepath);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function download(string $filename)
    {
        $path = $this->backupDir . DIRECTORY_SEPARATOR . $filename;
        if (!file_exists($path)) {
            abort(404, 'File backup tidak ditemukan.');
        }
        return response()->download($path, $filename);
    }

    public function destroy(string $filename, Request $request)
    {
        $path = $this->backupDir . DIRECTORY_SEPARATOR . $filename;
        if (!file_exists($path)) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan.'], 404);
        }
        unlink($path);
        return response()->json(['success' => true]);
    }

    private function getTablesWithoutExcludes(): array
    {
        $allTables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.' . config('database.default'));
        $tableKey = 'Tables_in_' . $dbName['database'];

        return array_values(array_filter(
            array_map(fn($t) => $t->{$tableKey}, $allTables),
            fn($t) => !in_array($t, $this->excludeTables)
        ));
    }

    private function listBackups(): array
    {
        $files = [];
        $handle = opendir($this->backupDir);
        if ($handle) {
            while (($file = readdir($handle)) !== false) {
                if (str_ends_with($file, '.sql.gz') || str_ends_with($file, '.sql')) {
                    $fullPath = $this->backupDir . DIRECTORY_SEPARATOR . $file;
                    $files[] = [
                        'filename'     => $file,
                        'size_bytes'   => filesize($fullPath),
                        'size_human'   => $this->humanSize(filesize($fullPath)),
                        'created_at'   => date('Y-m-d H:i:s', filemtime($fullPath)),
                        'age_seconds'  => time() - filemtime($fullPath),
                    ];
                }
            }
            closedir($handle);
        }

        usort($files, fn($a, $b) => $b['age_seconds'] <=> $a['age_seconds']);
        return $files;
    }

    private function totalSize(array $backups): string
    {
        return $this->humanSize(array_sum(array_column($backups, 'size_bytes')));
    }

    private function humanSize(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = (int) floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
    }

    private function purgeOldBackups(int $keep): void
    {
        $backups = $this->listBackups();
        if (count($backups) <= $keep) return;
        foreach (array_slice($backups, $keep) as $old) {
            $path = $this->backupDir . DIRECTORY_SEPARATOR . $old['filename'];
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }
}
