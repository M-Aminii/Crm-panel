<?php

namespace App\Http\Controllers;

use App\Http\Resources\FinalOrderResource;
use App\Models\FinalOrder;
use Illuminate\Http\Request;

class FinalOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // متد برای نمایش تمام سفارشات نهایی
    public function index()
    {
       /* $finalOrders = FinalOrder::all();
        return response()->json($finalOrders);*/

        // دریافت تمام سفارشات نهایی به همراه بارگذاری روابط
        $finalOrders = FinalOrder::with(['user', 'customer'])->get();

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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
