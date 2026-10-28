<?php
/**
 * HS Properties — PDO Database Singleton.
 *
 * Thread-safe PDO wrapper with prepared-statement convenience methods.
 * All queries go through prepared statements to prevent SQL injection.
 */

declare(strict_types=1);

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct(array $config)
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $config['host'],
            $config['port'],
            $config['name']
        );

        $this->pdo = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'",
        ]);
    }

    /**
     * Get or create the singleton instance.
     */
    public static function getInstance(?array $config = null): self
    {
        if (self::$instance === null) {
            if ($config === null) {
                throw new RuntimeException('Database not initialised. Provide config on first call.');
            }
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    /**
     * Get the underlying PDO instance.
     */
    public function pdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Execute a query and return the PDOStatement.
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch all rows.
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Fetch a single row.
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $row = $this->query($sql, $params)->fetch();
        return $row ?: null;
    }

    /**
     * Fetch a single column value.
     */
    public function fetchColumn(string $sql, array $params = []): mixed
    {
        return $this->query($sql, $params)->fetchColumn();
    }

    /**
     * Insert a row and return the last insert ID.
     */
    public function insert(string $table, array $data): int
    {
        $columns      = array_keys($data);
        $placeholders = array_map(fn($c) => ':' . $c, $columns);

        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $table,
            implode(', ', array_map(fn($c) => "`$c`", $columns)),
            implode(', ', $placeholders)
        );

        $this->query($sql, $data);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Update rows matching a WHERE clause.
     *
     * @return int Number of affected rows.
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $sets = [];
        foreach (array_keys($data) as $col) {
            $sets[] = "`$col` = :set_$col";
        }

        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE %s',
            $table,
            implode(', ', $sets),
            $where
        );

        $params = [];
        foreach ($data as $k => $v) {
            $params["set_$k"] = $v;
        }
        $params = array_merge($params, $whereParams);

        return $this->query($sql, $params)->rowCount();
    }

    /**
     * Delete rows matching a WHERE clause.
     *
     * @return int Number of affected rows.
     */
    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = sprintf('DELETE FROM `%s` WHERE %s', $table, $where);
        return $this->query($sql, $params)->rowCount();
    }

    /**
     * Count rows matching a WHERE clause.
     */
    public function count(string $table, string $where = '1=1', array $params = []): int
    {
        $sql = sprintf('SELECT COUNT(*) FROM `%s` WHERE %s', $table, $where);
        return (int) $this->fetchColumn($sql, $params);
    }

    /**
     * Begin a transaction.
     */
    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    /**
     * Commit a transaction.
     */
    public function commit(): void
    {
        $this->pdo->commit();
    }

    /**
     * Roll back a transaction.
     */
    public function rollBack(): void
    {
        $this->pdo->rollBack();
    }
}
