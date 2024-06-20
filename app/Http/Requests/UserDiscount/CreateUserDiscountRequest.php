<?php

namespace App\Http\Requests\UserDiscount;

use App\Enums\UserGender;
use App\Enums\UserStatus;
use App\Rules\MobileRule;
use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserDiscountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', Rule::unique('user_discounts', 'user_id')],
            'max_discount' => 'required|integer|min:0|max:20',
            'payment_terms' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'user_id.unique' => 'برای این کاربر از قبل تنظیمات فاکتور مشخص شده است .',
        ];
    }
}
