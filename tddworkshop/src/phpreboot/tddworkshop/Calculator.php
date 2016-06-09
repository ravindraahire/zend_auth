<?php
namespace phpreboot\tddworkshop;

class Calculator
{
    public function process($numbers = '', $processName = 'add')
    {
        if (empty($numbers)) {
            return 0;
        }

        if (!is_string($numbers)) {
            throw new \InvalidArgumentException('Parameters must be a string');
        }

        $numbers = str_replace(array('\n',':'), ",", $numbers);
        $numbersArray = explode(",", $numbers);

        if (array_filter($numbersArray, 'is_numeric') !== $numbersArray) {
            throw new \InvalidArgumentException('Parameters string must contain numbers');
        }
		
        if ($negativeNumbers = array_filter($numbersArray, function($v) {return $v < 0;})) {
                $negativeNumbersStr = implode(',', $negativeNumbers);
                throw new \InvalidArgumentException('Negative numbers ('. $negativeNumbersStr .') not allowed');
        }
        
        $numbersArray = array_filter($numbersArray, function($v) {return $v < 1000;});

        $process = ($processName === 'add') ? 'array_sum': 'array_product';
        return $process($numbersArray);
    }
}