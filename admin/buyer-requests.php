<?php
/**
 * HS Properties — Buyer Mandates & Inquiries Management.
 */

declare(strict_types=1);

$activePage = 'buyer_requests';
$adminTitle = 'Buyer Mandates';

require_once __DIR__ . '/../backend/bootstrap.php';
require_once __DIR__ . '/../backend/models/BuyerRequest.php';
require_once __DIR__ . '/../backend/controllers/AdminController.php';

$model = new BuyerRequest();
$adminCtrl = new AdminController();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $id     = (int)$_POST['id'];
    $status = $_POST['status'];
    $notes  = trim($_POST['admin_notes'] ?? '');
    $adminCtrl->updateBuyerStatus($id, $status, $notes);
    Session::flash('success', "Buyer inquiry #{$id} updated.");
    redirect(url('admin/buyer-requests.php?id=' . $id));
}

// Single inquiry detail
$selectedId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$detail = $selectedId ? $model->find($selectedId) : null;

// List view
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
  <!-- Detail View -->
  <div style="margin-bottom: 20px;">
    <a href="<?= e(url('admin/buyer-requests.php')) ?>" class="btn-admin btn-admin--sm btn-admin--secondary">&larr; Back to all buyer inquiries</a>
  </div>

  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <div class="admin-card">
      <div class="admin-card__header">
        <div>
          <h2 class="admin-card__title">Inquiry <?= e($detail['reference']) ?></h2>
          <span style="font-size: 0.8rem; color: #888;">Received <?= e(date('d M Y, H:i', strtotime($detail['created_at']))) ?></span>
        </div>
        <span class="badge badge--<?= e($detail['status']) ?>"><?= e($detail['status']) ?></span>
      </div>

      <div style="padding: 24px;">
        <h3 style="font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--c-secondary, #8d5629); margin-top: 0;">Buyer Profile</h3>
        <table class="admin-table" style="margin-bottom: 24px;">
          <tr><th style="width: 35%;">Client Name</th><td><strong><?= e($detail['full_name']) ?></strong></td></tr>
          <tr><th>Email Address</th><td><a href="mailto:<?= e($detail['email']) ?>"><?= e($detail['email']) ?></a></td></tr>
          <tr><th>Phone Contact</th><td><?= e($detail['phone'] ?: '—') ?></td></tr>
        </table>

        <h3 style="font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--c-secondary, #8d5629);">Acquisition Criteria</h3>
        <table class="admin-table" style="margin-bottom: 24px;">
          <tr><th style="width: 35%;">Preferred Archetype</th><td><?= e(ucfirst($detail['property_type'] ?? 'Any Luxury')) ?></td></tr>
          <tr><th>Target Geography</th><td><?= e($detail['locality'] ? $detail['locality'] . ', ' . $detail['city'] : ($detail['city'] ?? '—')) ?></td></tr>
          <tr><th>Budget Envelope</th><td><?= e($detail['budget_min'] || $detail['budget_max'] ? ($detail['budget_min'] ?? '') . ' – ' . ($detail['budget_max'] ?? '') : 'Discretionary') ?></td></tr>
          <tr><th>Bedroom Requirement</th><td><?= e($detail['bedrooms'] ? $detail['bedrooms'] . ' BHK' : '—') ?></td></tr>
          <tr><th>Timeline</th><td><?= e(ucfirst($detail['timeline'] ?? '—')) ?></td></tr>
          <tr><th>Bespoke Notes</th><td><?= nl2br(e($detail['special_requirements'] ?? '—')) ?></td></tr>
        </table>
      </div>
    </div>

    <!-- Status & Action Card -->
    <div>
      <div class="admin-card">
        <div class="admin-card__header">
          <h2 class="admin-card__title">Advisory Status</h2>
        </div>
        <div style="padding: 24px;">
          <form method="post" action="<?= e(url('admin/buyer-requests.php')) ?>">
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="id" value="<?= e((string)$detail['id']) ?>">

            <div class="admin-form-group">
              <label for="status">Status</label>
              <select name="status" id="status" class="admin-select">
                <option value="new" <?= $detail['status'] === 'new' ? 'selected' : '' ?>>New</option>
                <option value="contacted" <?= $detail['status'] === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                <option value="active" <?= $detail['status'] === 'active' ? 'selected' : '' ?>>Active Search</option>
                <option value="closed" <?= $detail['status'] === 'closed' ? 'selected' : '' ?>>Closed / Concluded</option>
              </select>
            </div>

            <div class="admin-form-group">
              <label for="admin_notes">Advisor Log</label>
              <textarea name="admin_notes" id="admin_notes" rows="6" class="admin-textarea" placeholder="Record conversations, curated property shortlists, or NDA execution..."><?= e($detail['admin_notes'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn-admin btn-admin--primary" style="width: 100%; justify-content: center;">
              Update Inquiry
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

<?php else: ?>
  <!-- List View -->
  <div class="admin-card">
    <div class="admin-card__header">
      <h2 class="admin-card__title">Buyer Inquiries (<?= e((string)$list['total']) ?>)</h2>
      <form method="get" action="<?= e(url('admin/buyer-requests.php')) ?>" style="display: flex; gap: 10px;">
        <input type="text" name="search" class="admin-input" style="width: 200px; padding: 6px 10px;" placeholder="Search client, email..." value="<?= e($search) ?>">
        <select name="status" class="admin-select" style="width: 150px; padding: 6px 10px;">
          <option value="">All Statuses</option>
          <option value="new" <?= $statusFilter === 'new' ? 'selected' : '' ?>>New</option>
          <option value="contacted" <?= $statusFilter === 'contacted' ? 'selected' : '' ?>>Contacted</option>
          <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Active</option>
          <option value="closed" <?= $statusFilter === 'closed' ? 'selected' : '' ?>>Closed</option>
        </select>
        <button type="submit" class="btn-admin btn-admin--secondary btn-admin--sm">Filter</button>
      </form>
    </div>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Reference</th>
          <th>Buyer Principal</th>
          <th>Email</th>
          <th>Target City</th>
          <th>Archetype</th>
          <th>Status</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($list['data'])): ?>
          <tr><td colspan="8" style="text-align: center; color: #888;">No buyer inquiries logged yet.</td></tr>
        <?php else: ?>
          <?php foreach ($list['data'] as $row): ?>
            <tr>
              <td><strong><?= e($row['reference']) ?></strong></td>
              <td><?= e($row['full_name']) ?></td>
              <td><?= e($row['email']) ?></td>
              <td><?= e($row['city'] ?: '—') ?></td>
              <td><?= e(ucfirst($row['property_type'] ?? '—')) ?></td>
              <td><span class="badge badge--<?= e($row['status']) ?>"><?= e($row['status']) ?></span></td>
              <td><?= e(date('d M Y', strtotime($row['created_at']))) ?></td>
              <td>
                <a href="<?= e(url('admin/buyer-requests.php?id=' . $row['id'])) ?>" class="btn-admin btn-admin--sm btn-admin--secondary">View</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
