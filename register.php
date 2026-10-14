<?php
declare(strict_types=1);

$pageTitle = 'Create Account - GlobeTrek Adventures';
require_once __DIR__ . '/includes/init.php';

$errors = [];
$values = ['name' => '', 'email' => '', 'phone' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = [
        'name' => trim($_POST['name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
    ];
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($values['name'] === '') {
        $errors[] = 'Name is required.';
    }
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email is required.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = db()->prepare("INSERT INTO users (name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, 'customer')");
            $stmt->bind_param('ssss', $values['name'], $values['email'], $values['phone'], $hash);
            $stmt->execute();
            login_user($values['email'], $password);
            flash('success', 'Account created. Start planning your trip.');
            redirect('planner.php');
        } catch (mysqli_sql_exception $exception) {
            $errors[] = str_contains($exception->getMessage(), 'Duplicate') ? 'This email is already registered.' : 'Registration failed. Please try again.';
        }
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
        <span class="eyebrow">Begin your journey</span>
        <h1>Create account</h1>
        <p class="muted">A minimal customer profile for bookings and travel queries.</p>
        <?php foreach ($errors as $error): ?>
            <div class="flash flash-error"><?= e($error) ?></div>
        <?php endforeach; ?>
        <form method="post">
            <div class="form-group">
                <label for="name">Full name</label>
                <input id="name" name="name" value="<?= e($values['name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="<?= e($values['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input id="phone" name="phone" value="<?= e($values['phone']) ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" minlength="6" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm password</label>
                <input id="confirm_password" type="password" name="confirm_password" minlength="6" required>
            </div>
            <button class="btn btn-primary" type="submit">Create Account</button>
            <p class="muted">Already registered? <a class="accent" href="login.php">Sign in</a></p>
        </form>
    </section>
</main>
</body>
</html>
