<?php
session_start();
require_once __DIR__ . '/db.php';

$id = intval($_GET['id'] ?? 0);
$type = $_GET['type'] ?? 'student'; // 'user' or 'student'
$status = $_GET['status'] === 'Active' ? 'Active' : 'Inactive';

if ($id > 0) {
    $pdo = getPDOConnection();
    if ($type === 'user') {
        $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE students SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }

    // Insert Audit Log Entry
    $auditStmt = $pdo->prepare("
        INSERT INTO audit_logs (username, action, entity, details, ip_address)
        VALUES ('admin', 'STATUS_TOGGLE', ?, ?, '127.0.0.1')
    ");
    $auditStmt->execute([ucfirst($type), "Changed status of {$type} #{$id} to {$status}."]);

    $_SESSION['dash_msg'] = "Status of {$type} #{$id} changed to {$status}. Logged in audit trail.";
}

header('Location: index.php');
exit;
