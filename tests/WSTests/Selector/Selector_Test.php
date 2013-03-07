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
    //  tests for input:[type=]
    // +----------------------------------------------------------------------+
    function test_text()
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
    }
    function test_textarea()
    {
        $selector = $this->selector;
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

    }
    function test_hidden()
    {
        // test Selector_Hidden

        $selector = $this->selector;
        $sel = $selector->getInstance( 'hidden', 'test' );

        $text = 'text <strong>bold</strong> output';
        $safe = $this->h( $text );
        $form = (string) $sel->popHtml( 'form', $text );

        $this->assertEquals( 'WScore\Selector\Element_Hidden', get_class( $sel ) );
        $this->assertEquals( $safe, $sel->popHtml( 'name', $text ) );
        $this->assertContains( $safe, $form );
        $this->assertEquals( '<input type="hidden" name="test" value="text &lt;strong&gt;bold&lt;/strong&gt; output" class="FormSelector" />', $form );

    }
    function test_mail()
    {
        // test Selector_Mail

        $selector = $this->selector;
        $sel = $selector->getInstance( 'mail', 'test' );

        $text = 'text <strong>bold</strong> output';
        $safe = $this->h( $text );
        $form = (string) $sel->popHtml( 'form', $text );

        $this->assertEquals( 'WScore\Selector\Element_Mail', get_class( $sel ) );
        $this->assertEquals( $safe, $sel->popHtml( 'name', $text ) );
        $this->assertContains( $safe, $form );
        $this->assertEquals( '<input type="mail" name="test" value="text &lt;strong&gt;bold&lt;/strong&gt; output" class="FormSelector" />', $form );

    }
    function test_password()
    {
        // test Selector_Mail

        $selector = $this->selector;
        $sel = $selector->getInstance( 'password', 'test' );

        $text = 'text <strong>bold</strong> output';
        $safe = $this->h( $text );
        $form = (string) $sel->popHtml( 'form', $text );

        $this->assertEquals( 'WScore\Selector\Element_Password', get_class( $sel ) );
        $this->assertEquals( $safe, $sel->popHtml( 'name', $text ) );
        $this->assertContains( $safe, $form );
        $this->assertEquals( '<input type="password" name="test" value="text &lt;strong&gt;bold&lt;/strong&gt; output" class="FormSelector" />', $form );

    }
    function test_date()
    {
        // test Selector_Mail

        $selector = $this->selector;
        $sel = $selector->getInstance( 'date', 'test' );

        $text = 'text <strong>bold</strong> output';
        $safe = $this->h( $text );
        $form = (string) $sel->popHtml( 'form', $text );

        $this->assertEquals( 'WScore\Selector\Element_Date', get_class( $sel ) );
        $this->assertEquals( $safe, $sel->popHtml( 'name', $text ) );
        $this->assertContains( $safe, $form );
        $this->assertEquals( '<input type="date" name="test" value="text &lt;strong&gt;bold&lt;/strong&gt; output" class="FormSelector" />', $form );

        // shows date like 2012.01.23.
        $htmlFilter = function( $val ) {
            return str_replace( '-', '.', $val );
        };
        $sel->setHtmlFilter( $htmlFilter );
        $dotted = (string) $sel->popHtml( 'html', '1999-12-31' );
        $this->assertEquals( '1999.12.31', $dotted );
    }
    // +----------------------------------------------------------------------+
    //  test on SelectDiv
    // +----------------------------------------------------------------------+
    function test_DateYMD()
    {
        // test Selector_SelYMD

        $selector = $this->selector;
        $sel = $selector->getInstance( 'dateYMD', 'test', 'start_y:1980' );

        $date = '1984-03-04';
        $form = (string) $sel->popHtml( 'form', $date );
        $slash= str_replace( '-', '/', $date );
        
        $this->assertEquals( 'WScore\Selector\Element_DateYMD', get_class( $sel ) );
        $this->assertEquals( $slash, $sel->popHtml( 'name', $date ) );
        $this->assertEquals( '<select name="test_y" class="FormSelector">
  <option value="1980">1980</option>
  <option value="1981">1981</option>
  <option value="1982">1982</option>
  <option value="1983">1983</option>
  <option value="1984" selected="selected">1984</option>
  <option value="1985">1985</option>
  <option value="1986">1986</option>
  <option value="1987">1987</option>
  <option value="1988">1988</option>
  <option value="1989">1989</option>
  <option value="1990">1990</option>
  <option value="1991">1991</option>
  <option value="1992">1992</option>
  <option value="1993">1993</option>
  <option value="1994">1994</option>
  <option value="1995">1995</option>
  <option value="1996">1996</option>
  <option value="1997">1997</option>
  <option value="1998">1998</option>
  <option value="1999">1999</option>
  <option value="2000">2000</option>
  <option value="2001">2001</option>
  <option value="2002">2002</option>
  <option value="2003">2003</option>
  <option value="2004">2004</option>
  <option value="2005">2005</option>
  <option value="2006">2006</option>
  <option value="2007">2007</option>
  <option value="2008">2008</option>
  <option value="2009">2009</option>
  <option value="2010">2010</option>
  <option value="2011">2011</option>
  <option value="2012">2012</option>
  <option value="2013">2013</option>
  <option value="2014">2014</option>
</select>
-<select name="test_m" class="FormSelector">
  <option value=" 1"> 1</option>
  <option value=" 2"> 2</option>
  <option value=" 3" selected="selected"> 3</option>
  <option value=" 4"> 4</option>
  <option value=" 5"> 5</option>
  <option value=" 6"> 6</option>
  <option value=" 7"> 7</option>
  <option value=" 8"> 8</option>
  <option value=" 9"> 9</option>
  <option value="10">10</option>
  <option value="11">11</option>
  <option value="12">12</option>
</select>
-<select name="test_d" class="FormSelector">
  <option value=" 1"> 1</option>
  <option value=" 2"> 2</option>
  <option value=" 3"> 3</option>
  <option value=" 4" selected="selected"> 4</option>
  <option value=" 5"> 5</option>
  <option value=" 6"> 6</option>
  <option value=" 7"> 7</option>
  <option value=" 8"> 8</option>
  <option value=" 9"> 9</option>
  <option value="10">10</option>
  <option value="11">11</option>
  <option value="12">12</option>
  <option value="13">13</option>
  <option value="14">14</option>
  <option value="15">15</option>
  <option value="16">16</option>
  <option value="17">17</option>
  <option value="18">18</option>
  <option value="19">19</option>
  <option value="20">20</option>
  <option value="21">21</option>
  <option value="22">22</option>
  <option value="23">23</option>
  <option value="24">24</option>
  <option value="25">25</option>
  <option value="26">26</option>
  <option value="27">27</option>
  <option value="28">28</option>
  <option value="29">29</option>
  <option value="30">30</option>
  <option value="31">31</option>
</select>
', $form );

    }
    // +----------------------------------------------------------------------+
    //  test on Radio/Check/Select
    // +----------------------------------------------------------------------+
    function test_select()
    {
        // test Selector_Mail

        $selector = $this->selector;
        $choices  = array(
            array( 'M', 'male' ),
            array( 'F', 'female' ),
        );
        $sel = $selector->getInstance( 'select', 'test', null, $choices );

        $text = $sel->popHtml( 'html', 'F' ); // should be female.
        $form = (string) $sel->popHtml( 'form', 'M' );

        $this->assertEquals( '<select name="test" class="FormSelector">
  <option value="M" selected="selected">male</option>
  <option value="F">female</option>
</select>
', $form );
        $this->assertEquals( 'female', $text );

    }
    function test_radio()
    {
        // test Selector_Mail

        $selector = $this->selector;
        $choices  = array(
            array( 'M', 'male' ),
            array( 'F', 'female' ),
        );
        $sel = $selector->getInstance( 'radio', 'test', null, $choices );

        $text = $sel->popHtml( 'html', 'F' ); // should be female.
        $form = (string) $sel->popHtml( 'form', 'M' );

        $this->assertEquals( '<div class="forms-DivList"><nl>
  <li><label><input type="radio" name="test" value="M" class="FormSelector" checked="checked" />male</label></li>
  <li><label><input type="radio" name="test" value="F" class="FormSelector" />female</label></li>
</nl>
</div>
', $form );
        $this->assertEquals( 'female', $text );

    }
    function test_check()
    {
        // test Selector_Mail

        $selector = $this->selector;
        $choices  = array(
            array( 'M', 'male' ),
            array( 'F', 'female' ),
        );
        $sel = $selector->getInstance( 'check', 'test', null, $choices );

        $text = $sel->popHtml( 'html', 'F' ); // should be female.
        $form = (string) $sel->popHtml( 'form', 'M' );

        $this->assertEquals( '<div class="forms-DivList"><nl>
  <li><label><input type="checkbox" name="test[]" value="M" class="FormSelector" checked="checked" />male</label></li>
  <li><label><input type="checkbox" name="test[]" value="F" class="FormSelector" />female</label></li>
</nl>
</div>
', $form );
        $this->assertEquals( 'female', $text );

    }
    function test_check_multiple()
    {
        // test Selector_Mail

        $selector = $this->selector;
        $choices  = array(
            array( 'M', 'male' ),
            array( 'F', 'female' ),
            array( 'U', 'unknown'),
            array( 'N', 'neutral' ),
        );
        $sel = $selector->getInstance( 'check', 'test', null, $choices );

        $text = $sel->popHtml( 'html', array( 'F', 'U' ) ); // should be female.
        $form = (string) $sel->popHtml( 'form', array( 'M', 'U' ) );

        $this->assertEquals( '<div class="forms-DivList"><nl>
  <li><label><input type="checkbox" name="test[]" value="M" class="FormSelector" checked="checked" />male</label></li>
  <li><label><input type="checkbox" name="test[]" value="F" class="FormSelector" />female</label></li>
  <li><label><input type="checkbox" name="test[]" value="U" class="FormSelector" checked="checked" />unknown</label></li>
  <li><label><input type="checkbox" name="test[]" value="N" class="FormSelector" />neutral</label></li>
</nl>
</div>
', $form );
        $this->assertEquals( 'female,unknown', $text );

    }
    function test_select_multiple()
    {
        // test Selector_Mail

        $selector = $this->selector;
        $choices  = array(
            array( 'M', 'male' ),
            array( 'F', 'female' ),
            array( 'U', 'unknown'),
            array( 'N', 'neutral' ),
        );
        $sel = $selector->getInstance( 'mult_select', 'test', null, $choices );

        $text = $sel->popHtml( 'html', array( 'F', 'U' ) ); // should be female.
        $form = (string) $sel->popHtml( 'form', array( 'M', 'U' ) );

        $this->assertEquals( 'female,unknown', $text );
        // TODO: fix test[] for multiple select.
        $this->assertEquals( '<select name="test[]" class="FormSelector" multiple="multiple">
  <option value="M" selected="selected">male</option>
  <option value="F">female</option>
  <option value="U" selected="selected">unknown</option>
  <option value="N">neutral</option>
</select>
', $form );

    }
    // +----------------------------------------------------------------------+
    function test_text_with_list()
    {
        $selector = $this->selector;
        $items = array( 'very good', 'excellent' );
        $sel = $selector->getInstance( 'text', 'test' );
        $sel->setItemData( $items );

        $text = 'text <strong>bold</strong> output';
        $safe = $this->h( $text );
        $form = (string) $sel->popHtml( 'form', $text );

        $this->assertEquals( 'WScore\Selector\Element_Text', get_class( $sel ) );
        $this->assertEquals( $safe, $sel->popHtml( 'name', $text ) );
        $this->assertContains( $safe, $form );
        $this->assertEquals( '<div>
  <input type="text" name="test" value="text &lt;strong&gt;bold&lt;/strong&gt; output" class="FormSelector" list="test_list" />
  <datalist id="test_list">
    <input type="option" name="" value="very good" />
    <input type="option" name="" value="excellent" />
  </datalist>
</div>
', $form );
    }
}


// test Selector_Text

