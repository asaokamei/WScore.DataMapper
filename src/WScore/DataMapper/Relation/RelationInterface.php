<?php
namespace WScore\DataMapper\Relation;

use \WScore\DataMapper\Entity\EntityInterface;
use \WScore\DataMapper\Entity\Collection;

interface RelationInterface
{
    /**
     * fetches related entities from database.
     *
     * @return EntityInterface[]
     */
    public function fetch();

    /**
     * gets related entities from EntityManager's repository.
     * i.e. no database access.
     * 
     * @return EntityInterface[]
     */
    public function get();
    
    /**
     * sets relation between the source and the target entity, 
     * i.e. replaces the existing relations. 
     *
     * @param EntityInterface|EntityInterface[] $target
     * @return RelationInterface
     */
    public function set( $target );

    /**
     * adds relation. works just like set except for JoinBy; 
     * it will simply adds target to the relation.
     * 
     * @param EntityInterface|EntityInterface[] $target
     * @return mixed
     */
    public function add( $target );
    
    /**
     * check if relation is already linked. 
     * 
     * @return bool
     */
    public function isLinked();
    
    /**
     * link the relation if relationship is not established.
     *
     * @return mixed
     */
    public function link();
}
