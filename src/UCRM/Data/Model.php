<?php
declare(strict_types=1);

namespace UCRM\Data;
//require __DIR__ . "/../../../vendor/autoload.php";

use PDO;
use UCRM\Coder\{Code, Casing};
use UCRM\Data\MAPPER;


abstract class Model
{

    /**
     * Model constructor.
     * @param array $values
     */
    public function __construct(/*string $table,*/ array $values = [])
    {
        //$this->table = $table; // TODO: No longer needed?
        //$this->populate($object);

        foreach($values as $key => $value)
            $this->$key = $value;

    }

    /**
     * @param int $id The ID of the Model to query.
     * @return Model|null Returns a Model with the requested ID, otherwise NULL.
     * @throws Exceptions\DatabaseQueryException Throws an exception if the database connection is not valid.
     */
    public static function getById(int $id): ?Model
    {
        $class = get_called_class();
        $table = $class::TABLE_NAME;
        $primary_key = $class::PRIMARY_KEY;

        $query = "SELECT * FROM $table WHERE $primary_key = $id";
        $results = Database::query($query);

        if($results->rowCount() === 0)
            return null;

        /** @var Model $model */
        $model = new $class($results->fetch());
        return $model;
    }

    /**
     * @return Model[] Returns all of the Models from the appropriate table.
     * @throws Exceptions\DatabaseQueryException Throws an exception if the database connection is not valid.
     */
    public function select(/*array $columns = []*/): array
    {
        $class = get_called_class();
        $table = $class::TABLE_NAME;
        $primary_key = $class::PRIMARY_KEY;

        //$select = $columns === [] ? "*" : implode(", ", $columns);
        $query = "SELECT * FROM $table";

        $results = Database::query($query);

        $models = [];
        while($row = $results->fetch())
        {
            /** @var Model $model */
            $model = new $class($row);
            $models[] = $model;
        }

        return $models;
    }

    /**
     * @param string $column The column on which to match.
     * @param string $value The value of which to match.
     * @return Model[] Returns all of the Models from the appropriate table, given the specified criteria.
     * @throws Exceptions\DatabaseQueryException Throws an exception if the database connection is not valid.
     */
    public static function where(string $column, string $value): array
    {
        $class = get_called_class();
        $table = $class::TABLE_NAME;
        $primary_key = $class::PRIMARY_KEY;

        $query = "SELECT * FROM $table WHERE $column = '$value'";
        $results = Database::query($query);

        $models = [];
        while($row = $results->fetch())
        {
            /** @var Model $model */
            $model = new $class($row);
            $models[] = $model;
        }

        return $models;
    }

    /**
     * @param string $column The column on which to match.
     * @param string $pattern The pattern of which to match.
     * @return Model[] Returns all of the Models from the appropriate table, given the specified criteria.
     * @throws Exceptions\DatabaseQueryException Throws an exception if the database connection is not valid.
     */
    public static function like(string $column, string $pattern): array
    {
        $class = get_called_class();
        $table = $class::TABLE_NAME;
        $primary_key = $class::PRIMARY_KEY;

        $query = "SELECT * FROM $table WHERE $column LIKE '$pattern'";

        $results = Database::query($query);

        $models = [];
        while($row = $results->fetch())
        {
            /** @var Model $model */
            $model = new $class($row);
            $models[] = $model;
        }

        return $models;
    }







    public function __toString()
    {
        $assoc = get_object_vars($this);
        //unset($assoc["pdo"]);
        //unset($assoc["table"]);
        $assoc = array_filter($assoc);

        return json_encode($assoc, JSON_UNESCAPED_SLASHES);
    }


}