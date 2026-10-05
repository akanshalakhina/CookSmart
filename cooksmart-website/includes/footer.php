<?php
/**
 * Global footer, floating actions, back-to-top and scripts.
 * (WordPress migration: footer.php — add wp_footer().)
 */
declare(strict_types=1);

$nav = nav_items();
?>
<footer class="site-footer">
    <svg class="site-footer__wave" viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true" focusable="false">
        <defs>
            <linearGradient id="footer-swoosh" x1="0" x2="1">
                <stop offset="0" stop-color="#319847"/><stop offset=".55" stop-color="#64B741"/><stop offset="1" stop-color="#A8E13C"/>
            </linearGradient>
        </defs>
        <path d="M0 120 V64 C 360 6, 1000 -10, 1440 52 V120 Z" fill="url(#footer-swoosh)"/>
        <path d="M0 120 V84 C 380 30, 1020 18, 1440 76 V120 Z" fill="#A8161A"/>
        <path d="M0 120 V100 C 400 56, 1040 44, 1440 96 V120 Z" fill="#7A0D11"/>
    </svg>

    <div class="site-footer__body">
        <div class="container site-footer__grid" data-anim-stagger="fade-up" data-stagger="0.1">
            <div class="footer-brand">
                <a href="<?= e(url()) ?>" class="footer-brand__badge" aria-label="CookSmart home">
                    <img src="<?= e(asset('images/logo/cooksmart-logo-sm.webp')) ?>" alt="CookSmart®" width="200" height="72" loading="lazy">
                </a>
                <p class="footer-brand__quote">“Quality is not expensive — it’s priceless.”</p>
                <p class="footer-brand__since">Serving Indian kitchens since <?= FOUNDED_YEAR ?>.</p>
                <a class="btn-shop" <?= shop_attrs() ?>><span>Shop Now</span><?= icon('cart') ?></a>
            </div>

            <div class="footer-col">
                <h2 class="footer-col__title">Quick Links</h2>
                <ul class="footer-links">
                    <?php foreach (array_merge($nav['left'], $nav['right']) as $item): ?>
                    <li><a href="<?= e(url($item['path'])) ?>"><?= icon('arrow-right') ?><?= e($item['label']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="footer-col">
                <h2 class="footer-col__title">Our Products</h2>
                <ul class="footer-links">
                    <?php foreach (products() as $p): ?>
                    <li><a href="<?= e(url('products/' . $p['slug'] . '/')) ?>"><?= icon('arrow-right') ?><?= e($p['name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="footer-col footer-col--contact">
                <h2 class="footer-col__title">Get in Touch</h2>
                <?php foreach (ADDRESSES as $addr): ?>
                <div class="footer-address">
                    <?= icon('map-pin') ?>
                    <p><strong><?= e($addr['label']) ?></strong><br><?= implode(', ', array_map('e', $addr['lines'])) ?></p>
                </div>
                <?php endforeach; ?>
                <a class="footer-contact" href="tel:<?= e(PHONE_E164) ?>"><?= icon('phone') ?><?= e(PHONE_DISPLAY) ?></a>
                <a class="footer-contact" href="mailto:<?= e(CONTACT_EMAIL) ?>"><?= icon('mail') ?><?= e(CONTACT_EMAIL) ?></a>
                <?php if ($socials = social_links()): ?>
                <ul class="footer-social">
                    <?php foreach ($socials as $network => $link): ?>
                    <li><a href="<?= e($link) ?>" target="_blank" rel="noopener" aria-label="<?= e(ucfirst($network)) ?>"><?= icon($network) ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="container site-footer__bottom">
            <p>© <?= date('Y') ?> <?= e(COMPANY_LEGAL_NAME) ?>. All rights reserved.</p>
            <p>CookSmart® is a registered trademark.</p>
        </div>
    </div>
</footer>

<?php partial('floating-actions'); ?>

<div class="toast" role="status" aria-live="polite" data-toast></div>

<script src="https://cdn.jsdelivr.net/npm/gsap@<?= GSAP_VERSION ?>/dist/gsap.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@<?= GSAP_VERSION ?>/dist/ScrollTrigger.min.js" defer></script>
<?php foreach (array_keys($GLOBALS['cs_scripts']) as $script): if (str_starts_with($script, 'https://')): ?>
<script src="<?= e($script) ?>" defer></script>
<?php endif; endforeach; ?>
<script src="<?= e(asset('js/animations.js')) ?>" defer></script>
<script src="<?= e(asset('js/main.js')) ?>" defer></script>
<?php foreach (array_keys($GLOBALS['cs_scripts']) as $script): if (!str_starts_with($script, 'https://')): ?>
<script src="<?= e(asset($script)) ?>" defer></script>
<?php endif; endforeach; ?>
<script>window.CS = { shopPendingMessage: <?= json_encode('Our online shop link is coming soon. Meanwhile, call us on ' . PHONE_DISPLAY . '.') ?> };</script>
</body>
</html>
