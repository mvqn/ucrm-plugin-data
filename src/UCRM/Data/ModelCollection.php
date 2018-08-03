<?php
declare(strict_types=1);

namespace UCRM\Data;
//require __DIR__ . "/../../../vendor/autoload.php";

use PDO;
use ArrayAccess;
use Coder\{Code, Casing};


class ModelCollection implements ArrayAccess
{
    protected $models = [];



    public function __construct(array $models)
    {
        foreach($models as $model)
        {
            if($model instanceof Model)
                $this->models[] = $model;
            else
                throw new DatabaseQueryException("ModelCollection expects only Model objects!");
        }
    }


    public function all(): ModelCollection
    {
        return new ModelCollection($this->models);
    }


    public function where(string $column, $value): ModelCollection
    {
        $collection = [];

        foreach($this->models as $model)
        {
            if($model->$column == $value)
                $collection[] = $model;
        }

        return new ModelCollection($collection);
    }







    public function __toString()
    {
        $array = [];

        foreach($this->models as $model)
            $array[] = (string)$model;

        $json = "[".implode(",", $array)."]\n";

        return $json;
    }


    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->models[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return isset($this->models[$offset]) ? $this->models[$offset] : null;
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->models[] = $value;
        } else {
            $this->models[$offset] = $value;
        }
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->models[$offset]);
    }
}