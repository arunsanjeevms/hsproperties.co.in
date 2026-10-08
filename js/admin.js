/* ==========================================================================
   HS Properties — Operations Console behaviour.

   Progressive enhancement only: without JavaScript the rail is a static
   sidebar, the filter row submits with its Apply button, and every row is
   reachable through its own VIEW link.
   ========================================================================== */

(function () {
  'use strict';

  /* ----------------------------------------------------------------------
     Sidebar drawer (below 1024px)
     ---------------------------------------------------------------------- */
  var rail = document.getElementById('console-rail');
  var toggle = document.getElementById('console-rail-toggle');
  var scrim = document.getElementById('console-scrim');

  function setDrawer(open) {
    rail.classList.toggle('is-open', open);
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');

    if (scrim) {
      scrim.hidden = !open;
    }
  }

  if (rail && toggle) {
    toggle.addEventListener('click', function () {
      setDrawer(!rail.classList.contains('is-open'));
    });

    if (scrim) {
      scrim.addEventListener('click', function () {
        setDrawer(false);
      });
    }
  }

  /* ----------------------------------------------------------------------
     Filter row: submit on change so the selects read as live filters.
     ---------------------------------------------------------------------- */
  var filters = document.getElementById('request-filters');

  if (filters) {
    filters.addEventListener('change', function (event) {
      if (event.target.tagName === 'SELECT') {
        filters.submit();
      }
    });
  }

  /* ----------------------------------------------------------------------
     "Change Status" menu — one instance, positioned under the pressed
     control, plus the confirmation toast the blueprint shows on selection.
     ---------------------------------------------------------------------- */
  var menu = document.getElementById('status-menu');
  var toast = document.getElementById('status-toast');
  var opener = null;

  function closeMenu() {
    if (!menu || menu.hidden) {
      return;
    }

    menu.hidden = true;

    if (opener) {
      opener.setAttribute('aria-expanded', 'false');
      opener = null;
    }
  }

  function openMenu(button) {
    var box = button.getBoundingClientRect();

    menu.hidden = false;
    menu.style.top = window.scrollY + box.bottom + 6 + 'px';
    menu.style.left =
      Math.max(8, window.scrollX + box.right - menu.offsetWidth) + 'px';

    button.setAttribute('aria-expanded', 'true');
    opener = button;
  }

  if (menu) {
    document.addEventListener('click', function (event) {
      var button = event.target.closest('[data-status-menu]');

      if (button) {
        event.preventDefault();

        if (opener === button) {
          closeMenu();
        } else {
          closeMenu();
          openMenu(button);
        }

        return;
      }

      // A status choice: confirm, then dismiss.
      if (event.target.closest('#status-menu .assign__option')) {
        closeMenu();

        if (toast) {
          toast.hidden = false;
          window.setTimeout(function () {
            toast.hidden = true;
          }, 2600);
        }

        return;
      }

      if (!event.target.closest('#status-menu')) {
        closeMenu();
      }
    });

    document.addEventListener('keydown', function (event) {
      if (event.key !== 'Escape') {
        return;
      }

      if (menu && !menu.hidden) {
        var last = opener;
        closeMenu();

        if (last) {
          last.focus();
        }
      } else if (rail && rail.classList.contains('is-open')) {
        setDrawer(false);
        toggle.focus();
      }
    });
  }

  /* ----------------------------------------------------------------------
     Row affordance: clicking a row opens it; its VIEW link stays the
     keyboard-reachable control.
     ---------------------------------------------------------------------- */
  Array.prototype.forEach.call(
    document.querySelectorAll('[data-href]'),
    function (row) {
      row.addEventListener('click', function (event) {
        if (event.target.closest('a, button')) {
          return;
        }

        if (window.getSelection && String(window.getSelection())) {
          return;
        }

        window.location.href = row.getAttribute('data-href');
      });
    }
  );

  /* ------------------------------------------------------------------------
     Manual status change.

     The form works on its own; this only spares the operator a button that
     does nothing. Save stays out of the way until the choice differs from what
     is stored, and a row's click-to-open is suppressed while the select is in
     use so changing a status never navigates away mid-choice.
     ---------------------------------------------------------------------- */
  Array.prototype.forEach.call(
    document.querySelectorAll('[data-status-select]'),
    function (select) {
      var form = select.closest('.status-change');

      if (!form) {
        return;
      }

      // The placeholder is the empty value, so "something was chosen" and
      // "there is a change to save" are the same question.
      select.addEventListener('change', function () {
        form.classList.toggle('is-dirty', select.value !== '');
      });

      // Inside a clickable row, the select must not open the property.
      select.addEventListener('click', function (event) {
        event.stopPropagation();
      });
    }
  );

})();
