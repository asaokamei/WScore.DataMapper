<?php
namespace WScore\DataMapper\Filter;

use WScore\DataMapper\Entity\EntityInterface;
use WScore\DataMapper\Model\Model;

abstract class RuleAbstract implements FilterInterface
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @var string
     */
    public $property;

    /**
     * @var string|array
     */
    public $value;

    /**
     * @var string
     */
    public $operator;

    /**
     * @param string $property
     * @param mixed  $value
     * @param string $operator
     */
    public function __construct( $property, $value, $operator='eq' )
    {
        $this->property = $property;
        $this->value    = $value;
        $this->operator = $operator;
    }

    /**
     * @param Model $model
     */
    public function setModel( $model )
    {
        $this->model = $model;
    }
    
    public function assignData( $data )
    {
        if( $this->operator === 'eq' ) {
            $data[ $this->property ] = $this->value;
        }
    }

    public function assignQuery()
    {
        $op = $this->operator;
        $this->model->query( false )->$op( $this->property, $this->value );
    }
}