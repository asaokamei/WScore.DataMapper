<?php
namespace WScore\Selector;

class Element_DateYMD extends SelectDiv
{
    public function __construct( $form )
    {
        parent::__construct( $form );
        $this->implode_with_div = FALSE;
        $this->divider = '-';

        // shows date like 2012/01/23.
        $this->htmlFilter = function( $val ) {
            return str_replace( '-', '/', $val );
        };
    }
    public function set( $name, $option=array(), $htmlFilter=NULL )
    {
        $this->name              = $name;
        $this->implode_with_div  = $this->arrGet( $option, 'implode_with_div', FALSE );
        $this->divider           = $this->arrGet( $option, 'divider', '-' );
        $this->default_items = $this->arrGet( $option, 'default', date( 'Y-m-d' ) );
        // do not pass default to sub-forms. Default in dateYMD are passed to each of sub-forms. 
        if( array_key_exists( 'default', $option ) ) unset( $option[ 'default' ] );
        
        $this->num_div = 3;
        $selY = new Element_DateYear(  $this->form );        $selY->set( "{$this->name}_y", $option );
        $selM = new Element_DateMonth( $this->form );        $selM->set( "{$this->name}_m", $option );
        $selD = new Element_DateDay(   $this->form );        $selD->set( "{$this->name}_d", $option );
        $this->d_forms[] = $selY;
        $this->d_forms[] = $selM;
        $this->d_forms[] = $selD;

        // shows date like 2012/01/23.
        $this->htmlFilter = function( $val ) {
            return str_replace( '-', '/', $val );
        };
    }
}