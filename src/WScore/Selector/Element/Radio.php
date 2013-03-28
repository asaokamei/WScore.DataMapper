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

}