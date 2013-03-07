<?php
namespace WScore\Selector;

class Element_CheckToggle extends ElementItemizedAbstract
{
    /**
     * @param \WScore\Html\Forms $form
     */
    public function __construct( $form )
    {
        $this->form = $form;
        $this->style = 'checkToggle';
    }

    /**
     * @param $value
     * @return \WScore\Html\Elements
     */
    public function makeForm( $value )
    {
        $form = $this->form;
        $forms = $form->input( 'hidden', $this->name, $this->item_data[0][0], $this->attributes );
        if( $value && $value == $this->item_data[1][0] ) {
            $this->attributes[ 'checked' ] = true;
        }
        $forms .= $form->checkLabel( $this->name, $this->item_data[1][0], $this->item_data[1][1], $this->attributes );
        return $forms;
    }

}