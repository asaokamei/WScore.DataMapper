<?php
namespace WScore\DataMapper\Model;

use \WScore\Selector\ElementAbstract;
use \WScore\Selector\ElementItemizedAbstract;

/**
 * base class for dao's for database tables.
 * a Table Data Gateway pattern.
 *
 */
class Validation
{
    /**
     * @var array
     */
    private $ruleInstances = array();

    /**
     * @Inject
     * @var \WScore\Validation\Rules
     */
    public $rules;

    /**
     * @var \WScore\DataMapper\Model\PropertyInterface
     */
    protected $property;
    
    protected $convert = array(
        'string' => 'text',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    );

    // +----------------------------------------------------------------------+
    //  Managing Object and Instances. 
    // +----------------------------------------------------------------------+
    /**
     */
    public function __construct()
    {
    }

    /**
     * @param \WScore\DataMapper\Model\PropertyInterface $property
     */
    public function setProperty( $property )
    {
        $this->property = $property;
    }

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
        if( !$validateInfo ) {
            // return text rule if not defined.
            return $this->rules->text();
        }
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

    /**
     * @param string $name
     * @return string
     */
    protected function getValidationType( $name )
    {
        if( !$type = $this->property->getProperty( $name, 'evaluateAs' ) ) {
            $type = $this->property->getProperty( $name, 'type' );
        }
        if( !$type ) $type = 'text';
        $type = Helper::arrGet( $this->convert, $type, $type );
        return $type;
    }
}

