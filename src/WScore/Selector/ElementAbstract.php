<?php
namespace WScore\Selector;

class ElementAbstract
{
    public $types = array(
        'edit' => 'Form',
        'new'  => 'Form',
        'disp' => 'Html',
        'name' => 'Html',
        'raw'  => 'Raw'
    );

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
     * @Inject
     * @var \WScore\Html\Forms
     */
    public $form;

    /**
     * set up Selector. make sure to overload this function.
     *
     * @param string      $name
     * @param array $option
     */
    public function set( $name, $option=array() )
    {
        $this->name  = $name;
        $this->attributes = array_merge( $this->attributes, $option );
    }

    /**
     * @param array $items
     */
    public function setItemData( $items ) {
        $this->item_data = $items;
    }

    /**
     * @param \Closure $filter
     */
    public function setHtmlFilter( $filter ) {
        if( $filter instanceof \Closure ) {
            $this->htmlFilter = $filter;
        }
    }

    /**
     * @param string $type
     * @param mixed  $value
     * @return mixed
     */
    public function popHtml( $type, $value='' )
    {
        if( !$value && $this->default_items ) $value = $this->default_items;
        $type = \strtolower( $type );
        $type = ( isset( $this->types[ $type ] ) ) ? $this->types[ $type ]: $type;
        $method = 'make' . ucwords( $type );
        return $this->$method( $value );
    }

    /**
     * @param $value
     * @return \WScore\Html\Elements
     */
    public function makeHidden( $value ) {
        return $this->form->input( 'hidden', $this->name, $value );
    }
    
    /**
     * makes RAW type of a value.
     * returns as is for single value, returns as 'div > nl > li' for arrays.
     *
     * @param $value
     * @return mixed
     */
    public function makeRaw( $value ) {
        return $value;
    }

    /**
     * makes HTML safe value.
     *
     * @param string|array $value
     * @return string|void
     */
    public function makeHtml( $value )
    {
        if( isset( $this->htmlFilter ) ) {
            $value = call_user_func( $this->htmlFilter, $value );
        }
        return $value;
    }
    /**
     * make FORM type of value.
     * create HTML Form element based on style.
     *
     * @param $value
     * @return mixed
     */
    public function makeForm( $value ) {}

    /**
     * @param string $string
     * @return string
     */
    public function htmlSafe( $string ) {
        return htmlentities( $string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * @param array $arr
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function arrGet( $arr, $key, $default=null ) {
        if( array_key_exists( $key, $arr ) ) {
            return $arr[ $key ];
        }
        return $default;
    }

}
