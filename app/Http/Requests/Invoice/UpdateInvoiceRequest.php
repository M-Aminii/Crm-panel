<?php

namespace App\Http\Requests\Invoice;

use App\Enums\UserGender;
use App\Enums\UserStatus;
use App\Rules\MobileRule;
use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvoiceRequest extends FormRequest
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
            'status' => 'required|string',
            'pre_payment' => 'nullable|numeric',
            'before_delivery' => 'nullable|numeric',
            'cheque' => 'nullable|numeric',
            'items' => 'required|array',
            'items.*.product' => 'required|integer|exists:products,id',
            'items.*.description' => 'required|array',
            'items.*.dimensions' => 'required|array',
            'items.*.technical_details' => 'required|array',
        ];
    }
}
