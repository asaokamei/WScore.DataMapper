<?php
namespace WScore\DataMapper\Relation;

use \WScore\DataMapper\Entity\EntityInterface;
use \WScore\DataMapper\Entity\Collection;

class RelationAbstract implements RelationInterface
{
    /** @var string */
    public $name;

    /**
     * information about the relationship.
     * - type   : type of relation (HasOne, BelongsTo, etc.)
     * - source : column name of source. uses id if not set.
     * - entity : entity class name of target object.
     * - target : column name of target. uses id if not set.
     * - hasOne : target's HasOne relation name.
     * - joinBy : entity class name of join table.
     *
     * @var array
     */
    public $info;

    /** @var EntityInterface */
    public $source;

    /** @var bool */
    public $linked = true;

    /**
     * @Inject
     * @var \WScore\DataMapper\EntityManager
     */
    protected $em;

    /**
     * @param \WScore\DataMapper\EntityManager $em
     * @param string                           $name
     * @param EntityInterface                  $source
     * @param array                            $info
     * @throws \RuntimeException
     * @return \WScore\DataMapper\Relation\RelationAbstract
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

    /**
     * fetches related entities from database.
     *
     * @return EntityInterface[]
     */
    public function fetch()
    {
        return $this->findEntity( 'fetch' );
    }

    /**
     * gets related entities from EntityManager's repository.
     * i.e. no database access.
     *
     * @return EntityInterface[]
     */
    public function get()
    {
        return $this->findEntity( 'get' );
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
        $class  = $this->info[ 'entity' ];
        $value  = $this->source[ $this->info[ 'source' ] ];
        $column = $this->info[ 'target' ];
        $target = $this->em->$by( $class, $value, $column );
        $this->source[ $this->name ] = $target;
        return $target;
    }

    /**
     * sets relation between the source and the target entity,
     * i.e. replaces the existing relations.
     *
     * @param EntityInterface|Collection $target
     * @return RelationInterface
     */
    public function set( $target ) {}

    /**
     * nothing to do, except for HasOne relationship.
     *
     * @return void
     */
    public function link() {}
}
