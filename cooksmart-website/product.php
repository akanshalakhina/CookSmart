<?php
/**
 * Single product — /products/{slug}/
 * (WordPress migration: single-cs_product.php)
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$slug    = preg_replace('/[^a-z0-9-]/', '', strtolower((string) ($_GET['slug'] ?? '')));
$product = get_product($slug);
if ($product === null) {
    require __DIR__ . '/404.php';
    exit;
}

$fullName = ($product['category'] === 'spices' ? 'Sugrihini ' : 'CookSmart ') . $product['name'];
$page = [
    'slug'        => 'products',
    'path'        => 'products/' . $product['slug'] . '/',
    'title'       => $fullName . ' — ' . implode(', ', $product['net_weights']),
    'description' => $product['description'],
    'body_class'  => 'product-page',
    'breadcrumbs' => [
        ['label' => 'Home', 'path' => ''],
        ['label' => 'Products', 'path' => 'products/'],
        ['label' => $product['name'], 'path' => 'products/' . $product['slug'] . '/'],
    ],
    'preload_image' => product_img($product),
];
enqueue_script('js/product.js');
enqueue_script('js/carousel.js');

$others = array_filter(products(), static fn ($p) => $p['slug'] !== $product['slug']);
$views  = array_keys($product['images']);

require __DIR__ . '/includes/header.php';
?>
<main id="main" style="--accent: <?= e($product['accent']) ?>; --accent-soft: <?= e($product['accent_soft']) ?>;">
<?php partial('page-hero', [
    'eyebrow' => $product['range'] . ($product['range_bn'] !== '' ? ' · ' . $product['range_bn'] : ''),
    'title'   => $product['name'],
    'crumbs'  => $page['breadcrumbs'],
    'accent'  => $product['accent'],
]); ?>

<section class="section product-detail" aria-labelledby="product-name">
    <div class="container product-detail__grid">
        <div class="product-gallery" data-gallery>
            <div class="product-gallery__stage" data-anim="zoom-in" data-tilt-stage>
                <span class="product-gallery__blob" aria-hidden="true"></span>
                <span class="product-gallery__ring" aria-hidden="true"></span>
                <?php foreach ($views as $i => $view): ?>
                <img src="<?= e(product_img($product, $view)) ?>" alt="<?= e($fullName . ' pack' . ($i ? ' — alternate view' : ', ' . $product['net_weights'][0])) ?>"
                     class="product-gallery__img<?= $i === 0 ? ' is-active' : '' ?>" width="600" height="900" data-gallery-img="<?= $i ?>"<?= $i ? ' loading="lazy"' : ' fetchpriority="high"' ?>>
                <?php endforeach; ?>
            </div>
            <?php if (count($views) > 1): ?>
            <div class="product-gallery__thumbs" role="group" aria-label="Pack views">
                <?php foreach ($views as $i => $view): ?>
                <button type="button" class="product-gallery__thumb<?= $i === 0 ? ' is-active' : '' ?>" data-gallery-thumb="<?= $i ?>" aria-label="Show view <?= $i + 1 ?>" aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>">
                    <img src="<?= e(product_img($product, $view, 'sm')) ?>" alt="" width="70" height="105" loading="lazy">
                </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="product-info">
            <p class="product-info__range" data-anim="fade-up">
                <?= e($product['range']) ?><?php if ($product['range_bn'] !== ''): ?> · <span lang="bn"><?= e($product['range_bn']) ?></span><?php endif; ?>
            </p>
            <h2 id="product-name" class="product-info__name" data-anim="split-up"><?= e($product['name']) ?></h2>
            <p class="product-info__hi" lang="hi" data-anim="fade-up"><?= e($product['name_hi']) ?></p>
            <?= swoosh_line() ?>
            <p class="product-info__desc" data-anim="fade-up" data-delay="0.1"><?= e($product['description']) ?></p>

            <div class="product-info__block" data-anim="fade-up" data-delay="0.15">
                <h3 class="product-info__label">Net weight</h3>
                <div class="product-info__weights">
                    <?php foreach ($product['net_weights'] as $w): ?><span class="weight-chip"><?= icon('package') ?><?= e($w) ?></span><?php endforeach; ?>
                </div>
            </div>

            <div class="product-info__block">
                <h3 class="product-info__label" data-anim="fade-up">On the pack</h3>
                <ul class="claim-list" data-anim-stagger="fade-up" data-stagger="0.07">
                    <?php foreach ($product['claims'] as $claim): ?>
                    <li class="claim"><?= icon($claim === 'Vegetarian' ? 'leaf' : 'check') ?><?= e($claim) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="product-info__actions" data-anim="fade-up" data-delay="0.2">
                <a class="btn-shop btn-shop--lg magnetic" <?= shop_attrs() ?>><span>Shop Now</span><?= icon('cart') ?></a>
                <a class="btn btn--outline magnetic" href="<?= e(url('contact-us/?product=' . $product['slug'] . '#enquiry')) ?>"><span>Enquire</span><?= icon('mail') ?></a>
            </div>
        </div>
    </div>
</section>

<section class="section section--cream section--pattern more-products" aria-labelledby="more-title">
    <div class="container">
        <div class="products-showcase__head">
            <?php partial('section-heading', ['eyebrow' => 'Explore', 'title' => 'More from <em>CookSmart</em>', 'align' => 'left', 'id' => 'more-title']); ?>
            <div class="carousel-nav" data-anim="fade-up">
                <button type="button" class="carousel-nav__btn" data-carousel-prev="more-products" aria-label="Previous products"><?= icon('arrow-left') ?></button>
                <button type="button" class="carousel-nav__btn" data-carousel-next="more-products" aria-label="Next products"><?= icon('arrow-right') ?></button>
            </div>
        </div>
        <div class="carousel" id="more-products" data-carousel>
            <div class="carousel__track" data-anim-stagger="fade-up" data-stagger="0.1">
                <?php foreach ($others as $other): ?>
                <div class="carousel__slide"><?php partial('product-card', ['product' => $other]); ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php partial('cta-band'); ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
