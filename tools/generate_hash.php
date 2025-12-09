<?php
// CLI-only helper: generate a password hash for admin_config.php
// Usage (PowerShell):
// php tools\generate_hash.php "YourNewPassword"

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from the command line.\n");
    exit(1);
}

if ($argc < 2) {
    fwrite(STDERR, "Usage: php tools/generate_hash.php \"YourNewPassword\"\n");
    exit(2);
}

$password = $argv[1];
// Generate bcrypt (or default algorithm) hash
$hash = password_hash($password, PASSWORD_DEFAULT);
if ($hash === false) {
    fwrite(STDERR, "Failed to generate password hash.\n");
    exit(3);
}

echo $hash . "\n";
