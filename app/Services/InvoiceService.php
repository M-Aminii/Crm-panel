<?php

namespace App\Services;


use App\Exceptions\DimensionException;
use App\Exceptions\InvalidDiscountException;
use App\Exceptions\NotFoundPageException;
use App\Helpers\NumberToWordsHelper;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\ProductSection;
use App\Models\TypeItem;
use App\Models\DimensionItem;
use App\Models\TechnicalItem;
use App\Models\AggregatedItem;
use App\Models\DescriptionDimension;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceService
{

   /* public static function processItems($invoice, $items)
    {
        $invoiceService = new InvoiceService();
        $dimensionGroups = collect();
        $globalKeyIndex = 1; // شمارنده سراسری برای کلیدها
        $totalPayableAmount = 0; // متغیر برای نگهداری مجموع مبالغ

        foreach ($items as $itemIndex => $item) {
            $item = $invoiceService->calculateItemPrice($item);
            $weight = $invoiceService->calculateWeight($item['description']);

            // افزودن شرط بررسی adhesive و تغییر آن در صورت لزوم
            foreach ($item['description'] as &$desc) {
                if (isset($desc['adhesive']) && $desc['adhesive'] == 'پلی سولفاید') {
                    foreach ($item['dimensions'] as $dimensionIndex => &$dimension) {
                        $area = round($invoiceService->CalculateArea($dimension['height'], $dimension['width']), 3);
                        $totalWeight = $weight * $area;

                        if ($totalWeight >= 250) {
                            $desc['adhesive'] = 'سیلیکون IG';
                            if (!isset($dimension['description_ids'])) {
                                $dimension['description_ids'] = [];
                            }
                            if (!in_array(1, $dimension['description_ids'])) {
                                $dimension['description_ids'][] = 1; // افزودن ID توضیحات
                            }
                        }
                    }
                }
            }

            $typeItem = TypeItem::updateOrCreate([
                'invoice_id' => $invoice->id,
                'key' => $itemIndex + 1 , // اضافه کردن مقدار key
            ],
            [
                'product_id' => $item['product'],
                'description' => $invoiceService->mergeProductStructures($item['description']),
                'price' => $item['price_per_unit'] ?? 0,

            ]);

            // پردازش ابعاد و دریافت مجموع مبالغ و شمارنده کلید بعدی
            $totalPayableAmount += $invoiceService->processDimensions($invoice->id, $typeItem, $item, $weight, $globalKeyIndex);
            $invoiceService->updateOrCreateTechnicalItem($invoice->id, $typeItem, $item['technical_details']);
        }

        return $totalPayableAmount; // بازگرداندن مجموع مبالغ
    }*/
    public static function processItems($invoice, $items, $discount, $delivery)
    {
        $DescriptionService = new DescriptionService;
        $invoiceService = new InvoiceService();
        $globalKeyIndex = 1; // شمارنده سراسری برای کلیدها
        $totalPayableAmount = 0; // متغیر برای نگهداری مجموع مبالغ

        foreach ($items as $itemIndex => $item) {
            $description_json = json_encode($item['description']);
            $convertDescriptions = $DescriptionService->convertDescriptions($item['description']);
            $item = $invoiceService->calculateItemPrice($item);
            $weight = $invoiceService->calculateWeight($convertDescriptions);

            foreach ($item['description'] as &$desc) {
                if (isset($desc['adhesive']) && $desc['adhesive'] == 'پلی سولفاید') {
                    foreach ($item['dimensions'] as $dimensionIndex => &$dimension) {
                        $area = round($invoiceService->CalculateArea($dimension['height'], $dimension['width']), 3);
                        $totalWeight = $weight * $area;

                        if ($totalWeight >= 250) {
                            $desc['adhesive'] = 'سیلیکون IG';
                            if (!isset($dimension['description_ids'])) {
                                $dimension['description_ids'] = [];
                            }
                            if (!in_array(1, $dimension['description_ids'])) {
                                $dimension['description_ids'][] = 1; // افزودن ID توضیحات
                            }
                        }
                    }
                }
            }

            $product = Product::find($item['product']);
            $productSection = isset($item['product_section']) ? ProductSection::find($item['product_section']) : null;

            $productImagePath = $productSection ? $productSection->image_path : ($product ? $product->image_path : null);

            $typeItem = TypeItem::updateOrCreate([
                'invoice_id' => $invoice->id,
                'key' => $itemIndex + 1,
            ],
                [
                    'product_id' => $item['product'],
                    'product_section_id' => $item['product_section'] ?? null,
                    'description_json' => $description_json,
                    'description' => $invoiceService->mergeProductStructures($convertDescriptions),
                    'price' => $item['price_per_unit'] ?? 0,
                    'image_path' => $productImagePath,
                ]);

            $totalPayableAmount += $invoiceService->processDimensions($invoice->id, $typeItem, $item, $weight, $globalKeyIndex, $discount, $delivery, $itemIndex);


            $invoiceService->updateOrCreateTechnicalItem($invoice->id, $typeItem, $item['technical_details']);
        }

        return $totalPayableAmount;
    }


    public function calculateItemPrice($item)
    {
        $DescriptionService = new DescriptionService;

        $CalculationService = new CalculationService();

        $productFunctions = [
            1 => 'calculatePriceScorit',
            2 => 'calculatePriceLaminate',
            3 => 'calculatePriceDouble',
            4 => 'calculatePriceDouble',
            5 => 'calculatePriceDoubleLaminate'
        ];

        if (isset($productFunctions[$item['product']]) && is_array($item['description'])) {
            $convertDescriptions = $DescriptionService->convertDescriptions($item['description']);
            $functionName = $productFunctions[$item['product']];
            $calculatedPrice = $CalculationService->$functionName($convertDescriptions);

            if ($calculatedPrice !== null) {
                $item['price_per_unit'] = intval($calculatedPrice);
            } else {
                throw new \Exception('مشکل در محاسبه قیمت محصول');
            }
        }

        return $item;
    }


    public function processDimensions($invoiceId, $typeItem, $item, $weight, &$globalKeyIndex, $discount, $delivery, $itemIndex)
    {
        $invoiceService = new InvoiceService();
        $dimensionGroups = collect();

        foreach ($item['dimensions'] as $dimensionIndex => $dimension) {
            try {
                $area = round($invoiceService->CalculateArea($dimension['height'], $dimension['width']), 3);
                $over = $invoiceService->calculateAspectRatio($dimension['height'], $dimension['width'], $itemIndex + 1, $dimensionIndex + 1);
            } catch (DimensionException $e) {
                throw new DimensionException($itemIndex + 1, $dimensionIndex + 1, $e->getMessage());
            }

            $dimensionItem = DimensionItem::updateOrCreate(
                ['invoice_id' => $invoiceId, 'type_id' => $typeItem->id, 'key' => $globalKeyIndex],
                [
                    'height' => $dimension['height'],
                    'width' => $dimension['width'],
                    'weight' => $weight * $area,
                    'quantity' => $dimension['quantity'],
                    'over' => $over,
                    'position' => $dimension['position'] === null ? random_int(1000, 9999) : $dimension['position'],
                ]
            );

            if (isset($dimension['description_ids']) && is_array($dimension['description_ids'])) {
                $dimensionItem->descriptionDimensions()->sync($dimension['description_ids']);
            }

            $descriptionKey = collect($dimension['description_ids'])->sort()->implode('-');
            if (!$dimensionGroups->has($descriptionKey)) {
                $dimensionGroups->put($descriptionKey, collect());
            }
            $dimensionGroups->get($descriptionKey)->push($dimensionItem);

            $globalKeyIndex++;
        }

        $totalPayableAmount = $this->createOrUpdateAggregatedItems($invoiceId, $typeItem, $dimensionGroups, $item, $weight, $globalKeyIndex, $discount, $delivery);

        return $totalPayableAmount;
    }


    public function updateOrCreateTechnicalItem($invoiceId, $typeItem, $technicalDetails)
    {
        TechnicalItem::updateOrCreate(
            ['invoice_id' => $invoiceId, 'type_id' => $typeItem->id],
            [
                'edge_type' => $technicalDetails['edge_type'],
                'glue_type' => $technicalDetails['glue_type'],
                'post_type' => $technicalDetails['post_type'],
                'delivery_date' => $technicalDetails['delivery_date'],
                'frame' => $technicalDetails['frame'],
                'balance' => $technicalDetails['balance'],
                'vault_type' => $technicalDetails['vault_type'],
                'map_dimension' => $technicalDetails['map_dimension'],
                'map_view' => $technicalDetails['map_view'],
                'usage' => $technicalDetails['usage'],
            ]
        );
    }

    public function createOrUpdateAggregatedItems($invoiceId, $typeItem, $dimensionGroups, $item, $weight, &$globalKeyIndex, $discount, $delivery)
    {
        $invoiceService = new InvoiceService();
        $totalPayableAmount = 0; // متغیر برای نگهداری مجموع مبالغ
        $descriptionNames = [];

        // ابتدا گروه‌بندی بر اساس descriptionIds و over
        $groupedDimensions = collect();

        foreach ($dimensionGroups as $descriptionKey => $dimensions) {
            foreach ($dimensions as $dimension) {
                $descriptionIds = explode('-', $descriptionKey);
                $overPercentage = round($dimension->over, 2);
                $groupKey = implode('-', $descriptionIds) . '-over-' . $overPercentage;

                if (!$groupedDimensions->has($groupKey)) {
                    $groupedDimensions->put($groupKey, collect());
                }
                $groupedDimensions->get($groupKey)->push($dimension);
            }
        }

        foreach ($groupedDimensions as $groupKey => $dimensions) {
            $totalArea = $dimensions->sum(function ($dimension) use ($invoiceService) {
                return round($invoiceService->CalculateArea($dimension->height, $dimension->width), 3);
            });

            $totalQuantity = $dimensions->sum('quantity');
            $totalMeterage = $dimensions->sum(function ($dimension) use ($invoiceService) {
                $area = round($invoiceService->CalculateArea($dimension->height, $dimension->width), 3);
                return $area * $dimension->quantity;
            });

            $basePrice = $item['price_per_unit'];

            // استخراج descriptionIds و overPercentage از groupKey
            list($descriptionIdsKey, $overPercentage) = explode('-over-', $groupKey);
            $descriptionIds = explode('-', $descriptionIdsKey);
            $overPercentage = floatval($overPercentage);

            $valueAddedTax = $basePrice;

            foreach ($descriptionIds as $id) {
                $description = DescriptionDimension::find($id);
                if ($description) {
                    $descriptionNames[$id] = $description->name;
                    if ($description->price) {
                        $valueAddedTax += $description->price;
                    } elseif ($description->percent) {
                        $valueAddedTax += ($basePrice * $description->percent) / 100;
                    }
                }
            }

            $valueAddedTax += ($valueAddedTax * $overPercentage) / 100;
            $priceUnit = intval(($valueAddedTax / 110) * 100);

            $user = auth()->user();
            $userMaxDiscount = $user->userDiscount->max_discount ?? 10;

            if ($discount > $userMaxDiscount) {
                throw new InvalidDiscountException();
            }

            $priceDiscounted = ($priceUnit / 100) * (100 - $discount);

            if ($delivery === 1) {
                $priceDiscounted += intval($weight * 37500); // افزودن کرایه بار فقط در صورت انتخاب گزینه حمل و نقل
            }

            $priceValueAddedFinal = ($priceDiscounted * 110) / 100;

            $totalPrice = intval($totalMeterage * $priceValueAddedFinal);

            // اضافه کردن مبلغ کل آیتم به مجموع قابل پرداخت
            $totalPayableAmount += $totalPrice;

            $names = collect($descriptionIds)->map(function ($id) use ($descriptionNames) {
                return $descriptionNames[$id] ?? ' ';
            })->implode(', ');

            if (!empty($overPercentage) && $overPercentage != 0) {
                $names .= '   درصد اور ' . $overPercentage;
            }

            // استفاده از updateOrCreate با استفاده از globalKeyIndex
            AggregatedItem::updateOrCreate([
                'invoice_id' => $invoiceId,
                'key' => $globalKeyIndex // استفاده از شمارنده سراسری به عنوان key
            ], [
                'description_product' => $typeItem->description,
                'description' => $names,
                'total_area' => $totalMeterage,
                'total_quantity' => $totalQuantity,
                'total_weight' => $totalMeterage * $weight,
                'price_unit' => $priceUnit,
                'price_discounted' => $priceDiscounted,
                'value_added_tax' => $priceValueAddedFinal,
                'total_price' => $totalPrice,
            ]);

            $globalKeyIndex++; // افزایش شمارنده سراسری برای استفاده در کلید بعدی
        }

        // بازگرداندن مجموع مبالغ
        return $totalPayableAmount;
    }

/*    public function createOrUpdateAggregatedItems($invoiceId, $typeItem, $dimensionGroups, $item, $weight, &$globalKeyIndex, $discount, $delivery)
        {
            $invoiceService = new InvoiceService();
            $totalPayableAmount = 0; // متغیر برای نگهداری مجموع مبالغ
            $descriptionNames = [];

            foreach ($dimensionGroups as $descriptionKey => $dimensions) {

                $totalArea = $dimensions->sum(function ($dimension) use ($invoiceService) {
                    return round($invoiceService->CalculateArea($dimension->height, $dimension->width), 3);
                });

                $totalQuantity = $dimensions->sum('quantity');
                $totalMeterage = $dimensions->sum(function ($dimension) use ($invoiceService) {
                    $area = round($invoiceService->CalculateArea($dimension->height, $dimension->width), 3);
                    return $area * $dimension->quantity;
                });

                $basePrice = $item['price_per_unit'];

                $descriptionIds = explode('-', $descriptionKey);

                $valueAddedTax = $basePrice;
                $overPercentage = 0;

                foreach ($descriptionIds as $id) {
                    $description = DescriptionDimension::find($id);
                    if ($description) {
                        $descriptionNames[$id] = $description->name;
                        if ($description->price) {
                            $valueAddedTax += $description->price;
                        } elseif ($description->percent) {
                            $valueAddedTax += ($basePrice * $description->percent) / 100;
                        }
                    }
                }

                foreach ($dimensions as $dimension) {
                    $overPercentage += $dimension->over;
                }

                //$overPercentage /= $dimensions->count();

                $valueAddedTax += ($valueAddedTax * $overPercentage) / 100;
                $priceUnit = intval(($valueAddedTax / 110) * 100);

                $user = auth()->user();
                $userMaxDiscount = $user->userDiscount->max_discount ?? 10;

                if ($discount > $userMaxDiscount) {
                   throw new InvalidDiscountException();
                }

                $priceDiscounted = ($priceUnit / 100) * (100 - $discount);

                if ($delivery === 1) {
                    $priceDiscounted += intval($weight * 37500); // افزودن کرایه بار فقط در صورت انتخاب گزینه حمل و نقل
                }

                $priceValueAddedFinal = ($priceDiscounted * 110) / 100;

                $totalPrice = intval($totalMeterage * $priceValueAddedFinal);

                // اضافه کردن مبلغ کل آیتم به مجموع قابل پرداخت
                $totalPayableAmount += $totalPrice;

                $names = collect($descriptionIds)->map(function ($id) use ($descriptionNames) {
                    return $descriptionNames[$id] ?? ' ';
                })->implode(', ');

                if (!empty($overPercentage) && $overPercentage != 0) {
                    $names .= '   درصد اور ' . $overPercentage;
                }

                // استفاده از updateOrCreate با استفاده از globalKeyIndex
                AggregatedItem::updateOrCreate([
                    'invoice_id' => $invoiceId,
                    'key' => $globalKeyIndex // استفاده از شمارنده سراسری به عنوان key
                ], [
                    'description_product' => $typeItem->description,
                    'description' => $names,
                    'total_area' => $totalMeterage,
                    'total_quantity' => $totalQuantity,
                    'total_weight' => $totalMeterage * $weight,
                    'price_unit' => $priceUnit,
                    'price_discounted' => $priceDiscounted,
                    'value_added_tax' => $priceValueAddedFinal,
                    'total_price' => $totalPrice,
                ]);

                $globalKeyIndex++; // افزایش شمارنده سراسری برای استفاده در کلید بعدی
            }

            // بازگرداندن مجموع مبالغ
            return $totalPayableAmount;
        }*/

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






   //validateInvoice
    public function validateInvoiceUpdate($invoice, $validatedData)
    {
        // بررسی تغییر وضعیت از informal به formal
        if ($invoice->status === 'informal' && $validatedData['status'] === 'formal') {
            // بررسی مقداردهی فیلدها
            if (!isset($validatedData['pre_payment']) || !isset($validatedData['before_delivery']) || !isset($validatedData['cheque'])) {
                throw new HttpResponseException(response()->json([
                    'message' => 'برای تغییر وضعیت به رسمی ، فیلدهای پیش پرداخت ، قبل از تحویل و چک باید مقداردهی شوند.'
                ], 422));
            }

            // بررسی وضعیت مشتری
            $customer = $invoice->customer;
            if ($customer->status === 'Incomplete' || $customer->status === 'Inactive') {
                throw new HttpResponseException(response()->json([
                    'message' => 'وضعیت مشتری اجازه تغییر وضعیت فاکتور به رسمی را نمی‌دهد.'
                ], 422));
            }
        }
    }
















    /*    public function processDimensions($invoiceId, $typeItem, $item, $weight, &$globalKeyIndex, $discount, $delivery)
        {
            $invoiceService = new InvoiceService();
            $dimensionGroups = collect();

            foreach ($item['dimensions'] as $dimensionIndex => $dimension) {
                $area = round($invoiceService->CalculateArea($dimension['height'], $dimension['width']), 3);
                $over =  $invoiceService->calculateAspectRatio($dimension['height'], $dimension['width']);

                $dimensionItem = DimensionItem::updateOrCreate(
                    ['invoice_id' => $invoiceId, 'type_id' => $typeItem->id, 'key' => $globalKeyIndex],
                    [
                        'height' => $dimension['height'],
                        'width' => $dimension['width'],
                        'weight' => $weight * $area,
                        'quantity' => $dimension['quantity'],
                        'over' => $over,
                        'position' =>  $dimension['position'] === null ? random_int(1000, 9999) : $dimension['position'],
                    ]
                );

                if (isset($dimension['description_ids']) && is_array($dimension['description_ids'])) {
                    $dimensionItem->descriptionDimensions()->sync($dimension['description_ids']);
                }

                $descriptionKey = collect($dimension['description_ids'])->sort()->implode('-');
                if (!$dimensionGroups->has($descriptionKey)) {
                    $dimensionGroups->put($descriptionKey, collect());
                }
                $dimensionGroups->get($descriptionKey)->push($dimensionItem);

                $globalKeyIndex++; // افزایش شمارنده کلید برای هر بعد جدید
            }

            $totalPayableAmount = $this->createOrUpdateAggregatedItems($invoiceId, $typeItem, $dimensionGroups, $item, $weight, $globalKeyIndex, $discount, $delivery);

            return $totalPayableAmount;
        }*/














    public function calculateWeight($description)
    {
        $totalWidth = 0;
        $additionalWeight = 0;

        foreach ($description as $desc) {
            if (isset($desc['width'])) {
                $totalWidth += $desc['width'];
            }


            if (isset($desc['laminate'])) {
                switch ($desc['laminate']) {
                    case "1.52":
                        $additionalWeight += 2.5;
                        break;
                    case "0.76":
                        $additionalWeight += 1.25;
                        break;
                    case "0.38":
                        $additionalWeight += 0.625;
                        break;
                }
            }

            if (isset($desc['spacer'])) {
                $additionalWeight += ($desc['spacer'] / 10) * 2.5;
            }
        }
        $weight = ($totalWidth * 2.5) + $additionalWeight;
        return $weight;
    }

  /*  public  function information($user, $buyer, $item)
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
    }*/

    public function mergeProductStructures($productStructures)
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

            // سپس داده‌های laminate را اضافه می‌کنیم
            if (isset($productStructure['laminate'])) {
                $tempDescription .= "+ طلق " . $productStructure['laminate'] . " + ";
            }
            if (isset($productStructure['spacer'])) {
                $tempDescription .= "+ اسپیسر " . $productStructure['spacer'] . " + ";
            }

            // اضافه کردن توضیحات موقتی به توضیحات اصلی
            $description .= trim($tempDescription) . ' ';
        }

        // حذف فاصله‌های اضافی و برگرداندن توضیحات ترکیب شده
        return trim($description);
    }


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
   /* public function calculateAspectRatio($height, $width)
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
    }*/
    public function calculateAspectRatio($height, $width, $productIndex, $dimensionIndex)
    {

        $height = (float)$height;
        $width = (float)$width;
        $area = $this->CalculateArea($height, $width);

        if ($height == 0 || $width == 0) {
            return "";
        }

        if (min($height, $width) > 3210) {
            throw new \App\Exceptions\DimensionException($productIndex, $dimensionIndex);
        }

        if (max($height, $width) > 4880 && min($height, $width) < 3211 && min($height, $width) > 2500) {
            return 140;
        }

        if (max($height, $width) > 4880 && min($height, $width) <= 2500) {
            return 140;
        }

        if (max($height, $width) > 4500 && min($height, $width) < 3211 && min($height, $width) >= 2500) {
            return 75;
        }

        if (max($height, $width) > 4500 && min($height, $width) <= 2500) {
            return 75;
        }

        if (min($height, $width) > 2440 && min($height, $width) >= 2500) {
            return 35;
        }

        if (max($height, $width) > 3660 || (min($height, $width) > 2440 && min($height, $width) <= 2500)) {
            return 20;
        }

        if (max($height, $width) <= 3660 && min($height, $width) <= 2440 && $area > 6 && $area <= 8.9304) {
            return 15;
        }

        if (max($height, $width) <= 3660 && min($height, $width) <= 2440 && $area <= 6) {
            return 0;
        }

        return "";
    }






}
