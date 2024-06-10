<?php

namespace App\Helpers;

class NumberToWordsHelper
{
    protected static $units = [
        '', 'یک', 'دو', 'سه', 'چهار', 'پنج', 'شش', 'هفت', 'هشت', 'نه', 'ده', 'یازده', 'دوازده', 'سیزده', 'چهارده', 'پانزده', 'شانزده', 'هفده', 'هجده', 'نوزده'
    ];

    protected static $tens = [
        '', '', 'بیست', 'سی', 'چهل', 'پنجاه', 'شصت', 'هفتاد', 'هشتاد', 'نود'
    ];

    protected static $hundreds = [
        '', 'صد', 'دویست', 'سیصد', 'چهارصد', 'پانصد', 'ششصد', 'هفتصد', 'هشتصد', 'نهصد'
    ];

    protected static $thousands = [
        '', 'هزار', 'میلیون', 'میلیارد'
    ];

    public static function convertNumberToWords($number)
    {
        if ($number == 0) {
            return 'صفر';
        }

        $string = '';

        // اعداد را به سه رقم سه رقم تقسیم کنید
        $numberStr = str_pad($number, ceil(strlen($number) / 3) * 3, '0', STR_PAD_LEFT);

        $chunks = str_split($numberStr, 3);

        foreach ($chunks as $i => $chunk) {
            $chunk = (int)$chunk;

            if ($chunk) {
                $string .= self::convertThreeDigitNumber($chunk) . ' ' . self::$thousands[count($chunks) - $i - 1] . ' و ';
            }
        }

        return rtrim($string, ' و ') . ' ریال';
    }

    protected static function convertThreeDigitNumber($number)
    {
        $hundred = floor($number / 100);
        $remainder = $number % 100;
        $tens = floor($remainder / 10);
        $units = $remainder % 10;

        $string = self::$hundreds[$hundred];

        if ($remainder) {
            if ($string) {
                $string .= ' و ';
            }

            if ($remainder < 20) {
                $string .= self::$units[$remainder];
            } else {
                $string .= self::$tens[$tens];

                if ($units) {
                    $string .= ' و ' . self::$units[$units];
                }
            }
        }

        return $string;
    }
}

