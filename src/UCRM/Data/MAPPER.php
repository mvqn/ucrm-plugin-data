<?php
declare(strict_types=1);

namespace UCRM\Data;

use PDO;
use UCRM\Coder\{Code, Casing};
use UCRM\Data\Exceptions\MapperUnknownTypeException;


/**
 * Class MAPPER
 *
 * An internal class used solely for parsing information concerning a Model class from a database table and generating
 * the necessary code to implement a simple ORM.
 *
 * @package UCRM\Data
 * @author  Ryan Spaeth <rspaeth@mvqn.net>
 * @internal
 * @final
 */
final class MAPPER
{
    /** @const string MODEL_PATH The folder in which to store the generated Models. */
    private const MODEL_PATH = __DIR__."/./Models";

    /** @const string MODEL_NAMESPACE_PREFIX The namespace for which to prefix on all generated Models. */
    private const MODEL_NAMESPACE_PREFIX = "UCRM\\Data\\Models\\";


    /**
     * Checks to see if the specified Model class already exists.
     *
     * @param string $class The class name for which to check existence.
     * @return bool Returns true if the class already exists, otherwise false.
     */
    public static function exists(string $class): bool
    {
        return file_exists(self::MODEL_PATH."/$class.php");
    }



    /**
     * Creates a new Model class, saves it to the default folder and returns an instance of itself.
     *
     * @param PDO $pdo The PDO to use for accessing the database for mapping.
     * @param string $table The table name to map.
     * @return Model Returns an instance of the newly generated class.
     * @throws MapperUnknownTypeException Throws an exception if the SQL Native Type is not configured.
     */
    public static function create(PDO $pdo, string $table): Model
    {
        $exceptions = [];
        $foreign_keys = MAPPER::echoForeignKeys($pdo, $table, $exceptions);
        $primary_key = MAPPER::echoPrimaryKey($pdo, $table, $exceptions);
        $fields = MAPPER::echoFields($pdo, $table, $exceptions);

        $class_name = Casing::snake_to_pascal($table);
        $class_path = self::MODEL_PATH."/$class_name.php";

        /*
        $dependencies = $exceptions;

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
        */

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
        $old_code = file_exists($class_path) ?
            preg_replace("/Auto-Generated on .+ by MAPPER/","", file_get_contents($class_path)) :
            "";
        $new_code = preg_replace("/Auto-Generated on .+ by MAPPER/","", $class_code);

        // Check to see if both the stored code and generated code match and overwrite the stored code if necessary...
        if(!file_exists($class_path) || $old_code !== $new_code)
            file_put_contents($class_path, $class_code);

        // Finally, return an instance of this class object.
        $full_name = self::MODEL_NAMESPACE_PREFIX."$class_name";
        return new $full_name();
    }



