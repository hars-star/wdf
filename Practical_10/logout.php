<?php
session_start();
require_once __DIR__ . '/db.php';

// Revoke remember-me token if cookie present
if (!empty($_COOKIE['studenthub_remember'])) {
    $parts = explode(':', $_COOKIE['studenthub_remember']);
    if (count($parts) === 2) {
        $selector = $parts[0];
        try {
            $pdo = getPDOConnection();
            $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE selector = ?");
            $stmt->execute([$selector]);
        } catch (Exception $e) {
            // Ignore DB errors during logout
        }
    }
    // Expire cookie
    setcookie('studenthub_remember', '', time() - 3600, '/');
}

// Clear Session
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

header('Location: login.php?msg=logout');
exit;
