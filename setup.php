<?php
declare(strict_types=1);

$pageTitle = 'Database Setup - GlobeTrek Adventures';
$currentPage = '';
require_once __DIR__ . '/includes/init.php';

$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = file_get_contents(__DIR__ . '/database/schema.sql');
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS);
        $connection->set_charset('utf8mb4');
        $connection->multi_query($sql);

        do {
            if ($result = $connection->store_result()) {
                $result->free();
            }
        } while ($connection->more_results() && $connection->next_result());

        $message = 'Database created and seeded successfully. You can now sign in with the demo accounts.';
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container narrow">
        <span class="eyebrow">Local setup</span>
        <h1>Prepare the GlobeTrek database</h1>
        <p>Run this once after starting MySQL in XAMPP. It creates tables and demo data for customers, staff, and administrators.</p>
    </div>
</section>
<section class="section">
    <div class="container narrow">
        <div class="form-card">
            <?php if ($message): ?>
                <div class="flash flash-success"><?= e($message) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="flash flash-error"><?= e($error) ?></div>
            <?php endif; ?>
            <h2>Demo accounts</h2>
            <p class="muted">Admin: admin@globetrek.local / admin123</p>
            <p class="muted">Staff: staff@globetrek.local / staff123</p>
            <p class="muted">Customer: customer@globetrek.local / customer123</p>
            <form method="post">
                <button class="btn btn-primary" type="submit">Create / Reset Database</button>
                <a class="btn btn-outline" href="index.php">Back to Website</a>
            </form>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

