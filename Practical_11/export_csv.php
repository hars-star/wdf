<?php
require_once __DIR__ . '/db.php';

$mysqli = getMySQLiConnection();
$sql = "SELECT id, enrollment_no, name, email, mobile, department, course, year, gender, gpa, status, created_at FROM students ORDER BY enrollment_no ASC";
$result = $mysqli->query($sql);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=students_roster_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Enrollment No', 'Full Name', 'Email', 'Mobile', 'Department', 'Course', 'Year', 'Gender', 'GPA', 'Status', 'Registration Date']);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit;
