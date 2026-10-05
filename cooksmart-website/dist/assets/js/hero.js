/**
 * Home hero slider — circular reveal between slides, Rochak-style text slide-in,
 * springy product packs, Ken Burns background, arrows, swipe & keyboard.
 */
(function () {
  'use strict';

  var hero = document.querySelector('[data-hero]');
  if (!hero) return;

  var html = document.documentElement;
  var slides = Array.prototype.slice.call(hero.querySelectorAll('[data-slide]'));
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var hasGsap = !!window.gsap;
  var DURATION = 7; // seconds per slide
  var index = 0;
  var busy = false;
  var timer = null;
  var hovering = false;
  var focusInside = false;

  slides.forEach(function (s, i) {
    if (i !== 0) s.setAttribute('aria-hidden', 'true');
  });

  function theme(i) {
    hero.dataset.theme = slides[i].classList.contains('hero-slide--spices') ? 'dark' : 'light';
  }

  function animateIn(slide) {
    var title = slide.querySelector('[data-hero-title]');
    var items = slide.querySelectorAll('[data-hero-item]');
    var packs = slide.querySelectorAll('[data-hero-pack]');
    var bg = slide.querySelector('.hero-slide__bg');
    if (!hasGsap || reduceMotion) {
      [title].concat(Array.prototype.slice.call(items), Array.prototype.slice.call(packs)).forEach(function (el) { if (el) el.style.opacity = 1; });
      return;
    }
    var words = window.CSAnim ? window.CSAnim.splitWords(title) : [title];
    var tl = gsap.timeline();
    tl.set(title, { opacity: 1 })
      .fromTo(bg, { scale: 1.12 }, { scale: 1, duration: DURATION, ease: 'power1.out' }, 0)
      .fromTo(items[0], { x: -70, opacity: 0 }, { x: 0, opacity: 1, duration: 0.8, ease: 'power3.out' }, 0.1)
      .fromTo(words, { yPercent: 115 }, { yPercent: 0, duration: 1, stagger: 0.055, ease: 'power4.out' }, 0.2)
      .fromTo(Array.prototype.slice.call(items, 1), { x: -70, opacity: 0 }, { x: 0, opacity: 1, duration: 0.9, stagger: 0.12, ease: 'power3.out' }, 0.5)
      .fromTo(packs, { y: 90, opacity: 0, scale: 0.82 }, { y: 0, opacity: 1, scale: 1, duration: 1.2, stagger: 0.1, ease: 'back.out(1.5)' }, 0.3);
    return tl;
  }

  /** Auto-advance timer, paused while the visitor hovers or focuses the slider. */
  function runTimer() {
    if (timer) timer.kill();
    if (!hasGsap) return;
    timer = gsap.delayedCall(DURATION, function () { go((index + 1) % slides.length, 1); });
    if (hovering || focusInside || document.hidden) timer.pause();
  }

  function go(next, dir) {
    if (next === index || busy) return;
    var cur = slides[index];
    var nxt = slides[next];
    dir = dir || (next > index ? 1 : -1);
    index = next;
    theme(index);
    nxt.removeAttribute('aria-hidden');
    cur.setAttribute('aria-hidden', 'true');

    if (!hasGsap || reduceMotion) {
      cur.classList.remove('is-active');
      nxt.classList.add('is-active');
      animateIn(nxt);
      runTimer();
      return;
    }

    busy = true;
    var origin = dir > 0 ? '88% 50%' : '12% 50%';
    nxt.classList.add('is-active');
    gsap.set(nxt, { zIndex: 2 });
    gsap.to(cur.querySelector('.hero-slide__inner'), { x: -60 * dir, opacity: 0, duration: 0.6, ease: 'power2.in' });
    gsap.fromTo(nxt, { clipPath: 'circle(0% at ' + origin + ')' }, {
      clipPath: 'circle(150% at ' + origin + ')', duration: 1.15, ease: 'power3.inOut',
      onComplete: function () {
        cur.classList.remove('is-active');
        gsap.set(cur.querySelector('.hero-slide__inner'), { clearProps: 'all' });
        gsap.set(nxt, { clearProps: 'clipPath,zIndex' });
        busy = false;
      }
    });
    animateIn(nxt);
    runTimer();
  }

  hero.querySelector('[data-hero-next]').addEventListener('click', function () { go((index + 1) % slides.length, 1); });
  hero.querySelector('[data-hero-prev]').addEventListener('click', function () { go((index - 1 + slides.length) % slides.length, -1); });

  // Pause while the visitor is reading / interacting (WCAG 2.2.2)
  hero.addEventListener('pointerenter', function (e) { if (e.pointerType === 'mouse') { hovering = true; timer && timer.pause(); } });
  hero.addEventListener('pointerleave', function (e) { if (e.pointerType === 'mouse') { hovering = false; if (!focusInside) timer && timer.resume(); } });
  hero.addEventListener('focusin', function (e) {
    // Keyboard focus pauses autoplay; a mouse click on an arrow does not.
    if (!e.target.matches(':focus-visible')) return;
    focusInside = true;
    timer && timer.pause();
  });
  hero.addEventListener('focusout', function (e) {
    if (!hero.contains(e.relatedTarget)) { focusInside = false; if (!hovering) timer && timer.resume(); }
  });
  document.addEventListener('visibilitychange', function () {
    if (!timer) return;
    if (document.hidden) timer.pause(); else if (!hovering && !focusInside) timer.resume();
  });
  hero.addEventListener('keydown', function (e) {
    if (e.key === 'ArrowRight') go((index + 1) % slides.length, 1);
    if (e.key === 'ArrowLeft') go((index - 1 + slides.length) % slides.length, -1);
  });

  // Swipe on touch screens
  var startX = null, startY = null;
  hero.addEventListener('pointerdown', function (e) {
    if (e.pointerType !== 'mouse') { startX = e.clientX; startY = e.clientY; }
  });
  hero.addEventListener('pointerup', function (e) {
    if (startX === null) return;
    var dx = e.clientX - startX, dy = e.clientY - startY;
    if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy)) {
      dx < 0 ? go((index + 1) % slides.length, 1) : go((index - 1 + slides.length) % slides.length, -1);
    }
    startX = startY = null;
  });

  function start() {
    if (start.done) return;
    start.done = true;
    theme(0);
    html.classList.add('hero-ready');
    animateIn(slides[0]);
    runTimer();
  }
  if (window.csReady) start();
  document.addEventListener('cs:ready', start);
  setTimeout(start, 3200);
})();
