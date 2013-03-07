<?php
namespace WScore\Selector;

class Selector
{
    /**
     * @var \WScore\Html\Forms 
     */
    protected $form;
    
    protected static $formStyle = array(
        'textarea'    => 'textarea',
        'select'      => 'select',
        'mult_select' => 'SelectMultiple',
        'check'       => 'check',
        'check_hor'   => 'check',
        'check_ver'   => 'check',
        'radio'       => 'radio',
        'radio_hor'   => 'radio',
        'radio_ver'   => 'radio',
        'toggle'      => 'checkToggle',
        'checktoggle' => 'checkToggle',
    );

    // +----------------------------------------------------------------------+
    /**
     * @Inject
     * @param \WScore\Html\Forms $form
     * @return \WScore\Selector\Selector
     */
    public function __construct( $form )
    {
        $this->form = $form;
        // setup filter for html safe value.
    }

    /**
     * @return \WScore\Html\Forms
     */
    public function form() {
        return $this->form;
    }

    /**
     * get instances of Selector for various styles in Selector_*. 
     * 
     * @param string   $style
     * @param string   $name
     * @param string   $option
     * @param array    $items
     * @param \Closure $htmlFilter
     * @return Selector
     * @throws \RuntimeException
     */
    public function getInstance( $style, $name, $option=null, $items=array(), $htmlFilter=null )
    {
        $formStyle = isset( static::$formStyle[ $style ] ) ? static::$formStyle[ $style ]: $style;
        if( class_exists( $class = '\WScore\Selector\Element_' . ucwords( $formStyle ) ) ) {
            $class = '\WScore\Selector\Element_' . ucwords( $formStyle );
        }        
        elseif( class_exists( $style ) ) {
            $class = $style;
        }
        else {
            $class = '\WScore\Selector\Selector';
        }
        /** @var $selector ElementAbstract */
        $option   = $this->prepareFilter( $option );
        $selector = new $class( $this->form );
        $selector->set( $name, $option );
        if( $items ) $selector->setItemData( $items );
        if( $htmlFilter ) $selector->setHtmlFilter( $htmlFilter );
        return $selector;
    }

    /**
     * prepares filter if it is in string; 'rule1:parameter1|rule2:parameter2'
     * This is copied from Validator. DRY!
     *
     * @param string|array $filter
     * @return array
     */
    public function prepareFilter( $filter )
    {
        if( empty( $filter ) ) return array();
        if( is_array( $filter ) ) return $filter;
        $filter_array = array();
        $rules = explode( '|', $filter );
        foreach( $rules as $rule ) {
            $filter = explode( ':', $rule, 2 );
            array_walk( $filter, function( &$v ) { $v = trim( $v ); } );
            if( isset( $filter[1] ) ) {
                $filter_array[ $filter[0] ] = ( $filter[1]=='FALSE' )? false: $filter[1];
            }
            else {
                $filter_array[ $filter[0] ] = true;
            }
        }
        return $filter_array;
    }
    // +----------------------------------------------------------------------+
}