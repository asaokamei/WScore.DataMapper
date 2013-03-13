<?php
namespace WSTests\DataMapper\entities;

use \WScore\DataMapper\Entity\EntityAbstract;

class fr2gr extends EntityAbstract
{
    static $_modelName = '\WSTests\DataMapper\models\Fr2Gr';
    public function getLeft() {
        return 'friend_id';
    }
    public function getRight() {
        return 'contact_id';
    }
}
