<?php
namespace WScore\DataMapper\Filter;

use WScore\DataMapper\Entity\EntityInterface;
use WScore\DataMapper\Model\Model;

abstract class RuleAbstract extends FilterAbstract
{
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

    public function onFetch( $data )
    {
        if( $this->operator === 'eq' ) {
            $data[ $this->property ] = $this->value;
        }
    }

    /**
     * @param $query \WScore\DbAccess\Query
     */
    public function onQuery( $query )
    {
        $op = $this->operator;
        $query->col( $this->property )->$op( $this->value );
    }
}