<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';

$connection = db();
$checks = [];

foreach (['users', 'destinations', 'packages', 'accommodations', 'vehicles', 'guides'] as $table) {
    $row = $connection->query('SELECT COUNT(*) AS total FROM ' . $table)->fetch_assoc();
    $checks[] = sprintf('%s=%d', $table, (int) $row['total']);
}

$accounts = [
    'admin@globetrek.local' => 'admin123',
    'staff@globetrek.local' => 'staff123',
    'customer@globetrek.local' => 'customer123',
];

foreach ($accounts as $email => $password) {
    $stmt = $connection->prepare('SELECT password_hash FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $row = first_or_null($stmt);
    if (!$row || !password_verify($password, $row['password_hash'])) {
        throw new RuntimeException('Password check failed for ' . $email);
    }
}

$connection->begin_transaction();

try {
    $userId = (int) $connection->query("SELECT id FROM users WHERE email = 'customer@globetrek.local'")->fetch_assoc()['id'];
    $package = $connection->query('SELECT * FROM packages ORDER BY id LIMIT 1')->fetch_assoc();
    $vehicle = $connection->query('SELECT * FROM vehicles ORDER BY id LIMIT 1')->fetch_assoc();
    $total = 500.00;
    $start = date('Y-m-d', strtotime('+10 days'));
    $end = date('Y-m-d', strtotime('+13 days'));
    $notes = 'Smoke test booking';

    $stmt = $connection->prepare(
        'INSERT INTO bookings (user_id, package_id, destination_id, vehicle_id, start_date, end_date, travelers, total_amount, notes)
         VALUES (?, ?, ?, ?, ?, ?, 2, ?, ?)'
    );
    $stmt->bind_param('iiiissds', $userId, $package['id'], $package['destination_id'], $vehicle['id'], $start, $end, $total, $notes);
    $stmt->execute();
    $bookingId = $connection->insert_id;

    $method = 'Card';
    $reference = 'GT-SMOKE';
    $stmt = $connection->prepare('INSERT INTO payments (booking_id, amount, method, transaction_ref) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('idss', $bookingId, $total, $method, $reference);
    $stmt->execute();

    $stmt = $connection->prepare('INSERT INTO queries (user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)');
    $name = 'Smoke Tester';
    $email = 'customer@globetrek.local';
    $subject = 'Smoke test query';
    $message = 'This query validates the support workflow.';
    $stmt->bind_param('issss', $userId, $name, $email, $subject, $message);
    $stmt->execute();

    $connection->rollback();
} catch (Throwable $exception) {
    $connection->rollback();
    throw $exception;
}

echo 'OK ' . implode(', ', $checks) . PHP_EOL;
echo "OK demo passwords\n";
echo "OK booking/payment/query transaction\n";

