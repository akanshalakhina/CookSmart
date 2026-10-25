<?php
/**
 * Career — job openings posted by the owner (see admin.php).
 * (WordPress migration: archive-cs_job.php)
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$jobs = jobs_open();

$schemas = [];
foreach ($jobs as $job) {
    $posting = [
        '@context'           => 'https://schema.org',
        '@type'              => 'JobPosting',
        'title'              => $job['title'],
        'description'        => $job['description'],
        'datePosted'         => $job['posted_at'],
        'employmentType'     => job_schema_type($job['type']),
        'hiringOrganization' => ['@type' => 'Organization', 'name' => COMPANY_LEGAL_NAME, 'sameAs' => absolute_url()],
        'jobLocation'        => ['@type' => 'Place', 'address' => ['@type' => 'PostalAddress', 'addressLocality' => $job['location'], 'addressCountry' => 'IN']],
    ];
    if ($job['apply_by'] !== '') {
        $posting['validThrough'] = $job['apply_by'];
    }
    $schemas[] = $posting;
}

$page = [
    'slug'        => 'career',
    'path'        => 'career/',
    'title'       => 'Career',
    'description' => 'Join CookSmart Premium Food. See our current job openings and apply — or send us your details and we will keep them on file.',
    'breadcrumbs' => [['label' => 'Home', 'path' => ''], ['label' => 'Career', 'path' => 'career/']],
    'schemas'     => $schemas,
];

$newSince = date('Y-m-d', strtotime('-14 days'));

require __DIR__ . '/includes/header.php';
?>
<main id="main">
<?php partial('page-hero', [
    'eyebrow' => 'Join the CookSmart family',
    'title'   => 'Career',
    'lead'    => 'Help us bring quality to Indian kitchens. Explore our current openings and apply today.',
    'crumbs'  => $page['breadcrumbs'],
]); ?>

<section class="section careers" aria-labelledby="openings-title">
    <div class="container">
        <?php partial('section-heading', [
            'eyebrow' => 'Current openings',
            'title'   => $jobs ? 'We are <em>hiring</em>' : 'Openings <em>coming soon</em>',
            'id'      => 'openings-title',
            'lead'    => $jobs
                ? 'Open the role that interests you to read the details, then apply with one click.'
                : 'There are no open positions right now, but we are always glad to hear from good people.',
        ]); ?>

        <?php if ($jobs): ?>
        <div class="job-list" data-anim-stagger="fade-up" data-stagger="0.1">
            <?php foreach ($jobs as $job):
                $applyUrl = url('contact-us/?type=career&job=' . rawurlencode($job['title']) . '#enquiry');
                $waText   = 'Hello CookSmart, I would like to apply for the position of ' . $job['title'] . '.';
            ?>
            <article class="job-card" id="job-<?= e($job['id']) ?>">
                <div class="job-card__head">
                    <div class="job-card__titles">
                        <?php if ($job['department'] !== ''): ?><p class="job-card__dept"><?= e($job['department']) ?></p><?php endif; ?>
                        <h3 class="job-card__title"><?= e($job['title']) ?>
                            <?php if ($job['posted_at'] >= $newSince): ?><span class="job-card__new">New</span><?php endif; ?>
                        </h3>
                    </div>
                    <div class="job-card__actions">
                        <a class="btn btn--primary magnetic" href="<?= e($applyUrl) ?>"><span>Apply Now</span><?= icon('arrow-right') ?></a>
                        <?php if (WHATSAPP_ENABLED): ?>
                        <a class="btn btn--outline" href="https://wa.me/<?= e(ltrim(PHONE_E164, '+')) ?>?text=<?= e(rawurlencode($waText)) ?>" target="_blank" rel="noopener" data-no-transition><?= icon('whatsapp') ?><span>WhatsApp</span></a>
                        <?php endif; ?>
                    </div>
                </div>

                <ul class="job-card__meta">
                    <li><?= icon('map-pin') ?><?= e($job['location']) ?></li>
                    <li><?= icon('briefcase') ?><?= e(job_types()[$job['type']]) ?></li>
                    <?php if ($job['experience'] !== ''): ?><li><?= icon('clock') ?><?= e($job['experience']) ?></li><?php endif; ?>
                    <?php if ($job['salary'] !== ''): ?><li><?= icon('rupee') ?><?= e($job['salary']) ?></li><?php endif; ?>
                    <?php if ($job['apply_by'] !== ''): ?><li><?= icon('calendar') ?>Apply by <?= e(date('d M Y', strtotime($job['apply_by']))) ?></li><?php endif; ?>
                </ul>

                <details class="job-card__more">
                    <summary><span class="job-card__more-open">View role details</span><span class="job-card__more-close">Hide details</span><?= icon('arrow-right') ?></summary>
                    <div class="job-card__body">
                        <?= paragraphs($job['description']) ?>
                        <?php if ($job['requirements']): ?>
                        <h4>Requirements</h4>
                        <ul class="job-card__list">
                            <?php foreach ($job['requirements'] as $req): ?><li><?= icon('check') ?><?= e($req) ?></li><?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                        <p class="job-card__posted">Posted on <?= e(date('d M Y', strtotime($job['posted_at']))) ?></p>
                    </div>
                </details>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="career-empty" data-anim="fade-up">
            <span class="career-empty__icon"><?= icon('briefcase') ?></span>
            <h3>No openings at the moment</h3>
            <p>Please check back soon, or send us your details and we will contact you when a suitable role opens.</p>
            <div class="career-empty__actions">
                <a class="btn btn--primary magnetic" href="<?= e(url('contact-us/?type=career#enquiry')) ?>"><span>Send your details</span><?= icon('arrow-right') ?></a>
                <?php if (WHATSAPP_ENABLED): ?>
                <a class="btn btn--outline" href="https://wa.me/<?= e(ltrim(PHONE_E164, '+')) ?>" target="_blank" rel="noopener" data-no-transition><?= icon('whatsapp') ?><span>WhatsApp <?= e(PHONE_DISPLAY) ?></span></a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php partial('cta-band', ['title' => 'Don’t see the right role?', 'text' => 'Tell us about yourself on the contact form or call ' . PHONE_DISPLAY . ' — we will keep your details in mind for future openings.']); ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
