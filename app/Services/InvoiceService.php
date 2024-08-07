<?php

namespace App\Services;


use App\Enums\CustomerStatus;
use App\Enums\InvoiceStatus;
use App\Enums\AccessPayment;
use App\Exceptions\DimensionException;
use App\Exceptions\InvalidDiscountException;
use App\Exceptions\WeightExceededException;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\ProductSection;
use App\Models\TypeItem;
use App\Models\DimensionItem;
use App\Models\TechnicalItem;
use App\Models\AggregatedItem;
use App\Models\DescriptionDimension;
use App\Models\Access;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class InvoiceService
{


    public static function processItems($invoice, $items, $discount, $delivery)
    {
        $DescriptionService = new DescriptionService;
        $invoiceService = new InvoiceService();
        $globalKeyIndex = 1;
        $totalPayableAmount = 0;

        // دریافت آیتم‌های موجود برای حذف آیتم‌های غیر موجود در درخواست جدید
        $existingItems = TypeItem::where('invoice_id', $invoice->id)->pluck('id')->toArray();
        $newItemsIds = [];

        foreach ($items as $itemIndex => $item) {
            $description_json = json_encode($item['description']);
            $convertDescriptions = $DescriptionService->convertDescriptions($item['description']);
            $item = $invoiceService->calculateItemPrice($item);
            $weight = $invoiceService->calculateWeight($convertDescriptions);

            foreach ($item['description'] as &$desc) {
                // اگر المان شامل 'adhesive' بود و مقدار آن برابر با 1 بود (معادل 'پلی سولفاید')
                if (isset($desc['adhesive']) && $desc['adhesive'] == 1) {

                    foreach ($item['dimensions'] as $dimensionIndex => &$dimension) {
                        $area = round($invoiceService->CalculateArea($dimension['height'], $dimension['width']), 3);
                        $totalWeight = $weight * $area;
                        if ($totalWeight >= 250) {
                            $desc['adhesive'] = 'سیلیکون IG';
                            if (!isset($dimension['description_ids'])) {
                                $dimension['description_ids'] = [];
                            }
                            if (!in_array(1, $dimension['description_ids'])) {
                                $dimension['description_ids'][] = 1;
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
            ], [
                'product_id' => $item['product'],
                'product_section_id' => $item['product_section'] ?? null,
                'description_json' => $description_json,
                'description' => $invoiceService->mergeProductStructures($convertDescriptions),
                'price' => $item['price_per_unit'] ?? 0,
                'image_path' => $productImagePath,
                'description_structure' =>  $item['description_structure'] ?? null
            ]);

            // افزودن آیتم جدید به آرایه newItemsIds
            $newItemsIds[] = $typeItem->id;

            $totalPayableAmount += $invoiceService->processDimensions($invoice->id, $typeItem, $item, $weight, $globalKeyIndex, $discount, $delivery, $itemIndex);

            $invoiceService->updateOrCreateTechnicalItem($invoice->id, $typeItem, $item['technical_details']);
        }

        // حذف آیتم‌هایی که در آرایه newItemsIds نیستند
        $itemsToDelete = array_diff($existingItems, $newItemsIds);
        TypeItem::destroy($itemsToDelete);

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


/*    public function processDimensions($invoiceId, $typeItem, $item, $weight, &$globalKeyIndex, $discount, $delivery, $itemIndex)
    {
        $invoiceService = new InvoiceService();
        $dimensionGroups = collect();

        // دریافت آیتم‌های موجود
        $existingDimensions = DimensionItem::where('invoice_id', $invoiceId)
            ->where('type_id', $typeItem->id)
            ->pluck('id')
            ->toArray();

        $newDimensionsIds = [];

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

            $newDimensionsIds[] = $dimensionItem->id;
            $globalKeyIndex++;
        }

        // حذف آیتم‌هایی که در آرایه newDimensionsIds نیستند
        $dimensionsToDelete = array_diff($existingDimensions, $newDimensionsIds);
        DimensionItem::destroy($dimensionsToDelete);

        $totalPayableAmount = $this->createOrUpdateAggregatedItems($invoiceId, $typeItem, $dimensionGroups, $item, $weight, $globalKeyIndex, $discount, $delivery);

        return $totalPayableAmount;
    }*/

    public function processDimensions($invoiceId, $typeItem, $item, $weight, &$globalKeyIndex, $discount, $delivery, $itemIndex)
    {
        $invoiceService = new InvoiceService();
        $dimensionGroups = collect();

        // دریافت آیتم‌های موجود
        $existingDimensions = DimensionItem::where('invoice_id', $invoiceId)
            ->where('type_id', $typeItem->id)
            ->pluck('id')
            ->toArray();

        $newDimensionsIds = [];

        // تولید یک عدد تصادفی چهار رقمی ثابت برای تمامی ابعاد در این فاکتور (در صورتی که کاربر عددی وارد نکرده باشد)
        $fixedPosition = random_int(1000, 9999);

        foreach ($item['dimensions'] as $dimensionIndex => $dimension) {
            try {
                $area = round($invoiceService->CalculateArea($dimension['height'], $dimension['width']), 3);
                $over = $invoiceService->calculateAspectRatio($dimension['height'], $dimension['width'], $itemIndex + 1, $dimensionIndex + 1);
            } catch (DimensionException $e) {
                throw new DimensionException($itemIndex + 1, $dimensionIndex + 1, $e->getMessage());
            }

            $dimensionWeight = $weight * $area;

            // بررسی وزن هر بعد
            if ($dimensionWeight > 900) {
                throw new WeightExceededException($itemIndex + 1, $dimensionIndex + 1, $dimensionWeight);
            }

            // چک کردن وجود position
            if (!isset($dimension['position'])) {
                // اگر position برای این بعد قبلاً ذخیره شده بود، از همان استفاده می‌کنیم
                $existingDimension = DimensionItem::where('invoice_id', $invoiceId)
                    ->where('type_id', $typeItem->id)
                    ->where('key', $globalKeyIndex)
                    ->first();

                if ($existingDimension) {
                    $position = $existingDimension->position;
                } else {
                    $position = $fixedPosition;
                }
            } else {
                $position = $dimension['position'];
            }

            $dimensionItem = DimensionItem::updateOrCreate(
                ['invoice_id' => $invoiceId, 'type_id' => $typeItem->id, 'key' => $globalKeyIndex],
                [
                    'height' => $dimension['height'],
                    'width' => $dimension['width'],
                    'weight' => $dimensionWeight,
                    'quantity' => $dimension['quantity'],
                    'over' => $over,
                    'position' => $position,
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

            $newDimensionsIds[] = $dimensionItem->id;
            $globalKeyIndex++;
        }

        // حذف آیتم‌هایی که در آرایه newDimensionsIds نیستند
        $dimensionsToDelete = array_diff($existingDimensions, $newDimensionsIds);
        DimensionItem::destroy($dimensionsToDelete);

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
        $totalPayableAmount = 0;
        $descriptionNames = [];
        $keyCounter = 1;

        // دریافت آیتم‌های تجمیعی موجود
        $existingAggregatedItems = AggregatedItem::where('invoice_id', $invoiceId)
            ->where('type_id', $typeItem->id)
            ->pluck('id')
            ->toArray();

        $newAggregatedItemsIds = [];

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


            list($descriptionIdsKey, $overPercentage) = explode('-over-', $groupKey);
            $descriptionIds = explode('-', $descriptionIdsKey);
            $overPercentage = floatval($overPercentage);

            $valueAddedTax = $basePrice;
            $specialDescriptionIds = [4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33];

            foreach ($descriptionIds as $id) {
                $description = DescriptionDimension::find($id);
                if ($description) {
                    $descriptionNames[$id] = $description->name;
                    if ($item['product'] == 2 && in_array($id, $specialDescriptionIds)) {
                        if ($description->price) {
                            $valueAddedTax += ($description->price * 2);
                        } elseif ($description->percent) {
                            $valueAddedTax += ($basePrice * $description->percent) / 100;
                        }
                    } else {
                        if ($description->price) {
                            $valueAddedTax += $description->price;
                        } elseif ($description->percent) {
                            $valueAddedTax += ($valueAddedTax * $description->percent) / 100;
                        }
                    }
                }
            }

            $valueAddedTax += ($valueAddedTax * $overPercentage) / 100;
            $priceUnit = intval(($valueAddedTax / 110) * 100);

            $user = auth()->user();
            $userMaxDiscount = $user->userDiscount->max_discount ?? 20;

            if ($discount > $userMaxDiscount) {
                throw new InvalidDiscountException();
            }

            $priceDiscounted = ($priceUnit / 100) * (100 - $discount);
            if ($delivery === 1) {
                $priceDiscounted += intval($weight * 37500);
            }

            $priceValueAddedFinal = ($priceDiscounted * 110) / 100;

            $totalPrice = intval($totalMeterage * $priceValueAddedFinal);

            $totalPayableAmount += $totalPrice;

            $names = collect($descriptionIds)->map(function ($id) use ($descriptionNames) {
                return $descriptionNames[$id] ?? ' ';
            })->implode(', ');

            if (!empty($overPercentage) && $overPercentage != 0) {
                $names .= '   درصد اور ' . $overPercentage;
            }

            $aggregatedItem = AggregatedItem::updateOrCreate([
                'invoice_id' => $invoiceId,
                'type_id' => $typeItem->id,
                'key' => $keyCounter++
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

            $newAggregatedItemsIds[] = $aggregatedItem->id;

            $globalKeyIndex++;
        }

        // حذف آیتم‌های تجمیعی که در آرایه newAggregatedItemsIds نیستند
        $aggregatedItemsToDelete = array_diff($existingAggregatedItems, $newAggregatedItemsIds);
        AggregatedItem::destroy($aggregatedItemsToDelete);

        return $totalPayableAmount;
    }


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
                return 'f-s4787051';
            }
        } catch (\Exception $e) {
            // لاگ کردن خطا
            Log::error('خطا در پردازش سریال: ' . $e->getMessage());
            // برگرداندن سریال پیش‌فرض در صورت بروز خطا
            return 'f-s4787051';
        }
    }






   //validateInvoice
    public function validateInvoiceUpdate(Invoice $invoice, array $validatedData)
    {
        // فقط اگر وضعیت تغییر کرده باشد بررسی وضعیت را انجام دهید
        if (isset($validatedData['status'])) {
            // بررسی تغییر وضعیت از informal به formal
            if ($invoice->status === InvoiceStatus::InFormal && $validatedData['status'] === InvoiceStatus::Formal) {
                // بررسی وضعیت مشتری
                $customer = $invoice->customer;
                if ($customer->status === CustomerStatus::INCOMPLETE || $customer->status === CustomerStatus::INACTIVE) {
                    throw new HttpResponseException(response()->json([
                        'message' => 'وضعیت مشتری اجازه تغییر وضعیت فاکتور به رسمی را نمی‌دهد.'
                    ], 422));
                }

                // بررسی مقداردهی فیلدها
                if (!isset($validatedData['pre_payment']) || !isset($validatedData['before_delivery'])) {
                    throw new HttpResponseException(response()->json([
                        'message' => 'برای تغییر وضعیت به رسمی، فیلدهای پیش پرداخت و قبل از تحویل باید مقداردهی شوند.'
                    ], 422));
                }

                // بررسی وضعیت payment_terms کاربر
                $userDiscount = Access::where('user_id', $invoice->user_id)->first();
                $userPaymentTerms = $userDiscount->payment_terms ?? AccessPayment::CASH;
                if ($userPaymentTerms === AccessPayment::CASH) {
                    // اگر کاربر فقط نقدی باشد، اجازه تعیین چک ندهد
                    if (isset($validatedData['cheque'])) {
                        throw new HttpResponseException(response()->json([
                            'message' => 'کاربر فقط به صورت نقدی پرداخت می‌کند و نمی‌تواند چک تعیین کند.'
                        ], 422));
                    }

                    // جمع درصد pre_payment و before_delivery باید 100 باشد
                    if (($validatedData['pre_payment'] + $validatedData['before_delivery']) !== 100) {
                        throw new HttpResponseException(response()->json([
                            'message' => 'برای پرداخت نقدی، جمع درصدهای پیش پرداخت و قبل از تحویل باید 100 باشد.'
                        ], 422));
                    }
                } else {
                    // اگر کاربر پرداخت به صورت چک دارد
                    if (($validatedData['pre_payment'] + $validatedData['before_delivery']) !== 100) {
                        // اگر جمع درصدهای pre_payment و before_delivery برابر 100 نباشد، باید فیلد cheque پر شود
                        if (!isset($validatedData['cheque'])) {
                            throw new HttpResponseException(response()->json([
                                'message' => 'برای تغییر وضعیت به رسمی و استفاده از چک، فیلد چک باید مقداردهی شود یا مجموع پیش پرداخت و قبل از تحویل 100 باشد.'
                            ], 422));
                        }
                    }
                }

                $userMinPayment = $userDiscount->min_pre_payment ?? 30;

                if ($validatedData['pre_payment'] < $userMinPayment) {
                    throw new HttpResponseException(response()->json([
                        'message' => 'پیش پرداخت باید حداقل ' . $userMinPayment . ' درصد باشد.'
                    ], 422));
                }
            }

            elseif ($invoice->status === InvoiceStatus::InFormal && $validatedData['status'] === InvoiceStatus::PreBuy) {
                // بررسی وضعیت مشتری
                $customer = $invoice->customer;
                if ($customer->status === CustomerStatus::INCOMPLETE || $customer->status === CustomerStatus::INACTIVE) {
                    throw new HttpResponseException(response()->json([
                        'message' => 'وضعیت مشتری اجازه تغییر وضعیت فاکتور به رسمی را نمی‌دهد.'
                    ], 422));
                }

            }

            elseif ($invoice->status === InvoiceStatus::PreBuy && $validatedData['status'] === InvoiceStatus::InFormal) {
                // بررسی وضعیت مشتری
                $customer = $invoice->customer;
                if ($customer->status === CustomerStatus::INCOMPLETE || $customer->status === CustomerStatus::INACTIVE) {
                    throw new HttpResponseException(response()->json([
                        'message' => 'وضعیت مشتری اجازه تغییر وضعیت فاکتور به رسمی را نمی‌دهد.'
                    ], 422));
                }
            }
        }
    }




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

    public function calculateAspectRatio($height, $width, $productIndex, $dimensionIndex)
    {

        $height = (float)$height;
        $width = (float)$width;
        $area = $this->CalculateArea($height, $width);

        if ($height == 0 || $width == 0) {
            return "";
        }

        if (min($height, $width) > 3210 || max($height, $width) > 6000) {
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

        if (min($height, $width) > 2440 && min($height, $width) > 2500) {
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
