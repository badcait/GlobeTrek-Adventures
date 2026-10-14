<?php
declare(strict_types=1);

$pageTitle = 'Contact GlobeTrek Adventures';
$currentPage = 'contact';
require_once __DIR__ . '/includes/init.php';

$user = current_user();
$errors = [];
$values = [
    'name' => $user['name'] ?? '',
    'email' => $user['email'] ?? '',
    'subject' => '',
    'message' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = [
        'name' => trim($_POST['name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'subject' => trim($_POST['subject'] ?? ''),
        'message' => trim($_POST['message'] ?? ''),
    ];

    if ($values['name'] === '') {
        $errors[] = 'Name is required.';
    }
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }
    if ($values['subject'] === '') {
        $errors[] = 'Subject is required.';
    }
    if (mb_strlen($values['message']) < 10) {
        $errors[] = 'Message should be at least 10 characters.';
    }

    if (!$errors) {
        $userId = $user ? (int) $user['id'] : null;
        $stmt = db()->prepare('INSERT INTO queries (user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('issss', $userId, $values['name'], $values['email'], $values['subject'], $values['message']);
        $stmt->execute();
        flash('success', 'Your query has been sent to GlobeTrek staff.');
        redirect('contact.php');
    }
}

require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container narrow">
        <span class="eyebrow">Customer support</span>
        <h1>Submit travel questions and arrangement requests.</h1>
        <p>Staff can review and respond to customer queries from the staff dashboard.</p>
    </div>
</section>
<section class="section">
    <div class="container narrow">
        <form class="form-card" method="post">
            <?php foreach ($errors as $error): ?>
                <div class="flash flash-error"><?= e($error) ?></div>
            <?php endforeach; ?>
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input id="name" name="name" value="<?= e($values['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="<?= e($values['email']) ?>" required>
                </div>
                <div class="form-group full">
                    <label for="subject">Subject</label>
                    <input id="subject" name="subject" value="<?= e($values['subject']) ?>" required>
                </div>
                <div class="form-group full">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="6" required><?= e($values['message']) ?></textarea>
                </div>
            </div>
            <button class="btn btn-primary" type="submit">Submit Query</button>
        </form>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

