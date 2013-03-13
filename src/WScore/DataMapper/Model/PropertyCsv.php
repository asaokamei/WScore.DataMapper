<?php
namespace WScore\DataMapper\Model;

/**
 * base class for dao's for database tables.
 * a Table Data Gateway pattern.
 *
 */
class PropertyCsv extends PropertyAbstract implements PropertyInterface
{
    /**
     * define property and data type. from this data,
     * properties, extraTypes and dataTypes are generated.
     * definition = array(
     *   column => [ name, data_type, extra_info ],
     * )
     *
     * @var array
     */
    public $definition = array();

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
    public $dataTypes  = array();

    /**
     * for selector construction. to use with WScore\Html\Selector,
     * $selectors = array(
     *  name => [ 'Selector', style, option text, [
     *      'items' => [ [ val1, str1 ], [ val2, str2 ], ... ],
     *      'filter' => function(){}
     *  ] ],
     * )
     *
     * @var array
     */
    public $selectors  = array();

    // +----------------------------------------------------------------------+
    //  Managing Object and Instances.
    // +----------------------------------------------------------------------+
    /**
     */
    public function __construct()
    {
    }

    /**
     * prepares restricted properties.
     */
    public function prepare( $csv_string, $dummy=array() )
    {
        $this->definition = $csv_string;
        $return = Helper::analyzeCsv( $csv_string );
        $this->properties = $return[ 'properties' ];
        $this->selectors  = $return[ 'selector' ];
        $this->validators = $return[ 'validation' ];
        $this->relations  = $return[ 'relation' ];
        $return = Helper::analyzeTypes( $this->properties, $this->relations, $this->id_name );
        $this->dataTypes  = $return[ 'dataTypes' ];
        $this->extraTypes = $return[ 'extraTypes' ];
        $this->protected  = $return[ 'protected' ];
    }
    public function present( $validation, $selector ) {}
    // +----------------------------------------------------------------------+
    //  Validation and Selector properties.
    // +----------------------------------------------------------------------+
    /**
     * @param string $name
     * @return null|array
     */
    public function getSelectInfo( $name ) {
        $info = Helper::arrGet( $this->selectors, $name );
        if( isset( $info[ 'selector' ] ) ) $info[0] = $info[ 'selector' ];
        return $info;
    }

    /**
     * @param string $name
     * @return null|array
     */
    public function getValidateInfo( $name ) {
        $info = Helper::arrGet( $this->validators, $name );
        if( isset( $info[ 'type' ] ) ) $info[0] = $info[ 'type' ];
        return $info;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isRequired( $name ) {
        $info = Helper::arrGet( $this->properties, $name );
        return isset( $info[ 'required' ] ) ? $info[ 'required' ]===true: false;
    }
    // +----------------------------------------------------------------------+
}