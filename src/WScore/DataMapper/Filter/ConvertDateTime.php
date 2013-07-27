<?php
namespace WScore\DataMapper\Filter;

use WScore\DataMapper\Model\Helper;
use WScore\DataMapper\Model\Model;

/**
 * Class DateTime
 *
 * @package WScore\DataMapper\Filter
 * 
 * @cacheable
 */
class ConvertDateTime implements FilterInterface
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @param $data
     * @return array
     */
    public function __invoke( $data ) {
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
            
            if( !$val instanceof \DateTime ) continue;
            $type = strtolower( $this->model->property->getProperty( $key, 'type' ) );
            if( in_array( $type, array( 'datetime', 'created_at', 'updated_at' ) ) ) {
                $data[ $key ] = $val->format( 'Y-m-d H:i:s' );
            }
        }
        return $data;
    }

    /**
     * @param Model $model
     */
    public function setModel( $model )
    {
        $this->model = $model;
    }
}