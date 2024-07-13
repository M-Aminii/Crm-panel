<?php

namespace App\Http\Requests\DescriptionDimension;

use App\Enums\UserGender;
use App\Enums\UserStatus;
use App\Models\Customer;
use App\Rules\MobileRule;
use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateDescriptionDimensionRequest extends FormRequest
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
            'name' => 'required|string|max:100',
            'percent' => 'required_without:price|integer',
            'price' => 'required_without:percent|nullable|integer',
        ];
    }

}
