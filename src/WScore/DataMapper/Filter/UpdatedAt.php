<?php
namespace WScore\DataMapper\Filter;

use WScore\DataMapper\Model\Helper;
use WScore\DataMapper\Model\Model;

class UpdatedAt implements FilterInterface
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @param $data
     * @return void
     */
    public function onSave( &$data ) {
        $data = $this->model->property->updatedAt( $data );
    }

    /**
     * @param Model $model
     */
    public function setModel( $model )
    {
        $this->model = $model;
    }
}