<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'english_name',
    ];

    // محاسبه قیمت نهایی بر اساس گزینه‌های انتخابی
    protected $basePrice = 6200000; // قیمت پایه
    protected $basePricDouble = 10900000; // قیمت پایه برای شیشه خام
    protected $basePriceDoubleSecurit = 10200000; // قیمت پایه برای یک یا هر دو شیشه سکوریت

    protected $Doublethickness =700000;

    protected $options = [
        'thickness' => [
            3 => 0, // قیمت اضافی برای 3 میلیمتر
            3.5 => 0,
            4 => 0,
            5 => 0 ,
            6 => 90000,
            8 => 280000 ,
            10 => 490000,
            12 => 670000,
            15 => 1410000, // قیمت اضافی برای 15 میلیمتر
        ],
        'color' => [
            'دودی' => 650000,
            'برنز' => 400000,
            'رفلکس طلایی' => 350000,
            'رفلکس نقره ای' => 350000,
            'ساتینا' =>1300000,
            'سوپر کلیر' => 0,
        ],
        'Material'=>[
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
            'چسب IG' => 5300000,
            'چسب SG' => 7200000,
        ],

        'laminate' => [
            0.38 => 8400000, // قیمت اضافی برای طلق 0.38
            0.76 => 10900000, // قیمت اضافی برای طلق 0.76
            1.52 => 16100000, // قیمت اضافی برای طلق 1.52
        ]
    ];

    public function calculatePrice($selectedOptionsList) {
        $totalFinalPrice = 0;

        foreach ($selectedOptionsList as $selectedOptions) {
            $finalPrice = $this->basePrice;

            // محاسبه قیمت بر اساس ضخامت
            if (isset($selectedOptions['thickness']) && array_key_exists($selectedOptions['thickness'], $this->options['thickness'])) {
                $finalPrice += $this->options['thickness'][$selectedOptions['thickness']];
            }
            // محاسبه قیمت بر اساس رنگ
            if (isset($selectedOptions['color'])) {
                if ($selectedOptions['color'] === 'ساتینا') {
                    // قیمت ثابت برای شیشه ساتینا
                    $finalPrice += $this->options['color']['ساتینا'];
                } elseif (array_key_exists($selectedOptions['color'], $this->options['color'])) {
                    // قیمت به ازای هر میلیمتر برای شیشه‌های دیگر
                    $thickness = $selectedOptions['thickness'];
                    $colorPrice = $this->options['color'][$selectedOptions['color']] * $thickness;
                    $finalPrice += $colorPrice;
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

            if (isset($selectedOptions['Material']) && $selectedOptions['Material'] == 'سکوریت') {
                $temperedCount++;
            }
            if ($temperedCount > 0) {
                $finalPrice += $temperedCount * $this->options['Material']['سکوریت'];
            }

            // محاسبه قیمت بر اساس ضخامت
            if (isset($selectedOptions['thickness'])) {
                $finalPrice += $selectedOptions['thickness'] * $this->Doublethickness ;
            }
            // محاسبه قیمت بر اساس رنگ
            if (isset($selectedOptions['color'])) {
                if ($selectedOptions['color'] === 'ساتینا') {
                    // قیمت ثابت برای شیشه ساتینا
                    $finalPrice += $this->options['color']['ساتینا'];
                } elseif (array_key_exists($selectedOptions['color'], $this->options['color'])) {
                    // قیمت به ازای هر میلیمتر برای شیشه‌های دیگر
                    $thickness = $selectedOptions['thickness'];
                    $colorPrice = $this->options['color'][$selectedOptions['color']] * $thickness;
                    $finalPrice += $colorPrice;
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

    public function calculatePriceDouble($selectedOptionsList) {
        $isTempered = false;
        $temperedCount = 0;
        $totalThickness = 0;

        foreach ($selectedOptionsList as $selectedOptions) {
            if (isset($selectedOptions['Material']) && $selectedOptions['Material'] == 'سکوریت') {
                $isTempered = true;
                $temperedCount++;
            }
            if (isset($selectedOptions['thickness'])) {
                $totalThickness += $selectedOptions['thickness'];
            }
        }

        $finalPrice = $isTempered ? $this->basePriceDoubleSecurit : $this->basePricDouble;

        if ($temperedCount > 0) {
            $finalPrice += $temperedCount * $this->options['Material']['سکوریت'];
        }

        if ($totalThickness > 8) {
            $extraThickness = $totalThickness - 8;
            $finalPrice += $extraThickness * $this->Doublethickness;
        }

        foreach ($selectedOptionsList as $selectedOptions) {
            // محاسبه قیمت بر اساس نوع شیشه و ضخامت
            if (isset($selectedOptions['color']) && isset($selectedOptions['thickness'])) {
                $color = $selectedOptions['color'];
                $thickness = $selectedOptions['thickness'];
                if (array_key_exists($color, $this->options['color'])) {
                    $finalPrice += $thickness * $this->options['color'][$color];
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

        return $finalPrice;
    }





}
