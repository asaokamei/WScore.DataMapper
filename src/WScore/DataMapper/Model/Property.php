<?php
namespace WScore\DataMapper;

/**
 * base class for dao's for database tables.
 * a Table Data Gateway pattern.
 *
 */
class Model_Property
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

    // +----------------------------------------------------------------------+
    //  Managing Object and Instances.
    // +----------------------------------------------------------------------+
    /**
     */
    public function __construct()
    {
    }

    /**
     * @param string $table
     * @param string $id_name
     */
    public function setTable( $table, $id_name )
    {
        $this->table   = $table;
        $this->id_name = $id_name;
    }
    /**
     * prepares restricted properties.
     */
    public function prepare( $definition, $relations )
    {
        $this->definition = $definition;
        $return = Model_Helper::prepare( $definition, $relations, $this->id_name );
        $this->properties = $return[ 'properties' ];
        $this->dataTypes  = $return[ 'dataTypes' ];
        $this->extraTypes = $return[ 'extraTypes' ];
        $this->protected  = $return[ 'protected' ];
    }

    // +----------------------------------------------------------------------+
    //  Managing Properties.
    // +----------------------------------------------------------------------+
    /**
     * checks if $name property exists in the model.
     *
     * @param string $name
     * @return bool
     */
    public function exists( $name ) {
        return array_key_exists( $name, $this->properties );
    }

    /**
     * checks if $name property is protected from automatically updated.
     * use this method to protect columns used for primary key or relations
     * from mass-assignment from form post.
     *
     * @param string $name
     * @return bool
     */
    public function isProtected( $name ) {
        return in_array( $name, $this->protected );
    }

    /**
     * @param array $data
     * @return array
     */
    public function updatedAt( $data ) {
        return Model_Helper::updatedAt( $data, $this->extraTypes );
    }

    /**
     * @param $data
     * @return array
     */
    public function createdAt( $data ) {
        return Model_Helper::createdAt( $data, $this->extraTypes );
    }

    /**
     * get label (property name for human readable form).
     *
     * @param $name
     * @return null
     */
    public function getLabel( $name ) {
        if( $this->exists( $name ) ) return $this->properties[ $name ][ 'name' ];
        return null;
    }

    /**
     * get full property of $name property.
     *
     * @param null $name
     * @return array|null
     */
    public function getProperty( $name=null ) {
        if( is_null( $name ) ) return $this->properties;
        if( $this->exists( $name ) ) return $this->properties[ $name ];
        return null;
    }

    // +----------------------------------------------------------------------+
    //  manipulating data
    // +----------------------------------------------------------------------+
    /**
     * restrict keys in the property list.
     *
     * @param array $data
     * @return array
     */
    public function restrict( $data )
    {
        if( empty( $data ) ) return $data;
        foreach( $data as $name => $val ) {
            if( !$this->exists( $name ) ) {
                unset( $data[ $name ] );
            }
        }
        return $data;
    }

    /**
     * protect data not to overwrite id or relation fields.
     *
     * @param $data
     * @param array $onlyTo
     * @return mixed
     */
    public function protect( $data, $onlyTo=array() )
    {
        if( empty( $data ) ) return $data;
        foreach( $data as $name => $val ) {
            if( $this->isProtected( $name ) ) {
                unset( $data[ $name ] );
            }
            elseif( !empty( $onlyTo ) && !in_array( $name, $onlyTo ) ) {
                unset( $data[ $name ] );
            }
        }
        return $data;
    }

    /**
     * @param null|string $name
     * @return array
     */
    public function getPropertyList( $name=null ) {
        $list = $this->protect( $this->getProperty() );
        return $list;
    }
}