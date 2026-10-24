<?php
session_start();
require_once __DIR__ . '/db.php';

$pdo = getPDOConnection();
$action = $_REQUEST['action'] ?? '';
$uploadDir = __DIR__ . '/uploads/';

// Helper to validate & process poster upload
function handleFileUpload($file, $uploadDir, &$errorMsg) {
    if (empty($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMsg = "File upload failed with error code: " . $file['error'];
        return false;
    }

    // 1. Max File Size Check (2MB = 2 * 1024 * 1024 bytes)
    $maxSize = 2 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        $errorMsg = "Poster file size exceeds the 2MB limit.";
        return false;
    }

    // 2. Allowed Extensions and MIME types check
    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts)) {
        $errorMsg = "Invalid file extension. Only JPG, PNG, and WEBP images are permitted.";
        return false;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedMimes)) {
        $errorMsg = "Invalid MIME type ({$mimeType}). Please upload a valid image file.";
        return false;
    }

    // 3. Safe Unique Filename
    $newFilename = uniqid('poster_', true) . '.' . $ext;
    $destination = $uploadDir . $newFilename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $errorMsg = "Failed to store uploaded file on the server.";
        return false;
    }

    return $newFilename;
}

if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = $_POST['category'] ?? 'Technical';
    $venue = trim($_POST['venue'] ?? '');
    $event_date = $_POST['event_date'] ?? date('Y-m-d');
    $starts_at = $event_date . ' ' . ($_POST['start_time'] ?? '09:00:00');
    $ends_at = $event_date . ' ' . ($_POST['end_time'] ?? '17:00:00');
    $max_seats = intval($_POST['max_seats'] ?? 100);
    $status = $_POST['status'] ?? 'Upcoming';

    $uploadError = '';
    $posterFile = handleFileUpload($_FILES['poster'] ?? [], $uploadDir, $uploadError);

    if ($posterFile === false) {
        $_SESSION['event_error'] = $uploadError;
        header('Location: index.php');
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO events (title, description, category, venue, event_date, starts_at, ends_at, max_seats, poster_file, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$title, $description, $category, $venue, $event_date, $starts_at, $ends_at, $max_seats, $posterFile, $status]);
        $eventId = $pdo->lastInsertId();

        // Audit Log
        $auditStmt = $pdo->prepare("INSERT INTO audit_logs (username, action, entity, details, ip_address) VALUES ('admin', 'EVENT_CREATE', 'Events', ?, '127.0.0.1')");
        $auditStmt->execute(["Created new event: {$title} (ID: {$eventId})"]);

        $_SESSION['event_success'] = "Event '{$title}' created successfully!";
    } catch (PDOException $e) {
        $_SESSION['event_error'] = "Database error: " . $e->getMessage();
    }
    header('Location: index.php');
    exit;
}

if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = $_POST['category'] ?? 'Technical';
    $venue = trim($_POST['venue'] ?? '');
    $event_date = $_POST['event_date'] ?? date('Y-m-d');
    $starts_at = $event_date . ' ' . ($_POST['start_time'] ?? '09:00:00');
    $ends_at = $event_date . ' ' . ($_POST['end_time'] ?? '17:00:00');
    $max_seats = intval($_POST['max_seats'] ?? 100);
    $status = $_POST['status'] ?? 'Upcoming';

    $uploadError = '';
    $newPoster = handleFileUpload($_FILES['poster'] ?? [], $uploadDir, $uploadError);

    if ($newPoster === false) {
        $_SESSION['event_error'] = $uploadError;
        header('Location: index.php');
        exit;
    }

    try {
        if ($newPoster) {
            // Delete old poster if exists
            $oldStmt = $pdo->prepare("SELECT poster_file FROM events WHERE id = ?");
            $oldStmt->execute([$id]);
            $oldFile = $oldStmt->fetchColumn();
            if ($oldFile && file_exists($uploadDir . $oldFile)) {
                @unlink($uploadDir . $oldFile);
            }

            $stmt = $pdo->prepare("
                UPDATE events 
                SET title = ?, description = ?, category = ?, venue = ?, event_date = ?, starts_at = ?, ends_at = ?, max_seats = ?, poster_file = ?, status = ?
                WHERE id = ?
            ");
            $stmt->execute([$title, $description, $category, $venue, $event_date, $starts_at, $ends_at, $max_seats, $newPoster, $status, $id]);
        } else {
            $stmt = $pdo->prepare("
                UPDATE events 
                SET title = ?, description = ?, category = ?, venue = ?, event_date = ?, starts_at = ?, ends_at = ?, max_seats = ?, status = ?
                WHERE id = ?
            ");
            $stmt->execute([$title, $description, $category, $venue, $event_date, $starts_at, $ends_at, $max_seats, $status, $id]);
        }

        $_SESSION['event_success'] = "Event '{$title}' updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['event_error'] = "Update failed: " . $e->getMessage();
    }
    header('Location: index.php');
    exit;
}

if ($action === 'delete') {
    $id = intval($_GET['id'] ?? 0);
    try {
        $oldStmt = $pdo->prepare("SELECT poster_file FROM events WHERE id = ?");
        $oldStmt->execute([$id]);
        $oldFile = $oldStmt->fetchColumn();
        if ($oldFile && file_exists($uploadDir . $oldFile)) {
            @unlink($uploadDir . $oldFile);
        }

        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['event_success'] = "Event #{$id} and associated poster deleted successfully.";
    } catch (PDOException $e) {
        $_SESSION['event_error'] = "Delete failed: " . $e->getMessage();
    }
    header('Location: index.php');
    exit;
}

header('Location: index.php');
exit;
