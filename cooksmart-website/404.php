<?php
/**
 * 404 — page not found.
 * (WordPress migration: 404.php)
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

http_response_code(404);

$page = [
    'slug'        => 'not-found',
    'path'        => '',
    'title'       => 'Page not found',
    'description' => 'The page you were looking for could not be found.',
];

require __DIR__ . '/includes/header.php';
?>
<main id="main">
<section class="not-found">
    <div class="container not-found__grid">
        <div class="not-found__plate" aria-hidden="true">
            <img src="<?= e(asset(asset_exists('images/spice-plate.webp') ? 'images/spice-plate.webp' : 'images/spice-plate.svg')) ?>" alt="CookSmart Spices" width="420" height="420">
            <span class="not-found__code">404</span>
        </div>
        <div class="not-found__text">
            <p class="eyebrow" data-anim="fade-up">Oops!</p>
            <h1 class="not-found__title" data-anim="split-up">Looks like this page wandered out of the kitchen.</h1>
            <p data-anim="fade-up" data-delay="0.15">The page you’re looking for doesn’t exist or has moved. Let’s get you back to something tasty.</p>
            <div class="hero-actions" data-anim="fade-up" data-delay="0.25">
                <a class="btn btn--primary magnetic" href="<?= e(url()) ?>"><span>Back to Home</span><?= icon('arrow-right') ?></a>
                <a class="btn btn--outline magnetic" href="<?= e(url('products/')) ?>"><span>See Products</span></a>
            </div>
        </div>
    </div>
</section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
