<?php

namespace App\Http\Controllers;

use App\Helpers\Benchmark;
use App\Http\Requests\Customer\ShowCustomerRequest;
use App\Http\Requests\Invoice\CreateInvoiceRequest;
use App\Http\Requests\Invoice\ListInvoiceRequest;
use App\Http\Requests\Invoice\ShowInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Customer;
use App\Models\DimensionItem;
use App\Models\Product;
use App\Models\TechnicalItem;
use App\Models\TypeItem;
use App\Models\User;
use App\Services\InvoiceService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;
use PhpParser\Node\Expr\New_;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ListInvoiceRequest $request)
    {
        // بررسی کنید که آیا کاربر مدیر است
        $user = auth()->user();

        if ($user->hasAnyAdminRole()) {
            // اگر کاربر مدیر است، همه فاکتورها را دریافت کنید
            $invoices = \App\Models\Invoice::all();
        } else {
            // اگر کاربر عادی است، فقط فاکتورهای مربوط به خودش را دریافت کنید
            $invoices = \App\Models\Invoice::where('user_id', $user->id)->get();
        }

        // تبدیل داده‌ها به JSON و بازگرداندن آن‌ها
        return response()->json($invoices, 200);


    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateInvoiceRequest $request)
    {
         // اعتبارسنجی داده‌های ورودی
        $validatedData = $request->all();
        try {
        DB::transaction(function () use ($validatedData) {
            $customer = Customer::find($validatedData['buyer']);

            if ($customer->status === "Incomplete" || $customer->status === "Inactive") {
                return response()->json(['message' => 'امکان صدور فاکتور برای این مشتری وجود ندارد'], 404);
            }
            $lastInvoiceSerial = InvoiceService::generateNewSerial();
            // ایجاد فاکتور جدید
            $invoice = \App\Models\Invoice::create([
                'serial_number' => $lastInvoiceSerial, // مقدار نمونه، شما می‌توانید این را تغییر دهید
                'user_id' => auth()->id(),
                'customer_id' => $validatedData['buyer'],
                'position' => "4554545545", // مقدار نمونه، شما می‌توانید این را تغییر دهید
                'status' => $validatedData['status'], // مقدار نمونه، شما می‌توانید این را تغییر دهید
            ]);

            // تعریف توابع محاسبه قیمت بر اساس محصول
            $productFunctions = [
                1 => 'calculatePriceScorit',
                2 => 'calculatePriceLaminate',
                3 => 'calculatePriceDouble'
            ];

            // ایجاد یک نمونه از سرویس فاکتور
            $invoiceService = new InvoiceService();

            foreach ($validatedData['items'] as $itemIndex => $item) {
                $productId = $item['product'];

                // محاسبه قیمت بر اساس محصول
                if (array_key_exists($productId, $productFunctions)) {
                    $functionName = $productFunctions[$productId];
                    if (is_array($item['description'])) {
                        $calculatedPrice = $invoiceService->$functionName($item['description']);
                        if ($calculatedPrice !== null) {
                            $item['price_per_unit'] = $calculatedPrice;
                        } else {
                            throw new \Exception('مشکل در محاسبه قیمت محصول');
                        }
                    }
                }

                // ترکیب توضیحات محصول
                if (is_array($item['description'])) {
                    $item['description'] = $invoiceService->mergeProductStructures($item['description']);
                }

                // ایجاد آیتم نوعی جدید
                $typeItem = TypeItem::create([
                    'key' =>  $itemIndex + 1,
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product'],
                    'description' => $item['description'],
                    'price' => $item['price_per_unit'] ?? 0 // قیمت محاسبه شده یا مقدار نمونه
                ]);

                // ایجاد آیتم فنی جدید برای هر بعد
                foreach ($item['dimensions'] as $dimensionIndex => $dimension) {

                    DimensionItem::create([
                        'key' => $dimensionIndex + 1,
                        'invoice_id' => $invoice->id,
                        'type_id' => $typeItem->id,
                        'height' => $dimension['height'],
                        'width' => $dimension['width'],
                        'weight' =>$dimension['weight'],
                        'quantity' => $dimension['quantity'],
                        'over' => $dimension['quantity'],  //TODO: اضافه کردن درصد اور
                        'description' => $dimension['description']
                    ]);
                }

                // ایجاد آیتم‌های ابعاد جدید
                $technicalDetails = $item['technical_details'];
                TechnicalItem::create([
                    'invoice_id' => $invoice->id,
                    'type_id' => $typeItem->id,
                    'edge_type' => $technicalDetails['edge_type'],
                    'glue_type' => $technicalDetails['glue_type'],
                    'post_type' => $technicalDetails['post_type'],
                    'delivery_date' => $technicalDetails['delivery_date'],
                    'frame' => $technicalDetails['frame'],
                    'balance' => $technicalDetails['balance'],
                    'vault_type' => $technicalDetails['vault_type'],
                    'part_number' => $technicalDetails['part_number'],
                    'map_dimension' => $technicalDetails['map_dimension'],
                    'map_view' => $technicalDetails['map_view'],
                    'vault_number' => $technicalDetails['vault_number'],
                    'delivery_meterage' => $technicalDetails['delivery_meterage'],
                    'order_number' => $technicalDetails['order_number'],
                    'usage' => $technicalDetails['usage'],
                    'car_type' => $technicalDetails['car_type'],
                ]);
            }
        });
        DB::commit();
        return response()->json(['message' => 'فاکتور با موفقیت ایجاد شد'], 201);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response(['message' => 'خطایی به وجود آمده است'], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(ShowInvoiceRequest $request)
    {
        try {
            $invoice = \App\Models\Invoice::with(['user', 'customer', 'typeItems.product', 'typeItems.technicalItems', 'typeItems.dimensionItems'])
                ->findOrFail($request->invoice);
            return new InvoiceResource($invoice);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while retrieving the invoice',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /*public function download(string $id)
    {
        $InvoiceService =new InvoiceService();

      $Invoice = \App\Models\Invoice::findOrFail($id);
      $item = json_decode($Invoice->items, true);

      $invoice = $InvoiceService->information($Invoice->user_id , $Invoice->customer_id , $item);

      //dd($invoice);
      $invoice->save('public');

      $link = $invoice->url();

      return $invoice->stream();
    }*/

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, string $id)
    {
        // اعتبارسنجی داده‌های ورودی
        $validatedData = $request->all();
        $invoiceId = $request->invoice;
        try {
            // استفاده از تراکنش برای اطمینان از صحت ذخیره‌سازی داده‌ها
            DB::transaction(function () use ($validatedData, $invoiceId) {
                // پیدا کردن فاکتور مورد نظر
                $invoice = \App\Models\Invoice::findOrFail($invoiceId);

                // بروزرسانی اطلاعات فاکتور
                $invoice->update([
                    'customer_id' => $validatedData['buyer'],
                    'position' => $validatedData['position'],
                    'status' => $validatedData['status'],
                ]);

                // تعریف توابع محاسبه قیمت بر اساس محصول
                $productFunctions = [
                    1 => 'calculatePriceScorit',
                    2 => 'calculatePriceLaminate',
                    3 => 'calculatePriceDouble'
                ];

                // ایجاد یک نمونه از سرویس فاکتور
                $invoiceService = new InvoiceService();

                foreach ($validatedData['items'] as $itemIndex => $item) {
                    $productId = $item['product'];

                    // محاسبه قیمت بر اساس محصول
                    if (array_key_exists($productId, $productFunctions)) {
                        $functionName = $productFunctions[$productId];
                        if (is_array($item['description'])) {
                            $calculatedPrice = $invoiceService->$functionName($item['description']);
                            if ($calculatedPrice !== null) {
                                $item['price_per_unit'] = $calculatedPrice;
                            } else {
                                throw new \Exception('مشکل در محاسبه قیمت محصول');
                            }
                        }
                    }

                    // ترکیب توضیحات محصول
                    if (is_array($item['description'])) {
                        $item['description'] = $invoiceService->mergeProductStructures($item['description']);
                    }

                    // بروزرسانی یا ایجاد آیتم نوعی جدید
                    $typeItem = TypeItem::updateOrCreate(
                        ['invoice_id' => $invoice->id, 'key' => $itemIndex + 1],
                        [
                            'product_id' => $item['product'],
                            'description' => $item['description'],
                            'price' => $item['price_per_unit'] ?? 0 // قیمت محاسبه شده یا مقدار نمونه
                        ]
                    );

                    // بروزرسانی یا ایجاد آیتم فنی جدید برای هر بعد
                    foreach ($item['dimensions'] as $dimensionIndex => $dimension) {
                        DimensionItem::updateOrCreate(
                            ['invoice_id' => $invoice->id, 'type_id' => $typeItem->id, 'key' => $dimensionIndex + 1],
                            [
                                'height' => $dimension['height'],
                                'width' => $dimension['width'],
                                'weight' => $dimension['weight'],
                                'quantity' => $dimension['quantity'],
                                'over' => $dimension['quantity'],  //TODO: اضافه کردن درصد اور
                                'description' => $dimension['description']
                            ]
                        );
                    }

                    // بروزرسانی یا ایجاد آیتم‌های ابعاد جدید
                    $technicalDetails = $item['technical_details'];
                    TechnicalItem::updateOrCreate(
                        ['invoice_id' => $invoice->id, 'type_id' => $typeItem->id],
                        [
                            'edge_type' => $technicalDetails['edge_type'],
                            'glue_type' => $technicalDetails['glue_type'],
                            'post_type' => $technicalDetails['post_type'],
                            'delivery_date' => $technicalDetails['delivery_date'],
                            'frame' => $technicalDetails['frame'],
                            'balance' => $technicalDetails['balance'],
                            'vault_type' => $technicalDetails['vault_type'],
                            'part_number' => $technicalDetails['part_number'],
                            'map_dimension' => $technicalDetails['map_dimension'],
                            'map_view' => $technicalDetails['map_view'],
                            'vault_number' => $technicalDetails['vault_number'],
                            'delivery_meterage' => $technicalDetails['delivery_meterage'],
                            'order_number' => $technicalDetails['order_number'],
                            'usage' => $technicalDetails['usage'],
                            'car_type' => $technicalDetails['car_type']
                        ]
                    );
                }
            });

            DB::commit();
            return response()->json(['message' => 'فاکتور با موفقیت بروزرسانی شد'], 200);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response()->json(['message' => 'خطایی به وجود آمده است'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $invoice)
    {
        try {
            // استفاده از تراکنش برای اطمینان از صحت عملیات حذف
            DB::transaction(function () use ($invoice) {
                // پیدا کردن فاکتور به همراه وابستگی‌هایش
                $invoice = \App\Models\Invoice::with(['typeItems.technicalItems', 'typeItems.dimensionItems'])
                    ->findOrFail($invoice);

                // حذف وابستگی‌های فاکتور
                foreach ($invoice->typeItems as $typeItem) {
                    // حذف آیتم‌های فنی مرتبط با آیتم نوعی
                    foreach ($typeItem->technicalItems as $technicalItem) {
                        $technicalItem->delete();
                    }
                    // حذف آیتم‌های ابعاد مرتبط با آیتم نوعی
                    foreach ($typeItem->dimensionItems as $dimensionItem) {
                        $dimensionItem->delete();
                    }
                    // حذف آیتم نوعی
                    $typeItem->delete();
                }
                // حذف خود فاکتور
                $invoice->delete();
            });

            DB::commit();
            return response()->json(['message' => 'فاکتور و تمام وابستگی‌های آن با موفقیت حذف شد'], 200);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response()->json(['message' => 'خطایی به وجود آمده است'], 500);
        }
    }
}
