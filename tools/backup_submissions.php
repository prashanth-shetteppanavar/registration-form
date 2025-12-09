<?php
// CLI script to backup data/submissions.json into data/backups with a timestamp.
// Usage: php tools/backup_submissions.php
if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Run from CLI only.\n");
    exit(1);
}

$dataDir = __DIR__ . '/../data';
$subFile = $dataDir . '/submissions.json';
$backDir = $dataDir . '/backups';
if (!is_dir($dataDir)) {
    fwrite(STDOUT, "No data directory; nothing to backup.\n");
    exit(0);
}
if (!file_exists($subFile)) {
    fwrite(STDOUT, "No submissions file to backup.\n");
    exit(0);
}
if (!is_dir($backDir)) mkdir($backDir, 0755, true);
$ts = date('Ymd_His');
$dst = $backDir . "/submissions_{$ts}.json";
if (!rename($subFile, $dst)) {
    fwrite(STDERR, "Failed to move submissions file to backups.\n");
    exit(2);
}
fwrite(STDOUT, "Backed up to: $dst\n");

// Keep last 10 backups
$files = glob($backDir . '/submissions_*.json');
usort($files, function($a,$b){return filemtime($b) - filemtime($a);});
if (count($files) > 10) {
    $remove = array_slice($files, 10);
    foreach ($remove as $f) @unlink($f);
}

// Recreate empty submissions.json
file_put_contents($subFile, json_encode([], JSON_PRETTY_PRINT));
fwrite(STDOUT, "Created new empty submissions.json\n");
exit(0);
