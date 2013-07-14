<?php
namespace WScore\DataMapper\Filter;

use WScore\DataMapper\Model\Helper;
use WScore\DataMapper\Model\Model;
use \DateTime as Now;

/**
 * Class DateTime
 *
 * @package WScore\DataMapper\Filter
 * 
 * @cacheable
 */
class DateTime implements FilterInterface
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
            $type = strtolower( $this->model->property->getType( $key ) );
            if( $type === 'datetime' ) {
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