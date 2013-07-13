<?php
namespace WScore\DataMapper\Filter;

abstract class FilterAbstract implements FilterInterface
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @param \WScore\DataMapper\Entity\EntityInterface $entity
     */
    public function onSave( $entity )
    {
    }

    /**
     * @param \WScore\DbAccess\Query $query
     */
    public function onQuery( $query )
    {
        
    }
    
    /**
     * @param \WScore\DbAccess\Query $query
     */
    public function onFetch( $query )
    {
        
    }

    /**
     * @param \WScore\DataMapper\Entity\EntityInterface $entity
     */
    public function onApply( $entity )
    {
        
    }

    /**
     * @param \WScore\DataMapper\Entity\EntityInterface $entity
     */
    public function onValidate( $entity )
    {
        
    }
    /**
     * @param Model $model
     */
    public function setModel( $model )
    {
        $this->model = $model;
    }
}