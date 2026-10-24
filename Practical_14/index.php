<?php
session_start();
require_once __DIR__ . '/db.php';

$pdo = getPDOConnection();
$msg = $_SESSION['dash_msg'] ?? null;
unset($_SESSION['dash_msg']);

// Aggregate KPI counts
$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$activeEvents = $pdo->query("SELECT COUNT(*) FROM events WHERE status = 'Upcoming'")->fetchColumn();
$totalRegs = $pdo->query("SELECT COUNT(*) FROM registrations")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Department statistics for Analytics Chart
$deptStmt = $pdo->query("SELECT department, COUNT(*) as count FROM students GROUP BY department");
$deptStats = $deptStmt->fetchAll();
$maxCount = 1;
foreach ($deptStats as $d) {
    if ($d['count'] > $maxCount) $maxCount = $d['count'];
}

// Recent Registrations
$regStmt = $pdo->query("
    SELECT r.id, r.registration_date, r.status, s.name as student_name, s.enrollment_no, e.title as event_title, e.category
    FROM registrations r
    JOIN students s ON r.student_id = s.id
    JOIN events e ON r.event_id = e.id
    ORDER BY r.id DESC LIMIT 6
");
$recentRegs = $regStmt->fetchAll();

// Users roster for status toggle
$usersStmt = $pdo->query("SELECT id, name, username, email, role, status FROM users ORDER BY id DESC LIMIT 5");
$usersList = $usersStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 14: Admin Dashboard, RBAC & Audit Trails</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <!-- Dynamic Role-Based Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-brand">Student<span>Hub</span> Admin</div>
        <ul class="sidebar-nav">
            <li><a href="index.php" class="active">📊 Overview & Analytics</a></li>
            <li><a href="audit_logs.php">🛡️ System Audit Logs</a></li>
            <li><a href="../Practical_11/index.php">🎓 Student Management</a></li>
            <li><a href="../Practical_12/index.php">📅 Event Management</a></li>
            <li><a href="../Practical_13/index.html">⚡ AJAX Data Center</a></li>
            <li><a href="../Practical_10/logout.php">🚪 Exit / Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div>
                <h1>University Administration Command Center</h1>
                <p style="color: var(--text-muted);">Real-time monitoring, SQL metrics aggregation, user moderation, and audit logs.</p>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <a href="audit_logs.php" class="btn btn-secondary">View Audit Trail</a>
                <a href="../Practical_11/index.php" class="btn btn-primary">+ Manage Students</a>
            </div>
        </div>

        <?php if ($msg): ?>
            <div style="background: #dcfce7; color: #15803d; padding: 0.9rem 1.25rem; border-radius: var(--radius); margin-bottom: 1.5rem; font-weight: 500;">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <!-- 4-Card KPI Metrics Grid -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <span class="kpi-title">Total Enrolled Students</span>
                <div class="kpi-value"><?= $totalStudents ?></div>
                <span class="kpi-sub">Across 6 FTE Departments</span>
            </div>

            <div class="kpi-card">
                <span class="kpi-title">Active Upcoming Events</span>
                <div class="kpi-value" style="color: #10b981;"><?= $activeEvents ?></div>
                <span class="kpi-sub">Open for Student Enrollment</span>
            </div>

            <div class="kpi-card">
                <span class="kpi-title">Event Registrations</span>
                <div class="kpi-value" style="color: #8b5cf6;"><?= $totalRegs ?></div>
                <span class="kpi-sub">Verified MySQL Records</span>
            </div>

            <div class="kpi-card">
                <span class="kpi-title">Portal User Accounts</span>
                <div class="kpi-value" style="color: #f59e0b;"><?= $totalUsers ?></div>
                <span class="kpi-sub">RBAC Protected Accounts</span>
            </div>
        </div>

        <!-- Visual Analytics Chart (CSS & Data-driven) -->
        <div class="chart-box">
            <h3>Student Enrollment by Engineering Department</h3>
            <p style="color: var(--text-muted); font-size: 0.85rem;">Live SQL aggregation: <code>SELECT department, COUNT(*) FROM students GROUP BY department</code></p>

            <div class="bar-chart-container">
                <?php foreach ($deptStats as $dept): 
                    $pct = round(($dept['count'] / $maxCount) * 100);
                ?>
                    <div class="bar-col">
                        <div class="bar-fill" style="height: <?= max(15, $pct) ?>%;" title="<?= htmlspecialchars($dept['department']) ?>: <?= $dept['count'] ?> students"></div>
                        <div class="bar-label"><?= htmlspecialchars(substr($dept['department'], 0, 10)) ?> (<?= $dept['count'] ?>)</div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Two Column Sections: Recent Registrations & User Status Moderation -->
        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem;">
            <!-- Recent Registrations -->
            <div class="data-table-card">
                <h3>Latest Event Registrations</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Event</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentRegs as $r): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($r['student_name']) ?></strong><br>
                                    <small style="color: var(--text-muted);"><?= htmlspecialchars($r['enrollment_no']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($r['event_title']) ?></td>
                                <td><small><?= htmlspecialchars($r['registration_date']) ?></small></td>
                                <td><span class="badge badge-active"><?= htmlspecialchars($r['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- User Status Moderation Toggle -->
            <div class="data-table-card">
                <h3>User Moderation Toggle</h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.75rem;">Click badge to toggle active/inactive status.</p>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Status (Toggle)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usersList as $u): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($u['name']) ?></strong><br>
                                    <small style="color: var(--text-muted);"><?= htmlspecialchars($u['email']) ?></small>
                                </td>
                                <td><small style="text-transform: uppercase; font-weight: bold;"><?= htmlspecialchars($u['role']) ?></small></td>
                                <td>
                                    <a href="toggle_status.php?type=user&id=<?= $u['id'] ?>&status=<?= $u['status'] === 'Active' ? 'Inactive' : 'Active' ?>"
                                       class="badge <?= $u['status'] === 'Active' ? 'badge-active' : 'badge-inactive' ?>"
                                       style="text-decoration: none;"
                                       title="Click to toggle status">
                                        <?= htmlspecialchars($u['status']) ?> ⇄
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
