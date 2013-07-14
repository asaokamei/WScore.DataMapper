<?php
namespace WScore\DataMapper\Filter;

use WScore\DataMapper\Model\Helper;
use WScore\DataMapper\Model\Model;
use \DateTime as Now;

/**
 * Class CreatedAt
 *
 * @package WScore\DataMapper\Filter
 *
 * @cacheable
 */
abstract class SetTimeAbstract implements FilterInterface
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
     * @var string
     */
    public $column_name;
    
    /**
     * @param $data
     * @return array
     */
    public function setTime( &$data ) 
    {
        $columns = $this->model->property->getExtraType( $this->column_name );
        if( !$columns ) return;
        $now = $this->getNow();
        foreach( $columns as $col ) {
            $data[ $col ] = $now;
        }
        return $data;
    }

    /**
     * @return Now
     */
    public function getNow() {
        if( $this->now ) {
            return $this->now;
        }
        return new \DateTime();
    }
    
    /**
     * @param Model $model
     */
    public function setModel( $model )
    {
        $this->model = $model;
    }
}