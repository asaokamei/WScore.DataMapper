<?php
namespace WScore\DataMapper\Filter;

use WScore\DataMapper\Model\Model;

/**
 * Class ModelFilter
 *
 * @package WScore\DataMapper\Filter
 * 
 * @cacheable
 */
class ModelFilter 
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @var FilterInterface[][]|\Closure[][]
     */
    public $filters = array();

    /**
     * @Inject
     * @var \WScore\DataMapper\Filter\SetCreateTime
     */
    public $createdAt;

    /**
     * @Inject
     * @var \WScore\DataMapper\Filter\SetUpdateTime
     */
    public $updatedAt;

    /**
     * @Inject
     * @var \WScore\DataMapper\Filter\ConvertDateTime
     */
    public $dateTime;
    
    public function __construct()
    {
        $this->addFilter( 'insert', $this->createdAt );
        $this->addFilter( 'insert', $this->updatedAt );
        $this->addFilter( 'update', $this->updatedAt );
        $this->addFilter( 'save',   $this->dateTime  );
    }

    /**
     * @param Model $model
     */
    public function setModel( $model )
    {
        $this->model = $model;
    }

    /**
     * @param string                   $event
     * @param FilterInterface|\Closure $filter
     * @return $this
     */
    public function addFilter( $event, $filter )
    {
        $this->filters[ $event ][] = $filter;
        return $this;
    }

    /**
     * @param string $event
     * @param mixed $data
     * @return mixed
     */
    public function event( $event, $data )
    {
        if( !isset( $this->filters[ $event ] ) ) return $data;

        $method = 'on' . ucwords( $event );
        foreach( $this->filters[ $event ] as $filter ) {

            if( $filter instanceof FilterInterface ) {
                $filter->setModel( $this->model );
            }
            if( is_callable( $filter ) ) {
                $data = $filter( $data );
            } elseif( method_exists( $filter, $method ) ) {
                $data = $filter->$method( $data );
            }
        }
        return $data;
    }
}