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




    }
