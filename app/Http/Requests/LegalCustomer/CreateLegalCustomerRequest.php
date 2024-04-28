<?php

namespace App\Http\Requests\LegalCustomer;

use App\Enums\UserGender;
use App\Enums\UserStatus;
use App\Rules\MobileRule;
use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateLegalCustomerRequest extends FormRequest
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
            'national_id' => ['nullable', 'string', 'max:11', Rule::unique('legal_customers')],
            'phone' => ['required_without:mobile', Rule::unique('legal_customers')],
            'mobile' => ['required_without:phone', new MobileRule ,Rule::unique('legal_customers')],
            'registration_number' => ['nullable', 'string' , Rule::unique('legal_customers')],
            'postal_code' => ['nullable', 'string', 'max:10', Rule::unique('legal_customers')],
            'address' => ['required', 'string'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'city_id' => ['nullable','exists:cities,id'],

        ];
    }
}
