<?php
declare(strict_types=1);

$pageTitle = 'Staff Dashboard - GlobeTrek Adventures';
require_once __DIR__ . '/../includes/init.php';
require_role(['staff', 'admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'booking_status') {
        $bookingId = (int) ($_POST['booking_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $allowed = ['confirmed', 'completed', 'cancelled'];

        if ($bookingId > 0 && in_array($status, $allowed, true)) {
            $stmt = db()->prepare('UPDATE bookings SET status = ? WHERE id = ?');
            $stmt->bind_param('si', $status, $bookingId);
            $stmt->execute();
            flash('success', 'Booking status updated.');
        } else {
            flash('error', 'Invalid booking status update.');
        }
    }

    if ($action === 'query_response') {
        $queryId = (int) ($_POST['query_id'] ?? 0);
        $response = trim($_POST['response'] ?? '');

        if ($queryId > 0 && $response !== '') {
            $status = 'answered';
            $stmt = db()->prepare('UPDATE queries SET response = ?, status = ? WHERE id = ?');
            $stmt->bind_param('ssi', $response, $status, $queryId);
            $stmt->execute();
            flash('success', 'Customer query answered.');
        } else {
            flash('error', 'Response text is required.');
        }
    }

    if ($action === 'package_update') {
        $packageId = (int) ($_POST['package_id'] ?? 0);
        $price = (float) ($_POST['base_price'] ?? 0);
        $status = $_POST['status'] ?? 'active';

        if ($packageId > 0 && $price > 0 && in_array($status, ['active', 'paused'], true)) {
            $stmt = db()->prepare('UPDATE packages SET base_price = ?, status = ? WHERE id = ?');
            $stmt->bind_param('dsi', $price, $status, $packageId);
            $stmt->execute();
            flash('success', 'Package details updated.');
        } else {
            flash('error', 'Package price and status are required.');
        }
    }

    redirect('index.php');
}

$bookings = db()->query(
    "SELECT b.*, u.name AS customer_name, u.email AS customer_email, p.title AS package_title,
            d.name AS destination_name, a.name AS accommodation_name, v.name AS vehicle_name, g.name AS guide_name
     FROM bookings b
     JOIN users u ON u.id = b.user_id
     JOIN packages p ON p.id = b.package_id
     JOIN destinations d ON d.id = b.destination_id
     LEFT JOIN accommodations a ON a.id = b.accommodation_id
     LEFT JOIN vehicles v ON v.id = b.vehicle_id
     LEFT JOIN guides g ON g.id = b.guide_id
     ORDER BY b.created_at DESC"
)->fetch_all(MYSQLI_ASSOC);

$queries = db()->query('SELECT * FROM queries ORDER BY created_at DESC')->fetch_all(MYSQLI_ASSOC);
$packages = db()->query(
    "SELECT p.*, d.name AS destination_name
     FROM packages p
     JOIN destinations d ON d.id = p.destination_id
     ORDER BY p.title"
)->fetch_all(MYSQLI_ASSOC);

$paidBookings = array_filter($bookings, fn(array $booking): bool => in_array($booking['status'], ['paid', 'confirmed', 'completed'], true));
$openQueries = array_filter($queries, fn(array $query): bool => $query['status'] === 'open');

require __DIR__ . '/../includes/header.php';
?>
<section class="dashboard-hero">
    <div class="container">
        <span class="eyebrow">Travel agency staff</span>
        <h1>Coordinate bookings, packages, and customer queries.</h1>
        <p class="muted">This dashboard demonstrates the staff role required by the assignment brief.</p>
        <div class="grid grid-3">
            <div class="stat"><strong><?= count($bookings) ?></strong><span>Total bookings</span></div>
            <div class="stat"><strong><?= count($paidBookings) ?></strong><span>Paid / confirmed</span></div>
            <div class="stat"><strong><?= count($openQueries) ?></strong><span>Open queries</span></div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <div>
                <h2>Booking coordination</h2>
                <p>Confirm paid bookings, complete trips, or cancel arrangements.</p>
            </div>
        </div>
        <div class="table-wrap panel">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Trip</th>
                    <th>Services</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Update</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td>#<?= (int) $booking['id'] ?></td>
                        <td><?= e($booking['customer_name']) ?><br><span class="muted"><?= e($booking['customer_email']) ?></span></td>
                        <td><?= e($booking['package_title']) ?><br><span class="muted"><?= e($booking['destination_name']) ?> · <?= e($booking['start_date']) ?> to <?= e($booking['end_date']) ?></span></td>
                        <td><?= e($booking['accommodation_name'] ?? 'No stay') ?> / <?= e($booking['vehicle_name'] ?? 'No vehicle') ?> / <?= e($booking['guide_name'] ?? 'No guide') ?></td>
                        <td><?= money($booking['total_amount']) ?></td>
                        <td><span class="badge"><?= e(status_label($booking['status'])) ?></span></td>
                        <td>
                            <form class="inline-form" method="post">
                                <input type="hidden" name="action" value="booking_status">
                                <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
                                <select name="status">
                                    <option value="confirmed">Confirm</option>
                                    <option value="completed">Complete</option>
                                    <option value="cancelled">Cancel</option>
                                </select>
                                <button class="btn btn-outline" type="submit">Save</button>
                            </form>
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
                <h2>Customer inquiries</h2>
                <p>Respond to submitted travel arrangement questions.</p>
            </div>
        </div>
        <div class="grid grid-2">
            <?php foreach ($queries as $query): ?>
                <article class="panel">
                    <span class="badge"><?= e(status_label($query['status'])) ?></span>
                    <h3><?= e($query['subject']) ?></h3>
                    <p class="muted"><?= e($query['name']) ?> · <?= e($query['email']) ?></p>
                    <p><?= e($query['message']) ?></p>
                    <?php if ($query['response']): ?>
                        <p><strong>Current response:</strong> <?= e($query['response']) ?></p>
                    <?php endif; ?>
                    <form method="post">
                        <input type="hidden" name="action" value="query_response">
                        <input type="hidden" name="query_id" value="<?= (int) $query['id'] ?>">
                        <div class="form-group">
                            <label for="response-<?= (int) $query['id'] ?>">Response</label>
                            <textarea id="response-<?= (int) $query['id'] ?>" name="response" rows="3" required><?= e($query['response'] ?? '') ?></textarea>
                        </div>
                        <button class="btn btn-primary" type="submit">Send Response</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <div>
                <h2>Package updates</h2>
                <p>Staff can update basic package status and pricing.</p>
            </div>
        </div>
        <div class="table-wrap panel">
            <table>
                <thead>
                <tr>
                    <th>Package</th>
                    <th>Destination</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Update</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($packages as $package): ?>
                    <tr>
                        <td><?= e($package['title']) ?></td>
                        <td><?= e($package['destination_name']) ?></td>
                        <td><?= money($package['base_price']) ?></td>
                        <td><span class="badge"><?= e(status_label($package['status'])) ?></span></td>
                        <td>
                            <form class="inline-form" method="post">
                                <input type="hidden" name="action" value="package_update">
                                <input type="hidden" name="package_id" value="<?= (int) $package['id'] ?>">
                                <input type="number" step="0.01" min="1" name="base_price" value="<?= e($package['base_price']) ?>" required>
                                <select name="status">
                                    <option value="active" <?= $package['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="paused" <?= $package['status'] === 'paused' ? 'selected' : '' ?>>Paused</option>
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
<?php require __DIR__ . '/../includes/footer.php'; ?>

