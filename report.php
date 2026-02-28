<?php
require_once 'config.php';

$id = intval($_GET['id'] ?? 0);
$download = isset($_GET['download']);

if (!$id) {
    die('Invalid session ID');
}

$db = getDB();

$stmt = $db->prepare("SELECT * FROM work_sessions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$session = $stmt->get_result()->fetch_assoc();

if (!$session) die('Session not found');

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

$db->close();

$date_formatted = date('F j, Y', strtotime($session['session_date']));
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Report – <?= htmlspecialchars($date_formatted) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Georgia', serif;
            background: #f5f5f0;
            color: #1a1a1a;
        }
        .report-wrapper {
            max-width: 860px;
            margin: 0 auto;
            background: #fff;
            min-height: 100vh;
        }
        .report-header {
            background: #1a1a2e;
            color: #fff;
            padding: 48px 56px 36px;
        }
        .report-header .label {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #8888aa;
            margin-bottom: 12px;
        }
        .report-header h1 {
            font-size: 32px;
            font-weight: normal;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }
        .report-header .meta {
            font-size: 13px;
            color: #9999bb;
            font-family: 'Courier New', monospace;
        }
        .report-header .task-count {
            display: inline-block;
            margin-top: 20px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            padding: 6px 16px;
            font-size: 12px;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
            color: #ccccee;
        }
        .report-body {
            padding: 48px 56px;
        }
        .task-block {
            margin-bottom: 48px;
            padding-bottom: 48px;
            border-bottom: 1px solid #e8e8e0;
        }
        .task-block:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .task-number {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            letter-spacing: 3px;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .task-title {
            font-size: 22px;
            font-weight: bold;
            color: #1a1a2e;
            margin-bottom: 6px;
            font-family: 'Georgia', serif;
        }
        .field-label {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #aaa;
            margin-top: 24px;
            margin-bottom: 8px;
        }
        .task-details {
            font-size: 15px;
            line-height: 1.8;
            color: #333;
        }
        .images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
            margin-top: 8px;
        }
        .images-grid img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border: 1px solid #e0e0d8;
            display: block;
        }
        .no-images {
            font-size: 13px;
            color: #bbb;
            font-style: italic;
        }
        .report-footer {
            background: #f5f5f0;
            padding: 24px 56px;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            color: #aaa;
            letter-spacing: 1px;
            display: flex;
            justify-content: space-between;
        }
        .print-bar {
            background: #1a1a2e;
            color: #fff;
            padding: 14px 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .print-bar span {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #9999bb;
        }
        .btn-print {
            background: #fff;
            color: #1a1a2e;
            border: none;
            padding: 8px 24px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            letter-spacing: 1px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-back {
            background: transparent;
            color: #9999bb;
            border: 1px solid #444466;
            padding: 8px 20px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        @media print {
            .print-bar { display: none; }
            body { background: #fff; }
            .report-wrapper { box-shadow: none; }
            .images-grid img { break-inside: avoid; }
            .task-block { break-inside: avoid; }
        }
    </style>
</head>
<body>

<div class="print-bar">
    <span>WORK LOG REPORT</span>
    <div style="display:flex;gap:12px;">
        <a href="index.php" class="btn-back">← Back</a>
        <button class="btn-print" onclick="window.print()">⬇ Print / Save PDF</button>
    </div>
</div>

<div class="report-wrapper">
    <div class="report-header">
        <div class="label">Daily Work Report</div>
        <h1><?= htmlspecialchars($date_formatted) ?></h1>
        <div class="meta">Generated: <?= date('M j, Y – H:i') ?></div>
        <div class="task-count"><?= count($tasks) ?> TASK<?= count($tasks) !== 1 ? 'S' : '' ?> COMPLETED</div>
    </div>

    <div class="report-body">
        <?php foreach ($tasks as $index => $task): ?>
        <div class="task-block">
            <div class="task-number">Task <?= str_pad($index + 1, 2, '0', STR_PAD_LEFT) ?></div>

            <div class="field-label">Work Title</div>
            <div class="task-title"><?= htmlspecialchars($task['title']) ?></div>

            <div class="field-label">Work Details</div>
            <div class="task-details"><?= nl2br(htmlspecialchars($task['details'])) ?></div>

            <div class="field-label">Images</div>
            <?php if (!empty($task['images'])): ?>
            <div class="images-grid">
                <?php foreach ($task['images'] as $img): ?>
                <img src="<?= $base_url . UPLOAD_URL . htmlspecialchars($img['filename']) ?>" alt="<?= htmlspecialchars($img['original_name']) ?>">
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="no-images">No images attached</div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="report-footer">
        <span>WORKLOG SYSTEM</span>
        <span><?= htmlspecialchars($date_formatted) ?> &mdash; <?= count($tasks) ?> Tasks</span>
    </div>
</div>

<?php if ($download): ?>
<script>window.onload = function() { window.print(); }</script>
<?php endif; ?>

</body>
</html>