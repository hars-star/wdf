<?php
session_start();
require_once __DIR__ . '/db.php';

$pdo = getPDOConnection();

$actionFilter = $_GET['action'] ?? '';
$sql = "SELECT * FROM audit_logs WHERE 1=1";
$params = [];

if ($actionFilter !== '') {
    $sql .= " AND action = ?";
    $params[] = $actionFilter;
}
$sql .= " ORDER BY id DESC LIMIT 50";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 14: System Audit Trail & Logs</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-brand">Student<span>Hub</span> Admin</div>
        <ul class="sidebar-nav">
            <li><a href="index.php">📊 Analytics & Overview</a></li>
            <li><a href="audit_logs.php" class="active">🛡️ System Audit Logs</a></li>
            <li><a href="../Practical_11/index.php">🎓 Student CRUD</a></li>
            <li><a href="../Practical_12/index.php">📅 Event CRUD</a></li>
            <li><a href="../Practical_10/login.php">🔐 Authentication</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <div>
                <h1>Security Audit Trail & Traceability</h1>
                <p style="color: var(--text-muted);">Immutable audit log recording administrative actions, database mutations, and registrations.</p>
            </div>
            <a href="index.php" class="btn btn-secondary">← Back to Dashboard</a>
        </div>

        <div class="data-table-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                <h3>Audit Trail Entries (<?= count($logs) ?>)</h3>
                <form method="GET" style="display: flex; gap: 0.5rem;">
                    <select name="action" onchange="this.form.submit()" style="padding: 0.4rem 0.8rem; border-radius: 6px; border: 1px solid var(--border);">
                        <option value="">All Actions</option>
                        <option value="SYSTEM_INIT" <?= $actionFilter === 'SYSTEM_INIT' ? 'selected' : '' ?>>SYSTEM_INIT</option>
                        <option value="EVENT_CREATE" <?= $actionFilter === 'EVENT_CREATE' ? 'selected' : '' ?>>EVENT_CREATE</option>
                        <option value="REGISTRATION" <?= $actionFilter === 'REGISTRATION' ? 'selected' : '' ?>>REGISTRATION</option>
                        <option value="STATUS_TOGGLE" <?= $actionFilter === 'STATUS_TOGGLE' ? 'selected' : '' ?>>STATUS_TOGGLE</option>
                        <option value="USER_REGISTRATION" <?= $actionFilter === 'USER_REGISTRATION' ? 'selected' : '' ?>>USER_REGISTRATION</option>
                    </select>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Log ID</th>
                        <th>Timestamp</th>
                        <th>Actor</th>
                        <th>Action</th>
                        <th>Entity</th>
                        <th>Details</th>
                        <th>Client IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr><td colspan="7" style="text-align: center; padding: 2rem;">No audit records matching filter.</td></tr>
                    <?php else: ?>
                        <?php foreach ($logs as $l): ?>
                            <tr>
                                <td>#<?= $l['id'] ?></td>
                                <td><?= htmlspecialchars($l['created_at']) ?></td>
                                <td><strong><?= htmlspecialchars($l['username']) ?></strong></td>
                                <td><span class="badge badge-active" style="background: #e0e7ff; color: #4338ca;"><?= htmlspecialchars($l['action']) ?></span></td>
                                <td><?= htmlspecialchars($l['entity']) ?></td>
                                <td><?= htmlspecialchars($l['details']) ?></td>
                                <td><code><?= htmlspecialchars($l['ip_address']) ?></code></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
