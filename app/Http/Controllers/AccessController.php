<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Access\CreateAccessRequest;
use App\Http\Requests\Access\UpdateAccessRequest;
use App\Models\Access;


class AccessController extends Controller
{
    public function index()
    {
        $userDiscounts = Access::all();
        return response()->json($userDiscounts);
    }

    public function store(CreateAccessRequest $request)
    {

        Access::create($request->validated());

        return response()->json(['message' => 'دسترسی برای کاربر با موفقیت اعمال شد'], 201);
    }

    public function show($userid)
    {
        $user = Access::where('user_id', $userid)->first();
        $userDiscount = Access::findOrFail($user->id);
        return response()->json($userDiscount);
    }

    public function update(UpdateAccessRequest $request, $userid)
    {
        $user = Access::where('user_id', $userid)->first();
        $userDiscount = Access::findOrFail($user->id);

        $userDiscount->update($request->validated());

        return response()->json($userDiscount);
    }

    public function destroy($userid)
    {
        $user = Access::where('user_id', $userid)->first();
        $userDiscount = Access::findOrFail($user->id);
        $userDiscount->delete();

        return response()->json(['message' => 'دسترسی کاربر با موفقیت به حالت پیش فرض تغییر کرد']);
    }
}

