<?php
/**
 * HS Properties — Property Model.
 *
 * Handles CRUD operations for portfolio properties displayed on the home page.
 */

declare(strict_types=1);

class Property
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all portfolio properties (for the home page).
     */
    public function getPortfolio(): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM `properties`
             WHERE `is_portfolio` = 1 AND `is_active` = 1
             ORDER BY `sort_order` ASC'
        );
    }

    /**
     * Get all properties with optional filtering and pagination.
     */
    public function getAll(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]           = '`status` = :status';
            $params['status']  = $filters['status'];
        }
        if (!empty($filters['type'])) {
            $where[]        = '`type` = :type';
            $params['type'] = $filters['type'];
        }
        if (!empty($filters['search'])) {
            $where[]          = '(`title` LIKE :search OR `location` LIKE :search OR `reference` LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $whereClause = implode(' AND ', $where);
        $offset      = ($page - 1) * $perPage;

        $total = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM `properties` WHERE $whereClause",
            $params
        );

        $rows = $this->db->fetchAll(
            "SELECT * FROM `properties` WHERE $whereClause ORDER BY `sort_order` ASC, `created_at` DESC LIMIT $perPage OFFSET $offset",
            $params
        );

        return [
            'data'       => $rows,
            'total'      => (int) $total,
            'page'       => $page,
            'per_page'   => $perPage,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Find a single property by ID.
     */
    public function find(int $id): ?array
    {
        return $this->db->fetchOne(
            'SELECT * FROM `properties` WHERE `id` = :id',
            ['id' => $id]
        );
    }

    /**
     * Find a property by reference number.
     */
    public function findByReference(string $reference): ?array
    {
        return $this->db->fetchOne(
            'SELECT * FROM `properties` WHERE `reference` = :ref',
            ['ref' => $reference]
        );
    }

    /**
     * Create a new property.
     */
    public function create(array $data): int
    {
        return $this->db->insert('properties', $data);
    }

    /**
     * Update a property.
     */
    public function update(int $id, array $data): int
    {
        return $this->db->update('properties', $data, '`id` = :id', ['id' => $id]);
    }

    /**
     * Delete a property.
     */
    public function delete(int $id): int
    {
        return $this->db->delete('properties', '`id` = :id', ['id' => $id]);
    }

    /**
     * Get the total count of properties.
     */
    public function count(string $where = '1=1', array $params = []): int
    {
        return $this->db->count('properties', $where, $params);
    }
}
