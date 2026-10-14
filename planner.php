<?php
declare(strict_types=1);

$pageTitle = 'Custom Trip Planner - GlobeTrek Adventures';
$currentPage = 'packages';
require_once __DIR__ . '/includes/init.php';

$selectedPackageId = (int) ($_GET['package'] ?? $_POST['package_id'] ?? 0);
$errors = [];

$packages = db()->query(
    "SELECT p.*, d.name AS destination_name, d.country
     FROM packages p
     JOIN destinations d ON d.id = p.destination_id
     WHERE p.status = 'active'
     ORDER BY p.title"
)->fetch_all(MYSQLI_ASSOC);

if ($selectedPackageId <= 0 && $packages) {
    $selectedPackageId = (int) $packages[0]['id'];
}

$selectedPackage = null;
foreach ($packages as $packageOption) {
    if ((int) $packageOption['id'] === $selectedPackageId) {
        $selectedPackage = $packageOption;
        break;
    }
}

if (!$selectedPackage) {
    $selectedPackage = $packages[0] ?? null;
    $selectedPackageId = $selectedPackage ? (int) $selectedPackage['id'] : 0;
}

$destinationId = $selectedPackage ? (int) $selectedPackage['destination_id'] : 0;
$stmt = db()->prepare('SELECT * FROM accommodations WHERE destination_id = ? ORDER BY price_per_night ASC');
$stmt->bind_param('i', $destinationId);
$stmt->execute();
$accommodations = fetch_all_assoc($stmt);
$vehicles = db()->query('SELECT * FROM vehicles ORDER BY price_per_day ASC')->fetch_all(MYSQLI_ASSOC);
$guides = db()->query('SELECT * FROM guides ORDER BY price_per_day ASC')->fetch_all(MYSQLI_ASSOC);

$values = [
    'package_id' => $selectedPackageId,
    'start_date' => $_POST['start_date'] ?? '',
    'end_date' => $_POST['end_date'] ?? '',
    'travelers' => (int) ($_POST['travelers'] ?? 2),
    'accommodation_id' => (int) ($_POST['accommodation_id'] ?? ($accommodations[0]['id'] ?? 0)),
    'vehicle_id' => (int) ($_POST['vehicle_id'] ?? ($vehicles[0]['id'] ?? 0)),
    'guide_id' => (int) ($_POST['guide_id'] ?? 0),
    'notes' => trim($_POST['notes'] ?? ''),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!is_logged_in()) {
        flash('warning', 'Please sign in or register before creating a booking.');
        redirect('login.php');
    }

    if (current_user()['role'] !== 'customer') {
        $errors[] = 'Only customer accounts can create travel bookings.';
    }

    if (!$selectedPackage) {
        $errors[] = 'Please select a valid package.';
    }
    if ($values['travelers'] < 1 || $values['travelers'] > 20) {
        $errors[] = 'Travelers must be between 1 and 20.';
    }
    if ($values['start_date'] === '' || $values['end_date'] === '') {
        $errors[] = 'Start date and end date are required.';
    } elseif (strtotime($values['end_date']) <= strtotime($values['start_date'])) {
        $errors[] = 'End date must be after the start date.';
    }

    $selectedAccommodation = null;
    foreach ($accommodations as $item) {
        if ((int) $item['id'] === $values['accommodation_id']) {
            $selectedAccommodation = $item;
            break;
        }
    }

    $selectedVehicle = null;
    foreach ($vehicles as $item) {
        if ((int) $item['id'] === $values['vehicle_id']) {
            $selectedVehicle = $item;
            break;
        }
    }

    $selectedGuide = null;
    foreach ($guides as $item) {
        if ((int) $item['id'] === $values['guide_id']) {
            $selectedGuide = $item;
            break;
        }
    }

    if (!$selectedVehicle) {
        $errors[] = 'Please select a valid vehicle.';
    }

    if (!$errors && $selectedPackage && $selectedVehicle) {
        $total = booking_total($selectedPackage, $selectedAccommodation, $selectedVehicle, $selectedGuide, $values['travelers'], $values['start_date'], $values['end_date']);
        $userId = (int) current_user()['id'];
        $accommodationId = $selectedAccommodation ? (int) $selectedAccommodation['id'] : null;
        $vehicleId = (int) $selectedVehicle['id'];
        $guideId = $selectedGuide ? (int) $selectedGuide['id'] : null;

        $stmt = db()->prepare(
            'INSERT INTO bookings (user_id, package_id, destination_id, accommodation_id, vehicle_id, guide_id, start_date, end_date, travelers, total_amount, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'iiiiiissids',
            $userId,
            $selectedPackage['id'],
            $selectedPackage['destination_id'],
            $accommodationId,
            $vehicleId,
            $guideId,
            $values['start_date'],
            $values['end_date'],
            $values['travelers'],
            $total,
            $values['notes']
        );
        $stmt->execute();
        $bookingId = db()->insert_id;

        flash('success', 'Trip plan saved. Complete the simulated payment to confirm your booking.');
        redirect('payment.php?booking=' . $bookingId);
    }
}

