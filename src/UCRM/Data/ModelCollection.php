<?php
declare(strict_types=1);

namespace UCRM\Data;

use UCRM\Data\Exceptions\ModelCollectionTypeException;



/**
 * Class ModelCollection
 * @package UCRM\Data
 */
final class ModelCollection
{
    /** @var Model[] $models The backing array to store the collection of Models. */
    protected $models = [];


    /**
     * @param Model[] $models An initial array of Models to store in this collection.
     * @throws ModelCollectionTypeException Throws an exception if any of the elements are not of type Model.
     */
    public function __construct(array $models = [])
    {
        foreach($models as $model)
        {
            if($model instanceof Model)
                $this->models[] = $model;
            else
                throw new ModelCollectionTypeException("ModelCollection expects only Model objects!");
        }
    }



    /**
     * @param Model $model
     * @return ModelCollection
     */
    public function push(Model $model): ModelCollection
    {
        $this->models[] = $model;
        return $this;
    }

    public function pop(int $quantity = 1): Model
    {


    }




}