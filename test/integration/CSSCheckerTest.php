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
		
		$t4 = $this->checker->getClassesInSelectorString('.hello > .world');
        $this->assertEquals(array('hello','world'), $t4);
		
		
		$t5 = $this->checker->getClassesInSelectorString('.author-matches .li-pub-cluster-small .pub-cluster-small .btn + .btn');
        $this->assertCount(5, $t5);
    }
}
