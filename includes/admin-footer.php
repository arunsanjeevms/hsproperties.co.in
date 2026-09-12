<?php
/**
 * Admin console shell — console footer and scripts.
 *
 * The footer line is the blueprint's own: fiduciary protocol version and the
 * encrypted-session notice, with the three charter links.
 */

$consoleFooterNote = $consoleFooterNote
  ?? 'HS Properties Private Operations Console • Fiduciary Protocol V4.8 • Encrypted Session';
?>
      </main>

      <footer class="console-foot">
        <p class="console-foot__note"><?= e($consoleFooterNote) ?></p>
        <p class="console-foot__links">
          <a href="#">Security Charter</a>
          <span aria-hidden="true">&bull;</span>
          <a href="#">Compliance Audit</a>
          <span aria-hidden="true">&bull;</span>
          <a href="#">Vault Access Key</a>
        </p>
      </footer>

  </div>

  <script src="<?= e(url('js/admin.js')) ?>" defer></script>
</body>
</html>
