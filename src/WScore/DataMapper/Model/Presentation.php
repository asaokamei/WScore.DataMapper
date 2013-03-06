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
     * @var array
     */
    private $selInstances = array();

    /**
     * @var array
     */
    private $ruleInstances = array();
    
    /**
     * @Inject
     * @var \WScore\Selector\Selector
     */
    protected $selector;

    /**
     * @Inject
     * @var \WScore\Validation\Rules
     */
    protected $rules;

    /**
     * @var \WScore\DataMapper\Model_Property
     */
    protected $property;

    // +----------------------------------------------------------------------+
    //  Managing Object and Instances. 
    // +----------------------------------------------------------------------+
    /**
     */
    public function __construct()
    {
    }

    /**
     * @param \WScore\DataMapper\Model_Property $property
     */
    public function setProperty( $property )
    {
        $this->property = $property;
    }

    // +----------------------------------------------------------------------+
    //  Validator for input validation.
    // +----------------------------------------------------------------------+
    public function getRule( $name ) {
        if( isset( $this->ruleInstances[ $name ] ) ) {
            return $this->ruleInstances[ $name ];
        }
        return $this->ruleInstances[ $name ] = $this->forgeRule( $name );
    }
    /**
     * @param string $name
     * @return \WScore\Validation\Rules
     */
    public function forgeRule( $name )
    {
        $validateInfo = $this->property->getValidateInfo( $name );
        if( !$validateInfo ) return null;
        $type   = array_key_exists( 0, $validateInfo ) ? $validateInfo[0] : null ;
        $filter = array_key_exists( 1, $validateInfo ) ? $validateInfo[1] : '' ;
        if( $type ) {
            $rule = $this->rules->$type( $filter );
        }
        else {
            $rule = $this->rules->text( $filter );
        }
        if( $this->property->isRequired( $name ) ) {
            $rule[ 'required' ] = true;
        }
        if( $pattern = $this->property->getPattern( $name ) ) {
            $rule[ 'pattern' ] = $pattern;
        }
        return $rule;
    }

    // +----------------------------------------------------------------------+
    //  Selector for HTML form elements.
    // +----------------------------------------------------------------------+
    /**
     * returns form element object for property name.
     * the object is pooled and will be reused for model/propName basis.
     *
     * @param string $name
     * @return null|object
     */
    public function getSelector( $name )
    {
        if( isset( $this->selInstances[ $name ] ) ) {
            return $this->selInstances[ $name ];
        }
        return $this->selInstances[ $name ] = $this->forgeSelector( $name );
    }

    /**
     * creates selector object based on selectors array.
     * see the structure of array in Model::$selectors section.
     *
     * TODO: simplify or move factory to Selector.
     *
     * @param string $name
     * @return null|object
     */
    public function forgeSelector( $name )
    {
        $selector = null;
        if( !$info = $this->property->getSelectInfo( $name ) ) return $selector;
        if( $info[0] == 'Selector' )
        {
            $arg2     = Model_Helper::arrGet( $info, 2, null );
            $extra    = Model_Helper::arrGet( $info, 3, null );
            $arg3 = Model_Helper::arrGet( $info, 'choice',  array() );
            $arg4 = Model_Helper::arrGet( $info, 'filter', null );
            if( is_array( $extra ) && !empty( $extra ) ) {
                $arg3 = Model_Helper::arrGet( $extra, 'choice',  array() );
                $arg4 = Model_Helper::arrGet( $extra, 'filter', null );
            }
            $selector = $this->selector->getInstance( $info[1], $name, $arg2, $arg3, $arg4 );
        }
        else
        {
            $class = $info[0];
            $arg1     = Model_Helper::arrGet( $info[1], 0, null );
            $arg2     = Model_Helper::arrGet( $info[1], 1, null );
            $arg3     = Model_Helper::arrGet( $info[1], 2, null );
            $selector = new $class( $name, $arg1, $arg2, $arg3 );
        }
        return $selector;
    }
    // +----------------------------------------------------------------------+
}

