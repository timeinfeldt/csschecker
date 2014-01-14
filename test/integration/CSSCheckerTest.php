<?php

namespace csschecker;

class CSSCheckerTest extends \PHPUnit_Framework_TestCase {

    private $checker;

    public function testGetClassesInSelectorString() {
        $this->checker = new CssChecker();

        $t1 = Helpers::getClassesInSelectorString('.hello .world');
        $this->assertEquals(array('hello', 'world'), $t1);

        $t2 = Helpers::getClassesInSelectorString('#hello .world');
        $this->assertEquals(array('world'), $t2);

        $t3 = Helpers::getClassesInSelectorString('.hello.world');
        $this->assertEquals(array('hello', 'world'), $t3);

        $t4 = Helpers::getClassesInSelectorString('.hello > .world');
        $this->assertEquals(array('hello', 'world'), $t4);
    }

    public function testGetElementsInSelectorString() {
        $this->checker = new CssChecker();

        $t1 = Helpers::getElementsInSelectorString('.hello .world .btn + .btn h4:hover');
        $this->assertCount(5, $t1);

        $t2 = Helpers::getElementsInSelectorString('.btn-group > .dropdown + .btn');
        $this->assertCount(3, $t2);
    }
}
