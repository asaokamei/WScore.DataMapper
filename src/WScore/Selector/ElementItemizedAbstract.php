<?php
namespace WScore\Selector;

class ElementItemizedAbstract extends ElementAbstract
{
    /**
     * makes RAW type of a value.
     * returns as is for single value, returns as 'div > nl > li' for arrays.
     *
     * @param $value
     * @return string
     */
    public function makeRaw( $value ) {
        if( is_array( $value ) ) {
            if( count( $value ) > 1 ) return implode( ',', $value );
            $value = array_pop( $value );
        }
        return $value;
    }

    /**
     * makes HTML safe value.
     *
     * @param string|array $value
     * @return string
     */
    public function makeHtml( $value )
    {
        if( !empty( $this->item_data ) ) {
            // match with items. assumed values are safe.
            $value = $this->makeHtmlItems( $value );
        }
        return $this->makeRaw( $value );
    }

    /**
     * returns itemized value as an array.
     * replaces the value with err_msg_empty if value fails to match with item_data.
     *
     * @param $value
     * @return array
     */
    public function makeHtmlItems( $value )
    {
        if( !is_array( $value ) ) $value = array( $value );
        foreach( $value as $key => &$val ) {
            if( $string = $this->findValueFromItems( $val ) ) {
                $value[ $key ] = $string;
            }
            else {
                $value[ $key ] = $this->err_msg_empty;
            }
        }
        return $value;
    }

    /**
     * finds a value for a given single value from itemized data.
     *
     * @param $value
     * @return bool
     */
    public function findValueFromItems( &$value )
    {
        foreach( $this->item_data as $item ) {
            if( $value == $this->arrGet( $item, 0 ) ) {
                return $item[1];
            }
        }
        return false;
    }

    /**
     * @param $value
     * @return \WScore\Html\Elements
     */
    public function makeForm( $value )
    {
        $form = $this->form;
        if( $this->style === 'check' ) {
            return $form->checkList( $this->name, $this->item_data, $value, $this->attributes );
        }
        if( $this->style === 'radio' ) {
            return $form->radioList( $this->name, $this->item_data, $value, $this->attributes );
        }
        return $form->select( $this->name, $this->item_data, $value, $this->attributes );
    }
}
