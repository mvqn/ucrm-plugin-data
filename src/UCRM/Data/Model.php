<?php
declare(strict_types=1);

namespace UCRM\Data;
require __DIR__ . "/../../../vendor/autoload.php";

use PDO;
use Coder\{Code, Casing};
use UCRM\Data\MAPPER;


abstract class Model
{
    protected $pdo;

    protected $table;

    protected static $PDO;



    public function populate(array $values): Model
    {
        foreach($values as $key => $value)
            $this->$key = $value;

        return $this;
    }







    public function __construct(PDO $pdo, string $table, array $object = [])
    {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->populate($object);
    }





    public function getById(int $id): ?Model
    {
        $class = get_called_class();
        $table = $class::TABLE_NAME;
        $primary_key = $class::PRIMARY_KEY;

        $query = "SELECT * FROM $table WHERE $primary_key = $id";

        $results = $this->pdo->query($query);
        return $this->populate($results->fetch());
    }

    // EQUALS
    public function where(string $column, string $value): array
    {

        $query = "SELECT * FROM {$this->table} WHERE $column = '$value'";

        $results = $this->pdo->query($query);

        $models = [];
        while($row = $results->fetch())
        {
            $models[] = $this->populate($row);
        }

        return $models;
    }

    public static function like(string $column, string $value): array
    {
        $full_name = get_called_class();
        $class_parts = explode("\\", $full_name);
        $class_name = end($class_parts);

        $table = Casing::pascal_to_snake($class_name);

        $query = "SELECT * FROM $table WHERE $column LIKE '$value'";




        $results = self::$PDO->query($query);


        $models = [];
        while($row = $results->fetch())
        {
            //$model = new $full_name(self::$PDO);
            //$model->populate($row);
            $model = self::set_obj_vars($full_name, $row);
            $models[] = $model;
        }

        return $models;


    }







    public function __toString()
    {
        $assoc = get_object_vars($this);
        unset($assoc["pdo"]);
        unset($assoc["table"]);

        return json_encode($assoc);
    }


}