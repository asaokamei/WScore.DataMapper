<?php
namespace WScore\DataMapper\Filter;

use WScore\DataMapper\Model\Helper;
use WScore\DataMapper\Model\Model;
use \DateTime as Now;

class CreatedAt implements FilterInterface
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
     * @return void
     */
    public function onInsert( &$data ) {
        $columns = $this->model->property->getExtraType( 'created_at' );
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