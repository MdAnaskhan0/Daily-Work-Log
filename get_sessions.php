<?php
require_once 'config.php';

header('Content-Type: application/json');

$db = getDB();

$result = $db->query("
    SELECT ws.id, ws.session_date, ws.created_at,
           COUNT(wt.id) as task_count
    FROM work_sessions ws
    LEFT JOIN work_tasks wt ON wt.session_id = ws.id
    GROUP BY ws.id
    ORDER BY ws.session_date DESC
");

$sessions = [];
while ($row = $result->fetch_assoc()) {
    $sessions[] = $row;
}

echo json_encode(['sessions' => $sessions]);
$db->close();
?>
