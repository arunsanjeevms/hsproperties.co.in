/**
 * HS Properties — Navigation
 *
 * Owns the fixed header's scrolled state and the mobile drawer, including its
 * focus and keyboard behaviour. No dependencies.
 */
(function () {
  'use strict';

  var header = document.getElementById('site-header');
  var toggle = document.getElementById('nav-toggle');
  var drawer = document.getElementById('nav-drawer');

  /* ----------------------------------------------------------------------
     Header: add a shadow once the page has scrolled off the top.
     Reads are batched into a rAF so scrolling stays smooth.
     ---------------------------------------------------------------------- */
  if (header) {
    var ticking = false;

    var syncHeader = function () {
      header.classList.toggle('is-scrolled', window.scrollY > 24);
      ticking = false;
    };

    window.addEventListener(
      'scroll',
      function () {
        if (!ticking) {
          ticking = true;
          window.requestAnimationFrame(syncHeader);
        }
      },
      { passive: true }
    );

    syncHeader();
  }

  /* ----------------------------------------------------------------------
     Mobile drawer
     ---------------------------------------------------------------------- */
  if (!toggle || !drawer) {
    return;
  }

  var isOpen = false;

  function setDrawer(open) {
    isOpen = open;

    // `hidden` is removed first so the open transition has something to run on.
    if (open) {
      drawer.hidden = false;
      window.requestAnimationFrame(function () {
        drawer.classList.add('is-open');
      });
    } else {
      drawer.classList.remove('is-open');
    }

    toggle.setAttribute('aria-expanded', String(open));
    toggle.setAttribute(
      'aria-label',
      open ? 'Close navigation menu' : 'Open navigation menu'
    );

    // Swap the glyph between menu and close.
    var use = toggle.querySelector('use');
    if (use) {
      var href = use.getAttribute('href') || '';
      use.setAttribute(
        'href',
        href.replace(/#i-(menu|close)$/, open ? '#i-close' : '#i-menu')
      );
    }

    // Prevent the page behind the drawer from scrolling.
    document.body.style.overflow = open ? 'hidden' : '';
  }

  // Once the closing transition finishes, take the drawer out of the a11y tree.
  drawer.addEventListener('transitionend', function (event) {
    if (event.propertyName === 'opacity' && !isOpen) {
      drawer.hidden = true;
    }
  });

  toggle.addEventListener('click', function () {
    setDrawer(!isOpen);
  });

  // Close on Escape, returning focus to the trigger.
  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && isOpen) {
      setDrawer(false);
      toggle.focus();
    }
  });

  // Close after following an in-page link.
  drawer.addEventListener('click', function (event) {
    if (event.target.closest('a')) {
      setDrawer(false);
    }
  });

  // Close if the viewport grows back to the desktop breakpoint.
  var desktop = window.matchMedia('(min-width: 1281px)');
  var onBreakpoint = function (event) {
    if (event.matches && isOpen) {
      setDrawer(false);
    }
  };

  if (typeof desktop.addEventListener === 'function') {
    desktop.addEventListener('change', onBreakpoint);
  } else if (typeof desktop.addListener === 'function') {
    desktop.addListener(onBreakpoint);
  }
})();
