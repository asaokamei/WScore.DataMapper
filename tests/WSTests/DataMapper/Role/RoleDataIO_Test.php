<?php
namespace WSTests\DataMapper\Role;

require( __DIR__ . '/../../../autoloader.php' );

class RoleDataIO_BasicTests extends \PHPUnit_Framework_TestCase
{
    /** @var mixed */
    public static $config = 'dsn=mysql:dbname=test_WScore username=admin password=admin';

    /** @var \WScore\DataMapper\RoleManager */
    public $rm;

    /** @var \WScore\DataMapper\EntityManager */
    public $em;

    public $friendEntity = '\WSTests\DataMapper\entities\friend';

    // +----------------------------------------------------------------------+
    static function setUpBeforeClass()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( __DIR__ . '/../../../../vendor/wscore/dicontainer/scripts/instance.php' );
        $container->set( '\Pdo', self::$config );
        /** @var $friend \WSTests\DataMapper\models\Friends */
        $friend = $container->get( '\WSTests\DataMapper\models\Friends' );
        $friend->setupTable();
        class_exists( '\WScore\DataMapper\Entity\EntityAbstract' );
        class_exists( '\WSTests\DataMapper\models\Friends' );
        class_exists( '\WSTests\DataMapper\entities\friend' );
    }

    /**
     *
     */
    function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( __DIR__ . '/../../../../vendor/wscore/dicontainer/scripts/instance.php' );
        $container->set( 'ContainerInterface', $container );
        $container->set( '\Pdo', self::$config );
        // set up persistence model
        $this->em = $container->get( '\WScore\DataMapper\EntityManager' );
        $this->rm = $container->get( '\WScore\DataMapper\RoleManager' );
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

    // +----------------------------------------------------------------------+
    function test_basic()
    {
        $this->assertEquals( 'WScore\DataMapper\RoleManager', get_class( $this->rm ) );
        $data   = $this->getFriendData(1);
        $friend = $this->em->newEntity( $this->friendEntity, $data );
        $role   = $this->rm->applyDataIO( $friend );
        $this->assertEquals( 'WScore\DataMapper\Role\DataIO', get_class( $role ) );
    }
    function test_load_data()
    {
        $data   = $this->getFriendData(1);
        $friend = $this->em->newEntity( $this->friendEntity );
        $role   = $this->rm->applyDataIO( $friend );
        $role->load( $data );
        foreach( $data as $key => $val ) {
            $this->assertEquals( $val, $friend[$key] );
        }
    }
    function test_validate()
    {
        $data   = $this->getFriendData(1);
        $friend = $this->em->newEntity( $this->friendEntity, $data );
        $role   = $this->rm->applyDataIO( $friend );
        $role->validate();
        $this->assertTrue( $role->isValid() );
    }
    function test_validate_inValid()
    {
        $data   = $this->getFriendData(1);
        $data[ 'friend_bday' ] = 'bad date format';
        $friend = $this->em->newEntity( $this->friendEntity, $data );
        $role   = $this->rm->applyDataIO( $friend );
        $role->validate();
        $this->assertFalse( $role->isValid() );
        $this->assertFalse( $role->isError( 'friend_name' ) );
        $this->assertTrue(  $role->isError( 'friend_bday' ) );
        $this->assertEquals( '', $role->getError( 'friend_name' ) );
        $this->assertEquals( 'invalid pattern with [0-9]{4}-[0-9]{2}-[0-9]{2}', $role->getError( 'friend_bday' ) );

        // test resetValid as well
        $role->resetValid();
        $this->assertNull( $role->isValid() );
    }
    function test_form_element()
    {
        $data   = $this->getFriendData(1);
        $friend = $this->em->newEntity( $this->friendEntity, $data );
        $role   = $this->rm->applyDataIO( $friend );
        // form should return selector object.
        $form = $role->form( 'friend_name' );
        $this->assertEquals( 'WScore\Selector\Element_Text', get_class( $form ) );
        // popHtml:html should return friend_name as text.
        $form = $role->popHtml( 'friend_name', 'html' );
        $this->assertEquals( $data[ 'friend_name' ], $form );
        // popHtml:form should return Elements object.
        $form = $role->popHtml( 'friend_name', 'form' );
        $this->assertEquals( 'WScore\Html\Elements', get_class( $form ) );

        // set default to return form/elements object.
        $role->setHtmlType( 'form' );
        $form = $role->popHtml( 'friend_name' );
        $this->assertEquals( 'WScore\Html\Elements', get_class( $form ) );
        // set default to return html value.
        $role->setHtmlType( 'html' );
        $form = $role->popHtml( 'friend_name' );
        $this->assertEquals( $data[ 'friend_name' ], $form );
    }
}