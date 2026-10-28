<?php
/**
 * Site footer + deferred scripts.
 */
?>
  <footer class="site-footer">
    <div class="container">

      <div class="site-footer__grid">
        <div>
          <div class="brand">
            <img
              class="brand__mark"
              src="<?= e(url('assets/images/logo.svg')) ?>"
              alt=""
              width="36" height="60">
            <span class="brand__name"><?= e(SITE_NAME) ?></span>
          </div>

          <p class="site-footer__about">
            Bespoke private real estate stewardship across premier metropolitan
            and global destinations. Discreet acquisitions, sovereign advisory,
            and structured liquidations.
          </p>

          <span class="badge badge--retainer">
            <?= icon('verified', 'icon--sm') ?>
            Certified Private Advisory Retainer
          </span>
        </div>

        <?php foreach (footer_groups() as $group): ?>
          <nav aria-label="<?= e($group['heading']) ?>">
            <h2 class="site-footer__heading"><?= e($group['heading']) ?></h2>
            <div class="site-footer__list">
              <?php foreach ($group['links'] as $link): ?>
                <a href="<?= e(isset($link['route']) ? route($link['route']) : '#') ?>">
                  <?= e($link['label']) ?>
                </a>
              <?php endforeach; ?>
            </div>
          </nav>
        <?php endforeach; ?>

        <div>
          <h2 class="site-footer__heading">Private Desk</h2>
          <p class="site-footer__desk">
            Direct encrypted liaison for accredited principals and verified
            trustees.
          </p>
          <address class="site-footer__desk">
            <a class="site-footer__email" href="mailto:desk@hsproperties.com">desk@hsproperties.com</a>
            <a class="site-footer__phone" href="tel:+442079460892">+44 (0) 20 7946 0892</a>
          </address>
          <p style="margin-top: var(--s-md);">
            <a class="link-action" href="#contact">
              Schedule Private Consultation
              <?= icon('arrow-right', 'icon--arrow icon--sm') ?>
            </a>
          </p>
        </div>
      </div>

      <div class="site-footer__bottom">
        <p>&copy; <?= date('Y') ?> HS Properties Advisory Ltd. All sovereign rights reserved. Confidential representation.</p>
        <nav class="site-footer__legal" aria-label="Legal">
          <a href="#">Privacy Charter</a>
          <a href="#">Terms of Mandate</a>
          <a href="#">Regulatory Disclosures</a>
        </nav>
      </div>

    </div>
  </footer>

  <?php /* Scripts are deferred so they never block first paint. */ ?>
  <script src="<?= e(url('js/navigation.js')) ?>" defer></script>
  <script src="<?= e(url('js/interactions.js')) ?>" defer></script>
  <script src="<?= e(url('js/main.js')) ?>" defer></script>
</body>
</html>
