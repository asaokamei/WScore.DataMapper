<?php
namespace WScore\DataMapper;

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
     * @var \WScore\DataMapper\Model_Persistence
     */
    public $persistence;

    /**
     * @var \WScore\DataMapper\Model_Presentation
     */
    public $presentation;

    /**
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
     * @return array
     */
    public function fetch( $value, $column=null, $packed=false )
    {
        return $this->persistence->fetch( $value, $column, $packed );
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
     * @return string
     */
    public function getModelName() {
        return get_called_class();
    }

    /**
     * @return string
     */
    public function getTable() {
        return $this->table;
    }

    public function getRelationInfo( $name=null ) {
        if( $name ) return Model_Helper::arrGet( $this->relations, $name );
        return $this->relations;
    }
}