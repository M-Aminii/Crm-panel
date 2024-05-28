<?php

namespace App\Http\Requests\Invoice;

use App\Enums\UserGender;
use App\Enums\UserStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Rules\MobileRule;
use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShowInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        $invoice = Invoice::find($this->invoice);
        if (!$invoice) {
            throw new NotFoundHttpException('فاکتور وجود ندارد');
        }

        return Gate::allows('viewInvoice', $invoice);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
          //
        ];
    }
}
