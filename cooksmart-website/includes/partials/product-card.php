<?php
/** Animated product card. Vars: $product (array). */
$detailUrl = url('products/' . $product['slug'] . '/');
?>
<article class="product-card" data-category="<?= e($product['category']) ?>" style="--accent: <?= e($product['accent']) ?>; --accent-soft: <?= e($product['accent_soft']) ?>;" data-tilt>
    <a class="product-card__link" href="<?= e($detailUrl) ?>">
        <div class="product-card__media">
            <span class="product-card__blob" aria-hidden="true"></span>
            <span class="product-card__ring" aria-hidden="true"></span>
            <img src="<?= e(product_img($product, 'primary', 'sm')) ?>" alt="<?= e($product['range'] . ' ' . $product['name'] . ' pack, ' . $product['packs'][0]['label']) ?>" width="280" height="420" loading="lazy" decoding="async" class="product-card__img">
        </div>
        <div class="product-card__body">
            <p class="product-card__range">
                <?= e($product['range']) ?><?php if ($product['range_bn'] !== ''): ?> · <span lang="bn"><?= e($product['range_bn']) ?></span><?php endif; ?>
            </p>
            <h3 class="product-card__name"><?= e($product['name']) ?></h3>
            <p class="product-card__hi" lang="<?= e($product['name_lang']) ?>"><?= e($product['name_local']) ?></p>
            <div class="product-card__meta">
                <?php foreach (pack_labels($product) as $w): ?><span class="chip"><?= e($w) ?></span><?php endforeach; ?>
                <?php if (!empty($product['price_pack'])): ?><span class="chip chip--price" title="Also available in <?= e(rupees($product['price_pack'])) ?> packet">Also <?= e(rupees($product['price_pack'])) ?> packet</span><?php endif; ?>
                <span class="product-card__more">View details <?= icon('arrow-right') ?></span>
            </div>
        </div>
    </a>
</article>
