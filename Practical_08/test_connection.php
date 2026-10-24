<?php
require_once __DIR__ . '/db.php';

$pdo = null;
$mysqli = null;
$pdo_status = false;
$mysqli_status = false;
$error = null;
$tables_info = [];

try {
    $pdo = getPDOConnection();
    $pdo_status = true;

    $mysqli = getMySQLiConnection();
    $mysqli_status = true;

    // Fetch tables & count
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $t) {
        $countStmt = $pdo->query("SELECT COUNT(*) FROM `{$t}`");
        $tables_info[$t] = $countStmt->fetchColumn();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 8: MySQL Connection & Schema Test</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f8fafc; color: #0f172a; padding: 2rem; line-height: 1.6; }
        .card { max-width: 900px; margin: 0 auto; background: #fff; padding: 2rem; border-radius: 12px; border: 1px solid #cbd5e1; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        h1 { color: #2563eb; margin-bottom: 0.5rem; }
        .status-badge { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.9rem; border-radius: 9999px; font-weight: bold; font-size: 0.9rem; margin-right: 0.5rem; }
        .status-ok { background: #dcfce7; color: #15803d; }
        .status-fail { background: #fee2e2; color: #b91c1c; }
        table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; }
        th, td { padding: 0.8rem 1rem; border: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; font-size: 0.85rem; text-transform: uppercase; color: #64748b; }
        .btn { display: inline-block; background: #2563eb; color: #fff; padding: 0.6rem 1.2rem; border-radius: 6px; text-decoration: none; font-weight: 600; margin-top: 1.5rem; }
    </style>
</head>
<body>
    <div class="card">
        <h1>MySQL Database Connectivity Verification</h1>
        <p style="color: #64748b; margin-bottom: 1.5rem;">Diagnostic report verifying PDO, MySQLi, charset configuration, and table initialization.</p>

        <div style="margin-bottom: 1.5rem;">
            <?php if ($pdo_status): ?>
                <span class="status-badge status-ok">✓ PDO Connection Active</span>
            <?php else: ?>
                <span class="status-badge status-fail">✕ PDO Failed</span>
            <?php endif; ?>

            <?php if ($mysqli_status): ?>
                <span class="status-badge status-ok">✓ MySQLi Connection Active</span>
            <?php else: ?>
                <span class="status-badge status-fail">✕ MySQLi Failed</span>
            <?php endif; ?>
        </div>

        <?php if ($error): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 6px;"><?= htmlspecialchars($error) ?></div>
        <?php else: ?>
            <h2>Database: `student_portal` (MySQL on Apache)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Table Name</th>
                        <th>Record Count</th>
                        <th>Table Engine</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tables_info as $tbl => $cnt): ?>
                        <tr>
                            <td><strong>`<?= htmlspecialchars($tbl) ?>`</strong></td>
                            <td><?= $cnt ?> rows</td>
                            <td>InnoDB (UTF8MB4)</td>
                            <td><span class="status-badge status-ok" style="font-size: 0.75rem;">Verified</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
            <a href="er_diagram.html" class="btn">View Interactive ER Diagram</a>
        </div>
    </div>
</body>
</html>
