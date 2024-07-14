<?php

namespace App\Http\Controllers;

use App\Http\Resources\FinalOrderResource;
use App\Models\FinalOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinalOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // دریافت تمام سفارشات نهایی به همراه بارگذاری روابط
        $finalOrders = FinalOrder::with([
            'user',
            'customer',
            'items.product',
            'invoice'
        ])->get();

        // بازگرداندن ریسورس با لیست سفارشات نهایی
        return FinalOrderResource::collection($finalOrders);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        // پیدا کردن FinalOrder با استفاده از شناسه
        $finalOrder = FinalOrder::findOrFail($id);

        // تنظیم تاریخ فعلی به عنوان financial_approval_date
        $finalOrder->financial_approval_date = Carbon::now();
        $finalOrder->save();

        return response()->json(['message' => 'Financial approval date updated successfully', 'finalOrder' => $finalOrder]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
