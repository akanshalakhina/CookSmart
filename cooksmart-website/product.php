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

$fullName = ($product['range'] === 'Sugrihini' ? 'Sugrihini ' : 'CookSmart ') . $product['name'];
$page = [
    'slug'        => 'products',
    'path'        => 'products/' . $product['slug'] . '/',
    'title'       => $fullName . ' — ' . pack_summary($product),
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

// same-category products first, then the rest of the range
$others = array_filter(products(), static fn ($p) => $p['slug'] !== $product['slug']);
uasort($others, static fn ($a, $b) => ($b['category'] === $product['category']) <=> ($a['category'] === $product['category']));

// pack sizes drawn side by side, biggest first; the first one starts selected
$packs   = $product['packs'];
$multi   = count($packs) > 1;
$lineSum = 0.0;
foreach ($packs as $pk) {
    $lineSum += $pk['scale'] * pack_aspect($product, $pk['id']);
}
$lineSum = round($lineSum * 0.88, 3); // packs overlap slightly, so they need a little less width

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
                <div class="pack-lineup" data-lineup data-view="front" style="--sum: <?= e((string) $lineSum) ?>;">
                    <?php foreach ($packs as $i => $pk): ?>
                    <button type="button" class="pack-lineup__item<?= $i === 0 ? ' is-selected' : '' ?>" data-pack="<?= e($pk['id']) ?>" data-label="<?= e($pk['label']) ?>"
                            data-thumb-front="<?= e(pack_img($product, $pk['id'], 'front', 'sm')) ?>" data-thumb-back="<?= e(pack_img($product, $pk['id'], 'back', 'sm')) ?>"
                            style="--s: <?= e((string) $pk['scale']) ?>; --ar: <?= e((string) pack_aspect($product, $pk['id'])) ?>; --z: <?= count($packs) - $i ?>;"
                            aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>" aria-label="Show the <?= e($pk['label']) ?> pack">
                        <span class="pack-lineup__frame">
                            <img src="<?= e(pack_img($product, $pk['id'], 'front')) ?>" alt="<?= e($fullName . ' pack, ' . $pk['label']) ?>"
                                 class="pack-lineup__img pack-lineup__img--front" width="600" height="900"<?= $i === 0 ? ' fetchpriority="high"' : '' ?>>
                            <img src="<?= e(pack_img($product, $pk['id'], 'back')) ?>" alt="<?= e($fullName . ' pack, ' . $pk['label'] . ' — back') ?>"
                                 class="pack-lineup__img pack-lineup__img--back" width="600" height="900" loading="lazy">
                        </span>
                        <span class="pack-lineup__tag"><?= e($pk['label']) ?></span>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="product-gallery__thumbs" role="group" aria-label="Pack views">
                <?php foreach (['front' => 'Front', 'back' => 'Back'] as $view => $viewLabel): ?>
                <button type="button" class="product-gallery__thumb<?= $view === 'front' ? ' is-active' : '' ?>" data-view-btn="<?= e($view) ?>" aria-pressed="<?= $view === 'front' ? 'true' : 'false' ?>" aria-label="Show the <?= e(strtolower($viewLabel)) ?> of the pack">
                    <img src="<?= e(pack_img($product, $packs[0]['id'], $view, 'sm')) ?>" alt="" width="70" height="105" loading="lazy" data-view-thumb="<?= e($view) ?>">
                    <span class="product-gallery__thumb-label"><?= e($viewLabel) ?></span>
                </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="product-info">
            <p class="product-info__range" data-anim="fade-up">
                <?= e($product['range']) ?><?php if ($product['range_bn'] !== ''): ?> · <span lang="bn"><?= e($product['range_bn']) ?></span><?php endif; ?>
            </p>
            <h2 id="product-name" class="product-info__name" data-anim="split-up"><?= e($product['name']) ?></h2>
            <p class="product-info__hi" lang="<?= e($product['name_lang']) ?>" data-anim="fade-up"><?= e($product['name_local']) ?></p>
            <?= swoosh_line() ?>
            <p class="product-info__desc" data-anim="fade-up" data-delay="0.1"><?= e($product['description']) ?></p>
            <?php if (!empty($product['price_pack'])): ?>
            <p class="rs5-callout" data-anim="fade-up" data-delay="0.12">
                <span class="rs5-callout__label">Available at</span>
                <span class="rs5-tag"><?= e(rupees($product['price_pack'])) ?></span>
                <span class="rs5-callout__text">Pocket-friendly pack</span>
            </p>
            <?php endif; ?>

            <div class="product-info__block" data-anim="fade-up" data-delay="0.15">
                <h3 class="product-info__label" id="pack-label"><?= $multi ? 'Choose pack size' : 'Net weight' ?></h3>
                <div class="pack-picker" role="radiogroup" aria-labelledby="pack-label" data-pack-picker>
                    <?php foreach ($packs as $i => $pk): ?>
                    <button type="button" role="radio" class="pack-option<?= $i === 0 ? ' is-active' : '' ?>" data-pack="<?= e($pk['id']) ?>" aria-checked="<?= $i === 0 ? 'true' : 'false' ?>" tabindex="<?= $i === 0 ? '0' : '-1' ?>">
                        <?= icon('package') ?><span><?= e($pk['label']) ?></span>
                    </button>
                    <?php endforeach; ?>
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
