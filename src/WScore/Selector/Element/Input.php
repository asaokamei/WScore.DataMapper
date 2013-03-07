<?php
namespace WScore\Selector;

class Element_Input extends ElementAbstract
{
    /**
     * @Inject
     * @param \WScore\Html\Forms $form
     * @param string $style
     */
    public function __construct( $form, $style=null )
    {
        $this->form = $form;
        if( $style ) $this->style = $style;
        // setup filter for html safe value.
        $this->htmlFilter = array( $this, 'htmlSafe' );
    }
    /**
     * make FORM type of value.
     * create HTML Form element based on style.
     *
     * @param $value
     * @return \WScore\Html\Elements
     */
    public function makeForm( $value )
    {
        $input = $this->form->input( $this->style, $this->name, $value, $this->attributes );
        if( empty( $this->item_data ) ) {
            return $input;
        }
        return $input;
    }
}