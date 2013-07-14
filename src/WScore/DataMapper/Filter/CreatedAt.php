<?php
namespace WScore\DataMapper\Filter;

use WScore\DataMapper\Model\Helper;
use WScore\DataMapper\Model\Model;

class CreatedAt implements FilterInterface
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @param $data
     * @return void
     */
    public function onInsert( &$data ) {
        $data = $this->model->property->createdAt( $data );
    }

    /**
     * @param Model $model
     */
    public function setModel( $model )
    {
        $this->model = $model;
    }
}