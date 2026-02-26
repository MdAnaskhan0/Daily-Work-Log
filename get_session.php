<?php
require_once 'config.php';

header('Content-Type: application/json');

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    echo json_encode(['error' => 'Invalid ID']);
    exit;
}

$db = getDB();

$stmt = $db->prepare("SELECT * FROM work_sessions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$session = $stmt->get_result()->fetch_assoc();

if (!$session) {
    echo json_encode(['error' => 'Session not found']);
    exit;
}

$stmt = $db->prepare("SELECT * FROM work_tasks WHERE session_id = ? ORDER BY task_order");
$stmt->bind_param("i", $id);
$stmt->execute();
$tasks_result = $stmt->get_result();

$tasks = [];
while ($task = $tasks_result->fetch_assoc()) {
    $stmt2 = $db->prepare("SELECT * FROM task_images WHERE task_id = ?");
    $stmt2->bind_param("i", $task['id']);
    $stmt2->execute();
    $imgs = $stmt2->get_result();
    $task['images'] = [];
    while ($img = $imgs->fetch_assoc()) {
        $task['images'][] = $img;
    }
    $tasks[] = $task;
}

$session['tasks'] = $tasks;
echo json_encode($session);
$db->close();
?>
