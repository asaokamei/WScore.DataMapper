<?php
namespace WScore\DataMapper\Filter;

use WScore\DataMapper\Entity\EntityInterface;
use WScore\DataMapper\Model\Model;

abstract class FilterAbstract implements FilterInterface
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @param Model $model
     */
    public function setModel( $model )
    {
        $this->model = $model;
    }
}