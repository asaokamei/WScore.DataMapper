<?php
namespace WScore\Selector;

class Element_Date extends Element_Input
{
    /**
     * @param \WScore\Html\Forms $form
     */
    public function __construct( $form )
    {
        parent::__construct( $form );
        $this->style = 'date';
        // shows date like 2012/01/23.
        $this->htmlFilter = function( $val ) {
            return str_replace( '-', '/', $val );
        };
    }
}