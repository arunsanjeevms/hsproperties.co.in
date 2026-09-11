/**
 * HS Properties — Interactions
 *
 * Scroll-triggered section reveals and smooth in-page navigation.
 * Both are disabled when the user prefers reduced motion.
 */
(function () {
  'use strict';

  var prefersReducedMotion = window.matchMedia(
    '(prefers-reduced-motion: reduce)'
  ).matches;

  /* ----------------------------------------------------------------------
     Scroll reveal

     Elements marked .reveal fade and rise once as they enter the viewport.
     Each is unobserved after firing, so this costs nothing after first pass.
     ---------------------------------------------------------------------- */
  var targets = document.querySelectorAll('.reveal');

  if (!targets.length) {
    return;
  }

  // No IntersectionObserver, or motion is unwanted: show everything at once.
  if (prefersReducedMotion || !('IntersectionObserver' in window)) {
    Array.prototype.forEach.call(targets, function (el) {
      el.classList.add('is-visible');
    });
  } else {
    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      },
      {
        // Fire slightly before the element reaches the fold so the motion
        // has finished by the time it is properly in view.
        rootMargin: '0px 0px -12% 0px',
        threshold: 0.08,
      }
    );

    Array.prototype.forEach.call(targets, function (el) {
      observer.observe(el);
    });
  }

  /* ----------------------------------------------------------------------
     Smooth anchor navigation

     CSS `scroll-behavior: smooth` handles the scroll; this handler exists to
     move keyboard focus to the destination, which browsers do not do for
     programmatic scrolls. Without it, tabbing after a jump resumes at the top.
     ---------------------------------------------------------------------- */
  document.addEventListener('click', function (event) {
    var link = event.target.closest('a[href^="#"]');

    if (!link) {
      return;
    }

    var id = link.getAttribute('href');

    if (!id || id === '#') {
      return;
    }

    var target = document.querySelector(id);

    if (!target) {
      return;
    }

    event.preventDefault();

    target.scrollIntoView({
      behavior: prefersReducedMotion ? 'auto' : 'smooth',
      block: 'start',
    });

    // Make the section focusable just long enough to receive focus.
    if (!target.hasAttribute('tabindex')) {
      target.setAttribute('tabindex', '-1');
      target.addEventListener(
        'blur',
        function () {
          target.removeAttribute('tabindex');
        },
        { once: true }
      );
    }

    target.focus({ preventScroll: true });

    // Keep the URL shareable without triggering a second jump.
    if (window.history && window.history.pushState) {
      window.history.pushState(null, '', id);
    }
  });
})();
