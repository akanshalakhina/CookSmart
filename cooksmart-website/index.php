<?php
/**
 * Home page.
 * (WordPress migration: front-page.php)
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page = [
    'slug'        => 'home',
    'path'        => '',
    'title'       => 'Home',
    'description' => 'CookSmart Premium Food — Sugrihini spices (Mirchi, Haldi, Jeera, Dhania) and Chakki Fresh Atta from West Bengal. Quality is not expensive — it’s priceless. Since 2015.',
];
enqueue_script('js/hero.js');
enqueue_script('js/carousel.js');

$c        = company();
$spices   = products_in('spices');
$atta     = get_product('chakki-fresh-atta');
$mirchi   = get_product('mirchi-powder');
$haldi    = get_product('haldi-powder');
$jeera    = get_product('jeera-powder');
$dhania   = get_product('dhania-powder');
// Drop a real photograph at assets/images/hero/family-kitchen.webp to turn slide 1 into a photo banner.
$familyPhoto = asset_exists('images/hero/family-kitchen.webp');
if ($familyPhoto) {
    $page['preload_image'] = asset('images/hero/family-kitchen.webp');
}

require __DIR__ . '/includes/header.php';
?>
<main id="main">

<!-- ================= HERO SLIDER ================= -->
<section class="hero" data-hero aria-roledescription="carousel" aria-label="CookSmart highlights">
    <div class="hero__slides">

        <!-- Slide 1 · Emotional banner -->
        <article class="hero-slide hero-slide--family is-active<?= $familyPhoto ? ' has-photo' : '' ?>" data-slide aria-roledescription="slide" aria-label="1 of 3">
            <div class="hero-slide__bg" aria-hidden="true">
                <?php if ($familyPhoto): ?>
                <img src="<?= e(asset('images/hero/family-kitchen.webp')) ?>" alt="" class="hero-slide__photo" fetchpriority="high">
                <?php endif; ?>
                <span class="hero-glow hero-glow--1"></span>
                <span class="hero-glow hero-glow--2"></span>
                <span class="hero-watermark" lang="bn">সুগৃহিণী</span>
            </div>
            <div class="container hero-slide__inner">
                <div class="hero-slide__text">
                    <p class="eyebrow" data-hero-item>Since <?= FOUNDED_YEAR ?> · From our kitchen to yours</p>
                    <h1 class="hero-title" data-hero-title>Quality is not expensive — <em>it’s priceless.</em></h1>
                    <p class="hero-lead" data-hero-item>From our own production to your family’s kitchen — spices and atta made with an unwavering commitment to quality.</p>
                    <div class="hero-actions" data-hero-item>
                        <a class="btn btn--primary magnetic" href="<?= e(url('products/')) ?>"><span>Explore Products</span><?= icon('arrow-right') ?></a>
                        <a class="btn-shop btn-shop--lg magnetic" <?= shop_attrs() ?>><span>Shop Now</span><?= icon('cart') ?></a>
                    </div>
                </div>
                <div class="hero-slide__visual hero-stage" aria-hidden="true">
                    <span class="hero-stage__sun"></span>
                    <svg class="hero-stage__swoosh" viewBox="0 0 600 160" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="stage-green" x1="0" x2="1">
                                <stop offset="0" stop-color="#319847"/><stop offset=".55" stop-color="#64B741"/><stop offset="1" stop-color="#A8E13C"/>
                            </linearGradient>
                        </defs>
                        <path d="M0 150 C 140 30, 420 10, 600 70 L 600 100 C 430 50, 160 70, 0 160 Z" fill="url(#stage-green)"/>
                        <path d="M0 160 C 160 70, 430 50, 600 100 L 600 130 C 440 90, 180 110, 30 160 Z" fill="#A8161A"/>
                    </svg>
                    <img src="<?= e(product_img($atta)) ?>" alt="" class="hero-stage__pack hero-stage__pack--atta" width="300" height="450" data-hero-pack>
                    <img src="<?= e(product_img($jeera, 'primary', 'sm')) ?>" alt="" class="hero-stage__pack hero-stage__pack--1" width="160" height="240" data-hero-pack>
                    <img src="<?= e(product_img($mirchi, 'primary', 'sm')) ?>" alt="" class="hero-stage__pack hero-stage__pack--2" width="180" height="270" data-hero-pack>
                    <img src="<?= e(product_img($haldi, 'primary', 'sm')) ?>" alt="" class="hero-stage__pack hero-stage__pack--3" width="180" height="270" data-hero-pack>
                    <img src="<?= e(product_img($dhania, 'primary', 'sm')) ?>" alt="" class="hero-stage__pack hero-stage__pack--4" width="160" height="240" data-hero-pack>
                </div>
            </div>
        </article>

        <!-- Slide 2 · Sugrihini spices with rotating plate -->
        <article class="hero-slide hero-slide--spices" data-slide aria-roledescription="slide" aria-label="2 of 3">
            <div class="hero-slide__bg" aria-hidden="true"></div>
            <div class="container hero-slide__inner">
                <div class="hero-slide__text">
                    <p class="eyebrow eyebrow--light" data-hero-item>Sugrihini · <span lang="bn">সুগৃহিণী</span></p>
                    <h2 class="hero-title" data-hero-title>The taste of a good homemaker’s <em>kitchen.</em></h2>
                    <p class="hero-lead" data-hero-item>Sugrihini means “the good homemaker”. Mirchi, Haldi, Jeera and Dhania — everyday spices, ready to use in convenient packs.</p>
                    <div class="hero-collage" data-hero-item>
                        <?php foreach ($spices as $p): ?>
                        <a href="<?= e(url('products/' . $p['slug'] . '/')) ?>" class="hero-collage__item" style="--accent: <?= e($p['accent']) ?>">
                            <img src="<?= e(product_img($p, 'primary', 'sm')) ?>" alt="<?= e('Sugrihini ' . $p['name']) ?>" width="90" height="135" loading="lazy">
                            <span><?= e(str_replace(' Powder', '', $p['name'])) ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="hero-actions" data-hero-item>
                        <a class="btn btn--ghost-light magnetic" href="<?= e(url('products/#spices')) ?>"><span>View Spices</span><?= icon('arrow-right') ?></a>
                        <a class="btn-shop btn-shop--lg magnetic" <?= shop_attrs() ?>><span>Shop Now</span><?= icon('cart') ?></a>
                    </div>
                </div>
                <div class="hero-slide__visual hero-plate" aria-hidden="true">
                    <span class="hero-plate__halo"></span>
                    <span class="hero-plate__ring"></span>
                    <img src="<?= e(asset(asset_exists('images/spice-plate.webp') ? 'images/spice-plate.webp' : 'images/spice-plate.svg')) ?>" alt="Sugrihini Spices" class="hero-plate__img" width="600" height="600" data-hero-pack>
                </div>
            </div>
        </article>

        <!-- Slide 3 · Chakki Fresh Atta -->
        <article class="hero-slide hero-slide--atta" data-slide aria-roledescription="slide" aria-label="3 of 3">
            <div class="hero-slide__bg" aria-hidden="true">
                <?= icon('wheat', 'hero-wheat hero-wheat--l') ?>
                <?= icon('wheat', 'hero-wheat hero-wheat--r') ?>
            </div>
            <div class="container hero-slide__inner">
                <div class="hero-slide__text">
                    <p class="eyebrow" data-hero-item>CookSmart · Chakki Fresh</p>
                    <h2 class="hero-title" data-hero-title>Chakki Fresh Atta — <em>Natural &amp; Fresh.</em></h2>
                    <p class="hero-lead" data-hero-item>Natural whole wheat, hygienically packed for your healthy life.</p>
                    <ul class="hero-claims" data-hero-item>
                        <li><?= icon('check') ?>100% Natural</li>
                        <li><?= icon('check') ?>No Additives</li>
                        <li><?= icon('check') ?>No Preservatives</li>
                    </ul>
                    <div class="hero-actions" data-hero-item>
                        <a class="btn btn--primary magnetic" href="<?= e(url('products/chakki-fresh-atta/')) ?>"><span>View Atta</span><?= icon('arrow-right') ?></a>
                        <a class="btn-shop btn-shop--lg magnetic" <?= shop_attrs() ?>><span>Shop Now</span><?= icon('cart') ?></a>
                    </div>
                </div>
                <div class="hero-slide__visual hero-atta" aria-hidden="true">
                    <span class="hero-atta__disc"></span>
                    <span class="hero-atta__ring"></span>
                    <img src="<?= e(product_img($atta)) ?>" alt="" class="hero-atta__pack" width="320" height="480" data-hero-pack>
                    <span class="hero-atta__tag hero-atta__tag--1" data-hero-pack><?= icon('package') ?>5 kg pack</span>
                    <span class="hero-atta__tag hero-atta__tag--2" data-hero-pack><?= icon('leaf') ?>Natural Whole Wheat</span>
                    <span class="hero-atta__tag hero-atta__tag--3" data-hero-pack><?= icon('shield') ?>Hygienically Packed</span>
                </div>
            </div>
        </article>
    </div>

    <div class="hero__controls">
        <div class="container hero__controls-inner">
            <button class="hero__arrow" type="button" data-hero-prev aria-label="Previous slide"><?= icon('arrow-left') ?></button>
            <button class="hero__arrow" type="button" data-hero-next aria-label="Next slide"><?= icon('arrow-right') ?></button>
        </div>
    </div>
</section>

<!-- ================= PROMISE STRIP ================= -->
<section class="promise" aria-label="Our promise">
    <div class="container">
        <ul class="promise__grid" data-anim-stagger="fade-up" data-stagger="0.12">
            <li class="promise-card">
                <span class="promise-card__icon"><?= icon('factory') ?></span>
                <h2 class="promise-card__title">Own Production</h2>
                <p>Everything we sell comes from our own production.</p>
            </li>
            <li class="promise-card">
                <span class="promise-card__icon"><?= icon('award') ?></span>
                <h2 class="promise-card__title">Quality First</h2>
                <p>Our one and only objective: quality is not expensive — it’s priceless.</p>
            </li>
            <li class="promise-card">
                <span class="promise-card__icon"><?= icon('badge-check') ?></span>
                <h2 class="promise-card__title">100% Satisfaction Guarantee</h2>
                <p>Printed on every Sugrihini spice pack.</p>
            </li>
            <li class="promise-card">
                <span class="promise-card__icon"><?= icon('package') ?></span>
                <h2 class="promise-card__title">Convenient Packs</h2>
                <p>Ready-to-use, in packs and sizes made for modern Indian kitchens.</p>
            </li>
        </ul>
    </div>
</section>

<!-- ================= ABOUT TEASER ================= -->
<section class="section about-teaser" aria-labelledby="about-teaser-title">
    <div class="container about-teaser__grid">
        <div class="about-visual" data-anim="zoom-in">
            <div class="about-visual__arch">
                <div class="about-visual__pattern" aria-hidden="true"></div>
                <span class="about-visual__badge"><img src="<?= e(asset('images/logo/cooksmart-logo-sm.webp')) ?>" alt="CookSmart®" width="200" height="72" loading="lazy"></span>
                <img src="<?= e(product_img($atta, 'primary', 'sm')) ?>" alt="CookSmart Chakki Fresh Atta pack" class="about-visual__pack about-visual__pack--back" width="220" height="330" loading="lazy" data-parallax data-speed="-0.08">
                <img src="<?= e(product_img($mirchi, 'primary', 'sm')) ?>" alt="Sugrihini Mirchi Powder pack" class="about-visual__pack about-visual__pack--left" width="170" height="255" loading="lazy" data-parallax data-speed="0.1">
                <img src="<?= e(product_img($haldi, 'primary', 'sm')) ?>" alt="Sugrihini Haldi Powder pack" class="about-visual__pack about-visual__pack--right" width="170" height="255" loading="lazy" data-parallax data-speed="0.16">
            </div>
            <div class="since-badge" aria-label="Since <?= FOUNDED_YEAR ?>">
                <svg viewBox="0 0 160 160" aria-hidden="true">
                    <defs><path id="since-circle" d="M80 80 m -60 0 a 60 60 0 1 1 120 0 a 60 60 0 1 1 -120 0"/></defs>
                    <text><textPath href="#since-circle" startOffset="0">SINCE <?= FOUNDED_YEAR ?> • COOKSMART PREMIUM FOOD • </textPath></text>
                </svg>
                <span class="since-badge__year"><?= FOUNDED_YEAR ?></span>
            </div>
        </div>
        <div class="about-teaser__text">
            <?php partial('section-heading', ['eyebrow' => 'About Us', 'title' => 'Welcome to <em>CookSmart</em>', 'align' => 'left', 'id' => 'about-teaser-title']); ?>
            <p class="lead-text" data-anim="fade-up"><?= e($c['intro']) ?></p>
            <p data-anim="fade-up" data-delay="0.1"><?= e($c['philosophy'][2]) ?></p>
            <?php partial('counters'); ?>
            <a class="btn btn--primary magnetic about-teaser__btn" href="<?= e(url('about-us/')) ?>" data-anim="fade-up"><span>Read Our Story</span><?= icon('arrow-right') ?></a>
        </div>
    </div>
</section>

<!-- ================= HERITAGE BAND ================= -->
<section class="heritage" aria-labelledby="heritage-title">
    <div class="heritage__pattern" aria-hidden="true"></div>
    <div class="container heritage__grid">
        <div class="heritage__art" aria-hidden="true">
            <?php include __DIR__ . '/includes/partials/shil-nora.php'; ?>
        </div>
        <div class="heritage__text">
            <p class="eyebrow eyebrow--light" data-anim="fade-up">Our Heritage</p>
            <h2 id="heritage-title" class="heritage__title" data-anim="split-up">Decades ago, spices were ground by hand at home.</h2>
            <p data-anim="fade-up" data-delay="0.1">Housewives made their own blends on the grinding stone. With changing times, that is giving way to ready-to-use spices in convenient packs — and the care behind them matters as much as ever.</p>
            <p class="heritage__bn" data-anim="fade-up" data-delay="0.2"><span lang="bn">সুগৃহিণী</span><span>Sugrihini means “the good homemaker” — the spirit behind every CookSmart spice pack.</span></p>
            <a class="btn btn--ghost-light magnetic" href="<?= e(url('company-policy/#heritage')) ?>" data-anim="fade-up" data-delay="0.3"><span>Our Heritage</span><?= icon('arrow-right') ?></a>
        </div>
    </div>
</section>

<!-- ================= PRODUCTS SHOWCASE ================= -->
<section class="section section--cream section--pattern products-showcase" aria-labelledby="products-title">
    <div class="container">
        <div class="products-showcase__head">
            <?php partial('section-heading', ['eyebrow' => 'Our Products', 'title' => 'Made for Every <em>Indian Kitchen</em>', 'align' => 'left', 'id' => 'products-title', 'lead' => 'Sugrihini spices and Chakki Fresh Atta — from our own production to your kitchen.']); ?>
            <div class="carousel-nav" data-anim="fade-up">
                <button type="button" class="carousel-nav__btn" data-carousel-prev="home-products" aria-label="Previous products"><?= icon('arrow-left') ?></button>
                <button type="button" class="carousel-nav__btn" data-carousel-next="home-products" aria-label="Next products"><?= icon('arrow-right') ?></button>
            </div>
        </div>
        <div class="carousel" id="home-products" data-carousel>
            <div class="carousel__track" data-anim-stagger="fade-up" data-stagger="0.1">
                <?php foreach (products() as $product): ?>
                <div class="carousel__slide"><?php partial('product-card', ['product' => $product]); ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="products-showcase__foot" data-anim="fade-up">
            <a class="btn btn--outline magnetic" href="<?= e(url('products/')) ?>"><span>View All Products</span><?= icon('arrow-right') ?></a>
        </div>
    </div>
</section>

<!-- ================= OBJECTIVE QUOTE ================= -->
<section class="objective" aria-label="Our objective">
    <div class="objective__pattern" aria-hidden="true"></div>
    <svg class="objective__swoosh" viewBox="0 0 1440 300" preserveAspectRatio="none" aria-hidden="true">
        <defs>
            <linearGradient id="obj-green" x1="0" x2="1"><stop offset="0" stop-color="#319847"/><stop offset=".55" stop-color="#64B741"/><stop offset="1" stop-color="#A8E13C"/></linearGradient>
        </defs>
        <path d="M0 300 C 300 120, 900 60, 1440 190 L 1440 230 C 920 110, 340 170, 0 300 Z" fill="url(#obj-green)" opacity=".9"/>
        <path d="M0 300 C 340 170, 920 110, 1440 230 L 1440 260 C 940 150, 380 210, 60 300 Z" fill="#7A0D11"/>
    </svg>
    <div class="container objective__inner">
        <span class="objective__icon" data-anim="zoom-in"><?= icon('quote') ?></span>
        <p class="objective__quote" data-anim="words-scrub">Quality is not expensive — it’s priceless.</p>
        <p class="objective__by" data-anim="fade-up">— Our one and only objective</p>
    </div>
</section>

<!-- ================= CATEGORIES & SEGMENTS ================= -->
<section class="section business" aria-labelledby="business-title">
    <div class="container">
        <?php partial('section-heading', ['eyebrow' => 'Our Business', 'title' => 'Core Categories &amp; <em>Segments</em>', 'id' => 'business-title', 'lead' => 'Our core business categories are spices, pulses and cereals — serving every segment from the neighbourhood store to the hotel kitchen.']); ?>
        <ul class="category-tiles" data-anim-stagger="fade-up" data-stagger="0.15">
            <?php foreach ($c['categories'] as $i => $cat): ?>
            <li class="category-tile category-tile--<?= $i + 1 ?>">
                <span class="category-tile__icon"><?= icon($cat['icon']) ?></span>
                <h3 class="category-tile__title"><?= e($cat['label']) ?></h3>
                <p><?= e($cat['text']) ?></p>
                <span class="category-tile__num" aria-hidden="true">0<?= $i + 1 ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="marquee" aria-label="Business segments">
        <?php for ($loop = 0; $loop < 2; $loop++): ?>
        <ul class="marquee__track"<?= $loop ? ' aria-hidden="true"' : '' ?>>
            <?php foreach ($c['segments'] as $seg): ?>
            <li><?= icon($seg['icon']) ?><span><?= e($seg['label']) ?></span></li>
            <?php endforeach; ?>
        </ul>
        <?php endfor; ?>
    </div>
</section>

<!-- ================= VISION & MISSION ================= -->
<section class="section section--cream vm" aria-labelledby="vm-title">
    <div class="container">
        <?php partial('section-heading', ['eyebrow' => 'What Drives Us', 'title' => 'Vision &amp; <em>Mission</em>', 'id' => 'vm-title']); ?>
        <div class="vm__grid">
            <article class="vm-card vm-card--vision" data-anim="slide-left">
                <span class="vm-card__icon"><?= icon('eye') ?></span>
                <h3>Our Vision</h3>
                <p><?= e($c['vision']) ?></p>
            </article>
            <article class="vm-card vm-card--mission" data-anim="slide-right">
                <span class="vm-card__icon"><?= icon('target') ?></span>
                <h3>Our Mission</h3>
                <p><?= e($c['mission']) ?></p>
            </article>
        </div>
        <p class="vm__more" data-anim="fade-up"><a class="btn btn--outline magnetic" href="<?= e(url('company-policy/')) ?>"><span>Read Company Policy</span><?= icon('arrow-right') ?></a></p>
    </div>
</section>

<?php partial('cta-band'); ?>

</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
