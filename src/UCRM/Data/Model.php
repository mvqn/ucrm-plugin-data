<?php
declare(strict_types=1);

namespace UCRM\Data;


/**
 * Class Model
 *
 * @package UCRM\Data
 * @author  Ryan Spaeth <rspaeth@mvqn.net>
 */
abstract class Model
{
    /**
     * @param array $values An optional array of values for which to use to initialize this Model.
     */
    public function __construct(array $values = [])
    {
        foreach($values as $key => $value)
            $this->$key = $value;
    }

    /**
     * @return string Returns a JSON representation of this Model.
     */
    public function __toString()
    {
        // Get an array of all Model properties.
        $assoc = get_object_vars($this);

        // Remove any that contain NULL values.
        $assoc = array_filter($assoc);

        // Return the array as a JSON string.
        return json_encode($assoc, JSON_UNESCAPED_SLASHES);
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
        //$primary_key = $class::PRIMARY_KEY;

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
        //$primary_key = $class::PRIMARY_KEY;

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
        //$primary_key = $class::PRIMARY_KEY;

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



    // TODO: Add Model->save() functionality?

}