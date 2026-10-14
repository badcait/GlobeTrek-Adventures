<?php
declare(strict_types=1);

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    static $user = null;
    if ($user !== null) {
        return $user;
    }

    try {
        $stmt = db()->prepare('SELECT id, name, email, phone, role, created_at FROM users WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $user = first_or_null($stmt);
    } catch (Throwable) {
        $user = null;
    }

    if (!$user) {
        unset($_SESSION['user_id']);
    }

    return $user;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash('warning', 'Please sign in to continue.');
        redirect('login.php');
    }
}

function require_role(array $roles): void
{
    require_login();
    $user = current_user();

    if (!$user || !in_array($user['role'], $roles, true)) {
        http_response_code(403);
        $home = str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/admin/') || str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/staff/') ? '../index.php' : 'index.php';
        require __DIR__ . '/header.php';
        echo '<section class="section"><div class="container narrow"><h1>Access denied</h1><p class="muted">Your account does not have permission to view this page.</p><a class="btn btn-primary" href="' . e($home) . '">Back to home</a></div></section>';
        require __DIR__ . '/footer.php';
        exit;
    }
}

function login_user(string $email, string $password): bool
{
    $stmt = db()->prepare('SELECT id, password_hash FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = first_or_null($stmt);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }

    $_SESSION['user_id'] = (int) $user['id'];
    return true;
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
