<?php
namespace WScore\Selector;

class Element_Mail extends Element_Input
{
    /**
     * @param \WScore\Html\Forms $form
     */
    public function __construct( $form )
    {
        parent::__construct( $form );
        $this->style = 'mail';
    }
}