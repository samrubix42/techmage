<?php

namespace App\Support;

/**
 * Converts a rupee amount to words using the Indian numbering system
 * (thousand, lakh, crore), e.g. 125430.50 =>
 * "Rupees One Lakh Twenty Five Thousand Four Hundred Thirty and Fifty Paise Only".
 */
class AmountInWords
{
    private const ONES = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
        'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen',
    ];

    private const TENS = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

    public static function rupees(float $amount): string
    {
        $amount = round(max(0, $amount), 2);
        $rupees = (int) floor($amount);
        $paise = (int) round(($amount - $rupees) * 100);

        $words = 'Rupees '.($rupees === 0 ? 'Zero' : self::convert($rupees));

        if ($paise > 0) {
            $words .= ' and '.self::convert($paise).' Paise';
        }

        return $words.' Only';
    }

    private static function convert(int $number): string
    {
        $parts = [];

        foreach ([[10000000, 'Crore'], [100000, 'Lakh'], [1000, 'Thousand'], [100, 'Hundred']] as [$value, $label]) {
            if ($number >= $value) {
                $count = intdiv($number, $value);
                $parts[] = ($value === 10000000 && $count >= 100 ? self::convert($count) : self::belowHundred($count)).' '.$label;
                $number %= $value;
            }
        }

        if ($number > 0) {
            $parts[] = self::belowHundred($number);
        }

        return implode(' ', $parts);
    }

    private static function belowHundred(int $number): string
    {
        if ($number < 20) {
            return self::ONES[$number];
        }

        return trim(self::TENS[intdiv($number, 10)].' '.self::ONES[$number % 10]);
    }
}
