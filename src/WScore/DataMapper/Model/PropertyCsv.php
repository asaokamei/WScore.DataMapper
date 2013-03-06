<?php
namespace WScore\DataMapper;

/**
 * base class for dao's for database tables.
 * a Table Data Gateway pattern.
 *
 */
class Model_PropertyCsv extends Model_PropertyAbstract
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
    protected $definition = array();

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
    protected $selectors  = array();

    /**
     * for validation of inputs
     * @var array
     */
    protected $validators = array();

    protected $relations = array();

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
    public function prepare( $csv_string )
    {
        $this->definition = $csv_string;
        $return = Model_Helper::analyzeCsv( $csv_string );
        $this->properties = $return[ 'properties' ];
        $this->selectors  = $return[ 'selector' ];
        $this->validators = $return[ 'validation' ];
        $this->relations  = $return[ 'relation' ];
        $return = Model_Helper::analyzeTypes( $this->properties, $this->relations, $this->id_name );
        $this->dataTypes  = $return[ 'dataTypes' ];
        $this->extraTypes = $return[ 'extraTypes' ];
        $this->protected  = $return[ 'protected' ];
    }
    // +----------------------------------------------------------------------+
    //  Validation and Selector properties.
    // +----------------------------------------------------------------------+
    /**
     * @param string $name
     * @return null|array
     */
    public function getSelectInfo( $name ) {
        $info = Model_Helper::arrGet( $this->selectors, $name );
        if( isset( $info[ 'selector' ] ) ) $info[0] = $info[ 'selector' ];
        return $info;
    }

    /**
     * @param string $name
     * @return null|array
     */
    public function getValidateInfo( $name ) {
        $info = Model_Helper::arrGet( $this->validators, $name );
        if( isset( $info[ 'type' ] ) ) $info[0] = $info[ 'type' ];
        return $info;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isRequired( $name ) {
        $info = Model_Helper::arrGet( $this->properties, $name );
        return isset( $info[ 'required' ] ) ? $info[ 'required' ]===true: false;
    }
    // +----------------------------------------------------------------------+
}