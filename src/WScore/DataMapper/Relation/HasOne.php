<?php
namespace WScore\DataMapper\Relation;

use \WScore\DataMapper\Entity\EntityInterface;
use \WScore\DataMapper\Entity\Collection;

class HasOne implements RelationInterface
{
    /** @var string */
    public $name;
    
    /** @var array */
    public $info;

    /** @var EntityInterface */
    public $source;
    
    /**
     * @Inject
     * @var \WScore\DataMapper\EntityManager
     */
    protected $em;
    
    public function __construct( $name, $info )
    {
        $this->name = $name;
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

    private function findEntity( $by )
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
    public function set( $target )
    {
        $sourceColumn      = $this->info[ 'sourceColumn' ];
        $targetColumn      = $this->info[ 'targetColumn' ];
        $value = $target[ $targetColumn ];
        $this->source[ $sourceColumn ] = $value;
        $this->source[ $this->name ] = $target;
        return $this;
    }

    /**
     * adds relation between the source and the target entity.
     * for Many-to-many relation, it will *add* relation.
     *
     * @param EntityInterface|Collection $target
     * @return RelationInterface
     */
    public function add( $target )
    {
        return $this->set( $target );
    }

    /**
     * deletes the relation between the source and the target entity.
     *
     * @param EntityInterface|Collection $target
     * @return RelationInterface
     */
    public function del( $target = null )
    {
        $targetColumn      = $this->info[ 'targetColumn' ];
        $target[ $targetColumn ] = null;
        return $this;
    }
}
