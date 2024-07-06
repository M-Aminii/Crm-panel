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
        $userDiscounts = Access::with('user:id,name,last_name,mobile')->get(['id', 'user_id']);

        return response()->json($userDiscounts);
    }

    public function store(CreateAccessRequest $request)
    {

        Access::create($request->validated());

        return response()->json(['message' => 'دسترسی برای کاربر با موفقیت اعمال شد'], 201);
    }

    public function show($id)
    {
        $UserAccess = Access::findOrFail($id);
        return response()->json($UserAccess);
    }

    public function update(UpdateAccessRequest $request, $id)
    {

        $UserAccess = Access::findOrFail($id);

        $UserAccess->update($request->validated());

        return response()->json($UserAccess);
    }

    public function destroy($id)
    {

        $userDiscount = Access::findOrFail($id);
        $userDiscount->delete();

        return response()->json(['message' => 'دسترسی کاربر با موفقیت به حالت پیش فرض تغییر کرد']);
    }
}

