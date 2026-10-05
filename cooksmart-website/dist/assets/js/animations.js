/**
 * CookSmart animation engine (GSAP + ScrollTrigger).
 *
 * Declarative — any template (or future WordPress block) can animate with attributes:
 *   data-anim="fade-up | fade-in | slide-left | slide-right | zoom-in | split-up | words-scrub | draw-svg"
 *   data-delay="0.2"
 *   data-anim-stagger="fade-up" data-stagger="0.12"   (animates the element's children)
 *   data-counter="90" [data-format="comma"]            (counts up when visible)
 *   data-parallax data-speed="0.1"                     (scroll parallax)
 * Waits for the "cs:ready" event (fired by main.js once the preloader / page curtain is gone).
 */
(function () {
  'use strict';

  var html = document.documentElement;
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /** Wrap every word of an element in mask spans; returns the inner spans. Safe to call twice. */
  function splitWords(el) {
    if (!el) return [];
    if (el.dataset.splitDone) return el.querySelectorAll('.split-word__inner');
    var walker = document.createTreeWalker(el, NodeFilter.SHOW_TEXT);
    var nodes = [];
    while (walker.nextNode()) nodes.push(walker.currentNode);
    nodes.forEach(function (node) {
      var frag = document.createDocumentFragment();
      node.textContent.split(/(\s+)/).forEach(function (part) {
        if (!part) return;
        if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(part)); return; }
        var outer = document.createElement('span');
        var inner = document.createElement('span');
        outer.className = 'split-word';
        inner.className = 'split-word__inner';
        inner.textContent = part;
        outer.appendChild(inner);
        frag.appendChild(outer);
      });
      node.parentNode.replaceChild(frag, node);
    });
    el.dataset.splitDone = '1';
    el.classList.add('is-split');
    return el.querySelectorAll('.split-word__inner');
  }

  /** Wrap words in plain spans for the scroll-scrubbed highlight effect. */
  function escapeHtml(s) {
    return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  function splitScrub(el) {
    var text = el.textContent.trim();
    el.innerHTML = '<span class="visually-hidden">' + escapeHtml(text) + '</span>' +
      text.split(/\s+/).map(function (w) { return '<span class="scrub-word" aria-hidden="true">' + escapeHtml(w) + '</span>'; }).join(' ');
    return el.querySelectorAll('.scrub-word');
  }

  function formatNumber(value, format) {
    var n = Math.round(value);
    return format === 'comma' ? n.toLocaleString('en-IN') : String(n);
  }

  window.CSAnim = { splitWords: splitWords };

  if (!window.gsap || !window.ScrollTrigger) {
    html.classList.remove('js');
    return;
  }
  gsap.registerPlugin(ScrollTrigger);
  html.classList.add('anim-ready');

  var PRESETS = {
    'fade-up': { y: 50, opacity: 0 },
    'fade-in': { opacity: 0 },
    'slide-left': { x: -80, opacity: 0 },
    'slide-right': { x: 80, opacity: 0 },
    'zoom-in': { scale: 0.86, opacity: 0 }
  };
  var TO = { x: 0, y: 0, scale: 1, opacity: 1 };

  function revealWithoutMotion() {
    document.querySelectorAll('[data-anim], [data-anim-stagger]').forEach(function (el) { el.classList.add('is-in'); });
    document.querySelectorAll('.scrub-word').forEach(function (w) { w.style.opacity = 1; });
    document.querySelectorAll('.marker').forEach(function (m) { m.classList.add('is-marked'); });
    document.querySelectorAll('.shil-nora').forEach(function (s) { s.classList.add('is-drawn'); });
  }

  function init() {
    if (init.done) return;
    init.done = true;

    if (reduceMotion) { revealWithoutMotion(); return; }

    // Simple presets
    document.querySelectorAll('[data-anim]').forEach(function (el) {
      var type = el.dataset.anim;
      var delay = parseFloat(el.dataset.delay || '0');

      if (type === 'split-up') {
        var words = splitWords(el);
        el.classList.add('is-in');
        gsap.fromTo(words, { yPercent: 115 }, {
          yPercent: 0, duration: 1, ease: 'power4.out', stagger: 0.06, delay: delay,
          scrollTrigger: { trigger: el, start: 'top 90%', once: true }
        });
        return;
      }

      if (type === 'words-scrub') {
        var scrubWords = splitScrub(el);
        el.classList.add('is-in');
        gsap.to(scrubWords, {
          opacity: 1, stagger: 0.12, ease: 'none',
          scrollTrigger: { trigger: el, start: 'top 82%', end: 'bottom 48%', scrub: 0.6 }
        });
        return;
      }

      if (type === 'draw-svg') {
        el.classList.add('is-in');
        var paths = el.querySelectorAll('.d');
        paths.forEach(function (p) {
          var len = p.getTotalLength ? Math.ceil(p.getTotalLength()) + 2 : 600;
          p.style.strokeDasharray = len;
          p.style.strokeDashoffset = len;
        });
        gsap.to(paths, {
          strokeDashoffset: 0, ease: 'none', stagger: 0.035,
          scrollTrigger: {
            trigger: el, start: 'top 85%', end: 'center 40%', scrub: 0.8,
            onUpdate: function (self) { el.classList.toggle('is-drawn', self.progress > 0.96); }
          }
        });
        return;
      }

      var from = PRESETS[type];
      if (!from) { el.classList.add('is-in'); return; }
      gsap.fromTo(el, from, Object.assign({}, TO, {
        duration: 1, ease: 'power3.out', delay: delay,
        scrollTrigger: { trigger: el, start: 'top 88%', once: true }
      }));
      el.classList.add('is-in');
    });

    // Staggered groups
    document.querySelectorAll('[data-anim-stagger]').forEach(function (group) {
      var from = PRESETS[group.dataset.animStagger] || PRESETS['fade-up'];
      var children = Array.prototype.slice.call(group.children).filter(function (c) { return c.tagName !== 'svg'; });
      gsap.fromTo(children, from, Object.assign({}, TO, {
        duration: 0.9, ease: 'power3.out', stagger: parseFloat(group.dataset.stagger || '0.1'),
        scrollTrigger: { trigger: group, start: 'top 88%', once: true }
      }));
      group.classList.add('is-in');
    });

    // Heading swoosh underline draws in
    document.querySelectorAll('.swoosh-line').forEach(function (svg) {
      var lines = svg.querySelectorAll('path');
      gsap.set(lines, { strokeDashoffset: 240 });
      gsap.to(lines, {
        strokeDashoffset: 0, duration: 1.3, ease: 'power3.inOut', stagger: 0.18,
        scrollTrigger: { trigger: svg, start: 'top 92%', once: true }
      });
    });

    // Counters
    document.querySelectorAll('[data-counter]').forEach(function (el) {
      var target = parseFloat(el.dataset.counter);
      var format = el.dataset.format || '';
      var obj = { v: 0 };
      el.textContent = formatNumber(0, format);
      ScrollTrigger.create({
        trigger: el, start: 'top 92%', once: true,
        onEnter: function () {
          gsap.to(obj, {
            v: target, duration: target > 100 ? 2.4 : 1.8, ease: 'power2.out',
            onUpdate: function () { el.textContent = formatNumber(obj.v, format); }
          });
        }
      });
    });

    // Highlighter marks
    document.querySelectorAll('.marker').forEach(function (m) {
      ScrollTrigger.create({ trigger: m, start: 'top 80%', once: true, onEnter: function () { m.classList.add('is-marked'); } });
    });

    // Parallax
    document.querySelectorAll('[data-parallax]').forEach(function (el) {
      var speed = parseFloat(el.dataset.speed || '0.1');
      gsap.to(el, {
        yPercent: speed * -100, ease: 'none',
        scrollTrigger: { trigger: el.closest('section') || el, start: 'top bottom', end: 'bottom top', scrub: true }
      });
    });

    // Timeline lines (About journey, Policy commitments) grow with scroll
    document.querySelectorAll('.journey__line, .steps__line').forEach(function (line) {
      gsap.fromTo(line, { scaleY: 0 }, {
        scaleY: 1, transformOrigin: 'top center', ease: 'none',
        scrollTrigger: { trigger: line.parentElement, start: 'top 75%', end: 'bottom 60%', scrub: 0.6 }
      });
    });

    window.addEventListener('load', function () { ScrollTrigger.refresh(); });
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(function () { ScrollTrigger.refresh(); });
  }

  if (window.csReady) init();
  document.addEventListener('cs:ready', init);
  // Never leave content hidden if the ready signal does not arrive.
  setTimeout(init, 3200);
})();
