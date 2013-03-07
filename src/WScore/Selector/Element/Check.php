<?php
namespace WScore\Selector;

class Element_Check extends ElementItemizedAbstract
{
    /**
     * @param \WScore\Html\Forms $form
     */
    public function __construct( $form )
    {
        $this->form = $form;
        $this->style = 'check';
    }

    /**
     * @param $value
     * @return \WScore\Html\Elements
     */
    public function makeForm( $value ) {
        $form = $this->form;
        return $form->checkList( $this->name, $this->item_data, $value, $this->attributes );
    }

}