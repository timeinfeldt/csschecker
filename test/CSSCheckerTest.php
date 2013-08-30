<?php

require_once(__DIR__ . '/../src/classes.php');

class CSSCheckerTest extends PHPUnit_Framework_TestCase{

    public function testGetClassesInSelectorString() {
		$this->checker = new CssChecker;
		
		$t1 = $this->checker->getClassesInSelectorString('.hello .world');
        $this->assertEquals(array('hello','world'), $t1);
		
		$t2 = $this->checker->getClassesInSelectorString('#hello .world');
        $this->assertEquals(array('world'), $t2);
		
		$t3 = $this->checker->getClassesInSelectorString('.hello.world');
        $this->assertEquals(array('hello','world'), $t3);
    }
}