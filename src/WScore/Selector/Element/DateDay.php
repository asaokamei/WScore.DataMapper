<?php
namespace WScore\Selector;

class Element_DateDay extends Element_Select
{
    public function __construct( $form )
    {
        $this->form = $form;
    }
    public function set( $name, $option=array(), $htmlFilter=NULL )
    {
        $this->name            = $name;
        $this->add_head_option = $this->arrGet( $option, 'add_head', '' );
        for( $day = 1; $day <= 31; $day ++ ) {
            $this->item_data[] = array(
                sprintf( '%2d', $day ),
                sprintf( '%2d', $day )
            );
        }
    }
    public function makeHtml( $value ) {
        $value = parent::makeHtml( $value );
        if( $value ) $value .= '日';
        return $value;
    }
}