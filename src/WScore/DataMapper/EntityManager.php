<?php
namespace WScore\DataMapper;

use \WScore\DiContainer\ContainerInterface;
use \WScore\DataMapper\Entity\EntityInterface;

class EntityManager
{
    /** @var \WScore\DataMapper\Model[] */
    protected $models = array();

    /**
     * @Inject 
     * @var ContainerInterface
     */
    protected $container;

    /** 
     * @Inject
     * @var \WScore\DataMapper\Entity\Collection 
     */
    protected $collection;

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
     * @return ContainerInterface
     */
    public function container() {
        return $this->container;
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
    public function getModel( $entity )
    {
        $modelName = $this->getModelName( $entity );
        $modelKey  = $modelName;
        if( substr( $modelKey, 0, 1 ) == '\\' ) $modelKey = substr( $modelKey, 1 );
        $modelKey = str_replace( '\\', '-', $modelKey );
        if( !array_key_exists( $modelKey, $this->models ) ) {
            $this->models[ $modelKey ] = $this->container->get( $modelName );
        }
        return $this->models[ $modelKey ];
    }

    /**
     * @param Entity\EntityInterface $entity
     * @return string
     */
    private function getClass( $entity ) {
        $class = is_string( $entity ) ? $entity : get_class( $entity );
        return $class;
    }

    /**
     * @param Entity\EntityInterface $entity
     * @return string
     */
    private function getModelName( $entity ) {
        if( is_string( $entity ) ) {
            /** @var $entity Entity\EntityAbstract  */
            $modelName = $entity::getStaticModelName();
        } else {
            $modelName = $entity->getModelName();
        }
        return $modelName;
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

    public function clear() {
        $this->collection->clear();
    }

    /**
     * get entity objects from EntityManager's repository.
     *
     * @param Entity\EntityInterface       $entity
     * @param string       $value
     * @param null|string  $column
     * @param bool|string  $packed
     * @return \WScore\DataMapper\Entity\EntityInterface[]
     */
    public function get( $entity, $value, $column=null, $packed=false )
    {
        $model     = $this->getModel( $entity );
        $modelName = $model->getModelName();
        $found = $this->collection->fetch( $modelName, $value, $column );
        if( $packed ) {
            $found->pack( $packed );
        }
        return $found;
    }
    
    /**
     * fetches entity object from database using models. 
     * 
     * @param Entity\EntityInterface|string       $entity
     * @param string       $value
     * @param null|string  $column
     * @param bool|string  $packed
     * @return EntityInterface[]
     */
    public function fetch( $entity, $value, $column=null, $packed=false )
    {
        $model = $this->getModel( $entity );
        $class = $this->getClass( $entity );
        $stmt  = $model->fetch( $value, $column, $packed );
        $stmt->setFetchMode( \PDO::FETCH_CLASS, $class, array( $model ) );
        $fetched = $stmt->fetchAll();
        $this->register( $fetched );
        return $fetched;
    }

    /**
     * constructs a *new* entity to be inserted into database. 
     * 
     * @param Entity\EntityInterface|string       $entity
     * @param array  $data
     * @return \WScore\DataMapper\Entity\EntityInterface
     */
    public function newEntity( $entity, $data=array() )
    {
        $model = $this->getModel( $entity );
        $class = $this->getClass( $entity );
        /** @var $record \WScore\DataMapper\Entity\EntityInterface */
        $record = new $class( $model, EntityInterface::_ID_TYPE_VIRTUAL );
        if( !empty( $data ) ) {
            foreach( $data as $key => $val ) {
                $record->$key = $val;
            }
        }
        $this->register( $record );
        return $record;
    }

    /**
     * saves or delete registered entities to/from database.
     */
    public function save()
    {
        if( empty( $this->collection->entities ) ) return;
        foreach( $this->collection->entities as $entity ) {
            $this->saveEntity( $entity );
        }
    }
    
    /**
     * saves or delete an entity to/from database.
     *
     * @param EntityInterface $entity
     */
    public function saveEntity( $entity )
    {
        $model  = $this->getModel( $entity );
        if( $entity->toDelete() ) {
            if( $entity->isIdPermanent() ) { // i.e. entity is from db.
                $model->delete( $entity->getId() );
            }
            // ignore if it is not permanent data; do not have to save. 
        }
        elseif( !$entity->isIdPermanent() ) { // i.e. entity is new. insert this.
            $data = $this->entityToArray( $entity );
            $id   = $model->insert( $data );
            $entity->setSystemId( $id );
        }
        else {
            $id   = $entity->getId();
            $data = $this->entityToArray( $entity );
            $model->update( $id, $data );
        }
    }

    /**
     * @param EntityInterface $entity
     * @return array
     */
    public function entityToArray( $entity ) {
        $data = get_object_vars( $entity );
        foreach( $data as $key => $val ) {
            if( substr( $key, 0, 1 ) === '_' ) {
                unset( $data[ $key ] );
            }
        }
        return $data;
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