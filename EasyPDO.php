<?php

declare(strict_types=1);

namespace Wildhoof;

use Closure;
use PDO;
use PDOException;
use PDOStatement;
use Throwable;

/**
 * Database wrapper class for enabling method chaining.
 */
class EasyPDO
{
    private PDO $pdo;
    private PDOStatement $stmt;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Creates a new prepared statement
     */
    public function query(string $query): Database
    {
        $this->stmt = $this->pdo->prepare($query);
        return $this;
    }

    /**
     * Binds parameters to a prepared statement.
     */
    public function bind(mixed $id, mixed $value, int $type = null): Database
    {
        if (is_null($type))
        {
            $type = match (true) {
                is_null($value) => PDO::PARAM_NULL,
                is_int($value) => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                default => PDO::PARAM_STR,
            };
        }

        $this->stmt->bindValue($id, $value, $type);
        return $this;
    }

    /**
     * Executes a prepared statement.
     */
    public function execute(): bool {
        return $this->stmt->execute();
    }

    /**
     * Returns all result rows.
     */
    public function fetchAll(int $mode = PDO::FETCH_DEFAULT): array
    {
        $this->execute();
        return $this->stmt->fetchAll($mode);
    }

    /**
     * Selects one result row.
     */
    public function fetch(int $mode = PDO::FETCH_DEFAULT): array
    {
        $this->execute();
        return $this->stmt->fetch($mode);
    }

    /**
     * Selects one result column.
     */
    public function fetchColumn(int $position = 0): mixed
    {
        $this->execute();
        return $this->stmt->fetchColumn($position);
    }

    /**
     * Get last inserted id.
     */
    public function lastInsertId(): int {
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Initiates a transaction.
     */
    public function beginTransaction(): bool {
        return $this->pdo->beginTransaction();
    }

    /**
     * Rolls back a transaction.
     */
    public function rollBack(): bool {
        return $this->pdo->rollBack();
    }

    /**
     * Commits a transaction.
     */
    public function commit(): bool {
        return $this->pdo->commit();
    }

    /**
     * Executes an anonymous function within a transaction and rolls back if
     * an exception occurred.
     */
    public function transaction(Closure $function): mixed
    {
        $this->beginTransaction();

        try {
            $result = $function($this);
            $this->commit();

            return $result;
        } catch (Throwable $exception) {
            $this->rollBack();

            throw new PDOException('Transaction failed');
        }
    }
}