require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container narrow">
        <span class="eyebrow">Custom travel planner</span>
        <h1>Pick a package, vehicle, hotel, guide, and dates.</h1>
        <p>This page demonstrates the main customer booking workflow required by the assignment brief.</p>
    </div>
</section>
<section class="section">
    <div class="container split">
        <form class="form-card" method="post" data-planner-form>
            <?php foreach ($errors as $error): ?>
                <div class="flash flash-error"><?= e($error) ?></div>
            <?php endforeach; ?>
            <input type="hidden" name="package_price" value="<?= e($selectedPackage['base_price'] ?? 0) ?>">
            <div class="form-grid">
                <div class="form-group full">
                    <label for="package_id">Tour package</label>
                    <select id="package_id" name="package_id" onchange="window.location='planner.php?package=' + this.value">
                        <?php foreach ($packages as $packageOption): ?>
                            <option value="<?= (int) $packageOption['id'] ?>" <?= $selectedPackageId === (int) $packageOption['id'] ? 'selected' : '' ?>>
                                <?= e($packageOption['title']) ?> - <?= e($packageOption['destination_name']) ?> (<?= money($packageOption['base_price']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="start_date">Start date</label>
                    <input id="start_date" type="date" name="start_date" value="<?= e($values['start_date']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="end_date">End date</label>
                    <input id="end_date" type="date" name="end_date" value="<?= e($values['end_date']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="travelers">Travelers</label>
                    <input id="travelers" type="number" name="travelers" min="1" max="20" value="<?= (int) $values['travelers'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="accommodation_id">Accommodation</label>
                    <select id="accommodation_id" name="accommodation_id" data-price-select="accommodation">
                        <option value="0" data-price="0">No accommodation</option>
                        <?php foreach ($accommodations as $accommodation): ?>
                            <option value="<?= (int) $accommodation['id'] ?>" data-price="<?= e($accommodation['price_per_night']) ?>" <?= $values['accommodation_id'] === (int) $accommodation['id'] ? 'selected' : '' ?>>
                                <?= e($accommodation['name']) ?> - <?= money($accommodation['price_per_night']) ?>/night
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="vehicle_id">Vehicle</label>
                    <select id="vehicle_id" name="vehicle_id" required>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <option value="<?= (int) $vehicle['id'] ?>" data-price="<?= e($vehicle['price_per_day']) ?>" <?= $values['vehicle_id'] === (int) $vehicle['id'] ? 'selected' : '' ?>>
                                <?= e($vehicle['name']) ?> - <?= money($vehicle['price_per_day']) ?>/day
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="guide_id">Travel guide</label>
                    <select id="guide_id" name="guide_id">
                        <option value="0" data-price="0">No guide</option>
                        <?php foreach ($guides as $guide): ?>
                            <option value="<?= (int) $guide['id'] ?>" data-price="<?= e($guide['price_per_day']) ?>" <?= $values['guide_id'] === (int) $guide['id'] ? 'selected' : '' ?>>
                                <?= e($guide['name']) ?> - <?= money($guide['price_per_day']) ?>/day
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full">
                    <label for="notes">Special notes</label>
                    <textarea id="notes" name="notes" rows="4" placeholder="Airport pickup, meal preferences, accessibility needs"><?= e($values['notes']) ?></textarea>
                </div>
            </div>
            <button class="btn btn-primary" type="submit">Create Booking</button>
        </form>

        <aside class="panel">
            <?php if ($selectedPackage): ?>
                <img class="card-image" src="<?= e($selectedPackage['image']) ?>" alt="<?= e($selectedPackage['title']) ?>">
                <h2><?= e($selectedPackage['title']) ?></h2>
                <p class="muted"><?= e($selectedPackage['destination_name']) ?>, <?= e($selectedPackage['country']) ?> · <?= (int) $selectedPackage['duration_days'] ?> days</p>
                <p><?= e($selectedPackage['description']) ?></p>
                <ul class="check-list">
                    <li>Base package price: <?= money($selectedPackage['base_price']) ?> per traveler</li>
                    <li>Estimated total updates before submission</li>
                    <li>Payment is simulated after booking creation</li>
                </ul>
                <p class="price">Estimated total: <span data-total-preview><?= money($selectedPackage['base_price']) ?></span></p>
            <?php endif; ?>
        </aside>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
