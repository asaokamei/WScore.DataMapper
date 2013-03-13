<?php
namespace WScore\DataMapper\Model;

/**
 * base class for dao's for database tables.
 * a Table Data Gateway pattern.
 *
 */
class Property extends PropertyAbstract implements PropertyInterface
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
    public function prepare( $definition, $relations )
    {
        $this->definition = $definition;
        $return = Helper::prepare( $definition, $relations, $this->id_name );
        $this->properties = $return[ 'properties' ];
        $this->dataTypes  = $return[ 'dataTypes' ];
        $this->extraTypes = $return[ 'extraTypes' ];
        $this->protected  = $return[ 'protected' ];
    }

    /**
     * @param array $validation
     * @param array $selector
     */
    public function present( $validation, $selector )
    {
        $this->validators = $validation;
        $this->selectors  = $selector;
    }
    // +----------------------------------------------------------------------+
    //  Validation and Selector properties.
    // +----------------------------------------------------------------------+
    /**
     * @param string $name
     * @return null|array
     */
    public function getSelectInfo( $name ) {
        return Helper::arrGet( $this->selectors, $name );
    }

    /**
     * @param string $name
     * @return null|array
     */
    public function getValidateInfo( $name ) {
        return Helper::arrGet( $this->validators, $name );
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isRequired( $name ) {
        $info = Helper::arrGet( $this->validators, $name );
        return isset( $info[ 'required' ] ) ?: false;
    }
    // +----------------------------------------------------------------------+
}