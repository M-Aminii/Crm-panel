<?php

namespace App\Http\Requests\FinalOrder;

use App\Enums\UserGender;
use App\Enums\UserStatus;
use App\Models\Customer;
use App\Rules\MobileRule;
use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateFinalOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
return true;
       /* $customer = Customer::find($this->buyer);

        if (!$customer) {
            throw new NotFoundHttpException('مشتری وجود ندارد');
        }

        return Gate::allows('createInvoice', $customer);*/
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pdf_map' => 'required',
        ];


    }
}
