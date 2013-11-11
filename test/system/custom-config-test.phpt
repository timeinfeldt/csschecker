--TEST--
Loading a custom config file from the cli
--FILE--
<?php
$_SERVER['argc'] = 5;
$_SERVER['argv'] = array(
    '',
    '--config',
    __DIR__ . '/config/simple-config.json',
    __DIR__ . '/html',
    __DIR__ . '/css'
);
require __DIR__ . '/../../csschecker';
--EXPECTF--
#!/usr/bin/env php


CSS check finished after %f seconds.

csschecker\checks\NoUsageCheck
========================
not-used-class: Not used.
nesting: Not used.
five: Not used.
levels: Not used.
deep: Not used.
action-dislike: Not used.

