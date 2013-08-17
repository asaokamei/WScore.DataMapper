<?php
namespace WScore\DataMapper\Filter;

use WScore\DataMapper\Model\Helper;

/**
 * Class DateTime
 *
 * converts DateTime object to a string.
 *
 * @package WScore\DataMapper\Filter
 * 
 * @cacheable
 */
class ConvertDateTime extends FilterAbstract
{
    /**
     * @param $data
     * @return array
     */
    public function onSave( $data ) {
        $data = $this->convert( $data );
        return $data;
    }

    /**
     * @param $data
     * @return array
     */
    public function convert( $data ) 
    {
        foreach( $data as $key => $val ) {
            
            if( $val instanceof \DateTime ) {
                $type = strtolower( $this->model->property->getProperty( $key, 'type' ) );
                if( method_exists( $val, '__toString' ) ) {
                    $data[ $key ] = (string) $val;
                }
                elseif( in_array( $type, [ 'date' ] ) ) {
                    $data[ $key ] = $val->format( 'Y-m-d' );
                }
                elseif( in_array( $type, [ 'time' ] ) ) {
                    $data[ $key ] = $val->format( 'H:i:s' );
                }
                else {
                    $data[ $key ] = $val->format( 'Y-m-d H:i:s' );
                }
            }
        }
        return $data;
    }
}