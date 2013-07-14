<?php
namespace WScore\DataMapper\Filter;

use WScore\DataMapper\Model\Helper;
use WScore\DataMapper\Model\Model;
use \DateTime as Now;

/**
 * Class UpdatedAt
 *
 * @package WScore\DataMapper\Filter
 * 
 * @cacheable
 */
class UpdatedAt extends SetTimeAbstract
{
    /**
     * @var string
     */
    public $column_name = 'updated_at';

    /**
     * @param $data
     * @return void
     */
    public function __invoke( &$data ) {
        $this->setTime( $data );
    }
}