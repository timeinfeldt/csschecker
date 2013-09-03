--TEST--
System test
--FILE--
<?php
$_SERVER['argc'] = 3;
$_SERVER['argv'] = array(
    '',
    __DIR__ . '/html',
    __DIR__ . '/css'
);
require __DIR__ . '/../../csschecker';
--EXPECTF--
#!/usr/bin/env php


CSS check finished after %f seconds.

csschecker\checks\SelectorLengthCheck
========================
.nesting .five .levels .deep .used-class: Selector is over 4 levels deep.
.nesting .five .levels .deep .not-used-class: Selector is over 4 levels deep.
csschecker\checks\NoUsageCheck
========================
not-used-class: Not used.
nesting: Not used.
five: Not used.
levels: Not used.
deep: Not used.
action-dislike: Not used.

