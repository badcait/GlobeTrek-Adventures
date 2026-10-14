<?php
declare(strict_types=1);

$pageTitle = 'Admin Dashboard - GlobeTrek Adventures';
require_once __DIR__ . '/../includes/init.php';
require_role(['admin']);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_staff') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
            $errors[] = 'Staff name, valid email, and 6 character password are required.';
        } else {
            try {
                $role = 'staff';
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = db()->prepare('INSERT INTO users (name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, ?)');
                $stmt->bind_param('sssss', $name, $email, $phone, $hash, $role);
                $stmt->execute();
                flash('success', 'Staff account created.');
                redirect('index.php');
            } catch (mysqli_sql_exception $exception) {
                $errors[] = str_contains($exception->getMessage(), 'Duplicate') ? 'That email is already used.' : 'Staff account could not be created.';
            }
        }
    }

    if ($action === 'role_update') {
        $userId = (int) ($_POST['user_id'] ?? 0);
        $role = $_POST['role'] ?? 'customer';

        if ($userId > 0 && in_array($role, ['customer', 'staff', 'admin'], true)) {
            $stmt = db()->prepare('UPDATE users SET role = ? WHERE id = ?');
            $stmt->bind_param('si', $role, $userId);
            $stmt->execute();
            flash('success', 'User role updated.');
            redirect('index.php');
        }
    }
}

$stats = [
    'users' => (int) db()->query('SELECT COUNT(*) AS total FROM users')->fetch_assoc()['total'],
    'bookings' => (int) db()->query('SELECT COUNT(*) AS total FROM bookings')->fetch_assoc()['total'],
    'revenue' => (float) db()->query("SELECT COALESCE(SUM(amount), 0) AS total FROM payments WHERE status = 'paid'")->fetch_assoc()['total'],
    'queries' => (int) db()->query("SELECT COUNT(*) AS total FROM queries WHERE status = 'open'")->fetch_assoc()['total'],
];

$users = db()->query('SELECT id, name, email, phone, role, created_at FROM users ORDER BY created_at DESC')->fetch_all(MYSQLI_ASSOC);
$sales = db()->query(
    "SELECT d.name AS destination_name, COUNT(b.id) AS bookings, COALESCE(SUM(pay.amount), 0) AS revenue
     FROM destinations d
     LEFT JOIN bookings b ON b.destination_id = d.id
     LEFT JOIN payments pay ON pay.booking_id = b.id
     GROUP BY d.id, d.name
     ORDER BY revenue DESC"
)->fetch_all(MYSQLI_ASSOC);
$recentPayments = db()->query(
    "SELECT pay.*, u.name AS customer_name, p.title AS package_title
     FROM payments pay
     JOIN bookings b ON b.id = pay.booking_id
     JOIN users u ON u.id = b.user_id
     JOIN packages p ON p.id = b.package_id
     ORDER BY pay.created_at DESC
     LIMIT 8"
)->fetch_all(MYSQLI_ASSOC);

require __DIR__ . '/../includes/header.php';
?>
<section class="dashboard-hero">
    <div class="container">
        <span class="eyebrow">Administrator</span>
        <h1>Oversee users, bookings, reports, and data security.</h1>
        <p class="muted">This dashboard demonstrates the administrator role and reporting requirement.</p>
        <div class="grid grid-4">
            <div class="stat"><strong><?= $stats['users'] ?></strong><span>Users</span></div>
            <div class="stat"><strong><?= $stats['bookings'] ?></strong><span>Bookings</span></div>
            <div class="stat"><strong><?= money($stats['revenue']) ?></strong><span>Paid revenue</span></div>
            <div class="stat"><strong><?= $stats['queries'] ?></strong><span>Open queries</span></div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container grid grid-2">
        <form class="form-card" method="post">
            <input type="hidden" name="action" value="create_staff">
            <h2>Create staff account</h2>
            <?php foreach ($errors as $error): ?>
                <div class="flash flash-error"><?= e($error) ?></div>
            <?php endforeach; ?>
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone">
                </div>
                <div class="form-group">
                    <label for="password">Temporary password</label>
                    <input id="password" type="password" name="password" minlength="6" required>
                </div>
            </div>
            <button class="btn btn-primary" type="submit">Create Staff</button>
        </form>

        <div class="panel">
            <h2>Sales by destination</h2>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Destination</th><th>Bookings</th><th>Revenue</th></tr></thead>
                    <tbody>
                    <?php foreach ($sales as $row): ?>
                        <tr>
                            <td><?= e($row['destination_name']) ?></td>
                            <td><?= (int) $row['bookings'] ?></td>
                            <td><?= money($row['revenue']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<section class="section section-soft">
    <div class="container">
        <div class="section-heading">
            <div>
                <h2>User management</h2>
                <p>Administrators can manage account roles for customers, staff, and admins.</p>
            </div>
        </div>
        <div class="table-wrap panel">
            <table>
                <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Created</th><th>Update</th></tr></thead>
                <tbody>
                <?php foreach ($users as $account): ?>
                    <tr>
                        <td><?= e($account['name']) ?></td>
                        <td><?= e($account['email']) ?></td>
                        <td><?= e($account['phone']) ?></td>
                        <td><span class="badge"><?= e(status_label($account['role'])) ?></span></td>
                        <td><?= e($account['created_at']) ?></td>
                        <td>
                            <form class="inline-form" method="post">
                                <input type="hidden" name="action" value="role_update">
                                <input type="hidden" name="user_id" value="<?= (int) $account['id'] ?>">
                                <select name="role">
                                    <option value="customer" <?= $account['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                                    <option value="staff" <?= $account['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                                    <option value="admin" <?= $account['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                                <button class="btn btn-outline" type="submit">Save</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <div>
                <h2>Recent payments</h2>
                <p>Simulated secure payments recorded against customer bookings.</p>
            </div>
        </div>
        <div class="table-wrap panel">
            <table>
                <thead><tr><th>Reference</th><th>Customer</th><th>Package</th><th>Method</th><th>Amount</th><th>Date</th></tr></thead>
                <tbody>
                <?php foreach ($recentPayments as $payment): ?>
                    <tr>
                        <td><?= e($payment['transaction_ref']) ?></td>
                        <td><?= e($payment['customer_name']) ?></td>
                        <td><?= e($payment['package_title']) ?></td>
                        <td><?= e($payment['method']) ?></td>
                        <td><?= money($payment['amount']) ?></td>
                        <td><?= e($payment['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$recentPayments): ?>
                    <tr><td colspan="6">No payments recorded yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>

