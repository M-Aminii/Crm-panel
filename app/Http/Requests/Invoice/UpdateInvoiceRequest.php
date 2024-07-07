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
            'status' => 'nullable|string',
            'informal_status' => 'nullable',
            'pre_payment' => 'nullable|numeric',
            'before_delivery' => 'nullable|numeric',
            'cheque' => 'nullable|numeric',
            'description'=> 'nullable|string',
            'items' => 'nullable|array',
            'items.*.product' => 'nullable|integer|exists:products,id',
            'items.*.description' => 'nullable|array',
            'items.*.dimensions' => 'nullable|array',
            'items.*.technical_details' => 'nullable|array',
        ];
    }
}
