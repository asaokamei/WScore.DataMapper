<?php
namespace WScore\DataMapper\Relation;

use \WScore\DataMapper\Entity\EntityInterface;
use \WScore\DataMapper\Entity\Collection;

interface RelationInterface
{
    /**
     * @param EntityInterface $source
     * @return mixed
     */
    public function setSource( $source );
    /**
     * fetches related entities from database.
     *
     * @return RelationInterface
     */
    public function fetch();

    /**
     * gets related entities from EntityManager's repository.
     * i.e. no database access.
     * 
     * @return Collection
     */
    public function get();
    
    /**
     * sets relation between the source and the target entity, 
     * i.e. replaces the existing relations. 
     *
     * @param EntityInterface|Collection $target
     * @return RelationInterface
     */
    public function set( $target );

    /**
     * link the relation if relationship is not established.
     *
     * @return mixed
     */
    public function link();
}
