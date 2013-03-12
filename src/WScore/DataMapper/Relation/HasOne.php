<?php
namespace WScore\DataMapper\Relation;

use \WScore\DataMapper\Entity\EntityInterface;
use \WScore\DataMapper\Entity\Collection;

class HasOne extends RelationAbstract
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
        $this->source[ $this->name ] = $target;
        $this->setRelation();
        return $this;
    }

    private function setRelation()
    {
        $name = $this->name;
        /** @var $target EntityInterface */
        $target = $this->source->$name;
        if( !$target->isIdPermanent() ) {
            $this->linked = false;
            return;
        }
        $sourceColumn      = $this->info[ 'sourceColumn' ];
        $targetColumn      = $this->info[ 'targetColumn' ];
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
