<?php

use App\Models\RangeType;

/**
 * Returns an ordinal version of a number
 * 
 * @return string
 */
function ordinal(int $number): string
{
    $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
    if(($number % 100) >= 11)
        return $number . 'th';
    else
        return $number . $ends[$number % 10];
}

// CONCERNING MONEY

/**
 * Returns a number as money
 * 
 * @param float $amount 
 * @param string $symbol
 * 
 * @return string 
 */
function amountToMoney(float $amount, string $symbol){
    $prefix = $symbol;
    if ($amount < 0)
    {
        $amount = abs($amount);
        $prefix = '-' . $prefix;
    }
    return $prefix . number_format($amount, 2, '.', ',');
}

function naira(float $amount)
{
    return amountToMoney($amount, '₦');
}

function percentage_fraction(float $number, float $percentage)
{
    return ($number * $percentage)/100;
}

function percent(float $number, float $fraction)
{
    return ($fraction/$number) * 100;
}

function getValueFromRange(float $amount, RangeType $rangeType)
{
    return $rangeType->getValue($amount);
}