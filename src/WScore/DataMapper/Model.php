<?php
namespace WScore\DataMapper;

use \WScore\DataMapper\Entity\EntityInterface;

/**
 * Model that governs how entity should be mapped to database (persistence) and to final view (presentation).
 * 
 * TODO: Model should be array world. maybe move entity stuff to EntityManager. 
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
     * return class from Pdo
     * @var \WScore\DataMapper\Entity\EntityInterface    
     */
    public $recordClassName = 'WScore\DataMapper\Entity\EntityGeneric';

    /**
     * temporary return class from Pdo.
     * @var string
     */
    public $entityClass = null;
    
    /**
     * define property and data type. from this data,
     * properties, extraTypes and dataTypes are generated.
     * definition = array(
     *   column => [ name, data_type, extra_info ],
     * )
     *
     * @var array
     */
    protected $definition = array();

    /**
     * relations settings
     * @var array                           
     */
    protected $relations  = array();

    /**
     * @Inject
     * @var \WScore\DataMapper\Model_Persistence
     */
    public $persistence;

    /**
     * @Inject
     * @var \WScore\DataMapper\Model_Presentation
     */
    public $presentation;

    /**
     * @Inject
     * @var \WScore\DataMapper\Model_Property
     */
    public $property;
    
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
        $this->prepare();
    }

    /**
     * prepares restricted properties.
     */
    public function prepare()
    {
        $this->property->setTable( $this->table, $this->id_name );
        $this->property->prepare( $this->definition, $this->relations );
        $this->persistence->setProperty( $this->property );
        $this->presentation->setProperty( $this->property );
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
        $stmt  = $this->persistence->fetch( $value, $column, $packed );
        $class = ( $this->entityClass ) ?: $this->recordClassName;
        $stmt->setFetchMode( \PDO::FETCH_CLASS, $class, array( $this ) );
        $class = null;
        return $stmt;
    }

    /**
     * @param array $data
     */
    public function update( $data )
    {
        $this->persistence->update( $data );
    }

    /**
     * @param array $data
     * @return string
     */
    public function insert( $data ) 
    {
        return $this->persistence->insertId( $data );
    }

    /**
     * @param array|string $data
     */
    public function delete( $data )
    {
        $id = is_array( $data ) ? $data[ $this->id_name ]: $data;
        $this->persistence->delete( $id );
    }

    /**
     * @param array $data
     * @return \WScore\DataMapper\Entity\EntityInterface
     */
    public function newEntity( $data=array() )
    {
        /** @var $record \WScore\DataMapper\Entity\EntityInterface */
        $class  = ( $this->entityClass ) ?: $this->recordClassName;
        $record = new $class( $this, EntityInterface::_ID_TYPE_VIRTUAL );
        $this->entityClass = null;
        if( !empty( $data ) ) {
            foreach( $data as $key => $val ) {
                $record->$key = $val;
            }
        }
        return $record;
    }
    // +----------------------------------------------------------------------+
    //  Managing Validation and Properties. 
    // +----------------------------------------------------------------------+
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
        if( $short ) $class = substr( $class, strrpos( '\\', $class ) );
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
        if( $name ) return Model_Helper::arrGet( $this->relations, $name );
        return $this->relations;
    }

}