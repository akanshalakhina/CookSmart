<?php
/**
 * PREVIEW ONLY — page shell of the practice owner area for the static design preview.
 *
 * build-static.php fetches this through router.php (/__demo/admin/) and saves it as dist/admin/index.html,
 * where preview/preview-admin.js turns it into a working demo. The live PHP site never serves this file:
 * it is inside includes/ (blocked on Apache) and has no rewrite rule — the real owner area is admin.php.
 */
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$page = [
    'slug'        => 'admin',
    'path'        => 'admin/',
    'title'       => 'Owner Area (Demo)',
    'description' => 'Practice copy of the CookSmart owner area.',
    'noindex'     => true,
    'breadcrumbs' => [['label' => 'Home', 'path' => ''], ['label' => 'Owner Area', 'path' => 'admin/']],
];
enqueue_script('js/preview-icons.js');
enqueue_script('js/preview-admin.js');

require __DIR__ . '/header.php';
?>
<main id="main">
<?php partial('page-hero', [
    'eyebrow' => 'Owner area · demo',
    'title'   => 'Job Openings',
    'lead'    => 'A practice copy: sign in with the demo login, post a job, then look for it on the Career page.',
    'crumbs'  => $page['breadcrumbs'],
]); ?>

<section class="section admin" aria-label="Manage job openings (demo)">
    <div class="container admin__wrap">
        <div id="demo-admin"></div>
        <noscript><p class="form-alert">This demo needs JavaScript. Please turn it on and reload the page.</p></noscript>
    </div>
</section>
</main>
<style>
.demo-tools { margin: -6px 0 22px; }
</style>
<?php require __DIR__ . '/footer.php'; ?>
