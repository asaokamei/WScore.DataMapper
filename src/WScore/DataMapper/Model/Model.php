<?php
namespace WScore\DataMapper\Model;

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
            return $this->persistence->query();
        }
        return $this->persistence->query;
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
        $this->filter->event( 'query', $this->persistence->query );
        $stmt  = $this->persistence->fetch( $value, $column, $packed );
        $stmt  = $this->filter->event( 'read', $stmt );
        return $stmt;
    }

    /**
     * @param array   $data
     * @param null    $extra
     */
    public function update( $data, $extra=null )
    {
        if( $extra ) {
            $id   = $data;
            $data = $extra;
        } else {
            $id = $data[ $this->id_name ];
        }
        $data = $this->filter->event( 'update', $data );
        $data = $this->filter->event( 'save',   $data );
        $this->persistence->update( $id, $data );
    }

    /**
     * @param array $data
     * @return string
     */
    public function insert( $data ) 
    {
        $data = $this->filter->event( 'insert', $data );
        $data = $this->filter->event( 'save',   $data );
        return $this->persistence->insertId( $data );
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
    public function property() {
        return $this->property->getProperty();
    }
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