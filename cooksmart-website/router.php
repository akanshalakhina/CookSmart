<?php
/**
 * DEVELOPMENT ONLY — emulates the .htaccess clean URLs for PHP's built-in server:
 *   php -S localhost:8000 router.php
 * Not used on Apache hosting.
 */
declare(strict_types=1);

$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');

// Never serve private folders.
if (preg_match('#^/(includes|data|storage)(/|$)#', $path)) {
    http_response_code(403);
    exit('Forbidden');
}

// Static files are served as-is.
if ($path !== '/' && is_file(__DIR__ . $path) && !str_ends_with($path, '.php')) {
    return false;
}

$routes = [
    '#^/$#'                              => 'index.php',
    '#^/about-us/?$#'                    => 'about-us.php',
    '#^/products/?$#'                    => 'products.php',
    '#^/products/([a-z0-9-]+)/?$#'       => 'product.php',
    '#^/company-policy/?$#'              => 'company-policy.php',
    '#^/contact-us/?$#'                  => 'contact-us.php',
    '#^/contact-submit\.php$#'           => 'contact-submit.php',
    '#^/sitemap\.xml$#'                  => 'sitemap.php',
];

foreach ($routes as $pattern => $file) {
    if (preg_match($pattern, $path, $m)) {
        if (isset($m[1])) {
            $_GET['slug'] = $m[1];
        }
        $_SERVER['SCRIPT_NAME'] = '/' . $file;
        require __DIR__ . '/' . $file;
        return true;
    }
}

require __DIR__ . '/404.php';
return true;
