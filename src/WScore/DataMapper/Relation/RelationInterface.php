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
     * adds relation between the source and the target entity.
     * for Many-to-many relation, it will *add* relation. 
     * 
     * @param EntityInterface|Collection $target
     * @return RelationInterface
     */
    public function add( $target );

    /**
     * deletes the relation between the source and the target entity.
     *
     * @param EntityInterface|Collection $target
     * @return RelationInterface
     */
    public function del( $target=null );
}
