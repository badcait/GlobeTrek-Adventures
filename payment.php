<?php
declare(strict_types=1);

$pageTitle = 'Simulated Payment - GlobeTrek Adventures';
require_once __DIR__ . '/includes/init.php';
require_role(['customer']);

$bookingId = (int) ($_GET['booking'] ?? $_POST['booking_id'] ?? 0);
$userId = (int) current_user()['id'];

$stmt = db()->prepare(
    "SELECT b.*, p.title AS package_title, d.name AS destination_name, a.name AS accommodation_name,
            v.name AS vehicle_name, g.name AS guide_name
     FROM bookings b
     JOIN packages p ON p.id = b.package_id
     JOIN destinations d ON d.id = b.destination_id
     LEFT JOIN accommodations a ON a.id = b.accommodation_id
     LEFT JOIN vehicles v ON v.id = b.vehicle_id
     LEFT JOIN guides g ON g.id = b.guide_id
     WHERE b.id = ? AND b.user_id = ?
     LIMIT 1"
);
$stmt->bind_param('ii', $bookingId, $userId);
$stmt->execute();
$booking = first_or_null($stmt);

if (!$booking) {
    http_response_code(404);
    require __DIR__ . '/includes/header.php';
    echo '<section class="section"><div class="container narrow"><h1>Booking not found</h1><p class="muted">This booking is unavailable.</p><a class="btn btn-primary" href="dashboard.php">Back to dashboard</a></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = trim($_POST['method'] ?? 'Card');
    $reference = 'GT-' . strtoupper(bin2hex(random_bytes(4)));
    $stmt = db()->prepare('INSERT INTO payments (booking_id, amount, method, transaction_ref) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('idss', $bookingId, $booking['total_amount'], $method, $reference);
    $stmt->execute();

    $status = 'paid';
    $stmt = db()->prepare('UPDATE bookings SET status = ? WHERE id = ? AND user_id = ?');
    $stmt->bind_param('sii', $status, $bookingId, $userId);
    $stmt->execute();

    flash('success', 'Simulated payment completed. Staff can now confirm the booking.');
    redirect('dashboard.php');
}

require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container narrow">
        <span class="eyebrow">Secure payment simulation</span>
        <h1>Complete booking payment.</h1>
        <p>This demonstrates the payment workflow without connecting to a real payment provider.</p>
    </div>
</section>
<section class="section">
    <div class="container split">
        <div class="panel">
            <h2>Booking summary</h2>
            <table>
                <tr><th>Package</th><td><?= e($booking['package_title']) ?></td></tr>
                <tr><th>Destination</th><td><?= e($booking['destination_name']) ?></td></tr>
                <tr><th>Dates</th><td><?= e($booking['start_date']) ?> to <?= e($booking['end_date']) ?></td></tr>
                <tr><th>Travelers</th><td><?= (int) $booking['travelers'] ?></td></tr>
                <tr><th>Accommodation</th><td><?= e($booking['accommodation_name'] ?? 'Not selected') ?></td></tr>
                <tr><th>Vehicle</th><td><?= e($booking['vehicle_name'] ?? 'Not selected') ?></td></tr>
                <tr><th>Guide</th><td><?= e($booking['guide_name'] ?? 'Not selected') ?></td></tr>
                <tr><th>Total</th><td><strong class="price"><?= money($booking['total_amount']) ?></strong></td></tr>
            </table>
        </div>
        <form class="form-card" method="post">
            <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
            <h2>Payment method</h2>
            <p class="muted">Use sample details. The system records a simulated transaction reference.</p>
            <div class="form-group">
                <label for="method">Method</label>
                <select id="method" name="method">
                    <option>Card</option>
                    <option>Bank Transfer</option>
                    <option>Digital Wallet</option>
                </select>
            </div>
            <div class="form-group">
                <label for="card">Card number</label>
                <input id="card" value="4242 4242 4242 4242" readonly>
            </div>
            <button class="btn btn-primary" type="submit">Pay <?= money($booking['total_amount']) ?></button>
            <a class="btn btn-outline" href="dashboard.php">Pay Later</a>
        </form>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

