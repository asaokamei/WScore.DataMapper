<?php
namespace WScore\DataMapper\Relation;

use \WScore\DataMapper\Entity\EntityInterface;
use \WScore\DataMapper\Entity\Collection;

class BelongsTo extends RelationAbstract
{
    /**
     * sets relation between the source and the target entity,
     * i.e. replaces the existing relations.
     * TODO: check for duplicated entities.
     *
     * @param EntityInterface $target
     * @return RelationInterface
     */
    public function set( $target )
    {
        $name = $this->info[ 'hasOne' ];
        if( $target instanceof EntityInterface ) $target = array( $target );
        if( !isset( $this->source->$name ) ) $this->source->$name = array();
        foreach( $target as $t ) {
            $this->em->relation( $t, $name )->set( $this->source );
            array_push( $this->source->$name, $t );
        }
        return $this;
    }

}
