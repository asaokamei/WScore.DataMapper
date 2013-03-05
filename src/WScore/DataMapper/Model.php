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
        $this->persistence->setProperty( $this->property );
    }

    /**
     * prepares restricted properties.
     */
    public function prepare()
    {
        $this->property->setTable( $this->table, $this->id_name );
        $this->property->prepare( $this->definition, $this->relations );
    }

    // +----------------------------------------------------------------------+
    //  Persistence Methods.
    //  how about converting entity object to array here...
    // +----------------------------------------------------------------------+
    public function fetch( $value, $column=null )
    {
        return $this->persistence->fetch( $value, $column );
    }
    
    public function update( $entity )
    {
        $values = $this->entityToArray( $entity );
        return $this->persistence->update( $values );
    }
    
    public function insert( $entity ) 
    {
        $values = $this->entityToArray( $entity );
        return $this->persistence->insertId( $values );
    }
    // +----------------------------------------------------------------------+
    //  Managing Validation and Properties. 
    // +----------------------------------------------------------------------+
    /**
     * returns name of property, if set.
     *
     * @param $var_name
     * @return null
     */
    public function propertyName( $var_name ) {
        return $this->property->getLabel( $var_name );
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
     * @param \WScore\DataMapper\Entity_Interface $entity
     * @return null|string
     */
    public function getId( $entity ) {
        $idName = $this->id_name;
        $id = ( isset( $entity->$idName ) ) ? $entity->$idName: null;
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

    /**
     * TODO: restrict to property here. also extract only the updated value.
     *
     * @param array|Entity_Interface $entity
     * @return array
     */
    public function entityToArray( $entity ) {
        if( is_object( $entity ) ) {
            return get_object_vars( $entity );
        }
        return $entity;
    }
}