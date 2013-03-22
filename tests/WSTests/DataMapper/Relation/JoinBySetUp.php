<?php
namespace WSTests\DataMapper\Relation;

use \WSTests\DataMapper\entities\friend;

abstract class JoinBySetUp extends \PHPUnit_Framework_TestCase
{
    /** @var mixed */
    public static $config = 'dsn=mysql:dbname=test_WScore username=admin password=admin';

    public static $table = 'friend';

    /** @var \WScore\DataMapper\EntityManager */
    public $em;

    public $friendEntity = '\WSTests\DataMapper\entities\friend';

    public $groupEntity = '\WSTests\DataMapper\entities\group';

    public $fr2grEntity = '\WSTests\DataMapper\entities\fr2gr';

    // +----------------------------------------------------------------------+
    static function setUpBeforeClass()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
        $container->set( '\Pdo', self::$config );
        
        /** @var $friend \WSTests\DataMapper\models\Friends */
        $friend = $container->get( '\WSTests\DataMapper\models\Friends' );
        $friend->setupTable();
        
        /** @var $friend \WSTests\DataMapper\models\Groups */
        $group = $container->get( '\WSTests\DataMapper\models\Groups' );
        $group->setupTable();

        /** @var $friend \WSTests\DataMapper\models\Fr2gr */
        $fr2gr = $container->get( '\WSTests\DataMapper\models\Fr2gr' );
        $fr2gr->setupTable();
        
        class_exists( '\WScore\DataMapper\Entity\EntityAbstract' );
        class_exists( '\WSTests\DataMapper\entities\friend' );
        class_exists( '\WSTests\DataMapper\entities\group' );
        class_exists( '\WSTests\DataMapper\entities\fr2gr' );
    }

    /**
     * @param int $idx
     * @return array
     */
    function getFriendData( $idx=1 )
    {
        /** @var $model \WSTests\DataMapper\models\Friends */
        $model = $this->em->getModel( $this->friendEntity );
        return $model->getFriendData( $idx );
    }

    function getGroupData( $idx=1 )
    {
        /** @var $model \WSTests\DataMapper\models\Groups */
        $model = $this->em->getModel( $this->groupEntity );
        return $model->makeGroup( $idx );
    }

    /**
     *
     */
    function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
        $container->set( 'ContainerInterface', $container );
        $container->set( '\Pdo', self::$config );
        // set up persistence model
        $this->em = $container->get( '\WScore\DataMapper\EntityManager' );
    }
}
