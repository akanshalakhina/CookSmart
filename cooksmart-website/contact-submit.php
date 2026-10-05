<?php
/**
 * Enquiry form handler.
 * Responds with JSON to fetch() requests, otherwise redirects back to /contact-us/.
 * On localhost (DEV_MODE) enquiries are appended to storage/enquiries.log instead of emailed.
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

session_start();

$wantsJson = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');

/** @return never */
function respond(bool $ok, string $message, array $errors = [], int $status = 200, array $old = []): void
{
    global $wantsJson;
    if ($wantsJson) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => $ok, 'message' => $message, 'errors' => (object) $errors], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $_SESSION['contact_flash'] = ['sent' => $ok, 'message' => $message, 'errors' => $errors, 'old' => $ok ? [] : $old];
    header('Location: ' . url('contact-us/') . '#enquiry', true, 303);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: ' . url('contact-us/'), true, 303);
    exit;
}

$clean = static fn (string $key, int $max): string =>
    text_cut(trim(str_replace(["\r", "\0"], '', (string) ($_POST[$key] ?? ''))), $max);

$input = [
    'name'    => preg_replace('/\s+/', ' ', $clean('name', 100)),
    'phone'   => $clean('phone', 20),
    'email'   => $clean('email', 150),
    'type'    => $clean('type', 20),
    'product' => $clean('product', 40),
    'message' => $clean('message', 2000),
];

// Honeypot: bots fill the hidden field — pretend success, send nothing.
if (trim((string) ($_POST['website'] ?? '')) !== '') {
    respond(true, 'Thank you! Your enquiry has been sent.');
}

if (!hash_equals((string) ($_SESSION['csrf'] ?? ''), (string) ($_POST['csrf'] ?? ''))) {
    respond(false, 'Your session has expired. Please reload the page and try again.', [], 400, $input);
}

if (time() - (int) ($_SESSION['last_enquiry_at'] ?? 0) < 30) {
    respond(false, 'Please wait a few seconds before sending another enquiry.', [], 429, $input);
}

$errors = [];
if (text_len($input['name']) < 2) {
    $errors['name'] = 'Please enter your name.';
}
if (!preg_match('/^\+?[0-9][0-9\s\-()]{7,18}$/', $input['phone'])) {
    $errors['phone'] = 'Please enter a valid phone number.';
}
if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please enter a valid email address.';
}
if (text_len($input['message']) < 10) {
    $errors['message'] = 'Please tell us a little more (at least 10 characters).';
}
$types = enquiry_types();
if (!isset($types[$input['type']])) {
    $input['type'] = 'general';
}
$product = $input['product'] !== '' ? get_product($input['product']) : null;

if ($errors) {
    respond(false, 'Please check the highlighted fields.', $errors, 422, $input);
}

$subject = 'Website enquiry: ' . $types[$input['type']] . ' — ' . $input['name'];
$body = implode("\n", [
    'New enquiry from the CookSmart website',
    str_repeat('-', 40),
    'Name:         ' . $input['name'],
    'Phone:        ' . $input['phone'],
    'Email:        ' . $input['email'],
    'Enquiry type: ' . $types[$input['type']],
    'Product:      ' . ($product ? $product['name'] : 'Not specified'),
    '',
    'Message:',
    $input['message'],
    '',
    str_repeat('-', 40),
    'Sent: ' . date('d M Y, H:i') . ' · IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'),
]);

if (DEV_MODE) {
    $dir = __DIR__ . '/storage';
    if (!is_dir($dir)) {
        mkdir($dir, 0750, true);
    }
    $sent = file_put_contents($dir . '/enquiries.log', $subject . "\n" . $body . "\n\n", FILE_APPEND | LOCK_EX) !== false;
} else {
    $host    = preg_replace('/^www\./', '', (string) ($_SERVER['SERVER_NAME'] ?? 'localhost'));
    $headers = [
        'From'         => 'CookSmart Website <no-reply@' . $host . '>',
        'Reply-To'     => $input['email'],
        'MIME-Version' => '1.0',
        'Content-Type' => 'text/plain; charset=UTF-8',
        'X-Mailer'     => 'CookSmart',
    ];
    $sent = mail(CONTACT_TO_EMAIL, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers);
}

if (!$sent) {
    respond(false, 'Sorry, we could not send your enquiry right now. Please call us on ' . PHONE_DISPLAY . ' or email ' . CONTACT_EMAIL . '.', [], 500, $input);
}

$_SESSION['last_enquiry_at'] = time();
respond(true, 'Thank you, ' . $input['name'] . '! Your enquiry has been sent. We will get back to you soon.');
