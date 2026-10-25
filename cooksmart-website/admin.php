<?php
/**
 * Owner area — post, edit, close and delete job openings shown on the Career page.
 *
 * First visit: create the admin password. After that: sign in with it.
 * Job openings are stored in storage/jobs.json (see includes/careers.php).
 * (WordPress migration: not needed — use the WordPress dashboard for the `cs_job` post type.)
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? '') === '443');
session_set_cookie_params(['lifetime' => 0, 'path' => base_path() . '/', 'secure' => $secure, 'httponly' => true, 'samesite' => 'Lax']);
session_start();
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');

function flash(string $message, string $type = 'ok'): void
{
    $_SESSION['admin_flash'] = ['message' => $message, 'type' => $type];
}

/** @return never */
function back_to(string $query = ''): void
{
    header('Location: ' . url('admin/') . $query, true, 303);
    exit;
}

/* ------------------------------------------------------------------
 * Actions (POST)
 * ------------------------------------------------------------------ */
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!hash_equals(csrf_token(), (string) ($_POST['csrf'] ?? ''))) {
        flash('Your session expired. Please try again.', 'error');
        back_to();
    }
    $action = (string) ($_POST['action'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if ($action === 'setup' && !admin_is_configured()) {
        if (strlen($password) < 8) {
            flash('Please choose a password of at least 8 characters.', 'error');
        } elseif ($password !== (string) ($_POST['password2'] ?? '')) {
            flash('The two passwords do not match.', 'error');
        } elseif (!admin_set_password($password)) {
            flash('Could not save the password. Please check that the storage/ folder is writable.', 'error');
        } else {
            session_regenerate_id(true);
            $_SESSION['cs_admin'] = admin_hash();
            flash('Password created — you are signed in. You can now post job openings.');
        }
        back_to();
    }

    if ($action === 'login') {
        if (login_locked()) {
            flash('Too many wrong attempts. Please wait 15 minutes and try again.', 'error');
        } elseif (admin_is_configured() && password_verify($password, admin_hash())) {
            session_regenerate_id(true);
            $_SESSION['cs_admin'] = admin_hash();
            login_clear_failures();
        } else {
            login_record_failure();
            flash('That password is not correct.', 'error');
        }
        back_to();
    }

    if (!admin_logged_in()) {
        back_to();
    }

    switch ($action) {
        case 'logout':
            unset($_SESSION['cs_admin']);
            session_regenerate_id(true);
            flash('You have been signed out.');
            back_to();

        case 'save':
            $id       = (string) ($_POST['id'] ?? '');
            $existing = $id !== '' ? job_get($id) : null;
            if ($id !== '' && $existing === null) {
                flash('That job no longer exists.', 'error');
                back_to();
            }
            [$job, $errors] = job_from_input($_POST, $existing);
            if ($errors) {
                $_SESSION['admin_form'] = ['values' => $_POST, 'errors' => $errors];
                flash('Please fix the highlighted fields.', 'error');
                back_to($existing ? '?edit=' . rawurlencode($id) : '?new=1');
            }
            $saved = jobs_update(static function (array $all) use ($job, $existing): array {
                if ($existing === null) {
                    $all[] = $job;
                    return $all;
                }
                return array_map(static fn ($j) => ($j['id'] ?? '') === $job['id'] ? $job : $j, $all);
            });
            flash($saved ? ($existing ? 'Job updated.' : 'Job posted — it is now on the Career page.') : 'Could not save. Please check that the storage/ folder is writable.', $saved ? 'ok' : 'error');
            back_to();

        case 'toggle':
            $id = (string) ($_POST['id'] ?? '');
            jobs_update(static fn (array $all): array => array_map(static function ($j) use ($id) {
                if (($j['id'] ?? '') === $id) {
                    $j['status'] = ($j['status'] ?? 'open') === 'open' ? 'closed' : 'open';
                }
                return $j;
            }, $all));
            flash('Job status changed.');
            back_to();

        case 'delete':
            $id = (string) ($_POST['id'] ?? '');
            jobs_update(static fn (array $all): array => array_filter($all, static fn ($j) => ($j['id'] ?? '') !== $id));
            flash('Job deleted.');
            back_to();

        case 'password':
            if (!password_verify((string) ($_POST['current'] ?? ''), admin_hash())) {
                flash('Your current password is not correct.', 'error');
            } elseif (strlen($password) < 8) {
                flash('The new password must be at least 8 characters.', 'error');
            } elseif ($password !== (string) ($_POST['password2'] ?? '')) {
                flash('The two new passwords do not match.', 'error');
            } elseif (defined('ADMIN_PASSWORD_HASH') && ADMIN_PASSWORD_HASH !== '') {
                flash('The password is set in includes/config.php — change it there.', 'error');
            } elseif (!admin_set_password($password)) {
                flash('Could not save the new password.', 'error');
            } else {
                $_SESSION['cs_admin'] = admin_hash();
                flash('Password changed.');
            }
            back_to();
    }
    back_to();
}

/* ------------------------------------------------------------------
 * Page (GET)
 * ------------------------------------------------------------------ */
$flash = $_SESSION['admin_flash'] ?? null;
unset($_SESSION['admin_flash']);
$formState = $_SESSION['admin_form'] ?? null;
unset($_SESSION['admin_form']);

$loggedIn = admin_logged_in();
$view = !admin_is_configured() ? 'setup' : (!$loggedIn ? 'login' : 'list');
$editing = null;
if ($loggedIn && isset($_GET['new'])) {
    $view = 'form';
} elseif ($loggedIn && isset($_GET['edit'])) {
    $editing = job_get((string) $_GET['edit']);
    $view = $editing ? 'form' : 'list';
}

// values shown in the job form: failed submission > job being edited > blank
$values = $formState['values'] ?? ($editing
    ? array_merge($editing, ['requirements' => implode("\n", $editing['requirements'] ?? [])])
    : ['type' => 'full-time', 'status' => 'open']);
$errors = $formState['errors'] ?? [];
$val = static fn (string $k): string => (string) ($values[$k] ?? '');

$page = [
    'slug'        => 'admin',
    'path'        => 'admin/',
    'title'       => 'Owner Area',
    'description' => 'Manage CookSmart job openings.',
    'noindex'     => true,
    'breadcrumbs' => [['label' => 'Home', 'path' => ''], ['label' => 'Owner Area', 'path' => 'admin/']],
];
enqueue_script('js/admin.js');

$fieldClass = static fn (string $k, string $extra = ''): string => trim('field ' . $extra . (isset($errors[$k]) ? ' has-error' : ''));

require __DIR__ . '/includes/header.php';
?>
<main id="main">
<?php partial('page-hero', [
    'eyebrow' => 'Owner area',
    'title'   => 'Job Openings',
    'lead'    => $loggedIn ? 'Post, edit or close the openings shown on the Career page.' : 'Sign in to manage the openings shown on the Career page.',
    'crumbs'  => $page['breadcrumbs'],
]); ?>

<section class="section admin" aria-label="Manage job openings">
    <div class="container admin__wrap">

        <?php if ($flash): ?>
        <p class="form-alert<?= $flash['type'] === 'ok' ? ' form-alert--info' : '' ?>" role="<?= $flash['type'] === 'ok' ? 'status' : 'alert' ?>"><?= e($flash['message']) ?></p>
        <?php endif; ?>

        <?php if ($view === 'setup'): ?>
        <div class="admin-card admin-card--narrow">
            <span class="admin-card__icon"><?= icon('lock') ?></span>
            <h2>Create your password</h2>
            <p>This is the first time the owner area is opened. Choose a password — you will use it to sign in and post job openings. Do this now, before sharing the website link.</p>
            <form method="post" action="<?= e(url('admin/')) ?>" novalidate>
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="setup">
                <div class="field"><input type="password" id="a-pass" name="password" placeholder=" " required minlength="8" autocomplete="new-password"><label for="a-pass">New password (8+ characters)</label></div>
                <div class="field"><input type="password" id="a-pass2" name="password2" placeholder=" " required minlength="8" autocomplete="new-password"><label for="a-pass2">Repeat the password</label></div>
                <button class="btn btn--primary btn--block magnetic" type="submit"><span>Create password</span><?= icon('arrow-right') ?></button>
            </form>
        </div>

        <?php elseif ($view === 'login'): ?>
        <div class="admin-card admin-card--narrow">
            <span class="admin-card__icon"><?= icon('lock') ?></span>
            <h2>Owner sign in</h2>
            <form method="post" action="<?= e(url('admin/')) ?>" novalidate>
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="login">
                <div class="field"><input type="password" id="a-login" name="password" placeholder=" " required autocomplete="current-password" autofocus><label for="a-login">Password</label></div>
                <button class="btn btn--primary btn--block magnetic" type="submit"><span>Sign in</span><?= icon('arrow-right') ?></button>
            </form>
        </div>

        <?php elseif ($view === 'form'): ?>
        <div class="admin-card">
            <div class="admin-card__top">
                <h2><?= $editing ? 'Edit job opening' : 'Post a new job opening' ?></h2>
                <a class="btn btn--outline" href="<?= e(url('admin/')) ?>"><?= icon('arrow-left') ?><span>Back to list</span></a>
            </div>
            <form method="post" action="<?= e(url('admin/')) ?>" novalidate class="admin-form">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" value="<?= e($editing['id'] ?? '') ?>">

                <div class="form-row">
                    <div class="<?= e($fieldClass('title')) ?>">
                        <input type="text" id="j-title" name="title" placeholder=" " required maxlength="100" value="<?= e($val('title')) ?>">
                        <label for="j-title">Job title *</label>
                        <p class="field__error"><?= e($errors['title'] ?? '') ?></p>
                    </div>
                    <div class="field">
                        <input type="text" id="j-dept" name="department" placeholder=" " maxlength="80" value="<?= e($val('department')) ?>">
                        <label for="j-dept">Department (e.g. Sales)</label>
                    </div>
                </div>
                <div class="form-row">
                    <div class="<?= e($fieldClass('location')) ?>">
                        <input type="text" id="j-loc" name="location" placeholder=" " required maxlength="100" value="<?= e($val('location')) ?>">
                        <label for="j-loc">Location *</label>
                        <p class="field__error"><?= e($errors['location'] ?? '') ?></p>
                    </div>
                    <div class="field field--select">
                        <select id="j-type" name="type">
                            <?php foreach (job_types() as $key => $label): ?>
                            <option value="<?= e($key) ?>"<?= $val('type') === $key ? ' selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="j-type">Job type</label>
                    </div>
                </div>
                <div class="form-row">
                    <div class="field">
                        <input type="text" id="j-exp" name="experience" placeholder=" " maxlength="60" value="<?= e($val('experience')) ?>">
                        <label for="j-exp">Experience (e.g. 1–3 years)</label>
                    </div>
                    <div class="field">
                        <input type="text" id="j-sal" name="salary" placeholder=" " maxlength="60" value="<?= e($val('salary')) ?>">
                        <label for="j-sal">Salary (optional)</label>
                    </div>
                </div>
                <div class="form-row">
                    <div class="<?= e($fieldClass('apply_by', 'field--float')) ?>">
                        <input type="date" id="j-by" name="apply_by" placeholder=" " value="<?= e($val('apply_by')) ?>">
                        <label for="j-by">Last date to apply (optional)</label>
                        <p class="field__error"><?= e($errors['apply_by'] ?? '') ?></p>
                    </div>
                    <div class="field field--select">
                        <select id="j-status" name="status">
                            <option value="open"<?= $val('status') !== 'closed' ? ' selected' : '' ?>>Open — show on the Career page</option>
                            <option value="closed"<?= $val('status') === 'closed' ? ' selected' : '' ?>>Closed — hide from the Career page</option>
                        </select>
                        <label for="j-status">Status</label>
                    </div>
                </div>
                <div class="<?= e($fieldClass('description')) ?>">
                    <textarea id="j-desc" name="description" rows="6" placeholder=" " required maxlength="4000"><?= e($val('description')) ?></textarea>
                    <label for="j-desc">About the role *</label>
                    <p class="field__error"><?= e($errors['description'] ?? '') ?></p>
                </div>
                <div class="field">
                    <textarea id="j-req" name="requirements" rows="5" placeholder=" " maxlength="3000"><?= e($val('requirements')) ?></textarea>
                    <label for="j-req">Requirements — one per line (optional)</label>
                </div>
                <div class="admin-form__actions">
                    <button class="btn btn--primary magnetic" type="submit"><span><?= $editing ? 'Save changes' : 'Post job' ?></span><?= icon('arrow-right') ?></button>
                    <a class="btn btn--outline" href="<?= e(url('admin/')) ?>"><span>Cancel</span></a>
                </div>
            </form>
        </div>

        <?php else:
            $all = jobs_all();
            $today = date('Y-m-d');
        ?>
        <div class="admin-card">
            <div class="admin-card__top">
                <h2>Your job openings <span class="catalogue__group-count"><?= count($all) ?></span></h2>
                <div class="admin-card__tools">
                    <a class="btn btn--primary magnetic" href="<?= e(url('admin/?new=1')) ?>"><?= icon('plus') ?><span>Post a new job</span></a>
                    <a class="btn btn--outline" href="<?= e(url('career/')) ?>"><span>View Career page</span><?= icon('external') ?></a>
                </div>
            </div>

            <?php if (!$all): ?>
            <p class="admin-empty">No job openings yet. Click <strong>Post a new job</strong> to add the first one — it appears on the Career page straight away.</p>
            <?php else: ?>
            <ul class="admin-jobs">
                <?php foreach ($all as $job):
                    $expired = ($job['apply_by'] ?? '') !== '' && $job['apply_by'] < $today;
                    $live    = ($job['status'] ?? 'open') === 'open' && !$expired;
                ?>
                <li class="admin-job<?= $live ? '' : ' is-off' ?>">
                    <div class="admin-job__info">
                        <p class="admin-job__title"><?= e($job['title']) ?>
                            <span class="status-pill status-pill--<?= $live ? 'open' : 'closed' ?>"><?= $live ? 'Live' : ($expired ? 'Expired' : 'Closed') ?></span>
                        </p>
                        <p class="admin-job__meta"><?= e(implode(' · ', array_filter([$job['department'], $job['location'], job_types()[$job['type']] ?? '', 'Posted ' . date('d M Y', strtotime($job['posted_at'])), $job['apply_by'] !== '' ? 'Apply by ' . date('d M Y', strtotime($job['apply_by'])) : '']))) ?></p>
                    </div>
                    <div class="admin-job__actions">
                        <a class="btn btn--outline btn--sm" href="<?= e(url('admin/?edit=' . rawurlencode($job['id']))) ?>"><?= icon('edit') ?><span>Edit</span></a>
                        <form method="post" action="<?= e(url('admin/')) ?>">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="id" value="<?= e($job['id']) ?>">
                            <button class="btn btn--outline btn--sm" type="submit"><?= icon(($job['status'] ?? 'open') === 'open' ? 'pause' : 'play') ?><span><?= ($job['status'] ?? 'open') === 'open' ? 'Close' : 'Reopen' ?></span></button>
                        </form>
                        <form method="post" action="<?= e(url('admin/')) ?>" data-confirm="Delete “<?= e($job['title']) ?>” permanently?">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= e($job['id']) ?>">
                            <button class="btn btn--danger btn--sm" type="submit"><?= icon('trash') ?><span>Delete</span></button>
                        </form>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>

        <details class="admin-card admin-card--plain admin-password">
            <summary><?= icon('lock') ?><span>Change password</span></summary>
            <form method="post" action="<?= e(url('admin/')) ?>" novalidate class="admin-form">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="password">
                <div class="field"><input type="password" id="p-cur" name="current" placeholder=" " required autocomplete="current-password"><label for="p-cur">Current password</label></div>
                <div class="form-row">
                    <div class="field"><input type="password" id="p-new" name="password" placeholder=" " required minlength="8" autocomplete="new-password"><label for="p-new">New password (8+ characters)</label></div>
                    <div class="field"><input type="password" id="p-new2" name="password2" placeholder=" " required minlength="8" autocomplete="new-password"><label for="p-new2">Repeat new password</label></div>
                </div>
                <button class="btn btn--primary magnetic" type="submit"><span>Change password</span></button>
            </form>
        </details>

        <form method="post" action="<?= e(url('admin/')) ?>" class="admin-logout">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="logout">
            <button class="btn btn--outline" type="submit"><?= icon('log-out') ?><span>Sign out</span></button>
        </form>
        <?php endif; ?>
    </div>
</section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
