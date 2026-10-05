<?php
declare(strict_types=1);

$baseUrl = 'http://localhost:8000';
$distDir = __DIR__ . '/dist';

if (!is_dir($distDir)) {
    mkdir($distDir, 0777, true);
}

// Copy assets
function copyDir(string $src, string $dst): void {
    $dir = opendir($src);
    @mkdir($dst, 0777, true);
    while (false !== ($file = readdir($dir))) {
        if ($file !== '.' && $file !== '..') {
            if (is_dir($src . '/' . $file)) {
                copyDir($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

echo "Copying assets...\n";
copyDir(__DIR__ . '/assets', $distDir . '/assets');
if (file_exists(__DIR__ . '/robots.txt')) {
    copy(__DIR__ . '/robots.txt', $distDir . '/robots.txt');
}

$products = require __DIR__ . '/data/products.php';

$routes = [
    '/' => 'index.html',
    '/about-us' => 'about-us/index.html',
    '/products' => 'products/index.html',
    '/company-policy' => 'company-policy/index.html',
    '/contact-us' => 'contact-us/index.html',
    '/404' => '404.html',
];

foreach (array_keys($products) as $slug) {
    $routes["/products/{$slug}"] = "products/{$slug}/index.html";
}

$ctx = stream_context_create(['http' => ['ignore_errors' => true]]);

echo "Exporting HTML pages...\n";
foreach ($routes as $route => $destFile) {
    $url = $baseUrl . ($route === '/404' ? '/non-existent-page-404' : $route);
    $html = @file_get_contents($url, false, $ctx);
    if ($html === false) {
        echo "Failed to fetch: {$url}\n";
        continue;
    }
    $targetPath = $distDir . '/' . $destFile;
    @mkdir(dirname($targetPath), 0777, true);
    file_put_contents($targetPath, $html);
    echo "  Exported: {$route} -> {$destFile}\n";
}

echo "\nDone! All files ready in cooksmart-website/dist\n";
