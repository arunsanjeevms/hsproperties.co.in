/**
 * HS Properties — Intake enhancements.
 *
 * Everything here is progressive enhancement. Selection states are handled by
 * CSS `:has(:checked)` against real inputs, so the forms are fully usable —
 * selectable, keyboard-navigable and submittable — with this file absent.
 */
(function () {
  'use strict';

  /* ----------------------------------------------------------------------
     File upload: drag-and-drop plus a live list of chosen files.

     The native file input still works on its own; this adds the drop target
     and renders what the user picked.
     ---------------------------------------------------------------------- */
  var dropzone = document.getElementById('dropzone');
  var fileInput = document.getElementById('document-files');
  var fileList = document.getElementById('file-list');

  if (dropzone && fileInput && fileList) {
    ['dragenter', 'dragover'].forEach(function (type) {
      dropzone.addEventListener(type, function (event) {
        event.preventDefault();
        dropzone.classList.add('is-dragover');
      });
    });

    ['dragleave', 'drop'].forEach(function (type) {
      dropzone.addEventListener(type, function (event) {
        event.preventDefault();
        dropzone.classList.remove('is-dragover');
      });
    });

    dropzone.addEventListener('drop', function (event) {
      var dropped = event.dataTransfer && event.dataTransfer.files;

      if (!dropped || !dropped.length) {
        return;
      }

      // DataTransfer lets us push dropped files into the real input, so the
      // form still submits them normally.
      if (typeof DataTransfer === 'function') {
        var bucket = new DataTransfer();

        Array.prototype.forEach.call(fileInput.files, function (f) {
          bucket.items.add(f);
        });
        Array.prototype.forEach.call(dropped, function (f) {
          bucket.items.add(f);
        });

        fileInput.files = bucket.files;
      }

      renderChosen(dropped);
    });

    fileInput.addEventListener('change', function () {
      renderChosen(fileInput.files);
    });

    /** Append a row for each newly chosen file. */
    function renderChosen(files) {
      Array.prototype.forEach.call(files, function (file) {
        fileList.appendChild(buildRow(file));
      });
    }

    function buildRow(file) {
      var row = document.createElement('li');
      row.className = 'file';

      var ext = (file.name.split('.').pop() || 'FILE').toUpperCase();

      var badge = document.createElement('span');
      badge.className = 'file__badge';
      badge.setAttribute('aria-hidden', 'true');
      badge.textContent = ext.slice(0, 4);

      var body = document.createElement('span');
      body.className = 'file__body';

      var name = document.createElement('span');
      name.className = 'file__name';
      name.textContent = file.name;

      var meta = document.createElement('span');
      meta.className = 'file__meta';

      var size = document.createElement('span');
      size.textContent = formatSize(file.size);

      var state = document.createElement('span');
      state.className = 'file__state';
      state.textContent = 'Ready to upload';

      meta.appendChild(size);
      meta.appendChild(document.createTextNode(' • '));
      meta.appendChild(state);

      body.appendChild(name);
      body.appendChild(meta);

      var actions = document.createElement('span');
      actions.className = 'file__actions';

      var remove = document.createElement('button');
      remove.className = 'file__remove';
      remove.type = 'button';
      remove.innerHTML =
        '<svg class="icon icon--sm" aria-hidden="true"><use href="#i-trash"></use></svg>';

      var label = document.createElement('span');
      label.className = 'visually-hidden';
      label.textContent = 'Remove ' + file.name;
      remove.appendChild(label);

      actions.appendChild(remove);

      row.appendChild(badge);
      row.appendChild(body);
      row.appendChild(actions);

      return row;
    }

    function formatSize(bytes) {
      if (!bytes) {
        return '0 KB';
      }
      var mb = bytes / (1024 * 1024);
      return mb >= 1 ? mb.toFixed(1) + ' MB' : Math.max(1, Math.round(bytes / 1024)) + ' KB';
    }
  }

  /* ----------------------------------------------------------------------
     Removing a staged document row.
     Delegated, so it covers rows added after load too.
     ---------------------------------------------------------------------- */
  document.addEventListener('click', function (event) {
    var remove = event.target.closest('.file__remove');

    if (!remove) {
      return;
    }

    var row = remove.closest('.file');

    if (row) {
      row.remove();
    }
  });

  /* ----------------------------------------------------------------------
     Step 03: swap to the confirmation view without a reload.

     The form's action already points at ?state=submitted, so if anything here
     fails the normal submission still lands on the confirmation.
     ---------------------------------------------------------------------- */
  var reviewForm = document.getElementById('intake-form');
  var confirmation = document.getElementById('intake-confirmation');

  if (reviewForm && confirmation) {
    reviewForm.addEventListener('submit', function (event) {
      // Let the browser surface its own validation for the declaration.
      if (!reviewForm.checkValidity()) {
        return;
      }

      event.preventDefault();

      reviewForm.hidden = true;
      confirmation.hidden = false;

      // Keep the URL honest so a refresh or a shared link shows the same view.
      if (window.history && window.history.pushState) {
        window.history.pushState({}, '', '?state=submitted');
      }

      // Move focus to the confirmation so the change is announced.
      confirmation.setAttribute('tabindex', '-1');
      confirmation.focus({ preventScroll: true });
      confirmation.scrollIntoView({
        behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches
          ? 'auto'
          : 'smooth',
        block: 'start',
      });
    });
  }
})();
