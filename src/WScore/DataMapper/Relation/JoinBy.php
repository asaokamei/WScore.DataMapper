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
     * @return \WScore\DataMapper\Relation\JoinBy
     */
    public function set( $target )
    {
        $name = $this->name;
        // fetch target and join entities from database. 
        if( !$this->source->isIdPermanent() ) {
            // no need to fetch for new entity. there should be no join entity in the database. 
            $this->fetched = true;
        } elseif( !$this->fetched ) {
            // if not fetched yet, fetch entities from database. 
            $this->fetch();
        }
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
     *
     * @return \WScore\DataMapper\Relation\JoinBy
     */
    protected function setRelation()
    {
        $name = $this->name;
        $this->linked = true;
        if( !isset( $this->source->$name ) || empty( $this->source->$name ) ) return $this;
        if( !$this->source->isIdPermanent() ) {
            // at least source has to have permanent id. 
            $this->linked  = false; // not linked, yet. 
            return $this;
        }
        // get join entity
        $value  = $this->source[ $this->info[ 'source' ] ];
        $joiner = $this->em->get( $this->info[ 'by' ], $value, $this->info[ 'bySource' ] );
        $joiner = $this->em->newCollection( $joiner );
        // delete the join entity. 
        $joiner->toDelete( true );        
        // loop target entities. 
        foreach( $this->source->$name as $target ) 
        {
            /** @var $target EntityInterface */
            if( !$target->isIdPermanent() ) {
                $this->linked = false;
                continue;
            }
            $value = $target[ $this->info[ 'target' ] ];
            if( $join = $joiner->fetch( $this->info[ 'by' ], $value, $this->info[ 'byTarget' ] ) ) {
                // used in the join; mark the entity un-delete.
                $join[0]->toDelete( false ); 
            } else {
                // create new join for new target.
                $this->em->newEntity( $this->info[ 'by' ], array(
                    $this->info[ 'source' ] => $this->source[ $this->info[ 'source' ] ],
                    $this->info[ 'target' ] => $value,
                ) );
            }
        }
        return $this;
    }
}
