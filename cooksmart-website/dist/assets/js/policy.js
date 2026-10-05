/** Company Policy — scroll-spy table of contents with reading progress. */
(function () {
  'use strict';

  var toc = document.querySelector('[data-toc]');
  if (!toc) return;
  var links = Array.prototype.slice.call(toc.querySelectorAll('[data-toc-link]'));
  var bar = toc.querySelector('[data-toc-progress]');
  var list = toc.querySelector('ol');

  function setActive(id) {
    links.forEach(function (a) {
      var on = a.dataset.tocLink === id;
      a.classList.toggle('is-active', on);
      if (on) {
        a.setAttribute('aria-current', 'true');
        if (list && list.scrollWidth > list.clientWidth) {
          list.scrollTo({ left: a.offsetLeft - 12, behavior: 'smooth' });
        }
      } else {
        a.removeAttribute('aria-current');
      }
    });
  }

  var sections = Array.prototype.slice.call(document.querySelectorAll('[data-toc-section]'));
  if ('IntersectionObserver' in window) {
    var visible = {};
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) { visible[en.target.id] = en.isIntersecting; });
      var current = sections.filter(function (s) { return visible[s.id]; })[0];
      if (current) setActive(current.id);
    }, { rootMargin: '-35% 0px -55% 0px' });
    sections.forEach(function (s) { io.observe(s); });
  }
  if (sections[0]) setActive(sections[0].id);

  var content = document.querySelector('.policy__content');
  function progress() {
    if (!bar || !content) return;
    var r = content.getBoundingClientRect();
    var total = r.height - window.innerHeight * 0.5;
    var p = Math.min(1, Math.max(0, (window.innerHeight * 0.5 - r.top) / total));
    bar.style.transform = 'scaleX(' + p.toFixed(3) + ')';
  }
  var ticking = false;
  window.addEventListener('scroll', function () {
    if (!ticking) { ticking = true; requestAnimationFrame(function () { progress(); ticking = false; }); }
  }, { passive: true });
  progress();
})();
