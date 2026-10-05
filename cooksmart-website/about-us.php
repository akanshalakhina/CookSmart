<?php
/**
 * About Us.
 * (WordPress migration: page-about-us.php)
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page = [
    'slug'        => 'about-us',
    'path'        => 'about-us/',
    'title'       => 'About Us',
    'description' => 'CookSmart Premium Food began in 2015 under the leadership of Mr Santanu Saha. Today our turnover touches around ₹90 crore, with our own production.',
    'breadcrumbs' => [['label' => 'Home', 'path' => ''], ['label' => 'About Us', 'path' => 'about-us/']],
];
$c = company();

require __DIR__ . '/includes/header.php';
?>
<main id="main">
<?php partial('page-hero', [
    'eyebrow' => 'Our Story',
    'title'   => 'About Us',
    'lead'    => 'Since 2015 — with one and only objective: quality is not expensive, it’s priceless.',
    'crumbs'  => $page['breadcrumbs'],
    'images'  => [product_img(get_product('mirchi-powder'), 'primary', 'sm'), product_img(get_product('chakki-fresh-atta'), 'primary', 'sm'), product_img(get_product('haldi-powder'), 'primary', 'sm')],
]); ?>

<!-- Story + journey -->
<section class="section story" aria-labelledby="story-title">
    <div class="container story__grid">
        <div class="story__text">
            <?php partial('section-heading', ['eyebrow' => 'Who We Are', 'title' => 'A journey that began in <em>2015</em>', 'align' => 'left', 'id' => 'story-title']); ?>
            <p class="lead-text" data-anim="fade-up"><?= e($c['intro']) ?></p>
            <p data-anim="fade-up" data-delay="0.1"><?= e($c['philosophy'][0]) ?></p>
            <blockquote class="story__quote" data-anim="slide-left">
                <?= icon('quote') ?>
                <p><?= e($c['objective']) ?></p>
                <cite>Our one and only objective</cite>
            </blockquote>
        </div>
        <div class="journey" data-anim="fade-up">
            <svg class="journey__line" viewBox="0 0 4 400" preserveAspectRatio="none" aria-hidden="true"><path d="M2 0 V400"/></svg>
            <div class="journey__step">
                <span class="journey__dot"></span>
                <p class="journey__year"><?= FOUNDED_YEAR ?></p>
                <h3 class="journey__title">The beginning</h3>
                <p>Started our journey in the food processing sector, under the able guidance and leadership of <?= e(LEADER_NAME) ?>.</p>
            </div>
            <div class="journey__step">
                <span class="journey__dot"></span>
                <p class="journey__year">Today</p>
                <h3 class="journey__title">No looking back</h3>
                <p>Our turnover touches around ₹90 crore — with our own production.</p>
            </div>
        </div>
    </div>
</section>

<!-- Leadership -->
<section class="section section--cream leader" aria-labelledby="leader-title">
    <div class="container leader__grid">
        <div class="leader__portrait" data-anim="zoom-in" aria-hidden="true">
            <!-- Replace this monogram with a real photo: <img src="assets/images/leader.webp" alt="Mr Santanu Saha"> -->
            <span class="leader__ring"></span>
            <span class="leader__monogram">SS</span>
        </div>
        <div class="leader__text">
            <p class="eyebrow" data-anim="fade-up">Leadership</p>
            <p class="leader__intro" data-anim="fade-up">Under the able guidance &amp; leadership of</p>
            <h2 id="leader-title" class="leader__name" data-anim="split-up"><?= e(LEADER_NAME) ?></h2>
            <?= swoosh_line() ?>
            <p data-anim="fade-up" data-delay="0.1">With this guidance, CookSmart Premium Food began its journey in <?= FOUNDED_YEAR ?> — and since then there has been no looking back.</p>
        </div>
    </div>
</section>

<!-- Numbers -->
<section class="section numbers" aria-labelledby="numbers-title">
    <div class="container">
        <?php partial('section-heading', ['eyebrow' => 'CookSmart in Numbers', 'title' => 'Growing with <em>every kitchen</em>', 'id' => 'numbers-title']); ?>
        <?php partial('counters'); ?>
    </div>
</section>

<!-- Philosophy -->
<section class="philosophy" aria-labelledby="philosophy-title">
    <div class="philosophy__pattern" aria-hidden="true"></div>
    <div class="container philosophy__grid">
        <div>
            <p class="eyebrow eyebrow--light" data-anim="fade-up">Our Philosophy</p>
            <h2 id="philosophy-title" class="philosophy__title" data-anim="split-up">The internal power of creativity</h2>
        </div>
        <div class="philosophy__text" data-anim-stagger="fade-up" data-stagger="0.12">
            <?php foreach ($c['philosophy'] as $para): ?>
            <p><?= e($para) ?></p>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Brand family -->
<section class="section brands" aria-labelledby="brands-title">
    <div class="container">
        <?php partial('section-heading', ['eyebrow' => 'Our Brands', 'title' => 'The CookSmart <em>family</em>', 'id' => 'brands-title']); ?>
        <div class="brands__grid">
            <a class="brand-tile brand-tile--sugrihini" href="<?= e(url('products/#spices')) ?>" data-anim="slide-left">
                <div class="brand-tile__text">
                    <p class="brand-tile__kicker">Spices</p>
                    <h3 class="brand-tile__name">Sugrihini <span lang="bn">সুগৃহিণী</span></h3>
                    <p>“The good homemaker” — Mirchi, Haldi, Jeera and Dhania powder.</p>
                    <span class="brand-tile__link">Explore spices <?= icon('arrow-right') ?></span>
                </div>
                <div class="brand-tile__packs" aria-hidden="true">
                    <?php foreach (products_in('spices') as $i => $p): ?>
                    <img src="<?= e(product_img($p, 'primary', 'sm')) ?>" alt="" width="120" height="180" loading="lazy" class="brand-tile__pack">
                    <?php endforeach; ?>
                </div>
            </a>
            <a class="brand-tile brand-tile--atta" href="<?= e(url('products/chakki-fresh-atta/')) ?>" data-anim="slide-right">
                <div class="brand-tile__text">
                    <p class="brand-tile__kicker">Atta</p>
                    <h3 class="brand-tile__name">Chakki Fresh</h3>
                    <p>Premium quality natural whole wheat atta — natural &amp; fresh.</p>
                    <span class="brand-tile__link">Explore atta <?= icon('arrow-right') ?></span>
                </div>
                <div class="brand-tile__packs" aria-hidden="true">
                    <img src="<?= e(product_img(get_product('chakki-fresh-atta'), 'primary', 'sm')) ?>" alt="" width="160" height="240" loading="lazy" class="brand-tile__pack">
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Vision · Mission · Values -->
<section class="section section--cream section--pattern vmv" aria-labelledby="vmv-title">
    <div class="container">
        <?php partial('section-heading', ['eyebrow' => 'What Drives Us', 'title' => 'Vision, Mission &amp; <em>Values</em>', 'id' => 'vmv-title']); ?>
        <div class="vmv__grid" data-anim-stagger="fade-up" data-stagger="0.14">
            <article class="arch-card">
                <span class="arch-card__icon"><?= icon('eye') ?></span>
                <h3>Vision</h3>
                <p><?= e($c['vision']) ?></p>
            </article>
            <article class="arch-card arch-card--featured">
                <span class="arch-card__icon"><?= icon('target') ?></span>
                <h3>Mission</h3>
                <p><?= e($c['mission']) ?></p>
            </article>
            <article class="arch-card">
                <span class="arch-card__icon"><?= icon('heart') ?></span>
                <h3>Values</h3>
                <p><?= e($c['values']) ?></p>
            </article>
        </div>
        <p class="vm__more" data-anim="fade-up"><a class="btn btn--outline magnetic" href="<?= e(url('company-policy/')) ?>"><span>Read Company Policy</span><?= icon('arrow-right') ?></a></p>
    </div>
</section>

<!-- Offices -->
<section class="section offices" aria-labelledby="offices-title">
    <div class="container">
        <?php partial('section-heading', ['eyebrow' => 'Where to Find Us', 'title' => 'Our <em>Offices</em>', 'id' => 'offices-title']); ?>
        <div class="offices__grid" data-anim-stagger="fade-up" data-stagger="0.14">
            <?php foreach (ADDRESSES as $key => $addr): ?>
            <article class="office-card">
                <span class="office-card__icon"><?= icon($key === 'corporate' ? 'building' : 'map-pin') ?></span>
                <h3><?= e($addr['label']) ?></h3>
                <address><?= implode('<br>', array_map('e', $addr['lines'])) ?></address>
                <a href="<?= e(url('contact-us/#' . $key)) ?>" class="office-card__link">View on map <?= icon('arrow-right') ?></a>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php partial('cta-band'); ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
