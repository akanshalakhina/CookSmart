/**
 * PREVIEW ONLY — a practice copy of the owner area for the static design preview (Vercel cannot run PHP).
 * Jobs are kept in this browser's localStorage and shown on the preview's Career page (preview-careers.js).
 * The real owner area is admin.php: it keeps jobs on the server and its password never reaches the browser.
 * This file is copied into dist/ by build-static.php; it is not part of the PHP site.
 */
(function () {
  'use strict';

  var root = document.getElementById('demo-admin');
  if (!root) return;

  var ICON = window.CS_ICONS || {};
  var KEY = 'cs-demo-jobs';
  var SESSION_KEY = 'cs-demo-admin';
  var DEMO_ID = 'demo';
  var DEMO_HASH = 'f0b0036a54a5a9344c961873a08ae734802d60aeb430d5c03132d2aad3d8efaa'; // SHA-256 of the demo password
  var TYPES = { 'full-time': 'Full-time', 'part-time': 'Part-time', 'contract': 'Contract', 'internship': 'Internship' };

  var memory = null;      // used when the browser blocks localStorage
  var signedIn = false;
  var view = 'list';      // 'login' | 'list' | 'form'
  var editingId = '';
  var flash = null;       // { type: 'ok' | 'error', message }
  var formState = null;   // { values, errors } after a failed save

  try { signedIn = sessionStorage.getItem(SESSION_KEY) === '1'; } catch (err) {}
  if (!signedIn) view = 'login';

  /* ---------- storage ---------- */
  function readJobs() {
    try {
      var raw = localStorage.getItem(KEY);
      var list = raw ? JSON.parse(raw) : [];
      list = Array.isArray(list) ? list : [];
      memory = list;
    } catch (err) { /* keep the in-memory copy */ }
    return (memory || []).slice().sort(function (a, b) {
      return String(b.posted_at).localeCompare(String(a.posted_at)) || (b.t || 0) - (a.t || 0);
    });
  }
  function writeJobs(list) {
    memory = list;
    try { localStorage.setItem(KEY, JSON.stringify(list)); } catch (err) {}
  }

  /* ---------- helpers ---------- */
  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }
  function icon(name) { return ICON[name] || ''; }
  function today() {
    var d = new Date();
    return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
  }
  function fmt(iso) {
    var d = new Date(iso + 'T00:00:00');
    return isNaN(d) ? iso : d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
  }
  function sha256(text) {
    if (!(window.crypto && window.crypto.subtle)) return Promise.reject(new Error('no-crypto'));
    return window.crypto.subtle.digest('SHA-256', new TextEncoder().encode(text)).then(function (buf) {
      return Array.prototype.map.call(new Uint8Array(buf), function (b) { return ('0' + b.toString(16)).slice(-2); }).join('');
    });
  }
  function isLive(job) {
    return job.status !== 'closed' && !(job.apply_by && job.apply_by < today());
  }

  /* ---------- views ---------- */
  function flashHtml() {
    if (!flash) return '';
    var html = '<p class="form-alert' + (flash.type === 'ok' ? ' form-alert--info' : '') + '" role="' + (flash.type === 'ok' ? 'status' : 'alert') + '">' + esc(flash.message) + '</p>';
    flash = null;
    return html;
  }

  function loginView() {
    return '<div class="admin-card admin-card--narrow">' +
      '<span class="admin-card__icon">' + icon('lock') + '</span>' +
      '<h2>Owner sign in</h2>' +
      '<form data-form="login" novalidate>' +
      '<div class="field"><input type="text" id="a-id" name="username" placeholder=" " required autocomplete="username" autocapitalize="none" spellcheck="false"><label for="a-id">Login ID</label></div>' +
      '<div class="field"><input type="password" id="a-login" name="password" placeholder=" " required autocomplete="current-password"><label for="a-login">Password</label></div>' +
      '<button class="btn btn--primary btn--block" type="submit"><span>Sign in</span>' + icon('arrow-right') + '</button>' +
      '</form></div>';
  }

  function listView() {
    var jobs = readJobs();
    var html = '<div class="admin-card"><div class="admin-card__top">' +
      '<h2>Your job openings <span class="catalogue__group-count">' + jobs.length + '</span></h2>' +
      '<div class="admin-card__tools">' +
      '<button class="btn btn--primary" type="button" data-act="new">' + icon('plus') + '<span>Post a new job</span></button>' +
      '<a class="btn btn--outline" href="/career/"><span>View Career page</span>' + icon('external') + '</a>' +
      '</div></div>';
    if (!jobs.length) {
      html += '<p class="admin-empty">No job openings yet. Click <strong>Post a new job</strong> to add the first one — it appears on the Career page straight away.</p>';
    } else {
      html += '<ul class="admin-jobs">';
      jobs.forEach(function (job) {
        var expired = job.apply_by && job.apply_by < today();
        var live = isLive(job);
        var meta = [job.department, job.location, TYPES[job.type] || '', 'Posted ' + fmt(job.posted_at), job.apply_by ? 'Apply by ' + fmt(job.apply_by) : '']
          .filter(Boolean).join(' · ');
        html += '<li class="admin-job' + (live ? '' : ' is-off') + '"><div class="admin-job__info">' +
          '<p class="admin-job__title">' + esc(job.title) + ' <span class="status-pill status-pill--' + (live ? 'open' : 'closed') + '">' + (live ? 'Live' : (expired ? 'Expired' : 'Closed')) + '</span></p>' +
          '<p class="admin-job__meta">' + esc(meta) + '</p></div>' +
          '<div class="admin-job__actions">' +
          '<button class="btn btn--outline btn--sm" type="button" data-act="edit" data-id="' + esc(job.id) + '">' + icon('edit') + '<span>Edit</span></button>' +
          '<button class="btn btn--outline btn--sm" type="button" data-act="toggle" data-id="' + esc(job.id) + '">' + icon(job.status === 'closed' ? 'play' : 'pause') + '<span>' + (job.status === 'closed' ? 'Reopen' : 'Close') + '</span></button>' +
          '<button class="btn btn--danger btn--sm" type="button" data-act="delete" data-id="' + esc(job.id) + '" data-title="' + esc(job.title) + '">' + icon('trash') + '<span>Delete</span></button>' +
          '</div></li>';
      });
      html += '</ul>';
    }
    html += '</div>' +
      '<div class="demo-tools"><button class="btn btn--outline btn--sm" type="button" data-act="reset"><span>Clear all demo jobs</span></button></div>' +
      '<div class="admin-logout"><button class="btn btn--outline" type="button" data-act="logout">' + icon('log-out') + '<span>Sign out</span></button></div>';
    return html;
  }

  function fieldCls(errors, key, extra) {
    return ('field ' + (extra || '') + (errors[key] ? ' has-error' : '')).trim();
  }

  function formView() {
    var editing = editingId ? readJobs().filter(function (j) { return j.id === editingId; })[0] : null;
    var values = (formState && formState.values) || (editing
      ? Object.assign({}, editing, { requirements: (editing.requirements || []).join('\n') })
      : { type: 'full-time', status: 'open' });
    var errors = (formState && formState.errors) || {};
    formState = null;
    var v = function (k) { return esc(values[k] == null ? '' : values[k]); };
    var opts = Object.keys(TYPES).map(function (k) {
      return '<option value="' + k + '"' + (values.type === k ? ' selected' : '') + '>' + TYPES[k] + '</option>';
    }).join('');
    return '<div class="admin-card"><div class="admin-card__top"><h2>' + (editing ? 'Edit job opening' : 'Post a new job opening') + '</h2>' +
      '<button class="btn btn--outline" type="button" data-act="back">' + icon('arrow-left') + '<span>Back to list</span></button></div>' +
      '<form data-form="job" novalidate class="admin-form">' +
      '<div class="form-row">' +
      '<div class="' + fieldCls(errors, 'title') + '"><input type="text" id="j-title" name="title" placeholder=" " required maxlength="100" value="' + v('title') + '"><label for="j-title">Job title *</label><p class="field__error">' + esc(errors.title || '') + '</p></div>' +
      '<div class="field"><input type="text" id="j-dept" name="department" placeholder=" " maxlength="80" value="' + v('department') + '"><label for="j-dept">Department (e.g. Sales)</label></div>' +
      '</div><div class="form-row">' +
      '<div class="' + fieldCls(errors, 'location') + '"><input type="text" id="j-loc" name="location" placeholder=" " required maxlength="100" value="' + v('location') + '"><label for="j-loc">Location *</label><p class="field__error">' + esc(errors.location || '') + '</p></div>' +
      '<div class="field field--select"><select id="j-type" name="type">' + opts + '</select><label for="j-type">Job type</label></div>' +
      '</div><div class="form-row">' +
      '<div class="field"><input type="text" id="j-exp" name="experience" placeholder=" " maxlength="60" value="' + v('experience') + '"><label for="j-exp">Experience (e.g. 1–3 years)</label></div>' +
      '<div class="field"><input type="text" id="j-sal" name="salary" placeholder=" " maxlength="60" value="' + v('salary') + '"><label for="j-sal">Salary (optional)</label></div>' +
      '</div><div class="form-row">' +
      '<div class="field field--float"><input type="date" id="j-by" name="apply_by" placeholder=" " value="' + v('apply_by') + '"><label for="j-by">Last date to apply (optional)</label></div>' +
      '<div class="field field--select"><select id="j-status" name="status"><option value="open"' + (values.status !== 'closed' ? ' selected' : '') + '>Open — show on the Career page</option><option value="closed"' + (values.status === 'closed' ? ' selected' : '') + '>Closed — hide from the Career page</option></select><label for="j-status">Status</label></div>' +
      '</div>' +
      '<div class="' + fieldCls(errors, 'description') + '"><textarea id="j-desc" name="description" rows="6" placeholder=" " required maxlength="4000">' + v('description') + '</textarea><label for="j-desc">About the role *</label><p class="field__error">' + esc(errors.description || '') + '</p></div>' +
      '<div class="field"><textarea id="j-req" name="requirements" rows="5" placeholder=" " maxlength="3000">' + v('requirements') + '</textarea><label for="j-req">Requirements — one per line (optional)</label></div>' +
      '<div class="admin-form__actions"><button class="btn btn--primary" type="submit"><span>' + (editing ? 'Save changes' : 'Post job') + '</span>' + icon('arrow-right') + '</button>' +
      '<button class="btn btn--outline" type="button" data-act="back"><span>Cancel</span></button></div>' +
      '</form></div>';
  }

  function render() {
    var body = view === 'login' ? loginView() : (view === 'form' ? formView() : listView());
    root.innerHTML = flashHtml() + body;
    var first = root.querySelector('input[type="text"], input[type="password"]');
    if (first && view !== 'list') first.focus({ preventScroll: true });
  }

  /* ---------- actions ---------- */
  function cleanJob(form, existing) {
    var get = function (name) { return (form.elements[name] ? form.elements[name].value : '') || ''; };
    var line = function (name, max) { return get(name).replace(/\s+/g, ' ').trim().slice(0, max); };
    var requirements = get('requirements').replace(/\r/g, '').split('\n').map(function (l) {
      return l.trim().replace(/^[-•*·\s]+/, '').slice(0, 200);
    }).filter(Boolean).slice(0, 20);
    var type = get('type');
    var job = {
      id: existing ? existing.id : Math.random().toString(16).slice(2, 10),
      title: line('title', 100), department: line('department', 80), location: line('location', 100),
      type: TYPES[type] ? type : 'full-time', experience: line('experience', 60), salary: line('salary', 60),
      description: get('description').replace(/\r/g, '').trim().slice(0, 4000), requirements: requirements,
      apply_by: get('apply_by'), status: get('status') === 'closed' ? 'closed' : 'open',
      posted_at: existing ? existing.posted_at : today(), t: existing ? existing.t : Date.now()
    };
    var errors = {};
    if (job.title.length < 3) errors.title = 'Please enter the job title.';
    if (!job.location) errors.location = 'Please enter the job location.';
    if (job.description.length < 20) errors.description = 'Please describe the role (at least 20 characters).';
    return { job: job, errors: errors, raw: {
      title: get('title'), department: get('department'), location: get('location'), type: type, experience: get('experience'),
      salary: get('salary'), apply_by: get('apply_by'), status: get('status'), description: get('description'), requirements: get('requirements')
    } };
  }

  root.addEventListener('submit', function (e) {
    var form = e.target.closest('form[data-form]');
    if (!form) return;
    e.preventDefault();

    if (form.dataset.form === 'login') {
      var id = form.elements.username.value.trim().toLowerCase();
      sha256(form.elements.password.value).then(function (hash) {
        if (id === DEMO_ID && hash === DEMO_HASH) {
          signedIn = true; view = 'list';
          try { sessionStorage.setItem(SESSION_KEY, '1'); } catch (err) {}
        } else {
          flash = { type: 'error', message: 'That Login ID or password is not correct.' };
        }
        render();
      }, function () {
        flash = { type: 'error', message: 'This browser cannot check the demo login. Please open the page over https.' };
        render();
      });
      return;
    }

    if (form.dataset.form === 'job') {
      var existing = editingId ? readJobs().filter(function (j) { return j.id === editingId; })[0] : null;
      var result = cleanJob(form, existing);
      if (Object.keys(result.errors).length) {
        formState = { values: result.raw, errors: result.errors };
        flash = { type: 'error', message: 'Please fix the highlighted fields.' };
        render();
        return;
      }
      var list = readJobs().filter(function (j) { return j.id !== result.job.id; });
      list.push(result.job);
      writeJobs(list);
      flash = { type: 'ok', message: existing ? 'Job updated.' : 'Job posted — it is now on the Career page.' };
      view = 'list'; editingId = '';
      render();
      window.scrollTo({ top: root.getBoundingClientRect().top + window.scrollY - 120, behavior: 'smooth' });
    }
  });

  root.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-act]');
    if (!btn) return;
    var act = btn.dataset.act;
    var id = btn.dataset.id;

    if (act === 'new') { view = 'form'; editingId = ''; formState = null; render(); }
    else if (act === 'edit') { view = 'form'; editingId = id; formState = null; render(); }
    else if (act === 'back') { view = 'list'; editingId = ''; formState = null; render(); }
    else if (act === 'toggle') {
      writeJobs(readJobs().map(function (j) { if (j.id === id) j.status = j.status === 'closed' ? 'open' : 'closed'; return j; }));
      flash = { type: 'ok', message: 'Job status changed.' }; render();
    } else if (act === 'delete') {
      if (window.confirm('Delete “' + btn.dataset.title + '” permanently?')) {
        writeJobs(readJobs().filter(function (j) { return j.id !== id; }));
        flash = { type: 'ok', message: 'Job deleted.' }; render();
      }
    } else if (act === 'reset') {
      if (window.confirm('Remove all demo jobs from this browser?')) {
        writeJobs([]); flash = { type: 'ok', message: 'All demo jobs were removed.' }; render();
      }
    } else if (act === 'logout') {
      signedIn = false; view = 'login';
      try { sessionStorage.removeItem(SESSION_KEY); } catch (err) {}
      flash = { type: 'ok', message: 'You have been signed out.' }; render();
    }
  });

  render();
})();
