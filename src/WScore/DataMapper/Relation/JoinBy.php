<?php
namespace WScore\DataMapper\Relation;

use \WScore\DataMapper\Entity\EntityInterface;
use \WScore\DataMapper\Entity\Collection;

class JoinBy extends RelationAbstract
{
    /** @var bool */
    private $fetched = false;
    
    /** @var \WScore\DataMapper\Entity\Collection */
    private $joiner;

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
            $info[ 'bySource' ] = $info[ 'source' ];
        }
        if( !isset( $info[ 'target' ] ) || !$info[ 'target' ] ) {
            $info[ 'target' ] = $this->em->getIdName( $info[ 'entity' ] );
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
        /** @var $join Collection */
        $join = $this->em->$by( $class, $value, $this->info[ 'bySource' ] );
        $this->joiner = $join;
        // extract the column for join. 
        $packed = $join->pack( $this->info[ 'byTarget' ] );
        // get/fetch the target. 
        if( $packed && !empty( $packed ) ) {
            $class  = $this->info[ 'entity' ];
            $target = $this->em->$by( $class, $packed, $this->info[ 'target' ] );
        } else {
            $target = $this->em->newCollection();
        }
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
        $linked = $this->em->newCollection();
        foreach( $target as $t ) {
            if( $t ) $linked->add( $t );
        }
        $this->source->$name = $linked;
        $this->setRelation();
        return $this;
    }
    
    public function add( $target )
    {
        $name = $this->name;
        if( $target instanceof EntityInterface ) $target = array( $target );
        if( !isset( $this->source->$name ) ) {
            $link = $this->em->newCollection();
        } else {
            $link = $this->source->$name;
        }
        foreach( $target as $t ) {
            if( $t ) $link->add( $t );
        }
        $this->source->$name = $link;
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
        if( !$this->source->isIdPermanent() ) {
            // at least source has to have permanent id. 
            $this->linked  = false; // not linked, yet. 
            return $this;
        }
        // delete the join entity. 
        if( isset( $this->joiner ) ) {
            $this->joiner->toDelete( true );
        } else {
            $this->joiner = $this->em->newCollection();
        }
        
        // loop target entities. 
        foreach( $this->source->$name as $target ) 
        {
            /** @var $target EntityInterface */
            if( !$target->isIdPermanent() ) {
                $this->linked = false;
                continue;
            }
            if( $join = $this->getJoin( $target ) ) {
                // used in the join; mark the entity un-delete.
                $join->toDelete( false ); 
            } else {
                // create new join for new target.
                $join = $this->em->newEntity( $this->info[ 'by' ], array(
                    $this->info[ 'source' ] => $this->source[ $this->info[ 'source' ] ],
                    $this->info[ 'target' ] => $target[ $this->info[ 'target' ] ],
                ) );
                $this->joiner->add( $join );
            }
        }
        return $this;
    }

    /**
     * @param EntityInterface $target
     * @return \WScore\DataMapper\Entity\EntityInterface|null
     */
    public function getJoin( $target )
    {
        if( !isset( $this->joiner ) ) return null;
        $joinModel     = $this->em->getModel( $this->info[ 'by' ] );
        $joinModelName = $joinModel->getModelName();
        $value = $target[ $this->info[ 'target' ] ];
        $join = $this->joiner->fetch( $joinModelName, $value, $this->info[ 'byTarget' ] );
        if( $join ) return $join[0];
        return null;
    }
}
