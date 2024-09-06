<?php

namespace App\Http\Controllers;

use App\Enums\InformalInvoiceStatus;
use App\Enums\InvoiceStatus;
use App\Enums\AccessPayment;
use App\Exceptions\DimensionException;
use App\Exceptions\InvalidDiscountException;
use App\Exceptions\LaminatedColoredGlassDimensionException;
use App\Exceptions\SatinGlassDimensionException;
use App\Exceptions\SpecialGlassDimensionException;
use App\Exceptions\WeightExceededException;
use App\Helpers\Benchmark;
use App\Http\Requests\Customer\ShowCustomerRequest;
use App\Http\Requests\Invoice\CreateInvoiceRequest;
use App\Http\Requests\Invoice\ListInvoiceRequest;
use App\Http\Requests\Invoice\ShowInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\AggregatedItem;
use App\Models\Customer;
use App\Models\DescriptionDimension;
use App\Models\DimensionItem;
use App\Models\FinalOrder;
use App\Models\Product;
use App\Models\TechnicalItem;
use App\Models\TypeItem;
use App\Models\User;
use App\Services\InvoiceService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
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

    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(ListInvoiceRequest $request, $status)
    {
        $user = auth()->user();

        // تعریف وضعیت‌های معتبر
        $validStatuses = [InvoiceStatus::Formal, InvoiceStatus::InFormal, InvoiceStatus::PreBuy];

        // بررسی معتبر بودن وضعیت
        if (!in_array($status, $validStatuses)) {
            return response()->json(['error' => 'وضعیت وارد شده نامعتبر است'], 400);
        }

        // ایجاد کوئری اصلی برای دریافت فاکتورها
        $query = \App\Models\Invoice::select('id', 'serial_number', 'user_id', 'customer_id', 'description', 'status', 'informal_status', 'pre_payment', 'before_delivery', 'cheque')
            ->with(['user:id,name,last_name', 'customer:id,name']);

        // اگر کاربر نقش مدیر یا مدیر فروش ندارد، فاکتورها را بر اساس user_id فیلتر کنید
        if (!$user->hasAnyAdminRole()) {
            $query->where('user_id', $user->id);
        }

        // استفاده از اسکوپ برای فیلتر کردن بر اساس وضعیت
        if ($status) {
            if ($status === InvoiceStatus::Formal) {
                $query->formal();
            } elseif ($status === InvoiceStatus::InFormal) {
                $query->informal();
            } elseif ($status === InvoiceStatus::PreBuy) {
                $query->prebuy();
            }
        }

        // اجرای کوئری و دریافت نتایج
        $invoices = $query->get();

        // دریافت اطلاعات تخفیف‌ها
        $userDiscounts = \App\Models\Access::whereIn('user_id', $invoices->pluck('user_id'))->get()->keyBy('user_id');

        // ترکیب دستی نتایج
        $invoices->each(function ($invoice) use ($userDiscounts) {
            $access = $userDiscounts->get($invoice->user_id);
            $invoice->max_discount = optional($access)->max_discount ?? 20;
            $invoice->payment_terms = optional($access)->payment_terms ?? AccessPayment::CASH;
            $invoice->min_pre_payment = optional($access)->min_pre_payment ?? 30;
        });

        return response()->json($invoices, 200);
    }



    /**
     * Store a newly created resource in storage.
     */
