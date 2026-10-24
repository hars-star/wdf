<?php
require_once __DIR__ . '/auth_check.php';
requireRole('student');

$pdo = getPDOConnection();
$uStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$uStmt->execute([$_SESSION['user_id']]);
$user = $uStmt->fetch();

$eventsStmt = $pdo->query("SELECT * FROM events WHERE status = 'Upcoming' LIMIT 4");
$events = $eventsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Practical 10</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container" style="max-width: 900px;">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border); padding-bottom: 1rem;">
                <div>
                    <h1>Student Portal Workspace</h1>
                    <p style="color: var(--text-muted);">Welcome, <strong><?= htmlspecialchars($user['name']) ?></strong> (Role: <span style="color: #16a34a; font-weight: bold;">STUDENT</span>)</p>
                </div>
                <a href="logout.php" class="btn btn-secondary">Sign Out</a>
            </div>

            <div style="margin: 1.5rem 0;">
                <p><strong>Department / Program:</strong> <?= htmlspecialchars($user['course'] ?? 'B.Tech IT') ?></p>
                <p><strong>Registered Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Last Login Timestamp:</strong> <?= htmlspecialchars($user['last_login'] ?? 'Just now') ?></p>
            </div>

            <h2 style="margin-top: 1.5rem; margin-bottom: 1rem;">Available Campus Events</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem;">
                <?php foreach ($events as $ev): ?>
                    <div style="background: #f8fafc; border: 1px solid var(--border); padding: 1rem; border-radius: 8px;">
                        <span style="font-size: 0.75rem; background: #dbeafe; color: #1e40af; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: bold;"><?= htmlspecialchars($ev['category']) ?></span>
                        <h3 style="font-size: 1.1rem; margin: 0.5rem 0;"><?= htmlspecialchars($ev['title']) ?></h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">📅 <?= htmlspecialchars($ev['event_date']) ?> | 📍 <?= htmlspecialchars($ev['venue']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
