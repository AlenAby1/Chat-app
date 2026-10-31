<?php
require __DIR__ . '/db.php';
if (!empty($_SESSION['uid'])) { header('Location: index.php'); exit; }

$error = '';
$username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (!csrf_ok()) {
        $error = 'Session expired. Please try again.';
    } elseif (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
        $error = 'Username must be 3-30 letters, numbers or underscores.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $chk = db()->prepare('SELECT id FROM users WHERE username = ?');
        $chk->execute([$username]);
        if ($chk->fetch()) {
            $error = 'That username is already taken.';
        } else {
            $ins = db()->prepare('INSERT INTO users (username, password_hash, created_at, last_seen) VALUES (?, ?, ?, ?)');
            $ins->execute([$username, password_hash($password, PASSWORD_DEFAULT), time(), time()]);
            session_regenerate_id(true);
            $_SESSION['uid'] = (int) db()->lastInsertId();
            $_SESSION['username'] = $username;
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | ChatApp</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth">
  <main class="card">
    <h1>Create account</h1>
    <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
    <form method="post" autocomplete="off">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" value="<?= e($username) ?>" required maxlength="30">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required minlength="6">
      <label for="confirm">Confirm password</label>
      <input type="password" id="confirm" name="confirm" required minlength="6">
      <button type="submit">Register</button>
    </form>
    <p class="alt">Already have an account? <a href="login.php">Log in</a></p>
  </main>
</body>
</html>
