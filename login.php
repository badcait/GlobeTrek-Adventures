<?php
declare(strict_types=1);

$pageTitle = 'Sign In - GlobeTrek Adventures';
require_once __DIR__ . '/includes/init.php';

if (is_logged_in()) {
    $role = current_user()['role'];
    redirect($role === 'admin' ? 'admin/index.php' : ($role === 'staff' ? 'staff/index.php' : 'dashboard.php'));
}

$email = trim($_POST['email'] ?? '');
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Enter a valid email and password.';
    } elseif (!login_user($email, $password)) {
        $error = 'Invalid email or password.';
    } else {
        $role = current_user()['role'];
        flash('success', 'Welcome back to GlobeTrek.');
        redirect($role === 'admin' ? 'admin/index.php' : ($role === 'staff' ? 'staff/index.php' : 'dashboard.php'));
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">
<main class="auth-minimal">
    <a class="auth-logo" href="index.php" aria-label="Back to GlobeTrek home">
        <img src="assets/images/globe-trek-logo.png" alt="Globe Trek Adventures logo">
    </a>
    <section class="auth-card glass-card">
        <span class="eyebrow">Welcome back</span>
        <h1>Sign in</h1>
        <p class="muted">Access bookings, staff tools, or admin reports.</p>
        <?php if ($error): ?>
            <div class="flash flash-error"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="<?= e($email) ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
            </div>
            <button class="btn btn-primary" type="submit">Sign In</button>
            <p class="muted">No account yet? <a class="accent" href="register.php">Create a customer account</a></p>
        </form>
    </section>
</main>
</body>
</html>
