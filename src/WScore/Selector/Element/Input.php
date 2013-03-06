<?php
namespace WScore\Selector;

class Element_Input extends Selector
{
    /**
     * make FORM type of value.
     * create HTML Form element based on style.
     *
     * @param $value
     * @return \WScore\Html\Elements
     */
    public function makeForm( $value )
    {
        $input = $this->form->input( $this->style, $this->name, $value, $this->attributes );
        if( empty( $this->item_data ) ) {
            return $input;
        }
        return $input;
    }
}