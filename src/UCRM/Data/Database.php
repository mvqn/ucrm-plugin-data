<?php
declare(strict_types=1);

namespace UCRM\Data;

use PDO;
use PDOStatement;
use UCRM\Data\Exceptions\DatabaseQueryException;



/**
 * Class Database
 *
 * A class to interact with the UCRM database directly from code.
 *
 * @package UCRM\Data
 * @author  Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 */
final class Database
{
    /** @var PDO|null $_pdo The singleton instance of a PDO to use for all database interactions. */
    private static $_pdo = null;

    /**
     * Connects to database using the provided PDO and stores it for further use.
     *
     * @param PDO $pdo A PDO to use for all database interactions.
     * @return PDO Returns the same PDO as provided.
     */
    public static function connect(PDO $pdo): PDO
    {
        self::$_pdo = $pdo;
        return self::$_pdo;
    }

    /**
     * Disconnects from an existing database and removed the stored PDO.
     *
     * @return bool Returns true if the database was successfully disconnected, otherwise false.
     */
    public static function disconnect(): bool
    {
        if(self::$_pdo !== null)
        {
            self::$_pdo = null;
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Checks to see if we have connected to the database already.
     *
     * @return bool Returns true if a stored PDO exists, otherwise false.
     */
    public static function connected(): bool
    {
        return (self::$_pdo !== null);
    }

    /**
     * Gets the stored PDO.
     *
     * @return PDO|null Returns the stored PDO or null if none exists.
     */
    public static function PDO(): ?PDO
    {
        return self::$_pdo;
    }



    /**
     * Executes a query against the database.
     *
     * @param string $query The query string to execute.
     * @return PDOStatement Returns a PDOStatement for parsing.
     * @throws DatabaseQueryException Throws an exception if the database is not already connected.
     */
    public static function query(string $query): PDOStatement
    {
        if(!self::connected())
            throw new DatabaseQueryException("Database is not connected!");

        $results = self::$_pdo->query($query);

        return $results;
    }

}