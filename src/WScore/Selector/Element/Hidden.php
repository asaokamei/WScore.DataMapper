<?php
namespace WScore\DataMapper\Selector;

class Element_Hidden extends Selector
{
    /**
     * @param Form $form
     */
    public function __construct( $form )
    {
        parent::__construct( $form );
        $this->style = 'hidden';
    }
}