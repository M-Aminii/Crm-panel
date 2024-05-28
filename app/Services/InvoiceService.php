<?php

namespace App\Services;




use Illuminate\Support\Facades\Log;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;

class InvoiceService
{

    public static function generateNewSerial(): string
    {
        try {
            // جستجو برای یافتن آخرین سریال
            $latestSerial = \App\Models\Invoice::orderBy('created_at', 'desc')->value('serial_number');
            // اگر سریال وجود داشت، 4 رقم به آن اضافه کنید
            if ($latestSerial) {
                $newSerialNumber = 'f-s'.((int) substr($latestSerial, 3) + 4);
                return $newSerialNumber;
            } else {
                // اگر سریال وجود نداشت، سریال اولیه را برگردانید
                return 'f-s186524';
            }
        } catch (\Exception $e) {
            // لاگ کردن خطا
            Log::error('خطا در پردازش سریال: ' . $e->getMessage());
            // برگرداندن سریال پیش‌فرض در صورت بروز خطا
            return 'f-s186524';
        }
    }

/*    public static function information($user ,$buyer ,$item )
    {
        $sellerData= $user;
        // دریافت اطلاعات خریدار از ورودی کاربر
        $buyerData = $buyer;

        $client = new Party([
            'name'          => 'Roosevelt Lloyd',
            'phone'         => '(520) 318-9486',
            'custom_fields' => [
                'note'        => 'IDDQD',
                'business id' => '365#GG',
            ],
        ]);

        $customer = new Party([
            'name'          => 'name',
            'address'       => 'The Green Street 12',
            'code'          => '#22663214',
            'custom_fields' => [
                'order number' => '> 654321 <',
            ],
        ]);

        // دریافت اطلاعات آیتم‌ها از ورودی کاربر
        $itemsDatas = $item;

        // ایجاد شیء جدید از کتابخانه Laravel Invoices
        $invoice = Invoice::make('پیش فاکتور')
            ->series('f-s')
            ->status(__('invoices::invoice.paid'))
            ->sequencePadding(4)
            ->sequence(4990)
            ->seller($client)
            ->buyer($customer)
            ->serialNumberFormat('{SEQUENCE}/{SERIES}')
            ->date(now())
            ->dateFormat('m/d/Y')
            ->payUntilDays(14)
            ->currencySymbol('ریال')
            ->currencyCode('IRR')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator(',')
            ->currencyDecimalPoint(',')
            ->filename( $customer->name . '_invoice55');


        $items = [];
        foreach ($itemsDatas as $itemData) {

            $item = InvoiceItem::make($itemData['title'])
                ->description(self::mergeProductStructures($itemData['description']))
                ->pricePerUnit($itemData['price_per_unit'])
                ->quantity($itemData['quantity'])
                ->TechnicalDetails($itemData['technical_details']);

            $dimensions = [];
            $totalQuantity = 0;
            $totalArea = 0;
            $totalEnvironment =0;

            foreach ($itemData['dimensions'] as $key=> $data) {
                $area = self::CalculateArea($data['height'] ,$data['width']);
                $environment =self::CalculateEnvironment($data['height'] ,$data['width'] ,$data['quantity']);
                $over =self::calculateAspectRatio($data['height'] ,$data['width']);
                $total_area = $area * $data['quantity']; // محاسبه total_area

                // گرد کردن مقادیر
                $area = round($area, 3);
                $environment = round($environment, 3);
                $total_area = round($total_area, 3);

                $dimensions[] = [
                    'row' => $key + 1,
                    'height' => $data['height'],
                    'width' => $data['width'],
                    'quantity' => $data['quantity'],
                    'position' => $data['position'],
                    'area' => $area,
                    'total_area' => $total_area,
                    'environment' => $environment,
                    'over' => $over
                ];

                // جمع کردن مقدار quantity
                $totalQuantity += $data['quantity'];
                $totalArea += $total_area;
                $totalEnvironment +=$environment;
            }

// اضافه کردن totalQuantity به آرایه dimensions
            $result = [
                'dimensions' => $dimensions,
                'totalQuantity' => $totalQuantity,
                'totalArea' => $totalArea,
                'totalEnvironment'=>$totalEnvironment
            ];

            $item->dimensions($result);

            if (!isset($itemData['description'])) {
                dd('not ok description');
            }

            // افزودن آیتم به لیست آیتم‌ها
            $items[] = $item;
        }
        // اضافه کردن تمام آیتم‌ها به فاکتور
        $invoice->addItems($items);
        $invoice->save('public');

        return $invoice;
    }*/
    public  function information($user, $buyer, $item)
    {
        // دریافت اطلاعات فروشنده از ورودی کاربر
        $sellerData = $user;

        // دریافت اطلاعات خریدار از ورودی کاربر
        $buyerData = $buyer;

        // ایجاد اشیاء Party برای فروشنده و خریدار
        $client = new Party([
            'name'          => 'Roosevelt Lloyd',
            'phone'         => '(520) 318-9486',
            'custom_fields' => [
                'note'        => 'IDDQD',
                'business id' => '365#GG',
            ],
        ]);

        $customer = new Party([
            'name'          => 'name',
            'address'       => 'The Green Street 12',
            'code'          => '#22663214',
            'custom_fields' => [
                'order number' => '> 654321 <',
            ],
        ]);

        // دریافت اطلاعات آیتم‌ها از ورودی کاربر
        $itemsDatas = $item;

        // ایجاد شیء جدید از کتابخانه Laravel Invoices
        $invoice = Invoice::make('پیش فاکتور')
            ->series('f-s')
            ->status(__('invoices::invoice.paid'))
            ->sequencePadding(4)
            ->sequence(4990)
            ->seller($client)
            ->buyer($customer)
            ->serialNumberFormat('{SEQUENCE}/{SERIES}')
            ->date(now())
            ->dateFormat('m/d/Y')
            ->payUntilDays(14)
            ->currencySymbol('ریال')
            ->currencyCode('IRR')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator(',')
            ->currencyDecimalPoint(',')
            ->filename($customer->name . '_invoice88');

        $items = [];
        foreach ($itemsDatas as $itemData) {

            $item = InvoiceItem::make($itemData['title'])
                ->description($itemData['description'])
                ->pricePerUnit( $itemData['price_per_unit'])
                ->quantity($itemData['quantity'])
                ->technicalDetails($itemData['technical_details']);

            $dimensions = [];
            $totalQuantity = 0;
            $totalArea = 0;
            $totalEnvironment = 0;

            foreach ($itemData['dimensions'] as $key => $data) {
                $area = self::CalculateArea($data['height'], $data['width']);
                $environment = self::CalculateEnvironment($data['height'], $data['width'], $data['quantity']);
                $over = self::calculateAspectRatio($data['height'], $data['width']);
                $total_area = $area * $data['quantity'];

                $area = round($area, 3);
                $environment = round($environment, 3);
                $total_area = round($total_area, 3);

                $dimensions[] = [
                    'row' => $key + 1,
                    'height' => $data['height'],
                    'width' => $data['width'],
                    'quantity' => $data['quantity'],
                    'position' => $data['position'],
                    'area' => $area,
                    'total_area' => $total_area,
                    'environment' => $environment,
                    'over' => $over
                ];

                $totalQuantity += $data['quantity'];
                $totalArea += $total_area;
                $totalEnvironment += $environment;
            }

            $result = [
                'dimensions' => $dimensions,
                'totalQuantity' => $totalQuantity,
                'totalArea' => $totalArea,
                'totalEnvironment' => $totalEnvironment
            ];

            $item->dimensions($result);

            if (!isset($itemData['description'])) {
                dd('not ok description');
            }

            $items[] = $item;
        }

        $invoice->addItems($items);
        $invoice->save('public');

        return $invoice;
    }


