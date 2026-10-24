<?php
session_start();
require_once __DIR__ . '/db.php';

$mysqli = getMySQLiConnection();

$success = $_SESSION['crud_success'] ?? null;
$error = $_SESSION['crud_error'] ?? null;
unset($_SESSION['crud_success'], $_SESSION['crud_error']);

// Filter parameters
$search = trim($_GET['search'] ?? '');
$deptFilter = trim($_GET['dept'] ?? '');
$yearFilter = trim($_GET['year'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');

$page = max(1, intval($_GET['page'] ?? 1));
$limit = 8;
$offset = ($page - 1) * $limit;

// Construct query
$where = ["1=1"];
$params = [];
$types = "";

if ($search !== '') {
    $where[] = "(name LIKE ? OR email LIKE ? OR enrollment_no LIKE ? OR course LIKE ?)";
    $s = "%{$search}%";
    $params[] = $s; $params[] = $s; $params[] = $s; $params[] = $s;
    $types .= "ssss";
}
if ($deptFilter !== '') {
    $where[] = "department = ?";
    $params[] = $deptFilter;
    $types .= "s";
}
if ($yearFilter !== '') {
    $where[] = "year = ?";
    $params[] = intval($yearFilter);
    $types .= "i";
}
if ($statusFilter !== '') {
    $where[] = "status = ?";
    $params[] = $statusFilter;
    $types .= "s";
}

$whereClause = implode(" AND ", $where);

// Count total matching
$countSql = "SELECT COUNT(*) as total FROM students WHERE {$whereClause}";
$countStmt = $mysqli->prepare($countSql);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);
$countStmt->close();

// Fetch paginated rows
$dataSql = "SELECT * FROM students WHERE {$whereClause} ORDER BY id DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$dataStmt = $mysqli->prepare($dataSql);
$dataStmt->bind_param($types, ...$params);
$dataStmt->execute();
$students = $dataStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$dataStmt->close();

// Get unique departments for filter dropdown
$deptRes = $mysqli->query("SELECT DISTINCT department FROM students ORDER BY department ASC");
$departments = $deptRes->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 11: Student Management CRUD with Search & Filter</title>
    <link rel="stylesheet" href="css/crud.css">
</head>
<body>
    <div class="crud-container">
        <header class="header-row">
            <div>
                <h1>Student Management Directory</h1>
                <p style="color: var(--text-muted);">Manage student records with MySQLi prepared statements, dynamic filtering, and CSV export.</p>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <a href="export_csv.php" class="btn btn-outline">📥 Export CSV</a>
                <button type="button" class="btn btn-primary" onclick="openAddModal()">+ Add New Student</button>
            </div>
        </header>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Search & Filter Bar -->
        <form method="GET" action="index.php" class="filter-card">
            <input type="text" name="search" placeholder="Search by Name, Email, Enrollment No..." value="<?= htmlspecialchars($search) ?>">

            <select name="dept" onchange="this.form.submit()">
                <option value="">All Departments</option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?= htmlspecialchars($d['department']) ?>" <?= $deptFilter === $d['department'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d['department']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="year" onchange="this.form.submit()">
                <option value="">All Years</option>
                <option value="1" <?= $yearFilter === '1' ? 'selected' : '' ?>>Year 1</option>
                <option value="2" <?= $yearFilter === '2' ? 'selected' : '' ?>>Year 2</option>
                <option value="3" <?= $yearFilter === '3' ? 'selected' : '' ?>>Year 3</option>
                <option value="4" <?= $yearFilter === '4' ? 'selected' : '' ?>>Year 4</option>
            </select>

            <select name="status" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="Active" <?= $statusFilter === 'Active' ? 'selected' : '' ?>>Active</option>
                <option value="Inactive" <?= $statusFilter === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>

            <button type="submit" class="btn btn-primary">Filter</button>
            <?php if ($search || $deptFilter || $yearFilter || $statusFilter): ?>
                <a href="index.php" class="btn btn-outline">Reset</a>
            <?php endif; ?>
        </form>

        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.9rem; color: var(--text-muted);">
            <span>Found <strong><?= $totalRows ?></strong> matching student records</span>
            <span>Page <?= $page ?> of <?= max(1, $totalPages) ?></span>
        </div>

        <!-- Student Data Table -->
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Enrollment</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Department</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th>GPA</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                        <tr><td colspan="10" style="text-align: center; padding: 2rem; color: var(--text-muted);">No student records found matching criteria.</td></tr>
                    <?php else: ?>
                        <?php foreach ($students as $row): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($row['enrollment_no']) ?></strong></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['mobile']) ?></td>
                                <td><?= htmlspecialchars($row['department']) ?></td>
                                <td><?= htmlspecialchars($row['course']) ?></td>
                                <td>Yr <?= htmlspecialchars($row['year']) ?></td>
                                <td><strong style="color: #16a34a;"><?= htmlspecialchars($row['gpa']) ?></strong></td>
                                <td>
                                    <span class="badge <?= $row['status'] === 'Active' ? 'badge-active' : 'badge-inactive' ?>" 
                                          style="cursor: pointer;" 
                                          onclick='confirmToggleStatus(<?= $row['id'] ?>, "<?= $row['status'] ?>")'
                                          title="Click to toggle status">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-outline btn-sm" onclick='openEditModal(<?= json_encode($row) ?>)'>✏️ Edit</button>
                                    <button class="btn btn-danger btn-sm" onclick='confirmDelete(<?= $row['id'] ?>, <?= json_encode($row['name']) ?>)'>🗑️</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a href="?page=<?= $p ?>&search=<?= urlencode($search) ?>&dept=<?= urlencode($deptFilter) ?>&year=<?= urlencode($yearFilter) ?>&status=<?= urlencode($statusFilter) ?>" 
                       class="page-link <?= $p === $page ? 'active' : '' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal: Add Student -->
    <div id="addStudentModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Add New Student Record</h2>
                <button type="button" class="modal-close" onclick="closeAddModal()">&times;</button>
            </div>
            <form action="student_action.php?action=add" method="POST">
                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Enrollment No *</label>
                        <input type="text" name="enrollment_no" required placeholder="e.g. 23IT022">
                    </div>
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="name" required placeholder="Student Name">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" required placeholder="name@studenthub.com">
                    </div>
                    <div class="form-group">
                        <label>Mobile *</label>
                        <input type="tel" name="mobile" required placeholder="10-digit number" pattern="[6-9][0-9]{9}">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Department *</label>
                        <select name="department" required>
                            <option value="Information Technology">Information Technology</option>
                            <option value="Computer Engineering">Computer Engineering</option>
                            <option value="Computer Science">Computer Science</option>
                            <option value="Electronics & Comm.">Electronics & Communication</option>
                            <option value="Mechanical Engg">Mechanical Engineering</option>
                            <option value="Civil Engineering">Civil Engineering</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Course *</label>
                        <select name="course" required>
                            <option value="B.Tech IT">B.Tech IT</option>
                            <option value="B.Tech CE">B.Tech CE</option>
                            <option value="B.Tech CSE">B.Tech CSE</option>
                            <option value="B.Tech EC">B.Tech EC</option>
                            <option value="B.Tech ME">B.Tech ME</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Year *</label>
                        <select name="year" required>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3" selected>3rd Year</option>
                            <option value="4">4th Year</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Gender *</label>
                        <select name="gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>GPA (0.0 - 4.0) *</label>
                        <input type="number" step="0.01" min="0" max="4" name="gpa" value="3.50" required>
                    </div>
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-outline" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Student</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Student -->
    <div id="editStudentModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Edit Student Details</h2>
                <button type="button" class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <form action="student_action.php?action=edit" method="POST">
                <input type="hidden" id="edit_id" name="id">

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Enrollment No *</label>
                        <input type="text" id="edit_enrollment" name="enrollment_no" required>
                    </div>
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" id="edit_name" name="name" required>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" id="edit_email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Mobile *</label>
                        <input type="tel" id="edit_mobile" name="mobile" required>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Department *</label>
                        <select id="edit_department" name="department" required>
                            <option value="Information Technology">Information Technology</option>
                            <option value="Computer Engineering">Computer Engineering</option>
                            <option value="Computer Science">Computer Science</option>
                            <option value="Electronics & Comm.">Electronics & Communication</option>
                            <option value="Mechanical Engg">Mechanical Engineering</option>
                            <option value="Civil Engineering">Civil Engineering</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Course *</label>
                        <select id="edit_course" name="course" required>
                            <option value="B.Tech IT">B.Tech IT</option>
                            <option value="B.Tech CE">B.Tech CE</option>
                            <option value="B.Tech CSE">B.Tech CSE</option>
                            <option value="B.Tech EC">B.Tech EC</option>
                            <option value="B.Tech ME">B.Tech ME</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>Year *</label>
                        <select id="edit_year" name="year" required>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Gender *</label>
                        <select id="edit_gender" name="gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label>GPA *</label>
                        <input type="number" step="0.01" min="0" max="4" id="edit_gpa" name="gpa" required>
                    </div>
                    <div class="form-group">
                        <label>Status *</label>
                        <select id="edit_status" name="status" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-outline" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Student</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/crud.js"></script>
</body>
</html>
