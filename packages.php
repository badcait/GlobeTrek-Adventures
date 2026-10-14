<?php
declare(strict_types=1);

$pageTitle = 'Tour Packages - GlobeTrek Adventures';
$currentPage = 'packages';
require_once __DIR__ . '/includes/init.php';

$keyword = trim($_GET['q'] ?? '');
$destinationId = (int) ($_GET['destination'] ?? 0);
$destinations = db()->query('SELECT id, name, country FROM destinations ORDER BY name')->fetch_all(MYSQLI_ASSOC);

$sql = "SELECT p.*, d.name AS destination_name, d.country
        FROM packages p
        JOIN destinations d ON d.id = p.destination_id
        WHERE p.status = 'active'";
$types = '';
$params = [];

if ($keyword !== '') {
    $sql .= ' AND (p.title LIKE ? OR p.description LIKE ? OR p.activities LIKE ? OR d.name LIKE ?)';
    $search = '%' . $keyword . '%';
    $types .= 'ssss';
    array_push($params, $search, $search, $search, $search);
}

if ($destinationId > 0) {
    $sql .= ' AND p.destination_id = ?';
    $types .= 'i';
    $params[] = $destinationId;
}

$sql .= ' ORDER BY p.base_price ASC';
$stmt = db()->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$packages = fetch_all_assoc($stmt);

require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container narrow">
        <span class="eyebrow">Search travel packages</span>
        <h1>Find destinations, activities, and pricing in one view.</h1>
        <p>Compare tour packages and open a package to customize transport, accommodation, dates, and guides.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        <form class="toolbar" method="get">
            <div class="form-group">
                <label for="q">Search</label>
                <input id="q" type="search" name="q" value="<?= e($keyword) ?>" placeholder="Beach, safari, Dubai, hiking">
            </div>
            <div class="form-group">
                <label for="destination">Destination</label>
                <select id="destination" name="destination">
                    <option value="">All destinations</option>
                    <?php foreach ($destinations as $destination): ?>
                        <option value="<?= (int) $destination['id'] ?>" <?= $destinationId === (int) $destination['id'] ? 'selected' : '' ?>>
                            <?= e($destination['name']) ?>, <?= e($destination['country']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Search</button>
            <a class="btn btn-outline" href="packages.php">Reset</a>
        </form>

        <div class="grid grid-3">
            <?php foreach ($packages as $package): ?>
                <article class="card">
                    <img class="card-image" src="<?= e($package['image']) ?>" alt="<?= e($package['title']) ?>">
                    <div class="card-body">
                        <span class="badge"><?= e($package['activity_level']) ?></span>
                        <h3><?= e($package['title']) ?></h3>
                        <p class="muted"><?= e($package['destination_name']) ?>, <?= e($package['country']) ?> · <?= (int) $package['duration_days'] ?> days</p>
                        <p><?= e(excerpt($package['description'])) ?></p>
                        <div class="card-meta">
                            <strong class="price"><?= money($package['base_price']) ?></strong>
                            <a class="btn btn-outline" href="package.php?slug=<?= e($package['slug']) ?>">View Details</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if (!$packages): ?>
            <div class="panel">
                <h2>No matching packages found</h2>
                <p class="muted">Try another destination, keyword, or reset the search filters.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

