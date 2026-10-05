/**
 * CookSmart — global UI: preloader & page curtain, header, mobile menu, back-to-top,
 * shop-link notice, magnetic buttons and 3D card tilt.
 */
(function () {
  'use strict';

  var html = document.documentElement;
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  var startedAt = performance.now();

  /* ---------- Ready signal (after preloader / curtain) ---------- */
  function signalReady() {
    if (window.csReady) return;
    window.csReady = true;
    document.dispatchEvent(new CustomEvent('cs:ready'));
  }

  function finishPreloader() {
    var pre = document.querySelector('.preloader');
    var wait = Math.max(0, 1300 - (performance.now() - startedAt));
    setTimeout(function () {
      if (pre) pre.classList.add('is-done');
      setTimeout(signalReady, 350);
      setTimeout(function () { html.classList.remove('is-first-visit'); }, 1000);
    }, wait);
  }

  if (html.classList.contains('is-first-visit') && !reduceMotion) {
    if (document.readyState === 'complete') finishPreloader();
    else window.addEventListener('load', finishPreloader);
    setTimeout(finishPreloader, 3000); // slow network: don't hold the visitor
  } else if (html.classList.contains('is-entering') && !reduceMotion) {
    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        html.classList.add('curtain-out');
        setTimeout(signalReady, 260);
        setTimeout(function () { html.classList.remove('is-entering', 'curtain-out'); }, 900);
      });
    });
  } else {
    html.classList.remove('is-first-visit', 'is-entering');
    signalReady();
  }

  /* ---------- Page curtain on internal navigation ---------- */
  document.addEventListener('click', function (e) {
    var a = e.target.closest('a[href]');
    if (!a || reduceMotion || e.defaultPrevented) return;
    if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
    if (a.target && a.target !== '_self') return;
    if (a.hasAttribute('download') || a.hasAttribute('data-shop-pending') || a.hasAttribute('data-no-transition')) return;
    var url;
    try { url = new URL(a.href, location.href); } catch (err) { return; }
    if (url.origin !== location.origin || !/^https?:$/.test(url.protocol)) return;
    if (url.pathname === location.pathname && url.search === location.search) return; // same page (anchors)
    e.preventDefault();
    try { sessionStorage.setItem('cs-curtain', '1'); } catch (err) {}
    html.classList.add('curtain-in');
    setTimeout(function () { location.href = url.href; }, 620);
  });
  window.addEventListener('pageshow', function (e) {
    if (e.persisted) html.classList.remove('curtain-in', 'is-entering', 'curtain-out');
  });

  /* ---------- Header: shrink on scroll, hide on scroll down ---------- */
  var header = document.querySelector('[data-header]');
  var lastY = window.scrollY;
  var ticking = false;
  var toTop = document.querySelector('[data-to-top]');
  var ring = toTop && toTop.querySelector('.to-top__progress');

  function onScroll() {
    var y = window.scrollY;
    var menuOpen = document.body.classList.contains('menu-open');
    if (header) {
      header.classList.toggle('is-scrolled', y > 40);
      if (!menuOpen) {
        if (y > 420 && y > lastY + 4) header.classList.add('is-hidden');
        else if (y < lastY - 4 || y < 420) header.classList.remove('is-hidden');
      }
    }
    if (toTop) {
      var max = document.documentElement.scrollHeight - window.innerHeight;
      var p = max > 0 ? y / max : 0;
      toTop.classList.toggle('is-visible', y > 600);
      if (ring) ring.style.strokeDashoffset = String(132 - 132 * p);
    }
    lastY = y;
    ticking = false;
  }
  window.addEventListener('scroll', function () {
    if (!ticking) { requestAnimationFrame(onScroll); ticking = true; }
  }, { passive: true });
  onScroll();

  if (toTop) {
    toTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
    });
  }

  /* ---------- Mobile menu ---------- */
  var burger = document.querySelector('[data-burger]');
  var menu = document.querySelector('[data-mobile-menu]');
  function setMenu(open) {
    if (!burger || !menu) return;
    burger.setAttribute('aria-expanded', String(open));
    burger.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    menu.classList.toggle('is-open', open);
    menu.setAttribute('aria-hidden', String(!open));
    document.body.classList.toggle('menu-open', open);
    if (open) {
      header && header.classList.remove('is-hidden');
      var first = menu.querySelector('a');
      setTimeout(function () { first && first.focus({ preventScroll: true }); }, 400);
    } else {
      burger.focus({ preventScroll: true });
    }
  }
  if (burger && menu) {
    burger.addEventListener('click', function () { setMenu(burger.getAttribute('aria-expanded') !== 'true'); });
    menu.addEventListener('click', function (e) { if (e.target.closest('a')) setMenu(false); });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && menu.classList.contains('is-open')) setMenu(false);
    });
    window.addEventListener('resize', function () {
      if (window.innerWidth >= 1024 && menu.classList.contains('is-open')) setMenu(false);
    });
  }

  /* ---------- Toast + "Shop Now" until the shop URL is configured ---------- */
  var toast = document.querySelector('[data-toast]');
  var toastTimer;
  function showToast(message) {
    if (!toast) return;
    toast.textContent = message;
    toast.classList.add('is-visible');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { toast.classList.remove('is-visible'); }, 4200);
  }
  window.csToast = showToast;
  document.addEventListener('click', function (e) {
    var link = e.target.closest('[data-shop-pending]');
    if (!link) return;
    e.preventDefault();
    showToast((window.CS && window.CS.shopPendingMessage) || 'Our online shop is coming soon.');
  });

  /* ---------- Magnetic buttons ---------- */
  if (finePointer && !reduceMotion) {
    document.querySelectorAll('.magnetic').forEach(function (btn) {
      btn.addEventListener('pointermove', function (e) {
        var r = btn.getBoundingClientRect();
        var x = (e.clientX - r.left - r.width / 2) * 0.22;
        var y = (e.clientY - r.top - r.height / 2) * 0.35;
        btn.style.translate = x.toFixed(1) + 'px ' + y.toFixed(1) + 'px';
      });
      btn.addEventListener('pointerleave', function () { btn.style.translate = ''; });
    });

    /* 3D tilt on product cards */
    document.querySelectorAll('[data-tilt]').forEach(function (card) {
      card.addEventListener('pointermove', function (e) {
        var r = card.getBoundingClientRect();
        var px = (e.clientX - r.left) / r.width - 0.5;
        var py = (e.clientY - r.top) / r.height - 0.5;
        card.style.setProperty('--ry', (px * 10).toFixed(2) + 'deg');
        card.style.setProperty('--rx', (py * -10).toFixed(2) + 'deg');
      });
      card.addEventListener('pointerleave', function () {
        card.style.setProperty('--ry', '0deg');
        card.style.setProperty('--rx', '0deg');
      });
    });
  }

})();