/*    public function store(CreateInvoiceRequest $request)
    {
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            $lastInvoiceSerial = InvoiceService::generateNewSerial();
            $invoice = \App\Models\Invoice::create([
                'serial_number' => $lastInvoiceSerial,
                'user_id' => auth()->id(),
                'customer_id' => $validatedData['buyer'],
                'description' => $validatedData['description'], //TODO: در این مکان توضیحات برای فاکتور زده میشه ولیدیشن ها درست شود
                'status' => InvoiceStatus::InFormal,
                'informal_status' => InformalInvoiceStatus::PENDING_APPROVAL,
                'discount' => $validatedData['discount'],
                'delivery' => $validatedData['delivery'],
            ]);

            $AmountPayable = InvoiceService::processItems($invoice, $validatedData['items'], $validatedData['discount'], $validatedData['delivery']);

            $invoice->update(['amount_payable' => $AmountPayable]);

            DB::commit();

            return response()->json(['message' => 'فاکتور با موفقیت ایجاد شد'], 201);
        } catch (InvalidDiscountException $exception) {
            DB::rollBack();
            Log::error($exception);
            return response()->json(['message' => $exception->getMessage()], 422);
        } catch (DimensionException $exception) {
            DB::rollBack();
            Log::error($exception);
            $message = $exception->getMessage() . " ساختار شماره {$exception->getProductIndex()} ردیف  {$exception->getDimensionIndex()} وجود ندارد  ";
            return response()->json(['message' => $message], 422);
        } catch (WeightExceededException $exception) {
            DB::rollBack();
            Log::error($exception);
            $message = "وزن ابعاد در ساختار شماره {$exception->getProductIndex()} و ردیف {$exception->getDimensionIndex()} برابر با {$exception->getWeight()} کیلوگرم است که بیش از حد مجاز می‌باشد.";
            return response()->json(['message' => $message], 422);
        }catch (SatinGlassDimensionException $exception) {
            DB::rollBack();
            Log::error($exception);
            $message = "وزن ابعاد در ساختار شماره {$exception->getProductIndex()} و ردیف {$exception->getDimensionIndex()} برابر با {$exception->getWidth()} کیلوگرم است که بیش از حد مجاز می‌باشد.";
            return response()->json(['message' => $message], 422);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response()->json(['message' => 'خطایی به وجود آمده است: ' . $exception->getMessage()], 500);
        }
    }*/
    public function store(CreateInvoiceRequest $request)
    {
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            $lastInvoiceSerial = InvoiceService::generateNewSerial();
            $invoice = \App\Models\Invoice::create([
                'serial_number' => $lastInvoiceSerial,
                'user_id' => auth()->id(),
                'customer_id' => $validatedData['buyer'],
                'description' => $validatedData['description'],
                'status' => InvoiceStatus::InFormal,
                'informal_status' => InformalInvoiceStatus::PENDING_APPROVAL,
                'discount' => $validatedData['discount'],
                'delivery' => $validatedData['delivery'],
            ]);

            $AmountPayable = InvoiceService::processItems($invoice, $validatedData['items'], $validatedData['discount'], $validatedData['delivery']);

            $invoice->update(['amount_payable' => $AmountPayable]);

            DB::commit();

            return response()->json(['message' => 'فاکتور با موفقیت ایجاد شد'], 201);
        } catch (InvalidDiscountException $exception) {
            DB::rollBack();
            Log::error($exception);
            return response()->json(['message' => $exception->getMessage()], 422);
        } catch (DimensionException $exception) {
            DB::rollBack();
            Log::error($exception);
            $message = $exception->getMessage() . " ساختار شماره {$exception->getProductIndex()} ردیف {$exception->getDimensionIndex()} وجود ندارد.";
            return response()->json(['message' => $message], 422);
        } catch (WeightExceededException $exception) {
            DB::rollBack();
            Log::error($exception);
            $message = "وزن ابعاد در ساختار شماره {$exception->getProductIndex()} و ردیف {$exception->getDimensionIndex()} برابر با {$exception->getWeight()} کیلوگرم است که بیش از حد مجاز می‌باشد.";
            return response()->json(['message' => $message], 422);
        } catch (SpecialGlassDimensionException $exception) {
            DB::rollBack();
            Log::error($exception);
            $message = "ابعاد شیشه {$exception->getGlassType()} در ساختار شماره {$exception->getProductIndex()} و ردیف {$exception->getDimensionIndex()} برابر با ارتفاع: {$exception->getHeight()}، عرض: {$exception->getWidth()} است که بیش از حد مجاز می‌باشد.";
            return response()->json(['message' => $message], 422);
        }catch (LaminatedColoredGlassDimensionException $exception) {
            DB::rollBack();
            Log::error($exception);
            $message = "عرض شیشه لمینت رنگی در محصول شماره {$exception->getProductIndex()} و بعد {$exception->getDimensionIndex()} برابر با {$exception->getWidth()} میلی‌متر است که بیش از حد مجاز می‌باشد.";
            return response()->json(['message' => $message], 422);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response()->json(['message' => 'خطایی به وجود آمده است: ' . $exception->getMessage()], 500);
        }
    }





    /**
     * Display the specified resource.
     */
    public function show(ShowInvoiceRequest $request)
    {
        try {
            $invoice = \App\Models\Invoice::with(['user', 'customer', 'typeItems.product','typeItems.productSection', 'typeItems.technicalItems', 'typeItems.dimensionItems', 'typeItems.dimensionItems.descriptionDimensions' ,'aggregatedItems'])
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


        $invoiceId = $id;

        try {
            DB::transaction(function () use ($invoiceId, $validatedData) {
                $invoice = \App\Models\Invoice::findOrFail($invoiceId);

                $this->invoiceService->validateInvoiceUpdate($invoice, $validatedData);

                // به‌روزرسانی وضعیت و سه مقدار جدید
                $invoice->update([
                    'status' => $validatedData['status'] ?? $invoice->status,
                    'informal_status' => $validatedData['informal_status'] ?? $invoice->informal_status,
                    'discount' => $validatedData['discount'] ?? $invoice->discount,
                    'delivery' => $validatedData['delivery'] ?? $invoice->delivery,
                    'description' => $validatedData['description'] ?? $invoice->description,
                    'pre_payment' => $validatedData['pre_payment'] ?? $invoice->pre_payment,
                    'before_delivery' => $validatedData['before_delivery'] ?? $invoice->before_delivery,
                    'cheque' => $validatedData['cheque'] ?? $invoice->cheque,
                ]);

                // به‌روزرسانی مبلغ قابل پرداخت بر اساس موجودیت موارد ارسال شده
                if (isset($validatedData['items']) || isset($validatedData['discount']) || isset($validatedData['delivery'])) {
                    $items = $validatedData['items'] ?? $invoice->items;
                    $discount = $validatedData['discount'] ?? $invoice->discount;
                    $delivery = $validatedData['delivery'] ?? $invoice->delivery;

                    $amountPayable = InvoiceService::processItems($invoice, $items, $discount, $delivery);
                    $invoice->update(['amount_payable' => $amountPayable]);
                }


            });

            return response()->json(['message' => 'فاکتور با موفقیت به‌روزرسانی شد'], 200);
        } catch (InvalidDiscountException $exception) {
            DB::rollBack();
            Log::error($exception);
            return response()->json(['message' => $exception->getMessage()], 422);
        } catch (DimensionException $exception) {
            DB::rollBack();
            Log::error($exception);
            $message = $exception->getMessage() . " ساختار شماره {$exception->getProductIndex()} ردیف  {$exception->getDimensionIndex()} وجود ندارد  " ;
            return response()->json(['message' => $message], 422);
        } catch (WeightExceededException $exception) {
            DB::rollBack();
            Log::error($exception);
            $message = "وزن ابعاد در ساختار شماره {$exception->getProductIndex()} و ردیف {$exception->getDimensionIndex()} برابر با {$exception->getWeight()} کیلوگرم است که بیش از حد مجاز می‌باشد.";
            return response()->json(['message' => $message], 422);
        } catch (HttpResponseException $exception) {
            DB::rollBack();
            throw $exception;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response()->json(['message' => 'خطایی به وجود آمده است: ' . $exception->getMessage()], 500);
        }
    }


   /* public function changeStatus(Request $request, $id)
    {
        // اعتبارسنجی داده‌های ورودی
        $validatedData = $request->validate([
            'status' => 'required|in:formal,informal',
        ]);

        $status = $validatedData['status'];

        try {
            DB::transaction(function () use ($id, $status) {
                $invoice = Invoice::findOrFail($id);
                $originalStatus = $invoice->status;

                // بروزرسانی وضعیت فاکتور
                $invoice->update(['status' => $status]);

                if ($status === 'formal' && $originalStatus !== 'formal') {
                    // اگر وضعیت به رسمی تغییر کرد
                    $technicalItem = $invoice->technicalItem;
                    FinalOrder::updateOrCreate(
                        ['invoice_id' => $invoice->id],
                        [
                            'user_id' => $invoice->user_id,
                            'customer_id' => $invoice->customer_id,
                            'serial_number' => $invoice->serial_number,
                            'delivery_date' => $technicalItem->delivery_date, // فرض بر این که تکنیکال آیتِم موجود است
                            'informal_invoice_date' => $invoice->getOriginal('updated_at'),
                            'formal_invoice_date' => $invoice->updated_at,
                            'delivery_time' => 25, // یا مقدار محاسبه شده براساس نیاز شما
                            'pre_payment' => $invoice->pre_payment,
                            'before_delivery' => $invoice->before_delivery,
                            'cheque' => $invoice->cheque
                        ]
                    );
                } elseif ($status === 'informal' && $originalStatus === 'formal') {
                    // اگر وضعیت به غیر رسمی تغییر کرد
                    FinalOrder::where('invoice_id', $invoice->id)->delete();
                }
            });

            return response()->json(['message' => 'وضعیت فاکتور با موفقیت به‌روزرسانی شد'], 200);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'خطایی به وجود آمده است: ' . $exception->getMessage()], 500);
        }
    }*/

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $invoice)
    {
        try {
            // استفاده از تراکنش برای اطمینان از صحت عملیات حذف
            DB::transaction(function () use ($invoice) {
                // پیدا کردن فاکتور به همراه وابستگی‌هایش
                $invoice = \App\Models\Invoice::with(['typeItems.technicalItems', 'typeItems.dimensionItems', 'typeItems.dimensionItems.descriptionDimensions' ,'aggregatedItems'])
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
