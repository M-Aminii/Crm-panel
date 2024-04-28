<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (! $users = User::With('roles.permissions')->get()){
            throw new NotFoundHttpException('user not found');
        }
        return $users;
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




    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

        try {
            DB::beginTransaction();

            // دریافت کاربر
            $user = auth()->user(); // یا از طریق $request->user() هم می‌توانید دریافت کنید

            // بررسی وجود نقش مورد نظر
            $roleName = $request->input('role');



            // اختصاص نقش به کاربر
            $user->syncRoles($roleName);


            DB::commit();
            return response(['message' => 'مشخصات فردی بروزرسانی شد'], 200);
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
