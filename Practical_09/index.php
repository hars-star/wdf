<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success = $_SESSION['reg_success'] ?? null;
$error = $_SESSION['reg_error'] ?? null;
unset($_SESSION['reg_success'], $_SESSION['reg_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 9: Secure Registration with MySQL & Password Hashing</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container" style="max-width: 650px;">
        <div class="card">
            <h1>Secure Student Registration</h1>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
                Registration module with prepared statement duplicate checks, bcrypt hashing, and audit logging.
            </p>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="register_process.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" required placeholder="e.g. Harshit Dhanani">
                </div>

                <div class="form-group">
                    <label for="username">Username (Unique) *</label>
                    <input type="text" id="username" name="username" required placeholder="e.g. harshit_it">
                </div>

                <div class="form-group">
                    <label for="email">Email Address (Unique) *</label>
                    <input type="email" id="email" name="email" required placeholder="harshit@charusat.edu.in">
                </div>

                <div class="form-group">
                    <label for="mobile">Mobile Number (10 digits) *</label>
                    <input type="tel" id="mobile" name="mobile" required placeholder="9898123456" pattern="[6-9][0-9]{9}">
                </div>

                <div class="form-group">
                    <label for="course">Academic Course *</label>
                    <select id="course" name="course" required>
                        <option value="B.Tech IT">B.Tech Information Technology</option>
                        <option value="B.Tech CE">B.Tech Computer Engineering</option>
                        <option value="B.Tech CSE">B.Tech Computer Science</option>
                        <option value="B.Tech EC">B.Tech Electronics & Comm.</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="password">Account Password *</label>
                    <input type="password" id="password" name="password" required minlength="8" placeholder="At least 8 characters">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                </div>

                <button type="submit" class="btn" style="width: 100%; margin-top: 1rem;">Register Account</button>
            </form>
        </div>
    </div>
</body>
</html>
