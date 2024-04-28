<?php

namespace App\Http\Controllers;

use App\DTO\IndividualCustomerDTO;

use App\Http\Requests\IndividualCustomer\CreateIndividualCustomerRequest;
use App\Http\Requests\IndividualCustomer\UpdateIndividualCustomerRequest;
use App\Models\IndividualCustomer;
use App\Services\FilterService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IndividualCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = IndividualCustomer::query();
        // اعمال فیلترها
        $query = FilterService::ApplyFilterCustomers($query,$request);
        // انتخاب فیلدهای مورد نیاز
        $fields = ['name', 'user_id', 'national_id', 'phone', 'mobile'];
        // انجام کوئری و بازگشت نتیجه
        $customers = $query->select($fields)->get();
        // بازگشت نتیجه به عنوان پاسخ
        return response()->json($customers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateIndividualCustomerRequest $request)
    {
        try {
            DB::beginTransaction();
            $LegalCustomerDTO = new IndividualCustomerDTO($request->validated());
            IndividualCustomer::create((array) $LegalCustomerDTO);
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
            // جستجوی شخص حقیقی با استفاده از شناسه
            $individualCustomer = IndividualCustomer::findOrFail($id);
            // بازگشت نتیجه به عنوان پاسخ
            return response()->json($individualCustomer);
        } catch (ModelNotFoundException $exception) {
            // در صورتی که شخص حقیقی پیدا نشود
            return response()->json(['message' => 'individualCustomer not found'], 404);
        } catch (Exception $exception) {
            // در صورتی که خطای دیگری رخ دهد
            return response()->json(['message' => 'خطایی به وجود آمده است'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIndividualCustomerRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            // یافتن شخص حقیقی
            $individualCustomer = IndividualCustomer::findOrFail($id);
            // پر کردن فیلدهای داده‌ای با استفاده از DTO
            $individualCustomer->fill($request->validated());
            // بررسی تغییرات قبل از ذخیره
            if ($individualCustomer->isDirty()) {
                // ذخیره شخص حقیقی
                $individualCustomer->save();
            }
            //TODO:زمان تغییر نکردن موردی ریپانس برگرده

            DB::commit();
            return response()->json(['message' => 'اطلاعات شخص حقیقی با موفقیت بروزرسانی شد'], 200);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            return response(['message' => 'شخص حقیقی مورد نظر یافت نشد'], 404);
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
