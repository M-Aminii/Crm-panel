<?php

namespace App\Http\Requests\Customer;

use App\Enums\CustomerType;
use App\Enums\UserGender;
use App\Enums\UserStatus;
use App\Rules\MobileRule;
use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCustomerRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'national_id' => ['nullable', 'integer', 'max:11', 'unique:customers,national_id'],
            'registration_number' => ['nullable', 'integer' ,'max:4', 'unique:customers,registration_number'],
            'phone' => ['nullable', 'integer', 'max:15', 'required_without:mobile', 'unique:customers,phone'],
            'mobile' => ['nullable', 'integer', 'max:15', 'required_without:phone', new MobileRule, 'unique:customers,mobile'],
            'type' => 'required|in:' . implode(',', CustomerType::toValues()),
            'postal_code' => ['nullable', 'integer', 'max:10', 'unique:customers,postal_code'],
            'address' => ['nullable', 'string'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
        ];
    }

}
