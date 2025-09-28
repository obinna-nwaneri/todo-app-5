<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';

if (is_admin_logged_in()) {
    header('Location: /admin/dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo instanceof PDO) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please provide both an email address and password.';
    } else {
        $user = verify_admin_credentials($pdo, $email, $password);

        if ($user === null) {
            $error = 'Invalid credentials or insufficient permissions.';
        } else {
            login_admin($user);
            header('Location: /admin/dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin login</title>
    <link rel="stylesheet" href="/assets/styles.css">
</head>
<body class="auth-layout">
    <main class="auth-panel">
        <h1>Admin login</h1>
        <?php if ($dbError !== null): ?>
            <div class="alert alert-error">Database connection error: <?= htmlspecialchars($dbError); ?></div>
        <?php endif; ?>

        <?php if ($error !== null): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" class="stack">
            <label>
                <span>Email address</span>
                <input type="email" name="email" required autofocus>
            </label>
            <label>
                <span>Password</span>
                <input type="password" name="password" required>
            </label>
            <button type="submit" class="button">Sign in</button>
        </form>
        <p class="muted"><a href="/">Back to the todo list</a></p>
    </main>
</body>
</html>
