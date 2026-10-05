<?php
/**
 * Company Policy — the full company profile.
 * (WordPress migration: page-company-policy.php)
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page = [
    'slug'        => 'company-policy',
    'path'        => 'company-policy/',
    'title'       => 'Company Policy',
    'description' => 'CookSmart’s objective, vision, mission, values, heritage, industry outlook, business categories, segments and commitments.',
    'breadcrumbs' => [['label' => 'Home', 'path' => ''], ['label' => 'Company Policy', 'path' => 'company-policy/']],
];
enqueue_script('js/policy.js');
$c = company();

$toc = [
    'objective'   => 'Our Objective',
    'who-we-are'  => 'Who We Are',
    'vision'      => 'Vision',
    'mission'     => 'Mission',
    'values'      => 'Values',
    'heritage'    => 'Our Heritage',
    'industry'    => 'Industry Outlook',
    'business'    => 'Our Business',
    'commitments' => 'Our Commitments',
];

require __DIR__ . '/includes/header.php';
?>
<main id="main">
<?php partial('page-hero', [
    'eyebrow' => 'How We Work',
    'title'   => 'Company Policy',
    'lead'    => 'The beliefs, objectives and commitments that guide Cooksmart Premium Food Private Limited.',
    'crumbs'  => $page['breadcrumbs'],
]); ?>

<div class="section policy">
    <div class="container policy__layout">
        <nav class="policy-toc" aria-label="On this page" data-toc>
            <p class="policy-toc__title">On this page</p>
            <ol>
                <?php foreach ($toc as $id => $label): ?>
                <li><a href="#<?= e($id) ?>" data-toc-link="<?= e($id) ?>"><?= e($label) ?></a></li>
                <?php endforeach; ?>
            </ol>
            <span class="policy-toc__progress" aria-hidden="true"><span data-toc-progress></span></span>
        </nav>

        <div class="policy__content">

            <section id="objective" class="policy-block policy-objective" aria-labelledby="objective-title" data-toc-section>
                <h2 id="objective-title" class="visually-hidden">Our Objective</h2>
                <span class="policy-objective__icon" data-anim="zoom-in"><?= icon('quote') ?></span>
                <p class="policy-objective__quote" data-anim="words-scrub"><?= e($c['objective']) ?></p>
                <p class="policy-objective__by" data-anim="fade-up">— Our one and only objective</p>
            </section>

            <section id="who-we-are" class="policy-block" aria-labelledby="who-title" data-toc-section>
                <p class="eyebrow" data-anim="fade-up">Who We Are</p>
                <h2 id="who-title" class="policy-block__title" data-anim="split-up">CookSmart Premium Food</h2>
                <?= swoosh_line() ?>
                <p class="lead-text" data-anim="fade-up"><?= e($c['intro']) ?></p>
                <?php foreach ($c['philosophy'] as $i => $para): ?>
                <p data-anim="fade-up" data-delay="<?= 0.05 * $i ?>"><?= e($para) ?></p>
                <?php endforeach; ?>
            </section>

            <div class="policy-pillars" data-anim-stagger="fade-up" data-stagger="0.14">
                <section id="vision" class="arch-card" aria-labelledby="vision-title" data-toc-section>
                    <span class="arch-card__icon"><?= icon('eye') ?></span>
                    <h2 id="vision-title">Vision</h2>
                    <p><?= e($c['vision']) ?></p>
                </section>
                <section id="mission" class="arch-card arch-card--featured" aria-labelledby="mission-title" data-toc-section>
                    <span class="arch-card__icon"><?= icon('target') ?></span>
                    <h2 id="mission-title">Mission</h2>
                    <p><?= e($c['mission']) ?></p>
                </section>
                <section id="values" class="arch-card" aria-labelledby="values-title" data-toc-section>
                    <span class="arch-card__icon"><?= icon('heart') ?></span>
                    <h2 id="values-title">Values</h2>
                    <p><?= e($c['values']) ?></p>
                </section>
            </div>
            <ul class="value-chips" aria-label="What we value" data-anim-stagger="zoom-in" data-stagger="0.07">
                <?php foreach ($c['value_words'] as $v): ?>
                <li><?= icon($v['icon']) ?><?= e($v['label']) ?></li>
                <?php endforeach; ?>
            </ul>

            <section id="heritage" class="policy-block policy-heritage" aria-labelledby="heritage-title" data-toc-section>
                <div class="policy-heritage__art" aria-hidden="true">
                    <?php include __DIR__ . '/includes/partials/shil-nora.php'; ?>
                </div>
                <div>
                    <p class="eyebrow eyebrow--light" data-anim="fade-up">Our Heritage</p>
                    <p class="policy-heritage__num" data-anim="fade-up"><span data-counter="7000" data-format="comma">7,000</span><small> years</small></p>
                    <h2 id="heritage-title" class="policy-block__title" data-anim="split-up">As old as civilisation itself</h2>
                    <?php foreach ($c['heritage'] as $para): ?>
                    <p data-anim="fade-up"><?= e($para) ?></p>
                    <?php endforeach; ?>
                </div>
            </section>

            <section id="industry" class="policy-block" aria-labelledby="industry-title" data-toc-section>
                <p class="eyebrow" data-anim="fade-up">Industry Outlook</p>
                <h2 id="industry-title" class="policy-block__title" data-anim="split-up">Why spices matter more than ever</h2>
                <?= swoosh_line() ?>
                <div class="policy-columns">
                    <?php foreach ($c['industry'] as $para): ?>
                    <p data-anim="fade-up"><?= $para /* trusted copy from data/company.php, contains <mark> */ ?></p>
                    <?php endforeach; ?>
                </div>
            </section>

            <section id="business" class="policy-block" aria-labelledby="business-title" data-toc-section>
                <p class="eyebrow" data-anim="fade-up">Our Business</p>
                <h2 id="business-title" class="policy-block__title" data-anim="split-up">Categories &amp; segments</h2>
                <?= swoosh_line() ?>
                <h3 class="policy-sub" data-anim="fade-up">Core business categories</h3>
                <ul class="category-tiles category-tiles--compact" data-anim-stagger="fade-up" data-stagger="0.12">
                    <?php foreach ($c['categories'] as $i => $cat): ?>
                    <li class="category-tile category-tile--<?= $i + 1 ?>">
                        <span class="category-tile__icon"><?= icon($cat['icon']) ?></span>
                        <h4 class="category-tile__title"><?= e($cat['label']) ?></h4>
                        <span class="category-tile__num" aria-hidden="true">0<?= $i + 1 ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <h3 class="policy-sub" data-anim="fade-up">Core business segments</h3>
                <ul class="segment-grid" data-anim-stagger="fade-up" data-stagger="0.06">
                    <?php foreach ($c['segments'] as $seg): ?>
                    <li class="segment"><span class="segment__icon"><?= icon($seg['icon']) ?></span><?= e($seg['label']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <section id="commitments" class="policy-block" aria-labelledby="commitments-title" data-toc-section>
                <p class="eyebrow" data-anim="fade-up">Our Commitments</p>
                <h2 id="commitments-title" class="policy-block__title" data-anim="split-up">Built to sustain growth</h2>
                <?= swoosh_line() ?>
                <div class="steps" data-steps>
                    <svg class="steps__line" viewBox="0 0 4 100" preserveAspectRatio="none" aria-hidden="true"><path d="M2 0 V100"/></svg>
                    <ol>
                        <?php foreach ($c['commitments'] as $i => $item): ?>
                        <li class="step" data-anim="slide-left" data-delay="<?= 0.08 * $i ?>">
                            <span class="step__num">0<?= $i + 1 ?></span>
                            <p><?= e($item) ?></p>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </section>

            <section class="policy-closing" aria-label="Closing statement">
                <svg class="policy-closing__swoosh" viewBox="0 0 800 120" preserveAspectRatio="none" aria-hidden="true">
                    <path d="M0 120 C 200 30, 560 10, 800 70 L 800 92 C 560 40, 220 64, 30 120 Z" fill="#64B741"/>
                    <path d="M30 120 C 220 64, 560 40, 800 92 L 800 110 C 580 66, 250 90, 80 120 Z" fill="#7A0D11"/>
                </svg>
                <p data-anim="split-up"><?= e($c['closing']) ?></p>
            </section>
        </div>
    </div>
</div>

<?php partial('cta-band'); ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
