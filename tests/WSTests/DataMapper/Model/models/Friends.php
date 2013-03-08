<?php
namespace WSTests\DataMapper\Model\models;

use \WScore\DataMapper\Model;

class Friends extends Model
{
    protected $table = 'friend';
    
    protected $id_name = 'friend_id';
    
    public function __construct()
    {
        parent::__construct();
        $csv = file_get_contents( __DIR__ . '/friends.csv' );
        $this->property->prepare( $csv );
    }
}