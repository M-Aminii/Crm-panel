<?php

namespace App\Http\Requests\Customer;
use App\Enums\CustomerType;
use App\Models\Customer;
use App\Rules\MobileRule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;


class ListCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // استفاده از Gate برای بررسی مجوز
        return Gate::allows('viewAny', Customer::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
           ////
        ];
    }
}
