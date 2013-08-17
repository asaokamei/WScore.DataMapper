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
    public $modelManager;
    
    /** 
     * @Inject
     * @var \WScore\DataMapper\Entity\Collection 
     */
    public $collection;

    /**
     * @Inject
     * @var \WScore\DataMapper\Entity\Utils
     */
    public $utils;
    
    /**
     * @Inject
     * @var \WScore\DataMapper\RelationManager
     */
    public $relation;

    /**
     * @var bool
     */
    protected $fetchByGet = false;

    /**
     * @var null|string
     */
    protected $entityNamespace = null;

    // +----------------------------------------------------------------------+
    //  construction and managing objects. 
    // +----------------------------------------------------------------------+
    /**
     */
    public function __construct() {
    }

    /**
     * @return ModelManager
     */
    public function mm() {
        return $this->modelManager;
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

    /**
     * @param string $entity
     * @return EntityQuery|\WScore\DbAccess\QueryInterface
     */
    public function query( $entity ) {
        return new EntityQuery( $this, $entity );
    }

    /**
     * @param string $namespace
     * @return $this
     */
    public function setNamespace( $namespace )
    {
        $this->mm()->setNamespace( $namespace );
        if( substr( $namespace, -1 ) !== '\\' ) $namespace .= '\\';
        $this->entityNamespace = $namespace;
        return $this;
    }
    // +----------------------------------------------------------------------+
    //  Managing Entities and Models.
    // +----------------------------------------------------------------------+
    /**
     * @param Entity\EntityInterface|string $entity
     * @return \WScore\DataMapper\Model\Model
     */
    public function getModel( $entity ) {
        return $this->modelManager->getModel( $entity );
    }

    /**
     * @param Entity\EntityInterface $entity
     * @throws \RuntimeException
     * @return string
     */
    private function getClass( $entity )
    {
        if( is_string( $entity ) ) {
            if( $this->entityNamespace && strpos( $entity, '\\' ) === false ) {
                $entity = $this->entityNamespace . $entity;
            }
            return $entity;
        }
        if( is_object( $entity ) ) {
            return get_class( $entity );
        }
        throw new \RuntimeException( 'entity is not a class nor object. ' );
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

    /** clears the entities collected in this entity manager.
     */
    public function clear() {
        $this->collection->clear();
    }

    /**
     * fetch only from the collection inside entity manager.
     *
     * @param bool $set
     * @return $this
     */
    public function fetchByGet( $set=true ) {
        $this->fetchByGet = $set;
        return $this;
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
        if( $this->fetchByGet ) {
            return $this->get( $entity, $value, $column, $packed );
        }
        $model = $this->getModel( $entity );
        $class = $this->getClass( $entity );
        if( !$value ) {
            return $this->newCollection();
        }
        if( $value instanceof \PDOStatement ) {
            $stmt = $value;
        } else {
            $stmt  = $model->fetch( $value, $column, $packed );
        }
        $stmt->setFetchMode( \PDO::FETCH_CLASS, $class, array( $model ) );
        $fetched = $stmt->fetchAll();
        $fetched = $this->register( $fetched );
        $this->utils->preserveOriginalValue( $fetched, $model );
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
     * tries to save all entities and relations based on
     * the number of unsaved entities and relations.
     *
     * @param bool $throw
     * @return bool
     * @throws \RuntimeException
     */
    public function save( $throw=true )
    {
        list( $togoLink, $togoEntity ) = list( $prevLink, $prevEntity ) = $this->doSave();

        while( $togoLink || $togoEntity ) {
            
            list( $togoLink, $togoEntity ) = $this->doSave();
            if( $togoLink === $prevLink && $togoEntity === $prevEntity ) {
                if( $throw ) {
                    throw new \RuntimeException( 'cannot save entity', 5001 );
                }
                break;
            }
        }
        return $togoLink === 0 && $togoEntity === 0;
    }

    /**
     * do save the entity. 
     * returns number of unsaved relation and number of entities *increased* 
     * during the save process (which may happen from many-to-many relation).
     * 
     * @return int
     */
    private function doSave()
    {
        $initEntityCount = $this->collection->count();
        $unsavedCount = 0;
        if( empty( $this->collection->entities ) ) return $unsavedCount;
        foreach( $this->collection->entities as $entity ) {
            $unsavedCount += $this->saveEntity( $entity );
        }
        $lastEntityCount = $this->collection->count();
        return array( $unsavedCount, $lastEntityCount - $initEntityCount );
    }
    
    /**
     * saves or delete an entity to/from database.
     *
     * @param EntityInterface $entity
     * @return int
     */
    public function saveEntity( $entity ) 
    {
        $cenaID = $entity->getCenaId();
        $unsavedCount = $this->relation->link( $cenaID );
        $model  = $this->getModel( $entity );
        if( $this->utils->isModified( $entity, $model ) ) {
            $this->utils->saveEntity( $model, $entity );
        }
        if( $unsavedCount ) {
            $unsavedCount = $this->relation->link( $cenaID );
        }
        return $unsavedCount;
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