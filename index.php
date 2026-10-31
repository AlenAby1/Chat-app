<?php
require __DIR__ . '/db.php';
if (empty($_SESSION['uid'])) { header('Location: login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf" content="<?= e(csrf_token()) ?>">
  <meta name="me" content="<?= (int) $_SESSION['uid'] ?>">
  <title>ChatApp</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="chat-page">
  <div class="app" id="app">
    <aside class="sidebar" id="sidebar">
      <header class="side-head">
        <div>
          <strong><?= e($_SESSION['username']) ?></strong>
          <span class="me-tag">you</span>
        </div>
        <a href="logout.php" class="logout">Logout</a>
      </header>
      <ul id="contacts">
        <li class="contact active" data-id="0">
          <span class="avatar group">#</span>
          <span class="cname">Everyone (group)</span>
        </li>
      </ul>
    </aside>

    <section class="conversation">
      <header class="conv-head">
        <button id="back" class="back" aria-label="Back to contacts">&larr;</button>
        <span id="title">Everyone (group)</span>
      </header>
      <div id="messages" class="messages" aria-live="polite"></div>
      <form id="composer" class="composer" autocomplete="off">
        <input type="text" id="text" maxlength="1000" placeholder="Type a message" required>
        <button type="submit">Send</button>
      </form>
    </section>
  </div>
  <script src="assets/chat.js"></script>
</body>
</html>
