<?php
declare(strict_types=1);

$pageTitle = 'GlobeTrek Adventures - Travel Booking Platform';
$currentPage = 'home';
require_once __DIR__ . '/includes/init.php';

$needsSetup = false;
$packages = $destinations = $accommodations = [];

try {
    $packages = db()->query(
        "SELECT p.*, d.name AS destination_name, d.country
         FROM packages p
         JOIN destinations d ON d.id = p.destination_id
         WHERE p.status = 'active'
         ORDER BY p.id
         LIMIT 4"
    )->fetch_all(MYSQLI_ASSOC);

    $destinations = db()->query('SELECT * FROM destinations ORDER BY id LIMIT 3')->fetch_all(MYSQLI_ASSOC);
    $accommodations = db()->query(
        'SELECT a.*, d.name AS destination_name
         FROM accommodations a
         JOIN destinations d ON d.id = a.destination_id
         ORDER BY a.rating DESC
         LIMIT 4'
    )->fetch_all(MYSQLI_ASSOC);
} catch (Throwable) {
    $needsSetup = true;
}

require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="container hero-inner">
        <span class="eyebrow">Global travel specialists</span>
        <h1>Embark on your journey to secure the ideal getaway.</h1>
        <p>Browse curated tour packages, pick hotels, select vehicles, add expert travel guides, and book a complete trip from one friendly platform.</p>

        <?php if ($needsSetup): ?>
            <div class="search-panel">
                <div class="card-body">
                    <h2>Database setup required</h2>
                    <p class="muted">Start MySQL in XAMPP, then create the GlobeTrek database and demo accounts.</p>
                    <a class="btn btn-primary" href="setup.php">Open Setup</a>
                </div>
            </div>
        <?php else: ?>
            <form class="search-panel" action="packages.php" method="get">
                <div class="search-tabs">
                    <span class="tab-pill active">Hotels</span>
                    
                </div>
                <div class="search-grid">
                    <div class="field">
                        <label for="destination">Destination</label>
                        <select id="destination" name="destination">
                            <option value="">Where are you going?</option>
                            <?php foreach ($destinations as $destination): ?>
                                <option value="<?= (int) $destination['id'] ?>"><?= e($destination['name']) ?>, <?= e($destination['country']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="check-in">Check In</label>
                        <input id="check-in" type="date" name="start_date">
                    </div>
                    <div class="field">
                        <label for="check-out">Check Out</label>
                        <input id="check-out" type="date" name="end_date">
                    </div>
                    <div class="field">
                        <label for="guests">Guests</label>
                        <input id="guests" type="number" name="travelers" min="1" max="20" value="2">
                    </div>
                    <button class="btn btn-primary" type="submit">Search Trips</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</section>

<?php if (!$needsSetup): ?>
    <section class="section">
        <div class="container">
            <div class="section-heading">
                <div>
                    <h2>Well-liked accommodations in close proximity</h2>
                    <p>Comfortable hotels and villas matched with your destination choices.</p>
                </div>
                <a class="btn btn-outline" href="accommodations.php">View Stays</a>
            </div>
            <div class="grid grid-4">
                <?php foreach ($accommodations as $accommodation): ?>
                    <article class="card">
                        <img class="card-image" src="<?= e($accommodation['image']) ?>" alt="<?= e($accommodation['name']) ?>">
                        <div class="card-body">
                            <div class="card-meta"><span><?= e($accommodation['type']) ?></span><span><?= e($accommodation['rating']) ?> rating</span></div>
                            <h3><?= e($accommodation['name']) ?></h3>
                            <p class="muted"><?= e($accommodation['destination_name']) ?></p>
                            <div class="card-meta"><strong class="price"><?= money($accommodation['price_per_night']) ?></strong><span>per night</span></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section section-soft">
        <div class="container split">
            <div class="feature-image" aria-hidden="true"></div>
            <div>
                <span class="eyebrow">Why choose us</span>
                <h2>Plan every part of the trip in one calm place.</h2>
                <p class="muted">GlobeTrek Adventures supports travelers with package browsing, accommodation selection, transport coordination, guided experiences, staff booking confirmation, and clear communication.</p>
                <ul class="check-list">
                    <li>Verified packages with destinations, activities, pricing, and duration.</li>
                    <li>Custom travel planner for vehicles, hotels, guides, dates, and group size.</li>
                    <li>Customer, staff, and admin dashboards for booking management.</li>
                    <li>Simulated secure payment flow for coursework demonstration.</li>
                </ul>
                <a class="btn btn-primary" href="planner.php">Customize a Trip</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-heading">
                <div>
                    <h2>Trending <span class="accent">destinations</span> now days</h2>
                    <p>Popular places ready for packages, guides, accommodation, and transport.</p>
                </div>
                <a class="btn btn-outline" href="packages.php">Explore Packages</a>
            </div>
            <div class="grid grid-3">
                <?php foreach ($destinations as $destination): ?>
                    <article class="destination-card">
                        <img src="<?= e($destination['image']) ?>" alt="<?= e($destination['name']) ?>">
                        <div>
                            <span class="badge"><?= e($destination['best_season']) ?></span>
                            <h3><?= e($destination['name']) ?></h3>
                            <p><?= e($destination['summary']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section section-soft">
        <div class="container">
            <div class="section-heading">
                <div>
                    <h2>Featured tour packages</h2>
                    <p>Ready-made trips customers can book immediately or customize.</p>
                </div>
                <a class="btn btn-outline" href="packages.php">All Packages</a>
            </div>
            <div class="grid grid-4">
                <?php foreach ($packages as $package): ?>
                    <article class="card">
                        <img class="card-image" src="<?= e($package['image']) ?>" alt="<?= e($package['title']) ?>">
                        <div class="card-body">
                            <div class="card-meta"><span><?= e($package['destination_name']) ?></span><span><?= (int) $package['duration_days'] ?> days</span></div>
                            <h3><?= e($package['title']) ?></h3>
                            <p class="muted"><?= e($package['activity_level']) ?> activity</p>
                            <div class="card-meta"><strong class="price"><?= money($package['base_price']) ?></strong><a href="package.php?slug=<?= e($package['slug']) ?>">Details</a></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
