<?php
namespace WScore\DataMapper\Relation;

use \WScore\DataMapper\Entity\EntityInterface;
use \WScore\DataMapper\Entity\Collection;

class JoinBy extends RelationAbstract
{
    /** @var bool */
    private $fetched = false;

    /**
     * @param \WScore\DataMapper\EntityManager $em
     * @param string                           $name
     * @param EntityInterface                  $source
     * @param array                            $info
     * @throws \RuntimeException
     * @return \WScore\DataMapper\Relation\JoinBy
     */
    public function __construct( $em, $name, $source, $info )
    {
        $this->em     = $em;
        $this->name   = $name;
        $this->source = $source;

        if( !isset( $info[ 'entity' ] ) ) {
            throw new \RuntimeException( 'target type not set. ' );
        }
        if( !isset( $info[ 'by' ] ) ) {
            throw new \RuntimeException( 'target type not set. ' );
        }
        if( !isset( $info[ 'source' ] ) || !$info[ 'source' ] ) {
            $info[ 'source' ] = $source->getIdName( $info[ 'entity' ] );
        }
        if( !isset( $info[ 'bySource' ] ) || !$info[ 'bySource' ] ) {
            $info[ 'bySource' ] = $info[ 'entity' ];
        }
        if( !isset( $info[ 'target' ] ) || !$info[ 'target' ] ) {
            $info[ 'target' ] = $info[ 'source' ];
        }
        if( !isset( $info[ 'byTarget' ] ) || !$info[ 'byTarget' ] ) {
            $info[ 'byTarget' ] = $info[ 'target' ];
        }
        $this->info = $info;
    }

    /**
     * fetches related entities from database.
     *
     * @return EntityInterface[]
     */
    public function fetch()
    {
        $this->fetched = true;
        return $this->findEntity( 'fetch' );
    }

    /**
     * a utility method for finding target entity object from EM.
     * specify method, get or fetch, to use for retrieval.
     *
     * @param $by
     * @return EntityInterface[]
     */
    protected function findEntity( $by )
    {
        // find entity for join.
        $class  = $this->info[ 'by' ];
        $value  = $this->source[ $this->info[ 'source' ] ];
        $joiner = $this->em->$by( $class, $value, $this->info[ 'bySource' ] );
        // extract the column for join. 
        $packed = array();
        foreach( $joiner as $joins ) {
            $packed[] = $joins[ $this->info[ 'byTarget' ] ];
        }
        // get/fetch the target. 
        $class  = $this->info[ 'entity' ];
        $target = $this->em->$by( $class, $packed, $this->info[ 'target' ] );
        // found targets.
        $this->source[ $this->name ] = $target;
        return $target;
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
        if( !isset( $this->source->$name ) ) $this->source->$name = array();
        foreach( $target as $t ) {
            array_push( $this->source->$name, $t );
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
            // at least source has to have permanent id. 
            $this->linked = false;
            return;
        }
        $sourceColumn       = $this->info[ 'source' ];
        $value              = $this->source[ $sourceColumn ];
        $targetColumn       = $this->info[ 'target' ];
        foreach( $this->source->$name as $t ) {
            // find join entities by get. 
        }
        $this->linked = true;
    }
}
