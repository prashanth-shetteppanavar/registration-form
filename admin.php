<?php
$config = [];
if (file_exists(__DIR__ . '/admin_config.php')) {
    $config = include __DIR__ . '/admin_config.php';
}

session_start();

// Session-based auth with optional IP allowlist. If not authenticated, redirect to login.
$adminUser = $config['user'] ?? 'admin';
$ipAllow = $config['ip_allow'] ?? [];

// IP allowlist check (if non-empty)
if (!empty($ipAllow) && isset($_SERVER['REMOTE_ADDR'])) {
    $remote = $_SERVER['REMOTE_ADDR'];
    if (!in_array($remote, $ipAllow, true)) {
        header('HTTP/1.0 403 Forbidden');
        echo 'Access denied.';
        exit;
    }
}

// Allow session auth or fallback to HTTP Basic if session not present
if (empty($_SESSION['admin_user']) || $_SESSION['admin_user'] !== $adminUser) {
    // If HTTP Basic credentials are provided, accept them (for scripts/tools)
    if (isset($_SERVER['PHP_AUTH_USER'])) {
        $config = include __DIR__ . '/admin_config.php';
        $adminHash = $config['pass_hash'] ?? '';
        if (!hash_equals($adminUser, $_SERVER['PHP_AUTH_USER']) || !password_verify($_SERVER['PHP_AUTH_PW'] ?? '', $adminHash)) {
            header('HTTP/1.0 401 Unauthorized');
            echo 'Invalid credentials.';
            exit;
        }
        // allow access via Basic auth
    } else {
        // redirect to login form
        header('Location: admin_login.php');
        exit;
    }
}

// Simple admin viewer for submissions.json (stored in protected data/ folder)
$submissions_file = __DIR__ . '/data/submissions.json';
$submissions = [];
if (file_exists($submissions_file)) {
    $raw = file_get_contents($submissions_file);
    $submissions = json_decode($raw, true) ?: [];
}
function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Submissions Admin</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;margin:20px;background:#111;color:#eee}
        table{width:100%;border-collapse:collapse;margin-top:12px}
        th,td{padding:8px;border:1px solid #222;text-align:left;font-size:0.9rem}
        th{background:#1f1f1f}
        .actions a{margin-right:8px;color:#9ad}
        .badge{background:#222;padding:3px 8px;border-radius:12px}
    </style>
</head>
<body>
    <h1>Submissions (<?php echo count($submissions); ?>)</h1>
    <p>Raw file: <code><?php echo h($submissions_file); ?></code></p>
    <?php if (empty($submissions)): ?>
        <p>No submissions yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Timestamp</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Country</th>
                    <th>State</th>
                    <th>Skills</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($submissions as $i => $s): ?>
                <tr>
                    <td><?php echo $i+1; ?></td>
                    <td><?php echo h($s['timestamp'] ?? ''); ?></td>
                    <td><?php echo h(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? '')); ?></td>
                    <td><?php echo h($s['email'] ?? ''); ?></td>
                    <td><?php echo h($s['phone'] ?? ''); ?></td>
                    <td><?php echo h($s['country'] ?? ''); ?></td>
                    <td><?php echo h($s['state'] ?? ''); ?></td>
                    <td><?php echo isset($s['skills']) ? h(implode(', ', $s['skills'])) : ''; ?></td>
                    <td class="actions">
                        <a href="admin.php?view=<?php echo $i; ?>">View</a>
                        <a href="admin.php?download=<?php echo $i; ?>">Download JSON</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

<?php
if (isset($_GET['view'])) {
    $idx = (int)$_GET['view'];
    if (isset($submissions[$idx])) {
        echo '<h2>Submission Details</h2><pre style="background:#000;padding:12px;border-radius:6px">'.h(json_encode($submissions[$idx], JSON_PRETTY_PRINT)).'</pre>';
    }
}

if (isset($_GET['download'])) {
    $idx = (int)$_GET['download'];
    if (isset($submissions[$idx])) {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="submission_'.$idx.'.json"');
        echo json_encode($submissions[$idx], JSON_PRETTY_PRINT);
        exit;
    }
}
?>
</body>
</html>
