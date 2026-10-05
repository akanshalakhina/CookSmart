/** Products page — category filter with GSAP Flip re-ordering and a sliding pill. */
(function () {
  'use strict';

  var grid = document.querySelector('[data-product-grid]');
  var buttons = Array.prototype.slice.call(document.querySelectorAll('[data-filter]'));
  var pill = document.querySelector('.filter-bar__pill');
  if (!grid || !buttons.length) return;

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var canFlip = !!(window.gsap && window.Flip) && !reduceMotion;
  if (canFlip) gsap.registerPlugin(Flip);

  function movePill(btn) {
    if (!pill || !btn) return;
    pill.style.left = btn.offsetLeft + 'px';
    pill.style.width = btn.offsetWidth + 'px';
  }

  function apply(filter, animate) {
    if (!buttons.some(function (b) { return b.dataset.filter === filter; })) filter = 'all';
    var cards = Array.prototype.slice.call(grid.querySelectorAll('.product-card'));
    var state = animate && canFlip ? Flip.getState(cards) : null;

    cards.forEach(function (card) {
      card.classList.toggle('is-hidden', filter !== 'all' && card.dataset.category !== filter);
    });
    buttons.forEach(function (b) {
      var on = b.dataset.filter === filter;
      b.classList.toggle('is-active', on);
      b.setAttribute('aria-selected', String(on));
      if (on) movePill(b);
    });

    if (state) {
      Flip.from(state, {
        duration: 0.7, ease: 'power3.inOut', absolute: true, stagger: 0.04,
        onEnter: function (els) { return gsap.fromTo(els, { opacity: 0, scale: 0.8 }, { opacity: 1, scale: 1, duration: 0.6, ease: 'back.out(1.4)' }); },
        onLeave: function (els) { return gsap.to(els, { opacity: 0, scale: 0.8, duration: 0.35 }); }
      });
    }
    if (animate) history.replaceState(null, '', filter === 'all' ? location.pathname : '#' + filter);
  }

  buttons.forEach(function (b) {
    b.addEventListener('click', function () { apply(b.dataset.filter, true); });
  });
  window.addEventListener('resize', function () { movePill(document.querySelector('.filter-bar__btn.is-active')); });
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(function () { movePill(document.querySelector('.filter-bar__btn.is-active')); });

  apply((location.hash || '#all').slice(1), false);
})();
