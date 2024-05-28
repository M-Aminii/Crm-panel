<?php

namespace App\Http\Requests\Customer;
use App\Enums\CustomerType;
use App\Models\Customer;
use App\Rules\MobileRule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class ShowCustomerRequest extends FormRequest
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
        return Gate::allows('view', $customer);

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
