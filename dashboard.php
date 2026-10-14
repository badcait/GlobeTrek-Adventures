<?php
declare(strict_types=1);

$pageTitle = 'Customer Dashboard - GlobeTrek Adventures';
require_once __DIR__ . '/includes/init.php';
require_role(['customer']);

$user = current_user();
$userId = (int) $user['id'];

$stmt = db()->prepare(
    "SELECT b.*, p.title AS package_title, d.name AS destination_name, pay.transaction_ref
     FROM bookings b
     JOIN packages p ON p.id = b.package_id
     JOIN destinations d ON d.id = b.destination_id
     LEFT JOIN payments pay ON pay.booking_id = b.id
     WHERE b.user_id = ?
     ORDER BY b.created_at DESC"
);
$stmt->bind_param('i', $userId);
$stmt->execute();
$bookings = fetch_all_assoc($stmt);

$stmt = db()->prepare('SELECT * FROM queries WHERE user_id = ? OR email = ? ORDER BY created_at DESC');
$stmt->bind_param('is', $userId, $user['email']);
$stmt->execute();
$queries = fetch_all_assoc($stmt);

$paidCount = 0;
$totalSpent = 0.0;
foreach ($bookings as $booking) {
    if (in_array($booking['status'], ['paid', 'confirmed', 'completed'], true)) {
        $paidCount++;
        $totalSpent += (float) $booking['total_amount'];
    }
}

require __DIR__ . '/includes/header.php';
?>
<section class="dashboard-hero">
    <div class="container">
        
        <h1>Hello, <?= e($user['name']) ?></h1>
        <p class="muted">Manage your travel plans, payment status, and queries.</p>
        <div class="grid grid-3">
            <div class="stat"><strong><?= count($bookings) ?></strong><span>Total bookings</span></div>
            <div class="stat"><strong><?= $paidCount ?></strong><span>Paid bookings</span></div>
            <div class="stat"><strong><?= money($totalSpent) ?></strong><span>Paid value</span></div>
        </div>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="section-heading">
            <div>
                <h2>Your bookings</h2>
                <p>Bookings move from pending payment to paid, then staff confirmation.</p>
            </div>
            <a class="btn btn-primary" href="planner.php">New Booking</a>
        </div>
        <div class="table-wrap panel">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Package</th>
                    <th>Destination</th>
                    <th>Dates</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td>#<?= (int) $booking['id'] ?></td>
                        <td><?= e($booking['package_title']) ?></td>
                        <td><?= e($booking['destination_name']) ?></td>
                        <td><?= e($booking['start_date']) ?> to <?= e($booking['end_date']) ?></td>
                        <td><?= money($booking['total_amount']) ?></td>
                        <td><span class="badge"><?= e(status_label($booking['status'])) ?></span></td>
                        <td>
                            <?php if ($booking['status'] === 'pending_payment'): ?>
                                <a class="btn btn-outline" href="payment.php?booking=<?= (int) $booking['id'] ?>">Pay</a>
                            <?php else: ?>
                                <?= e($booking['transaction_ref'] ?? 'Awaiting staff action') ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$bookings): ?>
                    <tr><td colspan="7">No bookings yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<section class="section section-soft">
    <div class="container">
        <div class="section-heading">
            <div>
                <h2>Your queries</h2>
                <p>Messages submitted through the contact form and staff responses.</p>
            </div>
            <a class="btn btn-outline" href="contact.php">Submit Query</a>
        </div>
        <div class="grid grid-2">
            <?php foreach ($queries as $query): ?>
                <article class="panel">
                    <span class="badge"><?= e(status_label($query['status'])) ?></span>
                    <h3><?= e($query['subject']) ?></h3>
                    <p><?= e($query['message']) ?></p>
                    <?php if ($query['response']): ?>
                        <p><strong>Staff response:</strong> <?= e($query['response']) ?></p>
                    <?php endif; ?>
                    <p class="muted"><?= e($query['created_at']) ?></p>
                </article>
            <?php endforeach; ?>
            <?php if (!$queries): ?>
                <div class="panel"><p>No queries yet.</p></div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

