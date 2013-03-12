<?php
namespace WScore\DataMapper\Relation;

use \WScore\DataMapper\Entity\EntityInterface;
use \WScore\DataMapper\Entity\Collection;

class BelongsTo extends RelationAbstract
{
    /**
     * fetches related entities from database.
     *
     * @return RelationInterface
     */
    public function fetch()
    {
        $this->findEntity( 'fetch' );
        return $this;
    }

    /**
     * gets related entities from EntityManager's repository.
     * i.e. no database access.
     *
     * @return Collection
     */
    public function get()
    {
        $this->findEntity( 'get' );
        return $this;
    }

    /**
     * sets relation between the source and the target entity,
     * i.e. replaces the existing relations.
     *
     * @param EntityInterface $target
     * @return RelationInterface
     */
    public function set( $target )
    {
        $name = $this->info[ 'target_hasOne' ];
        if( $target instanceof EntityInterface ) $target = array( $target );
        foreach( $target as $t ) {
            $this->em->relation( $t, $name )->set( $this->source );
        }
        return $this;
    }

}
