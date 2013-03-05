<?php
namespace WScore\DataMapper;

/**
 * base class for dao's for database tables.
 * a Table Data Gateway pattern.
 *
 */
class Model_Presentation
{
    /**
     * for selector construction. to use with WScore\Html\Selector,
     * $selectors = array(
     *  name => [ 'Selector', style, option text, [
     *      'items' => [ [ val1, str1 ], [ val2, str2 ], ... ],
     *      'filter' => function(){}
     *  ] ],
     * )
     *
     * @var array                                  */
    protected $selectors  = array();

    /**
     * for validation of inputs
     * @var array
     */
    protected $validators = array();

    // +----------------------------------------------------------------------+
    //  Managing Object and Instances. 
    // +----------------------------------------------------------------------+
    /**
     */
    public function __construct()
    {
    }

    // +----------------------------------------------------------------------+
    //  Managing information about selector, validator, and property list.
    // +----------------------------------------------------------------------+
    /**
     * @param string $name
     * @return null|array
     */
    public function getSelectInfo( $name ) {
        return Model_Helper::arrGet( $this->selectors, $name );
    }

    /**
     * @param string $name
     * @return null|array
     */
    public function getValidateInfo( $name ) {
        return Model_Helper::arrGet( $this->validators, $name );
    }
    // +----------------------------------------------------------------------+
    //  Managing Validation and Properties. 
    // +----------------------------------------------------------------------+
}

