<?php
namespace WSTests\Selector;

require( __DIR__ . '/../../autoloader.php' );

class Selector_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\Selector\Selector */
    public $selector;

    function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( __DIR__ . '/../../../vendor/wscore/dicontainer/scripts/instance.php' );

        /** @var $selector \WScore\Selector\Selector */
        $this->selector = $container->get( '\WScore\Selector\Selector' );

    }
    function h( $text ) {
        return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
    }
    // +----------------------------------------------------------------------+
    function test_0()
    {
        $selector = $this->selector;
        $sel = $selector->getInstance( 'text', 'test' );
        
        $text = 'text <strong>bold</strong> output';
        $safe = $this->h( $text );
        $form = (string) $sel->popHtml( 'form', $text );
        
        $this->assertEquals( 'WScore\Selector\Element_Text', get_class( $sel ) );
        $this->assertEquals( $safe, $sel->popHtml( 'name', $text ) );
        $this->assertContains( $safe, $form );
        $this->assertEquals( '<input type="text" name="test" value="text &lt;strong&gt;bold&lt;/strong&gt; output" class="FormSelector" />', $form );
        
        // test Selector_Textarea

        $sel = $selector->getInstance( 'textarea', 'test' );
        $text = "text <strong>bold</strong>\n output";
        $safe = $this->h( $text );
        $form = (string) $sel->popHtml( 'form', $text );

        $this->assertEquals( 'WScore\Selector\Element_Textarea', get_class( $sel ) );
        $this->assertEquals( nl2br( $safe ), $sel->popHtml( 'name', $text ) );
        $this->assertContains( $safe, $form );
        $this->assertEquals( 
            '<textarea name="test" class="FormSelector">text &lt;strong&gt;bold&lt;/strong&gt;'
                . "\n"
                . ' output</textarea>', 
            $form );
        
        // test Selector_Hidden

        $sel = $selector->getInstance( 'hidden', 'test' );

        $text = 'text <strong>bold</strong> output';
        $safe = $this->h( $text );
        $form = (string) $sel->popHtml( 'form', $text );

        $this->assertEquals( 'WScore\Selector\Element_Hidden', get_class( $sel ) );
        $this->assertEquals( $safe, $sel->popHtml( 'name', $text ) );
        $this->assertContains( $safe, $form );
        $this->assertEquals( '<input type="hidden" name="test" value="text &lt;strong&gt;bold&lt;/strong&gt; output" class="FormSelector" />', $form );

        // test Selector_Mail

        $sel = $selector->getInstance( 'mail', 'test' );

        $text = 'text <strong>bold</strong> output';
        $safe = $this->h( $text );
        $form = (string) $sel->popHtml( 'form', $text );

        $this->assertEquals( 'WScore\Selector\Element_Mail', get_class( $sel ) );
        $this->assertEquals( $safe, $sel->popHtml( 'name', $text ) );
        $this->assertContains( $safe, $form );
        $this->assertEquals( '<input type="mail" name="test" value="text &lt;strong&gt;bold&lt;/strong&gt; output" class="FormSelector" />', $form );

// test Selector_SelYMD

        $sel = $selector->getInstance( 'dateYMD', 'test', 'start_y:1980' );

        echo 'class: ' . get_class( $sel ) . "\n";
        echo 'html: ' . $sel->popHtml( 'name', "1984-03-31" ) . "\n";
        echo 'form: ' . $sel->popHtml( 'form', "1984-03-31" ) . "\n";

    }
}


// test Selector_Text

