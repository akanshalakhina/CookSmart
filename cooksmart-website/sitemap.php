<?php
/** XML sitemap — served at /sitemap.xml. */
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/xml; charset=utf-8');

$paths = ['', 'about-us/', 'products/', 'company-policy/', 'contact-us/'];
foreach (products() as $p) {
    $paths[] = 'products/' . $p['slug'] . '/';
}

echo '<?xml version="1.0" encoding="UTF-8"?>', "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', "\n";
foreach ($paths as $path) {
    echo '  <url><loc>', e(absolute_url($path)), '</loc></url>', "\n";
}
echo '</urlset>', "\n";
