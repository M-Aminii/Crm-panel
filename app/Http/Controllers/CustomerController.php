<?php

namespace App\Http\Controllers;

use App\DTO\CustomerDTO;
use App\Http\Requests\LegalCustomer\CreateCustomerRequest;
use App\Http\Requests\LegalCustomer\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\FilterService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Customer::query();
        // اعمال فیلترها
        $filters = [
            'name' => 'name',
            'phone' => 'phone',
            'national_id' => 'national_id',
            'mobile' => 'mobile',
        ];
        $query = FilterService::ApplyFilterCustomers($query,$filters,$request);
        // انتخاب فیلدهای مورد نیاز
        $fields = ['name', 'user_id', 'national_id', 'registration_number', 'phone', 'mobile','type'];
        // انجام کوئری و بازگشت نتیجه
        $customers = $query->select($fields)->get();
        // بازگشت نتیجه به عنوان پاسخ
        return response()->json($customers);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCustomerRequest $request)
    {
        try {
            DB::beginTransaction();
            $CustomerDTO = new CustomerDTO($request->validated());
            Customer::create((array) $CustomerDTO);
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
    public function show(string $id)
    {
        try {
            // جستجوی شخص با استفاده از شناسه
            $Customer = Customer::findOrFail($id);
            // بازگشت نتیجه به عنوان پاسخ
            return response()->json($Customer);
        } catch (ModelNotFoundException $exception) {
            // در صورتی که شخص  پیدا نشود
            return response()->json(['message' => 'مشتری پیدا نشد'], 404);
        } catch (Exception $exception) {
            // در صورتی که خطای دیگری رخ دهد
            return response()->json(['message' => 'خطایی به وجود آمده است'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            // یافتن کاربر
            $Customer = Customer::findOrFail($id);
            // پر کردن فیلدهای داده‌ای با استفاده از DTO
            $Customer->fill($request->validated());
            // بررسی تغییرات قبل از ذخیره
            if ($Customer->isDirty()) {
                // ذخیره کاربر
                $Customer->save();
            }
                //TODO:زمان تغییر نکردن موردی ریپانس برگرده

            DB::commit();
            return response()->json(['message' => 'اطلاعات مشتری با موفقیت بروزرسانی شد'], 200);
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
