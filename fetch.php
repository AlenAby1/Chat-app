<?php
// Returns new messages for one conversation. The browser calls this every 2 seconds.
require __DIR__ . '/../db.php';
$me = api_user_id();

$with  = (int) ($_GET['with'] ?? 0);            // 0 = group room
$after = (int) ($_GET['after'] ?? 0);           // last message id the browser already has

// Heartbeat for the "online" dot
db()->prepare('UPDATE users SET last_seen = ? WHERE id = ?')->execute([time(), $me]);

$limit = 50;
if ($with === 0) {
    $sql = 'SELECT m.id, m.sender_id, u.username, m.body, m.created_at
            FROM messages m JOIN users u ON u.id = m.sender_id
            WHERE m.receiver_id IS NULL AND m.id > ?';
    $args = [$after];
} else {
    $sql = 'SELECT m.id, m.sender_id, u.username, m.body, m.created_at
            FROM messages m JOIN users u ON u.id = m.sender_id
            WHERE ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?))
              AND m.id > ?';
    $args = [$me, $with, $with, $me, $after];
}

if ($after === 0) {                             // first load: newest 50, shown oldest-first
    $stmt = db()->prepare($sql . ' ORDER BY m.id DESC LIMIT ' . $limit);
    $stmt->execute($args);
    $rows = array_reverse($stmt->fetchAll());
} else {
    $stmt = db()->prepare($sql . ' ORDER BY m.id ASC LIMIT 200');
    $stmt->execute($args);
    $rows = $stmt->fetchAll();
}

foreach ($rows as &$r) {
    $r['id'] = (int) $r['id'];
    $r['sender_id'] = (int) $r['sender_id'];
    $r['created_at'] = (int) $r['created_at'];
}
json_out(['messages' => $rows]);