    /**
     * Gets the primary key of the specified table.
     *
     * @param PDO $pdo The PDO to use for accessing the database for mapping.
     * @param string $table The table name to inspect.
     * @return array Returns an array of information pertaining to the PRIMARY KEY of the specified table.
     */
    public static function getPrimaryKey(PDO $pdo, string $table): array
    {
        /** @noinspection SqlResolve */
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

    /**
     * Gets the foreign keys of the specified table.
     *
     * @param PDO $pdo The PDO to use for accessing the database for mapping.
     * @param string $table The table name to inspect.
     * @return array Returns an array of information pertaining to the FOREIGN KEYs of the specified table.
     */
    public static function getForeignKeys(PDO $pdo, string $table): array
    {
        /** @noinspection SqlResolve */
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
            $array[] = $row;

        return $array;
    }

    /**
     * Checks to see if the specified column of the specified table is a PRIMARY KEY.
     *
     * @param PDO $pdo The PDO to use for accessing the database for mapping.
     * @param string $table The table name to inspect.
     * @param string $column The column name to inspect.
     * @return bool Returns true if the specified column of the specified table is a PRIMARY KEY.
     */
    public static function isPrimaryKey(PDO $pdo, string $table, string $column): bool
    {
        return self::getPrimaryKey($pdo, $table)["column_name"] === $column;
    }

    /**
     * Get an array of the names of all FOREIGN KEYs for the specified table.
     *
     * @param PDO $pdo The PDO to use for accessing the database for mapping.
     * @param string $table The table name to inspect.
     * @return array Returns an array of the column names of all FOREIGN KEYs of the specified table.
     */
    public static function foreignKeyNames(PDO $pdo, string $table): array
    {
        $array = [];

        foreach(self::getForeignKeys($pdo, $table) as $key)
            $array[] = $key["column_name"];

        return $array;
    }


    /**
     * Generates the code for the PRIMARY KEY to be included in the generated class.
     *
     * @param PDO $pdo The PDO to use for accessing the database for mapping.
     * @param string $table The table name to use on generation.
     * @param array|null $completed An optional reference array used to track the completed column names.
     * @return string Returns a string of code ready for insertion into the template.
     */
    public static function echoPrimaryKey(PDO $pdo, string $table, array &$completed = null): string
    {
        $completed = $completed === null ? [] : $completed;

        // Get the primary key information for this table.
        $key = self::getPrimaryKey($pdo, $table);
        $key_name = $key["column_name"];
        $php_type = "int";

        // Convert the getter function name to camel-case.
        $get_name = Casing::snake_to_camel("get_".$key_name);

        // Generate the code...
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

        // Adjust the code indentation of the first line to align correctly in the template.
        $code = Code::adjust_indent($code, 0, -16);

        // Add this column to the list of completed columns...
        if(!in_array($key_name, $completed))
            $completed[] = $key_name;

        // Finally, return the newly generated code!
        return $code;
    }

    /**
     * Generates the code for the FOREIGN KEYs to be included in the generated class.
     *
     * @param PDO $pdo The PDO to use for accessing the database for mapping.
     * @param string $table The table name to use on generation.
     * @param array|null $completed An optional reference array used to track the completed column names.
     * @return string Returns a string of code ready for insertion into the template.
     */
    public static function echoForeignKeys(PDO $pdo, string $table, array &$completed = null): string
    {
        $completed = $completed === null ? [] : $completed;

        // Get the foreign key information for this table.
        $keys = self::getForeignKeys($pdo, $table);

        $code = [];
        foreach($keys as $key)
        {
            $key_name = $key["column_name"];
            $php_type = "int";

            // Convert the foreign key name to that of the referenced class object.
            $php_class = Casing::snake_to_pascal(str_replace("_id", "", $key_name));

            // Convert the foreign key name to that of the referenced table.
            $foreign_table = str_replace("_id", "", $key_name);

            // Convert the getter function name to camel-case.
            $get_name = Casing::snake_to_camel("get_" . $php_class);
            // Convert the setter function name to camel-case.
            $set_name = Casing::snake_to_camel("set_" . $php_class);

            // Generate the code...
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

            // Add this column to the list of completed columns...
            if(!in_array($key_name, $completed))
                $completed[] = $key_name;
        }

        // Join the code snippets and adjust the code indentation of the first line to align correctly in the template.
        $code = implode(PHP_EOL, $code);
        $code = Code::adjust_indent($code, 0, -16);

        // Finally, return the newly generated code!
        return $code;
    }


    /**
     * Generates the code for the FIELDS to be included in the generated class.
     *
     * @param PDO $pdo The PDO to use for accessing the database for mapping.
     * @param string $table The table name to use on generation.
     * @param array $exclude_columns An optional array of column names to exclude, namely the PRIMARY & FOREIGN KEYs.
     * @return string Returns a string of code ready for insertion into the template.
     * @throws MapperUnknownTypeException Throws an exception if the SQL Native Type is not configured.
     */
    public static function echoFields(PDO $pdo, string $table, array $exclude_columns = []): string
    {
        // Query the first row of data from the table.
        $query = "SELECT * FROM $table LIMIT 0";
        $results = $pdo->query($query);

        // TODO: Determine what happens when a table contains ZERO rows?

        $code = [];

        // Loop through each column...
        for ($i = 0; $i < $results->columnCount(); $i++)
        {
            // Get the metadata for the current column.
            $col = $results->getColumnMeta($i);

            // EXAMPLE METADATA:
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

            // Check to determine if the column should be excluded...
            if (in_array($col["name"], $exclude_columns))
                continue;

            // Set come default values for the column.
            $col_name = $col["name"];
            $php_type = "";
            //$sql_type = "";

            // Check the SQL Native Type...
            switch ($col["native_type"]) {
                case "int4":
                    $php_type = "int";
                    //$sql_type = "integer";
                    break;

                case "varchar":
                    $php_type = "string";
                    //$sql_type = "varchar(" . ($col["precision"] - 4) . ")";
                    break;

                case "timestamp":
                    $php_type = "string";
                    //$sql_type = "timestamp(0)";
                    break;

                case "text":
                    $php_type = "string";
                    //$sql_type = "text";
                    break;

                case "bool":
                    $php_type = "bool";
                    //$sql_type = "boolean";
                    break;

                default:
                    throw new MapperUnknownTypeException("Missing MAPPER Type: {$col['native_type']}");
                    //die("Missing MAPPER Type: {$col['native_type']}");
            }

            // Convert the getter function name to camel-case.
            $get_name = Casing::snake_to_camel("get_" . $col_name);
            // Convert the setter function name to camel-case.
            $set_name = Casing::snake_to_camel("set_" . $col_name);

            // Generate the code...
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
                 * @param $php_type \$value
                 */
                public function $set_name($php_type \$value): void
                {
                    \$this->$col_name = \$value;
                }
            ");
        }

        // Join the code snippets and adjust the code indentation of the first line to align correctly in the template.
        $code = implode(PHP_EOL, $code);
        $code = Code::adjust_indent($code, 0, -16);

        // Finally, return the newly generated code!
        return $code;
    }

}