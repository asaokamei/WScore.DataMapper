<?php
namespace WScore\DataMapper;

use \WScore\DataMapper\Entity\EntityInterface;

/**
 * Class EntityManager
 *
 * @package WScore\DataMapper
 * 
 * @singleton
 */
class EntityManager
{
    /**
     * @Inject
     * @var \WScore\DataMapper\ModelManager
     */
    protected $modelManager;
    
    /** 
     * @Inject
     * @var \WScore\DataMapper\Entity\Collection 
     */
    protected $collection;

    /**
     * @Inject
     * @var \WScore\DataMapper\Entity\Utils
     */
    protected $utils;
    
    /**
     * @Inject
     * @var \WScore\DataMapper\RelationManager
     */
    protected $relation;

    // +----------------------------------------------------------------------+
    //  construction and managing objects. 
    // +----------------------------------------------------------------------+
    /**
     */
    public function __construct() {
    }

    /**
     * @param EntityInterface[] $entities
     * @return Entity\Collection
     */
    public function newCollection( $entities=array() ) {
        return $this->collection->collection( $entities );
    }

    /**
     * @param EntityInterface|string $entity
     * @return string
     */
    public function getIdName( $entity ) {
        $model = $this->getModel( $entity );
        return $model->getIdName();
    }
    // +----------------------------------------------------------------------+
    //  Managing Entities and Models.
    // +----------------------------------------------------------------------+
    /**
     * @param Entity\EntityInterface|string $entity
     * @return \WScore\DataMapper\Model
     */
    public function getModel( $entity ) {
        return $this->modelManager->getModel( $entity );
    }

    /**
     * @param Entity\EntityInterface $entity
     * @return string
     */
    private function getClass( $entity ) {
        $class = is_string( $entity ) ? $entity : get_class( $entity );
        return $class;
    }

    // +----------------------------------------------------------------------+
    //  Methods for Entities. 
    // +----------------------------------------------------------------------+
    /**
     * @param Entity\EntityInterface|Entity\EntityInterface[] $entity
     * @return Entity\EntityInterface|Entity\EntityInterface[]
     */
    public function register( $entity )
    {
        if( is_array( $entity ) ) {
            foreach( $entity as $key => $ent ) {
                $entity[ $key ] = $this->register( $ent );
            }
            return $entity;
        }
        /** @var Entity\EntityInterface */
        $this->collection->add( $entity );
        return $entity;
    }

    /**
     * 
     */
    public function clear() {
        $this->collection->clear();
    }

    /**
     * get entity objects from EntityManager's repository.
     *
     * @param Entity\EntityInterface|string       $entity
     * @param string       $value
     * @param null|string  $column
     * @param bool|string  $packed
     * @return \WScore\DataMapper\Entity\Collection
     */
    public function get( $entity, $value, $column=null, $packed=false )
    {
        $model     = $this->getModel( $entity );
        $modelName = $model->getModelName();
        $found = $this->collection->fetch( $modelName, $value, $column );
        if( $packed ) {
            $found = $this->newCollection( $found );
            $found = $found->pack( $packed );
        }
        return $found;
    }

    /**
     * @param string $cenaId
     * @return bool|EntityInterface
     */
    public function getByCenaId( $cenaId ) {
        return $this->collection->getByCenaId( $cenaId );
    }
    
    /**
     * fetches entity object from database using models. 
     * 
     * @param Entity\EntityInterface|string       $entity
     * @param string       $value
     * @param null|string  $column
     * @param bool|string  $packed
     * @return \WScore\DataMapper\Entity\Collection
     */
    public function fetch( $entity, $value, $column=null, $packed=false )
    {
        $model = $this->getModel( $entity );
        $class = $this->getClass( $entity );
        if( $value instanceof \PDOStatement ) {
            $stmt = $value;
        } else {
            $stmt  = $model->fetch( $value, $column, $packed );
        }
        $stmt->setFetchMode( \PDO::FETCH_CLASS, $class, array( $model ) );
        $fetched = $stmt->fetchAll();
        $fetched = $this->register( $fetched );
        $fetched = $this->newCollection( $fetched );
        return $fetched;
    }

    /**
     * constructs a *new* entity to be inserted into database.
     *
     * @param Entity\EntityInterface|string $class
     * @param array                         $data
     * @param null|string                   $id
     * @return \WScore\DataMapper\Entity\EntityInterface
     */
    public function newEntity( $class, $data=array(), $id=null )
    {
        $model = $this->getModel( $class );
        $class = $this->getClass( $class );
        $entity = $this->utils->forge( $model, $class, $data, $id );
        $this->register( $entity );
        return $entity;
    }

    /**
     * saves or delete registered entities to/from database.
     */
    public function save()
    {
        if( empty( $this->collection->entities ) ) return;
        foreach( $this->collection->entities as $entity ) {
            $this->relation->link( $entity->getCenaId() );
            $this->saveEntity( $entity );
        }
    }
    
    /**
     * saves or delete an entity to/from database.
     *
     * @param EntityInterface $entity
     */
    public function saveEntity( $entity ) {
        $model  = $this->getModel( $entity );
        $this->utils->saveEntity( $model, $entity );
    }

    /**
     * @param EntityInterface $entity
     * @return array
     */
    public function entityToArray( $entity ) {
        return $this->utils->entityToArray( $entity );
    }

    /**
     * @param EntityInterface $source
     * @param string $name
     * @return Relation\RelationInterface
     */
    public function relation( $source, $name )
    {
        $model = $this->getModel( $source );
        $info  = $model->getRelationInfo( $name );
        return $this->relation->relation( $this, $name, $source, $info );
    }
}