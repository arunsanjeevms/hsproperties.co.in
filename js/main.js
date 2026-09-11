/**
 * HS Properties — Entry point
 *
 * navigation.js and interactions.js are self-contained and load first. This
 * file carries only cross-cutting concerns, and is the place to register any
 * page-level behaviour added later.
 */
(function () {
  'use strict';

  /*
   * Note: the `has-js` class is set by a synchronous script in the document
   * head, not here — it has to land before first paint so that scroll-reveal
   * elements never flash.
   */

  /**
   * Inline SVG sprite.
   *
   * `<use href="sprite.svg#id">` fails on file:// in several browsers and
   * costs an extra request otherwise. Fetching the sprite once and injecting
   * it into the document makes every reference resolve locally.
   */
  var spriteSource = document.querySelector('use[href*="sprite.svg"]');

  if (spriteSource) {
    var spriteUrl = spriteSource.getAttribute('href').split('#')[0];

    fetch(spriteUrl)
      .then(function (response) {
        return response.ok ? response.text() : Promise.reject(response.status);
      })
      .then(function (markup) {
        var holder = document.createElement('div');
        holder.setAttribute('aria-hidden', 'true');
        holder.style.display = 'none';
        holder.innerHTML = markup;
        document.body.insertBefore(holder, document.body.firstChild);

        // Re-point every reference at the now-inlined symbols.
        var refs = document.querySelectorAll('use[href*="sprite.svg"]');
        Array.prototype.forEach.call(refs, function (use) {
          var id = use.getAttribute('href').split('#')[1];
          use.setAttribute('href', '#' + id);
        });
      })
      .catch(function () {
        /* External references still work over http(s); nothing to do. */
      });
  }
})();
