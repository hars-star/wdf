<?php
session_start();
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// 1. Verify CSRF Token
if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $_SESSION['reg_error'] = 'Security validation failed: Invalid CSRF token.';
    header('Location: index.php');
    exit;
}

// 2. Sanitize and retrieve fields
$name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS));
$username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
$email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
$mobile = trim(filter_input(INPUT_POST, 'mobile', FILTER_SANITIZE_SPECIAL_CHARS));
$course = trim(filter_input(INPUT_POST, 'course', FILTER_SANITIZE_SPECIAL_CHARS));
$password = $_POST['password'] ?? '';
$confirm_pass = $_POST['confirm_password'] ?? '';

// 3. Validation
$errors = [];

if (strlen($name) < 3) $errors[] = 'Full name must have at least 3 characters.';
if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) $errors[] = 'Username must be 3-20 alphanumeric characters or underscores.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
if (!preg_match('/^[6-9]\d{9}$/', $mobile)) $errors[] = 'Valid 10-digit mobile number required.';
if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters long.';
if ($password !== $confirm_pass) $errors[] = 'Passwords do not match.';

if (!empty($errors)) {
    $_SESSION['reg_error'] = implode(' ', $errors);
    header('Location: index.php');
    exit;
}

$pdo = getPDOConnection();

try {
    // 4. Duplicate Check using Prepared Statement
    $checkStmt = $pdo->prepare("SELECT id, email, username FROM users WHERE email = ? OR username = ? LIMIT 1");
    $checkStmt->execute([$email, $username]);
    $existing = $checkStmt->fetch();

    if ($existing) {
        if ($existing['email'] === $email) {
            $_SESSION['reg_error'] = 'An account with this email address already exists.';
        } else {
            $_SESSION['reg_error'] = 'This username is already taken. Please choose another.';
        }
        header('Location: index.php');
        exit;
    }

    // 5. Secure Password Hashing
    $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    // 6. Insert User into MySQL
    $insertStmt = $pdo->prepare("
        INSERT INTO users (name, username, email, mobile, password_hash, role, course, year, gender, status)
        VALUES (?, ?, ?, ?, ?, 'student', ?, 3, 'Male', 'Active')
    ");
    $insertStmt->execute([$name, $username, $email, $mobile, $password_hash, $course]);
    $newUserId = $pdo->lastInsertId();

    // 7. Audit Log Entry
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $auditStmt = $pdo->prepare("
        INSERT INTO audit_logs (user_id, username, action, entity, details, ip_address)
        VALUES (?, ?, 'USER_REGISTRATION', 'Users', ?, ?)
    ");
    $auditStmt->execute([$newUserId, $username, "New student user registered: {$name} ({$email})", $ip]);

    // Reset CSRF token
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['reg_success'] = "Account successfully registered! User ID #{$newUserId} created in MySQL database with hashed password.";

    header('Location: index.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['reg_error'] = "Database error: " . $e->getMessage();
    header('Location: index.php');
    exit;
}
