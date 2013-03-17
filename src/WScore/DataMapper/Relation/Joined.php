<?php
namespace WScore\DataMapper\Relation;

use \WScore\DataMapper\Entity\EntityInterface;
use \WScore\DataMapper\Entity\Collection;

class Joined extends RelationAbstract
{

    /**
     * @param \WScore\DataMapper\EntityManager $em
     * @param string                           $name
     * @param EntityInterface                  $source
     * @param array                            $info
     * @throws \RuntimeException
     * @return \WScore\DataMapper\Relation\Joined
     */
    public function __construct( $em, $name, $source, $info )
    {
        $this->em     = $em;
        $this->name   = $name;
        $this->source = $source;

        if( !isset( $info[ 'entity' ] ) ) {
            throw new \RuntimeException( 'target type not set. ' );
        }
        if( !isset( $info[ 'source' ] ) || !$info[ 'source' ] ) {
            $info[ 'source' ] = $source->getIdName( $info[ 'entity' ] );
        }
        if( !isset( $info[ 'target' ] ) || !$info[ 'target' ] ) {
            $info[ 'target' ] = $info[ 'source' ];
        }
        $this->info = $info;
    }
    
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
        $name = $this->name;
        if( $target instanceof EntityInterface ) $target = array( $target );
        if( !isset( $this->source->$name ) ) $this->source->$name = $this->em->newCollection();
        /** @var $collection Collection */
        $collection = $this->source->$name;
        foreach( $target as $t ) {
            $collection->add( $t );
        }
        $this->setRelation();
        return $this;
    }

    /**
     * sets a relationship between source and target by setting
     * source column with target value.
     *
     * if the target's id is not permanent, sets linked flag to false.
     */
    protected function setRelation()
    {
        $name = $this->name;
        $this->linked = true;
        if( !isset( $this->source->$name ) || empty( $this->source->$name ) ) return;
        if( !$this->source->isIdPermanent() ) {
            $this->linked = false;
            return;
        }
        $sourceColumn       = $this->info[ 'source' ];
        $value              = $this->source[ $sourceColumn ];
        $targetColumn       = $this->info[ 'target' ];
        foreach( $this->source->$name as $t ) {
            $t[ $targetColumn ] = $value;
        }
        $this->linked = true;
    }
}
