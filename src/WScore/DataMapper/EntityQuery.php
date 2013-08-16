<?php
namespace WScore\DataMapper;

use WScore\DataMapper\Filter\FilterInterface;
use WScore\DbAccess\QueryInterface;

/**
 * Class EntityQuery
 *
 * @package WScore\DataMapper
 * 
 */
class EntityQuery
{
    /**
     * @var EntityManager
     */
    public $em;

    /**
     * @var string
     */
    public $entity;

    /**
     * @var Model\Model
     */
    public $model;

    /**
     * @var \WScore\DbAccess\QueryInterface
     */
    public $query;

    /**
     * @param EntityManager $em
     * @param string        $entity
     * @return \WScore\DataMapper\EntityQuery
     */
    public function __construct( $em, $entity )
    {
        $this->em     = $em;
        $this->entity = $entity;
        $this->model  = $em->getModel( $entity );
        $this->query  = $this->model->query();
    }

    /**
     * @return Entity\Collection
     */
    public function fetch()
    {
        $stmt = $this->query->select();
        return $this->em->fetch( $this->entity, $stmt );
    }

    /**
     * @param string $name
     * @param array  $args
     * @return $this
     */
    public function __call( $name, $args )
    {
        $returned = $this;
        $noExec = array( 'select', 'update', 'insert', 'delete', 'exec', 'fetchAll', 'fetchNumRow', 'table',  );
        if( in_array( strtolower( $name ), $noExec ) ) {
            // do nothing. 
        }
        elseif( method_exists( $this->query, $name ) ) {
            $returned = call_user_func_array( array( $this->query, $name ), $args );
        }
        if( $returned instanceof QueryInterface ) {
            return $this;
        }
        return $returned;
    }

    /**
     * @param Filter\FilterInterface $rule
     * @return $this|\WScore\DbAccess\Query
     */
    public function rule( $rule )
    {
        if( $rule instanceof FilterInterface && method_exists( $rule, 'onQuery' ) ) {
            $rule->apply( 'query', $this );
        }
        return $this;
    }

}