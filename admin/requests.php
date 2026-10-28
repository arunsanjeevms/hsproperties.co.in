<?php
/**
 * HS Properties — Seller Mandates Management.
 */

declare(strict_types=1);

$activePage = 'seller_requests';
$adminTitle = 'Seller Mandates';

require_once __DIR__ . '/../backend/bootstrap.php';
require_once __DIR__ . '/../backend/models/SellerRequest.php';
require_once __DIR__ . '/../backend/models/Document.php';
require_once __DIR__ . '/../backend/controllers/AdminController.php';

$model = new SellerRequest();
$docModel = new Document();
$adminCtrl = new AdminController();

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $requestId = (int)$_POST['id'];
    $status    = $_POST['status'];
    $notes     = trim($_POST['admin_notes'] ?? '');
    $adminCtrl->updateSellerStatus($requestId, $status, $notes);
    Session::flash('success', "Mandate #{$requestId} status updated to {$status}.");
    redirect(url('admin/requests.php?id=' . $requestId));
}

// Single Request Detail View
$selectedId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$detail = $selectedId ? $model->find($selectedId) : null;
$documents = $selectedId ? $model->getDocuments($selectedId) : [];

// List View
$statusFilter = $_GET['status'] ?? '';
$search       = $_GET['search'] ?? '';
$page         = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$list = $model->getAll([
    'status' => $statusFilter,
    'search' => $search,
], $page, 20);

include __DIR__ . '/header.php';
?>

