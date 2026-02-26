<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkLog — Daily Work Tracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Mono:wght@300;400;500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        :root {
            --bg: #f4f3ef;
            --surface: #ffffff;
            --border: #e2e0d8;
            --text-primary: #1c1c1e;
            --text-secondary: #6b6a65;
            --text-muted: #a8a7a2;
            --accent: #1a1a2e;
            --accent-hover: #2d2d4e;
            --danger: #c0392b;
            --warning-bg: #fffbf0;
            --warning-border: #f0d080;
            --warning-text: #7a5c00;
            --edit-bg: #f0f4ff;
            --edit-border: #c0cff5;
            --edit-text: #1a2a6e;
        }
        * { box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text-primary); min-height: 100vh; }

        /* SIDEBAR */
        .sidebar { width: 280px; min-height: 100vh; background: var(--accent); position: fixed; left: 0; top: 0; display: flex; flex-direction: column; z-index: 200; }
        .sidebar-brand { padding: 32px 28px 24px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .sidebar-brand .mono { font-family: 'DM Mono', monospace; font-size: 10px; letter-spacing: 3px; color: rgba(255,255,255,0.35); text-transform: uppercase; margin-bottom: 6px; }
        .sidebar-brand h1 { font-family: 'DM Serif Display', serif; font-size: 24px; color: #fff; margin: 0; font-weight: 400; }
        .sidebar-nav { padding: 20px 0; flex: 1; }
        .nav-label { font-family: 'DM Mono', monospace; font-size: 9px; letter-spacing: 3px; color: rgba(255,255,255,0.25); text-transform: uppercase; padding: 0 28px; margin-bottom: 8px; margin-top: 16px; }
        .nav-btn { display: block; width: 100%; padding: 12px 28px; font-size: 14px; color: rgba(255,255,255,0.6); font-family: 'DM Sans', sans-serif; background: none; border: none; text-align: left; cursor: pointer; }
        .nav-btn:hover, .nav-btn.active { color: #fff; background: rgba(255,255,255,0.08); }
        .nav-btn.active { border-left: 2px solid rgba(255,255,255,0.5); }
        .sidebar-footer { padding: 20px 28px; border-top: 1px solid rgba(255,255,255,0.08); font-family: 'DM Mono', monospace; font-size: 10px; color: rgba(255,255,255,0.2); }

        /* MAIN */
        .main-content { margin-left: 280px; padding: 48px 52px; min-height: 100vh; }
        .page-header { margin-bottom: 40px; padding-bottom: 28px; border-bottom: 1px solid var(--border); display: flex; align-items: flex-end; justify-content: space-between; }
        .page-header h2 { font-family: 'DM Serif Display', serif; font-size: 36px; font-weight: 400; color: var(--text-primary); margin: 0; }
        .page-header .date-display { font-family: 'DM Mono', monospace; font-size: 12px; color: var(--text-muted); letter-spacing: 1px; }
        .panel { display: none; }
        .panel.active { display: block; }

        /* EDIT MODE BANNER */
        .edit-banner {
            background: var(--edit-bg);
            border: 1px solid var(--edit-border);
            padding: 14px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .edit-banner .edit-label {
            font-family: 'DM Mono', monospace;
            font-size: 12px;
            color: var(--edit-text);
            letter-spacing: 1px;
        }
        .edit-banner .edit-label strong { font-weight: 600; }
        .btn-clear-edit {
            background: none;
            border: 1px solid var(--edit-border);
            color: var(--edit-text);
            font-family: 'DM Mono', monospace;
            font-size: 11px;
            padding: 5px 14px;
            cursor: pointer;
        }
        .btn-clear-edit:hover { background: var(--edit-border); }

        /* DATE EXISTS WARNING */
        .date-exists-notice {
            background: var(--warning-bg);
            border: 1px solid var(--warning-border);
            padding: 12px 18px;
            margin-bottom: 16px;
            font-family: 'DM Mono', monospace;
            font-size: 12px;
            color: var(--warning-text);
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .date-exists-notice.show { display: flex; }
        .btn-load-existing {
            background: var(--warning-text);
            color: #fff;
            border: none;
            font-family: 'DM Mono', monospace;
            font-size: 11px;
            padding: 6px 16px;
            cursor: pointer;
            white-space: nowrap;
        }

        /* FORM */
        .form-card { background: var(--surface); border: 1px solid var(--border); padding: 36px 40px; margin-bottom: 20px; }
        .date-row { display: flex; align-items: center; gap: 16px; margin-bottom: 36px; padding-bottom: 28px; border-bottom: 1px solid var(--border); }
        .date-row label { font-family: 'DM Mono', monospace; font-size: 11px; letter-spacing: 2px; color: var(--text-muted); text-transform: uppercase; }
        .date-input { border: 1px solid var(--border); background: var(--bg); font-family: 'DM Mono', monospace; font-size: 14px; padding: 10px 16px; color: var(--text-primary); outline: none; }
        .date-input:focus { border-color: var(--accent); }

        /* TASK BLOCK */
        .task-block { border: 1px solid var(--border); margin-bottom: 20px; background: var(--surface); position: relative; }
        .task-block.is-existing { border-color: #c8d8f0; }
        .task-block-header { background: var(--bg); padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border); }
        .task-block.is-existing .task-block-header { background: #eef3fb; border-bottom-color: #c8d8f0; }
        .task-number-badge { font-family: 'DM Mono', monospace; font-size: 11px; letter-spacing: 2px; color: var(--text-muted); text-transform: uppercase; }
        .task-saved-badge { font-family: 'DM Mono', monospace; font-size: 10px; letter-spacing: 1px; color: #3a6acc; background: #dce8ff; padding: 2px 10px; margin-left: 10px; }
        .btn-remove-task { background: none; border: 1px solid var(--border); color: var(--danger); font-size: 12px; font-family: 'DM Mono', monospace; padding: 4px 14px; cursor: pointer; }
        .btn-remove-task:hover { background: var(--danger); color: #fff; border-color: var(--danger); }
        .task-block-body { padding: 28px 28px 24px; }
        .field-label { font-family: 'DM Mono', monospace; font-size: 10px; letter-spacing: 2px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px; display: block; }
        .form-input { width: 100%; border: 1px solid var(--border); background: var(--bg); padding: 12px 16px; font-family: 'DM Sans', sans-serif; font-size: 15px; color: var(--text-primary); outline: none; margin-bottom: 20px; resize: vertical; min-height: 44px; }
        .form-input:focus { border-color: var(--accent); background: #fff; }
        textarea.form-input { min-height: 110px; }

        /* IMAGE UPLOAD */
        .image-upload-area { border: 1.5px dashed var(--border); padding: 20px 24px; text-align: center; cursor: pointer; background: var(--bg); position: relative; }
        .image-upload-area:hover { border-color: var(--accent); }
        .image-upload-area input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
        .image-upload-area .upload-label { font-family: 'DM Mono', monospace; font-size: 11px; color: var(--text-muted); letter-spacing: 1px; }
        .image-upload-area .upload-sub { font-size: 11px; color: var(--text-muted); margin-top: 4px; }

        /* IMAGE PREVIEWS */
        .image-preview-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 14px; }
        .image-thumb-wrap { position: relative; }
        .image-thumb { width: 88px; height: 70px; object-fit: cover; border: 1px solid var(--border); display: block; }
        .image-thumb-wrap .saved-badge {
            position: absolute; bottom: 0; left: 0; right: 0;
            background: rgba(0,0,0,0.5); color: #fff;
            font-family: 'DM Mono', monospace; font-size: 9px;
            text-align: center; padding: 2px 0; letter-spacing: 1px;
        }
        .remove-img { position: absolute; top: -6px; right: -6px; width: 20px; height: 20px; background: var(--danger); color: #fff; border: none; font-size: 13px; cursor: pointer; display: flex; align-items: center; justify-content: center; line-height: 1; font-weight: bold; }
        .remove-img:hover { background: #8b0000; }

        /* ACTION ROW */
        .action-row { display: flex; align-items: center; justify-content: space-between; margin-top: 8px; }
        .btn-add-task { background: none; border: 1px solid var(--accent); color: var(--accent); font-family: 'DM Mono', monospace; font-size: 12px; letter-spacing: 1px; padding: 12px 28px; cursor: pointer; }
        .btn-add-task:hover { background: var(--accent); color: #fff; }
        .btn-save { background: var(--accent); border: none; color: #fff; font-family: 'DM Mono', monospace; font-size: 12px; letter-spacing: 2px; padding: 13px 40px; cursor: pointer; text-transform: uppercase; }
        .btn-save:hover { background: var(--accent-hover); }
        .btn-save:disabled { opacity: 0.5; cursor: not-allowed; }

        /* LIST */
        .sessions-list { display: flex; flex-direction: column; gap: 2px; }
        .session-item { background: var(--surface); border: 1px solid var(--border); padding: 20px 28px; display: flex; align-items: center; justify-content: space-between; }
        .session-item:hover { border-color: #ccc; }
        .session-date-text { font-family: 'DM Serif Display', serif; font-size: 20px; color: var(--text-primary); }
        .session-meta { font-family: 'DM Mono', monospace; font-size: 11px; color: var(--text-muted); margin-top: 4px; }
        .session-actions { display: flex; gap: 8px; }
        .btn-action { font-family: 'DM Mono', monospace; font-size: 11px; letter-spacing: 1px; padding: 8px 18px; cursor: pointer; text-decoration: none; display: inline-block; border: 1px solid; }
        .btn-edit-session { border-color: #c8d8f0; color: #3a6acc; background: #eef3fb; }
        .btn-edit-session:hover { background: #3a6acc; color: #fff; border-color: #3a6acc; }
        .btn-view { border-color: var(--accent); color: var(--accent); background: none; }
        .btn-view:hover { background: var(--accent); color: #fff; }
        .btn-download { border-color: var(--border); color: var(--text-secondary); background: none; }
        .btn-download:hover { background: var(--text-secondary); color: #fff; }

        .empty-state { text-align: center; padding: 80px 0; color: var(--text-muted); }
        .empty-state .big { font-family: 'DM Serif Display', serif; font-size: 48px; color: var(--border); display: block; margin-bottom: 16px; }
        .empty-state p { font-size: 14px; }
        .loading { font-family: 'DM Mono', monospace; font-size: 12px; color: var(--text-muted); padding: 40px 0; text-align: center; }

        /* TOAST */
        .toast-msg { position: fixed; bottom: 32px; right: 32px; background: var(--accent); color: #fff; padding: 14px 24px; font-family: 'DM Mono', monospace; font-size: 13px; z-index: 9999; display: none; }
        .toast-msg.error { background: var(--danger); }
        .toast-msg.show { display: block; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <div class="mono">Daily Tracker</div>
        <h1>WorkLog</h1>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-label">Menu</div>
        <button class="nav-btn active" id="nav-log" onclick="showPanel('log', this)">Log Work</button>
        <button class="nav-btn" id="nav-list" onclick="showPanel('list', this)">View Records</button>
    </nav>
    <div class="sidebar-footer">&copy; <?= date('Y') ?> WorkLog System</div>
</div>

<div class="main-content">

    <!-- LOG PANEL -->
    <div id="panel-log" class="panel active">
        <div class="page-header">
            <div>
                <h2 id="form-title">Log Today's Work</h2>
            </div>
            <div class="date-display" id="live-date"></div>
        </div>

        <!-- Edit mode banner -->
        <div class="edit-banner" id="edit-banner" style="display:none;">
            <div class="edit-label">✏️ &nbsp;<strong>Edit Mode</strong> — Modifying existing log for <span id="edit-date-label"></span></div>
            <button class="btn-clear-edit" onclick="resetForm()">✕ New Log</button>
        </div>

        <!-- Date exists notice -->
        <div class="date-exists-notice" id="date-exists-notice">
            <span>⚠️ A work log already exists for this date.</span>
            <button class="btn-load-existing" id="btn-load-existing">Load & Edit Existing</button>
        </div>

        <form id="work-form" enctype="multipart/form-data">
            <div class="form-card">
                <div class="date-row">
                    <label>Date</label>
                    <input type="date" class="date-input" name="session_date" id="session-date" required>
                </div>
                <div id="tasks-container"></div>
            </div>
            <div class="action-row">
                <button type="button" class="btn-add-task" onclick="addTask()">+ Add More Work</button>
                <button type="submit" class="btn-save" id="save-btn">Save Log</button>
            </div>
        </form>
    </div>

    <!-- LIST PANEL -->
    <div id="panel-list" class="panel">
        <div class="page-header">
            <h2>Work Records</h2>
            <div class="date-display">All Entries</div>
        </div>
        <div id="sessions-container" class="sessions-list">
            <div class="loading">Loading records…</div>
        </div>
    </div>

</div>

<div class="toast-msg" id="toast"></div>

<script>
let taskCount = 0;
let currentSessionId = null; // null = new, number = editing existing
let existingSessionIdForDate = null; // session that exists for current date

const dateInput = document.getElementById('session-date');
dateInput.value = new Date().toISOString().split('T')[0];

// Live date
const liveDateEl = document.getElementById('live-date');
function updateDate() {
    liveDateEl.textContent = new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
}
updateDate();

// Panel switching
function showPanel(name, btn) {
    document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('panel-' + name).classList.add('active');
    btn.classList.add('active');
    if (name === 'list') loadSessions();
}

// Date change: check if session exists for that date
dateInput.addEventListener('change', async function() {
    if (currentSessionId) return; // already in edit mode, don't interrupt
    const notice = document.getElementById('date-exists-notice');
    notice.classList.remove('show');
    existingSessionIdForDate = null;

    const date = this.value;
    if (!date) return;

    try {
        const res = await fetch('check_date.php?date=' + encodeURIComponent(date));
        const data = await res.json();
        if (data.exists) {
            existingSessionIdForDate = data.session_id;
            notice.classList.add('show');
            document.getElementById('btn-load-existing').onclick = () => loadSessionForEdit(data.session_id);
        }
    } catch(e) {}
});

// Check on page load for today
(async () => {
    try {
        const res = await fetch('check_date.php?date=' + encodeURIComponent(dateInput.value));
        const data = await res.json();
        if (data.exists) {
            existingSessionIdForDate = data.session_id;
            const notice = document.getElementById('date-exists-notice');
            notice.classList.add('show');
            document.getElementById('btn-load-existing').onclick = () => loadSessionForEdit(data.session_id);
        }
    } catch(e) {}
})();

// Load an existing session into the form
async function loadSessionForEdit(sessionId) {
    try {
        const res = await fetch('get_session.php?id=' + sessionId);
        const data = await res.json();
        if (data.error) { showToast(data.error, true); return; }

        // Switch to log panel
        showPanel('log', document.getElementById('nav-log'));

        // Set state
        currentSessionId = sessionId;
        dateInput.value = data.session_date;
        dateInput.readOnly = true;
        dateInput.style.opacity = '0.6';

        // Hide date-exists notice
        document.getElementById('date-exists-notice').classList.remove('show');

        // Show edit banner
        const banner = document.getElementById('edit-banner');
        banner.style.display = 'flex';
        const d = new Date(data.session_date + 'T12:00:00');
        document.getElementById('edit-date-label').textContent =
            d.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });

        document.getElementById('form-title').textContent = 'Edit Work Log';

        // Clear and populate tasks
        document.getElementById('tasks-container').innerHTML = '';
        taskCount = 0;

        for (const task of data.tasks) {
            addTask(task);
        }
    } catch(e) {
        showToast('Failed to load session.', true);
    }
}

// Reset form to new state
function resetForm() {
    currentSessionId = null;
    existingSessionIdForDate = null;

    dateInput.readOnly = false;
    dateInput.style.opacity = '';
    dateInput.value = new Date().toISOString().split('T')[0];

    document.getElementById('edit-banner').style.display = 'none';
    document.getElementById('date-exists-notice').classList.remove('show');
    document.getElementById('form-title').textContent = "Log Today's Work";
    document.getElementById('tasks-container').innerHTML = '';
    taskCount = 0;
    addTask();
}

// Add task block — optionally pre-fill with existing data
function addTask(existingTask) {
    const idx = taskCount++;
    const taskId = existingTask ? existingTask.id : 0;
    const isExisting = taskId > 0;
    const visNum = String(document.querySelectorAll('.task-block').length + 1).padStart(2,'0');

    const block = document.createElement('div');
    block.className = 'task-block' + (isExisting ? ' is-existing' : '');
    block.id = 'task-' + idx;

    const savedBadge = isExisting ? '<span class="task-saved-badge">SAVED</span>' : '';
    const canRemove = (taskCount > 1 || currentSessionId);
    const removeBtn = `<button type="button" class="btn-remove-task" onclick="removeTask(${idx})">Remove</button>`;

    block.innerHTML = `
        <div class="task-block-header">
            <span>
                <span class="task-number-badge">Task ${visNum}</span>
                ${savedBadge}
            </span>
            ${canRemove ? removeBtn : ''}
        </div>
        <div class="task-block-body">
            <input type="hidden" name="task_id[]" value="${taskId}">
            <input type="hidden" name="deleted_images_${idx}" id="deleted-imgs-${idx}" value="">

            <label class="field-label">Work Title</label>
            <input type="text" class="form-input" name="title[]"
                placeholder="e.g. Completed API integration"
                value="${existingTask ? escHtml(existingTask.title) : ''}" required>

            <label class="field-label">Work Details</label>
            <textarea class="form-input" name="details[]"
                placeholder="Describe what you did, challenges faced, outcomes…">${existingTask ? escHtml(existingTask.details) : ''}</textarea>

            <label class="field-label">Images</label>
            <div class="image-preview-grid" id="preview-${idx}"></div>
            <div class="image-upload-area" style="margin-top:${isExisting && existingTask.images.length ? '12px' : '0'}">
                <input type="file" name="images_${idx}[]" multiple accept="image/*"
                    onchange="previewNewImages(this, ${idx})">
                <div class="upload-label">${isExisting ? '+ Add More Images' : 'Click or drag images here'}</div>
                <div class="upload-sub">JPG, PNG, GIF, WEBP accepted</div>
            </div>
        </div>
    `;
    document.getElementById('tasks-container').appendChild(block);

    // Load existing saved images
    if (isExisting && existingTask.images && existingTask.images.length > 0) {
        const grid = document.getElementById('preview-' + idx);
        existingTask.images.forEach(img => {
            addSavedImageThumb(grid, img, idx);
        });
    }

    if (!existingTask) {
        block.querySelector('input[name="title[]"]').focus();
    }
}

function addSavedImageThumb(grid, img, taskIdx) {
    const wrap = document.createElement('div');
    wrap.className = 'image-thumb-wrap';
    wrap.id = 'img-wrap-' + img.id;
    wrap.innerHTML = `
        <img src="uploads/${img.filename}" class="image-thumb" alt="${escHtml(img.original_name)}">
        <div class="saved-badge">SAVED</div>
        <button type="button" class="remove-img" title="Remove image"
            onclick="markImageDeleted(${img.id}, ${taskIdx}, this.parentElement)">×</button>
    `;
    grid.appendChild(wrap);
}

function markImageDeleted(imgId, taskIdx, wrapEl) {
    const hiddenField = document.getElementById('deleted-imgs-' + taskIdx);
    const current = hiddenField.value ? hiddenField.value.split(',') : [];
    current.push(imgId);
    hiddenField.value = current.join(',');
    wrapEl.style.opacity = '0.3';
    wrapEl.style.pointerEvents = 'none';
    const badge = wrapEl.querySelector('.saved-badge');
    if (badge) badge.textContent = 'DELETED';
    badge.style.background = 'rgba(192,57,43,0.7)';
}

function previewNewImages(input, idx) {
    const grid = document.getElementById('preview-' + idx);
    // Remove previous new-image thumbs (keep saved ones)
    grid.querySelectorAll('.new-thumb').forEach(el => el.remove());

    for (let f of input.files) {
        const reader = new FileReader();
        reader.onload = e => {
            const wrap = document.createElement('div');
            wrap.className = 'image-thumb-wrap new-thumb';
            wrap.innerHTML = `<img src="${e.target.result}" class="image-thumb">`;
            grid.appendChild(wrap);
        };
        reader.readAsDataURL(f);
    }
}

function removeTask(idx) {
    document.getElementById('task-' + idx).remove();
    renumberTasks();
}

function renumberTasks() {
    document.querySelectorAll('.task-number-badge').forEach((el, i) => {
        el.textContent = 'Task ' + String(i + 1).padStart(2, '0');
    });
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Initial empty task
addTask();

// Save
document.getElementById('work-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('save-btn');
    btn.disabled = true;
    btn.textContent = 'Saving…';

    const formData = new FormData(this);

    try {
        const res = await fetch('save.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.success) {
            const isEdit = currentSessionId !== null;
            showToast(isEdit ? 'Work log updated successfully!' : 'Work log saved successfully!', false);

            if (isEdit) {
                // Reload the session to refresh saved images etc.
                loadSessionForEdit(data.session_id);
            } else {
                resetForm();
            }
        } else {
            showToast(data.error || 'Failed to save', true);
        }
    } catch (err) {
        showToast('Network error. Check server.', true);
    }

    btn.disabled = false;
    btn.textContent = currentSessionId ? 'Update Log' : 'Save Log';
});

// Update save button label when in edit mode
function updateSaveBtnLabel() {
    document.getElementById('save-btn').textContent = currentSessionId ? 'Update Log' : 'Save Log';
}

// Load sessions list
async function loadSessions() {
    const container = document.getElementById('sessions-container');
    container.innerHTML = '<div class="loading">Loading records…</div>';
    try {
        const res = await fetch('get_sessions.php');
        const data = await res.json();
        renderSessions(data.sessions || []);
    } catch {
        container.innerHTML = '<div class="loading">Failed to load records.</div>';
    }
}

function renderSessions(sessions) {
    const container = document.getElementById('sessions-container');
    if (!sessions.length) {
        container.innerHTML = `<div class="empty-state"><span class="big">○</span><p>No work logs yet. Start by logging today's work.</p></div>`;
        return;
    }
    container.innerHTML = sessions.map(s => {
        const d = new Date(s.session_date + 'T12:00:00');
        const dateStr = d.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        return `
        <div class="session-item">
            <div>
                <div class="session-date-text">${dateStr}</div>
                <div class="session-meta">${s.task_count} task${s.task_count != 1 ? 's' : ''} logged</div>
            </div>
            <div class="session-actions">
                <button class="btn-action btn-edit-session" onclick="loadSessionForEdit(${s.id})">Edit</button>
                <a href="report.php?id=${s.id}" target="_blank" class="btn-action btn-view">View</a>
                <a href="report.php?id=${s.id}&download=1" target="_blank" class="btn-action btn-download">Download PDF</a>
            </div>
        </div>`;
    }).join('');
}

function showToast(msg, isError) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast-msg show' + (isError ? ' error' : '');
    setTimeout(() => t.className = 'toast-msg', 3500);
}
</script>
</body>
</html>
