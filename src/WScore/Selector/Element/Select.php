<?php
namespace WScore\Selector;

class Element_Select extends ElementItemizedAbstract
{
    /**
     * @param \WScore\Html\Forms $form
     */
    public function __construct( $form )
    {
        $this->form = $form;
        $this->style = 'select';
    }

    /**
     * @param $value
     * @return \WScore\Html\Elements
     */
    public function makeForm( $value ) {
        $form = $this->form;
        if( $this->style == 'mult_select' ) {
            $this->attributes[ 'multiple' ] = true;
        }
        return $form->select( $this->name, $this->item_data, $value, $this->attributes );
    }

}