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
            'status' => 'required|string',
            'items' => 'required|array',
            'items.*.product' => 'required|integer|exists:products,id',
            'items.*.description' => 'required|array',
            'items.*.dimensions' => 'required|array',
            'items.*.technical_details' => 'required|array',
        ];
       /* return [
            'buyer' => 'required|exists:customers,id',
            'items' => 'required|array',
            'items.*.product' => 'required|exists:products,id',
            'items.*.description' => 'required|array',
            'items.*.technical_details' => 'required|array',
            'items.*.dimensions' => 'required|array',
            'items.*.dimensions.*.height' => 'required|integer',
            'items.*.dimensions.*.width' => 'required|integer',
            'items.*.dimensions.*.quantity' => 'required|integer',
            'items.*.dimensions.*.description' => 'nullable'
        ];*/
    }
}
