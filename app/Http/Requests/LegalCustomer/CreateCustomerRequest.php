<?php

namespace App\Http\Requests\LegalCustomer;

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
            'national_id' => ['nullable', 'string', 'max:11'],
            'registration_number' => ['nullable', 'string' ,'max:4'],
            'phone' => ['required_without:mobile'],
            'mobile' => ['required_without:phone', new MobileRule ],
            'type' => 'required|in:' . implode(',', CustomerType::toValues()),
            'postal_code' => ['nullable', 'string', 'max:10'],
            'address' => ['required', 'string'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'city_id' => ['nullable','exists:cities,id'],
        ];
    }
}
