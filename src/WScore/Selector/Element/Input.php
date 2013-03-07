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
        $id = $this->name . '_list';
        $input->list( $id );
        /** @var $lists \WScore\Html\Forms */
        $lists = $this->form->elements->datalist()->id( $id );
        foreach( $this->item_data as $item ) {
            $option = $this->form->option()->value( $item );
            $option->_noBodyTag = true;
            $lists->_contain( $option );
        }
        $div = $this->form->elements->div( $input, $lists );
        return $div;
    }
}