<?php

namespace csschecker;

class CSSCheckerTest extends \PHPUnit_Framework_TestCase{

    private $checker;

    public function testGetClassesInSelectorString() {
		$this->checker = new CssChecker();
		
		$t1 = $this->checker->getClassesInSelectorString('.hello .world');
        $this->assertEquals(array('hello','world'), $t1);
		
		$t2 = $this->checker->getClassesInSelectorString('#hello .world');
        $this->assertEquals(array('world'), $t2);
		
		$t3 = $this->checker->getClassesInSelectorString('.hello.world');
        $this->assertEquals(array('hello','world'), $t3);
    }
}
