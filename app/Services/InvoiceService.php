<?php

namespace App\Services;




class InvoiceService
{
    public static function mergeProductStructures($productStructures)
    {
        $description = '';

        foreach ($productStructures as $key => $value) {
            if (strpos($key, 'spacer') !== false) {
                // اگر کلید شامل "spacer" باشد، فقط در آن صورت
                // فاصله‌گذار اضافی اضافه می‌شود
                $description .= " + p.v.b $value +";
            }elseif (strpos($key, 'laminate') !== false){

                $description .= "+ طلق $value +";

            } else {
                // در غیر این صورت، اطلاعات دیگر است
                $description .= "$value";
            }
        }

        // برگرداندن توضیحات ترکیب شده
        return trim($description);
    }

    public static function CalculateEnvironment($height, $width,$quantity)
    {
        $result = 2*($height+$width)*$quantity/1000;
        return $result;
    }
    public static function CalculateArea($height, $width)
    {
        $result = ($height * $width / 1000000 < 0.5) ? (($height * $width / 1000000 == 0) ? 0 : 0.5) : ($height * $width / 1000000);
        return $result;
    }
    public static function calculateAspectRatio($height, $width)
    {
        $area = self::CalculateArea((float)$height,(float)$width );

        if ($height == 0 || $width == 0) {
            return "";
        }

        if (min($height,$width) > 3210) {
            return "error";
        }

        if (max($height,$width) > 4880 && min($height,$width) < 3211 && min($height,$width) > 2500){
            return 140;
        }

        if (max($height,$width) > 4880 && min($height,$width) <= 2500 ){
            return 140 ;
        }

        if ( max($height,$width) > 4500 && min($height,$width) < 3211 && min($height,$width) >= 2500) {
            return 75;
        }

        if ( max($height,$width) > 4500 &&  min($height,$width) <= 2500) {
            return 75;
        }

        if ( min($height,$width) > 2440 && min($height,$width)>= 2500) {
            return 35;
        }

        if ( max($height,$width)> 3660 || ( min($height,$width) > 2440 && min($height,$width) <= 2500)) {
            return 20;
        }

        if ( max($height,$width) <= 3660 &&  min($height,$width) <= 2440 && $area > 6 && $area <= 8.9304) {
            return 15;
        }

        if ( max($height,$width) <= 3660 && min($height,$width) <= 2440 && $area <= 6) {
            return 0;
        }

        return "";
    }








}
