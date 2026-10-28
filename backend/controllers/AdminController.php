<?php
/**
 * HS Properties — Admin Controller.
 *
 * Handles dashboard statistics, authentication workflows, and inquiry status updates.
 */

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../models/Property.php';
require_once __DIR__ . '/../models/SellerRequest.php';
require_once __DIR__ . '/../models/BuyerRequest.php';

class AdminController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Process login form submission.
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('admin/login.php'));
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            Session::flash('error', 'Please enter both email and password.');
            redirect(url('admin/login.php'));
        }

        $user = Auth::attempt($email, $password);

        if (!$user) {
            Session::flash('error', 'Invalid email address or password.');
            redirect(url('admin/login.php'));
        }

        log_activity('admin_login', 'users', $user['id'], "Admin {$user['email']} logged in");
        redirect(url('admin/index.php'));
    }

    /**
     * Process logout.
     */
    public function logout(): void
    {
        if (Auth::check()) {
            log_activity('admin_logout', 'users', Auth::id(), 'Admin logged out');
        }
        Auth::logout();
        redirect(url('admin/login.php'));
    }

    /**
     * Retrieve statistics for dashboard metrics.
     */
    public function getDashboardStats(): array
    {
        Auth::requireAdmin();

        $sellerRequestsTotal = (int)$this->db->fetchColumn('SELECT COUNT(*) FROM `seller_requests`');
        $sellerPending       = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM `seller_requests` WHERE `status` = 'submitted'");
        $buyerRequestsTotal  = (int)$this->db->fetchColumn('SELECT COUNT(*) FROM `buyer_requests`');
        $propertiesTotal     = (int)$this->db->fetchColumn('SELECT COUNT(*) FROM `properties`');
        $unreadMessages      = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM `contact_messages` WHERE `status` = 'unread'");

        $recentSellerRequests = $this->db->fetchAll(
            "SELECT id, reference, city, locality, property_type, status, created_at
             FROM `seller_requests`
             ORDER BY `created_at` DESC LIMIT 5"
        );

        $recentBuyerRequests = $this->db->fetchAll(
            "SELECT id, reference, full_name, email, city, status, created_at
             FROM `buyer_requests`
             ORDER BY `created_at` DESC LIMIT 5"
        );

        $recentActivity = $this->db->fetchAll(
            "SELECT a.*, u.name as user_name
             FROM `activity_log` a
             LEFT JOIN `users` u ON a.user_id = u.id
             ORDER BY a.created_at DESC LIMIT 8"
        );

        return [
            'total_seller_requests' => $sellerRequestsTotal,
            'pending_seller_review' => $sellerPending,
            'total_buyer_requests'  => $buyerRequestsTotal,
            'total_properties'      => $propertiesTotal,
            'unread_messages'       => $unreadMessages,
            'recent_sellers'        => $recentSellerRequests,
            'recent_buyers'         => $recentBuyerRequests,
            'recent_activity'       => $recentActivity,
        ];
    }

    /**
     * Update seller request status or notes.
     */
    public function updateSellerStatus(int $id, string $status, ?string $notes = null): void
    {
        Auth::requireAdmin();
        $fields = ['status' => $status];
        if ($notes !== null) {
            $fields['admin_notes'] = $notes;
        }
        $this->db->update('seller_requests', $fields, '`id` = :id', ['id' => $id]);
        log_activity('seller_status_updated', 'seller_requests', $id, "Status changed to {$status}");
    }

    /**
     * Update buyer request status or notes.
     */
    public function updateBuyerStatus(int $id, string $status, ?string $notes = null): void
    {
        Auth::requireAdmin();
        $fields = ['status' => $status];
        if ($notes !== null) {
            $fields['admin_notes'] = $notes;
        }
        $this->db->update('buyer_requests', $fields, '`id` = :id', ['id' => $id]);
        log_activity('buyer_status_updated', 'buyer_requests', $id, "Status changed to {$status}");
    }
}
