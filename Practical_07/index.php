<?php
session_start();

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success_msg = $_SESSION['success_msg'] ?? null;
$error_msg = $_SESSION['error_msg'] ?? null;
unset($_SESSION['success_msg'], $_SESSION['error_msg']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 7: PHP Form Processing & File Storage</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Student Inquiry Registration</h1>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
                Server-side form processing with validation, sanitization, CSRF token security, and dual CSV & JSON storage.
            </p>

            <?php if ($success_msg): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
            <?php endif; ?>

            <?php if ($error_msg): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
            <?php endif; ?>

            <form action="process.php" method="POST">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" required placeholder="e.g. Harshit Dhanani">
                </div>

                <div class="form-group">
                    <label for="email">Student Email Address *</label>
                    <input type="email" id="email" name="email" required placeholder="name@charusat.edu.in">
                </div>

                <div class="form-group">
                    <label for="mobile">Mobile Number *</label>
                    <input type="tel" id="mobile" name="mobile" required placeholder="10-digit mobile number">
                </div>

                <div class="form-group">
                    <label for="department">Department *</label>
                    <select id="department" name="department" required>
                        <option value="">-- Select Department --</option>
                        <option value="Information Technology">Information Technology</option>
                        <option value="Computer Engineering">Computer Engineering</option>
                        <option value="Computer Science">Computer Science</option>
                        <option value="Electronics & Comm.">Electronics & Communication</option>
                        <option value="Mechanical Engineering">Mechanical Engineering</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="inquiry">Inquiry or Event Interest</label>
                    <textarea id="inquiry" name="inquiry" rows="4" placeholder="Briefly state your academic inquiry or event interest..."></textarea>
                </div>

                <div style="display: flex; gap: 1rem; align-items: center; margin-top: 1.5rem;">
                    <button type="submit" class="btn">Submit to Server</button>
                    <a href="view_submissions.php" class="btn btn-secondary">View Stored Records</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
