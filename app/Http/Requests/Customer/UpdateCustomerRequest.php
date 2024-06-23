<?php

namespace App\Http\Requests\Customer;

use App\Enums\CustomerStatus;
use App\Enums\CustomerType;
use App\Enums\UserGender;
use App\Enums\UserStatus;
use App\Models\Customer;
use App\Rules\MobileRule;
use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // بررسی وجود مشتری
        $customer = Customer::find($this->route('customer'));
        if (!$customer) {
            throw new NotFoundHttpException('مشتری وجود ندارد');
        }
        // استفاده از Gate برای بررسی مجوز
        return Gate::allows('update', $customer);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:100'],
            'national_id' => ['nullable', 'integer', 'max:11'],
            'registration_number' => ['nullable', 'integer' ,'max:4'],
            'phone' => ['nullable', 'integer', 'max:15', 'required_without:mobile'],
            'mobile' => ['nullable', 'integer', 'max:15', 'required_without:phone', new MobileRule],
            'type' => 'nullable|in:' . implode(',', CustomerType::toValues()),
            'status' => 'nullable|in:' . implode(',', CustomerStatus::toValues()),
            'postal_code' => ['nullable', 'integer', 'max:10'],
            'address' => ['nullable', 'string'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
        ];
    }
}
