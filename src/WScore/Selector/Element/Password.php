<?php
namespace WScore\Selector;

class Element_Password extends Element_Input
{
    /**
     * @param Form $form
     */
    public function __construct( $form )
    {
        parent::__construct( $form );
        $this->style = 'password';
    }
}