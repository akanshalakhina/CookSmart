<?php /** Floating quick actions (right edge) and back-to-top with scroll progress. */ ?>
<div class="fab" role="group" aria-label="Quick contact">
    <a class="fab__btn fab__btn--call" href="tel:<?= e(PHONE_E164) ?>" aria-label="Call <?= e(PHONE_DISPLAY) ?>">
        <?= icon('phone') ?><span class="fab__label">Call us</span>
    </a>
    <?php if (WHATSAPP_ENABLED): ?>
    <a class="fab__btn fab__btn--wa" href="https://wa.me/<?= e(ltrim(PHONE_E164, '+')) ?>" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
        <?= icon('whatsapp') ?><span class="fab__label">WhatsApp</span>
    </a>
    <?php endif; ?>
    <a class="fab__btn fab__btn--mail" href="mailto:<?= e(CONTACT_EMAIL) ?>" aria-label="Email <?= e(CONTACT_EMAIL) ?>">
        <?= icon('mail') ?><span class="fab__label">Email us</span>
    </a>
    <a class="fab__btn fab__btn--shop" <?= shop_attrs() ?> aria-label="Shop Now">
        <?= icon('cart') ?><span class="fab__label">Shop Now</span>
    </a>
</div>

<button class="to-top" type="button" aria-label="Back to top" data-to-top>
    <svg class="to-top__ring" viewBox="0 0 48 48" aria-hidden="true">
        <defs>
            <linearGradient id="to-top-grad" x1="0" x2="1" y1="0" y2="1">
                <stop offset="0" stop-color="#319847"/><stop offset="1" stop-color="#A8E13C"/>
            </linearGradient>
        </defs>
        <circle cx="24" cy="24" r="21" class="to-top__track"/>
        <circle cx="24" cy="24" r="21" class="to-top__progress" stroke="url(#to-top-grad)"/>
    </svg>
    <?= icon('arrow-up') ?>
</button>
