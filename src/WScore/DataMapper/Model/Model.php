<?php
namespace WScore\DataMapper\Model;

use \WScore\Selector\ElementAbstract;
use \WScore\Selector\ElementItemizedAbstract;
use \WScore\DataMapper\Filter\FilterInterface;
use WScore\DataMapper\Filter\CreatedAt;
use WScore\DataMapper\Filter\UpdatedAt;

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
     * @var \WScore\DataMapper\Model\PropertyCsv
     */
    public $property;

    /**
     * @var FilterInterface[][]|\Closure[][]
     */
    public $filters = array();

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

        $created = new CreatedAt();
        $updated = new UpdatedAt();
        $this->addFilter( $created, 'insert' );
        $this->addFilter( $updated, 'insert' );
        $this->addFilter( $updated, 'update' );
    }

    /**
     * prepares restricted properties.
     */
    public function prepareByDefinition( $definition, $relation )
    {
        $this->property->setTable( $this->table, $this->id_name );
        $this->property->prepare( $definition, $relation );
    }

    /**
     * @param string $csv
     */
    public function prepareByCsv( $csv )
    {
        $this->property->prepare( $csv );
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
     * @param FilterInterface|\Closure $filter
     * @internal param $event
     * @return $this
     */
    public function addFilter( $filter )
    {
        $events = func_get_args();
        array_shift( $events );
        foreach( $events as $event ) {
            $this->filters[ $event ][] = $filter;
        }
        return $this;
    }
    
    /**
     * @param array $data
     * @return array
     * @internal param $event
     */
    public function filter( $data )
    {
        $events = func_get_args();
        array_shift( $events );
        foreach( $events as $event ) {
            
            if( isset( $this->filters[ $event ] ) ) {
                
                $method = 'on' . ucwords( $event );
                foreach( $this->filters[ $event ] as $filter ) {
                    
                    if( $filter instanceof FilterInterface ) {
                        $filter->setModel( $this );
                    }
                    if( is_callable( $filter ) ) {
                        $filter( $data );
                    } elseif( method_exists( $filter, $method ) ) {
                        $filter->$method( $data );
                    }
                }
            }
        }
        return $data;
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
        $this->filter( null, 'fetch' );
        $stmt  = $this->persistence->fetch( $value, $column, $packed );
        $stmt  = $this->filter( $stmt, 'retrieve' );
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
        $data = $this->filter( $data, 'update' );
        $data = $this->filter( $data, 'save' );
        $this->persistence->update( $id, $data );
    }

    /**
     * @param array $data
     * @return string
     */
    public function insert( $data ) 
    {
        $data = $this->filter( $data, 'insert' );
        $data = $this->filter( $data, 'save' );
        return $this->persistence->insertId( $data );
    }

    /**
     * @param array|string $data
     */
    public function delete( $data )
    {
        $this->filter( $data, 'delete' );
        $id = is_array( $data ) ? $data[ $this->id_name ]: $data;
        $this->persistence->delete( $id );
    }
    // +----------------------------------------------------------------------+
    //  Managing Validation and Properties. 
    // +----------------------------------------------------------------------+
    public function property() {
        return $this->property->property();
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
        return $this->property->getLabel( $name );
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
        return $this->property->getRelationInfo( $name );
    }

    /**
     * @param $name
     * @return \WScore\Validation\Rules
     */
    public function getRule( $name ) {
        return $this->presentation->getRule( $name );
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