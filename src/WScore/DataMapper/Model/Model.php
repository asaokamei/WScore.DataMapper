<?php
namespace WScore\DataMapper\Model;

use WScore\DataMapper\Entity\EntityInterface;
use \WScore\Selector\ElementAbstract;
use \WScore\Selector\ElementItemizedAbstract;

/**
 * Model that governs how entity should be mapped to database (persistence) and to final view (presentation).
 * 
 */
class Model
{
    /**
     * name of database table
     * @var string                          
     */
    protected $table;

    /**
     * name of primary key
     * @var string                          
     */
    protected $id_name;
    
    /**
     * @Inject
     * @var \WScore\DataMapper\Model\Persistence
     */
    public $persistence;

    /**
     * @Inject
     * @var \WScore\DataMapper\Model\Presentation
     */
    public $presentation;

    /**
     * @Inject
     * @var \WScore\DataMapper\Model\Validation
     */
    public $validation;
    
    /**
     * @Inject
     * @var \WScore\DataMapper\Model\PropertySet
     */
    public $property;

    /**
     * @Inject
     * @var \WScore\DataMapper\Model\ModelFilter
     */
    public $filter;

    /**
     * @Inject
     * @var \WScore\DataMapper\Entity\Utils
     */
    public $utils;

    /**
     * specify insert method to use, insertId or insertValue, in Persistence model.
     * @var string
     */
    protected $insertMethod = 'insertId';

    // +----------------------------------------------------------------------+
    //  Managing Object and Instances. 
    // +----------------------------------------------------------------------+
    /**
     * @param string $table
     * @param string $id_name
     */
    public function __construct( $table=null, $id_name=null )
    {
        if( $table   ) $this->table   = $table;
        if( $id_name ) $this->id_name = $id_name;
        $this->persistence->setProperty( $this->property );
        $this->persistence->setTable( $this->table, $this->id_name );
        $this->presentation->setProperty( $this->property );
        $this->validation->setProperty( $this->property );

        $this->filter->setModel( $this );
    }

    /**
     * @param bool $resetQuery
     * @return \WScore\DbAccess\Query
     */
    public function query( $resetQuery=true ) {
        if( $resetQuery ) {
            $query = $this->persistence->query();
            $this->filter->event( 'query', $query );
        } else {
            $query = $this->persistence->query;
        }
        return $query;
    }

    /**
     * @return \WScore\DbAccess\DbAccess
     */
    public function dbAccess() {
        return $this->persistence->query()->dbAccess();
    }

    /**
     * @return \WScore\DataMapper\Model\ModelFilter
     */
    public function filter() {
        return $this->filter;
    }
    // +----------------------------------------------------------------------+
    //  Persistence Methods.
    //  how about converting entity object to array here...
    // +----------------------------------------------------------------------+
    /**
     * @param string       $value
     * @param null|string  $column
     * @param bool|string  $packed
     * @return \PdoStatement
     */
    public function fetch( $value, $column=null, $packed=false )
    {
        $this->persistence->query();
        $stmt  = $this->persistence->fetch( $value, $column, $packed );
        $stmt  = $this->filter->event( 'read', $stmt );
        return $stmt;
    }

    /**
     * @param array|EntityInterface $entity
     * @return array
     * @throws \RuntimeException
     */
    public function convert( $entity )
    {
        if( is_array( $entity ) ) {
            $data = $entity;
            $data = $this->property->restrict( $data );
        }
        elseif( $entity instanceof EntityInterface )
        {
            $modified = false;
            $original = $entity->getEntityAttribute( 'originalValue' );
            $list = $this->property->getProperty();
            $list = array_keys( $list );
            $data = array();
            foreach( $list as $property ) {
                if( !$original || $entity[$property] !== $original[$property] ) {
                    $data[ $property ] = $entity->$property;
                    $type = $this->property( $property, 'type' );
                    if( !in_array( $type, [ 'updated_at', 'created_at' ] ) ) {
                        $modified = true;
                    }
                }
            }
            if( !$modified ) $data = array();
        }
        else {
            throw new \RuntimeException( 'entity not instance of EntityInterface nor an array. ' );
        }
        return $data;
    }

    /**
     * @param array|EntityInterface   $entity
     * @param null    $extra
     */
    public function update( $entity, $extra=null )
    {
        if( $extra ) {
            $id   = $entity;
            $entity = $extra;
        } else {
            $id = $entity[ $this->id_name ];
        }
        $entity = $this->filter->event( 'update', $entity );
        $entity = $this->filter->event( 'save',   $entity );
        if( $data = $this->convert( $entity ) ) {
            $this->persistence->update( $id, $data );
        }
    }

    /**
     * @param array|EntityInterface $entity
     * @return string
     */
    public function insert( $entity )
    {
        $entity = $this->filter->event( 'insert', $entity );
        $entity = $this->filter->event( 'save',   $entity );
        $data = $this->convert( $entity );
        $method = $this->insertMethod;
        return $this->persistence->$method( $data );
    }

    /**
     * @param array|string $data
     */
    public function delete( $data )
    {
        $this->filter->event( 'delete', $data );
        $id = is_array( $data ) ? $data[ $this->id_name ]: $data;
        $this->persistence->delete( $id );
    }
    // +----------------------------------------------------------------------+
    //  Managing Validation and Properties. 
    // +----------------------------------------------------------------------+
    /**
     * get various properties of given $column.
     *
     * @param null|string $column
     * @param null|string $key
     * @return array|bool
     */
    public function property( $column=null, $key=null ) {
        return $this->property->getProperty( $column, $key );
    }

    /**
     * remove protected data from $data, such as created_at column.
     * $data = array( column => value );
     *
     * @param $data
     * @return mixed
     */
    public function protect( $data )
    {
        return $this->property->protect( $data );
    }
    /**
     * returns name of property, if set.
     *
     * @param $name
     * @return null
     */
    public function propertyName( $name ) {
        return $this->property->getProperty( $name, 'title' );
    }

    /**
     * name of primary key.
     *
     * @return string
     */
    public function getIdName() {
        return $this->id_name;
    }

    /**
     * @param array $data
     * @return null|string
     */
    public function getId( $data ) {
        $idName = $this->id_name;
        $id = ( isset( $data[ $idName ] ) ) ? $data[ $idName ]: null;
        return $id;
    }

    /**
     * name of the model: i.e. class name.
     * @param bool $short
     * @return string
     */
    public function getModelName( $short=false ) {
        $class = get_called_class();
        if( $short && strpos( $class, '\\' ) !== false ) {
            $class = substr( $class, strrpos( $class, '\\' )+1 );
        }
        return $class;
    }

    /**
     * @return string
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * @param null|string $name
     * @return array
     */
    public function getRelationInfo( $name=null ) {
        $info = $this->property->getRelation( $name );
        $info[ 'type' ] = $info[ 'relation' ];
        return $info;
    }

    /**
     * @param $name
     * @return \WScore\Validation\Rules
     */
    public function getRule( $name ) {
        return $this->validation->getRule( $name );
    }

    /**
     * @param      $name
     * @param bool $forge
     * @return ElementAbstract|ElementItemizedAbstract
     */
    public function getSelector( $name, $forge=false ) {
        return $this->presentation->getSelector( $name, $forge );
    }

}