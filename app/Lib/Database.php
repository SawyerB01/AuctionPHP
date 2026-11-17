<?php
namespace App\Lib;

use PDO;
use PDOException;
use ReflectionClass;
use ReflectionException;

/**
 * Class Database
 * @package app\Lib
 */
class Database
{
    /**
     * @var Database|null
     */
    private static $databaseObj = null;

    /**
     * @var PDO|null
     */
    private $connection = null;

    /**
     * Returns a Database object using singleton
     * @return Database
     */
    public static function getConnection(): Database
    {
        if (!self::$databaseObj) {
            self::$databaseObj = new self();
        }
        return self::$databaseObj;
    }

    /**
     * Database constructor.
     */
    private function __construct()
    {
        try {
            $this->connection = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
                DB_USER,
                DB_PASSWORD
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            Logger::getLogger()->critical("Database connection failed: " . $e->getMessage());
            die();
        }
    }

    public function __destruct()
    {
        $this->connection = null;
    }

    /**
     * @param string $sql
     * @param array|null $bindVal
     * @param bool $retStmt
     * @return bool|\PDOStatement
     */
    public function sqlQuery(string $sql, $bindVal = null, bool $retStmt = false)
    {
        try {
            $statement = $this->connection->prepare($sql);

            if (is_array($bindVal)) {
                $result = $statement->execute($bindVal);
            } else {
                $result = $statement->execute();
            }

            if ($retStmt) {
                return $statement;
            } else {
                return $result;
            }
        } catch (PDOException $e) {
            Logger::getLogger()->error("SQL Query failed: " . $e->getMessage() . " | SQL: " . $sql);
            die();

        }
    }

    /**
     * @param string $sql
     * @param string $class
     * @param array|null $bindVal
     * @return array
     */
    public function fetch(string $sql, string $class, $bindVal = null): array
    {
        $statement = $this->sqlQuery($sql, $bindVal, true);

        if ($statement->rowCount() == 0) {
            return [];
        }

        try {
            $reflect = new ReflectionClass($class);

            if ($reflect->getConstructor() == null) {
                $ctor_args = [];
            } else {
                $num = count($reflect->getConstructor()->getParameters());
                $ctor_args = array_fill(0, $num, null);
            }

            return $statement->fetchAll(
                PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,
                $class,
                $ctor_args
            );
        } catch (ReflectionException $e) {
            Logger::getLogger()->error("Reflection failed in fetch(): " . $e->getMessage());
            die();

        }
    }

    /**
     * @return string
     */
    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }

    /**
     * Execute a statement and return the number of rows returned
     * @param string $sql
     * @param array|null $bindVal
     * @return int
     */
    public function rowCount(string $sql, $bindVal = null): int
    {
        $statement = $this->sqlQuery($sql, $bindVal, true);
        return $statement->rowCount();
    }
}