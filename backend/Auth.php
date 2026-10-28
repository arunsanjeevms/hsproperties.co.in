<?php
/**
 * HS Properties — Authentication Manager.
 *
 * Handles admin login, logout, session-based auth state,
 * and password management using bcrypt.
 */

declare(strict_types=1);

class Auth
{
    /**
     * Attempt to log in with email and password.
     *
     * @return array|null User data on success, null on failure.
     */
    public static function attempt(string $email, string $password): ?array
    {
        $db   = Database::getInstance();
        $user = $db->fetchOne(
            'SELECT * FROM `users` WHERE `email` = :email AND `is_active` = 1',
            ['email' => $email]
        );

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return null;
        }

        // Update last login
        $db->update('users', ['last_login_at' => date('Y-m-d H:i:s')], '`id` = :id', ['id' => $user['id']]);

        // Store in session
        if (!headers_sent()) {
            session_regenerate_id(true);
        }
        Session::set('auth_user_id', $user['id']);
        Session::set('auth_user_name', $user['name']);
        Session::set('auth_user_email', $user['email']);
        Session::set('auth_user_role', $user['role']);

        return $user;
    }

    /**
     * Check if a user is currently authenticated.
     */
    public static function check(): bool
    {
        return Session::has('auth_user_id');
    }

    /**
     * Get the current authenticated user's ID.
     */
    public static function id(): ?int
    {
        $id = Session::get('auth_user_id');
        return $id !== null ? (int) $id : null;
    }

    /**
     * Get the current authenticated user's data from session.
     */
    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        return [
            'id'    => (int) Session::get('auth_user_id'),
            'name'  => Session::get('auth_user_name'),
            'email' => Session::get('auth_user_email'),
            'role'  => Session::get('auth_user_role'),
        ];
    }

    /**
     * Get the full user record from the database.
     */
    public static function freshUser(): ?array
    {
        $id = self::id();
        if ($id === null) {
            return null;
        }

        return Database::getInstance()->fetchOne(
            'SELECT `id`, `name`, `email`, `role`, `last_login_at`, `created_at`
             FROM `users` WHERE `id` = :id AND `is_active` = 1',
            ['id' => $id]
        );
    }

    /**
     * Log the current user out.
     */
    public static function logout(): void
    {
        Session::forget('auth_user_id');
        Session::forget('auth_user_name');
        Session::forget('auth_user_email');
        Session::forget('auth_user_role');
        Session::destroy();
    }

    /**
     * Guard: redirect to login if not authenticated.
     */
    public static function requireAuth(?string $loginUrl = null): void
    {
        $loginUrl = $loginUrl ?? url('admin/login.php');
        if (!self::check()) {
            Session::flash('error', 'Please log in to continue.');
            redirect($loginUrl);
        }
    }

    /**
     * Guard: redirect to login if not an authenticated admin.
     */
    public static function requireAdmin(?string $loginUrl = null): void
    {
        $loginUrl = $loginUrl ?? url('admin/login.php');
        if (!self::check()) {
            Session::flash('error', 'Please log in to access the Advisory Console.');
            redirect($loginUrl);
        }

        $user = self::user();
        if (($user['role'] ?? '') !== 'admin') {
            Session::flash('error', 'Unauthorized access restricted to senior partners.');
            redirect($loginUrl);
        }
    }

    /**
     * Hash a password using bcrypt.
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Change the current user's password.
     */
    public static function changePassword(string $currentPassword, string $newPassword): bool
    {
        $id   = self::id();
        $db   = Database::getInstance();
        $user = $db->fetchOne('SELECT `password_hash` FROM `users` WHERE `id` = :id', ['id' => $id]);

        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            return false;
        }

        $db->update(
            'users',
            ['password_hash' => self::hashPassword($newPassword)],
            '`id` = :id',
            ['id' => $id]
        );

        return true;
    }

    /**
     * Create a new admin user.
     */
    public static function createUser(string $name, string $email, string $password, string $role = 'admin'): int
    {
        return Database::getInstance()->insert('users', [
            'name'          => $name,
            'email'         => $email,
            'password_hash' => self::hashPassword($password),
            'role'          => $role,
        ]);
    }
}
