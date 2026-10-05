/** Product detail — pack view switcher and mouse-parallax tilt. */
(function () {
  'use strict';

  var gallery = document.querySelector('[data-gallery]');
  if (!gallery) return;
  var stage = gallery.querySelector('[data-tilt-stage]');
  var images = gallery.querySelectorAll('[data-gallery-img]');
  var thumbs = gallery.querySelectorAll('[data-gallery-thumb]');

  thumbs.forEach(function (thumb) {
    thumb.addEventListener('click', function () {
      var i = thumb.dataset.galleryThumb;
      images.forEach(function (img) { img.classList.toggle('is-active', img.dataset.galleryImg === i); });
      thumbs.forEach(function (t) {
        var on = t === thumb;
        t.classList.toggle('is-active', on);
        t.setAttribute('aria-pressed', String(on));
      });
    });
  });

  var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (stage && finePointer && !reduceMotion) {
    stage.addEventListener('pointermove', function (e) {
      var r = stage.getBoundingClientRect();
      var px = (e.clientX - r.left) / r.width - 0.5;
      var py = (e.clientY - r.top) / r.height - 0.5;
      stage.style.setProperty('--ry', (px * 16).toFixed(2) + 'deg');
      stage.style.setProperty('--rx', (py * -12).toFixed(2) + 'deg');
    });
    stage.addEventListener('pointerleave', function () {
      stage.style.setProperty('--ry', '0deg');
      stage.style.setProperty('--rx', '0deg');
    });
  }
})();
