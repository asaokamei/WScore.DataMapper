<?php
namespace WScore\Selector;

/*
TODO: move Selector stuff to Selector folder.

Selector( $style, $name, $opt1, $opt2, $ime );

class htmlText extends Selector {
    function __construct( $name, $width, $limit, $ime ) {
        $this->style = 'text';
        $this->name  = $name;
        $this->width = $width;
        $this->maxlength = $limit;
        $this->setIME( $ime );
    }
}

class sel_active_flag extends Selector {
    function __construct( $name, $opt1, $opt2, $ime ) {
        // code should work as is pretty much.
    }
}

class selYear ...

class selDateDs ...

*/
class Selector
{
    /**
     * html type: select, textarea, radio, check, and others (text, hidden, date, etc.)
     * @var string
     *
     */
    public $style           = '';
    public $name            = '';
    public $item_data       = array();
    public $default_items   = '';
    public $err_msg_empty   = '*invalid*';
    public $add_head_option = '';
    public $attributes      = array( 'class' => 'FormSelector' );

    /** @var callable|null */
    public $htmlFilter      = null;

    /** 
     * @var \WScore\Html\Forms 
     */
    protected $form;
    
    protected static $types = array(
        'form' => 'form',
        'edit' => 'form',
        'new'  => 'form',
        'html' => 'html',
        'disp' => 'html',
        'name' => 'html',
        'raw'  => 'raw'
    );
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
        'checktoggle'    => 'checkToggle',
    );
    public static $encoding = 'UTF-8';

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
        $styleToPass = null;
        $formStyle = isset( static::$formStyle[ $style ] ) ? static::$formStyle[ $style ]: $style;
        if( class_exists( $class = '\WScore\Selector\Element_' . ucwords( $formStyle ) ) ) {
            $class = '\WScore\Selector\Element_' . ucwords( $formStyle );
        }        
        elseif( class_exists( $style ) ) {
            $class = $style;
        }
        else {
            $class = '\WScore\Selector\Selector';
            $styleToPass = $style;
        }
        /** @var $selector ElementAbstract */
        $option   = $this->prepareFilter( $option );
        $selector = new $class( $this->form, $styleToPass );
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