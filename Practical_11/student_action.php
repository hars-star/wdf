<?php
session_start();
require_once __DIR__ . '/db.php';

$mysqli = getMySQLiConnection();
$action = $_REQUEST['action'] ?? '';

if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $enrollment = trim($_POST['enrollment_no'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $year = intval($_POST['year'] ?? 1);
    $gender = $_POST['gender'] ?? 'Male';
    $gpa = floatval($_POST['gpa'] ?? 3.5);
    $status = $_POST['status'] ?? 'Active';

    // Check duplicate enrollment or email
    $checkStmt = $mysqli->prepare("SELECT id FROM students WHERE enrollment_no = ? OR email = ?");
    $checkStmt->bind_param("ss", $enrollment, $email);
    $checkStmt->execute();
    $checkRes = $checkStmt->get_result();

    if ($checkRes->num_rows > 0) {
        $_SESSION['crud_error'] = "A student with enrollment '{$enrollment}' or email '{$email}' already exists.";
    } else {
        $stmt = $mysqli->prepare("
            INSERT INTO students (enrollment_no, name, email, mobile, department, course, year, gender, gpa, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssssisds", $enrollment, $name, $email, $mobile, $department, $course, $year, $gender, $gpa, $status);
        if ($stmt->execute()) {
            $_SESSION['crud_success'] = "Student '{$name}' ({$enrollment}) successfully added!";
        } else {
            $_SESSION['crud_error'] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
    $checkStmt->close();
    header('Location: index.php');
    exit;
}

if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $enrollment = trim($_POST['enrollment_no'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $year = intval($_POST['year'] ?? 1);
    $gender = $_POST['gender'] ?? 'Male';
    $gpa = floatval($_POST['gpa'] ?? 3.5);
    $status = $_POST['status'] ?? 'Active';

    $stmt = $mysqli->prepare("
        UPDATE students 
        SET enrollment_no = ?, name = ?, email = ?, mobile = ?, department = ?, course = ?, year = ?, gender = ?, gpa = ?, status = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ssssssisdsi", $enrollment, $name, $email, $mobile, $department, $course, $year, $gender, $gpa, $status, $id);
    if ($stmt->execute()) {
        $_SESSION['crud_success'] = "Student #{$id} record updated successfully!";
    } else {
        $_SESSION['crud_error'] = "Update failed: " . $stmt->error;
    }
    $stmt->close();
    header('Location: index.php');
    exit;
}

if ($action === 'delete') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $mysqli->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['crud_success'] = "Student record #{$id} deleted successfully.";
    } else {
        $_SESSION['crud_error'] = "Delete failed: " . $stmt->error;
    }
    $stmt->close();
    header('Location: index.php');
    exit;
}

if ($action === 'toggle_status') {
    $id = intval($_GET['id'] ?? 0);
    $status = $_GET['status'] === 'Active' ? 'Active' : 'Inactive';
    $stmt = $mysqli->prepare("UPDATE students SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    if ($stmt->execute()) {
        $_SESSION['crud_success'] = "Student status updated to '{$status}'.";
    }
    $stmt->close();
    header('Location: index.php');
    exit;
}

header('Location: index.php');
exit;
