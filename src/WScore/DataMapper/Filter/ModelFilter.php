<?php
namespace WScore\DataMapper\Filter;

use WScore\DataMapper\Filters;

/**
 * Class ModelFilter
 *
 * @package WScore\DataMapper\Filter
 * 
 * @cacheable
 */
class ModelFilter extends Filters
{
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
}