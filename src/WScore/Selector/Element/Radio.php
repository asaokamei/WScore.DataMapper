<?php
namespace WScore\Selector;

class Element_Radio extends ElementItemizedAbstract
{
    /**
     * @param \WScore\Html\Forms $form
     */
    public function __construct( $form )
    {
        $this->form = $form;
        $this->style = 'radio';
    }

    /**
     * @param $value
     * @return \WScore\Html\Elements
     */
    public function makeForm( $value ) {
        $form = $this->form;
        return $form->radioList( $this->name, $this->item_data, $value, $this->attributes );
    }

}