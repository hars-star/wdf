<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

// Inactivity Timeout limit: 15 minutes (900 seconds)
$timeout_duration = 900;

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // Session timed out
    session_unset();
    session_destroy();
    header('Location: login.php?msg=timeout');
    exit;
}
$_SESSION['last_activity'] = time();

// Auto-login via Remember-Me Cookie if session is not set
if (empty($_SESSION['user_id']) && !empty($_COOKIE['studenthub_remember'])) {
    $cookieParts = explode(':', $_COOKIE['studenthub_remember']);
    if (count($cookieParts) === 2) {
        [$selector, $validator] = $cookieParts;
        $pdo = getPDOConnection();
        $stmt = $pdo->prepare("SELECT * FROM remember_tokens WHERE selector = ? AND expires_at > NOW() LIMIT 1");
        $stmt->execute([$selector]);
        $tok = $stmt->fetch();

        if ($tok && hash_equals($tok['token_hash'], hash('sha256', $validator))) {
            // Valid token, fetch user
            $uStmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND status = 'Active' LIMIT 1");
            $uStmt->execute([$tok['user_id']]);
            $user = $uStmt->fetch();

            if ($user) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['last_activity'] = time();
            }
        }
    }
}

// Function to enforce authentication
function requireLogin() {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php?msg=unauthorized');
        exit;
    }
}

// Function to enforce Role-Based Access Control
function requireRole($requiredRole) {
    requireLogin();
    if ($_SESSION['role'] !== $requiredRole) {
        if ($_SESSION['role'] === 'admin') {
            header('Location: admin_dashboard.php');
        } else {
            header('Location: student_dashboard.php');
        }
        exit;
    }
}
