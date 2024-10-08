<?php

namespace App\Http\Requests\Invoice;

use App\Enums\UserGender;
use App\Enums\UserStatus;
use App\Models\Customer;
use App\Rules\MobileRule;
use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        $customer = Customer::find($this->buyer);

        if (!$customer) {
            throw new NotFoundHttpException('مشتری وجود ندارد');
        }

        return Gate::allows('createInvoice', $customer);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'buyer' => 'required|integer|exists:customers,id',
            'items' => 'required|array',
            'items.*.product' => 'required|integer|exists:products,id',
            'items.*.product_section' => 'nullable|integer|exists:product_sections,id',
            'items.*.description_structure' => 'nullable|string',
            'items.*.description' => 'required|array',
            'items.*.dimensions' => 'required|array',
            'items.*.technical_details' => 'required|array',
            'discount' => 'required|integer|min:0|max:30', // افزودن فیلد تخفیف و اعتبارسنجی آن
            'delivery' => 'required|integer', // افزودن فیلد تخفیف و اعتبارسنجی آن
            'description'=> 'required|string',
        ];


    }
}