<?php if ($detail): ?>
  <!-- Single Request Detail View -->
  <div style="margin-bottom: 20px;">
    <a href="<?= e(url('admin/requests.php')) ?>" class="btn-admin btn-admin--sm btn-admin--secondary">&larr; Back to all mandates</a>
  </div>

  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <div class="admin-card">
      <div class="admin-card__header">
        <div>
          <h2 class="admin-card__title">Mandate <?= e($detail['reference']) ?></h2>
          <span style="font-size: 0.8rem; color: #888;">Submitted on <?= e(date('d M Y, H:i', strtotime($detail['created_at']))) ?></span>
        </div>
        <span class="badge badge--<?= e($detail['status']) ?>"><?= e(str_replace('_', ' ', $detail['status'])) ?></span>
      </div>

      <div style="padding: 24px;">
        <h3 style="font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--c-secondary, #8d5629); margin-top: 0;">1. Property Profile</h3>
        <table class="admin-table" style="margin-bottom: 24px;">
          <tr><th style="width: 35%;">Property Type</th><td><?= e(ucfirst($detail['property_type'] ?? '—')) ?></td></tr>
          <tr><th>Property Status</th><td><?= e(ucfirst($detail['property_status'] ?? '—')) ?></td></tr>
          <tr><th>Location</th><td><?= e($detail['locality'] ? $detail['locality'] . ', ' . $detail['city'] : $detail['city']) ?> (PIN: <?= e($detail['pincode'] ?? '—') ?>)</td></tr>
          <tr><th>Full Address</th><td><?= e($detail['address'] ?? '—') ?></td></tr>
          <tr><th>Extent / Size</th><td><?= e($detail['extent'] ? $detail['extent'] . ' ' . ($detail['measurement_unit'] ?? '') : '—') ?></td></tr>
          <tr><th>Bedrooms</th><td><?= e($detail['bedrooms'] ? $detail['bedrooms'] . ' BHK' : '—') ?></td></tr>
          <tr><th>Ownership</th><td><?= e(ucfirst($detail['ownership'] ?? '—')) ?></td></tr>
          <tr><th>Physical Condition</th><td><?= e(ucfirst($detail['condition_status'] ?? '—')) ?></td></tr>
        </table>

        <h3 style="font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--c-secondary, #8d5629);">2. Advisory & Valuation Parameters</h3>
        <table class="admin-table" style="margin-bottom: 24px;">
          <tr><th style="width: 35%;">Indicative Price</th><td><?= e($detail['target_price'] ? '₹ ' . number_format((float)$detail['target_price']) : 'Unstated / Discretionary') ?></td></tr>
          <tr><th>Timeline Target</th><td><?= e(ucfirst($detail['timeline'] ?? '—')) ?></td></tr>
          <tr><th>Mandate Structure</th><td><?= e(ucfirst($detail['mandate_structure'] ?? '—')) ?></td></tr>
        </table>

        <h3 style="font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--c-secondary, #8d5629);">3. Deposited Documentation</h3>
        <?php if (empty($documents)): ?>
          <p style="font-size: 0.85rem; color: #888;">No uploaded documents deposited.</p>
        <?php else: ?>
          <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px;">
            <?php foreach ($documents as $doc): ?>
              <li style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; background: #faf6f1; border-radius: 6px; font-size: 0.85rem;">
                <div>
                  <strong><?= e($doc['original_name'] ?? $doc['file_name'] ?? 'Document') ?></strong>
                  <span style="color: #888; font-size: 0.75rem; margin-left: 8px;"><?= e(number_format(($doc['file_size'] ?? 0) / 1024, 1)) ?> KB</span>
                </div>
                <a href="<?= e(url($doc['file_path'])) ?>" target="_blank" class="btn-admin btn-admin--sm btn-admin--secondary">Download</a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>

    <!-- Status Management Form -->
    <div>
      <div class="admin-card">
        <div class="admin-card__header">
          <h2 class="admin-card__title">Advisory Action</h2>
        </div>
        <div style="padding: 24px;">
          <form method="post" action="<?= e(url('admin/requests.php')) ?>">
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="id" value="<?= e((string)$detail['id']) ?>">

            <div class="admin-form-group">
              <label for="status">Review Status</label>
              <select name="status" id="status" class="admin-select">
                <option value="draft" <?= $detail['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="submitted" <?= $detail['status'] === 'submitted' ? 'selected' : '' ?>>Submitted (New)</option>
                <option value="under_review" <?= $detail['status'] === 'under_review' ? 'selected' : '' ?>>Under Review</option>
                <option value="verified" <?= $detail['status'] === 'verified' ? 'selected' : '' ?>>Verified</option>
                <option value="approved" <?= $detail['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                <option value="rejected" <?= $detail['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                <option value="closed" <?= $detail['status'] === 'closed' ? 'selected' : '' ?>>Closed Mandate</option>
              </select>
            </div>

            <div class="admin-form-group">
              <label for="admin_notes">Internal Advisory Notes</label>
              <textarea name="admin_notes" id="admin_notes" rows="6" class="admin-textarea" placeholder="Record verification checks, title diligence notes, or communications..."><?= e($detail['admin_notes'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn-admin btn-admin--primary" style="width: 100%; justify-content: center;">
              Update Mandate Status
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

<?php else: ?>
  <!-- Mandates Table View -->
  <div class="admin-card">
    <div class="admin-card__header">
      <h2 class="admin-card__title">All Seller Mandates (<?= e((string)$list['total']) ?>)</h2>

      <!-- Filter bar -->
      <form method="get" action="<?= e(url('admin/requests.php')) ?>" style="display: flex; gap: 10px;">
        <input type="text" name="search" class="admin-input" style="width: 200px; padding: 6px 10px;" placeholder="Search reference, city..." value="<?= e($search) ?>">
        <select name="status" class="admin-select" style="width: 150px; padding: 6px 10px;">
          <option value="">All Statuses</option>
          <option value="submitted" <?= $statusFilter === 'submitted' ? 'selected' : '' ?>>Submitted</option>
          <option value="under_review" <?= $statusFilter === 'under_review' ? 'selected' : '' ?>>Under Review</option>
          <option value="verified" <?= $statusFilter === 'verified' ? 'selected' : '' ?>>Verified</option>
          <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>Approved</option>
          <option value="closed" <?= $statusFilter === 'closed' ? 'selected' : '' ?>>Closed</option>
        </select>
        <button type="submit" class="btn-admin btn-admin--secondary btn-admin--sm">Filter</button>
      </form>
    </div>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Reference</th>
          <th>Location</th>
          <th>Archetype</th>
          <th>Extent</th>
          <th>Asking Price</th>
          <th>Status</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($list['data'])): ?>
          <tr><td colspan="8" style="text-align: center; color: #888;">No seller requests matched your criteria.</td></tr>
        <?php else: ?>
          <?php foreach ($list['data'] as $row): ?>
            <tr>
              <td><strong><?= e($row['reference']) ?></strong></td>
              <td><?= e($row['locality'] ? $row['locality'] . ', ' . $row['city'] : ($row['city'] ?? '—')) ?></td>
              <td><?= e(ucfirst($row['property_type'] ?? '—')) ?></td>
              <td><?= e($row['extent'] ? $row['extent'] . ' ' . ($row['measurement_unit'] ?? '') : '—') ?></td>
              <td><?= e($row['target_price'] ? '₹ ' . number_format((float)$row['target_price']) : 'Discretionary') ?></td>
              <td><span class="badge badge--<?= e($row['status']) ?>"><?= e(str_replace('_', ' ', $row['status'])) ?></span></td>
              <td><?= e(date('d M Y', strtotime($row['created_at']))) ?></td>
              <td>
                <a href="<?= e(url('admin/requests.php?id=' . $row['id'])) ?>" class="btn-admin btn-admin--sm btn-admin--secondary">Review</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
