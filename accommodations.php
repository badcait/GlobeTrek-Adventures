<?php
declare(strict_types=1);

$pageTitle = 'Hotels and Villas - GlobeTrek Adventures';
$currentPage = 'accommodations';
require_once __DIR__ . '/includes/init.php';

$items = db()->query(
    'SELECT a.*, d.name AS destination_name, d.country
     FROM accommodations a
     JOIN destinations d ON d.id = a.destination_id
     ORDER BY a.rating DESC'
)->fetch_all(MYSQLI_ASSOC);

require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container narrow">
        <span class="eyebrow">Hotels & villas</span>
        <h1>Stay close to the experience.</h1>
        <p>Accommodation options can be attached to package bookings through the trip planner.</p>
    </div>
</section>
<section class="section">
    <div class="container grid grid-3">
        <?php foreach ($items as $item): ?>
            <article class="card">
                <img class="card-image" src="<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>">
                <div class="card-body">
                    <div class="card-meta"><span><?= e($item['destination_name']) ?>, <?= e($item['country']) ?></span><span><?= e($item['rating']) ?></span></div>
                    <h3><?= e($item['name']) ?></h3>
                    <p class="muted"><?= e($item['description']) ?></p>
                    <div class="card-meta"><strong class="price"><?= money($item['price_per_night']) ?></strong><span><?= e($item['type']) ?></span></div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

