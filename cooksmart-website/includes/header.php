<?php
/**
 * Global header: <head>, preloader, page curtain, top strip, centred-logo navigation.
 * Expects $page = ['slug', 'title', 'description', 'path', 'body_class'?].
 * (WordPress migration: header.php — add wp_head(), body_class(), wp_nav_menu().)
 */
declare(strict_types=1);

$page        = $page ?? [];
$slug        = $page['slug'] ?? '';
$titleTag    = ($slug === 'home')
    ? 'CookSmart® — Quality is not expensive, it’s priceless | Sugrihini Spices & Chakki Fresh Atta'
    : ($page['title'] ?? '') . ' | CookSmart®';
$description = $page['description'] ?? '';
$canonical   = absolute_url($page['path'] ?? '');
$nav         = nav_items();
$socials     = social_links();

$orgSchema = [
    '@context' => 'https://schema.org',
    '@type'    => 'Organization',
    'name'     => COMPANY_LEGAL_NAME,
    'alternateName' => SITE_NAME,
    'url'      => absolute_url(),
    'logo'     => absolute_url('assets/images/logo/cooksmart-logo.png'),
    'email'    => CONTACT_EMAIL,
    'telephone'=> PHONE_E164,
    'foundingDate' => (string) FOUNDED_YEAR,
    'address'  => array_values(array_map(static fn ($a) => [
        '@type'           => 'PostalAddress',
        'name'            => $a['label'],
        'streetAddress'   => $a['street'],
        'addressLocality' => $a['locality'],
        'addressRegion'   => $a['region'],
        'postalCode'      => $a['postcode'],
        'addressCountry'  => 'IN',
    ], ADDRESSES)),
];
if ($socials) {
    $orgSchema['sameAs'] = array_values($socials);
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($titleTag) ?></title>
<meta name="description" content="<?= e($description) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<meta name="theme-color" content="#C3161A">

<meta property="og:type" content="website">
<meta property="og:site_name" content="CookSmart">
<meta property="og:title" content="<?= e($titleTag) ?>">
<meta property="og:description" content="<?= e($description) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e(absolute_url('assets/images/og-image.jpg')) ?>">
<meta property="og:locale" content="en_IN">
<meta name="twitter:card" content="summary_large_image">

<link rel="icon" type="image/png" sizes="32x32" href="<?= e(asset('images/logo/favicon-32.png')) ?>">
<link rel="icon" type="image/png" sizes="48x48" href="<?= e(asset('images/logo/favicon-48.png')) ?>">
<link rel="apple-touch-icon" href="<?= e(asset('images/logo/apple-touch-icon.png')) ?>">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400..700&family=Fraunces:opsz,wght,SOFT@9..144,600..900,100&family=Hind:wght@500;600&family=Hind+Siliguri:wght@600;700&family=Kaushan+Script&display=swap">
<link rel="stylesheet" href="<?= e(asset('css/main.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('css/pages.css')) ?>">
<?php if (!empty($page['preload_image'])): ?>
<link rel="preload" as="image" href="<?= e($page['preload_image']) ?>">
<?php endif; ?>

<script>
/* Runs before first paint: enables animation states, preloader and page-curtain handoff. */
(function (d, s) {
  var h = d.documentElement;
  h.classList.add('js');
  try {
    if (!s.getItem('cs-visited')) { h.classList.add('is-first-visit'); s.setItem('cs-visited', '1'); }
    if (s.getItem('cs-curtain') === '1') { h.classList.add('is-entering'); s.removeItem('cs-curtain'); }
  } catch (err) {}
  // Failsafe: if the animation engine never starts, show all content.
  setTimeout(function () { if (!h.classList.contains('anim-ready')) h.classList.remove('js', 'is-first-visit', 'is-entering'); }, 4000);
})(document, window.sessionStorage);
</script>
<script type="application/ld+json"><?= json_encode($orgSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php if (!empty($page['breadcrumbs'])): ?>
<script type="application/ld+json"><?= json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'BreadcrumbList',
    'itemListElement' => array_map(static fn ($c, $i) => [
        '@type' => 'ListItem', 'position' => $i + 1, 'name' => $c['label'], 'item' => absolute_url($c['path']),
    ], $page['breadcrumbs'], array_keys($page['breadcrumbs'])),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php endif; ?>
</head>
<body class="page-<?= e($slug) ?> <?= e($page['body_class'] ?? '') ?>">
<a class="skip-link" href="#main">Skip to content</a>

<?php partial('preloader'); ?>

<div class="curtain" aria-hidden="true">
    <div class="curtain__panel curtain__panel--green"></div>
    <div class="curtain__panel curtain__panel--red">
        <img src="<?= e(asset('images/logo/cooksmart-logo-sm.webp')) ?>" alt="" width="180" height="64" class="curtain__logo">
    </div>
</div>

<div class="topbar">
    <div class="container topbar__inner">
        <div class="topbar__contact">
            <a href="tel:<?= e(PHONE_E164) ?>"><?= icon('phone') ?><span><?= e(PHONE_DISPLAY) ?></span></a>
            <a href="mailto:<?= e(CONTACT_EMAIL) ?>"><?= icon('mail') ?><span><?= e(CONTACT_EMAIL) ?></span></a>
        </div>
        <div class="topbar__right">
            <p class="topbar__tagline"><?= icon('sparkle') ?><span>Quality is not expensive — it’s priceless</span></p>
            <?php if ($socials): ?>
            <ul class="topbar__social" aria-label="CookSmart on social media">
                <?php foreach ($socials as $network => $link): ?>
                <li><a href="<?= e($link) ?>" target="_blank" rel="noopener" aria-label="<?= e(ucfirst($network)) ?>"><?= icon($network) ?></a></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<header class="site-header" data-header>
    <div class="container site-header__inner">
        <a class="header-cart" <?= shop_attrs() ?> aria-label="Shop Now">
            <?= icon('cart') ?>
        </a>

        <nav class="main-nav main-nav--left" aria-label="Main">
            <ul>
                <?php foreach ($nav['left'] as $key => $item): ?>
                <li><a class="nav-link<?= is_current($key, $slug) ? ' is-active' : '' ?>" href="<?= e(url($item['path'])) ?>"<?= is_current($key, $slug) ? ' aria-current="page"' : '' ?>><?= e($item['label']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <a class="brand-badge" href="<?= e(url()) ?>" aria-label="CookSmart home">
            <span class="brand-badge__plate" aria-hidden="true"></span>
            <img src="<?= e(asset('images/logo/cooksmart-logo.webp')) ?>" alt="CookSmart®" width="360" height="129" class="brand-badge__logo">
        </a>

        <nav class="main-nav main-nav--right" aria-label="Main (continued)">
            <ul>
                <?php foreach ($nav['right'] as $key => $item): ?>
                <li><a class="nav-link<?= is_current($key, $slug) ? ' is-active' : '' ?>" href="<?= e(url($item['path'])) ?>"<?= is_current($key, $slug) ? ' aria-current="page"' : '' ?>><?= e($item['label']) ?></a></li>
                <?php endforeach; ?>
                <li><a class="btn-shop magnetic" <?= shop_attrs() ?>><span>Shop Now</span><?= icon('cart') ?></a></li>
            </ul>
        </nav>

        <button class="burger" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-menu" data-burger>
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<div class="mobile-menu" id="mobile-menu" aria-hidden="true" data-mobile-menu>
    <div class="mobile-menu__bg" aria-hidden="true"></div>
    <nav class="mobile-menu__nav" aria-label="Mobile">
        <ul>
            <?php $i = 0; foreach (array_merge($nav['left'], $nav['right']) as $key => $item): $i++; ?>
            <li><a href="<?= e(url($item['path'])) ?>" class="<?= is_current($key, $slug) ? 'is-active' : '' ?>"><span class="mobile-menu__num">0<?= $i ?></span><?= e($item['label']) ?></a></li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <div class="mobile-menu__footer">
        <a class="btn-shop btn-shop--lg" <?= shop_attrs() ?>><span>Shop Now</span><?= icon('cart') ?></a>
        <a href="tel:<?= e(PHONE_E164) ?>"><?= icon('phone') ?> <?= e(PHONE_DISPLAY) ?></a>
        <a href="mailto:<?= e(CONTACT_EMAIL) ?>"><?= icon('mail') ?> <?= e(CONTACT_EMAIL) ?></a>
    </div>
</div>
