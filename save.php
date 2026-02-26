<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$db = getDB();

$titles       = $_POST['title']      ?? [];
$details      = $_POST['details']    ?? [];
$task_ids     = $_POST['task_id']    ?? [];
$session_date = $_POST['session_date'] ?? date('Y-m-d');

if (empty($titles) || count(array_filter($titles)) === 0) {
    echo json_encode(['error' => 'At least one task is required']);
    exit;
}

$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// Get or create session
$stmt = $db->prepare("SELECT id FROM work_sessions WHERE session_date = ?");
$stmt->bind_param("s", $session_date);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if ($row) {
    $session_id = $row['id'];
} else {
    $stmt = $db->prepare("INSERT INTO work_sessions (session_date) VALUES (?)");
    $stmt->bind_param("s", $session_date);
    $stmt->execute();
    $session_id = $db->insert_id;
}

// Delete tasks removed by user
$submitted_task_ids = [];
foreach ($task_ids as $tid) {
    $tid = intval($tid);
    if ($tid > 0) $submitted_task_ids[] = $tid;
}

if (!empty($submitted_task_ids)) {
    $placeholders = implode(',', $submitted_task_ids);
    $db->query("DELETE FROM work_tasks WHERE session_id = $session_id AND id NOT IN ($placeholders)");
} else {
    $db->query("DELETE FROM work_tasks WHERE session_id = $session_id");
}

// Process each task
for ($i = 0; $i < count($titles); $i++) {
    $title  = trim($titles[$i]);
    $detail = trim($details[$i] ?? '');
    $tid    = intval($task_ids[$i] ?? 0);

    if (empty($title)) continue;

    if ($tid > 0) {
        $stmt = $db->prepare("UPDATE work_tasks SET title=?, details=?, task_order=? WHERE id=? AND session_id=?");
        $stmt->bind_param("ssiii", $title, $detail, $i, $tid, $session_id);
        $stmt->execute();
        $task_id = $tid;
    } else {
        $stmt = $db->prepare("INSERT INTO work_tasks (session_id, title, details, task_order) VALUES (?,?,?,?)");
        $stmt->bind_param("issi", $session_id, $title, $detail, $i);
        $stmt->execute();
        $task_id = $db->insert_id;
    }

    // Delete removed images
    $deleted_images = $_POST['deleted_images_' . $i] ?? '';
    if (!empty($deleted_images)) {
        $del_ids = array_filter(array_map('intval', explode(',', $deleted_images)));
        foreach ($del_ids as $img_id) {
            $r = $db->query("SELECT filename FROM task_images WHERE id=$img_id AND task_id=$task_id");
            if ($r && $r->num_rows > 0) {
                $img = $r->fetch_assoc();
                @unlink(UPLOAD_DIR . $img['filename']);
                $db->query("DELETE FROM task_images WHERE id=$img_id");
            }
        }
    }

    // New image uploads
    $file_key = 'images_' . $i;
    if (isset($_FILES[$file_key]) && !empty($_FILES[$file_key]['name'][0])) {
        $files      = $_FILES[$file_key];
        $file_count = count($files['name']);

        for ($j = 0; $j < $file_count; $j++) {
            if ($files['error'][$j] !== UPLOAD_ERR_OK) continue;
            $mime = mime_content_type($files['tmp_name'][$j]);
            if (!in_array($mime, $allowed_types)) continue;
            $ext      = pathinfo($files['name'][$j], PATHINFO_EXTENSION);
            $filename = uniqid('img_') . '.' . strtolower($ext);
            $dest     = UPLOAD_DIR . $filename;
            if (move_uploaded_file($files['tmp_name'][$j], $dest)) {
                $original = $files['name'][$j];
                $stmt2 = $db->prepare("INSERT INTO task_images (task_id, filename, original_name) VALUES (?,?,?)");
                $stmt2->bind_param("iss", $task_id, $filename, $original);
                $stmt2->execute();
            }
        }
    }
}

echo json_encode(['success' => true, 'session_id' => $session_id]);
$db->close();
?>
