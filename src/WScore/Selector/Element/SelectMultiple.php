<?php
namespace WScore\Selector;

class Element_SelectMultiple extends ElementItemizedAbstract
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
        $this->attributes[ 'multiple' ] = true;
        return parent::makeForm( $value );
    }

}