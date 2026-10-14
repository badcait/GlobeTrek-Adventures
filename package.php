<?php
declare(strict_types=1);

$pageTitle = 'Package Details - GlobeTrek Adventures';
$currentPage = 'packages';
require_once __DIR__ . '/includes/init.php';

$slug = trim($_GET['slug'] ?? '');
$stmt = db()->prepare(
    "SELECT p.*, d.name AS destination_name, d.country, d.summary, d.best_season
     FROM packages p
     JOIN destinations d ON d.id = p.destination_id
     WHERE p.slug = ? AND p.status = 'active'
     LIMIT 1"
);
$stmt->bind_param('s', $slug);
$stmt->execute();
$package = first_or_null($stmt);

if (!$package) {
    http_response_code(404);
    require __DIR__ . '/includes/header.php';
    echo '<section class="section"><div class="container narrow"><h1>Package not found</h1><p class="muted">The selected package is unavailable.</p><a class="btn btn-primary" href="packages.php">Back to packages</a></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$stmt = db()->prepare('SELECT * FROM accommodations WHERE destination_id = ? ORDER BY rating DESC');
$stmt->bind_param('i', $package['destination_id']);
$stmt->execute();
$stays = fetch_all_assoc($stmt);

$pageTitle = $package['title'] . ' - GlobeTrek Adventures';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container narrow">
        <span class="eyebrow"><?= e($package['destination_name']) ?>, <?= e($package['country']) ?></span>
        <h1><?= e($package['title']) ?></h1>
        <p><?= e($package['description']) ?></p>
    </div>
</section>
<section class="section">
    <div class="container split">
        <img class="card" src="<?= e($package['image']) ?>" alt="<?= e($package['title']) ?>">
        <div class="panel">
            <div class="card-meta">
                <span class="badge"><?= e($package['activity_level']) ?></span>
                <strong class="price"><?= money($package['base_price']) ?></strong>
            </div>
            <h2>Package includes</h2>
            <ul class="check-list">
                <?php foreach (array_map('trim', explode(',', $package['activities'])) as $activity): ?>
                    <li><?= e($activity) ?></li>
                <?php endforeach; ?>
                <li><?= (int) $package['duration_days'] ?> day itinerary</li>
                <li>Best season: <?= e($package['best_season']) ?></li>
            </ul>
            <a class="btn btn-primary" href="planner.php?package=<?= (int) $package['id'] ?>">Customize & Book</a>
            <a class="btn btn-outline" href="contact.php">Ask a Question</a>
        </div>
    </div>
</section>
<section class="section section-soft">
    <div class="container">
        <div class="section-heading">
            <div>
                <h2>Recommended stays for this destination</h2>
                <p>Customers can choose one of these while booking.</p>
            </div>
        </div>
        <div class="grid grid-3">
            <?php foreach ($stays as $stay): ?>
                <article class="card">
                    <img class="card-image" src="<?= e($stay['image']) ?>" alt="<?= e($stay['name']) ?>">
                    <div class="card-body">
                        <div class="card-meta"><span><?= e($stay['type']) ?></span><span><?= e($stay['rating']) ?> rating</span></div>
                        <h3><?= e($stay['name']) ?></h3>
                        <p class="muted"><?= e($stay['description']) ?></p>
                        <strong class="price"><?= money($stay['price_per_night']) ?> / night</strong>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

