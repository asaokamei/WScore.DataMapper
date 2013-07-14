<?php
namespace WScore\DataMapper\Filter;

class ModelFilter extends FilterAbstract
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
    
    public function __construct( $property, $value, $operator='eq' )
    {
        $this->property = $property;
        $this->value    = $value;
        $this->operator = $operator;
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