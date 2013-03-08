<?php
namespace WScore\DataMapper;

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
     * @var \WScore\DataMapper\Model_PropertyCsv
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
        $this->persistence->setProperty( $this->property );
        $this->persistence->setTable( $this->table, $this->id_name );
        $this->presentation->setProperty( $this->property );
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
        return $stmt;
    }

    /**
     * @param array   $data
     * @param null    $extra
     */
    public function update( $data, $extra=null )
    {
        $this->persistence->update( $data, $extra );
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

    /**
     * @param $name
     * @return \WScore\Validation\Rules
     */
    public function getRule( $name ) {
        return $this->presentation->getRule( $name );
    }

    /**
     * @param $name
     * @return null|object
     */
    public function getSelector( $name ) {
        return $this->presentation->getSelector( $name );
    }

}