<?php

namespace App\Http\Controllers;

use App\Helpers\Benchmark;
use App\Http\Requests\Invoice\CreateInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Services\InvoiceService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = \App\Models\Invoice::query();
        // انتخاب فیلدهای مورد نیاز
        $fields = ['user_id', 'customer_id','serial_number', 'status'];
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
        $user = auth('api')->id();
        $buyer = $request->input('buyer');
        $items = $request->input('items');

        // تعریف یک مپ برای اختصاص توابع به شناسه‌های محصول
        $productFunctions = [
            1 => 'calculatePriceSecorit',
            2 => 'calculatePriceLaminate',
            3 => 'calculatePriceDouble'
        ];

        $InvoiceService = new InvoiceService();
        // اعمال تغییرات بر روی هر آیتم و زیرآیتم‌ها
        foreach ($items as &$item) {
            $productId = $item['title'];
            if (array_key_exists($productId, $productFunctions)) {
                $functionName = $productFunctions[$productId];
                // اعمال تغییرات بر روی هر بخش از description
                if (is_array($item['description'])) {
                        // بررسی کنید که تابع به درستی فراخوانی و مقدار برگردانده می‌شود
                        $calculatedPrice = $InvoiceService->$functionName($item['description']);
                        if ($calculatedPrice !== null) {
                            $item['price_per_unit'] = $calculatedPrice;
                        } else {
                            // در صورت بروز مشکل، لاگ یا دیباگ
                           dd('مشکل در محاسبه قیمت محصول ');
                        }
                }
            }
            // بروزرسانی فیلد title
            $product = Product::select('name')->find($item['title']);
            if ($product) {
                $item['title'] = $product->name;
            }
            // اعمال دیگر تغییرات بر روی description
            if (is_array($item['description'])) {
                $item['description'] = $InvoiceService->mergeProductStructures($item['description']);
            }
        }
        unset($item); // پاک کردن ارجاع به $item

        try {
            DB::beginTransaction();
            $lastInvoice = InvoiceService::generateNewSerial();
            \App\Models\Invoice::create([
                'user_id' => $user,
                'customer_id' => $buyer,
                'serial_number' => $lastInvoice,
                'status' => "informal",
                'items' => json_encode($items) // ذخیره کردن کل آیتم‌ها به صورت JSON
            ]);
            DB::commit();

            return response([
                'message' => 'فاکتور با موفقیت ایجاد شد',
            ], 201);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response(['message' => 'خطایی به وجود آمده است'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // جستجوی شخص با استفاده از شناسه
            $Invoice = \App\Models\Invoice::findOrFail($id);

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
