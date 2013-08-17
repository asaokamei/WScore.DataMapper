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
abstract class SetTimeAbstract extends FilterAbstract
{
    /**
     * @var Now
     */
    public $now;
    
    /**
     * @var string
     */
    public $column_type;
    
    /**
     * @param $data
     * @return array
     */
    public function setTime( &$data ) 
    {
        $columns = $this->model->property->getByType( $this->column_type );
        if( !$columns ) return $data;
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