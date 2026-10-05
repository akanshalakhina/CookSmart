/** Contact page — office/map tabs and the enquiry form (validation + AJAX submit). */
(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- Office tabs ---------- */
  var tabs = Array.prototype.slice.call(document.querySelectorAll('[data-location-tab]'));
  var panels = Array.prototype.slice.call(document.querySelectorAll('[data-location-panel]'));

  function activate(key, focus) {
    tabs.forEach(function (t) {
      var on = t.dataset.locationTab === key;
      t.classList.toggle('is-active', on);
      t.setAttribute('aria-selected', String(on));
      t.tabIndex = on ? 0 : -1;
      if (on && focus) t.focus();
    });
    panels.forEach(function (p) {
      var on = p.dataset.locationPanel === key;
      if (on) {
        var frame = p.querySelector('iframe[data-src]');
        if (frame) { frame.src = frame.dataset.src; frame.removeAttribute('data-src'); }
        if (p.hidden) {
          p.hidden = false;
          if (!reduceMotion) {
            p.classList.remove('is-entering');
            void p.offsetWidth;
            p.classList.add('is-entering');
          }
        }
      } else {
        p.hidden = true;
      }
    });
  }
  tabs.forEach(function (t, i) {
    t.tabIndex = t.classList.contains('is-active') ? 0 : -1;
    t.addEventListener('click', function () { activate(t.dataset.locationTab); });
    t.addEventListener('keydown', function (e) {
      if (e.key !== 'ArrowDown' && e.key !== 'ArrowUp' && e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
      e.preventDefault();
      var dir = (e.key === 'ArrowDown' || e.key === 'ArrowRight') ? 1 : -1;
      var nextTab = tabs[(i + dir + tabs.length) % tabs.length];
      activate(nextTab.dataset.locationTab, true);
    });
  });
  var hashKey = location.hash.slice(1);
  if (tabs.some(function (t) { return t.dataset.locationTab === hashKey; })) activate(hashKey);

  /* ---------- Enquiry form ---------- */
  var form = document.querySelector('[data-enquiry-form]');
  if (!form) return;
  var success = document.querySelector('[data-form-success]');
  var alertBox = form.querySelector('.form-alert');
  var submit = form.querySelector('[data-submit]');

  var rules = {
    name: function (v) { return v.trim().length >= 2 || 'Please enter your name.'; },
    phone: function (v) { return /^\+?[0-9][0-9\s\-()]{7,18}$/.test(v.trim()) || 'Please enter a valid phone number.'; },
    email: function (v) { return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()) || 'Please enter a valid email address.'; },
    message: function (v) { return v.trim().length >= 10 || 'Please tell us a little more (at least 10 characters).'; }
  };

  function setError(name, message) {
    var input = form.elements[name];
    if (!input) return;
    var field = input.closest('.field');
    var err = field && field.querySelector('.field__error');
    if (field) field.classList.toggle('has-error', !!message);
    if (err) err.textContent = message || '';
    input.setAttribute('aria-invalid', message ? 'true' : 'false');
    if (err) {
      if (message) input.setAttribute('aria-describedby', err.id);
      else input.removeAttribute('aria-describedby');
    }
  }

  function validate(name) {
    var result = rules[name](form.elements[name].value);
    setError(name, result === true ? '' : result);
    return result === true;
  }

  Object.keys(rules).forEach(function (name) {
    var input = form.elements[name];
    input.addEventListener('blur', function () { if (input.value) validate(name); });
    input.addEventListener('input', function () {
      if (input.closest('.field').classList.contains('has-error')) validate(name);
    });
  });

  function showAlert(message, tone) {
    if (!alertBox) return;
    alertBox.textContent = message || '';
    alertBox.hidden = !message;
    alertBox.classList.toggle('form-alert--info', tone === 'info');
  }

  function burst(origin) {
    if (reduceMotion || !window.gsap) return;
    var colors = ['#F20000', '#FFC93C', '#64B741', '#A8E13C', '#C07C0A'];
    var rect = origin.getBoundingClientRect();
    for (var i = 0; i < 36; i++) {
      var dot = document.createElement('span');
      var size = 4 + Math.random() * 7;
      dot.style.cssText = 'position:fixed;z-index:300;pointer-events:none;border-radius:50%;width:' + size + 'px;height:' + size + 'px;background:' +
        colors[i % colors.length] + ';left:' + (rect.left + rect.width / 2) + 'px;top:' + (rect.top + rect.height / 3) + 'px;';
      document.body.appendChild(dot);
      gsap.to(dot, {
        x: (Math.random() - 0.5) * 420, y: -60 - Math.random() * 260, opacity: 0, rotation: Math.random() * 360,
        duration: 1.4 + Math.random() * 0.6, ease: 'power3.out',
        onComplete: (function (d) { return function () { d.remove(); }; })(dot)
      });
    }
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    showAlert('');
    var ok = Object.keys(rules).map(validate).every(Boolean);
    if (!ok) {
      var firstBad = form.querySelector('.has-error input, .has-error textarea');
      firstBad && firstBad.focus();
      return;
    }
    // Static design preview (e.g. on Vercel): nothing to send to.
    if (form.hasAttribute('data-preview')) {
      showAlert('This is a design preview — the enquiry form will be active on the live website.', 'info');
      return;
    }
    submit.disabled = true;
    submit.classList.add('is-loading');

    fetch(form.action, {
      method: 'POST',
      body: new FormData(form),
      headers: { Accept: 'application/json' },
      credentials: 'same-origin'
    })
      .then(function (res) { return res.json().catch(function () { return { ok: false, message: 'Something went wrong. Please try again.' }; }); })
      .then(function (data) {
        if (data.ok) {
          form.hidden = true;
          success.hidden = false;
          var msg = success.querySelector('[data-success-message]');
          if (msg && data.message) msg.textContent = data.message;
          success.querySelectorAll('circle, path').forEach(function (el) {
            el.style.animation = 'none'; void el.getBoundingClientRect(); el.style.animation = '';
          });
          burst(success);
          success.setAttribute('tabindex', '-1');
          success.focus();
        } else {
          var errors = data.errors || {};
          Object.keys(errors).forEach(function (name) { setError(name, errors[name]); });
          showAlert(data.message);
        }
      })
      .catch(function () {
        showAlert('We could not reach the server. Please check your connection, or call us directly.');
      })
      .then(function () {
        submit.disabled = false;
        submit.classList.remove('is-loading');
      });
  });

  var again = document.querySelector('[data-form-reset]');
  again && again.addEventListener('click', function () {
    form.reset();
    Object.keys(rules).forEach(function (n) { setError(n, ''); });
    success.hidden = true;
    form.hidden = false;
    form.elements.name.focus();
  });
})();
