<?php
/**
 * CookSmart — job openings (Career page) and the owner's admin login.
 *
 * Openings are kept in storage/jobs.json and the admin password hash in storage/admin.json.
 * Both files live in storage/, which is blocked from the web (.htaccess + router.php),
 * so no database is needed on shared hosting.
 * (WordPress migration: custom post type `cs_job` with these fields.)
 */
declare(strict_types=1);

const JOBS_FILE        = __DIR__ . '/../storage/jobs.json';
const ADMIN_FILE       = __DIR__ . '/../storage/admin.json';
const ATTEMPTS_FILE    = __DIR__ . '/../storage/login-attempts.json';
const LOGIN_MAX_TRIES  = 5;      // wrong passwords allowed ...
const LOGIN_WINDOW_SEC = 900;    // ... within this many seconds, per visitor IP

/* ------------------------------------------------------------------
 * Job openings
 * ------------------------------------------------------------------ */

function job_types(): array
{
    return [
        'full-time'  => 'Full-time',
        'part-time'  => 'Part-time',
        'contract'   => 'Contract',
        'internship' => 'Internship',
    ];
}

/** schema.org employmentType for each job type. */
function job_schema_type(string $type): string
{
    return ['full-time' => 'FULL_TIME', 'part-time' => 'PART_TIME', 'contract' => 'CONTRACTOR', 'internship' => 'INTERN'][$type] ?? 'OTHER';
}

/** All openings, newest first. */
function jobs_all(): array
{
    $raw = is_file(JOBS_FILE) ? file_get_contents(JOBS_FILE) : false;
    $jobs = $raw !== false ? json_decode($raw, true) : null;
    if (!is_array($jobs)) {
        return [];
    }
    usort($jobs, static fn ($a, $b) => strcmp((string) ($b['posted_at'] ?? ''), (string) ($a['posted_at'] ?? '')));
    return $jobs;
}

/** Openings shown on the public Career page: status open and not past the apply-by date. */
function jobs_open(): array
{
    $today = date('Y-m-d');
    return array_values(array_filter(jobs_all(), static fn ($j) =>
        ($j['status'] ?? 'open') === 'open' && (($j['apply_by'] ?? '') === '' || $j['apply_by'] >= $today)));
}

function job_get(string $id): ?array
{
    foreach (jobs_all() as $job) {
        if (($job['id'] ?? '') === $id) {
            return $job;
        }
    }
    return null;
}

/** Atomically rewrite the jobs file. $change receives the current list and returns the new one. */
function jobs_update(callable $change): bool
{
    $dir = dirname(JOBS_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0750, true);
    }
    $fh = fopen(JOBS_FILE, 'c+');
    if ($fh === false || !flock($fh, LOCK_EX)) {
        return false;
    }
    $current = json_decode((string) stream_get_contents($fh), true);
    $new = $change(is_array($current) ? $current : []);
    ftruncate($fh, 0);
    rewind($fh);
    $ok = fwrite($fh, json_encode(array_values($new), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) !== false;
    fflush($fh);
    flock($fh, LOCK_UN);
    fclose($fh);
    return $ok;
}

