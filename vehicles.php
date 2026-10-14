<?php
declare(strict_types=1);

$pageTitle = 'Vehicles - GlobeTrek Adventures';
$currentPage = 'vehicles';
require_once __DIR__ . '/includes/init.php';

$vehicles = db()->query('SELECT * FROM vehicles ORDER BY price_per_day ASC')->fetch_all(MYSQLI_ASSOC);

require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container narrow">
        <span class="eyebrow">Transport services</span>
        <h1>Choose the right vehicle for the journey.</h1>
        <p>Cars, vans, SUVs, and shuttles can be added when customers customize a booking.</p>
    </div>
</section>
<section class="section">
    <div class="container grid grid-4">
        <?php foreach ($vehicles as $vehicle): ?>
            <article class="card">
                <img class="card-image" src="<?= e($vehicle['image']) ?>" alt="<?= e($vehicle['name']) ?>">
                <div class="card-body">
                    <div class="card-meta"><span><?= e($vehicle['type']) ?></span><span><?= (int) $vehicle['capacity'] ?> seats</span></div>
                    <h3><?= e($vehicle['name']) ?></h3>
                    <p class="muted"><?= e($vehicle['description']) ?></p>
                    <strong class="price"><?= money($vehicle['price_per_day']) ?> / day</strong>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

