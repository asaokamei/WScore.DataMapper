<?php
namespace WScore\DataMapper\Filter;

use WScore\DataMapper\Model\Helper;
use WScore\DataMapper\Model\Model;
use \DateTime as Now;

class UpdatedAt implements FilterInterface
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @var Now
     */
    public $now;

    /**
     * @param $data
     */
    public function __invoke( &$data ) {
        $this->onSave( $data );
    }

    /**
     * @param $data
     * @return void
     */
    public function onSave( &$data ) 
    {
        $columns = $this->model->property->getExtraType( 'updated_at' );
        if( !$columns ) return;
        if( $this->now ) {
            $now = $this->now;
        } else {
            $now = new \DateTime();
        }
        foreach( $columns as $col ) {
            $data[ $col ] = $now->format( 'Y-m-d H:i:s' );
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