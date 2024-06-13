<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserDiscount\CreateUserDiscountRequest;
use App\Http\Requests\UserDiscount\UpdateUserDiscountRequest;
use App\Models\UserDiscount;


class UserDiscountController extends Controller
{
    public function index()
    {
        $userDiscounts = UserDiscount::all();
        return response()->json($userDiscounts);
    }

    public function store(CreateUserDiscountRequest $request)
    {

        $userDiscount = UserDiscount::create($request->validated());

        return response()->json($userDiscount, 201);
    }

    public function show($id)
    {
        $userDiscount = UserDiscount::findOrFail($id);
        return response()->json($userDiscount);
    }

    public function update(UpdateUserDiscountRequest $request, $id)
    {

        $userDiscount = UserDiscount::findOrFail($id);
        $userDiscount->update($request->validated());

        return response()->json($userDiscount);
    }

    public function destroy($id)
    {
        $userDiscount = UserDiscount::findOrFail($id);
        $userDiscount->delete();

        return response()->json(['message' => 'User discount deleted successfully']);
    }
}

