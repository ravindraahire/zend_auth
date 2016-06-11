<?php
require_once 'vendor/autoload.php';

use phpreboot\tddworkshop\Calculator;

$calculator = new Calculator();

if (!isset($argv[1])) {
echo 'Operation missing.' . PHP_EOL;
exit(0);
}

try {
    switch ($argv[1]) {
        case 'add':
            $numbers = isset($argv[2]) ? $argv[2] : '';
            echo $calculator->process($numbers, 'add') . PHP_EOL;
            break;
        case 'multiply':
            $numbers = isset($argv[2]) ? $argv[2] : '';
            echo $calculator->process($numbers, 'multiply') . PHP_EOL;
        default:
            echo 'Please check the operator.' . PHP_EOL;
    }
} catch (\InvalidArgumentException $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}