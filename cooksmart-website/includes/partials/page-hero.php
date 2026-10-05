<?php
/**
 * Inner-page hero banner.
 * Vars: $title, $eyebrow, $crumbs (list of ['label','path']), $images (list of image URLs),
 *       $accent (optional CSS colour), $lead (optional).
 */
$images = $images ?? [];
$accent = $accent ?? null;
?>
<section class="page-hero<?= $images ? ' page-hero--with-media' : '' ?>"<?= $accent ? ' style="--hero-accent: ' . e($accent) . ';"' : '' ?>>
    <div class="page-hero__pattern" aria-hidden="true"></div>
    <div class="container page-hero__inner">
        <div class="page-hero__text">
            <?php if (!empty($eyebrow)): ?><p class="eyebrow eyebrow--light" data-anim="fade-up"><?= e($eyebrow) ?></p><?php endif; ?>
            <h1 class="page-hero__title" data-anim="split-up"><?= e($title) ?></h1>
            <?php if (!empty($lead)): ?><p class="page-hero__lead" data-anim="fade-up" data-delay="0.25"><?= e($lead) ?></p><?php endif; ?>
            <nav class="breadcrumb" aria-label="Breadcrumb" data-anim="fade-up" data-delay="0.35">
                <ol>
                    <?php foreach ($crumbs as $i => $c): $last = $i === array_key_last($crumbs); ?>
                    <li><?php if ($last): ?><span aria-current="page"><?= e($c['label']) ?></span><?php else: ?><a href="<?= e(url($c['path'])) ?>"><?= e($c['label']) ?></a><?php endif; ?></li>
                    <?php endforeach; ?>
                </ol>
            </nav>
        </div>
        <?php if ($images): ?>
        <div class="page-hero__media page-hero__media--<?= count($images) ?>" aria-hidden="true">
            <?php foreach ($images as $i => $src): ?>
            <img src="<?= e($src) ?>" alt="" class="page-hero__pack page-hero__pack--<?= $i + 1 ?>" width="200" height="300" decoding="async">
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <svg class="page-hero__edge" viewBox="0 0 1440 90" preserveAspectRatio="none" aria-hidden="true" focusable="false">
        <path d="M0 90 V38 C 420 -6, 1020 -6, 1440 38 V90 Z" fill="#319847"/>
        <path d="M0 90 V52 C 420 10, 1020 10, 1440 52 V90 Z" fill="#A8E13C"/>
        <path class="page-hero__edge-fill" d="M0 90 V62 C 420 22, 1020 22, 1440 62 V90 Z"/>
    </svg>
</section>
