<?php
/**
 * Contact Us.
 * (WordPress migration: page-contact-us.php — the form can post to admin-post.php
 *  or be replaced by a form plugin.)
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

session_start();
$flash = $_SESSION['contact_flash'] ?? null;
unset($_SESSION['contact_flash']);
$old    = $flash['old'] ?? [];
$errors = $flash['errors'] ?? [];

$selectedProduct = (string) ($old['product'] ?? ($_GET['product'] ?? ''));
if ($selectedProduct !== '' && get_product($selectedProduct) === null) {
    $selectedProduct = '';
}

$page = [
    'slug'        => 'contact-us',
    'path'        => 'contact-us/',
    'title'       => 'Contact Us',
    'description' => 'Contact Cooksmart Premium Food Private Limited — Registered Office: Singur, Hooghly. Corporate Office: Sarat Bose Road, Kolkata. Call +91 98000 87155 or email cooksmartkol@gmail.com.',
    'breadcrumbs' => [['label' => 'Home', 'path' => ''], ['label' => 'Contact Us', 'path' => 'contact-us/']],
];
enqueue_script('js/contact.js');

$mapSrc = static fn (array $a): string => 'https://www.google.com/maps?q=' . rawurlencode($a['map_query']) . '&output=embed';

require __DIR__ . '/includes/header.php';
?>
<main id="main">
<?php partial('page-hero', [
    'eyebrow' => 'We’d love to hear from you',
    'title'   => 'Contact Us',
    'lead'    => 'Trade enquiries, product questions or feedback — reach out and our team will get back to you.',
    'crumbs'  => $page['breadcrumbs'],
]); ?>

<section class="section contact-cards" aria-label="Contact details">
    <div class="container">
        <div class="info-cards" data-anim-stagger="fade-up" data-stagger="0.12">
            <a class="info-card" href="tel:<?= e(PHONE_E164) ?>">
                <span class="info-card__icon"><?= icon('phone') ?></span>
                <span class="info-card__label">Call Us</span>
                <span class="info-card__value"><?= e(PHONE_DISPLAY) ?></span>
            </a>
            <a class="info-card" href="mailto:<?= e(CONTACT_EMAIL) ?>">
                <span class="info-card__icon"><?= icon('mail') ?></span>
                <span class="info-card__label">Email Us</span>
                <span class="info-card__value"><?= e(CONTACT_EMAIL) ?></span>
            </a>
            <div class="info-card">
                <span class="info-card__icon"><?= icon('building') ?></span>
                <span class="info-card__label">Company</span>
                <span class="info-card__value"><?= e(COMPANY_LEGAL_NAME) ?></span>
            </div>
        </div>
    </div>
</section>

<section class="section section--cream section--pattern locations" aria-labelledby="locations-title">
    <div class="container">
        <?php partial('section-heading', ['eyebrow' => 'Visit Us', 'title' => 'Our <em>Offices</em>', 'id' => 'locations-title']); ?>
        <div class="locations__grid">
            <div class="locations__tabs" role="tablist" aria-label="Office locations" data-anim="slide-left">
                <?php $first = true; foreach (ADDRESSES as $key => $addr): ?>
                <button type="button" role="tab" id="tab-<?= e($key) ?>" class="location-tab<?= $first ? ' is-active' : '' ?>" aria-selected="<?= $first ? 'true' : 'false' ?>" aria-controls="panel-<?= e($key) ?>" data-location-tab="<?= e($key) ?>">
                    <span class="location-tab__icon"><?= icon($key === 'corporate' ? 'building' : 'map-pin') ?></span>
                    <span class="location-tab__text">
                        <strong><?= e($addr['label']) ?></strong>
                        <span id="<?= e($key) ?>"><?= implode(', ', array_map('e', $addr['lines'])) ?></span>
                    </span>
                    <?= icon('arrow-right', 'location-tab__arrow') ?>
                </button>
                <?php $first = false; endforeach; ?>
                <div class="locations__direct">
                    <a href="tel:<?= e(PHONE_E164) ?>"><?= icon('phone') ?><?= e(PHONE_DISPLAY) ?></a>
                    <a href="mailto:<?= e(CONTACT_EMAIL) ?>"><?= icon('mail') ?><?= e(CONTACT_EMAIL) ?></a>
                </div>
            </div>
            <div class="locations__maps" data-anim="zoom-in">
                <?php $first = true; foreach (ADDRESSES as $key => $addr): ?>
                <div role="tabpanel" id="panel-<?= e($key) ?>" aria-labelledby="tab-<?= e($key) ?>" class="map-panel<?= $first ? ' is-active' : '' ?>"<?= $first ? '' : ' hidden' ?> data-location-panel="<?= e($key) ?>">
                    <iframe title="Map: CookSmart <?= e($addr['label']) ?>" <?= $first ? 'src' : 'data-src' ?>="<?= e($mapSrc($addr)) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                    <a class="map-panel__open" href="https://www.google.com/maps/search/?api=1&amp;query=<?= e(rawurlencode($addr['map_query'])) ?>" target="_blank" rel="noopener"><?= icon('external') ?> Open in Google Maps</a>
                </div>
                <?php $first = false; endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="section enquiry" id="enquiry" aria-labelledby="enquiry-title">
    <div class="container enquiry__grid">
        <div class="enquiry__intro">
            <?php partial('section-heading', ['eyebrow' => 'Send an Enquiry', 'title' => 'Let’s <em>talk</em>', 'align' => 'left', 'id' => 'enquiry-title', 'lead' => 'Fill in the form and we will get back to you. For anything urgent, please call us directly.']); ?>
            <ul class="enquiry__points" data-anim-stagger="fade-up" data-stagger="0.1">
                <li><?= icon('store') ?>General &amp; Modern Trade</li>
                <li><?= icon('chef-hat') ?>HORECA &amp; bulk requirements</li>
                <li><?= icon('smile') ?>Product feedback</li>
            </ul>
            <div class="enquiry__packs" aria-hidden="true">
                <img src="<?= e(product_img(get_product('dhania-powder'), 'primary', 'sm')) ?>" alt="" width="120" height="180" loading="lazy">
                <img src="<?= e(product_img(get_product('chakki-fresh-atta'), 'primary', 'sm')) ?>" alt="" width="150" height="225" loading="lazy">
                <img src="<?= e(product_img(get_product('jeera-powder'), 'primary', 'sm')) ?>" alt="" width="120" height="180" loading="lazy">
            </div>
        </div>

        <div class="enquiry__card" data-anim="slide-right">
            <?php if (!empty($flash['sent'])): ?>
            <div class="form-success is-static" role="status">
                <svg class="form-success__check" viewBox="0 0 52 52" aria-hidden="true"><circle cx="26" cy="26" r="24"/><path d="M14 27 l8 8 l16 -18"/></svg>
                <h3>Thank you!</h3>
                <p><?= e($flash['message']) ?></p>
            </div>
            <?php endif; ?>
            <form class="enquiry-form" action="<?= e(url('contact-submit.php')) ?>" method="post" novalidate data-enquiry-form<?= !empty($flash['sent']) ? ' hidden' : '' ?>>
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <div class="hp-field" aria-hidden="true">
                    <label for="website">Leave this empty</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <?php if (!empty($flash['message']) && empty($flash['sent'])): ?>
                <p class="form-alert" role="alert"><?= e($flash['message']) ?></p>
                <?php else: ?>
                <p class="form-alert" role="alert" hidden></p>
                <?php endif; ?>

                <div class="form-row">
                    <div class="field<?= isset($errors['name']) ? ' has-error' : '' ?>">
                        <input type="text" id="f-name" name="name" placeholder=" " required maxlength="100" autocomplete="name" value="<?= e($old['name'] ?? '') ?>">
                        <label for="f-name">Your name *</label>
                        <p class="field__error" id="err-name"><?= e($errors['name'] ?? '') ?></p>
                    </div>
                    <div class="field<?= isset($errors['phone']) ? ' has-error' : '' ?>">
                        <input type="tel" id="f-phone" name="phone" placeholder=" " required maxlength="20" autocomplete="tel" inputmode="tel" value="<?= e($old['phone'] ?? '') ?>">
                        <label for="f-phone">Phone number *</label>
                        <p class="field__error" id="err-phone"><?= e($errors['phone'] ?? '') ?></p>
                    </div>
                </div>
                <div class="field<?= isset($errors['email']) ? ' has-error' : '' ?>">
                    <input type="email" id="f-email" name="email" placeholder=" " required maxlength="150" autocomplete="email" value="<?= e($old['email'] ?? '') ?>">
                    <label for="f-email">Email address *</label>
                    <p class="field__error" id="err-email"><?= e($errors['email'] ?? '') ?></p>
                </div>
                <div class="form-row">
                    <div class="field field--select">
                        <select id="f-type" name="type">
                            <?php foreach (enquiry_types() as $key => $label): ?>
                            <option value="<?= e($key) ?>"<?= ($old['type'] ?? '') === $key ? ' selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="f-type">Enquiry type</label>
                    </div>
                    <div class="field field--select">
                        <select id="f-product" name="product">
                            <option value="">Any / not specific</option>
                            <?php foreach (products() as $p): ?>
                            <option value="<?= e($p['slug']) ?>"<?= $selectedProduct === $p['slug'] ? ' selected' : '' ?>><?= e($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="f-product">Product</label>
                    </div>
                </div>
                <div class="field<?= isset($errors['message']) ? ' has-error' : '' ?>">
                    <textarea id="f-message" name="message" rows="5" placeholder=" " required maxlength="2000"><?= e($old['message'] ?? '') ?></textarea>
                    <label for="f-message">Your message *</label>
                    <p class="field__error" id="err-message"><?= e($errors['message'] ?? '') ?></p>
                </div>
                <button class="btn btn--primary btn--block magnetic" type="submit" data-submit>
                    <span class="btn__label">Send Enquiry</span><?= icon('arrow-right') ?>
                    <span class="btn__spinner" aria-hidden="true"></span>
                </button>
                <p class="form-note">By sending this form you agree that we may contact you about your enquiry.</p>
            </form>
            <div class="form-success" role="status" hidden data-form-success>
                <svg class="form-success__check" viewBox="0 0 52 52" aria-hidden="true"><circle cx="26" cy="26" r="24"/><path d="M14 27 l8 8 l16 -18"/></svg>
                <h3>Thank you!</h3>
                <p data-success-message>Your enquiry has been sent. We will get back to you soon.</p>
                <button type="button" class="btn btn--outline" data-form-reset><span>Send another enquiry</span></button>
            </div>
        </div>
    </div>
</section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
