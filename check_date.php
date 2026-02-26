<?php
require_once 'config.php';
header('Content-Type: application/json');

$date = $_GET['date'] ?? '';
if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode(['exists' => false]);
    exit;
}

$db = getDB();
$stmt = $db->prepare("SELECT id FROM work_sessions WHERE session_date = ?");
$stmt->bind_param("s", $date);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$db->close();

if ($row) {
    echo json_encode(['exists' => true, 'session_id' => $row['id']]);
} else {
    echo json_encode(['exists' => false]);
}
?>
