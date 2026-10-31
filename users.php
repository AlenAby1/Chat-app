<?php
// Returns every other user and whether they were active in the last 30 seconds.
require __DIR__ . '/../db.php';
$me = api_user_id();

$stmt = db()->prepare('SELECT id, username, last_seen FROM users WHERE id <> ? ORDER BY username');
$stmt->execute([$me]);
$now = time();
$out = [];
foreach ($stmt->fetchAll() as $u) {
    $out[] = [
        'id'       => (int) $u['id'],
        'username' => $u['username'],
        'online'   => ($now - (int) $u['last_seen']) <= 30,
    ];
}
json_out(['users' => $out]);
