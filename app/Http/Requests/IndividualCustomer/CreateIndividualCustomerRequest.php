<?php

namespace App\Http\Requests\IndividualCustomer;

use App\Enums\UserGender;
use App\Enums\UserStatus;
use App\Rules\MobileRule;
use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateIndividualCustomerRequest extends FormRequest
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
            'national_id' => ['nullable', 'string', 'max:10', Rule::unique('individual_customers')],
            'phone' => ['required_without:mobile', Rule::unique('individual_customers')],
            'mobile' => ['required_without:phone', new MobileRule ,Rule::unique('individual_customers')],
            'postal_code' => ['nullable', 'string', 'max:10', Rule::unique('individual_customers')],
            'address' => ['required', 'string'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
         ];
    }
}
