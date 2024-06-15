<?php

namespace App\Services;




use Illuminate\Support\Facades\Log;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;

class CalculationService
{


    public function CalculateEnvironment($height, $width,$quantity)
    {
        $result = 2*($height+$width)*$quantity/1000;
        return $result;
    }
    public function CalculateArea($height, $width)
    {
        $result = ($height * $width / 1000000 < 0.5) ? (($height * $width / 1000000 == 0) ? 0 : 0.5) : ($height * $width / 1000000);
        return $result;
    }
    public function calculateAspectRatio($height, $width)
    {
        $area = $this->CalculateArea((float)$height,(float)$width );

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


//TODO:درست کردن خواندن قیمت ها از دیتابیس
    // محاسبه قیمت نهایی بر اساس گزینه‌های انتخابی
    protected $basePrice = 6200000; // قیمت پایه
    protected $basePricDouble = 10900000; // قیمت پایه برای شیشه خام
    protected $basePriceDoubleSecurit = 10200000; // قیمت پایه برای یک یا هر دو شیشه سکوریت

    protected $Doublewidth =700000;

    protected $options = [
        'width' => [
            3 => 0, // قیمت اضافی برای 3 میلیمتر
            3.5 => 0,
            4 => 0,
            5 => 0 ,
            6 => 900000,
            8 => 2800000 ,
            10 => 4900000,
            12 => 6700000,
            15 => 14100000, // قیمت اضافی برای 15 میلیمتر
        ],
        'type' => [
            'دودی' => 650000,
            'برنز' => 400000,
            'رفلکس طلایی' => 350000,
            'رفلکس نقره ای' => 350000,
            'ساتینا' =>1300000,
            'ساتینا(زبرا)' => 2450000,
            'سوپر کلیر' => 0,
        ],
        'material'=>[
            'خام' => 0,
            'سکوریت' => 1500000,
        ],
        'spacer'=>[
            10 => 0,
            12 => 0,
            14 => 300000,
            16 => 600000,
            18 => 800000,
            20 => 1100000,
            22 => 1400000,
            24 => 1700000,
            26 => 2000000,
            28 => 2300000,
        ],
        'spacerColor'=>[
            'نقره ای'=>0,
            'مشکی' =>150000,
        ],
        'adhesive' => [
            'پلی سولفاید'=> 0,
            'سیلیکون IG' => 5300000,
            'سیلیکون SG' => 7200000,
        ],

        'laminate' => [
            '0/38' => 8400000, // قیمت اضافی برای طلق 0.38
            '0/76' => 10900000, // قیمت اضافی برای طلق 0.76
            '1/52' => 16100000, // قیمت اضافی برای طلق 1.52
        ],

        'laminateColor'=>[
            'normal' => 0,
            'hued' =>4400000,
        ],
    ];

    public function calculatePriceScorit($selectedOptionsList) {
        $totalFinalPrice = 0;

        foreach ($selectedOptionsList as $selectedOptions) {
            $finalPrice = $this->basePrice;

            // محاسبه قیمت بر اساس ضخامت
            if (isset($selectedOptions['width']) && array_key_exists($selectedOptions['width'], $this->options['width'])) {
                $finalPrice += $this->options['width'][$selectedOptions['width']];
            }
            // محاسبه قیمت بر اساس رنگ
            if (isset($selectedOptions['type'])) {
                if ($selectedOptions['type'] === 'ساتینا' ) {
                    // قیمت ثابت برای شیشه ساتینا
                    $finalPrice += $this->options['type']['ساتینا'];
                }elseif ($selectedOptions['type'] === 'ساتینا(زبرا)'){
                    $finalPrice += $this->options['type']['ساتینا(زبرا)'];
                } elseif (array_key_exists($selectedOptions['type'], $this->options['type'])) {
                    // قیمت به ازای هر میلیمتر برای شیشه‌های دیگر
                    $width = $selectedOptions['width'];
                    $typePrice = $this->options['type'][$selectedOptions['type']] * $width;
                    $finalPrice += $typePrice;
                }
            }

            // محاسبه قیمت بر اساس لمینت
            if (isset($selectedOptions['laminate']) && array_key_exists($selectedOptions['laminate'], $this->options['laminate'])) {
                $finalPrice += $this->options['laminate'][$selectedOptions['laminate']];
            }

            $totalFinalPrice += $finalPrice;
        }

        return $totalFinalPrice;
    }

    public function calculatePriceLaminate($selectedOptionsList) {
        $totalFinalPrice = 0;

        foreach ($selectedOptionsList as $selectedOptions) {
            $finalPrice = 0;
            $temperedCount = 0;

            if (isset($selectedOptions['material']) && $selectedOptions['material'] == 'سکوریت') {
                $temperedCount++;
            }


            if ($temperedCount > 0) {
                $finalPrice += $temperedCount * $this->options['material']['سکوریت'];
            }


            // محاسبه قیمت بر اساس ضخامت
            if (isset($selectedOptions['width'])) {
                $finalPrice += $selectedOptions['width'] * $this->Doublewidth;
            }

            // محاسبه قیمت بر اساس رنگ
            if (isset($selectedOptions['type'])) {
                if ($selectedOptions['type'] === 'ساتینا') {
                    // قیمت ثابت برای شیشه ساتینا
                    $finalPrice += $this->options['type']['ساتینا'];
                }
                elseif ($selectedOptions['type'] === 'ساتینا(زبرا)'){
                    $finalPrice += $this->options['type']['ساتینا(زبرا)'];
                }
                elseif (array_key_exists($selectedOptions['type'], $this->options['type'])) {
                    // قیمت به ازای هر میلیمتر برای شیشه‌های دیگر
                    $width = $selectedOptions['width'];
                    $typePrice = $this->options['type'][$selectedOptions['type']] * $width;
                    $finalPrice += $typePrice;
                }
            }

            // محاسبه قیمت بر اساس رنگ لمینت
            if (isset($selectedOptions['laminateColor']) && array_key_exists($selectedOptions['laminateColor'], $this->options['laminateColor'])) {
                $finalPrice += $this->options['laminateColor'][$selectedOptions['laminateColor']];
            }

            // محاسبه قیمت بر اساس لمینت
            if (isset($selectedOptions['laminate']) && array_key_exists($selectedOptions['laminate'], $this->options['laminate'])) {
                $finalPrice += $this->options['laminate'][$selectedOptions['laminate']];
            }

            $totalFinalPrice += $finalPrice;

        }

        // محاسبه تعداد تکرار هر دسته و افزودن 20% به قیمت نهایی برای هر تکرار اضافی
        $countType = 0;
        $countWidth = 0;
        $countMaterial = 0;
        $countLaminate = 0;

        foreach ($selectedOptionsList as $selectedOptions) {
            if (isset($selectedOptions['type']) && $selectedOptions['type']) {
                $countType++;
            }
            if (isset($selectedOptions['width']) && $selectedOptions['width']) {
                $countWidth++;
            }
            if (isset($selectedOptions['material']) && $selectedOptions['material']) {
                $countMaterial++;
            }
            if (isset($selectedOptions['laminate']) && $selectedOptions['laminate'] ) {
                $countLaminate++;
            }
        }
        // هر دسته اضافی 20% اضافه کند
        $repeats = min($countType, $countWidth, $countMaterial, $countLaminate) - 1;

        if ($repeats > 0) {
            $totalFinalPrice += $totalFinalPrice * 20  / 100 * $repeats;
        }

        // تبدیل نتیجه نهایی به عدد صحیح
        return $totalFinalPrice;
    }


    public function calculatePriceDouble($selectedOptionsList) {
        $isTempered = false;
        $temperedCount = 0;
        $totalThickness = 0;

        // شمارنده‌های الگوهای مورد نظر
        $typeMaterialCounts = [];
        $spacerCounts = [];

        foreach ($selectedOptionsList as $selectedOptions) {
            if (isset($selectedOptions['material']) && $selectedOptions['material'] == 'سکوریت') {
                $isTempered = true;
                $temperedCount++;
            }
            if (isset($selectedOptions['width'])) {
                $totalThickness += $selectedOptions['width'];
            }

            // شمارش ترکیب type و material
            if (isset($selectedOptions['type']) && isset($selectedOptions['width']) && isset($selectedOptions['material'])) {
                $typeMaterialKey = $selectedOptions['type'] . '-' . $selectedOptions['width'] . '-' . $selectedOptions['material'];
                if (!isset($typeMaterialCounts[$typeMaterialKey])) {
                    $typeMaterialCounts[$typeMaterialKey] = 0;
                }
                $typeMaterialCounts[$typeMaterialKey]++;
            }

            // شمارش spacer و spacerColor و adhesive
            if (isset($selectedOptions['spacer']) && isset($selectedOptions['spacerColor']) && isset($selectedOptions['adhesive'])) {
                $spacerKey = $selectedOptions['spacer'] . '-' . $selectedOptions['spacerColor'] . '-' . $selectedOptions['adhesive'];
                if (!isset($spacerCounts[$spacerKey])) {
                    $spacerCounts[$spacerKey] = 0;
                }
                $spacerCounts[$spacerKey]++;
            }
        }

        $finalPrice = $isTempered ? $this->basePriceDoubleSecurit : $this->basePricDouble;

        if ($temperedCount > 0) {
            $finalPrice += $temperedCount * $this->options['material']['سکوریت'];
        }

        if ($totalThickness > 8) {
            $extraThickness = $totalThickness - 8;
            $finalPrice += $extraThickness * $this->Doublewidth;
        }

        foreach ($selectedOptionsList as $selectedOptions) {
            // محاسبه قیمت بر اساس نوع شیشه و ضخامت
            if (isset($selectedOptions['type']) && isset($selectedOptions['width'])) {
                $type = $selectedOptions['type'];
                $width = $selectedOptions['width'];
                if ($selectedOptions['type'] === 'ساتینا') {
                    // قیمت ثابت برای شیشه ساتینا
                    $finalPrice += $this->options['type']['ساتینا'];
                }
                elseif ($selectedOptions['type'] === 'ساتینا(زبرا)'){
                    $finalPrice += $this->options['type']['ساتینا(زبرا)'];
                }
                elseif (array_key_exists($type, $this->options['type'])) {
                    $finalPrice += $width * $this->options['type'][$type];
                }
            }

            // محاسبه قیمت بر اساس ضخامت فاصله‌دهنده
            if (isset($selectedOptions['spacer'])) {
                $spacer = $selectedOptions['spacer'];
                if (array_key_exists($spacer, $this->options['spacer'])) {
                    $finalPrice += $this->options['spacer'][$spacer];
                } else {
                    return "ارور"; // اگر ضخامت فاصله‌دهنده نامعتبر باشد
                }
            }

            // محاسبه قیمت بر اساس رنگ فاصله‌دهنده
            if (isset($selectedOptions['spacerColor'])) {
                $spacerColor = $selectedOptions['spacerColor'];
                $spacer = $selectedOptions['spacer'] ?? 0;
                if (array_key_exists($spacerColor, $this->options['spacerColor'])) {
                    $finalPrice += $spacer * $this->options['spacerColor'][$spacerColor];
                } else {
                    return "ارور"; // اگر رنگ فاصله‌دهنده نامعتبر باشد
                }
            }

            // محاسبه قیمت بر اساس چسب
            if (isset($selectedOptions['adhesive'])) {
                $adhesive = $selectedOptions['adhesive'];
                if (array_key_exists($adhesive, $this->options['adhesive'])) {
                    $finalPrice += $this->options['adhesive'][$adhesive];
                } else {
                    return "ارور"; // اگر نوع چسب نامعتبر باشد
                }
            }
        }

        // بررسی تعداد تکرار الگوهای مشابه
        $repeatedTypeMaterial = false;
        foreach ($typeMaterialCounts as $count) {
            if ($count >= 3) {
                $repeatedTypeMaterial = true;
                break;
            }
        }

        $repeatedSpacer = false;
        foreach ($spacerCounts as $count) {
            if ($count >= 2) {
                $repeatedSpacer = true;
                break;
            }
        }

        // اگر هر دو الگو به تعداد مشخص تکرار شدند، مبلغ اضافی 4100000 را اضافه کنید
        if ($repeatedTypeMaterial && $repeatedSpacer) {
            $finalPrice += 4100000;
        }
        return $finalPrice;
    }

    public function calculatePriceDoubleLaminate($selectedOptionsList) {
        $temperedCount = 0;
        $totalThickness = 0;

        foreach ($selectedOptionsList as $selectedOptions) {
            if (isset($selectedOptions['material']) && $selectedOptions['material'] == 'سکوریت') {
                $temperedCount++;
            }
            if (isset($selectedOptions['width'])) {
                $totalThickness += $selectedOptions['width'];
            }


            // شمارش ترکیب type و material
            if (isset($selectedOptions['type']) && isset($selectedOptions['width']) && isset($selectedOptions['material'])) {
                $typeMaterialKey = $selectedOptions['type'] . '-' . $selectedOptions['width'] . '-' . $selectedOptions['material'];
                if (!isset($typeMaterialCounts[$typeMaterialKey])) {
                    $typeMaterialCounts[$typeMaterialKey] = 0;
                }
                $typeMaterialCounts[$typeMaterialKey]++;
            }

            // شمارش spacer و spacerColor و adhesive
            if (isset($selectedOptions['spacer']) && isset($selectedOptions['spacerColor']) && isset($selectedOptions['adhesive'])) {
                $spacerKey = $selectedOptions['spacer'] . '-' . $selectedOptions['spacerColor'] . '-' . $selectedOptions['adhesive'];
                if (!isset($spacerCounts[$spacerKey])) {
                    $spacerCounts[$spacerKey] = 0;
                }
                $spacerCounts[$spacerKey]++;
            }
        }

        $finalPrice =  $this->basePriceDoubleSecurit ;

        if ($temperedCount > 0) {
            $finalPrice += $temperedCount * $this->options['material']['سکوریت'];
        }

        if ($totalThickness > 8) {
            $extraThickness = $totalThickness - 8;
            $finalPrice += $extraThickness * $this->Doublewidth;
        }

        foreach ($selectedOptionsList as $selectedOptions) {
            // محاسبه قیمت بر اساس نوع شیشه و ضخامت
            if (isset($selectedOptions['type']) && isset($selectedOptions['width'])) {
                $type = $selectedOptions['type'];
                $width = $selectedOptions['width'];
                if ($selectedOptions['type'] === 'ساتینا') {
                    // قیمت ثابت برای شیشه ساتینا
                    $finalPrice += $this->options['type']['ساتینا'];
                }
                elseif ($selectedOptions['type'] === 'ساتینا(زبرا)'){

                    $finalPrice += $this->options['type']['ساتینا(زبرا)'];
                }
                elseif (array_key_exists($type, $this->options['type'])) {
                    $finalPrice += $width * $this->options['type'][$type];
                }
            }
            // محاسبه قیمت بر اساس رنگ لمینت
            if (isset($selectedOptions['laminateColor']) && array_key_exists($selectedOptions['laminateColor'], $this->options['laminateColor'])) {
                $finalPrice += $this->options['laminateColor'][$selectedOptions['laminateColor']];
            }

            // محاسبه قیمت بر اساس لمینت
            if (isset($selectedOptions['laminate']) && array_key_exists($selectedOptions['laminate'], $this->options['laminate'])) {
                $finalPrice += $this->options['laminate'][$selectedOptions['laminate']];
            }
            // محاسبه قیمت بر اساس ضخامت فاصله‌دهنده
            if (isset($selectedOptions['spacer'])) {
                $spacer = $selectedOptions['spacer'];
                if (array_key_exists($spacer, $this->options['spacer'])) {
                    $finalPrice += $this->options['spacer'][$spacer];
                } else {
                    return "ارور"; // اگر ضخامت فاصله‌دهنده نامعتبر باشد
                }
            }

            // محاسبه قیمت بر اساس رنگ فاصله‌دهنده
            if (isset($selectedOptions['spacerColor'])) {
                $spacerColor = $selectedOptions['spacerColor'];
                $spacer = $selectedOptions['spacer'] ?? 0;
                if (array_key_exists($spacerColor, $this->options['spacerColor'])) {
                    $finalPrice += $spacer * $this->options['spacerColor'][$spacerColor];
                } else {
                    return "ارور"; // اگر رنگ فاصله‌دهنده نامعتبر باشد
                }
            }

            // محاسبه قیمت بر اساس چسب
            if (isset($selectedOptions['adhesive'])) {
                $adhesive = $selectedOptions['adhesive'];
                if (array_key_exists($adhesive, $this->options['adhesive'])) {
                    $finalPrice += $this->options['adhesive'][$adhesive];
                } else {
                    return "ارور"; // اگر نوع چسب نامعتبر باشد
                }
            }
        }


        // بررسی تعداد تکرار الگوهای مشابه
        $repeatedTypeMaterial = false;
        foreach ($typeMaterialCounts as $count) {
            if ($count >= 3) {
                $repeatedTypeMaterial = true;
                break;
            }
        }

        $repeatedSpacer = false;
        foreach ($spacerCounts as $count) {
            if ($count >= 2) {
                $repeatedSpacer = true;
                break;
            }
        }

        // اگر هر دو الگو به تعداد مشخص تکرار شدند، مبلغ اضافی 4100000 را اضافه کنید
        if ($repeatedTypeMaterial && $repeatedSpacer) {
            $finalPrice += 4100000;
        }
        return $finalPrice;
    }








}
