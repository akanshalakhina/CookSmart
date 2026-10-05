<?php
/** Animated product card. Vars: $product (array). */
$detailUrl = url('products/' . $product['slug'] . '/');
?>
<article class="product-card" data-category="<?= e($product['category']) ?>" style="--accent: <?= e($product['accent']) ?>; --accent-soft: <?= e($product['accent_soft']) ?>;" data-tilt>
    <a class="product-card__link" href="<?= e($detailUrl) ?>">
        <div class="product-card__media">
            <span class="product-card__blob" aria-hidden="true"></span>
            <span class="product-card__ring" aria-hidden="true"></span>
            <img src="<?= e(product_img($product, 'primary', 'sm')) ?>" alt="<?= e($product['range'] . ' ' . $product['name'] . ' pack, ' . $product['net_weights'][0]) ?>" width="280" height="420" loading="lazy" decoding="async" class="product-card__img">
        </div>
        <div class="product-card__body">
            <p class="product-card__range">
                <?= e($product['range']) ?><?php if ($product['range_bn'] !== ''): ?> · <span lang="bn"><?= e($product['range_bn']) ?></span><?php endif; ?>
            </p>
            <h3 class="product-card__name"><?= e($product['name']) ?></h3>
            <p class="product-card__hi" lang="hi"><?= e($product['name_hi']) ?></p>
            <div class="product-card__meta">
                <?php foreach ($product['net_weights'] as $w): ?><span class="chip"><?= e($w) ?></span><?php endforeach; ?>
                <span class="product-card__more">View details <?= icon('arrow-right') ?></span>
            </div>
        </div>
    </a>
</article>
