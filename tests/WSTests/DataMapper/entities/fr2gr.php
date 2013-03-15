<?php
namespace WSTests\DataMapper\entities;

use \WScore\DataMapper\Entity\EntityAbstract;

class fr2gr extends EntityAbstract
{
    static $_modelName = '\WSTests\DataMapper\models\Fr2gr';
    public $fr2gr_id;
    public $friend_id;
    public $group_code;
    public $created_at;
    public $updated_at;
    
    public static function getLeft() {
        return 'friend_id';
    }
    public static function getRight() {
        return 'contact_id';
    }
}
