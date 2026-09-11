<?php
/**
 * Primary navigation — desktop bar and the mobile drawer share one source
 * (nav_items()) so the two can never drift apart.
 *
 * Expects: $currentPage (string) from the including page.
 */

$currentPage = $currentPage ?? 'home';
$items = nav_items();
?>
<nav class="nav" aria-label="Primary">
  <?php foreach ($items as $item): ?>
    <a
      class="nav__link"
      href="<?= e($item['href']) ?>"
      <?= $currentPage === $item['key'] ? 'aria-current="page"' : '' ?>
    ><?= e($item['label']) ?></a>
  <?php endforeach; ?>
</nav>
