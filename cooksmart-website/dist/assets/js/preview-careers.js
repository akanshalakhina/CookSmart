/**
 * PREVIEW ONLY — makes the static design preview show the jobs posted in the demo owner area (preview-admin.js).
 * On the Career page it lists those jobs; on the Contact page it fills in the enquiry form from an "Apply Now" link.
 * The real PHP pages do this on the server (career.php, contact-us.php); this file is copied into dist/ by build-static.php.
 */
(function () {
  'use strict';

  var KEY = 'cs-demo-jobs';
  var ICON = window.CS_ICONS || {};
  var TYPES = { 'full-time': 'Full-time', 'part-time': 'Part-time', 'contract': 'Contract', 'internship': 'Internship' };

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
  function openJobs() {
    var list = [];
    try { list = JSON.parse(localStorage.getItem(KEY) || '[]'); } catch (err) {}
    if (!Array.isArray(list)) return [];
    return list.filter(function (j) {
      return j.status !== 'closed' && !(j.apply_by && j.apply_by < today());
    }).sort(function (a, b) {
      return String(b.posted_at).localeCompare(String(a.posted_at)) || (b.t || 0) - (a.t || 0);
    });
  }
  function paragraphs(text) {
    return String(text || '').split(/\n+/).map(function (p) { return p.trim(); }).filter(Boolean)
      .map(function (p) { return '<p>' + esc(p) + '</p>'; }).join('');
  }

  /* ---------- Career page ---------- */
  function career() {
    var section = document.querySelector('.careers');
    if (!section) return;
    var jobs = openJobs();
    if (!jobs.length) return;
    var wrap = section.querySelector('.container');

    var waLink = document.querySelector('a[href^="https://wa.me/"]');
    var waBase = waLink ? waLink.getAttribute('href').split('?')[0] : '';
    var newSince = new Date(Date.now() - 14 * 86400000);
    var newSinceIso = newSince.getFullYear() + '-' + ('0' + (newSince.getMonth() + 1)).slice(-2) + '-' + ('0' + newSince.getDate()).slice(-2);

    var title = wrap.querySelector('#openings-title');
    if (title) title.innerHTML = 'We are <em>hiring</em>';
    var lead = wrap.querySelector('.section-lead');
    if (lead) lead.textContent = 'Open the role that interests you to read the details, then apply with one click.';
    var empty = wrap.querySelector('.career-empty');
    if (empty) empty.remove();

    var note = document.createElement('p');
    note.className = 'form-alert form-alert--info';
    note.style.cssText = 'max-width:920px;margin:0 auto 22px;';
    note.innerHTML = '<strong>Preview demo:</strong> these openings were posted from the demo owner area and are saved only in this browser. On the real website they are saved on the server for everyone.';

    var list = document.createElement('div');
    list.className = 'job-list';
    list.innerHTML = jobs.map(function (job) {
      var apply = '/contact-us/?type=career&job=' + encodeURIComponent(job.title) + '#enquiry';
      var wa = waBase ? waBase + '?text=' + encodeURIComponent('Hello CookSmart, I would like to apply for the position of ' + job.title + '.') : '';
      var meta = '<li>' + icon('map-pin') + esc(job.location) + '</li><li>' + icon('briefcase') + esc(TYPES[job.type] || 'Full-time') + '</li>';
      if (job.experience) meta += '<li>' + icon('clock') + esc(job.experience) + '</li>';
      if (job.salary) meta += '<li>' + icon('rupee') + esc(job.salary) + '</li>';
      if (job.apply_by) meta += '<li>' + icon('calendar') + 'Apply by ' + esc(fmt(job.apply_by)) + '</li>';
      var reqs = (job.requirements || []).length
        ? '<h4>Requirements</h4><ul class="job-card__list">' + job.requirements.map(function (r) { return '<li>' + icon('check') + esc(r) + '</li>'; }).join('') + '</ul>'
        : '';
      return '<article class="job-card" id="job-' + esc(job.id) + '">' +
        '<div class="job-card__head"><div class="job-card__titles">' +
        (job.department ? '<p class="job-card__dept">' + esc(job.department) + '</p>' : '') +
        '<h3 class="job-card__title">' + esc(job.title) + (job.posted_at >= newSinceIso ? ' <span class="job-card__new">New</span>' : '') + '</h3></div>' +
        '<div class="job-card__actions"><a class="btn btn--primary" href="' + esc(apply) + '"><span>Apply Now</span>' + icon('arrow-right') + '</a>' +
        (wa ? '<a class="btn btn--outline" href="' + esc(wa) + '" target="_blank" rel="noopener" data-no-transition>' + icon('whatsapp') + '<span>WhatsApp</span></a>' : '') + '</div></div>' +
        '<ul class="job-card__meta">' + meta + '</ul>' +
        '<details class="job-card__more"><summary><span class="job-card__more-open">View role details</span><span class="job-card__more-close">Hide details</span>' + icon('arrow-right') + '</summary>' +
        '<div class="job-card__body">' + paragraphs(job.description) + reqs + '<p class="job-card__posted">Posted on ' + esc(fmt(job.posted_at)) + '</p></div></details>' +
        '</article>';
    }).join('');

    wrap.appendChild(note);
    wrap.appendChild(list);
  }

  /* ---------- Contact page: fill the form from an "Apply Now" link ---------- */
  function contact() {
    var form = document.querySelector('[data-enquiry-form]');
    if (!form) return;
    var params = new URLSearchParams(location.search);
    var type = params.get('type');
    var job = params.get('job');
    var select = form.querySelector('#f-type');
    if (select && type && Array.prototype.some.call(select.options, function (o) { return o.value === type; })) select.value = type;
    var message = form.querySelector('#f-message');
    if (message && type === 'career' && job && !message.value) {
      message.value = 'I would like to apply for the position of ' + job.replace(/[\r\n\0]+/g, ' ').trim().slice(0, 100) + '. ';
    }
  }

  career();
  contact();
})();
