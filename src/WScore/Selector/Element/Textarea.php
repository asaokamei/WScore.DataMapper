<?php
namespace WScore\Selector;

class Element_Textarea extends ElementAbstract
{
    /**
     * @param Form $form
     */
    public function __construct( $form )
    {
        $this->form  = $form;
        $this->style = 'textarea';
        $this->htmlFilter = array( $this, 'htmlSafe' );
    }

    /**
     * @param $value
     * @return \WScore\Html\Elements
     */
    public function makeForm( $value ) {
        return $this->form->textArea( $this->name, $value, $this->attributes );
    }

    public function htmlSafe( $value ) {
        $value = htmlentities( $value, ENT_QUOTES, 'UTF-8' );
        $value = nl2br( $value );
        return $value;
    }
}