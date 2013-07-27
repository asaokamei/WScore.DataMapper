<?php
namespace WScore\DataMapper\Model;

use \WScore\Selector\ElementAbstract;
use \WScore\Selector\ElementItemizedAbstract;

/**
 * base class for dao's for database tables.
 * a Table Data Gateway pattern.
 *
 */
class Presentation
{
    /**
     * @var array
     */
    private $selInstances = array();

    /**
     * @Inject
     * @var \WScore\Selector\Selector
     */
    public $selector;

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

    /**
     * returns form element object for property name.
     * the object is pooled and will be reused for model/propName basis.
     *
     * @param string $name
     * @param bool   $forge
     * @return ElementAbstract|ElementItemizedAbstract
     */
    public function getSelector( $name, $forge=false )
    {
        if( !$forge && isset( $this->selInstances[ $name ] ) ) {
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
     * @return ElementAbstract|ElementItemizedAbstract
     */
    public function forgeSelector( $name )
    {
        $selector = null;
        if( !$info = $this->property->getSelectInfo( $name ) ) {
            // return input:text html elements if not set.
            return $this->selector->getInstance( 'text', $name );
        }
        $type   = Helper::arrGet( $info, 'type', null );
        $choice = Helper::arrGet( $info, 'choice',  array() );
        $extra  = Helper::arrGet( $info, 'extra', null );
        if( $info[0] == 'Selector' )
        {
            $selector = $this->selector->getInstance( $type, $name, $extra, $choice );
        }
        else
        {
            $class = $info[0];
            $selector = new $class( $name, $extra, $choice );
        }
        return $selector;
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function getSelectorType( $name )
    {
        if( !$type = $this->property->getProperty( $name, 'presentAs' ) ) {
            $type  = $this->property->getProperty( $name, 'type' );
        }
        $type = Helper::arrGet( $this->convert, $type, $type );
        return $type;
    }
    // +----------------------------------------------------------------------+
}

