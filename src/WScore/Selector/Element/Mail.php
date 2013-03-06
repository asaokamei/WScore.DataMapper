<?php
namespace WScore\Selector;

class Element_Mail extends Selector
{
    /**
     * @param Form $form
     */
    public function __construct( $form )
    {
        parent::__construct( $form );
        $this->style = 'mail';
    }
}