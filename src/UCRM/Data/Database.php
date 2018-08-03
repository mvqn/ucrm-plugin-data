<?php
declare(strict_types=1);

namespace UCRM\Data;

use PDO;
use PDOStatement;
use UCRM\Data\Exceptions\DatabaseQueryException;

/**
 * Class Database
 *
 * @package UCRM\Data
 * @author  Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 */
final class Database
{
    /** @var PDO|null  */
    private static $_pdo = null;

    /**
     * @param PDO $pdo
     * @return PDO
     */
    public static function connect(PDO $pdo): PDO
    {
        self::$_pdo = $pdo;
        return self::$_pdo;
    }

    /**
     *
     */
    public static function disconnect(): void
    {
        self::$_pdo = null;
    }

    /**
     * @return bool
     */
    public static function connected(): bool
    {
        return (self::$_pdo !== null);
    }

    /**
     * @return PDO|null
     */
    public static function PDO(): ?PDO
    {
        return self::$_pdo;
    }


    /**
     * @param string $query
     * @return PDOStatement
     * @throws DatabaseQueryException
     */
    public static function query(string $query): PDOStatement
    {
        if(!self::connected())
            throw new DatabaseQueryException("Database is not connected!");

        $results = self::$_pdo->query($query);

        return $results;
    }






}