    public  function mergeProductStructures($productStructures)
    {
        $description = '';

        foreach ($productStructures as $productStructure) {
            $tempDescription = '';

            // ابتدا داده‌های type, width و material را جمع‌آوری می‌کنیم
            if (isset($productStructure['type'])) {
                $tempDescription .= $productStructure['type'] . ' ';
            }
            if (isset($productStructure['width'])) {
                $tempDescription .= $productStructure['width'] . ' ';
            }
            if (isset($productStructure['material'])) {
                $tempDescription .= $productStructure['material'] . ' ';
            }

            // سپس داده‌های spacer و laminate را اضافه می‌کنیم
            foreach ($productStructure as $key => $value) {
                if (strpos($key, 'spacer') !== false) {
                    $tempDescription .= "+ p.v.b $value + ";
                } elseif (strpos($key, 'laminate') !== false) {
                    $tempDescription .= "+ طلق $value + ";
                }
            }

            // اضافه کردن توضیحات موقتی به توضیحات اصلی
            $description .= trim($tempDescription) . ' ';
        }


        // برگرداندن توضیحات ترکیب شده
        return trim($description);
    }

/*    public static  function mergeProductStructures($productStructures)
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
    }*/

    public static  function CalculateEnvironment($height, $width,$quantity)
    {
        $result = 2*($height+$width)*$quantity/1000;
        return $result;
    }
    public static  function CalculateArea($height, $width)
    {
        $result = ($height * $width / 1000000 < 0.5) ? (($height * $width / 1000000 == 0) ? 0 : 0.5) : ($height * $width / 1000000);
        return $result;
    }
    public static  function calculateAspectRatio($height, $width)
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
            6 => 90000,
            8 => 280000 ,
            10 => 490000,
            12 => 670000,
            15 => 1410000, // قیمت اضافی برای 15 میلیمتر
        ],
        'type' => [
            'دودی' => 650000,
            'برنز' => 400000,
            'رفلکس طلایی' => 350000,
            'رفلکس نقره ای' => 350000,
            'ساتینا' =>1300000,
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
            'چسب IG' => 5300000,
            'چسب SG' => 7200000,
        ],

        'laminate' => [
            0.38 => 8400000, // قیمت اضافی برای طلق 0.38
            0.76 => 10900000, // قیمت اضافی برای طلق 0.76
            1.52 => 16100000, // قیمت اضافی برای طلق 1.52
        ]
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
                if ($selectedOptions['type'] === 'ساتینا') {
                    // قیمت ثابت برای شیشه ساتینا
                    $finalPrice += $this->options['type']['ساتینا'];
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

        foreach ($selectedOptionsList as $selectedOptions) {
            if (isset($selectedOptions['material']) && $selectedOptions['material'] == 'سکوریت') {
                $isTempered = true;
                $temperedCount++;
            }
            if (isset($selectedOptions['width'])) {
                $totalThickness += $selectedOptions['width'];
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
                if (array_key_exists($type, $this->options['type'])) {
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

        return $finalPrice;
    }








}
