<?php
/**
 * HS Properties — Buyer Request Model.
 *
 * Manages buyer inquiry submissions.
 */

declare(strict_types=1);

class BuyerRequest
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Create a new buyer request.
     */
    public function create(array $data): int
    {
        $data['reference'] = generate_reference($data['city'] ?? '');
        return $this->db->insert('buyer_requests', $data);
    }

    /**
     * Find by ID.
     */
    public function find(int $id): ?array
    {
        return $this->db->fetchOne(
            'SELECT * FROM `buyer_requests` WHERE `id` = :id',
            ['id' => $id]
        );
    }

    /**
     * Find by reference.
     */
    public function findByReference(string $reference): ?array
    {
        return $this->db->fetchOne(
            'SELECT * FROM `buyer_requests` WHERE `reference` = :ref',
            ['ref' => $reference]
        );
    }

    /**
     * Get all with filtering and pagination.
     */
    public function getAll(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]           = '`status` = :status';
            $params['status']  = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $where[]          = '(`reference` LIKE :search OR `full_name` LIKE :search OR `email` LIKE :search OR `city` LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $whereClause = implode(' AND ', $where);
        $offset      = ($page - 1) * $perPage;

        $total = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM `buyer_requests` WHERE $whereClause",
            $params
        );

        $rows = $this->db->fetchAll(
            "SELECT * FROM `buyer_requests` WHERE $whereClause ORDER BY `created_at` DESC LIMIT $perPage OFFSET $offset",
            $params
        );

        return [
            'data'        => $rows,
            'total'       => (int) $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Update status.
     */
    public function updateStatus(int $id, string $status, ?int $assignedTo = null, ?string $notes = null): void
    {
        $data = ['status' => $status];
        if ($assignedTo !== null) {
            $data['assigned_to'] = $assignedTo;
        }
        if ($notes !== null) {
            $data['admin_notes'] = $notes;
        }
        if ($status === 'contacted') {
            $data['contacted_at'] = date('Y-m-d H:i:s');
        }

        $this->db->update('buyer_requests', $data, '`id` = :id', ['id' => $id]);
    }

    /**
     * Count by status.
     */
    public function countByStatus(): array
    {
        $rows   = $this->db->fetchAll(
            'SELECT `status`, COUNT(*) as `count` FROM `buyer_requests` GROUP BY `status`'
        );
        $counts = [];
        foreach ($rows as $row) {
            $counts[$row['status']] = (int) $row['count'];
        }
        return $counts;
    }

    /**
     * Get recent requests.
     */
    public function getRecent(int $limit = 5): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM `buyer_requests` ORDER BY `created_at` DESC LIMIT $limit"
        );
    }

    /**
     * Total count.
     */
    public function count(): int
    {
        return $this->db->count('buyer_requests');
    }
}
