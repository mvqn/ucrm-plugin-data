<?php
declare(strict_types=1);

namespace UCRM\Data;
require __DIR__ . "/../../../vendor/autoload.php";

use PDO;
use ArrayAccess;
use Coder\{Code, Casing};


final class Database
{
    private $pdo;



    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }



    public function learn()
    {
        $query = "SELECT * FROM pg_catalog.pg_tables";
        $query = "SELECT * FROM information_schema.COLUMNS WHERE TABLE_NAME = 'client'";
        $query = "
            SELECT
                tc.constraint_name, tc.table_name, kcu.column_name, 
                ccu.table_name AS foreign_table_name,
                ccu.column_name AS foreign_column_name 
            FROM 
                information_schema.table_constraints AS tc 
                JOIN information_schema.key_column_usage AS kcu
                  ON tc.constraint_name = kcu.constraint_name
                JOIN information_schema.constraint_column_usage AS ccu
                  ON ccu.constraint_name = tc.constraint_name
            WHERE constraint_type = 'FOREIGN KEY' AND tc.table_name = 'app_key'
        ";


        $results = $this->pdo->query($query);

        foreach($results as $table)
        {
            print_r($table);
            //echo json_encode($table, JSON_PRETTY_PRINT)."\n";
        }



    }







    public function id(string $class, int $id): Model
    {
        $table = Casing::pascal_to_snake(Casing::class_name($class));

        $query = "SELECT * FROM $table WHERE {$table}_id = $id";

        $results = $this->pdo->query($query);

        $row = $results->fetch();

        return new $class($row);
    }


    public function where(string $class, string $column, string $value): ModelCollection
    {
        $table = Casing::pascal_to_snake(Casing::class_name($class));

        $query = "SELECT * FROM $table WHERE $column = '$value'";

        $results = $this->pdo->query($query);

        $models = [];
        while($row = $results->fetch())
        {
            $model = new $class($row);
            $models[] = $model;
        }

        return new ModelCollection($models);
    }

    public function like(string $class, string $column, string $value): ModelCollection
    {
        $table = Casing::pascal_to_snake(Casing::class_name($class));

        $query = "SELECT * FROM $table WHERE $column LIKE '$value'";

        $results = $this->pdo->query($query);

        $models = [];
        while($row = $results->fetch())
        {
            $model = new $class($row);
            $models[] = $model;
        }

        return new ModelCollection($models);
    }








}