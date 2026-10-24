<?php
require_once __DIR__ . '/auth_check.php';

// Redirect if already logged in
if (!empty($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: student_dashboard.php');
    }
    exit;
}

$error = null;
$msg = $_GET['msg'] ?? '';

if ($msg === 'timeout') {
    $error = 'Your session has expired due to inactivity. Please log in again.';
} elseif ($msg === 'unauthorized') {
    $error = 'You must be logged in to access that page.';
} elseif ($msg === 'logout') {
    $success = 'You have logged out securely.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $remember = !empty($_POST['remember']);

    if (empty($login) || empty($password)) {
        $error = 'Both username/email and password are required.';
    } else {
        $pdo = getPDOConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE (email = ? OR username = ?) LIMIT 1");
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['status'] !== 'Active') {
                $error = 'Your account has been deactivated. Please contact the administrator.';
            } else {
                // Prevent Session Fixation
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['last_activity'] = time();

                // Update last_login timestamp
                $upStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $upStmt->execute([$user['id']]);

                // Handle Remember Me Token
                if ($remember) {
                    $selector = bin2hex(random_bytes(16));
                    $validator = bin2hex(random_bytes(32));
                    $tokenHash = hash('sha256', $validator);
                    $expiresAt = date('Y-m-d H:i:s', time() + 86400 * 30); // 30 days

                    $tokStmt = $pdo->prepare("
                        INSERT INTO remember_tokens (selector, user_id, token_hash, expires_at)
                        VALUES (?, ?, ?, ?)
                    ");
                    $tokStmt->execute([$selector, $user['id'], $tokenHash, $expiresAt]);

                    setcookie('studenthub_remember', "{$selector}:{$validator}", [
                        'expires'  => time() + 86400 * 30,
                        'path'     => '/',
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
                }

                // Role-Based Redirection
                if ($user['role'] === 'admin') {
                    header('Location: admin_dashboard.php');
                } else {
                    header('Location: student_dashboard.php');
                }
                exit;
            }
        } else {
            $error = 'Invalid credentials. Please verify your username/email and password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 10: Secure Role-Based Authentication & Session Management</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .demo-credentials {
            background: #f1f5f9;
            border-left: 4px solid var(--primary);
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 480px;">
        <div class="card">
            <h1>StudentHub Login</h1>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
                Secure authentication with BCRYPT, session regeneration, RBAC redirection, and remember-me token.
            </p>

            <div class="demo-credentials">
                <strong style="color: var(--primary);">Quick Demo Credentials (Click to Autofill):</strong><br>
                <div style="display: flex; flex-direction: column; gap: 0.4rem; margin-top: 0.5rem;">
                    <button type="button" class="btn" style="background: white; color: #1e293b; border: 1px solid #cbd5e1; font-size: 0.8rem; padding: 0.35rem 0.6rem; text-align: left; cursor: pointer;" onclick="fillCreds('admin@studenthub.com', 'admin123')">
                        👑 <strong>Admin:</strong> admin@studenthub.com / admin123 <span style="float: right; color: var(--primary);">⚡ Auto-fill</span>
                    </button>
                    <button type="button" class="btn" style="background: white; color: #1e293b; border: 1px solid #cbd5e1; font-size: 0.8rem; padding: 0.35rem 0.6rem; text-align: left; cursor: pointer;" onclick="fillCreds('hrdhanani2117@gmail.com', 'student123')">
                        🎓 <strong>Student:</strong> hrdhanani2117@gmail.com / student123 <span style="float: right; color: var(--primary);">⚡ Auto-fill</span>
                    </button>
                    <button type="button" class="btn" style="background: white; color: #1e293b; border: 1px solid #cbd5e1; font-size: 0.8rem; padding: 0.35rem 0.6rem; text-align: left; cursor: pointer;" onclick="fillCreds('student@studenthub.com', 'student123')">
                        🎓 <strong>Student:</strong> student@studenthub.com / student123 <span style="float: right; color: var(--primary);">⚡ Auto-fill</span>
                    </button>
                </div>
            </div>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="login">Email or Username</label>
                    <input type="text" id="login" name="login" required placeholder="Enter email or username">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter password">
                </div>

                <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" id="remember" name="remember" style="width: auto;">
                    <label for="remember" style="margin: 0; font-weight: normal; cursor: pointer;">Remember Me (30 Days)</label>
                </div>

                <button type="submit" class="btn" style="width: 100%; margin-top: 0.5rem;">Sign In</button>
            </form>
        </div>
    </div>

    <script>
    function fillCreds(login, pwd) {
        document.getElementById('login').value = login;
        document.getElementById('password').value = pwd;
    }
    </script>
</body>
</html>
