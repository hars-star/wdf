<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// 1. Verify CSRF Token
if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $_SESSION['error_msg'] = 'Security validation failed: Invalid CSRF token.';
    header('Location: index.php');
    exit;
}

// 2. Retrieve & Sanitize Inputs
$name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS));
$email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
$mobile = trim(filter_input(INPUT_POST, 'mobile', FILTER_SANITIZE_SPECIAL_CHARS));
$department = trim(filter_input(INPUT_POST, 'department', FILTER_SANITIZE_SPECIAL_CHARS));
$inquiry = trim(filter_input(INPUT_POST, 'inquiry', FILTER_SANITIZE_SPECIAL_CHARS));

// 3. Server-Side Validation
$errors = [];

if (empty($name) || strlen($name) < 3) {
    $errors[] = 'Full name must contain at least 3 characters.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}

if (!preg_match('/^[6-9]\d{9}$/', $mobile)) {
    $errors[] = 'Mobile number must be a 10-digit Indian number starting with 6-9.';
}

if (empty($department)) {
    $errors[] = 'Department selection is mandatory.';
}

if (!empty($errors)) {
    $_SESSION['error_msg'] = implode(' ', $errors);
    header('Location: index.php');
    exit;
}

// 4. Prepare Record
$timestamp = date('Y-m-d H:i:s');
$record = [
    'timestamp' => $timestamp,
    'name' => $name,
    'email' => $email,
    'mobile' => $mobile,
    'department' => $department,
    'inquiry' => $inquiry
];

$storageDir = __DIR__ . '/storage';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0777, true);
}

// 5. Store in CSV File (with LOCK_EX)
$csvFile = $storageDir . '/submissions.csv';
$csvExists = file_exists($csvFile);

$fp = fopen($csvFile, 'a');
if ($fp) {
    if (flock($fp, LOCK_EX)) {
        if (!$csvExists) {
            fputcsv($fp, ['Timestamp', 'Full Name', 'Email', 'Mobile', 'Department', 'Inquiry']);
        }
        fputcsv($fp, array_values($record));
        flock($fp, LOCK_UN);
    }
    fclose($fp);
}

// 6. Store in JSON File (with LOCK_EX)
$jsonFile = $storageDir . '/submissions.json';
$existingData = [];
if (file_exists($jsonFile)) {
    $content = file_get_contents($jsonFile);
    $existingData = json_decode($content, true) ?: [];
}

$existingData[] = $record;
file_put_contents($jsonFile, json_encode($existingData, JSON_PRETTY_PRINT), LOCK_EX);

// 7. Regenerate CSRF Token and Redirect
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$_SESSION['success_msg'] = "Thank you, {$name}! Your record was securely validated and saved to both CSV and JSON storage.";

header('Location: index.php');
exit;
