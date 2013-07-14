<?php
namespace WScore\DataMapper\Filter;

use WScore\DataMapper\Model\Helper;
use WScore\DataMapper\Model\Model;
use \DateTime as Now;

class DateTime implements FilterInterface
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @param $data
     */
    public function __invoke( &$data ) {
        $this->convert( $data );
    }

    /**
     * @param $data
     * @return void
     */
    public function convert( &$data ) 
    {
        foreach( $data as $key => $val ) {
            
            if( !$val instanceof \DateTime ) continue;
            $type = strtolower( $this->model->property->getType( $key ) );
            if( $type === 'datetime' ) {
                $data[ $key ] = $val->format( 'Y-m-d H:i:s' );
            }
        }
    }

    /**
     * @param Model $model
     */
    public function setModel( $model )
    {
        $this->model = $model;
    }
}