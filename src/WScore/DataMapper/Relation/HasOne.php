<?php
namespace WScore\DataMapper\Relation;

use \WScore\DataMapper\Entity\EntityInterface;
use \WScore\DataMapper\Entity\Collection;

class HasOne extends RelationAbstract
{

    /**
     * @param \WScore\DataMapper\EntityManager $em
     * @param string                           $name
     * @param EntityInterface                  $source
     * @param array                            $info
     * @throws \RuntimeException
     * @return \WScore\DataMapper\Relation\HasOne
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
            $info[ 'source' ] = $this->em->getIdName( $info[ 'entity' ] );
        }
        if( !isset( $info[ 'target' ] ) || !$info[ 'target' ] ) {
            $info[ 'target' ] = $info[ 'source' ];
        }
        $this->info = $info;
    }
    
    public function get()
    {
        $target = parent::get();
        $this->singleRelation();
        return $target;
    }

    public function fetch()
    {
        $target = parent::fetch();
        $this->singleRelation();
        return $target;
    }
    
    private function singleRelation()
    {
        $name = $this->name;
        if( !is_array( $this->source->$name ) ) return;
        $temp = $this->source->$name;
        $this->source->$name = $temp[0];
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
        $this->source[ $this->name ] = $target;
        $this->setRelation();
        return $this;
    }

    /**
     * sets a relationship between source and target by setting
     * source column with target value.
     *
     * if the target's id is not permanent, sets linked flag to false.
     */
    private function setRelation()
    {
        $name = $this->name;
        /** @var $target EntityInterface */
        $target = $this->source->$name;
        if( !$target->isIdPermanent() ) {
            $this->linked = false;
            return;
        }
        $sourceColumn      = $this->info[ 'source' ];
        $targetColumn      = $this->info[ 'target' ];
        $value = $target[ $targetColumn ];
        $this->source[ $sourceColumn ] = $value;
        $this->linked = true;
    }

    /**
     * link the relation if relationship is not established.
     *
     * @return mixed
     */
    public function link()
    {
        if( !$this->linked ) {
            $this->setRelation();
        }
    }
}
