<?php
namespace WSTests\DataMapper\entities;

use \WScore\DataMapper\Entity\EntityAbstract;

class group extends EntityAbstract
{
    static $_modelName = '\WSTests\DataMapper\models\Groups';

    public $group_code;
    public $name;
    public $created_at;
    public $updated_at;
}