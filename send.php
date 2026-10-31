<?php
require __DIR__ . '/../db.php';
$me = api_user_id();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_ok()) {
    json_out(['error' => 'Bad request'], 400);
}

$to   = (int) ($_POST['to'] ?? 0);              // 0 = group room
$text = trim((string) ($_POST['text'] ?? ''));
if ($text === '' || mb_strlen($text) > 1000) {
    json_out(['error' => 'Message must be 1-1000 characters'], 422);
}

if ($to !== 0) {                                // private message: receiver must exist
    $chk = db()->prepare('SELECT id FROM users WHERE id = ?');
    $chk->execute([$to]);
    if (!$chk->fetch() || $to === $me) {
        json_out(['error' => 'Unknown recipient'], 422);
    }
}

$ins = db()->prepare('INSERT INTO messages (sender_id, receiver_id, body, created_at) VALUES (?, ?, ?, ?)');
$ins->execute([$me, $to === 0 ? null : $to, $text, time()]);
json_out(['ok' => true, 'id' => (int) db()->lastInsertId()]);
