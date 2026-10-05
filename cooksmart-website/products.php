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
    'description' => 'CookSmart products: Sugrihini Mirchi, Haldi, Jeera and Dhania powder, and CookSmart Chakki Fresh Atta — from our own production to your kitchen.',
    'breadcrumbs' => [['label' => 'Home', 'path' => ''], ['label' => 'Products', 'path' => 'products/']],
];
enqueue_script('https://cdn.jsdelivr.net/npm/gsap@' . GSAP_VERSION . '/dist/Flip.min.js');
enqueue_script('js/products.js');

$all     = products();
$filters = [
    'all'    => ['label' => 'All Products',      'count' => count($all)],
    'spices' => ['label' => 'Sugrihini Spices',  'count' => count(products_in('spices'))],
    'atta'   => ['label' => 'Atta',              'count' => count(products_in('atta'))],
];

require __DIR__ . '/includes/header.php';
?>
<main id="main">
<?php partial('page-hero', [
    'eyebrow' => 'Pure · Ready to use',
    'title'   => 'Our Products',
    'lead'    => 'Pure, ready-to-use spices and chakki-fresh atta — from our own production to your kitchen.',
    'crumbs'  => $page['breadcrumbs'],
    'images'  => [product_img(get_product('jeera-powder'), 'primary', 'sm'), product_img(get_product('mirchi-powder'), 'primary', 'sm'), product_img(get_product('dhania-powder'), 'primary', 'sm')],
]); ?>

<section class="section catalogue" aria-labelledby="catalogue-title">
    <div class="container">
        <h2 id="catalogue-title" class="visually-hidden">Product catalogue</h2>
        <div class="filter-bar" role="tablist" aria-label="Filter products" data-anim="fade-up">
            <?php foreach ($filters as $key => $f): ?>
            <button type="button" role="tab" class="filter-bar__btn<?= $key === 'all' ? ' is-active' : '' ?>" data-filter="<?= e($key) ?>" aria-selected="<?= $key === 'all' ? 'true' : 'false' ?>">
                <?= e($f['label']) ?> <span class="filter-bar__count"><?= $f['count'] ?></span>
            </button>
            <?php endforeach; ?>
            <span class="filter-bar__pill" aria-hidden="true"></span>
        </div>

        <div class="product-grid" data-product-grid data-anim-stagger="fade-up" data-stagger="0.1">
            <?php foreach ($all as $product): ?>
            <?php partial('product-card', ['product' => $product]); ?>
            <?php endforeach; ?>
        </div>

        <div class="catalogue__note" data-anim="fade-up">
            <span class="catalogue__note-icon"><?= icon('badge-check') ?></span>
            <p><strong>100% Satisfaction Guarantee</strong> on every Sugrihini spice pack, and our Chakki Fresh Atta is 100% natural — no additives, no preservatives.</p>
        </div>
    </div>
</section>

<?php partial('cta-band', ['title' => 'Ready to cook smart?', 'text' => 'Order Sugrihini spices and Chakki Fresh Atta from our online shop, or contact us for trade enquiries.']); ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
