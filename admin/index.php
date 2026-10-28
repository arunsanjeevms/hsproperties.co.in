<?php
/**
 * HS Properties — Master Advisory Dashboard.
 */

declare(strict_types=1);

$activePage = 'dashboard';
$adminTitle = 'Executive Overview';

require_once __DIR__ . '/../backend/bootstrap.php';
require_once __DIR__ . '/../backend/controllers/AdminController.php';

$controller = new AdminController();
$stats = $controller->getDashboardStats();

include __DIR__ . '/header.php';
?>

<!-- Metric Cards -->
<div class="admin-stats-grid">
  <div class="admin-stat-card">
    <span class="admin-stat-card__label">Seller Mandates</span>
    <p class="admin-stat-card__value"><?= e((string)$stats['total_seller_requests']) ?></p>
    <span class="admin-stat-card__hint"><?= e((string)$stats['pending_seller_review']) ?> Pending Review</span>
  </div>

  <div class="admin-stat-card">
    <span class="admin-stat-card__label">Buyer Mandates</span>
    <p class="admin-stat-card__value"><?= e((string)$stats['total_buyer_requests']) ?></p>
    <span class="admin-stat-card__hint">Discreet Inquiries</span>
  </div>

  <div class="admin-stat-card">
    <span class="admin-stat-card__label">Portfolio Properties</span>
    <p class="admin-stat-card__value"><?= e((string)$stats['total_properties']) ?></p>
    <span class="admin-stat-card__hint">Live on Presentation</span>
  </div>

  <div class="admin-stat-card">
    <span class="admin-stat-card__label">Client Messages</span>
    <p class="admin-stat-card__value"><?= e((string)$stats['unread_messages']) ?></p>
    <span class="admin-stat-card__hint">Unread Inquiries</span>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">

  <!-- Left: Recent Submissions -->
  <div>
    <!-- Recent Seller Mandates -->
    <div class="admin-card">
      <div class="admin-card__header">
        <h2 class="admin-card__title">Recent Seller Mandates</h2>
        <a href="<?= e(url('admin/requests.php')) ?>" class="btn-admin btn-admin--sm btn-admin--secondary">View All</a>
      </div>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Reference</th>
            <th>Location</th>
            <th>Type</th>
            <th>Status</th>
            <th>Submitted</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($stats['recent_sellers'])): ?>
            <tr>
              <td colspan="6" style="text-align: center; color: #888;">No seller mandates registered yet.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($stats['recent_sellers'] as $seller): ?>
              <tr>
                <td><strong><?= e($seller['reference']) ?></strong></td>
                <td><?= e($seller['locality'] ? $seller['locality'] . ', ' . $seller['city'] : $seller['city']) ?></td>
                <td><?= e(ucfirst($seller['property_type'] ?? '—')) ?></td>
                <td><span class="badge badge--<?= e($seller['status']) ?>"><?= e(str_replace('_', ' ', $seller['status'])) ?></span></td>
                <td><?= e(date('d M Y', strtotime($seller['created_at']))) ?></td>
                <td>
                  <a href="<?= e(url('admin/requests.php?id=' . $seller['id'])) ?>" class="btn-admin btn-admin--sm btn-admin--secondary">Review</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Recent Buyer Mandates -->
    <div class="admin-card">
      <div class="admin-card__header">
        <h2 class="admin-card__title">Recent Buyer Inquiries</h2>
        <a href="<?= e(url('admin/buyer-requests.php')) ?>" class="btn-admin btn-admin--sm btn-admin--secondary">View All</a>
      </div>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Reference</th>
            <th>Client</th>
            <th>City</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($stats['recent_buyers'])): ?>
            <tr>
              <td colspan="6" style="text-align: center; color: #888;">No buyer inquiries logged yet.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($stats['recent_buyers'] as $buyer): ?>
              <tr>
                <td><strong><?= e($buyer['reference']) ?></strong></td>
                <td><?= e($buyer['full_name']) ?></td>
                <td><?= e($buyer['city'] ?: '—') ?></td>
                <td><span class="badge badge--<?= e($buyer['status']) ?>"><?= e($buyer['status']) ?></span></td>
                <td><?= e(date('d M Y', strtotime($buyer['created_at']))) ?></td>
                <td>
                  <a href="<?= e(url('admin/buyer-requests.php?id=' . $buyer['id'])) ?>" class="btn-admin btn-admin--sm btn-admin--secondary">Details</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Right: Audit Trail -->
  <div>
    <div class="admin-card">
      <div class="admin-card__header">
        <h2 class="admin-card__title">Audit & Activity Log</h2>
      </div>
      <div style="padding: 16px 20px;">
        <?php if (empty($stats['recent_activity'])): ?>
          <p style="font-size: 0.85rem; color: #888;">No recent activity recorded.</p>
        <?php else: ?>
          <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 14px;">
            <?php foreach ($stats['recent_activity'] as $act): ?>
              <li style="border-bottom: 1px solid #f2ebe2; padding-bottom: 10px; font-size: 0.82rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                  <strong style="color: var(--c-secondary, #8d5629);"><?= e(str_replace('_', ' ', $act['action'])) ?></strong>
                  <span style="color: #999; font-size: 0.75rem;"><?= e(date('H:i, d M', strtotime($act['created_at']))) ?></span>
                </div>
                <div style="color: #444;"><?= e($act['details'] ?? 'Activity recorded') ?></div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div>

<?php include __DIR__ . '/footer.php'; ?>
