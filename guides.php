<?php
declare(strict_types=1);

$pageTitle = 'Travel Guides - GlobeTrek Adventures';
$currentPage = 'guides';
require_once __DIR__ . '/includes/init.php';

$guides = db()->query('SELECT * FROM guides ORDER BY rating DESC')->fetch_all(MYSQLI_ASSOC);

require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container narrow">
        <span class="eyebrow">Guided experiences</span>
        <h1>Travel with people who know the place.</h1>
        <p>Guides can be selected during booking to support cultural, adventure, city, and family travel plans.</p>
    </div>
</section>
<section class="section">
    <div class="container grid grid-4">
        <?php foreach ($guides as $guide): ?>
            <article class="card">
                <img class="card-image" src="<?= e($guide['image']) ?>" alt="<?= e($guide['name']) ?>">
                <div class="card-body">
                    <div class="card-meta"><span><?= e($guide['specialty']) ?></span><span><?= e($guide['rating']) ?></span></div>
                    <h3><?= e($guide['name']) ?></h3>
                    <p class="muted"><?= e($guide['languages']) ?></p>
                    <p><?= e($guide['bio']) ?></p>
                    <strong class="price"><?= money($guide['price_per_day']) ?> / day</strong>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

