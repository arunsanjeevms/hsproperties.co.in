<?php
/**
 * HS Properties — User Model.
 *
 * Manages administrative users, roles, and profile updates.
 */

declare(strict_types=1);

class User
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find a user by ID.
     */
    public function find(int $id): ?array
    {
        return $this->db->fetchOne(
            'SELECT id, name, email, role, is_active, last_login_at, created_at, updated_at FROM `users` WHERE `id` = :id',
            ['id' => $id]
        );
    }

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?array
    {
        return $this->db->fetchOne(
            'SELECT * FROM `users` WHERE `email` = :email',
            ['email' => $email]
        );
    }

    /**
     * Get all users.
     */
    public function all(): array
    {
        return $this->db->fetchAll(
            'SELECT id, name, email, role, is_active, last_login_at, created_at FROM `users` ORDER BY id ASC'
        );
    }

    /**
     * Create a new user.
     */
    public function create(array $data): int
    {
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->db->insert('users', [
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password_hash' => $hash,
            'role'          => $data['role'] ?? 'admin',
            'is_active'     => $data['is_active'] ?? 1,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Update user details.
     */
    public function update(int $id, array $data): int
    {
        $fields = [
            'name'      => $data['name'],
            'email'     => $data['email'],
            'role'      => $data['role'],
            'is_active' => $data['is_active'] ?? 1,
        ];

        if (!empty($data['password'])) {
            $fields['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        return $this->db->update('users', $fields, '`id` = :id', ['id' => $id]);
    }

    /**
     * Delete a user.
     */
    public function delete(int $id): int
    {
        return $this->db->delete('users', '`id` = :id', ['id' => $id]);
    }
}
