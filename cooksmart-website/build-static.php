<?php
/**
 * Builds a static HTML copy of the site into dist/ for a design preview on Vercel
 * (Vercel cannot run PHP). The PHP site itself is not changed.
 *
 *   php build-static.php                                   (run from this folder)
 *   php build-static.php https://your-project.vercel.app   (optional: real URL for share images)
 *
 * In dist/ the enquiry form shows a "design preview" notice instead of sending,
 * and every page is marked noindex so search engines ignore the preview.
 * Not used on PHP hosting — never upload dist/ or this file there.
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$site    = __DIR__;
$distDir = $site . DIRECTORY_SEPARATOR . 'dist';
$host    = '127.0.0.1:8090';
$origin  = 'http://' . $host;
$siteUrl = rtrim($argv[1] ?? '', '/');

/* ---------- Pages ---------- */
$products = require $site . '/data/products.php';
$pages = ['/', '/about-us/', '/products/', '/company-policy/', '/contact-us/'];
foreach (array_keys($products) as $slug) {
    $pages[] = '/products/' . $slug . '/';
}

/* ---------- Fresh dist/ ---------- */
if (is_dir($distDir)) {
    removeDir($distDir);
}
mkdir($distDir, 0777, true);

/* ---------- Private PHP server just for the build (no need to start one yourself) ---------- */
$server = proc_open(
    [PHP_BINARY, '-S', $host, '-t', $site, $site . DIRECTORY_SEPARATOR . 'router.php'],
    [0 => ['pipe', 'r'], 1 => ['file', PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null', 'w'], 2 => ['file', PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null', 'w']],
    $pipes,
    $site
);
if (!is_resource($server)) {
    fwrite(STDERR, "Could not start the PHP server.\n");
    exit(1);
}
for ($i = 0; $i < 50 && !@fsockopen('127.0.0.1', 8090); $i++) {
    usleep(100000);
}

echo "Exporting HTML pages...\n";
try {
    foreach ($pages as $path) {
        $html = fetch($origin . $path, $status);
        if ($status !== 200) {
            throw new RuntimeException("$path returned HTTP $status");
        }
        save($distDir . $path . 'index.html', finalize($html));
        echo "  $path\n";
    }
    save($distDir . '/404.html', finalize(fetch($origin . '/this-page-does-not-exist/', $status)));
    echo "  /404.html\n";
} finally {
    proc_terminate($server);
    proc_close($server);
}

echo "Copying assets...\n";
copyDir($site . '/assets', $distDir . '/assets');
save($distDir . '/robots.txt', "# Design preview — not the live CookSmart website.\nUser-agent: *\nAllow: /\n");
save($distDir . '/vercel.json', json_encode([
    'cleanUrls'     => true,
    'trailingSlash' => true,
    'headers'       => [['source' => '/(.*)', 'headers' => [['key' => 'X-Robots-Tag', 'value' => 'noindex, nofollow']]]],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

echo "\nDone! " . count($pages) . " pages + 404 in cooksmart-website/dist\n";

/* ================= helpers ================= */

function fetch(string $url, ?int &$status): string
{
    $body = file_get_contents($url, false, stream_context_create(['http' => ['ignore_errors' => true, 'timeout' => 30]]));
    $status = 0;
    foreach ($http_response_header ?? [] as $h) {
        if (preg_match('#^HTTP/\S+\s+(\d{3})#', $h, $m)) {
            $status = (int) $m[1];
        }
    }
    if ($body === false) {
        throw new RuntimeException("Could not load $url");
    }
    return $body;
}

/** Preview-only adjustments to an exported page. */
function finalize(string $html): string
{
    global $origin, $siteUrl;
    // Absolute links (canonical, share image, structured data): preview URL, or site-relative.
    $html = str_replace($origin, $siteUrl, $html);
    // Keep the preview out of search results.
    $html = preg_replace('#<meta charset="utf-8">#', "$0\n<meta name=\"robots\" content=\"noindex, nofollow\">", $html, 1);
    // Enquiry form: show the "design preview" notice instead of posting (see assets/js/contact.js).
    return str_replace('data-enquiry-form', 'data-enquiry-form data-preview', $html);
}

function save(string $file, string $contents): void
{
    if (!is_dir(dirname($file))) {
        mkdir(dirname($file), 0777, true);
    }
    file_put_contents($file, $contents);
}

function copyDir(string $from, string $to): void
{
    $items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($from, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
    foreach ($items as $item) {
        $target = $to . DIRECTORY_SEPARATOR . substr($item->getPathname(), strlen($from) + 1);
        $item->isDir() ? (is_dir($target) || mkdir($target, 0777, true)) : copy($item->getPathname(), $target);
    }
}

function removeDir(string $dir): void
{
    $items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($items as $item) {
        $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
    }
    rmdir($dir);
}