/** Clean one opening from submitted form data. Returns [job, errors]. */
function job_from_input(array $in, ?array $existing = null): array
{
    $line = static fn (string $k, int $max): string =>
        text_cut(trim(preg_replace('/\s+/', ' ', str_replace("\0", '', (string) ($in[$k] ?? '')))), $max);
    $block = static fn (string $k, int $max): string =>
        text_cut(trim(str_replace(["\r\n", "\r", "\0"], ["\n", "\n", ''], (string) ($in[$k] ?? ''))), $max);

    $requirements = array_values(array_filter(array_map(
        static fn ($l) => text_cut(trim(ltrim(trim($l), "-•*· \t")), 200),
        explode("\n", $block('requirements', 3000))
    ), static fn ($l) => $l !== ''));

    $type = (string) ($in['type'] ?? 'full-time');
    $job = [
        'id'           => $existing['id'] ?? bin2hex(random_bytes(4)),
        'title'        => $line('title', 100),
        'department'   => $line('department', 80),
        'location'     => $line('location', 100),
        'type'         => isset(job_types()[$type]) ? $type : 'full-time',
        'experience'   => $line('experience', 60),
        'salary'       => $line('salary', 60),
        'description'  => $block('description', 4000),
        'requirements' => array_slice($requirements, 0, 20),
        'apply_by'     => '',
        'status'       => (($in['status'] ?? 'open') === 'closed') ? 'closed' : 'open',
        'posted_at'    => $existing['posted_at'] ?? date('Y-m-d'),
    ];

    $errors = [];
    if (text_len($job['title']) < 3) {
        $errors['title'] = 'Please enter the job title.';
    }
    if ($job['location'] === '') {
        $errors['location'] = 'Please enter the job location.';
    }
    if (text_len($job['description']) < 20) {
        $errors['description'] = 'Please describe the role (at least 20 characters).';
    }
    $applyBy = trim((string) ($in['apply_by'] ?? ''));
    if ($applyBy !== '') {
        $d = DateTime::createFromFormat('Y-m-d', $applyBy);
        if ($d && $d->format('Y-m-d') === $applyBy) {
            $job['apply_by'] = $applyBy;
        } else {
            $errors['apply_by'] = 'Please choose a valid date.';
        }
    }
    return [$job, $errors];
}

/** Paragraphs of a multi-line text as escaped <p> tags. */
function paragraphs(string $text): string
{
    $out = '';
    foreach (preg_split('/\n{1,}/', trim($text)) ?: [] as $p) {
        if (trim($p) !== '') {
            $out .= '<p>' . e(trim($p)) . '</p>';
        }
    }
    return $out;
}

/* ------------------------------------------------------------------
 * Admin login (single owner account)
 * ------------------------------------------------------------------ */

function admin_hash(): string
{
    if (defined('ADMIN_PASSWORD_HASH') && ADMIN_PASSWORD_HASH !== '') {
        return ADMIN_PASSWORD_HASH;
    }
    $data = is_file(ADMIN_FILE) ? json_decode((string) file_get_contents(ADMIN_FILE), true) : null;
    return is_array($data) ? (string) ($data['hash'] ?? '') : '';
}

function admin_is_configured(): bool
{
    return admin_hash() !== '';
}

function admin_set_password(string $password): bool
{
    $dir = dirname(ADMIN_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0750, true);
    }
    return file_put_contents(ADMIN_FILE, json_encode(['hash' => password_hash($password, PASSWORD_DEFAULT), 'updated' => date('c')]), LOCK_EX) !== false;
}

function admin_logged_in(): bool
{
    return !empty($_SESSION['cs_admin']) && hash_equals(admin_hash(), (string) $_SESSION['cs_admin']);
}

/** Visitors are keyed by a hash of their IP so no address is stored. */
function login_attempt_key(): string
{
    return hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . '|cooksmart');
}

function login_attempts(): array
{
    $all = is_file(ATTEMPTS_FILE) ? json_decode((string) file_get_contents(ATTEMPTS_FILE), true) : [];
    $all = is_array($all) ? $all : [];
    $cut = time() - LOGIN_WINDOW_SEC;
    foreach ($all as $k => $times) {
        $all[$k] = array_values(array_filter((array) $times, static fn ($t) => $t > $cut));
        if (!$all[$k]) {
            unset($all[$k]);
        }
    }
    return $all;
}

function login_locked(): bool
{
    return count(login_attempts()[login_attempt_key()] ?? []) >= LOGIN_MAX_TRIES;
}

function login_record_failure(): void
{
    $all = login_attempts();
    $all[login_attempt_key()][] = time();
    file_put_contents(ATTEMPTS_FILE, json_encode($all), LOCK_EX);
}

function login_clear_failures(): void
{
    $all = login_attempts();
    unset($all[login_attempt_key()]);
    file_put_contents(ATTEMPTS_FILE, json_encode($all), LOCK_EX);
}
