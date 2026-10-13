<?php
/**
 * Products listing.
 * (WordPress migration: archive-cs_product.php)
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page = [
    'slug'        => 'products',
    'path'        => 'products/',
    'title'       => 'Products',
    'description' => 'CookSmart products: Sugrihini ground spices (Mirchi, Haldi, Jeera, Dhania), Sugrihini whole spices (cumin, mustard, fenugreek, fennel, carom, panch phoron) and CookSmart Chakki Fresh Atta.',
    'breadcrumbs' => [['label' => 'Home', 'path' => ''], ['label' => 'Products', 'path' => 'products/']],
];
enqueue_script('https://cdn.jsdelivr.net/npm/gsap@' . GSAP_VERSION . '/dist/Flip.min.js');
enqueue_script('js/products.js');

$all        = products();
$categories = product_categories();
$filters    = ['all' => ['label' => 'All Products', 'count' => count($all)]];
foreach ($categories as $key => $cat) {
    $filters[$key] = ['label' => $cat['label'], 'count' => count(products_in($key))];
}

require __DIR__ . '/includes/header.php';
?>
<main id="main">
<?php partial('page-hero', [
    'eyebrow' => 'Pure · Ready to use',
    'title'   => 'Our Products',
    'lead'    => 'Ground spices, whole spices and chakki-fresh atta — from our own production to your kitchen.',
    'crumbs'  => $page['breadcrumbs'],
    'images'  => [product_img(get_product('mirchi-powder'), 'primary', 'sm'), product_img(get_product('whole-cumin'), 'primary', 'sm'), product_img(get_product('dhania-powder'), 'primary', 'sm')],
]); ?>

<section class="section catalogue" aria-label="Product catalogue">
    <div class="container">
        <div class="filter-bar" role="tablist" aria-label="Filter products" data-anim="fade-up">
            <?php foreach ($filters as $key => $f): ?>
            <button type="button" role="tab" class="filter-bar__btn<?= $key === 'all' ? ' is-active' : '' ?>" data-filter="<?= e($key) ?>" aria-selected="<?= $key === 'all' ? 'true' : 'false' ?>">
                <?= e($f['label']) ?> <span class="filter-bar__count"><?= $f['count'] ?></span>
            </button>
            <?php endforeach; ?>
            <span class="filter-bar__pill" aria-hidden="true"></span>
        </div>

        <div class="product-grid" data-product-grid data-anim-stagger="fade-up" data-stagger="0.1">
            <?php foreach ($categories as $key => $cat): $items = products_in($key); if (!$items) continue; ?>
            <header class="catalogue__group" data-category="<?= e($key) ?>" id="<?= e($key) ?>-group">
                <p class="catalogue__group-range"><?= e($cat['range']) ?><?php if ($cat['range'] === 'Sugrihini'): ?> · <span lang="bn">সুগৃহিণী</span><?php endif; ?></p>
                <h2 class="catalogue__group-title"><?= e($cat['label']) ?> <span class="catalogue__group-count"><?= count($items) ?> <?= count($items) === 1 ? 'product' : 'products' ?></span></h2>
                <p class="catalogue__group-text"><?= e($cat['text']) ?></p>
            </header>
            <?php foreach ($items as $product): ?>
            <?php partial('product-card', ['product' => $product]); ?>
            <?php endforeach; ?>
            <?php endforeach; ?>
        </div>

        <div class="catalogue__note" data-anim="fade-up">
            <span class="catalogue__note-icon"><?= icon('badge-check') ?></span>
            <p><strong>100% Satisfaction Guarantee</strong> on every Sugrihini ground-spice pack, <strong>Premium</strong> quality on every whole-spice pack, and Chakki Fresh Atta that is 100% natural — no additives, no preservatives.</p>
        </div>
    </div>
</section>

<?php partial('cta-band', ['title' => 'Ready to cook smart?', 'text' => 'Order Sugrihini ground and whole spices and Chakki Fresh Atta from our online shop, or contact us for trade enquiries.']); ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
