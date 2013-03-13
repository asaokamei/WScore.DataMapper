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
    
    protected function setRelation() {}
}
