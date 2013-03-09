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

    /** @ var \WScore\DataMapper\Relation */
    // protected $relation;

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
     * @return Entity\Collection
     */
    public function emptyCollection() {
        return $this->collection->collection();
    }
    // +----------------------------------------------------------------------+
    //  Managing Model.
    // +----------------------------------------------------------------------+
    /**
     * @param Entity\EntityInterface $entity
     * @return \WScore\DataMapper\Model
     */
    public function getModel( $entity )
    {
        if( is_string( $entity ) ) {
            /** @var $entity Entity\EntityAbstract  */
            $modelName = $entity::staticModelName();
        } else {
            $modelName = $entity->getModelName();
        }
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
    public function getClass( $entity ) {
        $class = is_string( $entity ) ? $entity : get_class( $entity );
        return $class;
    }
    // +----------------------------------------------------------------------+
    //  methods from original Model
    // +----------------------------------------------------------------------+
    /**
     * fetches entity object from database using models. 
     * 
     * @param Entity\EntityInterface       $entity
     * @param string       $value
     * @param null|string  $column
     * @param bool|string  $packed
     * @return \PdoStatement
     */
    public function fetch( $entity, $value, $column=null, $packed=false )
    {
        $model = $this->getModel( $entity );
        $class = $this->getClass( $entity );
        $stmt  = $model->fetch( $value, $column, $packed );
        $stmt->setFetchMode( \PDO::FETCH_CLASS, $class, array( $this ) );
        return $stmt;
    }

    /**
     * constructs a *new* entity to be inserted into database. 
     * 
     * @param Entity\EntityInterface       $entity
     * @param array  $data
     * @return \WScore\DataMapper\Entity\EntityInterface
     */
    public function newEntity( $entity, $data=array() )
    {
        /** @var $record \WScore\DataMapper\Entity\EntityInterface */
        $class = $this->getClass( $entity );
        $record = new $class( $this, EntityInterface::_ID_TYPE_VIRTUAL );
        if( !empty( $data ) ) {
            foreach( $data as $key => $val ) {
                $record->$key = $val;
            }
        }
        return $record;
    }
}