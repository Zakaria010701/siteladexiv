<?php

namespace App\Support;

class Calculator
{
    /**
     * Apply the given discount to the target price
     */
    public static function applyDiscount(float $target, float $discount, int $decimals = 2, bool $rate = false): float
    {
        if ($rate) {
            return round($target * (1 - $discount / 100), $decimals);
        }

        return round($target - $discount, $decimals);
    }

    /**
     * Remove the given discount from the target price
     */
    public static function removeDiscount(float $target, float $discount, int $decimals = 2, bool $rate = false): float
    {
        if ($rate) {
            return round($target / (1 - $discount / 100), $decimals);
        }

        return round($target + $discount, $decimals);
    }

    /**
     * Get the discount amount for the target price from discount percentage
     */
    public static function getDiscountAmmount(float $target, float $discount, int $decimals = 2, bool $net = true): float
    {
        if ($net) {
            return round($target - Calculator::applyDiscount($target, $discount, $decimals, true), $decimals);
        }

        return round(Calculator::removeDiscount($target, $discount, $decimals, true) - $target, $decimals);
    }

    public static function getDiscountPercentage(float $target, float $discount, int $decimals = 2): float
    {
        $targetPercentage = $target / 100;
        if ($targetPercentage == 0) {
            return 0;
        }

        return round($discount / ($targetPercentage), $decimals);
    }

    /**
     * Apply the given tax to the target price
     */
    public static function applyTax(float $target, float $tax, int $decimals = 2, bool $rate = false): float
    {
        if ($rate) {
            return round($target * (1 + $tax / 100), $decimals);
        }

        return round($target + $tax, $decimals);
    }

    /**
     * Remove the given tax from the target price
     */
    public static function removeTax(float $target, float $tax, int $decimals = 2, bool $rate = false): float
    {
        if ($rate) {
            return round($target / (1 + $tax / 100), $decimals);
        }

        return round($target - $tax, $decimals);
    }

    /**
     * Get the tax amount for the target price from tax percentage
     */
    public static function getTaxAmmount(float $target, float $tax, int $decimals = 2, bool $net = true): float
    {
        if ($net) {
            return round(Calculator::applyTax($target, $tax, $decimals, true) - $target, $decimals);
        }

        return round($target - Calculator::removeTax($target, $tax, $decimals, true), $decimals);
    }

    /**
     * Apply the given quantity to the target price
     */
    public static function applyQuantity(float $target, float $quantity, int $decimals = 2): float
    {
        return round($target * $quantity, $decimals);
    }

    /**
     * Remove the given quantity from the target price
     */
    public static function removeQuantity(float $target, float $quantity, int $decimals = 2): float
    {
        return round($target / $quantity, $decimals);
    }
}
