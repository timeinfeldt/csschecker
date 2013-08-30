#!/usr/bin/env php

<?php

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/src/classes.php');

$checks = array(
    'MultipleDefinitionsCheck' => array(
        'maxDefinitions' => 2
    ),
    'SelectorLengthCheck' => array(
        'maxSelectorLength' => 4
    ),
    'NoUsageCheck' => array()
);

$args = $_SERVER['argv'];
unset($args[0]);

$checker = new CssChecker;
$report = new Report;

$checker->runChecks($args, $checks, $report);