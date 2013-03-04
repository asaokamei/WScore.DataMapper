<?php
namespace WScore\DataMapper;

/**
 * base class for dao's for database tables.
 * a Table Data Gateway pattern.
 *
 */
class Property
{
    /** @var string                          name of database table          */
    protected $table;

    /** @var string                          name of primary key             */
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

    /** @var array                           property names as key => name   */
    protected $properties = array();

    /**
     * extra information on property.
     *    extraTypes = array(
     *      type => column name,
     *    );
     * where types are:
     *   - created_at: adds timestamps at insert.
     *   - updated_at: adds timestamps at update.
     *   - primaryKey: specifies primary key(s).
     *
     * @var array
     */
    protected $extraTypes = array();

    /**
     * store data types for each properties as in
     * prepared statement's bindValue as key => type
     *
     * for special case,
     *    !created_at => key
     *    !updated_at => key
     *
     * @var array
     */
    protected $dataTypes  = array();


    /** @var array                           protected properties            */
    protected $protected  = array();

    /**
     * prepares restricted properties.
     */
    public function prepare( $definition, $relations, $id_name )
    {
        $this->definition = $definition;
        $return = Model_Helper::prepare( $definition, $relations, $id_name );
        $this->properties = $return[ 'properties' ];
        $this->dataTypes  = $return[ 'dataTypes' ];
        $this->extraTypes = $return[ 'extraTypes' ];
        $this->protected  = $return[ 'protected' ];
    }
    
    public function exists( $name ) {
        return array_key_exists( $this->properties, $name );
    }
    
    public function isProtected( $name ) {
        return array_key_exists( $this->protected, $name );
    }
    
    public function updatedAt( & $values ) {
        Model_Helper::updatedAt( $values, $this->extraTypes );
    }

    public function createdAt( & $values ) {
        Model_Helper::createdAt( $values, $this->extraTypes );
    }
    
    public function getLabel( $name ) {
        if( $this->exists( $name ) ) return $this->properties[ $name ][ 'name' ];
        return null;
    }
    
    public function getProperty( $name=null ) {
        if( is_null( $name ) ) return $this->properties;
        if( $this->exists( $name ) ) return $this->properties[ $name ];
        return null;
    }
}