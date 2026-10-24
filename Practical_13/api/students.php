<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../db.php';

$pdo = getPDOConnection();
$method = $_SERVER['REQUEST_METHOD'];

// Helper to send structured JSON
function sendResponse($success, $message, $data = null, $code = 200) {
    http_response_code($code);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data'    => $data,
        'timestamp' => date('c')
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 1. GET: Retrieve all or single student
if ($method === 'GET') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    $search = trim($_GET['search'] ?? '');
    $dept = trim($_GET['department'] ?? '');

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $student = $stmt->fetch();
        if ($student) {
            sendResponse(true, "Student found", $student);
        } else {
            sendResponse(false, "Student not found", null, 404);
        }
    }

    $sql = "SELECT * FROM students WHERE 1=1";
    $params = [];

    if ($search !== '') {
        $sql .= " AND (name LIKE ? OR email LIKE ? OR enrollment_no LIKE ? OR course LIKE ?)";
        $s = "%{$search}%";
        $params = array_merge($params, [$s, $s, $s, $s]);
    }
    if ($dept !== '') {
        $sql .= " AND department = ?";
        $params[] = $dept;
    }
    $sql .= " ORDER BY id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $list = $stmt->fetchAll();

    sendResponse(true, "Students retrieved successfully", $list);
}

// Parse request body for POST/PUT/DELETE
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$action = $input['action'] ?? ($method === 'DELETE' ? 'delete' : ($method === 'PUT' ? 'update' : 'create'));

// 2. CREATE Student
if ($action === 'create') {
    $enrollment = trim($input['enrollment_no'] ?? '');
    $name = trim($input['name'] ?? '');
    $email = trim($input['email'] ?? '');
    $mobile = trim($input['mobile'] ?? '');
    $department = trim($input['department'] ?? '');
    $course = trim($input['course'] ?? '');
    $year = intval($input['year'] ?? 1);
    $gender = $input['gender'] ?? 'Male';
    $gpa = floatval($input['gpa'] ?? 3.5);
    $status = $input['status'] ?? 'Active';

    if (empty($enrollment) || empty($name) || empty($email)) {
        sendResponse(false, "Enrollment, name, and email are mandatory fields.", null, 400);
    }

    // Check duplicate
    $check = $pdo->prepare("SELECT id FROM students WHERE enrollment_no = ? OR email = ?");
    $check->execute([$enrollment, $email]);
    if ($check->fetch()) {
        sendResponse(false, "Duplicate enrollment number or email address.", null, 400);
    }

    $stmt = $pdo->prepare("
        INSERT INTO students (enrollment_no, name, email, mobile, department, course, year, gender, gpa, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$enrollment, $name, $email, $mobile, $department, $course, $year, $gender, $gpa, $status]);
    $newId = $pdo->lastInsertId();

    sendResponse(true, "Student registered successfully without page reload!", ['id' => $newId], 201);
}

// 3. UPDATE Student
if ($action === 'update') {
    $id = intval($input['id'] ?? 0);
    if (!$id) sendResponse(false, "Missing student ID.", null, 400);

    $enrollment = trim($input['enrollment_no'] ?? '');
    $name = trim($input['name'] ?? '');
    $email = trim($input['email'] ?? '');
    $mobile = trim($input['mobile'] ?? '');
    $department = trim($input['department'] ?? '');
    $course = trim($input['course'] ?? '');
    $year = intval($input['year'] ?? 1);
    $gender = $input['gender'] ?? 'Male';
    $gpa = floatval($input['gpa'] ?? 3.5);
    $status = $input['status'] ?? 'Active';

    $stmt = $pdo->prepare("
        UPDATE students 
        SET enrollment_no = ?, name = ?, email = ?, mobile = ?, department = ?, course = ?, year = ?, gender = ?, gpa = ?, status = ?
        WHERE id = ?
    ");
    $stmt->execute([$enrollment, $name, $email, $mobile, $department, $course, $year, $gender, $gpa, $status, $id]);

    sendResponse(true, "Student #{$id} updated asynchronously!", ['id' => $id]);
}

// 4. DELETE Student
if ($action === 'delete') {
    $id = intval($input['id'] ?? ($_GET['id'] ?? 0));
    if (!$id) sendResponse(false, "Missing student ID.", null, 400);

    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$id]);

    sendResponse(true, "Student #{$id} deleted successfully from database!");
}

sendResponse(false, "Invalid API request or unsupported action.", null, 405);
