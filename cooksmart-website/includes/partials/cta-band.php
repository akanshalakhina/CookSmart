<?php /** Closing call-to-action band. Vars: $title (optional), $text (optional). */ ?>
<section class="cta-band" aria-labelledby="cta-title">
    <div class="container">
        <div class="cta-band__card" data-anim="zoom-in">
            <div class="cta-band__text">
                <p class="eyebrow eyebrow--light">CookSmart®</p>
                <h2 id="cta-title" class="cta-band__title"><?= e($title ?? 'Bring CookSmart home today.') ?></h2>
                <p><?= e($text ?? 'Sugrihini spices and Chakki Fresh Atta — made with an unwavering commitment to quality.') ?></p>
                <div class="cta-band__actions">
                    <a class="btn-shop btn-shop--lg magnetic" <?= shop_attrs() ?>><span>Shop Now</span><?= icon('cart') ?></a>
                    <a class="btn btn--ghost-light magnetic" href="<?= e(url('contact-us/')) ?>"><span>Contact Us</span><?= icon('arrow-right') ?></a>
                </div>
            </div>
            <div class="cta-band__packs" aria-hidden="true">
                <?php foreach (['mirchi-powder', 'chakki-fresh-atta', 'haldi-powder'] as $i => $s): $p = get_product($s); ?>
                <img src="<?= e(product_img($p, 'primary', 'sm')) ?>" alt="" width="140" height="210" loading="lazy" class="cta-band__pack cta-band__pack--<?= $i + 1 ?>">
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
