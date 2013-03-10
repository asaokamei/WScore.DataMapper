<?php
namespace WScore\DataMapper\Model;

/**
 * base class for dao's for database tables.
 * a Table Data Gateway pattern.
 *
 */
class PropertyAbstract
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
     * property names as key => name
     * @var array
     */
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
     * protected properties
     * @var array
     */
    protected $protected  = array();

    /**
     * for validation of inputs
     * @var array
     */
    protected $validators = array();

    /**
     * @param string $table
     * @param string $id_name
     */
    public function setTable( $table, $id_name )
    {
        $this->table   = $table;
        $this->id_name = $id_name;
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
        return Helper::updatedAt( $data, $this->extraTypes );
    }

    /**
     * @param $data
     * @return array
     */
    public function createdAt( $data ) {
        return Helper::createdAt( $data, $this->extraTypes );
    }

    /**
     * get label (property name for human readable form).
     *
     * @param $name
     * @return null
     */
    public function getLabel( $name ) {
        if( $this->exists( $name ) ) return $this->properties[ $name ][ 'label' ];
        return null;
    }

    /**
     * @param $name
     * @return null
     */
    public function getPattern( $name ) {
        $info = Helper::arrGet( $this->validators, $name );
        return isset( $info[ 'pattern' ] ) ? $info[ 'pattern' ]: null;
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


}
