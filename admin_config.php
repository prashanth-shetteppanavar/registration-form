<?php
// Admin config for `admin.php`.
// Secure defaults: username 'admin', password 'admin123' (hashed).
// Change 'user' and 'pass_hash' values before publishing.
return [
    'user' => 'admin',
    // Password hash (generated). If you want a different password, run `php tools\generate_hash.php "NewPassword"` and replace this value.
    'pass_hash' => '$2y$10$hcaIT7U50uiH9Ur3qOs/qebffflhfXlQID8YovuiZOYfZcE1OjxJe',
    // Optional IP allowlist (empty array = allow all). Use strings like '127.0.0.1' or '::1'.
    'ip_allow' => ['127.0.0.1', '::1']
];
