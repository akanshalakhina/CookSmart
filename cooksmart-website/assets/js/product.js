/**
 * Product detail — pack size picker (the chosen pack stays in colour, the others turn grey),
 * front / back view, and mouse-parallax tilt.
 */
(function () {
  'use strict';

  var gallery = document.querySelector('[data-gallery]');
  if (!gallery) return;

  var stage = gallery.querySelector('[data-tilt-stage]');
  var lineup = gallery.querySelector('[data-lineup]');
  var items = Array.prototype.slice.call(gallery.querySelectorAll('.pack-lineup__item'));
  var viewBtns = Array.prototype.slice.call(gallery.querySelectorAll('[data-view-btn]'));
  var options = Array.prototype.slice.call(document.querySelectorAll('[data-pack-picker] .pack-option'));
  var note = document.querySelector('[data-pack-note]');
  var current = items.length ? items[0].dataset.pack : '';

  function itemFor(id) {
    return items.filter(function (i) { return i.dataset.pack === id; })[0];
  }

  /* ---------- pack size ---------- */
  function selectPack(id, opts) {
    var item = itemFor(id);
    if (!item) return;
    current = id;
    items.forEach(function (i) {
      var on = i === item;
      i.classList.toggle('is-selected', on);
      i.setAttribute('aria-pressed', String(on));
    });
    options.forEach(function (o) {
      var on = o.dataset.pack === id;
      o.classList.toggle('is-active', on);
      o.setAttribute('aria-checked', String(on));
      o.tabIndex = on ? 0 : -1;
    });
    // front / back thumbnails follow the selected pack
    viewBtns.forEach(function (b) {
      var img = b.querySelector('[data-view-thumb]');
      var src = item.dataset[b.dataset.viewBtn === 'back' ? 'thumbBack' : 'thumbFront'];
      if (img && src) img.src = src;
    });
    if (note) {
      note.innerHTML = 'Showing the <strong></strong> pack — the other size is greyed out.';
      note.querySelector('strong').textContent = item.dataset.label;
    }
    if (opts && opts.focus) {
      var opt = options.filter(function (o) { return o.dataset.pack === id; })[0];
      if (opt) opt.focus();
    }
    if (opts && opts.hash && items.length > 1) {
      try { history.replaceState(null, '', '#pack-' + id); } catch (err) {}
    }
  }

  items.forEach(function (item) {
    item.addEventListener('click', function () { selectPack(item.dataset.pack, { hash: true }); });
  });
  options.forEach(function (opt, i) {
    opt.addEventListener('click', function () { selectPack(opt.dataset.pack, { hash: true }); });
    opt.addEventListener('keydown', function (e) {
      var step = { ArrowRight: 1, ArrowDown: 1, ArrowLeft: -1, ArrowUp: -1 }[e.key];
      if (!step) return;
      e.preventDefault();
      var next = options[(i + step + options.length) % options.length];
      selectPack(next.dataset.pack, { hash: true, focus: true });
    });
  });

  // links such as /products/haldi-powder/#pack-50g open that size
  var m = /^#pack-([a-z0-9]+)$/.exec(location.hash);
  if (m && itemFor(m[1])) selectPack(m[1]);

  /* ---------- front / back ---------- */
  viewBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var view = btn.dataset.viewBtn;
      if (lineup) lineup.dataset.view = view;
      viewBtns.forEach(function (b) {
        var on = b === btn;
        b.classList.toggle('is-active', on);
        b.setAttribute('aria-pressed', String(on));
      });
    });
  });

  /* ---------- tilt ---------- */
  var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (stage && finePointer && !reduceMotion) {
    stage.addEventListener('pointermove', function (e) {
      var r = stage.getBoundingClientRect();
      var px = (e.clientX - r.left) / r.width - 0.5;
      var py = (e.clientY - r.top) / r.height - 0.5;
      stage.style.setProperty('--ry', (px * 12).toFixed(2) + 'deg');
      stage.style.setProperty('--rx', (py * -9).toFixed(2) + 'deg');
    });
    stage.addEventListener('pointerleave', function () {
      stage.style.setProperty('--ry', '0deg');
      stage.style.setProperty('--rx', '0deg');
    });
  }
})();
