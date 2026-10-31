<?php
require __DIR__ . '/db.php';
if (!empty($_SESSION['uid'])) { header('Location: index.php'); exit; }

$error = '';
$username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!csrf_ok()) {
        $error = 'Session expired. Please try again.';
    } else {
        $q = db()->prepare('SELECT id, username, password_hash FROM users WHERE username = ?');
        $q->execute([$username]);
        $user = $q->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['uid'] = (int) $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit;
        }
        $error = 'Wrong username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | ChatApp</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth">
  <main class="card">
    <h1>ChatApp</h1>
    <p class="sub">Log in to start chatting</p>
    <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" value="<?= e($username) ?>" required>
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>
      <button type="submit">Log in</button>
    </form>
    <p class="alt">New here? <a href="register.php">Create an account</a></p>
  </main>
</body>
</html>
