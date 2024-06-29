<?php

namespace App\Http\Controllers;

use App\DTO\CustomerDTO;
use App\Http\Requests\Customer\CreateCustomerRequest;
use App\Http\Requests\Customer\ListCustomerRequest;
use App\Http\Requests\Customer\ShowCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\CustomerService;
use App\Services\FilterService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ListCustomerRequest $request)
    {
        $user = Auth::user();
        // اگر کاربر ادمین است، همه مشتریان را دریافت کن
        if ($user->hasAnyAdminRole()) {
            $query = Customer::all();
        } else {
            // در غیر این صورت، فقط مشتریان کاربر جاری را دریافت کن
            $query = $user->customers()->get();
        }
        // بازگشت نتیجه به عنوان پاسخ
        return response()->json($query);

     /*   $user = Auth::user();

        if ($user->hasAnyAdminRole()) {
            $customers = Customer::with(['province', 'city'])->get();
        } else {
            $customers = $user->customers()->with(['province', 'city'])->get();
        }

        // بازگشت نتیجه به عنوان پاسخ با استفاده از CustomerResource
        return CustomerResource::collection($customers);*/
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCustomerRequest $request)
    {
        try {
            DB::beginTransaction();
            $customer = new CustomerDTO($request->validated());
            $ServiceCustomer = new CustomerService();

            if ($ServiceCustomer->isDataComplete($customer)) {
                $customer->status = 'active';
            } else {
                $customer->status = 'incomplete';
            }
            $newCustomer = Customer::create((array) $customer);

            // اضافه کردن نقش به مشتری
            $newCustomer->assignCustomerRole(13);

            /*if ($request->has('roles')) {
                foreach ($request->input('roles') as $roleId) {
                    $newCustomer->assignCustomerRole($roleId);
                }
            }*/

            DB::commit();
            return response(['message' => 'مشخصات مشتری ثبت شد'], 201);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response(['message' => 'خطایی به وجود آمده است'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ShowCustomerRequest $request)
    {
        try {
            // جستجوی شخص با استفاده از شناسه
          /*  $Customer = Customer::findOrFail($request->customer);
            // بازگشت نتیجه به عنوان پاسخ
            return response()->json($Customer);*/
               // جستجوی شخص با استفاده از شناسه و بارگذاری روابط
               $customer = Customer::with(['province', 'city'])->findOrFail($request->customer);
               // بازگشت نتیجه به عنوان پاسخ با استفاده از CustomerResource
               return new CustomerResource($customer);
        } catch (ModelNotFoundException $exception) {
            // در صورتی که شخص پیدا نشود
            return response()->json(['message' => 'مشتری پیدا نشد'], 404);
        } catch (Exception $exception) {
            // در صورتی که خطای دیگری رخ دهد
            return response()->json(['message' => 'خطایی به وجود آمده است'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request)
    {
        try {
            DB::beginTransaction();

            // یافتن مشتری
            $customer = Customer::findOrFail($request->customer);

            // پر کردن فیلدهای داده‌ای با استفاده از DTO
            $customer->fill($request->validated());

            $ServiceCustomer = new CustomerService();

            if ($ServiceCustomer->isDataComplete($customer)) {
                $customer->status = 'active';
            } else {
                $customer->status = 'incomplete';
            }

            // چک کردن تغییرات
            if ($customer->isDirty()) {
                // اگر داده‌ها تغییر کرده بودند، آنها را ذخیره کنید
                $customer->save();

                // بروزرسانی نقش‌های مشتری
                /*if ($request->has('roles')) {
                    // حذف نقش‌های فعلی
                    $customer->customerRoles()->detach();

                    // اضافه کردن نقش‌های جدید
                    foreach ($request->input('roles') as $roleId) {
                        $customer->assignCustomerRole($roleId);
                    }
                }*/

                DB::commit();
                return response()->json(['message' => 'اطلاعات مشتری با موفقیت بروزرسانی شد'], 200);
            } else {
                // اگر هیچ تغییری در داده‌ها ایجاد نشده بود
                DB::rollBack();
                return response()->json(['message' => 'هیچ تغییری در اطلاعات مشتری ایجاد نشده است'], 200);
            }
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            return response(['message' => 'مشتری مورد نظر یافت نشد'], 404);
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
        //
    }
}
