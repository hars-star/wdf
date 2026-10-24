<?php
session_start();
require_once __DIR__ . '/db.php';

$pdo = getPDOConnection();

$success = $_SESSION['event_success'] ?? null;
$error = $_SESSION['event_error'] ?? null;
unset($_SESSION['event_success'], $_SESSION['event_error']);

$category = $_GET['cat'] ?? '';
$status = $_GET['status'] ?? '';

$sql = "SELECT * FROM events WHERE 1=1";
$params = [];

if ($category !== '') {
    $sql .= " AND category = ?";
    $params[] = $category;
}
if ($status !== '') {
    $sql .= " AND status = ?";
    $params[] = $status;
}
$sql .= " ORDER BY event_date ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 12: Event Management CRUD & Poster Upload</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .events-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; }
        .event-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; box-shadow: var(--shadow); }
        .poster-box { height: 160px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative; }
        .poster-box img { width: 100%; height: 100%; object-fit: cover; }
        .event-content { padding: 1.25rem; flex: 1; display: flex; flex-direction: column; }
    </style>
</head>
<body>
    <div class="crud-container">
        <header class="header-row">
            <div>
                <h1>University Event Management</h1>
                <p style="color: var(--text-muted);">Event CRUD with MIME-type validated poster uploads, safe renaming, and category filtering.</p>
            </div>
            <button class="btn btn-primary" onclick="document.getElementById('addEventModal').classList.add('active')">+ Add New Event</button>
        </header>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Filter Bar -->
        <form method="GET" class="filter-card">
            <select name="cat" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <option value="Technical" <?= $category === 'Technical' ? 'selected' : '' ?>>Technical</option>
                <option value="Workshop" <?= $category === 'Workshop' ? 'selected' : '' ?>>Workshop</option>
                <option value="Cultural" <?= $category === 'Cultural' ? 'selected' : '' ?>>Cultural</option>
                <option value="Seminar" <?= $category === 'Seminar' ? 'selected' : '' ?>>Seminar</option>
                <option value="Sports" <?= $category === 'Sports' ? 'selected' : '' ?>>Sports</option>
            </select>

            <select name="status" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="Upcoming" <?= $status === 'Upcoming' ? 'selected' : '' ?>>Upcoming</option>
                <option value="Ongoing" <?= $status === 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
                <option value="Closed" <?= $status === 'Closed' ? 'selected' : '' ?>>Closed</option>
            </select>

            <?php if ($category || $status): ?>
                <a href="index.php" class="btn btn-outline">Reset Filter</a>
            <?php endif; ?>
        </form>

        <!-- Events Grid -->
        <div class="events-grid">
            <?php if (empty($events)): ?>
                <p style="color: var(--text-muted); grid-column: 1/-1;">No events found matching your filter selection.</p>
            <?php else: ?>
                <?php foreach ($events as $ev): ?>
                    <article class="event-card">
                        <div class="poster-box">
                            <?php if (!empty($ev['poster_file']) && file_exists(__DIR__ . '/uploads/' . $ev['poster_file'])): ?>
                                <img src="uploads/<?= htmlspecialchars($ev['poster_file']) ?>" alt="<?= htmlspecialchars($ev['title']) ?>">
                            <?php else: ?>
                                <div style="color: #64748b; font-weight: 600;">🖼️ No Poster Image</div>
                            <?php endif; ?>
                            <span class="badge" style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.7); color: #fff;">
                                <?= htmlspecialchars($ev['category']) ?>
                            </span>
                        </div>
                        <div class="event-content">
                            <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($ev['title']) ?></h3>
                            <p style="font-size: 0.85rem; color: var(--text-muted); flex: 1; margin-bottom: 0.75rem;">
                                <?= htmlspecialchars(substr($ev['description'], 0, 100)) ?>...
                            </p>
                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                                📍 <strong>Venue:</strong> <?= htmlspecialchars($ev['venue']) ?><br>
                                📅 <strong>Date:</strong> <?= htmlspecialchars($ev['event_date']) ?><br>
                                💺 <strong>Seats:</strong> <?= htmlspecialchars($ev['max_seats']) ?>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); padding-top: 0.75rem;">
                                <span class="badge <?= $ev['status'] === 'Upcoming' ? 'badge-active' : 'badge-inactive' ?>">
                                    <?= htmlspecialchars($ev['status']) ?>
                                </span>
                                <div>
                                    <button class="btn btn-outline btn-sm" onclick='openEditEvent(<?= json_encode($ev) ?>)'>✏️ Edit</button>
                                    <a href="event_action.php?action=delete&id=<?= $ev['id'] ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Permanently delete this event?')">🗑️</a>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal: Add Event -->
    <div id="addEventModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Add New Campus Event</h2>
                <button type="button" class="modal-close" onclick="document.getElementById('addEventModal').classList.remove('active')">&times;</button>
            </div>
            <form action="event_action.php?action=add" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Event Title *</label>
                    <input type="text" name="title" required placeholder="e.g. AI Innovation Summit">
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category" required>
                            <option value="Technical">Technical</option>
                            <option value="Workshop">Workshop</option>
                            <option value="Cultural">Cultural</option>
                            <option value="Seminar">Seminar</option>
                            <option value="Sports">Sports</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" required>
                            <option value="Upcoming">Upcoming</option>
                            <option value="Ongoing">Ongoing</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Venue *</label>
                        <input type="text" name="venue" required placeholder="Auditorium / Lab">
                    </div>
                    <div class="form-group">
                        <label>Event Date *</label>
                        <input type="date" name="event_date" required value="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Start Time</label>
                        <input type="time" name="start_time" value="09:30">
                    </div>
                    <div class="form-group">
                        <label>End Time</label>
                        <input type="time" name="end_time" value="17:00">
                    </div>
                </div>

                <div class="form-group">
                    <label>Maximum Seats *</label>
                    <input type="number" name="max_seats" value="100" min="10" required>
                </div>

                <div class="form-group">
                    <label>Event Poster Image (Max 2MB: JPG, PNG, WEBP)</label>
                    <input type="file" name="poster" accept=".jpg,.jpeg,.png,.webp">
                </div>

                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" rows="3" required placeholder="Provide a summary of event agenda and rules..."></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('addEventModal').classList.remove('active')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Event</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Event -->
    <div id="editEventModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Edit Event Details</h2>
                <button type="button" class="modal-close" onclick="document.getElementById('editEventModal').classList.remove('active')">&times;</button>
            </div>
            <form action="event_action.php?action=edit" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="edit_id" name="id">

                <div class="form-group">
                    <label>Event Title *</label>
                    <input type="text" id="edit_title" name="title" required>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Category *</label>
                        <select id="edit_category" name="category" required>
                            <option value="Technical">Technical</option>
                            <option value="Workshop">Workshop</option>
                            <option value="Cultural">Cultural</option>
                            <option value="Seminar">Seminar</option>
                            <option value="Sports">Sports</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status *</label>
                        <select id="edit_status" name="status" required>
                            <option value="Upcoming">Upcoming</option>
                            <option value="Ongoing">Ongoing</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Venue *</label>
                        <input type="text" id="edit_venue" name="venue" required>
                    </div>
                    <div class="form-group">
                        <label>Event Date *</label>
                        <input type="date" id="edit_date" name="event_date" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Maximum Seats *</label>
                    <input type="number" id="edit_seats" name="max_seats" required>
                </div>

                <div class="form-group">
                    <label>Replace Poster (Optional, Max 2MB)</label>
                    <input type="file" name="poster" accept=".jpg,.jpeg,.png,.webp">
                </div>

                <div class="form-group">
                    <label>Description *</label>
                    <textarea id="edit_desc" name="description" rows="3" required></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('editEventModal').classList.remove('active')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Event</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditEvent(ev) {
            document.getElementById('edit_id').value = ev.id;
            document.getElementById('edit_title').value = ev.title;
            document.getElementById('edit_category').value = ev.category;
            document.getElementById('edit_status').value = ev.status;
            document.getElementById('edit_venue').value = ev.venue;
            document.getElementById('edit_date').value = ev.event_date;
            document.getElementById('edit_seats').value = ev.max_seats;
            document.getElementById('edit_desc').value = ev.description;
            document.getElementById('editEventModal').classList.add('active');
        }
    </script>
</body>
</html>
