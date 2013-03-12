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
     * - source_column: column name of source. uses id if not set.
     * - target_type  : entity class name of target object.
     * - target_column: column name of target. uses id if not set.
     * - join_type    : entity class name of join table.
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
     * @param string          $name
     * @param EntityInterface $source
     * @param array           $info
     * @throws \RuntimeException
     */
    public function __construct( $name, $source, $info )
    {
        $this->name   = $name;
        $this->source = $source;

        if( !isset( $info[ 'target_type' ] ) ) {
            throw new \RuntimeException( 'target type not set. ' );
        }
        if( !isset( $info[ 'source_column' ] ) ) {
            $info[ 'source_column' ] = $source->getIdName();
        }
        if( !isset( $info[ 'target_column' ] ) ) {
            $info[ 'target_column' ] = $this->em->getIdName( $info[ 'target_type' ] );
        }
        $this->info = $info;
    }

    /**
     * @param EntityInterface $source
     * @return mixed
     */
    public function setSource( $source )
    {
        $this->source = $source;
    }

    /**
     * fetches related entities from database.
     *
     * @return RelationInterface
     */
    public function fetch() {}

    /**
     * gets related entities from EntityManager's repository.
     * i.e. no database access.
     *
     * @return Collection
     */
    public function get() {}

    protected function findEntity( $by )
    {
        $entityClass = $this->info[ 'targetEntity' ];
        $value       = $this->source[ $this->info[ 'sourceColumn' ] ];
        $column      = $this->info[ 'targetColumn' ];
        $target      = $this->em->$by( $entityClass, $value, $column );
        $this->source[ $this->name ] = $target;
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
     * adds relation between the source and the target entity.
     * for Many-to-many relation, it will *add* relation.
     *
     * @param EntityInterface|Collection $target
     * @return RelationInterface
     */
    public function add( $target ) {}

    /**
     * deletes the relation between the source and the target entity.
     *
     * @param EntityInterface|Collection $target
     * @return RelationInterface
     */
    public function del( $target = null ) {}
}
