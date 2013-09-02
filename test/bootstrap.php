<?php

$autoload = __DIR__ . '/vendor/autoload.php';

if (!file_exists($autoload)) {
    die("Autoload missing. Please run 'composer install' before you execute the tests.");
}

require $autoload;
