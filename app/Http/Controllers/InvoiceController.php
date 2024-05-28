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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ListInvoiceRequest $request)
    {
        $user = Auth::user();
        // انتخاب فیلدهای مورد نیاز
        $fields = ['user_id', 'customer_id','serial_number', 'status'];

        if ($user->hasAnyAdminRole()) {
            $query = \App\Models\Invoice::all($fields);
        } else {
            // در غیر این صورت، فقط مشتریان کاربر جاری را دریافت کن
            $query = $user->customers()->select($fields)->get();
        }
        // انجام کوئری و بازگشت نتیجه
        $customers = $query->select($fields)->get();
        // بازگشت نتیجه به عنوان پاسخ
        return response()->json($customers);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateInvoiceRequest $request)
    {
       /* $userId = auth('api')->id();
        $buyerId = $request->input('buyer');
        $customer = Customer::find($buyerId);

        // بررسی وضعیت مشتری
        if ($customer->status === "Incomplete" || $customer->status === "Inactive") {
            return response()->json(['message' => 'امکان صدور فاکتور برای این مشتری وجود ندارد'], 404);
        }

        $items = $request->input('items');
        $productFunctions = [
            1 => 'calculatePriceSecorit',
            2 => 'calculatePriceLaminate',
            3 => 'calculatePriceDouble'
        ];

        $invoiceService = new InvoiceService();*/
        // اعتبارسنجی داده‌های ورودی
        $validatedData = $request->all();

        // استفاده از تراکنش برای اطمینان از صحت ذخیره‌سازی داده‌ها
        DB::transaction(function () use ($validatedData) {
            // ایجاد فاکتور جدید
            $invoice = \App\Models\Invoice::create([
                'serial_number' => 'SR-' . time(), // مقدار نمونه، شما می‌توانید این را تغییر دهید
                'user_id' => auth()->id(),
                'customer_id' => $validatedData['buyer'],
                'position' => 'Some Position', // مقدار نمونه، شما می‌توانید این را تغییر دهید
                'status' => 'informal', // مقدار نمونه، شما می‌توانید این را تغییر دهید
            ]);

            foreach ($validatedData['items'] as $itemIndex => $item) {
                // ایجاد آیتم نوعی جدید
                $typeItem = TypeItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product'],
                    'description' => 'ggggggg',
                    'price' => 0 // مقدار نمونه، شما می‌توانید این را تغییر دهید
                ]);

                // ایجاد آیتم فنی جدید برای هر بعد
                foreach ($item['dimensions'] as $dimensionIndex => $dimension) {
                    TechnicalItem::create([
                        'invoice_id' => $invoice->id,
                        'type_id' => $typeItem->id,
                        'height' => $dimension['height'],
                        'width' => $dimension['width'],
                        'over' => $dimension['quantity'], // استفاده از 'quantity' به عنوان 'over'
                        'description' => $dimension['description'],
                        'index' => $dimensionIndex + 1
                    ]);
                }

                // ایجاد آیتم‌های ابعاد جدید
                $technicalDetails = $item['technical_details'];
                DimensionItem::create([
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
                    'product_index' => $itemIndex + 1
                ]);
            }
        });

        return response()->json(['message' => 'Invoice created successfully'], 201);

    }

    /*foreach ($items as &$item) {
                $productId = $item['title'];

                // اعمال تغییرات بر روی هر بخش از description
                if (array_key_exists($productId, $productFunctions)) {
                    $functionName = $productFunctions[$productId];
                    if (is_array($item['description'])) {
                        $calculatedPrice = $invoiceService->$functionName($item['description']);
                        if ($calculatedPrice !== null) {
                            $item['price_per_unit'] = $calculatedPrice;
                        } else {
                            return response()->json(['message' => 'مشکل در محاسبه قیمت محصول'], 500);
                        }
                    }
                }

                // بروزرسانی فیلد title
                $product = Product::select('name')->find($item['title']);
                if ($product) {
                    $item['title'] = $product->name;
                }

                // اعمال تغییرات بر روی description
                if (is_array($item['description'])) {
                    $item['description'] = $invoiceService->mergeProductStructures($item['description']);
                }
            }
            unset($item); // پاک کردن ارجاع به $item

            try {
                DB::beginTransaction();
                $lastInvoiceSerial = InvoiceService::generateNewSerial();
                \App\Models\Invoice::create([
                    'user_id' => $userId,
                    'customer_id' => $buyerId,
                    'serial_number' => $lastInvoiceSerial,
                    'status' => "informal",
                    'items' => json_encode($items) // ذخیره کردن کل آیتم‌ها به صورت JSON
                ]);
                DB::commit();

                return response()->json([
                    'message' => 'فاکتور با موفقیت ایجاد شد',
                ], 201);
            } catch (Exception $exception) {
                DB::rollBack();
                Log::error($exception);
                return response()->json(['message' => 'خطایی به وجود آمده است'], 500);
            }*/
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

    /*try {
                // جستجوی شخص با استفاده از شناسه
                $Invoice = \App\Models\Invoice::findOrFail($request->invoice);

                $user = User::select('name', 'last_name','mobile')->find($Invoice->user_id);
                $customer = Customer::select('mobile', 'name', 'national_id', 'registration_number', 'mobile', 'postal_code', 'address')->find($Invoice->customer_id);

                // بازگشت نتیجه به عنوان پاسخ
                return response()->json([
                    'user'=> $user,
                    'customer'=>$customer,
                    'serial_number'=>$Invoice->serial_number,
                    'status'=>$Invoice->status,
                    'items'=>json_decode($Invoice->items),
                    'updated_at'=>$Invoice->updated_at
                    ]);
            } catch (ModelNotFoundException $exception) {
                // در صورتی که شخص  پیدا نشود
                return response()->json(['message' => 'فاکتور پیدا نشد'], 404);
            } catch (Exception $exception) {
                // در صورتی که خطای دیگری رخ دهد
                return response()->json(['message' => 'خطایی به وجود آمده است'], 500);
            }*/
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
        try {
            DB::beginTransaction();
            // یافتن فاکتور
            $Invoice =  \App\Models\Invoice::findOrFail($id);
            // پر کردن فیلدهای داده‌ای با استفاده از DTO
            $Invoice->fill($request->validated());
            // بررسی تغییرات قبل از ذخیره
            if ($Invoice->isDirty()) {
                // ذخیره کاربر
                $Invoice->save();
            }
            //TODO:زمان تغییر نکردن موردی ریپانس برگرده

            DB::commit();
            return response()->json(['message' => 'اطلاعات فاکتور با موفقیت بروزرسانی شد'], 200);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            return response(['message' => 'فاکتور مورد نظر یافت نشد'], 404);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response(['message' => 'خطایی به وجود آمده است'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            \App\Models\Invoice::destroy($id);
            DB::commit();
            return response(['message' => 'فاکتور مورد نظر با موفقیت حذف شد'], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response(['message' => 'حذف سوال با شکست مواجه شد'], 500);
        }
    }
}
