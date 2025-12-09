<?php
session_start();
$config = [];
if (file_exists(__DIR__ . '/admin_config.php')) {
    $config = include __DIR__ . '/admin_config.php';
}
$adminUser = $config['user'] ?? 'admin';
$adminHash = $config['pass_hash'] ?? '';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    if ($user === $adminUser && password_verify($pass, $adminHash)) {
        // authenticated
        $_SESSION['admin_user'] = $user;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Login</title>
  <style>body{font-family:Arial,Helvetica,sans-serif;background:#111;color:#eee;padding:20px} .login{max-width:360px;margin:40px auto;background:#171717;padding:20px;border-radius:8px} label{display:block;margin-top:10px} input{width:100%;padding:8px;margin-top:6px;border-radius:4px;border:1px solid #333;background:#000;color:#fff} button{margin-top:12px;padding:10px 14px;border-radius:4px;border:none;background:#e50914;color:#fff;cursor:pointer}</style>
</head>
<body>
  <div class="login">
    <h2>Admin Login</h2>
    <?php if ($error): ?><div style="color:#ff8080;margin-bottom:8px"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post" action="">
      <label>Username
        <input name="username" required>
      </label>
      <label>Password
        <input name="password" type="password" required>
      </label>
      <button type="submit">Sign in</button>
    </form>
  </div>
</body>
</html>
