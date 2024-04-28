<?php

namespace App\Http\Controllers;

use App\DTO\LegalCustomerDTO;
use App\Http\Requests\LegalCustomer\CreateIndividualCustomerRequest;
use App\Http\Requests\LegalCustomer\CreateLegalCustomerRequest;
use App\Http\Requests\LegalCustomer\UpdateLegalCustomerRequest;
use App\Models\LegalCustomer;
use App\Services\FilterService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LegalCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LegalCustomer::query();
        // اعمال فیلترها
        $filters = [
            'name' => 'name',
            'phone' => 'phone',
            'national_id' => 'national_id',
            'mobile' => 'mobile',
        ];
        $query = FilterService::ApplyFilterCustomers($query,$filters,$request);
        // انتخاب فیلدهای مورد نیاز
        $fields = ['name', 'user_id', 'national_id', 'registration_number', 'phone', 'mobile'];
        // انجام کوئری و بازگشت نتیجه
        $customers = $query->select($fields)->get();
        // بازگشت نتیجه به عنوان پاسخ
        return response()->json($customers);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateLegalCustomerRequest $request)
    {
        try {
            DB::beginTransaction();
            $LegalCustomerDTO = new LegalCustomerDTO($request->validated());
            LegalCustomer::create((array) $LegalCustomerDTO);
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
            // جستجوی شخص حقوقی با استفاده از شناسه
            $legalCustomer = LegalCustomer::findOrFail($id);
            // بازگشت نتیجه به عنوان پاسخ
            return response()->json($legalCustomer);
        } catch (ModelNotFoundException $exception) {
            // در صورتی که شخص حقوقی پیدا نشود
            return response()->json(['message' => 'LegalCustomer not found'], 404);
        } catch (Exception $exception) {
            // در صورتی که خطای دیگری رخ دهد
            return response()->json(['message' => 'خطایی به وجود آمده است'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLegalCustomerRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            // یافتن کاربر
            $legalCustomer = LegalCustomer::findOrFail($id);
            // پر کردن فیلدهای داده‌ای با استفاده از DTO
            $legalCustomer->fill($request->validated());
            // بررسی تغییرات قبل از ذخیره
            if ($legalCustomer->isDirty()) {
                // ذخیره کاربر
                $legalCustomer->save();
            }
                //TODO:زمان تغییر نکردن موردی ریپانس برگرده

            DB::commit();
            return response()->json(['message' => 'اطلاعات شخص حقوقی با موفقیت بروزرسانی شد'], 200);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            return response(['message' => 'شخص حقوقی مورد نظر یافت نشد'], 404);
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
