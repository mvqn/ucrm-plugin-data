<?php
declare(strict_types=1);

namespace UCRM\Data;
//require __DIR__ . "/../../../vendor/autoload.php";

use PDO;
use UCRM\Coder\{Code, Casing};


final class MAPPER
{
    private const MODEL_PATH = __DIR__."/./Models";
    private const MODEL_NAMESPACE_PREFIX = "UCRM\\Data\\Models\\";


    public static function exists(string $class): bool
    {
        return file_exists(self::MODEL_PATH."/$class.php");
    }


    public static function create(PDO $pdo, string $table)//: Model
    {
        $exceptions = [];
        $foreign_keys = MAPPER::echoForeignKeys($pdo, $table, $exceptions);
        $dependencies = $exceptions;
        $primary_key = MAPPER::echoPrimaryKey($pdo, $table, $exceptions);
        $fields = MAPPER::echoFields($pdo, $table, $exceptions);

        $class_name = Casing::snake_to_pascal($table);
        $class_path = self::MODEL_PATH."/$class_name.php";


        $uses = [];
        foreach($dependencies as $dependency)
        {
            $class = Casing::snake_to_pascal(str_replace("_id", "", $dependency));

            $use_code = self::MODEL_NAMESPACE_PREFIX.$class;

            $uses[] = Code::inline("
                use $use_code;
            ");
        }

        $uses_code = implode(PHP_EOL, $uses);
        $uses_code = Code::adjust_indent($uses_code, 0, -16);

        $timestamp = (new \DateTime())->format("m/d/Y @ H:i:s (\G\M\TP)")." by MAPPER";

        $class_code = Code::inline("
            <?php
            declare(strict_types=1);
            
            namespace UCRM\Data\Models;
            
            
            /**
             * Class $class_name
             *
             * @package UCRM\Data\Models
             * @author  Ryan Spaeth <rspaeth@mvqn.net>
             * @version Auto-Generated on $timestamp  
             */
            final class $class_name extends \UCRM\Data\Model
            {
                $primary_key
                $fields
                $foreign_keys
            }
        ", -12);

        // Ignore the Auto-Generated Timestamp when comparing for changes in the code.
        $old_code = preg_replace("/Auto-Generated on .+ by MAPPER/","", file_get_contents($class_path));
        $new_code = preg_replace("/Auto-Generated on .+ by MAPPER/","", $class_code);

        if(!file_exists($class_path) || $old_code !== $new_code)
            file_put_contents($class_path, $class_code);

        //$full_name = "UCRM\\Data\\Models\\$class_name";
        //return new $full_name();
    }





    public static function getPrimaryKey(PDO $pdo, string $table): array
    {
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
            WHERE constraint_type = 'PRIMARY KEY' AND tc.table_name = '$table'
        ";

        $results = $pdo->query($query);
        $key = $results->fetch(); // ONLY ONE PRIMARY KEY!

        return $key;
    }

    public static function getForeignKeys(PDO $pdo, string $table): array
    {
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
            WHERE constraint_type = 'FOREIGN KEY' AND tc.table_name = '$table'
        ";

        $array = [];

        $rows = $pdo->query($query);
        while($row = $rows->fetch())
        {
            $array[] = $row;
        }

        return $array;
    }





    public static function isPrimaryKey(PDO $pdo, string $table, string $column): bool
    {
        return self::getPrimaryKey($pdo, $table)["column_name"] === $column;
    }

    public static function foreignKeyNames(PDO $pdo, string $table): array
    {
        $array = [];

        foreach(self::getForeignKeys($pdo, $table) as $key)
            $array[] = $key["column_name"];

        return $array;
    }



    public static function echoPrimaryKey(PDO $pdo, string $table, array &$exceptions = null): string
    {
        $exceptions = $exceptions === null ? [] : $exceptions;

        $key = self::getPrimaryKey($pdo, $table);

        $key_name = $key["column_name"];
        $php_type = "int";
        $sql_type = "integer";

        $get_name = Casing::snake_to_camel("get_".$key_name);

        $code = Code::inline("
            /** @const string The column name of the PRIMARY KEY of this Model. */
            protected const PRIMARY_KEY = \"$key_name\"; 
            
            /** @const string The table name of this Model. */
            protected const TABLE_NAME = \"$table\";
        
        
        
            /**
             * @var $php_type
             */
            protected \$$key_name;
            
            /**
             * @return $php_type|null
             */
            public function $get_name(): ?$php_type
            {
                return \$this->$key_name;
            }
        ", 4);

        $code = Code::adjust_indent($code, 0, -16);

        if(!in_array($key_name, $exceptions))
            $exceptions[] = $key_name;

        return $code;
    }

    public static function echoForeignKeys(PDO $pdo, string $table, array &$exceptions = null): string
    {
        $code = [];
        $exceptions = $exceptions === null ? [] : $exceptions;

        $keys = self::getForeignKeys($pdo, $table);

        foreach($keys as $key)
        {
            $key_name = $key["column_name"];
            $php_type = "int";
            $php_class = Casing::snake_to_pascal(str_replace("_id", "", $key_name));
            $sql_type = "integer";

            $foreign_table = str_replace("_id", "", $key_name);

            $get_name = Casing::snake_to_camel("get_" . $php_class);
            $set_name = Casing::snake_to_camel("set_" . $php_class);

            $code[] = Code::inline("
                /**
                 * @var $php_type|null
                 */
                protected \$$key_name;
                
                /**
                 * @return $php_class|null
                 * @throws \UCRM\Data\Exceptions\DatabaseQueryException
                 */
                public function $get_name(): ?$php_class
                {
                    // TODO: Handle non-lazy loading also???
                    /** @var $php_class \${$foreign_table} */
                    \${$foreign_table} = {$php_class}::getById(\$this->$key_name);
                    return \$$foreign_table;
                }
                
                /**
                 * @param $php_class \$value
                 */
                public function $set_name($php_class \$value): void
                {
                    // TODO: Determine best way to store foreign table here...
                    //\$this->$foreign_table = \$value;
                }   
            ");

            if(!in_array($key_name, $exceptions))
                $exceptions[] = $key_name;
        }

        $string = implode(PHP_EOL, $code);
        $string = Code::adjust_indent($string, 0, -16);

        return $string;
    }





    public static function echoFields(PDO $pdo, string $table, array $exclude_columns = []): string
    {
        $code = [];

        $query = "SELECT * FROM $table LIMIT 0";

        $results = $pdo->query($query);

        for ($i = 0; $i < $results->columnCount(); $i++) {
            $col = $results->getColumnMeta($i);

            // $col = Array(
            //     [pgsql:oid] => 1043
            //     [pgsql:table_oid] => 18577
            //     [table] => app_key
            //     [native_type] => varchar
            //     [name] => name
            //     [len] => -1
            //     [precision] => 260
            //     [pdo_type] => 2
            // )

            if (in_array($col["name"], $exclude_columns))
                continue;

            $col_name = $col["name"];
            $php_type = "";
            $sql_type = "";

            switch ($col["native_type"]) {
                case "int4":
                    $php_type = "int";
                    $sql_type = "integer";
                    break;

                case "varchar":
                    $php_type = "string";
                    $sql_type = "varchar(" . ($col["precision"] - 4) . ")";
                    break;

                case "timestamp":
                    $php_type = "string";
                    $sql_type = "timestamp(0)";
                    break;

                case "text":
                    $php_type = "string";
                    $sql_type = "text";
                    break;

                case "bool":
                    $php_type = "bool";
                    $sql_type = "boolean";
                    break;

                default:
                    die("Add type: {$col['native_type']}");
            }

            $get_name = Casing::snake_to_camel("get_" . $col_name);
            $set_name = Casing::snake_to_camel("set_" . $col_name);

            $code[] = Code::inline("
                /**
                 * @var $php_type
                 */
                protected \$$col_name;
                
                /**
                 * @return $php_type|null
                 */
                public function $get_name(): ?$php_type
                {
                    return \$this->$col_name;
                }
                
                /**
                 * @param string \$value
                 */
                public function $set_name($php_type \$value): void
                {
                    \$this->$col_name = \$value;
                }
            ");
        }

        $string = implode(PHP_EOL, $code);
        $string = Code::adjust_indent($string, 0, -16);

        return $string;
    }






}