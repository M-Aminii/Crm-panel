<?php
namespace App\Services;

use App\Rules\PasswordRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Rfc4122\Validator;

class PasswordService
{
    public function setPassword($password, $password_confirmation)
    {
        try {
            if ($password !== $password_confirmation) {
                return response(['message' => 'رمزهای عبور با هم مطابقت ندارند'], 400);
            }

            $user = Auth::user();
            $user->password = bcrypt($password);
            $user->save();

            return response(['message' => 'رمز عبور با موفقیت تغییر یافت'], 200);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response(['message' => 'خطایی به وجود آمده است'], 500);
        }
    }
}
