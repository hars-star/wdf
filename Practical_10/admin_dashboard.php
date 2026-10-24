<?php
require_once __DIR__ . '/auth_check.php';
requireRole('admin');

$pdo = getPDOConnection();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalEvents = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Practical 10</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin: 1.5rem 0; }
        .stat-card { background: #f8fafc; border: 1px solid var(--border); padding: 1.5rem; border-radius: 8px; text-align: center; }
        .stat-num { font-size: 2.2rem; font-weight: bold; color: var(--primary); }
    </style>
</head>
<body>
    <div class="container" style="max-width: 900px;">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border); padding-bottom: 1rem;">
                <div>
                    <h1>Admin Control Center</h1>
                    <p style="color: var(--text-muted);">Logged in as <strong><?= htmlspecialchars($_SESSION['name']) ?></strong> (Role: <span style="color: #dc2626; font-weight: bold;">ADMIN</span>)</p>
                </div>
                <a href="logout.php" class="btn btn-secondary">Sign Out</a>
            </div>

            <div class="grid-3">
                <div class="stat-card">
                    <div class="stat-num"><?= $totalUsers ?></div>
                    <div>Registered Users</div>
                </div>
                <div class="stat-card">
                    <div class="stat-num" style="color: #16a34a;"><?= $totalStudents ?></div>
                    <div>Student Records</div>
                </div>
                <div class="stat-card">
                    <div class="stat-num" style="color: #8b5cf6;"><?= $totalEvents ?></div>
                    <div>Campus Events</div>
                </div>
            </div>

            <div class="alert alert-success">
                🛡️ <strong>RBAC Verification:</strong> This administrative view is strictly restricted to accounts with the <code>admin</code> role. Non-admin users are automatically intercepted and redirected to their student dashboard.
            </div>

            <div style="margin-top: 1.5rem;">
                <h3>Active Session Metrics:</h3>
                <ul style="line-height: 2; margin-top: 0.5rem; color: var(--text-muted);">
                    <li><strong>Session ID:</strong> <code><?= session_id() ?></code> (Regenerated on login)</li>
                    <li><strong>Timeout Window:</strong> 15 minutes of inactivity</li>
                    <li><strong>User Email:</strong> <?= htmlspecialchars($_SESSION['email']) ?></li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
